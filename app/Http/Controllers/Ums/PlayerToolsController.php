<?php

namespace App\Http\Controllers\Ums;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tournament;
use App\Models\League;
use App\Models\Payment;
use App\Models\FullFreeMember;
use App\Models\Settings;
use App\Models\User;

use App\Mail\Participation;
use App\Mail\ParticipationLeague;
use App\Mail\NewMembershipReq;
use App\Mail\ApproveMembership;

use Session;
use Auth;
use Carbon\Carbon;
use Mail;


class PlayerToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Player']);
    }

    public function wait_approval()
    {
        return view('backend.player.wait-approval');
    }

    public function player_dashboard()
    {
        $settings = Settings::findOrFail(1);

        $tournaments = Tournament::where('draw_status', $settings->tournaments_open_for)->whereYear('start', date('Y'))->get()->sortBy('start')->groupBy(function($d) {
                        return Carbon::parse($d->start)->format('Y');
                    });

        $leagues = League::where('draw_status', $settings->leagues_open_for)->whereYear('start', date('Y'))->get()->sortBy('start')->groupBy(function($d) {
                    return Carbon::parse($d->start)->format('Y');
                });

        $paid_fees = Payment::where(['status' => 'Paid', 'user_id' => Auth::id()])->sum('total_fees');
        $tour_part = Payment::where(['status' => 'Paid', 'user_id' => Auth::id()])->count();
        $leag_part = Payment::where(['league_status' => 'Paid', 'user_id' => Auth::id()])->count();

        $paid_part = $tour_part + $leag_part;

        $settings = Settings::findOrFail(1);

        return view('backend.player.index', compact('tournaments', 'leagues', 'paid_fees', 'paid_part', 'settings'));
    }

    public function participate()
    {

        if(Auth::user()->status == 'Full Member') {
            abort(403);
        } else {
            
            $settings = Settings::findOrFail(1);

            $tournaments = Tournament::where(['status' => 'On', 'draw_status' => $settings->tournaments_open_for])->whereYear('start', date('Y'))->orderBy('start')->get();                    
            $leagues = League::where(['status' => 'On', 'draw_status' => $settings->leagues_open_for])->whereYear('start', date('Y'))->orderBy('start')->get();


            $auth_payment = Payment::where(['user_id' => Auth::id(), 'draw_status' => $settings->tournaments_open_for])->whereYear('created_at', date('Y'))->get();
            if($auth_payment->count() == 0) {
                $auth_part = '';
            } elseif($auth_payment->count() == 1) {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => $settings->tournaments_open_for])->whereYear('created_at', date('Y'))->first();
            }


            $league_auth_payment = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => $settings->leagues_open_for])->whereYear('created_at', date('Y'))->get();
            if($league_auth_payment->count() == 0) {
                $league_auth_part = '';
            } elseif($league_auth_payment->count() == 1) {
                $league_auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => $settings->leagues_open_for])->whereYear('created_at', date('Y'))->first();
            }

            
            return view('backend.player.participate', compact('tournaments', 'leagues', 'settings', 'auth_part', 'league_auth_part'));

        }

    }

    public function full_member_preferences()
    {

        if(Auth::user()->status == 'Full Member') {
            
            $settings = Settings::findOrFail(1);

            $tournaments = Tournament::where(['status' => 'On', 'draw_status' => $settings->tournaments_open_for])->whereYear('start', date('Y'))->orderBy('start')->get();                    
            $leagues = League::where(['status' => 'On', 'draw_status' => $settings->leagues_open_for])->whereYear('start', date('Y'))->orderBy('start')->get();


            $auth_payment = Payment::where(['user_id' => Auth::id(), 'is_full' => 'Yes', 'draw_status' => $settings->tournaments_open_for])->whereYear('created_at', date('Y'))->get();
            if($auth_payment->count() == 0) {
                $auth_part = '';
            } elseif($auth_payment->count() == 1) {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'is_full' => 'Yes', 'draw_status' => $settings->tournaments_open_for])->whereYear('created_at', date('Y'))->first();
            }


            $league_auth_payment = Payment::where(['user_id' => Auth::id(), 'is_full' => 'Yes', 'league_draw_status' => $settings->leagues_open_for])->whereYear('created_at', date('Y'))->get();
            if($league_auth_payment->count() == 0) {
                $league_auth_part = '';
            } elseif($league_auth_payment->count() == 1) {
                $league_auth_part = Payment::where(['user_id' => Auth::id(), 'is_full' => 'Yes', 'league_draw_status' => $settings->leagues_open_for])->whereYear('created_at', date('Y'))->first();
            }

            
            return view('backend.player.full-member-preferences', compact('tournaments', 'leagues', 'settings', 'auth_part', 'league_auth_part'));


        } else {
            abort(403);
        }

    }

    public function store_tournaments_participation(Request $request)
    {

        $this->validate($request, [
            "tournaments" => ["required", "array", "max:2"],
        ]);
        
        $settings = Settings::findOrFail(1);
        $total = $request->tournament_fees + $request->league_fees;
        
        do {
            $code = substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', ceil(5/strlen($x)) )),1,5);
            $chk_code = Payment::where('participation_code', '#'.$code)->first();
        } while ($chk_code);

        
        if ($request->has('online_payment')) {

            $drawStatus = $settings->tournaments_open_for;
            $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => $drawStatus])->whereYear('created_at', date('Y'))->first();
            
            if ($auth_part) {
                Session::flash('error', 'You have already participated!');
                return redirect()->back();
            } else {

                $get_tours = Tournament::whereIn('id', $request->tournaments)->get();
                $tourNames = $get_tours->map(function ($item) {
                    return $item['name'];
                })->all();
                $stringName = implode(', ', $tourNames);
                
                
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $product = \Stripe\Product::create([
                    'name' => 'Tournament: '.$stringName,
                    'images' =>['https://tennis4allcyprus.com/assets/uploads/default/logo.png'],
                ]);

                $price = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => $request->tournament_fees * 100,
                    'currency' => 'eur',
                ]);

                $response = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'customer_email' => auth()->user()->email,
                    'line_items' => [[
                        'price' => $price->id,
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('tournaments.online.participation').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('cancel.online.participation'),
                ]);
                
                if(isset($response->id) && $response->id != '') {
                    session()->put('tournaments', $request->tournaments);
                    session()->put('tournament_fees', $request->tournament_fees);
                    session()->put('code', $code);
                    session()->put('total', $total);
                    session()->put('drawStatus', $drawStatus);
                    return redirect($response->url);
                } else {
                    return redirect()->route('cancel.online.participation');
                }

            }
            
        } else {

            if($settings->tournaments_open_for == '1st Draw') {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->first();
                
                if($auth_part) {
                    
                    Session::flash('error', 'You have already participated!');
                    return redirect()->back();

                } else {

                    $payment = new Payment();
                    $payment->participation_code = '#'.$code;
                    $payment->user_id = auth()->id();
                    $payment->tournaments = json_encode($request->tournaments);
                    $payment->tournament_fees = $request->tournament_fees;
                    $payment->total_fees = $total;
                    $payment->status = 'Pending';

                    $payment->draw_status = '1st Draw';

                    $payment->save();

                    $user = User::findOrFail($payment->user_id);
                    $admin = User::where('role', 'Administrator')->first();
                    Mail::to($admin->email)->send(new Participation($user, $payment));

                    Session::flash('success', 'Tournament Participated Successfully !');
                    return redirect()->back();

                }

            }


            if($settings->tournaments_open_for == '2nd Draw') {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->first();
                
                if($auth_part) {
                    
                    Session::flash('error', 'You have already participated!');
                    return redirect()->back();

                } else {
                    
                    $payment = new Payment();
                    $payment->participation_code = '#'.$code;
                    $payment->user_id = auth()->id();
                    $payment->tournaments = json_encode($request->tournaments);
                    $payment->tournament_fees = $request->tournament_fees;
                    $payment->total_fees = $total;
                    $payment->status = 'Pending';

                    $payment->draw_status = '2nd Draw';

                    $payment->save();

                    $user = User::findOrFail($payment->user_id);
                    $admin = User::where('role', 'Administrator')->first();
                    Mail::to($admin->email)->send(new Participation($user, $payment));

                    Session::flash('success', 'Tournament Participated Successfully !');
                    return redirect()->back();

                }

            }


            if($settings->tournaments_open_for == '3rd Draw') {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '3rd Draw'])->whereYear('created_at', date('Y'))->first();
                
                if($auth_part) {
                    
                    Session::flash('error', 'You have already participated!');
                    return redirect()->back();

                } else {
                    
                    $payment = new Payment();
                    $payment->participation_code = '#'.$code;
                    $payment->user_id = auth()->id();
                    $payment->tournaments = json_encode($request->tournaments);
                    $payment->tournament_fees = $request->tournament_fees;
                    $payment->total_fees = $total;
                    $payment->status = 'Pending';

                    $payment->draw_status = '3rd Draw';

                    $payment->save();

                    $user = User::findOrFail($payment->user_id);
                    $admin = User::where('role', 'Administrator')->first();
                    Mail::to($admin->email)->send(new Participation($user, $payment));

                    Session::flash('success', 'Tournament Participated Successfully !');
                    return redirect()->back();

                }

            }


            if($settings->tournaments_open_for == '4th Draw') {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '4th Draw'])->whereYear('created_at', date('Y'))->first();
                
                if($auth_part) {
                    
                    Session::flash('error', 'You have already participated!');
                    return redirect()->back();

                } else {

                    $payment = new Payment();
                    $payment->participation_code = '#'.$code;
                    $payment->user_id = auth()->id();
                    $payment->tournaments = json_encode($request->tournaments);
                    $payment->tournament_fees = $request->tournament_fees;
                    $payment->total_fees = $total;
                    $payment->status = 'Pending';

                    $payment->draw_status = '4th Draw';

                    $payment->save();

                    $user = User::findOrFail($payment->user_id);
                    $admin = User::where('role', 'Administrator')->first();
                    Mail::to($admin->email)->send(new Participation($user, $payment));

                    Session::flash('success', 'Tournament Participated Successfully !');
                    return redirect()->back();
                    
                }

            }

        }

    }

    public function tournaments_online_participation(Request $request)
    {
        if(isset($request->session_id)) {

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);
            
            $payment = new Payment();
            $payment->participation_code = '#'.session()->get('code');
            $payment->user_id = auth()->id();
            $payment->tournaments = json_encode(session()->get('tournaments'));
            $payment->tournament_fees = session()->get('tournament_fees');
            $payment->total_fees = session()->get('total');
            $payment->status = 'Paid';
            $payment->payment_info = 'Stripe (Online)';
            $payment->draw_status = session()->get('drawStatus');
            $payment->save();

            $user = User::findOrFail($payment->user_id);
            $admin = User::where('role', 'Administrator')->first();
            Mail::to($admin->email)->send(new Participation($user, $payment));

            session()->forget('code');
            session()->forget('tournaments');
            session()->forget('tournament_fees');
            session()->forget('total');
            session()->forget('drawStatus');

            Session::flash('success', 'Tournament Participated Successfully!');
            return redirect()->route('player.participate');             
    
        } else {
            return redirect()->route('cancel.online.participation');
        }
    }

    public function store_leagues_participation(Request $request)
    {

        $this->validate($request, [
            "leagues" => ["required", "array", "max:1"],
        ]);
        
        $settings = Settings::findOrFail(1);
        $total = $request->tournament_fees + $request->league_fees;
        
        do {
            $code = substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', ceil(5/strlen($x)) )),1,5);
            $chk_code = Payment::where('participation_code', '#'.$code)->first();
        } while ($chk_code);

        
        if ($request->has('online_payment')) {

            $drawStatus = $settings->leagues_open_for;
            $auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => $drawStatus])->whereYear('created_at', date('Y'))->first();
            
            if ($auth_part) {
                Session::flash('error', 'You have already participated!');
                return redirect()->back();
            } else {

                $get_leagues = League::whereIn('id', $request->leagues)->get();
                $leagueNames = $get_leagues->map(function ($item) {
                    return $item['name'];
                })->all();
                $stringName = implode(', ', $leagueNames);
                
                
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $product = \Stripe\Product::create([
                    'name' => 'League: '.$stringName,
                    'images' =>['https://tennis4allcyprus.com/assets/uploads/default/logo.png'],
                ]);

                $price = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => $request->league_fees * 100,
                    'currency' => 'eur',
                ]);

                $response = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'customer_email' => auth()->user()->email,
                    'line_items' => [[
                        'price' => $price->id,
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('leagues.online.participation').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('cancel.online.participation'),
                ]);
                
                if(isset($response->id) && $response->id != '') {
                    session()->put('leagues', $request->leagues);
                    session()->put('league_fees', $request->league_fees);
                    session()->put('code', $code);
                    session()->put('total', $total);
                    session()->put('drawStatus', $drawStatus);
                    return redirect($response->url);
                } else {
                    return redirect()->route('cancel.online.participation');
                }

            }
            
        } else {

            if($settings->leagues_open_for == '1st Draw') {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->first();
                
                if($auth_part) {

                    Session::flash('error', 'You have already participated!');
                    return redirect()->back();

                } else {
                   
                    $payment = new Payment();
                    $payment->participation_code = '#'.$code;
                    $payment->user_id = auth()->id();
                    $payment->leagues = json_encode($request->leagues);
                    $payment->league_fees = $request->league_fees;
                    $payment->total_fees = $total;
                    $payment->league_status = 'Pending';
                    
                    $payment->league_draw_status = '1st Draw';

                    $payment->save();

                    $user = User::findOrFail($payment->user_id);
                    $admin = User::where('role', 'Administrator')->first();
                    Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                    Session::flash('success', 'League Participated Successfully !');
                    return redirect()->back();

                }

            }


            if($settings->leagues_open_for == '2nd Draw') {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->first();
                
                if($auth_part) {
                    
                    Session::flash('error', 'You have already participated!');
                    return redirect()->back();

                } else {
                    
                    $payment = new Payment();
                    $payment->participation_code = '#'.$code;
                    $payment->user_id = auth()->id();
                    $payment->leagues = json_encode($request->leagues);
                    $payment->league_fees = $request->league_fees;
                    $payment->total_fees = $total;
                    $payment->league_status = 'Pending';

                    $payment->league_draw_status = '2nd Draw';

                    $payment->save();

                    $user = User::findOrFail($payment->user_id);
                    $admin = User::where('role', 'Administrator')->first();
                    Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                    Session::flash('success', 'League Participated Successfully !');
                    return redirect()->back();

                }

            }


            if($settings->leagues_open_for == 'Top16 Finals') {
                $auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => 'Top16 Finals'])->whereYear('created_at', date('Y'))->first();
                
                if($auth_part) {

                    Session::flash('error', 'You have already participated!');
                    return redirect()->back();

                } else {
                    
                    $payment = new Payment();
                    $payment->participation_code = '#'.$code;
                    $payment->user_id = auth()->id();
                    $payment->leagues = json_encode($request->leagues);
                    $payment->league_fees = $request->league_fees;
                    $payment->total_fees = $total;
                    $payment->league_status = 'Pending';

                    $payment->league_draw_status = 'Top16 Finals';

                    $payment->save();

                    $user = User::findOrFail($payment->user_id);
                    $admin = User::where('role', 'Administrator')->first();
                    Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                    Session::flash('success', 'League Participated Successfully !');
                    return redirect()->back();

                }

            }

        }

    }

    public function leagues_online_participation(Request $request)
    {
        if(isset($request->session_id)) {

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);
            
            $payment = new Payment();
            $payment->participation_code = '#'.session()->get('code');
            $payment->user_id = auth()->id();
            $payment->leagues = json_encode(session()->get('leagues'));
            $payment->league_fees = session()->get('league_fees');
            $payment->total_fees = session()->get('total');
            $payment->league_status = 'Paid';
            $payment->payment_info_league = 'Stripe (Online)';
            $payment->league_draw_status = session()->get('drawStatus');
            $payment->save();

            $user = User::findOrFail($payment->user_id);
            $admin = User::where('role', 'Administrator')->first();
            Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

            session()->forget('code');
            session()->forget('leagues');
            session()->forget('league_fees');
            session()->forget('total');
            session()->forget('drawStatus');

            Session::flash('success', 'League Participated Successfully!');
            return redirect()->route('player.participate');             
    
        } else {
            return redirect()->route('cancel.online.participation');
        }
    }

    public function cancel_online_participation()
    {
        Session::flash('error', 'Payment Cancelled! Try again later.');
        return redirect()->route('player.dashboard'); 
    }


    public function store_tournaments_preferences(Request $request)
    {

        $settings = Settings::findOrFail(1);

        $this->validate($request, [
            "tournaments" => ["array", "max:2", "required"],
        ]);

        $total = $request->tournament_fees + $request->league_fees;
        
        do {
            $code = substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', ceil(5/strlen($x)) )),1,5);
            $chk_code = Payment::where('participation_code', '#'.$code)->first();
        } while ($chk_code);

        if($settings->tournaments_open_for == '1st Draw') {
            $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->first();
            
            if($auth_part) {
                
                $payment = Payment::findOrFail($auth_part->id);
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;

                if($payment->leagues == null) {
                    $payment->leagues = null;
                }

                $payment->league_fees = $request->league_fees;

                if($payment->league_status == 'Paid') {
                    $payment->league_status = 'Paid';
                } else {
                    if($payment->leagues == null) {
                        $payment->league_status = null;
                    } else {
                        $payment->league_status = 'Pending';
                    }
                    
                }

                $payment->status = 'Paid';
                $payment->is_full = 'Yes';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            } else {
                
                $payment = new Payment();
                $payment->participation_code = '#'.$code;
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;
                $payment->status = 'Paid';
                $payment->is_full = 'Yes';
                $payment->draw_status = '1st Draw';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            }

        } else if($settings->tournaments_open_for == '2nd Draw') {
            $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->first();
            
            if($auth_part) {
                
                $payment = Payment::findOrFail($auth_part->id);
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;

                if($payment->leagues == null) {
                    $payment->leagues = null;
                }

                $payment->league_fees = $request->league_fees;

                if($payment->league_status == 'Paid') {
                    $payment->league_status = 'Paid';
                } else {
                    if($payment->leagues == null) {
                        $payment->league_status = null;
                    } else {
                        $payment->league_status = 'Pending';
                    }
                    
                }

                $payment->status = 'Paid';
                $payment->is_full = 'Yes';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            } else {
                
                $payment = new Payment();
                $payment->participation_code = '#'.$code;
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;
                $payment->status = 'Paid';
                $payment->is_full = 'Yes';
                $payment->draw_status = '2nd Draw';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            }

        } else if($settings->tournaments_open_for == '3rd Draw') {
            $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '3rd Draw'])->whereYear('created_at', date('Y'))->first();
            
            if($auth_part) {
                
                $payment = Payment::findOrFail($auth_part->id);
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;

                if($payment->leagues == null) {
                    $payment->leagues = null;
                }

                $payment->league_fees = $request->league_fees;

                if($payment->league_status == 'Paid') {
                    $payment->league_status = 'Paid';
                } else {
                    if($payment->leagues == null) {
                        $payment->league_status = null;
                    } else {
                        $payment->league_status = 'Pending';
                    }
                    
                }

                $payment->status = 'Paid';
                $payment->is_full = 'Yes';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            } else {
                
                $payment = new Payment();
                $payment->participation_code = '#'.$code;
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;
                $payment->status = 'Paid';
                $payment->is_full = 'Yes';
                $payment->draw_status = '3rd Draw';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            }

        } else if($settings->tournaments_open_for == '4th Draw') {
            $auth_part = Payment::where(['user_id' => Auth::id(), 'draw_status' => '4th Draw'])->whereYear('created_at', date('Y'))->first();
            
            if($auth_part) {
                
                $payment = Payment::findOrFail($auth_part->id);
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;

                if($payment->leagues == null) {
                    $payment->leagues = null;
                }

                $payment->league_fees = $request->league_fees;

                if($payment->league_status == 'Paid') {
                    $payment->league_status = 'Paid';
                } else {
                    if($payment->leagues == null) {
                        $payment->league_status = null;
                    } else {
                        $payment->league_status = 'Pending';
                    }
                    
                }

                $payment->status = 'Paid';
                $payment->is_full = 'Yes';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            } else {
                
                $payment = new Payment();
                $payment->participation_code = '#'.$code;
                $payment->user_id = auth()->id();
                $payment->tournaments = json_encode($request->tournaments);
                $payment->tournament_fees = $request->tournament_fees;
                $payment->total_fees = $total;
                $payment->status = 'Paid';
                $payment->is_full = 'Yes';
                $payment->draw_status = '4th Draw';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new Participation($user, $payment));

                Session::flash('success', 'Tournament Prefered Successfully !');
                return redirect()->back();

                
            }

        }

    }


    public function store_leagues_preferences(Request $request)
    {

        $settings = Settings::findOrFail(1);

        $this->validate($request, [
            "leagues" => ["array", "max:1", "required"],
        ]);

        $total = $request->tournament_fees + $request->league_fees;
        
        do {
            $code = substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', ceil(5/strlen($x)) )),1,5);
            $chk_code = Payment::where('participation_code', '#'.$code)->first();
        } while ($chk_code);

        if($settings->leagues_open_for == '1st Draw') {
            $auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->first();
            
            if($auth_part) {
                
                
                $payment = Payment::findOrFail($auth_part->id);
                $payment->user_id = auth()->id();
                $payment->leagues = json_encode($request->leagues);
                $payment->league_fees = $request->league_fees;
                $payment->total_fees = $total;

                if($payment->tournaments == null) {
                    $payment->tournaments = null;
                }

                $payment->tournament_fees = $request->tournament_fees;

                if($payment->status == 'Paid') {
                    $payment->status = 'Paid';
                } else {
                    if($payment->tournaments == null) {
                        $payment->status = null;
                    } else {
                        $payment->status = 'Pending';
                    }
                    
                }

                $payment->league_status = 'Paid';
                $payment->is_full = 'Yes';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                Session::flash('success', 'League Prefered Successfully !');
                return redirect()->back();

                

            } else {
                
                $payment = new Payment();
                $payment->participation_code = '#'.$code;
                $payment->user_id = auth()->id();
                $payment->leagues = json_encode($request->leagues);
                $payment->league_fees = $request->league_fees;
                $payment->total_fees = $total;
                $payment->league_status = 'Paid';
                $payment->is_full = 'Yes';
                $payment->league_draw_status = '1st Draw';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                Session::flash('success', 'League Prefered Successfully !');
                return redirect()->back();

                
            }

        } else if($settings->leagues_open_for == '2nd Draw') {
            $auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->first();
            
            if($auth_part) {
                
                
                $payment = Payment::findOrFail($auth_part->id);
                $payment->user_id = auth()->id();
                $payment->leagues = json_encode($request->leagues);
                $payment->league_fees = $request->league_fees;
                $payment->total_fees = $total;

                if($payment->tournaments == null) {
                    $payment->tournaments = null;
                }

                $payment->tournament_fees = $request->tournament_fees;

                if($payment->status == 'Paid') {
                    $payment->status = 'Paid';
                } else {
                    if($payment->tournaments == null) {
                        $payment->status = null;
                    } else {
                        $payment->status = 'Pending';
                    }
                    
                }

                $payment->league_status = 'Paid';
                $payment->is_full = 'Yes';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                Session::flash('success', 'League Prefered Successfully !');
                return redirect()->back();

                

            } else {
                
                $payment = new Payment();
                $payment->participation_code = '#'.$code;
                $payment->user_id = auth()->id();
                $payment->leagues = json_encode($request->leagues);
                $payment->league_fees = $request->league_fees;
                $payment->total_fees = $total;
                $payment->league_status = 'Paid';
                $payment->is_full = 'Yes';
                $payment->league_draw_status = '2nd Draw';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                Session::flash('success', 'League Prefered Successfully !');
                return redirect()->back();

                
            }

        } else if($settings->leagues_open_for == 'Top16 Finals') {
            $auth_part = Payment::where(['user_id' => Auth::id(), 'league_draw_status' => 'Top16 Finals'])->whereYear('created_at', date('Y'))->first();
            
            if($auth_part) {
                
                
                $payment = Payment::findOrFail($auth_part->id);
                $payment->user_id = auth()->id();
                $payment->leagues = json_encode($request->leagues);
                $payment->league_fees = $request->league_fees;
                $payment->total_fees = $total;

                if($payment->tournaments == null) {
                    $payment->tournaments = null;
                }

                $payment->tournament_fees = $request->tournament_fees;

                if($payment->status == 'Paid') {
                    $payment->status = 'Paid';
                } else {
                    if($payment->tournaments == null) {
                        $payment->status = null;
                    } else {
                        $payment->status = 'Pending';
                    }
                    
                }

                $payment->league_status = 'Paid';
                $payment->is_full = 'Yes';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                Session::flash('success', 'League Prefered Successfully !');
                return redirect()->back();

                

            } else {
                
                $payment = new Payment();
                $payment->participation_code = '#'.$code;
                $payment->user_id = auth()->id();
                $payment->leagues = json_encode($request->leagues);
                $payment->league_fees = $request->league_fees;
                $payment->total_fees = $total;
                $payment->league_status = 'Paid';
                $payment->is_full = 'Yes';
                $payment->league_draw_status = 'Top16 Finals';

                $payment->save();

                $user = User::findOrFail($payment->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new ParticipationLeague($user, $payment));

                Session::flash('success', 'League Prefered Successfully !');
                return redirect()->back();

                
            }

        }

    }


    public function player_pending_tournaments_participations()
    {
        $pendings = Payment::where(['status' => 'Pending', 'user_id' => Auth::id()])->whereYear('created_at', date('Y'))->get();

        if($pendings->count() > 0) {
            return view('backend.player.participation.tournaments.pending', compact('pendings'));
        } else {
            Session::flash('info', 'No pending tournament payment found !');
            return redirect()->back();
        }

    }

    public function player_paid_tournaments_participations()
    {
        $paids = Payment::where(['status' => 'Paid', 'user_id' => Auth::id()])->whereYear('created_at', date('Y'))->get();

        if($paids->count() > 0) {
            return view('backend.player.participation.tournaments.paid', compact('paids'));
        } else {
            if(Auth::user()->status == 'Full Member') {
                Session::flash('info', 'Select prefered tournaments first.');
            } else {
                Session::flash('info', 'No paid tournament payment found !');
            }

            return redirect()->back();
        }

    }

    public function player_declined_tournaments_participations()
    {
        $declineds = Payment::where(['status' => 'Declined', 'user_id' => Auth::id()])->whereYear('created_at', date('Y'))->get();

        if($declineds->count() > 0) {
            return view('backend.player.participation.tournaments.declined', compact('declineds'));
        } else {
            Session::flash('info', 'No declined tournament payment found !');
            return redirect()->back();
        }

    }

    public function player_pending_leagues_participations()
    {
        $pendings = Payment::where(['league_status' => 'Pending', 'user_id' => Auth::id()])->whereYear('created_at', date('Y'))->get();

        if($pendings->count() > 0) {
            return view('backend.player.participation.leagues.pending', compact('pendings'));
        } else {
            Session::flash('info', 'No pending league payment found !');
            return redirect()->back();
        }

    }

    public function player_paid_leagues_participations()
    {
        $paids = Payment::where(['league_status' => 'Paid', 'user_id' => Auth::id()])->whereYear('created_at', date('Y'))->get();

        if($paids->count() > 0) {
            return view('backend.player.participation.leagues.paid', compact('paids'));
        } else {
            
            if(Auth::user()->status == 'Full Member') {
                Session::flash('info', 'Select prefered league first.');
            } else {
                Session::flash('info', 'No paid league payment found !');
            }

            return redirect()->back();

        }

    }

    public function player_declined_leagues_participations()
    {
        $declineds = Payment::where(['league_status' => 'Declined', 'user_id' => Auth::id()])->whereYear('created_at', date('Y'))->get();

        if($declineds->count() > 0) {
            return view('backend.player.participation.leagues.declined', compact('declineds'));
        } else {
            Session::flash('info', 'No declined league payment found !');
            return redirect()->back();
        }

    }

    public function get_full_free()
    {
        $find = FullFreeMember::where(['user_id' => Auth::id(), 'year' => date('Y')])->first();
        return view('backend.player.request-full-free', compact('find'));
    }

    public function store_full_free(Request $request)
    {
        $already = FullFreeMember::where(['user_id' => Auth::id()])->whereYear('created_at', date('Y'))->first();

        if($already) {
            Session::flash('error', 'Already Requested! Please wait for the administrator response.');
            return redirect()->back();
        } else {
            
            do {
                $code = substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', ceil(5/strlen($x)) )),1,5);
                $chk_code = FullFreeMember::where('membership_code', '#'.$code)->first();
            } while ($chk_code);

            if ($request->has('online_payment')) {
                
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $product = \Stripe\Product::create([
                    'name' => 'Full Membership',
                    'images' =>['https://tennis4allcyprus.com/assets/uploads/default/logo.png'],
                ]);

                $price = \Stripe\Price::create([
                    'product' => $product->id,
                    'unit_amount' => 120 * 100,
                    'currency' => 'eur',
                ]);

                $response = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'customer_email' => auth()->user()->email,
                    'line_items' => [[
                        'price' => $price->id,
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('online.membership').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('cancel.online.participation'),
                ]);
                
                if(isset($response->id) && $response->id != '') {
                    session()->put('year', date('Y'));
                    session()->put('code', $code);

                    return redirect($response->url);
                } else {
                    return redirect()->route('cancel.online.participation');
                }
                
            } else {

                $full = new FullFreeMember();
                $full->user_id = Auth::id();
                $full->membership_code = '#'.$code;
                $full->status  = 'Pending';
                $full->year    = date('Y');

                $full->save();

                $user = User::findOrFail($full->user_id);
                $admin = User::where('role', 'Administrator')->first();
                Mail::to($admin->email)->send(new NewMembershipReq($user));

                Session::flash('info', 'Requested Successfully! Please wait for the administrator approval.');
                return redirect()->back();
            }

        }

    }

    public function online_membership(Request $request)
    {
        if(isset($request->session_id)) {

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);

            $get_member = new FullFreeMember();
            $get_member->status = 'Approved';
            $get_member->payment_info = 'Stripe (Online)';
            $get_member->user_id = Auth::id();
            $get_member->year = session()->get('year');
            $get_member->membership_code = session()->get('code');
            $get_member->save();

            $user = User::findOrFail($get_member->user_id);
            $user->status = 'Full Member';
            $user->save();

            Mail::to($user->email)->send(new ApproveMembership($user));

            session()->forget('year');
            session()->forget('code');

            Session::flash('success', 'Membership Approved Successfully !');
            return redirect()->back();

        } else {
            return redirect()->route('cancel.online.participation');
        }

    }


    public function view_tournament_tree($id)
    {
        $tournament = Tournament::findOrFail($id);
        $players = [];

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $t_d_quar = json_decode($tournament->quarter_final_deadline);
        $t_d_semf = json_decode($tournament->semi_final_deadline);
        $t_d_final = json_decode($tournament->final_deadline);

        $round_one_matches = json_decode($tournament->round_one_matches, true);
        $round_one_results = json_decode($tournament->round_one_results, true);
        $round_one_winners = json_decode($tournament->round_one_winners, true);
        $round_one_auto_selection = json_decode($tournament->round_one_auto_selection, true);
        $round_one_status  = json_decode($tournament->round_one_status, true);
        $round_one_retires = json_decode($tournament->round_one_retires, true);

        $round_two_matches = json_decode($tournament->round_two_matches, true);
        $round_two_results = json_decode($tournament->round_two_results, true);
        $round_two_winners = json_decode($tournament->round_two_winners, true);
        $round_two_auto_selection = json_decode($tournament->round_two_auto_selection, true);
        $round_two_status  = json_decode($tournament->round_two_status, true);
        $round_two_retires = json_decode($tournament->round_two_retires, true);

        $round_three_matches = json_decode($tournament->round_three_matches, true);
        $round_three_results = json_decode($tournament->round_three_results, true);
        $round_three_winners = json_decode($tournament->round_three_winners, true);
        $round_three_auto_selection = json_decode($tournament->round_three_auto_selection, true);
        $round_three_status  = json_decode($tournament->round_three_status, true);
        $round_three_retires = json_decode($tournament->round_three_retires, true);

        $semi_final_matches = json_decode($tournament->semi_final_matches, true);
        $semi_final_results = json_decode($tournament->semi_final_results, true);
        $semi_final_winners = json_decode($tournament->semi_final_winners, true);
        $semi_final_auto_selection = json_decode($tournament->semi_final_auto_selection, true);
        $semi_final_status  = json_decode($tournament->semi_final_status, true);
        $semi_final_retires = json_decode($tournament->semi_final_retires, true);

        $quarter_final_matches = json_decode($tournament->quarter_final_matches, true);
        $quarter_final_results = json_decode($tournament->quarter_final_results, true);
        $quarter_final_winners = json_decode($tournament->quarter_final_winners, true);
        $quarter_final_auto_selection = json_decode($tournament->quarter_final_auto_selection, true);
        $quarter_final_status  = json_decode($tournament->quarter_final_status, true);
        $quarter_final_retires = json_decode($tournament->quarter_final_retires, true);


        $final_matches = json_decode($tournament->final_matches, true);
        $final_results = json_decode($tournament->final_results, true);
        $final_winners = json_decode($tournament->final_winners, true);
        $final_auto_selection = json_decode($tournament->final_auto_selection, true);
        $final_status  = json_decode($tournament->final_status, true);
        $final_retires = json_decode($tournament->final_retires, true);

        $payments = Payment::all();

        foreach($payments as $key => $payment) {

            if($payment->status == 'Paid') {
                if($payment->tournaments) {
                    $arrays = json_decode($payment->tournaments);
                } else {
                    $arrays = [];
                }
            } else {
                $arrays = [];
            }
            
            foreach($arrays as $k => $value) {

                if($tournament->id == $value) {
                    array_push($players, $payment->user_id);
                }

            }

        }


        if ($tournament->tree_size == 8) {

            return view('backend.player.tournament.draw.eight-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

        } else if ($tournament->tree_size == 16) {

            return view('backend.player.tournament.draw.sixteen-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

        } else if ($tournament->tree_size == 32) {

            return view('backend.player.tournament.draw.thirtytwo-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

        } else if ($tournament->tree_size == 64) {

            return view('backend.player.tournament.draw.sixtyfour-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

        }


    }


    public function view_tournament_draws(Request $request)
    {
        $year = $request->year;
        $draw = $request->draw;
        
        if($year) {
            if($draw == 'All Draws') {
                $tournaments = Tournament::whereNotNull('tree_size')->whereYear('start', $year)->get()->sortBy('start');
            } else {
                $tournaments = Tournament::whereNotNull('tree_size')->whereYear('start', $year)->where('draw_status', $draw)->get()->sortBy('start');
            }
        } else {
            $tournaments = Tournament::whereNotNull('tree_size')->whereYear('start', date('Y'))->get()->sortBy('start');
        }

        return view('backend.player.tournament.view-draws', compact('tournaments'));

    }

    public function previous_tournament_winners(Request $request){
        $year = $request->year;
        $draw = $request->draw;
        
        if($year) {
            if($draw == 'All Draws') {
                $tournaments = Tournament::whereNotNull(['tree_size', 'final_winners'])->whereYear('start', $year)->get()->sortBy('start');
            } else {
                $tournaments = Tournament::whereNotNull(['tree_size', 'final_winners'])->whereYear('start', $year)->where('draw_status', $draw)->get()->sortBy('start');
            }
        } else {
            $tournaments = Tournament::whereNotNull(['tree_size', 'final_winners'])->whereYear('start', date('Y'))->get()->sortBy('start');
        }

        return view('backend.player.tournament.previous-winners', compact('tournaments'));
    }


    public function view_league_tree($id)
    {
        $tournament = League::findOrFail($id);
        $players = [];

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $t_d_quar = json_decode($tournament->quarter_final_deadline);
        $t_d_semf = json_decode($tournament->semi_final_deadline);
        $t_d_final = json_decode($tournament->final_deadline);

        $round_one_matches = json_decode($tournament->round_one_matches, true);
        $round_one_results = json_decode($tournament->round_one_results, true);
        $round_one_winners = json_decode($tournament->round_one_winners, true);
        $round_one_auto_selection = json_decode($tournament->round_one_auto_selection, true);
        $round_one_status  = json_decode($tournament->round_one_status, true);
        $round_one_retires = json_decode($tournament->round_one_retires, true);

        $round_two_matches = json_decode($tournament->round_two_matches, true);
        $round_two_results = json_decode($tournament->round_two_results, true);
        $round_two_winners = json_decode($tournament->round_two_winners, true);
        $round_two_auto_selection = json_decode($tournament->round_two_auto_selection, true);
        $round_two_status  = json_decode($tournament->round_two_status, true);
        $round_two_retires = json_decode($tournament->round_two_retires, true);

        $round_three_matches = json_decode($tournament->round_three_matches, true);
        $round_three_results = json_decode($tournament->round_three_results, true);
        $round_three_winners = json_decode($tournament->round_three_winners, true);
        $round_three_auto_selection = json_decode($tournament->round_three_auto_selection, true);
        $round_three_status  = json_decode($tournament->round_three_status, true);
        $round_three_retires = json_decode($tournament->round_three_retires, true);

        $semi_final_matches = json_decode($tournament->semi_final_matches, true);
        $semi_final_results = json_decode($tournament->semi_final_results, true);
        $semi_final_winners = json_decode($tournament->semi_final_winners, true);
        $semi_final_auto_selection = json_decode($tournament->semi_final_auto_selection, true);
        $semi_final_status  = json_decode($tournament->semi_final_status, true);
        $semi_final_retires = json_decode($tournament->semi_final_retires, true);

        $quarter_final_matches = json_decode($tournament->quarter_final_matches, true);
        $quarter_final_results = json_decode($tournament->quarter_final_results, true);
        $quarter_final_winners = json_decode($tournament->quarter_final_winners, true);
        $quarter_final_auto_selection = json_decode($tournament->quarter_final_auto_selection, true);
        $quarter_final_status  = json_decode($tournament->quarter_final_status, true);
        $quarter_final_retires = json_decode($tournament->quarter_final_retires, true);

        $final_matches = json_decode($tournament->final_matches, true);
        $final_results = json_decode($tournament->final_results, true);
        $final_winners = json_decode($tournament->final_winners, true);
        $final_auto_selection = json_decode($tournament->final_auto_selection, true);
        $final_status  = json_decode($tournament->final_status, true);
        $final_retires = json_decode($tournament->final_retires, true);

        $payments = Payment::all();

        foreach($payments as $key => $payment) {

            if($payment->league_status == 'Paid') {
                if($payment->leagues) {
                    $arrays = json_decode($payment->leagues);
                } else {
                    $arrays = [];
                }
            } else {
                $arrays = [];
            }
            
            foreach($arrays as $k => $value) {

                if($tournament->id == $value) {
                    array_push($players, $payment->user_id);
                }

            }

        }


        if ($tournament->tree_size == 8) {

            return view('backend.player.league.draw.eight-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

        } else if ($tournament->tree_size == 16) {

            return view('backend.player.league.draw.sixteen-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

        } else if ($tournament->tree_size == 32) {

            return view('backend.player.league.draw.thirtytwo-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

        } else {

            return view('backend.player.league.draw.eight-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));
            
        }


    }


    public function view_league_draws(Request $request)
    {
        $year = $request->year;
        $draw = $request->draw;
        
        if($year) {
            if($draw == 'All Draws') {
                $leagues = League::whereNotNull('group_size')->whereYear('start', $year)->get()->sortBy('start');
            } else {
                $leagues = League::whereNotNull('group_size')->whereYear('start', $year)->where('draw_status', $draw)->get()->sortBy('start');
            }
        } else {
            $leagues = League::whereNotNull('group_size')->whereYear('start', date('Y'))->get()->sortBy('start');
        }

        return view('backend.player.league.view-draws', compact('leagues'));

    }

    public function previous_league_winners(Request $request)
    {
        $year = $request->year;
        $draw = $request->draw;
        
        if($year) {
            if($draw == 'All Draws') {
                $leagues = League::whereNotNull(['group_size', 'final_winners'])->whereYear('start', $year)->get()->sortBy('start');
            } else {
                $leagues = League::whereNotNull(['group_size', 'final_winners'])->whereYear('start', $year)->where('draw_status', $draw)->get()->sortBy('start');
            }
        } else {
            $leagues = League::whereNotNull(['group_size', 'final_winners'])->whereYear('start', date('Y'))->get()->sortBy('start');
        }

        return view('backend.player.league.previous-winners', compact('leagues'));

    }

    
}

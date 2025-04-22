<?php

namespace App\Http\Controllers\Ums;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Tournament;
use App\Models\League;
use App\Models\Payment;
use App\Models\FullFreeMember;
use App\Models\Settings;

use App\Mail\PaymentApprove;
use App\Mail\Decline;
use App\Mail\Approve;
use App\Mail\PaymentApproveLeague;
use App\Mail\ApproveMembership;
use App\Mail\DeclineMembership;
use App\Mail\MailToAllPlayers;
use App\Mail\MailToAllFullMember;

use Carbon\Carbon;

use Session;
use Auth;
use DB;
use Mail;

class AdministratorToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Administrator']);
    }

    public function admin_dashboard()
    {
        // $all_tour_par = Payment::whereIn('status', ['Pending', 'Paid', 'Declined'])->where(['is_full' => 'No'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->count();
        $pend_tour_par = Payment::where(['status' => 'Pending'])->whereYear('created_at', date('Y'))->count();
        $paid_tour_par = Payment::where(['status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->count();
        $dec_tour_par  = Payment::where(['status' => 'Declined'])->whereYear('created_at', date('Y'))->count();
        $all_tour_par  = $pend_tour_par + $paid_tour_par + $dec_tour_par;


        // $all_league_par = Payment::whereIn('league_status', ['Pending', 'Paid', 'Declined'])->where(['is_full' => 'No'])->whereYear('created_at', date('Y'))->count();
        $pend_league_par = Payment::where(['league_status' => 'Pending'])->whereYear('created_at', date('Y'))->count();
        $paid_league_par = Payment::where(['league_status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->count();
        $dec_league_par  = Payment::where(['league_status' => 'Declined'])->whereYear('created_at', date('Y'))->count();
        $all_league_par  = $pend_league_par + $paid_league_par + $dec_league_par;


        $all_mem  = FullFreeMember::whereIn('status', ['Pending', 'Approved', 'Declined'])->whereYear('created_at', date('Y'))->count();
        $pend_mem = FullFreeMember::where(['status' => 'Pending', 'year' => date('Y')])->count();
        $appr_mem = FullFreeMember::where(['status' => 'Approved', 'year' => date('Y')])->count();
        $dec_mem  = FullFreeMember::where(['status' => 'Declined', 'year' => date('Y')])->count();


        $tour_pre = Payment::where(['status' => 'Paid', 'is_full' => 'Yes'])->whereYear('created_at', date('Y'))->count();
        $leag_pre = Payment::where(['league_status' => 'Paid', 'is_full' => 'Yes'])->whereYear('created_at', date('Y'))->count();
        $all_pre  = $tour_pre + $leag_pre;


        $all_players  = User::where('role', 'Player')->count();
        $admin_count  = User::where('role', 'Administrator')->count();
        $pend_players = User::where(['approved_at' => null, 'role' => 'Player'])->count();
        $appr_players = User::where([
                ['approved_at', '!=', null],
                ['role', '=', 'Player']
            ])->count();


        $all_tours = Tournament::orderBy('created_at', 'desc')->count();
        $on_tours  = Tournament::where('status', 'On')->count();
        $off_tours = Tournament::where('status', 'Off')->count();


        $all_leags = League::orderBy('created_at', 'desc')->count();
        $on_leags  = League::where('status', 'On')->count();
        $off_leags = League::where('status', 'Off')->count();


        $am_tour_pend = Payment::where(['status' => 'Pending', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('tournament_fees');
        $am_tour_paid = Payment::where(['status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('tournament_fees');
        $am_tour_dec  = Payment::where(['status' => 'Declined', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('tournament_fees');


        $am_leag_pend = Payment::where(['league_status' => 'Pending', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('league_fees');
        $am_leag_paid = Payment::where(['league_status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('league_fees');
        $am_leag_dec  = Payment::where(['league_status' => 'Declined', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('league_fees');


        $am_mem_pend = FullFreeMember::where(['status' => 'Pending', 'year' => date('Y')])->count();
        $am_mem_appr = FullFreeMember::where(['status' => 'Approved', 'year' => date('Y')])->where('payment_info', '!=', 'Free')->count();
        $am_mem_dec  = FullFreeMember::where(['status' => 'Declined', 'year' => date('Y')])->count();

        $settings = Settings::findOrFail(1);


        return view('backend.administrator.index', compact('all_tour_par', 'pend_tour_par', 'paid_tour_par', 'dec_tour_par', 'all_league_par', 'pend_league_par', 'paid_league_par', 'dec_league_par', 'all_mem', 'pend_mem', 'appr_mem', 'dec_mem', 'all_pre', 'tour_pre', 'leag_pre', 'all_players', 'admin_count', 'pend_players', 'appr_players', 'all_tours', 'on_tours', 'off_tours', 'all_leags', 'on_leags', 'off_leags', 'am_tour_pend', 'am_tour_paid', 'am_tour_dec', 'am_leag_pend', 'am_leag_paid', 'am_leag_dec', 'am_mem_pend', 'am_mem_appr', 'am_mem_dec', 'settings'));
    }

    public function head_to_head(Request $request)
    {
        //dd(session()->player_1, $request->player_2);
        $players = User::where([
                ['approved_at', '!=', null],
                ['role', '=', 'Player']
            ])->orderBy('created_at', 'desc')->get();
        
        return view('backend.administrator.head-to-head', compact('players'));
    }

    public function head_to_head_find(Request $request)
    {
        $player_1 = $request->player_1;
        $player_2 = $request->player_2;

        if($player_1 && $player_2) {
            if($player_1 == $player_2) {
                Session::flash('error', "2 Players can't be same!");
            }  
            
            $tournaments = Tournament::all();

            foreach($tournaments as $tournament) {

            }
        }

        return redirect()->route('head.to.head')->with(['player_1' => $player_1]);
        
    }

    public function players_list()
    {
        $players = User::where([
                ['approved_at', '!=', null],
                ['role', '=', 'Player']
            ])->orderBy('created_at', 'desc')->get();
        
        return view('backend.administrator.mngplayers.players-list', compact('players'));
    }

    public function approval_list()
    {
        $need_approval = User::where(['approved_at' => null, 'role' => 'Player'])->orderBy('created_at', 'desc')->get();

        return view('backend.administrator.mngplayers.approval-list', compact('need_approval'));
    }

    public function approve_player(Request $request)
    {
        $user = User::where(['id' => $request->id, 'role' => 'Player'])->first();
        $approve = User::where(['id' => $request->id, 'role' => 'Player'])->update(['approved_at' => now()]);

        Mail::to($user->email)->send(new Approve($user));

        Session::flash('success', 'Player Approved Successfully !');
        return redirect()->back();
    }

    public function decline_player(Request $request)
    {
        $user = User::where(['id' => $request->id, 'role' => 'Player'])->first();
        Mail::to($user->email)->send(new Decline($user));

        $user->delete();

        Session::flash('error', 'Player Declined and Deleted Permanently !');
        return redirect()->back();
    }


    public function edit_player(Request $request, $id)
    {
        $player = User::where(['id' => $id, 'role' => 'Player'])->first();

        return view('backend.administrator.mngplayers.edit-player', compact('player'));
    }


    public function update_player(Request $request, $id)
    {
        $find_user = User::findOrFail($id);

        $this->validate($request, [
            'name'   => 'required',
            'phone'  => 'required',
            'gender' => 'required',
            'age'    => 'required',
            'email'  => 'required|unique:users,email,'.$find_user->id
        ]);

        $find_user->name  = $request->name;
        $find_user->email = $request->email;
        $find_user->phone = '+357'.$request->phone;
        $find_user->gender = $request->gender;
        $find_user->age = $request->age;

        if ($request->hasFile('profile_photo_path')) {
            $image_tmp = $request->file('profile_photo_path');
            if ($image_tmp->isValid()) {
                $image_name = $image_tmp->getClientOriginalName();
                $extension = $image_tmp->getClientOriginalExtension();
                $image_new_name = $image_name.'-'.rand(111,99999).'.'.$extension;

                $original_image_path = 'assets/uploads/users/'.$image_new_name;
                
                Image::make($image_tmp)->save($original_image_path);
                
                $find_user->profile_photo_path = $image_new_name;
            }
        }

        $find_user->save();

        Session::flash('success', 'Player Info Updated Successfully !');
        return redirect()->back();
    }


    public function add_new_tournament()
    {
        return view('backend.administrator.tournament.create');
    }

    public function store_tournament(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'fees'    => 'required',
            'start'   => 'required',
            'end'   => 'required',
            'supervisor_name'   => 'required',
            'supervisor_phone'   => 'required',
        ]);

        $settings = Settings::findOrFail(1);
        $tournament = new Tournament();

        $tournament->name = $request->name;
        $tournament->fees = $request->fees;
        $tournament->start = date('Y-m-d', strtotime($request->start));
        $tournament->end = date('Y-m-t', strtotime($request->end));
        $tournament->supervisor_name = $request->supervisor_name;
        $tournament->supervisor_phone = $request->supervisor_phone;
        $tournament->draw_status = $settings->tournaments_open_for;

        if($request->status) {
            $tournament->status = 'On';
        } else {
            $tournament->status = 'Off';   
        }

        $tournament->save();

        Session::flash('success', 'Tournament Added Successfully !');
        return redirect()->route('all.tournaments');
    }

    public function all_tournaments()
    {
        $tournaments =  Tournament::get()->sortByDesc('created_at')->groupBy(function ($d) {
                            return Carbon::parse($d->start)->format('Y');
                        })->map(function ($group) {
                            return $group->sortByDesc('created_at');
                        });

        return view('backend.administrator.tournament.index', compact('tournaments'));
    }

    public function edit_tournament($id)
    {
        $tournament = Tournament::findOrFail($id);
        return view('backend.administrator.tournament.edit', compact('tournament'));
    }

    public function update_tournament(Request $request, $id)
    {
        $this->validate($request, [
            'name'             => 'required',
            'fees'             => 'required',
            'start'            => 'required',
            'end'              => 'required',
            'supervisor_name'  => 'required',
            'supervisor_phone' => 'required',
        ]);

        $tournament = Tournament::findOrFail($id);

        $tournament->name = $request->name;
        $tournament->fees = $request->fees;
        $tournament->start = date('Y-m-d', strtotime($request->start));
        $tournament->end = date('Y-m-t', strtotime($request->end));
        $tournament->supervisor_name = $request->supervisor_name;
        $tournament->supervisor_phone = $request->supervisor_phone;

        if($request->status) {
            $tournament->status = 'On';
        } else {
            $tournament->status = 'Off';   
        }

        $tournament->save();

        Session::flash('info', 'Tournament Updated Successfully !');
        return redirect()->route('all.tournaments');
    }

    public function delete_tournament($id)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->delete();

        Session::flash('error', 'Tournament Deleted Permanently !');
        return redirect()->back();
    }


    public function add_new_league()
    {
        return view('backend.administrator.league.create');
    }

    public function store_league(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'fees'    => 'required',
            'start'   => 'required',
            'end'   => 'required',
            'supervisor_name'   => 'required',
            'supervisor_phone'   => 'required',
        ]);

        
        $settings = Settings::findOrFail(1);
        $league = new League();

        $league->name = $request->name;
        $league->fees = $request->fees;
        $league->start = date('Y-m-d', strtotime($request->start));
        $league->end = date('Y-m-t', strtotime($request->end));
        $league->supervisor_name = $request->supervisor_name;
        $league->supervisor_phone = $request->supervisor_phone;
        $league->draw_status = $settings->leagues_open_for;

        if($request->status) {
            $league->status = 'On';
        } else {
            $league->status = 'Off';   
        }
        

        $league->save();

        Session::flash('success', 'League Added Successfully !');
        return redirect()->route('all.leagues');
    }

    public function all_leagues()
    {
        $leagues =  League::get()->sortByDesc('created_at')->groupBy(function ($d) {
                        return Carbon::parse($d->start)->format('Y');
                    })->map(function ($group) {
                        return $group->sortByDesc('created_at');
                    });

        return view('backend.administrator.league.index', compact('leagues'));
    }

    public function edit_league($id)
    {
        $league = League::findOrFail($id);
        return view('backend.administrator.league.edit', compact('league'));
    }

    public function update_league(Request $request, $id)
    {
        $this->validate($request, [
            'name'     => 'required',
            'fees'    => 'required',
            'start'   => 'required',
            'end'   => 'required',
            'supervisor_name'   => 'required',
            'supervisor_phone'   => 'required',
        ]);

        $league = League::findOrFail($id);

        $league->name = $request->name;
        $league->fees = $request->fees;
        $league->start = date('Y-m-d', strtotime($request->start));
        $league->end = date('Y-m-t', strtotime($request->end));
        $league->supervisor_name = $request->supervisor_name;
        $league->supervisor_phone = $request->supervisor_phone;

        if($request->status) {
            $league->status = 'On';
        } else {
            $league->status = 'Off';   
        }

        $league->save();

        Session::flash('info', 'League Updated Successfully !');
        return redirect()->route('all.leagues');
    }

    public function delete_league($id)
    {
        $league = League::findOrFail($id);
        $league->delete();

        Session::flash('error', 'League Deleted Permanently !');
        return redirect()->back();
    }

    public function admin_pending_tournaments_participations()
    {
        $pending = Payment::where(['status' => 'Pending'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        return view('backend.administrator.participation.tournaments.pending', compact('pending'));
    }

    public function admin_paid_tournaments_participations()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('tournament_fees');
        return view('backend.administrator.participation.tournaments.paid', compact('paid', 'amount'));
    }

    public function admin_paid_tournaments_participations_first()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->sum('tournament_fees');

        return view('backend.administrator.participation.tournaments.paid-1st', compact('paid', 'amount'));
    }

    public function admin_paid_tournaments_participations_second()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->sum('tournament_fees');

        return view('backend.administrator.participation.tournaments.paid-2nd', compact('paid', 'amount'));
    }

    public function admin_paid_tournaments_participations_third()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '3rd Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '3rd Draw'])->whereYear('created_at', date('Y'))->sum('tournament_fees');

        return view('backend.administrator.participation.tournaments.paid-3rd', compact('paid', 'amount'));
    }

    public function admin_paid_tournaments_participations_fourth()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '4th Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['status' => 'Paid', 'is_full' => 'No', 'draw_status' => '4th Draw'])->whereYear('created_at', date('Y'))->sum('tournament_fees');

        return view('backend.administrator.participation.tournaments.paid-4th', compact('paid', 'amount'));
    }

    public function admin_full_tournaments_participations()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();

        $first_paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $second_paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $third_paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '3rd Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $fourth_paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '4th Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['status' => 'Paid', 'is_full' => 'Yes'])->whereYear('created_at', date('Y'))->sum('tournament_fees');

        return view('backend.administrator.participation.tournaments.full-paid', compact('paid', 'first_paid', 'second_paid', 'third_paid', 'fourth_paid', 'amount'));
    }

    public function admin_full_tournaments_participations_first()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '1st Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();

        return view('backend.administrator.participation.tournaments.full-paid-1st', compact('paid'));
    }

    public function admin_full_tournaments_participations_second()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '2nd Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();

        return view('backend.administrator.participation.tournaments.full-paid-2nd', compact('paid'));
    }

    public function admin_full_tournaments_participations_third()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '3rd Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();

        return view('backend.administrator.participation.tournaments.full-paid-3rd', compact('paid'));
    }

    public function admin_full_tournaments_participations_fourth()
    {
        $paid = Payment::where(['status' => 'Paid', 'is_full' => 'Yes', 'draw_status' => '4th Draw'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();

        return view('backend.administrator.participation.tournaments.full-paid-4th', compact('paid'));
    }

    public function admin_declined_tournaments_participations()
    {
        $declined = Payment::where(['status' => 'Declined'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        return view('backend.administrator.participation.tournaments.declined', compact('declined'));
    }


    public function admin_pending_leagues_participations()
    {
        $pending = Payment::where(['league_status' => 'Pending'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        return view('backend.administrator.participation.leagues.pending', compact('pending'));
    }

    public function admin_paid_leagues_participations()
    {
        $paid = Payment::where(['league_status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['league_status' => 'Paid', 'is_full' => 'No'])->whereYear('created_at', date('Y'))->sum('league_fees');
        return view('backend.administrator.participation.leagues.paid', compact('paid', 'amount'));
    }

    public function admin_full_leagues_participations()
    {
        $paid = Payment::where(['league_status' => 'Paid', 'is_full' => 'Yes'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        $amount = Payment::where(['league_status' => 'Paid', 'is_full' => 'Yes'])->whereYear('created_at', date('Y'))->sum('league_fees');
        return view('backend.administrator.participation.leagues.full-paid', compact('paid', 'amount'));
    }

    public function admin_declined_leagues_participations()
    {
        $declined = Payment::where(['league_status' => 'Declined'])->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get();
        return view('backend.administrator.participation.leagues.declined', compact('declined'));
    }


    public function get_single_tournament($id)
    {
        $get_payment = Payment::findOrFail($id);
        $get_tournaments = json_decode($get_payment->tournaments);

        $tournament_names = [];
        $tournament_start = [];

        foreach($get_tournaments as $key => $get_tournament) {
            $tournament = Tournament::findOrFail($get_tournament);
            $start = date('F y', strtotime($tournament->start));
            $end = date('F y', strtotime($tournament->end));

            array_push($tournament_names, "<i class='mdi mdi-tennis text-success font-bold me-1'></i> ".$tournament->name." (".$start."-".$end.")<br><br>");
        }

        return $tournament_names;
    }

    public function get_single_league($id)
    {
        $get_payment = Payment::findOrFail($id);
        $get_leagues = json_decode($get_payment->leagues);

        $league_names = [];
        $league_start = [];

        foreach($get_leagues as $key => $get_league) {
            $league = League::findOrFail($get_league);
            $start = date('F y', strtotime($league->start));
            $end = date('F y', strtotime($league->end));

            array_push($league_names, "<i class='mdi mdi-tennis text-success font-bold me-1'></i> ".$league->name." (".$start."-".$end.")<br><br>");
        }

        return $league_names;
    }

    public function approve_tournament_participation(Request $request, $id)
    {
        $this->validate($request, [
            'payment_info' => 'required',
        ]);

        $payment = Payment::findOrFail($id);
        
        if($request->payment_info == 'Free'){
            $total = 0 + $payment->league_fees;
            $payment->tournament_fees = 0;
            $payment->total_fees = $total;
        }

        $payment->status = 'Paid';
        $payment->payment_info = $request->payment_info;
        $payment->save();

        $user = User::findOrFail($payment->user_id);
        Mail::to($user->email)->send(new PaymentApprove($user, $payment));

        Session::flash('success', 'Tour. Payment Accepted Successfully !');
        return redirect()->back();

    }

    public function decline_tournament_participation(Request $request, $id)
    {
        $this->validate($request, [
            'decline_reason' => 'required',
        ]);

        $get_payment = Payment::findOrFail($id);
        $get_payment->status = 'Declined';
        $get_payment->decline_reason = $request->decline_reason;
        $get_payment->save();

        Session::flash('error', 'Tournament Payment Declined !');
        return redirect()->back();

    }

    public function approve_league_participation(Request $request, $id)
    {
        $this->validate($request, [
            'payment_info_league' => 'required',
        ]);

        $payment = Payment::findOrFail($id);

        if($request->payment_info_league == 'Free'){
            $total = 0 + $payment->tournament_fees;
            $payment->league_fees = 0;
            $payment->total_fees = $total;
        }

        $payment->league_status = 'Paid';
        $payment->payment_info_league = $request->payment_info_league;
        $payment->save();

        $user = User::findOrFail($payment->user_id);
        Mail::to($user->email)->send(new PaymentApproveLeague($user, $payment));

        Session::flash('success', 'League Payment Accepted Successfully !');
        return redirect()->back();

    }

    public function decline_league_participation(Request $request, $id)
    {
        $this->validate($request, [
            'decline_reason_league' => 'required',
        ]);

        $get_payment = Payment::findOrFail($id);
        $get_payment->league_status = 'Declined';
        $get_payment->decline_reason_league = $request->decline_reason_league;
        $get_payment->save();

        Session::flash('error', 'League Payment Declined !');
        return redirect()->back();

    }

    public function admin_pending_membership()
    {
        $pending = FullFreeMember::where(['status' => 'Pending', 'year' => date('Y')])->orderBy('created_at', 'desc')->get();
        return view('backend.administrator.membership.pending', compact('pending'));
    }

    public function admin_approved_membership()
    {
        $approved = FullFreeMember::where(['status' => 'Approved', 'year' => date('Y')])->orderBy('created_at', 'desc')->get();
        $money_count = FullFreeMember::where(['status' => 'Approved', 'year' => date('Y')])->where('payment_info', '!=', 'Free')->count();        
        
        return view('backend.administrator.membership.approved', compact('approved', 'money_count'));
    }

    public function admin_declined_membership()
    {
        $declined = FullFreeMember::where(['status' => 'Declined', 'year' => date('Y')])->orderBy('created_at', 'desc')->get();
        return view('backend.administrator.membership.declined', compact('declined'));
    }

    public function delete_membership($id)
    {
        $find = FullFreeMember::findOrFail($id);
        
        $user = User::findOrFail($find->user_id);
        $user->status = null;
        $user->save();

        $find->delete();

        $settings = Settings::findOrFail(1);
        $payment = Payment::where(['user_id' => $user->id, 'draw_status' => $settings->tournaments_open_for, 'is_full' => 'Yes'])->whereYear('created_at', date('Y'))->first();

        if($payment) {
            $payment->tournaments = null;
            $payment->leagues = null;
            $payment->is_full = 'No';
            $payment->tournament_fees = 0;
            $payment->league_fees = 0;
            $payment->total_fees = 0;
            $payment->status = null;
            $payment->league_status = null;
            $payment->save();
        }

        Session::flash('error', 'Membership Deleted !');
        return redirect()->back();

    }


    public function send_emails()
    {
        $normal = User::where(['status' => null])->whereNotNull('approved_at')->get();   
        $normal_sents = User::whereNull('status')
                            ->where('is_sent', 'Yes')
                            ->whereNotNull('approved_at')
                            ->get();

        $full = User::where(['status' => 'Full Member'])->get();   
        $full_sents = User::where(['status' => 'Full Member', 'is_sent' => 'Yes'])->get();   

        return view('backend.administrator.send-emails', compact('normal', 'normal_sents', 'full', 'full_sents'));
    }

    public function send_mail_to_all_players(Request $request)
    {
        $this->validate($request, [
            'mail_msg' => 'required',
        ]);

        $users = User::whereNull('status')
                        ->whereNull('is_sent')
                        ->whereNotNull('approved_at')
                        ->get();

        foreach($users as $user) {
            $mail_msg = $request->mail_msg;
            Mail::to($user->email)->send(new MailToAllPlayers($user, $mail_msg));
            $user->is_sent = 'Yes';
            $user->save();
        }

        Session::flash('success', 'E-mails are sent to all normal players successfully !');
        return redirect()->back();

    }

    public function send_mail_to_all_fullmembers(Request $request)
    {
        $this->validate($request, [
            'mail_msg' => 'required',
        ]);

        $users = User::where(['status' => 'Full Member', 'is_sent' => null])->get();
        
        foreach($users as $user) {
            $mail_msg = $request->mail_msg;
            Mail::to($user->email)->send(new MailToAllFullMember($user, $mail_msg));
            $user->is_sent = 'Yes';
            $user->save();
        }

        Session::flash('success', 'E-mails are sent to all full members successfully !');
        return redirect()->back();

    }


    public function clear_mail_to_all_players(Request $request)
    {
        $users = User::where(['status' => null, 'is_sent' => 'Yes'])->get();

        foreach($users as $user) {
            $user->is_sent = null;
            $user->save();
        }

        Session::flash('success', 'E-mails of all normal players are cleared successfully !');
        return redirect()->back();

    }

    public function clear_mail_to_all_fullmembers(Request $request)
    {
        $users = User::where(['status' => 'Full Member', 'is_sent' => 'Yes'])->get();
        
        foreach($users as $user) {
            $user->is_sent = null;
            $user->save();
        }

        Session::flash('success', 'E-mails of all full member players are cleared successfully !');
        return redirect()->back();

    }


    public function approve_membership(Request $request, $id)
    {
        $get_member = FullFreeMember::findOrFail($id);
        $get_member->status = 'Approved';
        $get_member->payment_info = $request->payment_info;
        $get_member->save();

        $user = User::findOrFail($get_member->user_id);
        $user->status = 'Full Member';
        $user->save();

        // $settings = Settings::findOrFail(1);
        // $payment = Payment::where(['user_id' => $user->id, 'draw_status' => $settings->tournaments_open_for, 'is_full' => 'No'])->whereYear('created_at', date('Y'))->first();

        // if($payment) {
        //     $payment->tournaments = null;
        //     $payment->leagues = null;
        //     $payment->is_full = 'Yes';
        //     $payment->tournament_fees = 0;
        //     $payment->league_fees = 0;
        //     $payment->total_fees = 0;
        //     $payment->status = null;
        //     $payment->league_status = null;
        //     $payment->save();
        // }

        Mail::to($user->email)->send(new ApproveMembership($user));

        Session::flash('success', 'Membership Approved Successfully !');
        return redirect()->back();

    }

    public function decline_membership(Request $request, $id)
    {
        $get_member = FullFreeMember::findOrFail($id);
        $get_member->status = 'Declined';
        $get_member->decline_reason = $request->decline_reason;
        $get_member->save();

        $user = User::findOrFail($get_member->user_id);
        Mail::to($user->email)->send(new DeclineMembership($user));

        Session::flash('error', 'Membership Declined !');
        return redirect()->back();

    }

    public function get_settings()
    {
        $settings = Settings::findOrFail(1);
        return view('backend.administrator.settings', compact('settings'));
    }

    public function update_settings(Request $request)
    {
        $settings = Settings::findOrFail(1);
        $settings->two_tournament_fees = $request->two_tournament_fees;
        $settings->tournaments_open_for = $request->tournaments_open_for;
        $settings->leagues_open_for = $request->leagues_open_for;
        $settings->publish_button_status = $request->publish_button_status;
        $settings->save();

        Session::flash('success', 'Settings Saved !');
        return redirect()->back();

    }


    public function change_tournaments(Request $request, $id)
    {
        $this->validate($request, [
            "tournaments" => ["array", "max:2", "required"],
        ]);

        $payment = Payment::findOrFail($id);
        $payment->tournaments = json_encode($request->tournaments);
        $payment->tournament_fees = $request->tournament_fees;

        $payment->total_fees = $request->tournament_fees + $payment->league_fees;

        $payment->save();

        Session::flash('success', 'Tournaments Changed Successfully !');
        return redirect()->back();
    }

    public function change_leagues(Request $request, $id)
    {
        $this->validate($request, [
            "leagues" => ["array", "max:1", "required"],
        ]);

        $payment = Payment::findOrFail($id);
        $payment->leagues = json_encode($request->leagues);
        $payment->league_fees = $request->league_fees;

        $payment->total_fees = $request->league_fees + $payment->tournament_fees;

        $payment->save();

        Session::flash('success', 'League Changed Successfully !');
        return redirect()->back();
    }


    public function draw_tournament($id)
    {
        $tournament = Tournament::findOrFail($id);

        if($tournament->tree_size){
            if($tournament->tree_size == 8) {
                return redirect()->route('draw.tournament.eight.players', $tournament->id);
            } else if($tournament->tree_size == 16) {
                return redirect()->route('draw.tournament.sixteen.players', $tournament->id);
            } else if($tournament->tree_size == 32) {
                return redirect()->route('draw.tournament.thirtytwo.players', $tournament->id);
            } else {
                return redirect()->route('draw.tournament.sixtyfour.players', $tournament->id);
            }
        } else {
            $players = [];

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

            return view('backend.administrator.tournament.draw', compact('tournament', 'players'));
        }

    }


    public function draw_tournament_eight_players($id)
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

        return view('backend.administrator.tournament.draw.eight-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

    }

    public function draw_tournament_sixteen_players($id)
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

        return view('backend.administrator.tournament.draw.sixteen-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

    }

    public function draw_tournament_thirtytwo_players($id)
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

        return view('backend.administrator.tournament.draw.thirtytwo-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

    }

    public function draw_tournament_sixtyfour_players($id)
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

        return view('backend.administrator.tournament.draw.sixtyfour-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

    }


    public function group_league($id)
    {
        $league = League::findOrFail($id);

        $players = [];

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

                if($league->id == $value) {
                    array_push($players, $payment->user_id);
                }

            }

        }

        return view('backend.administrator.league.group', compact('league', 'players'));
    }

    public function group_tournament($id)
    {
        $league = Tournament::findOrFail($id);

        if (strpos($league->name, "ELITE1500") !== false) {

            $players = [];

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

                    if($league->id == $value) {
                        array_push($players, $payment->user_id);
                    }

                }

            }

            return view('backend.administrator.tournament.group', compact('league', 'players'));

        } else {
            abort(403);
        }
    }

    public function draw_league($id)
    {
        $league = League::findOrFail($id);
        return view('backend.administrator.league.draw', compact('league'));
    }

    public function tree_league(Request $request, $id)
    {
        $this->validate($request, [
            'tree_size' => 'required',
        ]);
        
        $league = League::findOrFail($id);

        $league->tree_size = $request->tree_size;

        $league->save();

        Session::flash('info', 'League Tree Generated Successfully !');

        if($league->tree_size == 8) {
            return redirect()->route('draw.league.eight.players', $league->id);
        } else if($league->tree_size == 16) {
            return redirect()->route('draw.league.sixteen.players', $league->id);
        } else if($league->tree_size == 32) {
            return redirect()->route('draw.league.thirtytwo.players', $league->id);
        } else {
            abort(403);
        }
        

    }


    public function draw_league_eight_players($id)
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

        return view('backend.administrator.league.draw.eight-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

    }


    public function draw_league_sixteen_players($id)
    {
        $tournament = League::findOrFail($id);
        $players = [];
        $pts_array = [];

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

        foreach ($payments as $key => $payment) {

            if ($payment->league_status == 'Paid') {
                if ($payment->leagues) {
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


        // for($i = 1; $i < $tournament->group_size + 1; $i++){
        //     $grp_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

        //     $stats_array = json_decode($tournament->{'group_'.$grp_word.'_stats'}, true);

        //     if ($stats_array) {
        //         foreach($stats_array as $stat_key => $stat_value) {
        //             $pts_array[$stat_key] = $stat_value;
        //         }

        //         uasort($pts_array, function($a, $b) {
        //             return $b['pts'] < $a['pts'] ? -1 : 1;
        //         });


        //         $incr = 1;
        //         foreach($pts_array as $plr_key => $value) {
        //             if($incr < 3) {
        //                 array_push($players, $plr_key);
        //             }
        //             $incr++;
        //         }

        //     }
        // }



        // $key_values = array_column($pts_array, 'pts'); 
        // array_multisort($key_values, SORT_DESC, $pts_array);
        
        // array_slice($pts_array, 0, 2, true);
        // uasort($pts_array, function($a, $b){
        //      return $b['pts'] <=> $a['pts'];
        //  });
        
        
        return view('backend.administrator.league.draw.sixteen-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

    }


    public function draw_league_thirtytwo_players($id)
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

        foreach ($payments as $key => $payment) {

            if ($payment->league_status == 'Paid') {
                if ($payment->leagues) {
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

        return view('backend.administrator.league.draw.thirtytwo-players', compact('tournament', 'players', 't_d_rou1', 't_d_rou2', 't_d_rou3', 't_d_quar', 't_d_semf', 't_d_final', 'round_one_matches', 'round_one_results', 'round_one_winners', 'round_one_status', 'round_one_auto_selection', 'round_one_retires', 'round_two_matches', 'round_two_results', 'round_two_winners', 'round_two_status', 'round_two_auto_selection', 'round_two_retires', 'round_three_matches', 'round_three_results', 'round_three_winners', 'round_three_status', 'round_three_auto_selection', 'round_three_retires', 'quarter_final_matches', 'quarter_final_results', 'quarter_final_winners', 'quarter_final_status', 'quarter_final_auto_selection', 'quarter_final_retires', 'semi_final_matches', 'semi_final_results', 'semi_final_winners', 'semi_final_status', 'semi_final_auto_selection', 'semi_final_retires', 'final_matches', 'final_results', 'final_winners', 'final_status', 'final_auto_selection', 'final_retires'));

    }


    public function tree_tournament(Request $request, $id)
    {
        $this->validate($request, [
            'tree_size' => 'required',
        ]);

        $tournament = Tournament::findOrFail($id);
        $tournament->tree_size = $request->tree_size;

        $tournament->save();

        Session::flash('info', 'Tournament Tree Generated Successfully !');

        if($tournament->tree_size == 8) {
            return redirect()->route('draw.tournament.eight.players', $tournament->id);
        } else if($tournament->tree_size == 16) {
            return redirect()->route('draw.tournament.sixteen.players', $tournament->id);
        } else if($tournament->tree_size == 32) {
            return redirect()->route('draw.tournament.thirtytwo.players', $tournament->id);
        } else {
            return redirect()->route('draw.tournament.sixtyfour.players', $tournament->id);
        }

    }


    public function store_league_group(Request $request, $id)
    {

        $this->validate($request, [
            'group_size' => 'required',
            'player_per_group' => 'required',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $league->group_size = $request->group_size;
        $league->player_per_group = $request->player_per_group;

        $league->save();

        Session::flash('info', 'Groups Generated Successfully !');
        return redirect()->back();

    }


    public function sms_send($to, $message)
    {

        $post['to'] = array($to);
        $post['text'] = $message;
        $post['from'] = "Tennis4all";
        $post['parts'] = 2;
        $user = "Eirini";
        $password = "Tennis4all!";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://gtw.nrsgateway.com/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            "Authorization: Basic ".base64_encode($user.":".$password)
        ));
        $result = curl_exec ($ch);
        
    }

    public function sms_send_two($to_two, $message_two)
    {

        $post['to'] = array($to_two);
        $post['text'] = $message_two;
        $post['from'] = "Tennis4all";
        $post['parts'] = 2;
        $user = "Eirini";
        $password = 'Tennis4all!';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://gtw.nrsgateway.com/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            "Authorization: Basic ".base64_encode($user.":".$password)
        ));
        $result = curl_exec ($ch);

    }


    public function sms_send_league($to_league, $message_league)
    {

        $post['to'] = array($to_league);
        $post['text'] = $message_league;
        $post['from'] = "Tennis4all";
        $post['parts'] = 4;
        $user = "Eirini";
        $password = "Tennis4all!";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://gtw.nrsgateway.com/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            "Authorization: Basic ".base64_encode($user.":".$password)
        ));
        $result = curl_exec ($ch);
        
    }


    public function submit_group_one_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_1' => 'array|required',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        foreach($request->group_1 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_1;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_one_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 1.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }
        
        $league->group_one_players = json_encode($request->group_1);
        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_two_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_2' => 'array|required',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_2 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_2;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_two_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 2.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_two_players = json_encode($request->group_2);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_three_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_3' => 'array|required',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_3 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_3;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_three_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 3.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_three_players = json_encode($request->group_3);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_four_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_4' => 'array|required',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_4 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_4;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_four_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 4.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_four_players = json_encode($request->group_4);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_five_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_5' => 'array|required|min:4',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_5 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_5;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_five_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 5.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_five_players = json_encode($request->group_5);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_six_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_6' => 'array|required|min:4',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_6 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_6;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_six_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 6.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_six_players = json_encode($request->group_6);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_seven_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_7' => 'array|required|min:4',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_7 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_7;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_seven_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 7.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_seven_players = json_encode($request->group_7);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_eight_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_8' => 'array|required|min:4',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_8 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_8;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_eight_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 8.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_eight_players = json_encode($request->group_8);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_nine_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_9' => 'array|required|min:4',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_9 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_9;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_nine_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 9.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_nine_players = json_encode($request->group_9);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }

    public function submit_group_ten_league(Request $request, $id)
    {
        $this->validate($request, [
            'group_10' => 'array|required|min:4',
        ]);

        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        foreach($request->group_10 as $plr_id) {
            $user = User::findOrFail($plr_id);
            
            $plrs_arr = $request->group_10;
            $arr_key = array_search($plr_id, $plrs_arr);

            if ($arr_key !== false) {
                unset($plrs_arr[$arr_key]);
            
                $new_plrs_arr = [];
                $i = 1;

                foreach($plrs_arr as $get_plr_id) {
                    $find_plr = User::findOrFail($get_plr_id);
                    $find_plr_phn = str_replace('+357', '', $find_plr->phone);
                    array_push($new_plrs_arr, $i++.'. '.$find_plr->name .' '.$find_plr_phn."\n");
                }

                $t_d_group = json_decode($league->group_ten_deadline);
                $end_group = explode(", ", $t_d_group->end);
                
                $to_league = $user->phone;
                $new_plrs_string = implode($new_plrs_arr);
                $message_league = "Hello {$league->name}, Group 10.\nYour opponents for Leagues are:\n{$new_plrs_string}Deadline is {$end_group[0]}. Please arrange your match as soon as possible since no extension is available.\nSupervisor: {$league->supervisor_name} {$league->supervisor_phone}.\nThanks and good luck.";
                $this->sms_send_league($to_league, $message_league);

            } else {
                Session::flash('error', 'Player Not Found !');
                return redirect()->back();
            }

        }

        $league->group_ten_players = json_encode($request->group_10);

        $league->save();

        Session::flash('success', 'Group Players Saved Successfully !');
        return redirect()->back();
    }


    public function submit_league_deadlines(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'Tournament') {
                $league = Tournament::findOrFail($id);
            } else {
                $league = League::findOrFail($id);
            }
        } else {
            $league = Tournament::findOrFail($id);
        }
        
        for ($i = 1; $i < $league->group_size + 1; $i++) {
            $grp_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            ${"group_" . $grp_word . "_deadline_array"} = [];
            ${"group_" . $grp_word . "_deadline_array"}['start'] = $request->{"group_" . $i . "_start"};
            ${"group_" . $grp_word . "_deadline_array"}['end'] = $request->{"group_" . $i . "_end"}; 
            $league->{"group_" . $grp_word . "_deadline"} = json_encode(${"group_" . $grp_word . "_deadline_array"});
            
        }

        $league->save();

        Session::flash('success', 'Deadlines Selected Successfully !');
        return redirect()->back();

    }



    // GROUP 1
    public function submit_group_one_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_one_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_1_mat_'.$i.'_player_1' => 'required',
                    'group_1_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_one_matches_array = [];
                $match_chk_players  = [$request->{"group_1_mat_" . $i . "_player_1"}, $request->{"group_1_mat_" . $i . "_player_2"}];

                $group_one_matches_array['match_'.$i] = $request->{"group_1_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_1_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_one_matches) {

                    $find_matches = json_decode($league->group_one_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_1_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_1_mat_" . $i . "_player_2"};
                    $league->group_one_matches = json_encode($find_matches);

                } else {
                    $league->group_one_matches = json_encode($group_one_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_one_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_one_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_one_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_one_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_one_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_one_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_one_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_one_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_one_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_one_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_one_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_one_results) {
                    
                    $find_results = json_decode($league->group_one_results, true);
                    
                    // if(array_key_exists('match_'.$i, $find_results)) {
                        
                    //     $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};
                    //     $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"};

                    //     $change_stats = json_decode($league->group_one_stats, true);

                    //     if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    //         $change_stats[$request->{"p1_m" . $i}]['gp']  = $change_stats[$request->{"p1_m" . $i}]['gp'] - 1;
                    //         $change_stats[$request->{"p1_m" . $i}]['w']   = $change_stats[$request->{"p1_m" . $i}]['w'] - 1;
                    //         $change_stats[$request->{"p1_m" . $i}]['l']   = $change_stats[$request->{"p1_m" . $i}]['l'] + 0;
                    //         $change_stats[$request->{"p1_m" . $i}]['pts'] = $change_stats[$request->{"p1_m" . $i}]['pts'] - 3;

                    //         $change_stats[$request->{"p2_m" . $i}]['gp']  = $change_stats[$request->{"p2_m" . $i}]['gp'] - 1;
                    //         $change_stats[$request->{"p2_m" . $i}]['w']   = $change_stats[$request->{"p2_m" . $i}]['w'] + 0;
                    //         $change_stats[$request->{"p2_m" . $i}]['l']   = $change_stats[$request->{"p2_m" . $i}]['l'] - 1;
                    //         $change_stats[$request->{"p2_m" . $i}]['pts'] = $change_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                    //     } else {
                    //         $change_stats[$request->{"p1_m" . $i}]['gp']  = $change_stats[$request->{"p1_m" . $i}]['gp'] - 1;
                    //         $change_stats[$request->{"p1_m" . $i}]['w']   = $change_stats[$request->{"p1_m" . $i}]['w'] + 0;
                    //         $change_stats[$request->{"p1_m" . $i}]['l']   = $change_stats[$request->{"p1_m" . $i}]['l'] - 1;
                    //         $change_stats[$request->{"p1_m" . $i}]['pts'] = $change_stats[$request->{"p1_m" . $i}]['pts'] + 0;

                    //         $change_stats[$request->{"p2_m" . $i}]['gp']  = $change_stats[$request->{"p2_m" . $i}]['gp'] - 1;
                    //         $change_stats[$request->{"p2_m" . $i}]['w']   = $change_stats[$request->{"p2_m" . $i}]['w'] - 1;
                    //         $change_stats[$request->{"p2_m" . $i}]['l']   = $change_stats[$request->{"p2_m" . $i}]['l'] + 0;
                    //         $change_stats[$request->{"p2_m" . $i}]['pts'] = $change_stats[$request->{"p2_m" . $i}]['pts'] - 3;
                    //     }

                    //     $league->group_one_stats = json_encode($change_stats); 
                        
                    // }


                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_one_results = json_encode($find_results);

                } else {
                    $league->group_one_results = json_encode($group_one_results_array);
                }

                
                $group_one_stats_array = [];
                if($league->group_one_stats) {
                    $find_stats = json_decode($league->group_one_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_one_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_one_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_one_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_one_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_one_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_one_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_one_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_one_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_one_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_one_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_one_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_one_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_one_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_one_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_one_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_one_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_one_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_one_stats = json_encode($group_one_stats_array);
                    
                }


                $group_one_status_array = [];
                if($league->group_one_status) {
                    
                    $find_status = json_decode($league->group_one_status, true);
                    if(array_key_exists('match_'.$i, $find_status)) {
                        unset($find_status['match_'.$i]);
                    }

                    $find_status['match_'.$i] = $request->{"group_1_mat_" . $i . "_status"};
                    $league->group_one_status = json_encode($find_status);

                } else {
                    $group_one_status_array['match_'.$i] = $request->{"group_1_mat_" . $i . "_status"};
                    $league->group_one_status = json_encode($group_one_status_array);
                }


                $group_one_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_one_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_one_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_one_winners) {
                    $find_winners = json_decode($league->group_one_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_one_winners = json_encode($find_winners);

                } else {
                    $league->group_one_winners = json_encode($group_one_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }

    
    // GROUP 2
    public function submit_group_two_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_two_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_2_mat_'.$i.'_player_1' => 'required',
                    'group_2_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_two_matches_array = [];
                $match_chk_players  = [$request->{"group_2_mat_" . $i . "_player_1"}, $request->{"group_2_mat_" . $i . "_player_2"}];

                $group_two_matches_array['match_'.$i] = $request->{"group_2_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_2_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_two_matches) {

                    $find_matches = json_decode($league->group_two_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_2_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_2_mat_" . $i . "_player_2"};
                    $league->group_two_matches = json_encode($find_matches);

                } else {
                    $league->group_two_matches = json_encode($group_two_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_two_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_two_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_two_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_two_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_two_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_two_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_two_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_two_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_two_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_two_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_two_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_two_results) {
                    
                    $find_results = json_decode($league->group_two_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_two_results = json_encode($find_results);

                } else {
                    $league->group_two_results = json_encode($group_two_results_array);
                }

                
                $group_two_stats_array = [];
                if($league->group_two_stats) {
                    $find_stats = json_decode($league->group_two_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_two_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_two_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_two_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_two_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_two_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_two_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_two_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_two_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_two_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_two_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_two_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_two_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_two_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_two_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_two_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_two_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_two_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_two_stats = json_encode($group_two_stats_array);
                    
                }


                $group_two_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_two_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_two_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_two_winners) {
                    $find_winners = json_decode($league->group_two_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_two_winners = json_encode($find_winners);

                } else {
                    $league->group_two_winners = json_encode($group_two_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 3
    public function submit_group_three_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_three_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_3_mat_'.$i.'_player_1' => 'required',
                    'group_3_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_three_matches_array = [];
                $match_chk_players  = [$request->{"group_3_mat_" . $i . "_player_1"}, $request->{"group_3_mat_" . $i . "_player_2"}];

                $group_three_matches_array['match_'.$i] = $request->{"group_3_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_3_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_three_matches) {

                    $find_matches = json_decode($league->group_three_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_3_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_3_mat_" . $i . "_player_2"};
                    $league->group_three_matches = json_encode($find_matches);

                } else {
                    $league->group_three_matches = json_encode($group_three_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_three_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_three_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_three_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_three_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_three_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_three_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_three_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_three_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_three_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_three_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_three_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_three_results) {
                    
                    $find_results = json_decode($league->group_three_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_three_results = json_encode($find_results);

                } else {
                    $league->group_three_results = json_encode($group_three_results_array);
                }

                
                $group_three_stats_array = [];
                if($league->group_three_stats) {
                    $find_stats = json_decode($league->group_three_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_three_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_three_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_three_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_three_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_three_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_three_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_three_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_three_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_three_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_three_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_three_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_three_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_three_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_three_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_three_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_three_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_three_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_three_stats = json_encode($group_three_stats_array);
                    
                }


                $group_three_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_three_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_three_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_three_winners) {
                    $find_winners = json_decode($league->group_three_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_three_winners = json_encode($find_winners);

                } else {
                    $league->group_three_winners = json_encode($group_three_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 4
    public function submit_group_four_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_four_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_4_mat_'.$i.'_player_1' => 'required',
                    'group_4_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_four_matches_array = [];
                $match_chk_players  = [$request->{"group_4_mat_" . $i . "_player_1"}, $request->{"group_4_mat_" . $i . "_player_2"}];

                $group_four_matches_array['match_'.$i] = $request->{"group_4_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_4_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_four_matches) {

                    $find_matches = json_decode($league->group_four_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_4_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_4_mat_" . $i . "_player_2"};
                    $league->group_four_matches = json_encode($find_matches);

                } else {
                    $league->group_four_matches = json_encode($group_four_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_four_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_four_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_four_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_four_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_four_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_four_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_four_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_four_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_four_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_four_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_four_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_four_results) {
                    
                    $find_results = json_decode($league->group_four_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_four_results = json_encode($find_results);

                } else {
                    $league->group_four_results = json_encode($group_four_results_array);
                }

                
                $group_four_stats_array = [];
                if($league->group_four_stats) {
                    $find_stats = json_decode($league->group_four_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_four_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_four_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_four_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_four_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_four_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_four_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_four_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_four_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_four_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_four_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_four_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_four_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_four_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_four_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_four_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_four_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_four_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_four_stats = json_encode($group_four_stats_array);
                    
                }


                $group_four_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_four_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_four_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_four_winners) {
                    $find_winners = json_decode($league->group_four_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_four_winners = json_encode($find_winners);

                } else {
                    $league->group_four_winners = json_encode($group_four_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 5
    public function submit_group_five_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_five_players, true);
        $count_players = count($group_players);
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));
        
            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_5_mat_'.$i.'_player_1' => 'required',
                    'group_5_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_five_matches_array = [];
                $match_chk_players  = [$request->{"group_5_mat_" . $i . "_player_1"}, $request->{"group_5_mat_" . $i . "_player_2"}];

                $group_five_matches_array['match_'.$i] = $request->{"group_5_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_5_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_five_matches) {

                    $find_matches = json_decode($league->group_five_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_5_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_5_mat_" . $i . "_player_2"};
                    $league->group_five_matches = json_encode($find_matches);

                } else {
                    $league->group_five_matches = json_encode($group_five_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }
            
        }

    }

    public function submit_group_five_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_five_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_five_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_five_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_five_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_five_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_five_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_five_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_five_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_five_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_five_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_five_results) {
                    
                    $find_results = json_decode($league->group_five_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_five_results = json_encode($find_results);

                } else {
                    $league->group_five_results = json_encode($group_five_results_array);
                }

                
                $group_five_stats_array = [];
                if($league->group_five_stats) {
                    $find_stats = json_decode($league->group_five_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_five_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_five_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_five_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_five_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_five_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_five_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_five_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_five_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_five_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_five_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_five_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_five_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_five_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_five_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_five_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_five_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_five_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_five_stats = json_encode($group_five_stats_array);
                    
                }


                $group_five_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_five_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_five_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_five_winners) {
                    $find_winners = json_decode($league->group_five_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_five_winners = json_encode($find_winners);

                } else {
                    $league->group_five_winners = json_encode($group_five_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 6
    public function submit_group_six_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_six_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;
        
        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_6_mat_'.$i.'_player_1' => 'required',
                    'group_6_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_six_matches_array = [];
                $match_chk_players  = [$request->{"group_6_mat_" . $i . "_player_1"}, $request->{"group_6_mat_" . $i . "_player_2"}];

                $group_six_matches_array['match_'.$i] = $request->{"group_6_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_6_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_six_matches) {

                    $find_matches = json_decode($league->group_six_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_6_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_6_mat_" . $i . "_player_2"};
                    $league->group_six_matches = json_encode($find_matches);

                } else {
                    $league->group_six_matches = json_encode($group_six_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_six_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_six_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_six_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_six_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_six_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_six_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_six_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_six_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_six_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_six_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_six_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_six_results) {
                    
                    $find_results = json_decode($league->group_six_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_six_results = json_encode($find_results);

                } else {
                    $league->group_six_results = json_encode($group_six_results_array);
                }

                
                $group_six_stats_array = [];
                if($league->group_six_stats) {
                    $find_stats = json_decode($league->group_six_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_six_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_six_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_six_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_six_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_six_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_six_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_six_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_six_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_six_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_six_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_six_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_six_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_six_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_six_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_six_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_six_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_six_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_six_stats = json_encode($group_six_stats_array);
                    
                }


                $group_six_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_six_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_six_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_six_winners) {
                    $find_winners = json_decode($league->group_six_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_six_winners = json_encode($find_winners);

                } else {
                    $league->group_six_winners = json_encode($group_six_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 7
    public function submit_group_seven_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_seven_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_7_mat_'.$i.'_player_1' => 'required',
                    'group_7_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_seven_matches_array = [];
                $match_chk_players  = [$request->{"group_7_mat_" . $i . "_player_1"}, $request->{"group_7_mat_" . $i . "_player_2"}];

                $group_seven_matches_array['match_'.$i] = $request->{"group_7_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_7_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_seven_matches) {

                    $find_matches = json_decode($league->group_seven_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_7_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_7_mat_" . $i . "_player_2"};
                    $league->group_seven_matches = json_encode($find_matches);

                } else {
                    $league->group_seven_matches = json_encode($group_seven_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_seven_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_seven_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_seven_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_seven_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_seven_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_seven_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_seven_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_seven_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_seven_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_seven_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_seven_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_seven_results) {
                    
                    $find_results = json_decode($league->group_seven_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_seven_results = json_encode($find_results);

                } else {
                    $league->group_seven_results = json_encode($group_seven_results_array);
                }

                
                $group_seven_stats_array = [];
                if($league->group_seven_stats) {
                    $find_stats = json_decode($league->group_seven_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_seven_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_seven_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_seven_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_seven_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_seven_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_seven_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_seven_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_seven_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_seven_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_seven_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_seven_stats = json_encode($group_seven_stats_array);
                    
                }


                $group_seven_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_seven_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_seven_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_seven_winners) {
                    $find_winners = json_decode($league->group_seven_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_seven_winners = json_encode($find_winners);

                } else {
                    $league->group_seven_winners = json_encode($group_seven_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 8
    public function submit_group_eight_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_eight_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_8_mat_'.$i.'_player_1' => 'required',
                    'group_8_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_eight_matches_array = [];
                $match_chk_players  = [$request->{"group_8_mat_" . $i . "_player_1"}, $request->{"group_8_mat_" . $i . "_player_2"}];

                $group_eight_matches_array['match_'.$i] = $request->{"group_8_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_8_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_eight_matches) {

                    $find_matches = json_decode($league->group_eight_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_8_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_8_mat_" . $i . "_player_2"};
                    $league->group_eight_matches = json_encode($find_matches);

                } else {
                    $league->group_eight_matches = json_encode($group_eight_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_eight_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_eight_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_eight_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_eight_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_eight_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_eight_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_eight_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_eight_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_eight_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_eight_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_eight_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_eight_results) {
                    
                    $find_results = json_decode($league->group_eight_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_eight_results = json_encode($find_results);

                } else {
                    $league->group_eight_results = json_encode($group_eight_results_array);
                }

                
                $group_eight_stats_array = [];
                if($league->group_eight_stats) {
                    $find_stats = json_decode($league->group_eight_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_eight_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_eight_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_eight_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_eight_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_eight_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_eight_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_eight_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_eight_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_eight_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_eight_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_eight_stats = json_encode($group_eight_stats_array);
                    
                }


                $group_eight_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_eight_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_eight_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_eight_winners) {
                    $find_winners = json_decode($league->group_eight_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_eight_winners = json_encode($find_winners);

                } else {
                    $league->group_eight_winners = json_encode($group_eight_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 9
    public function submit_group_nine_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_nine_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_9_mat_'.$i.'_player_1' => 'required',
                    'group_9_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_nine_matches_array = [];
                $match_chk_players  = [$request->{"group_9_mat_" . $i . "_player_1"}, $request->{"group_9_mat_" . $i . "_player_2"}];

                $group_nine_matches_array['match_'.$i] = $request->{"group_9_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_9_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_nine_matches) {

                    $find_matches = json_decode($league->group_nine_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_9_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_9_mat_" . $i . "_player_2"};
                    $league->group_nine_matches = json_encode($find_matches);

                } else {
                    $league->group_nine_matches = json_encode($group_nine_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_nine_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_nine_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_nine_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }


                $group_nine_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_nine_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_nine_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_nine_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_nine_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_nine_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_nine_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_nine_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_nine_results) {
                    
                    $find_results = json_decode($league->group_nine_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_nine_results = json_encode($find_results);

                } else {
                    $league->group_nine_results = json_encode($group_nine_results_array);
                }

                
                $group_nine_stats_array = [];
                if($league->group_nine_stats) {
                    $find_stats = json_decode($league->group_nine_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_nine_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_nine_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_nine_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_nine_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_nine_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_nine_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_nine_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_nine_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_nine_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_nine_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_nine_stats = json_encode($group_nine_stats_array);
                    
                }


                $group_nine_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_nine_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_nine_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_nine_winners) {
                    $find_winners = json_decode($league->group_nine_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_nine_winners = json_encode($find_winners);

                } else {
                    $league->group_nine_winners = json_encode($group_nine_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }


    // GROUP 10
    public function submit_group_ten_matches(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }
        
        $group_players = json_decode($league->group_ten_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {
                
                $this->validate($request, [
                    'group_10_mat_'.$i.'_player_1' => 'required',
                    'group_10_mat_'.$i.'_player_2' => 'required',
                ]);

                $group_ten_matches_array = [];
                $match_chk_players  = [$request->{"group_10_mat_" . $i . "_player_1"}, $request->{"group_10_mat_" . $i . "_player_2"}];

                $group_ten_matches_array['match_'.$i] = $request->{"group_10_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_10_mat_" . $i . "_player_2"};

                $chk_players = max(array_count_values($match_chk_players));
                
                if($chk_players > 1)
                {
                    Session::flash('error', '1 Player Can not be Assigned Twice !');
                    return redirect()->back();
                }

                if($league->group_ten_matches) {

                    $find_matches = json_decode($league->group_ten_matches, true);
                    if(array_key_exists('match_'.$i, $find_matches)) {
                        unset($find_matches['match_'.$i]);
                    }
                    $find_matches['match_'.$i] = $request->{"group_10_mat_" . $i . "_player_1"} . ' VS ' . $request->{"group_10_mat_" . $i . "_player_2"};
                    $league->group_ten_matches = json_encode($find_matches);

                } else {
                    $league->group_ten_matches = json_encode($group_ten_matches_array);
                }

                $league->save();

                Session::flash('success', 'Players Assigned to Match Successfully !');
                return redirect()->back();
            }

        }

    }

    public function submit_group_ten_results(Request $request, $match_word, $id)
    {
        if($request->chk_type == 'Tournament') {
            $league = Tournament::findOrFail($id);
        } else {
            $league = League::findOrFail($id);
        }

        $group_players = json_decode($league->group_ten_players, true);
        $count_players = count($group_players);        
        $gr_matches = ($count_players * ($count_players - 1)) / 2;

        for($i = 1; $i < $gr_matches + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $match_word) {

                $this->validate($request, [
                    'p1_m'.$i       => 'required',
                    'p2_m'.$i       => 'required',
                    'p1_m'.$i.'_s1' => 'required',
                    'p1_m'.$i.'_s2' => 'required',
                    'p2_m'.$i.'_s1' => 'required',
                    'p2_m'.$i.'_s2' => 'required',
                ]);

                $group_ten_results_array = [];


                if ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 0;

                } elseif ($request->{"p2_m" . $i . "_s1"} > $request->{"p1_m" . $i . "_s1"} && $request->{"p2_m" . $i . "_s2"} > $request->{"p1_m" . $i . "_s2"}) {

                    ${"p1_m" . $i . "_total"} = 0;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                } elseif ($request->{"p1_m" . $i . "_s1"} < $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} > $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} > $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 2;
                    ${"p2_m" . $i . "_total"} = 1;

                } elseif ($request->{"p1_m" . $i . "_s1"} > $request->{"p2_m" . $i . "_s1"} && $request->{"p1_m" . $i . "_s2"} < $request->{"p2_m" . $i . "_s2"} && $request->{"p1_m" . $i . "_s3"} < $request->{"p2_m" . $i . "_s3"}) {

                    ${"p1_m" . $i . "_total"} = 1;
                    ${"p2_m" . $i . "_total"} = 2;

                }
                

                $group_ten_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                $group_ten_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                $group_ten_results_array['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                $group_ten_results_array['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                $group_ten_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};
                $group_ten_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                $group_ten_results_array['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                $group_ten_results_array['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                
                $rslt_chk = [$request->{"p1_m" . $i}, $request->{"p2_m" . $i}];
                $chk_rslt = max(array_count_values($rslt_chk));

                if($chk_rslt > 1)
                {
                    Session::flash('error', '1 Player Result Can not be Announced Twice !');
                    return redirect()->back();
                }

                if($league->group_ten_results) {
                    
                    $find_results = json_decode($league->group_ten_results, true);
                    
                    if(array_key_exists('match_'.$i, $find_results)) {
                        unset($find_results['match_'.$i]);
                    }

                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_1'] = $request->{"p1_m" . $i . "_s1"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_2'] = $request->{"p1_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['set_3'] = $request->{"p1_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p1_m" . $i}]['total'] = ${"p1_m" . $i . "_total"};

                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_1'] = $request->{"p2_m" . $i . "_s1"};       
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_2'] = $request->{"p2_m" . $i . "_s2"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['set_3'] = $request->{"p2_m" . $i . "_s3"};
                    $find_results['match_'.$i][$request->{"p2_m" . $i}]['total'] = ${"p2_m" . $i . "_total"}; 

                    $league->group_ten_results = json_encode($find_results);

                } else {
                    $league->group_ten_results = json_encode($group_ten_results_array);
                }

                
                $group_ten_stats_array = [];
                if($league->group_ten_stats) {
                    $find_stats = json_decode($league->group_ten_stats, true);

                    if(array_key_exists($request->{"p1_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = $find_stats[$request->{"p1_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = $find_stats[$request->{"p1_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = $find_stats[$request->{"p1_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = $find_stats[$request->{"p1_m" . $i}]['pts'] + 0;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 3;
                        } else {
                            $find_stats[$request->{"p1_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p1_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p1_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p1_m" . $i}]['pts'] = 0;
                        }

                    }


                    if(array_key_exists($request->{"p2_m" . $i}, $find_stats)) {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = $find_stats[$request->{"p2_m" . $i}]['gp'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = $find_stats[$request->{"p2_m" . $i}]['w'] + 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = $find_stats[$request->{"p2_m" . $i}]['l'] + 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = $find_stats[$request->{"p2_m" . $i}]['pts'] + 3;
                        }

                    } else {

                        if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 0;
                        } else {
                            $find_stats[$request->{"p2_m" . $i}]['gp']  = 1;
                            $find_stats[$request->{"p2_m" . $i}]['w']   = 1;
                            $find_stats[$request->{"p2_m" . $i}]['l']   = 0;
                            $find_stats[$request->{"p2_m" . $i}]['pts'] = 3;
                        }

                    }

                    $league->group_ten_stats = json_encode($find_stats);

                } else {

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['w']   = 1;
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['l']   = 0;
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['pts'] = 3;

                        $group_ten_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_ten_stats_array[$request->{"p2_m" . $i}]['w']   = 0;
                        $group_ten_stats_array[$request->{"p2_m" . $i}]['l']   = 1;
                        $group_ten_stats_array[$request->{"p2_m" . $i}]['pts'] = 0;
                    } else {
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['gp']  = 1;
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['w']   = 0;
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['l']   = 1;
                        $group_ten_stats_array[$request->{"p1_m" . $i}]['pts'] = 0;

                        $group_ten_stats_array[$request->{"p2_m" . $i}]['gp']  = 1;
                        $group_ten_stats_array[$request->{"p2_m" . $i}]['w']   = 1;
                        $group_ten_stats_array[$request->{"p2_m" . $i}]['l']   = 0;
                        $group_ten_stats_array[$request->{"p2_m" . $i}]['pts'] = 3;
                    }

                    $league->group_ten_stats = json_encode($group_ten_stats_array);
                    
                }


                $group_ten_winners_array = [];        
                if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                    $group_ten_winners_array['match_'.$i] = $request->{"p1_m" . $i};
                } else {
                   $group_ten_winners_array['match_'.$i] = $request->{"p2_m" . $i}; 
                }

                if($league->group_ten_winners) {
                    $find_winners = json_decode($league->group_ten_winners, true);
                    if(array_key_exists('match_'.$i, $find_winners)) {
                        unset($find_winners['match_'.$i]);
                    }

                    if(${"p1_m" . $i . "_total"} > ${"p2_m" . $i . "_total"}) {
                        $find_winners['match_'.$i] = $request->{"p1_m" . $i};
                    } else {
                        $find_winners['match_'.$i] = $request->{"p2_m" . $i}; 
                    }
                    $league->group_ten_winners = json_encode($find_winners);

                } else {
                    $league->group_ten_winners = json_encode($group_ten_winners_array);
                }

                $league->save();
                Session::flash('success', 'Result Published & Winners Selected Successfully !');
                return redirect()->back();
            }
        }
    }

    // TOURNAMENT DEADLINE
    public function submit_deadlines(Request $request, $id)
    {

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }


        if($tournament->tree_size == 8) {

            $this->validate($request, [
                'rou_1_start' => 'required',
                'rou_1_end'   => 'required',
                'sem_start'   => 'required',
                'sem_end'     => 'required',
                'final_start' => 'required',
                'final_end'   => 'required',
            ]);

        } elseif($tournament->tree_size == 16) {

            $this->validate($request, [
                'rou_1_start' => 'required',
                'rou_1_end'   => 'required',
                'quar_start'  => 'required',
                'quar_end'    => 'required',
                'sem_start'   => 'required',
                'sem_end'     => 'required',
                'final_start' => 'required',
                'final_end'   => 'required',
            ]);

        } elseif($tournament->tree_size == 32) {

            $this->validate($request, [
                'rou_1_start' => 'required',
                'rou_1_end'   => 'required',
                'rou_2_start' => 'required',
                'rou_2_end'   => 'required',
                'quar_start'  => 'required',
                'quar_end'    => 'required',
                'sem_start'   => 'required',
                'sem_end'     => 'required',
                'final_start' => 'required',
                'final_end'   => 'required',
            ]);

        }


        $round_one_deadline_array = [];
        $round_one_deadline_array['start'] = $request->rou_1_start;
        $round_one_deadline_array['end'] = $request->rou_1_end; 
        $tournament->round_one_deadline = json_encode($round_one_deadline_array);


        $round_two_deadline_array = [];
        if($request->rou_2_start == null && $request->rou_2_end == null) {
            $tournament->round_two_deadline = null;
        } else {
            $round_two_deadline_array['start'] = $request->rou_2_start;
            $round_two_deadline_array['end'] = $request->rou_2_end; 
            $tournament->round_two_deadline = json_encode($round_two_deadline_array);
        }



        $round_three_deadline_array = [];
        if($request->rou_3_start == null && $request->rou_3_end == null) {
            $tournament->round_three_deadline = null;
        } else {
            $round_three_deadline_array['start'] = $request->rou_3_start;
            $round_three_deadline_array['end'] = $request->rou_3_end; 
            $tournament->round_three_deadline = json_encode($round_three_deadline_array);
        }



        $quarter_final_deadline_array = [];
        if($request->quar_start == null && $request->quar_end == null) {
            $tournament->quarter_final_deadline = null;
        } else {
            $quarter_final_deadline_array['start'] = $request->quar_start;
            $quarter_final_deadline_array['end'] = $request->quar_end; 
            $tournament->quarter_final_deadline = json_encode($quarter_final_deadline_array);
        }


        $semi_final_deadline_array = [];
        $semi_final_deadline_array['start'] = $request->sem_start;
        $semi_final_deadline_array['end'] = $request->sem_end; 
        $tournament->semi_final_deadline = json_encode($semi_final_deadline_array);

        $final_deadline_array = [];
        $final_deadline_array['start'] = $request->final_start;
        $final_deadline_array['end'] = $request->final_end; 
        $tournament->final_deadline = json_encode($final_deadline_array);

        $tournament->save();

        Session::flash('success', 'Tournament Deadline Selected Successfully !');
        return redirect()->back();

    }


    // ROUND 1
    public function submit_round_one_match_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_1_player_1' => 'required',
            'rou_1_mat_1_player_2' => 'required',
        ]);

        
        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_1_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_1_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS


        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_1_player_1, $request->rou_1_mat_1_player_2];

        $round_one_matches_array['match_1'] = $request->rou_1_mat_1_player_1 . ' VS ' . $request->rou_1_mat_1_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {

            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_1', $find_matches)) {
                unset($find_matches['match_1']);
            }
            $find_matches['match_1'] = $request->rou_1_mat_1_player_1 . ' VS ' . $request->rou_1_mat_1_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();


        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_2_player_1' => 'required',
            'rou_1_mat_2_player_2' => 'required',
        ]);

             
        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_2_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_2_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS


        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_2_player_1, $request->rou_1_mat_2_player_2];

        $round_one_matches_array['match_2'] = $request->rou_1_mat_2_player_1 . ' VS ' . $request->rou_1_mat_2_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_2', $find_matches)) {
                unset($find_matches['match_2']);
            }
            $find_matches['match_2'] = $request->rou_1_mat_2_player_1 . ' VS ' . $request->rou_1_mat_2_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_3_player_1' => 'required',
            'rou_1_mat_3_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_3_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_3_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_3_player_1, $request->rou_1_mat_3_player_2];

        $round_one_matches_array['match_3'] = $request->rou_1_mat_3_player_1 . ' VS ' . $request->rou_1_mat_3_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_3', $find_matches)) {
                unset($find_matches['match_3']);
            }
            $find_matches['match_3'] = $request->rou_1_mat_3_player_1 . ' VS ' . $request->rou_1_mat_3_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_4_player_1' => 'required',
            'rou_1_mat_4_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_4_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_4_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_4_player_1, $request->rou_1_mat_4_player_2];

        $round_one_matches_array['match_4'] = $request->rou_1_mat_4_player_1 . ' VS ' . $request->rou_1_mat_4_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_4', $find_matches)) {
                unset($find_matches['match_4']);
            }
            $find_matches['match_4'] = $request->rou_1_mat_4_player_1 . ' VS ' . $request->rou_1_mat_4_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_five(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_5_player_1' => 'required',
            'rou_1_mat_5_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_5_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_5_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_5_player_1, $request->rou_1_mat_5_player_2];

        $round_one_matches_array['match_5'] = $request->rou_1_mat_5_player_1 . ' VS ' . $request->rou_1_mat_5_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_5', $find_matches)) {
                unset($find_matches['match_5']);
            }
            $find_matches['match_5'] = $request->rou_1_mat_5_player_1 . ' VS ' . $request->rou_1_mat_5_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_six(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_6_player_1' => 'required',
            'rou_1_mat_6_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_6_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_6_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_6_player_1, $request->rou_1_mat_6_player_2];

        $round_one_matches_array['match_6'] = $request->rou_1_mat_6_player_1 . ' VS ' . $request->rou_1_mat_6_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_6', $find_matches)) {
                unset($find_matches['match_6']);
            }
            $find_matches['match_6'] = $request->rou_1_mat_6_player_1 . ' VS ' . $request->rou_1_mat_6_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_seven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_7_player_1' => 'required',
            'rou_1_mat_7_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_7_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_7_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_7_player_1, $request->rou_1_mat_7_player_2];

        $round_one_matches_array['match_7'] = $request->rou_1_mat_7_player_1 . ' VS ' . $request->rou_1_mat_7_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_7', $find_matches)) {
                unset($find_matches['match_7']);
            }
            $find_matches['match_7'] = $request->rou_1_mat_7_player_1 . ' VS ' . $request->rou_1_mat_7_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_eight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_8_player_1' => 'required',
            'rou_1_mat_8_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_8_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_8_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_8_player_1, $request->rou_1_mat_8_player_2];

        $round_one_matches_array['match_8'] = $request->rou_1_mat_8_player_1 . ' VS ' . $request->rou_1_mat_8_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_8', $find_matches)) {
                unset($find_matches['match_8']);
            }
            $find_matches['match_8'] = $request->rou_1_mat_8_player_1 . ' VS ' . $request->rou_1_mat_8_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_nine(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_9_player_1' => 'required',
            'rou_1_mat_9_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_9_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_9_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_9_player_1, $request->rou_1_mat_9_player_2];

        $round_one_matches_array['match_9'] = $request->rou_1_mat_9_player_1 . ' VS ' . $request->rou_1_mat_9_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_9', $find_matches)) {
                unset($find_matches['match_9']);
            }
            $find_matches['match_9'] = $request->rou_1_mat_9_player_1 . ' VS ' . $request->rou_1_mat_9_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_ten(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_10_player_1' => 'required',
            'rou_1_mat_10_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_10_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_10_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_10_player_1, $request->rou_1_mat_10_player_2];

        $round_one_matches_array['match_10'] = $request->rou_1_mat_10_player_1 . ' VS ' . $request->rou_1_mat_10_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_10', $find_matches)) {
                unset($find_matches['match_10']);
            }
            $find_matches['match_10'] = $request->rou_1_mat_10_player_1 . ' VS ' . $request->rou_1_mat_10_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_eleven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_11_player_1' => 'required',
            'rou_1_mat_11_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_11_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_11_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_11_player_1, $request->rou_1_mat_11_player_2];

        $round_one_matches_array['match_11'] = $request->rou_1_mat_11_player_1 . ' VS ' . $request->rou_1_mat_11_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_11', $find_matches)) {
                unset($find_matches['match_11']);
            }
            $find_matches['match_11'] = $request->rou_1_mat_11_player_1 . ' VS ' . $request->rou_1_mat_11_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twelve(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_12_player_1' => 'required',
            'rou_1_mat_12_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_12_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_12_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_12_player_1, $request->rou_1_mat_12_player_2];

        $round_one_matches_array['match_12'] = $request->rou_1_mat_12_player_1 . ' VS ' . $request->rou_1_mat_12_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_12', $find_matches)) {
                unset($find_matches['match_12']);
            }
            $find_matches['match_12'] = $request->rou_1_mat_12_player_1 . ' VS ' . $request->rou_1_mat_12_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_thirteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_13_player_1' => 'required',
            'rou_1_mat_13_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_13_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_13_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_13_player_1, $request->rou_1_mat_13_player_2];

        $round_one_matches_array['match_13'] = $request->rou_1_mat_13_player_1 . ' VS ' . $request->rou_1_mat_13_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_13', $find_matches)) {
                unset($find_matches['match_13']);
            }
            $find_matches['match_13'] = $request->rou_1_mat_13_player_1 . ' VS ' . $request->rou_1_mat_13_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_fourteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_14_player_1' => 'required',
            'rou_1_mat_14_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_14_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_14_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_14_player_1, $request->rou_1_mat_14_player_2];

        $round_one_matches_array['match_14'] = $request->rou_1_mat_14_player_1 . ' VS ' . $request->rou_1_mat_14_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_14', $find_matches)) {
                unset($find_matches['match_14']);
            }
            $find_matches['match_14'] = $request->rou_1_mat_14_player_1 . ' VS ' . $request->rou_1_mat_14_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_fifteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_15_player_1' => 'required',
            'rou_1_mat_15_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_15_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_15_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_15_player_1, $request->rou_1_mat_15_player_2];

        $round_one_matches_array['match_15'] = $request->rou_1_mat_15_player_1 . ' VS ' . $request->rou_1_mat_15_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_15', $find_matches)) {
                unset($find_matches['match_15']);
            }
            $find_matches['match_15'] = $request->rou_1_mat_15_player_1 . ' VS ' . $request->rou_1_mat_15_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_sixteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_16_player_1' => 'required',
            'rou_1_mat_16_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_16_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_16_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_16_player_1, $request->rou_1_mat_16_player_2];

        $round_one_matches_array['match_16'] = $request->rou_1_mat_16_player_1 . ' VS ' . $request->rou_1_mat_16_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_16', $find_matches)) {
                unset($find_matches['match_16']);
            }
            $find_matches['match_16'] = $request->rou_1_mat_16_player_1 . ' VS ' . $request->rou_1_mat_16_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_seventeen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_17_player_1' => 'required',
            'rou_1_mat_17_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_17_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_17_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_17_player_1, $request->rou_1_mat_17_player_2];

        $round_one_matches_array['match_17'] = $request->rou_1_mat_17_player_1 . ' VS ' . $request->rou_1_mat_17_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_17', $find_matches)) {
                unset($find_matches['match_17']);
            }
            $find_matches['match_17'] = $request->rou_1_mat_17_player_1 . ' VS ' . $request->rou_1_mat_17_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_eighteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_18_player_1' => 'required',
            'rou_1_mat_18_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_18_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_18_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_18_player_1, $request->rou_1_mat_18_player_2];

        $round_one_matches_array['match_18'] = $request->rou_1_mat_18_player_1 . ' VS ' . $request->rou_1_mat_18_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_18', $find_matches)) {
                unset($find_matches['match_18']);
            }
            $find_matches['match_18'] = $request->rou_1_mat_18_player_1 . ' VS ' . $request->rou_1_mat_18_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_nineteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_19_player_1' => 'required',
            'rou_1_mat_19_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_19_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_19_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_19_player_1, $request->rou_1_mat_19_player_2];

        $round_one_matches_array['match_19'] = $request->rou_1_mat_19_player_1 . ' VS ' . $request->rou_1_mat_19_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_19', $find_matches)) {
                unset($find_matches['match_19']);
            }
            $find_matches['match_19'] = $request->rou_1_mat_19_player_1 . ' VS ' . $request->rou_1_mat_19_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twenty(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_20_player_1' => 'required',
            'rou_1_mat_20_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_20_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_20_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_20_player_1, $request->rou_1_mat_20_player_2];

        $round_one_matches_array['match_20'] = $request->rou_1_mat_20_player_1 . ' VS ' . $request->rou_1_mat_20_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_20', $find_matches)) {
                unset($find_matches['match_20']);
            }
            $find_matches['match_20'] = $request->rou_1_mat_20_player_1 . ' VS ' . $request->rou_1_mat_20_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentyone(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_21_player_1' => 'required',
            'rou_1_mat_21_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_21_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_21_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_21_player_1, $request->rou_1_mat_21_player_2];

        $round_one_matches_array['match_21'] = $request->rou_1_mat_21_player_1 . ' VS ' . $request->rou_1_mat_21_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_21', $find_matches)) {
                unset($find_matches['match_21']);
            }
            $find_matches['match_21'] = $request->rou_1_mat_21_player_1 . ' VS ' . $request->rou_1_mat_21_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentytwo(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_22_player_1' => 'required',
            'rou_1_mat_22_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_22_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_22_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_22_player_1, $request->rou_1_mat_22_player_2];

        $round_one_matches_array['match_22'] = $request->rou_1_mat_22_player_1 . ' VS ' . $request->rou_1_mat_22_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_22', $find_matches)) {
                unset($find_matches['match_22']);
            }
            $find_matches['match_22'] = $request->rou_1_mat_22_player_1 . ' VS ' . $request->rou_1_mat_22_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentythree(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_23_player_1' => 'required',
            'rou_1_mat_23_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_23_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_23_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_23_player_1, $request->rou_1_mat_23_player_2];

        $round_one_matches_array['match_23'] = $request->rou_1_mat_23_player_1 . ' VS ' . $request->rou_1_mat_23_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_23', $find_matches)) {
                unset($find_matches['match_23']);
            }
            $find_matches['match_23'] = $request->rou_1_mat_23_player_1 . ' VS ' . $request->rou_1_mat_23_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentyfour(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_24_player_1' => 'required',
            'rou_1_mat_24_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_24_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_24_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_24_player_1, $request->rou_1_mat_24_player_2];

        $round_one_matches_array['match_24'] = $request->rou_1_mat_24_player_1 . ' VS ' . $request->rou_1_mat_24_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_24', $find_matches)) {
                unset($find_matches['match_24']);
            }
            $find_matches['match_24'] = $request->rou_1_mat_24_player_1 . ' VS ' . $request->rou_1_mat_24_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentyfive(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_25_player_1' => 'required',
            'rou_1_mat_25_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_25_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_25_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_25_player_1, $request->rou_1_mat_25_player_2];

        $round_one_matches_array['match_25'] = $request->rou_1_mat_25_player_1 . ' VS ' . $request->rou_1_mat_25_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_25', $find_matches)) {
                unset($find_matches['match_25']);
            }
            $find_matches['match_25'] = $request->rou_1_mat_25_player_1 . ' VS ' . $request->rou_1_mat_25_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentysix(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_26_player_1' => 'required',
            'rou_1_mat_26_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_26_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_26_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_26_player_1, $request->rou_1_mat_26_player_2];

        $round_one_matches_array['match_26'] = $request->rou_1_mat_26_player_1 . ' VS ' . $request->rou_1_mat_26_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_26', $find_matches)) {
                unset($find_matches['match_26']);
            }
            $find_matches['match_26'] = $request->rou_1_mat_26_player_1 . ' VS ' . $request->rou_1_mat_26_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentyseven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_27_player_1' => 'required',
            'rou_1_mat_27_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_27_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_27_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_27_player_1, $request->rou_1_mat_27_player_2];

        $round_one_matches_array['match_27'] = $request->rou_1_mat_27_player_1 . ' VS ' . $request->rou_1_mat_27_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_27', $find_matches)) {
                unset($find_matches['match_27']);
            }
            $find_matches['match_27'] = $request->rou_1_mat_27_player_1 . ' VS ' . $request->rou_1_mat_27_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentyeight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_28_player_1' => 'required',
            'rou_1_mat_28_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_28_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_28_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_28_player_1, $request->rou_1_mat_28_player_2];

        $round_one_matches_array['match_28'] = $request->rou_1_mat_28_player_1 . ' VS ' . $request->rou_1_mat_28_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_28', $find_matches)) {
                unset($find_matches['match_28']);
            }
            $find_matches['match_28'] = $request->rou_1_mat_28_player_1 . ' VS ' . $request->rou_1_mat_28_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_twentynine(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_29_player_1' => 'required',
            'rou_1_mat_29_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_29_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_29_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_29_player_1, $request->rou_1_mat_29_player_2];

        $round_one_matches_array['match_29'] = $request->rou_1_mat_29_player_1 . ' VS ' . $request->rou_1_mat_29_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_29', $find_matches)) {
                unset($find_matches['match_29']);
            }
            $find_matches['match_29'] = $request->rou_1_mat_29_player_1 . ' VS ' . $request->rou_1_mat_29_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_thirty(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_30_player_1' => 'required',
            'rou_1_mat_30_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_30_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_30_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_30_player_1, $request->rou_1_mat_30_player_2];

        $round_one_matches_array['match_30'] = $request->rou_1_mat_30_player_1 . ' VS ' . $request->rou_1_mat_30_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_30', $find_matches)) {
                unset($find_matches['match_30']);
            }
            $find_matches['match_30'] = $request->rou_1_mat_30_player_1 . ' VS ' . $request->rou_1_mat_30_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_thirtyone(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_31_player_1' => 'required',
            'rou_1_mat_31_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_31_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_31_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_31_player_1, $request->rou_1_mat_31_player_2];

        $round_one_matches_array['match_31'] = $request->rou_1_mat_31_player_1 . ' VS ' . $request->rou_1_mat_31_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_31', $find_matches)) {
                unset($find_matches['match_31']);
            }
            $find_matches['match_31'] = $request->rou_1_mat_31_player_1 . ' VS ' . $request->rou_1_mat_31_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_one_match_thirtytwo(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_1_mat_32_player_1' => 'required',
            'rou_1_mat_32_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->rou_1_mat_32_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_1_mat_32_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou1 = json_decode($tournament->round_one_deadline);
        $endd_r1 = explode(", ", $t_d_rou1->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament->tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_one_matches_array = [];
        $match_chk_players  = [$request->rou_1_mat_32_player_1, $request->rou_1_mat_32_player_2];

        $round_one_matches_array['match_32'] = $request->rou_1_mat_32_player_1 . ' VS ' . $request->rou_1_mat_32_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_matches) {
            
            $find_matches = json_decode($tournament->round_one_matches, true);
            if(array_key_exists('match_32', $find_matches)) {
                unset($find_matches['match_32']);
            }
            $find_matches['match_32'] = $request->rou_1_mat_32_player_1 . ' VS ' . $request->rou_1_mat_32_player_2;
            $tournament->round_one_matches = json_encode($find_matches);

        } else {
            $tournament->round_one_matches = json_encode($round_one_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }



    public function submit_round_one_result_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m1_s1' => 'required',
            'p1_m1_s2' => 'required',
            'p2_m1_s1' => 'required',
            'p2_m1_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2) {

            $p1_m1_total = 2;
            $p2_m1_total = 0;

        } elseif ($request->p2_m1_s1 > $request->p1_m1_s1 && $request->p2_m1_s2 > $request->p1_m1_s2) {

            $p1_m1_total = 0;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        }

        
        $round_one_results_array['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
        $round_one_results_array['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
        $round_one_results_array['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
        
        $round_one_results_array['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

        $round_one_results_array['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
        $round_one_results_array['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
        $round_one_results_array['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
        
        $round_one_results_array['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 

        $rslt_chk = [$request->p1_m1, $request->p2_m1];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_1', $find_results)) {
                unset($find_results['match_1']);
            }

            $find_results['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
            $find_results['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
            $find_results['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
            $find_results['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

            $find_results['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
            $find_results['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
            $find_results['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
            $find_results['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_1', $find_status)) {
                unset($find_status['match_1']);
            }

            $find_status['match_1'] = $request->rou_1_mat_1_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_1'] = $request->rou_1_mat_1_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m1_total > $p2_m1_total) {
            $round_one_winners_array['match_1'] = $request->p1_m1;
        } else {
           $round_one_winners_array['match_1'] = $request->p2_m1; 
        }


        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_1', $find_winners)) {
                unset($find_winners['match_1']);
            }

            if($p1_m1_total > $p2_m1_total) {
                $find_winners['match_1'] = $request->p1_m1;
            } else {
                $find_winners['match_1'] = $request->p2_m1; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m2_s1' => 'required',
            'p1_m2_s2' => 'required',
            'p2_m2_s1' => 'required',
            'p2_m2_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2) {

            $p1_m2_total = 2;
            $p2_m2_total = 0;

        } elseif ($request->p2_m2_s1 > $request->p1_m2_s1 && $request->p2_m2_s2 > $request->p1_m2_s2) {

            $p1_m2_total = 0;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        }


        $round_one_results_array['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
        $round_one_results_array['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
        $round_one_results_array['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
        
        $round_one_results_array['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

        $round_one_results_array['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
        $round_one_results_array['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
        $round_one_results_array['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
        
        $round_one_results_array['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 

        $rslt_chk = [$request->p1_m2, $request->p2_m2];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_2', $find_results)) {
                unset($find_results['match_2']);
            }

            $find_results['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
            $find_results['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
            $find_results['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
            $find_results['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

            $find_results['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
            $find_results['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
            $find_results['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
            $find_results['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_2', $find_status)) {
                unset($find_status['match_2']);
            }

            $find_status['match_2'] = $request->rou_1_mat_2_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_2'] = $request->rou_1_mat_2_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m2_total > $p2_m2_total) {
            $round_one_winners_array['match_2'] = $request->p1_m2;
        } else {
           $round_one_winners_array['match_2'] = $request->p2_m2; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_2', $find_winners)) {
                unset($find_winners['match_2']);
            }

            if($p1_m2_total > $p2_m2_total) {
                $find_winners['match_2'] = $request->p1_m2;
            } else {
                $find_winners['match_2'] = $request->p2_m2; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m3_s1' => 'required',
            'p1_m3_s2' => 'required',
            'p2_m3_s1' => 'required',
            'p2_m3_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2) {

            $p1_m3_total = 2;
            $p2_m3_total = 0;

        } elseif ($request->p2_m3_s1 > $request->p1_m3_s1 && $request->p2_m3_s2 > $request->p1_m3_s2) {

            $p1_m3_total = 0;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        }


        $round_one_results_array['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
        $round_one_results_array['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
        $round_one_results_array['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
        $round_one_results_array['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

        $round_one_results_array['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
        $round_one_results_array['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
        $round_one_results_array['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
        $round_one_results_array['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 

        $rslt_chk = [$request->p1_m3, $request->p2_m3];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_3', $find_results)) {
                unset($find_results['match_3']);
            }

            $find_results['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
            $find_results['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
            $find_results['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
            $find_results['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

            $find_results['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
            $find_results['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
            $find_results['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
            $find_results['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_3', $find_status)) {
                unset($find_status['match_3']);
            }

            $find_status['match_3'] = $request->rou_1_mat_3_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_3'] = $request->rou_1_mat_3_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m3_total > $p2_m3_total) {
            $round_one_winners_array['match_3'] = $request->p1_m3;
        } else {
           $round_one_winners_array['match_3'] = $request->p2_m3; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_3', $find_winners)) {
                unset($find_winners['match_3']);
            }

            if($p1_m3_total > $p2_m3_total) {
                $find_winners['match_3'] = $request->p1_m3;
            } else {
                $find_winners['match_3'] = $request->p2_m3; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m4_s1' => 'required',
            'p1_m4_s2' => 'required',
            'p2_m4_s1' => 'required',
            'p2_m4_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2) {

            $p1_m4_total = 2;
            $p2_m4_total = 0;

        } elseif ($request->p2_m4_s1 > $request->p1_m4_s1 && $request->p2_m4_s2 > $request->p1_m4_s2) {

            $p1_m4_total = 0;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        }


        $round_one_results_array['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
        $round_one_results_array['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
        $round_one_results_array['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
        $round_one_results_array['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

        $round_one_results_array['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
        $round_one_results_array['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
        $round_one_results_array['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
        $round_one_results_array['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 

        $rslt_chk = [$request->p1_m4, $request->p2_m4];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_4', $find_results)) {
                unset($find_results['match_4']);
            }

            $find_results['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
            $find_results['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
            $find_results['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
            $find_results['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

            $find_results['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
            $find_results['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
            $find_results['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
            $find_results['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_4', $find_status)) {
                unset($find_status['match_4']);
            }

            $find_status['match_4'] = $request->rou_1_mat_4_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_4'] = $request->rou_1_mat_4_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m4_total > $p2_m4_total) {
            $round_one_winners_array['match_4'] = $request->p1_m4;
        } else {
           $round_one_winners_array['match_4'] = $request->p2_m4; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_4', $find_winners)) {
                unset($find_winners['match_4']);
            }

            if($p1_m4_total > $p2_m4_total) {
                $find_winners['match_4'] = $request->p1_m4;
            } else {
                $find_winners['match_4'] = $request->p2_m4; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_five(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m5_s1' => 'required',
            'p1_m5_s2' => 'required',
            'p2_m5_s1' => 'required',
            'p2_m5_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2) {

            $p1_m5_total = 2;
            $p2_m5_total = 0;

        } elseif ($request->p2_m5_s1 > $request->p1_m5_s1 && $request->p2_m5_s2 > $request->p1_m5_s2) {

            $p1_m5_total = 0;
            $p2_m5_total = 2;

        } elseif ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 < $request->p2_m5_s2 && $request->p1_m5_s3 > $request->p2_m5_s3) {

            $p1_m5_total = 2;
            $p2_m5_total = 1;

        } elseif ($request->p1_m5_s1 < $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2 && $request->p1_m5_s3 < $request->p2_m5_s3) {

            $p1_m5_total = 1;
            $p2_m5_total = 2;

        } elseif ($request->p1_m5_s1 < $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2 && $request->p1_m5_s3 > $request->p2_m5_s3) {

            $p1_m5_total = 2;
            $p2_m5_total = 1;

        } elseif ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 < $request->p2_m5_s2 && $request->p1_m5_s3 < $request->p2_m5_s3) {

            $p1_m5_total = 1;
            $p2_m5_total = 2;

        }


        $round_one_results_array['match_5'][$request->p1_m5]['set_1'] = $request->p1_m5_s1;
        $round_one_results_array['match_5'][$request->p1_m5]['set_2'] = $request->p1_m5_s2;
        $round_one_results_array['match_5'][$request->p1_m5]['set_3'] = $request->p1_m5_s3;
        $round_one_results_array['match_5'][$request->p1_m5]['total'] = $p1_m5_total;

        $round_one_results_array['match_5'][$request->p2_m5]['set_1'] = $request->p2_m5_s1;       
        $round_one_results_array['match_5'][$request->p2_m5]['set_2'] = $request->p2_m5_s2;
        $round_one_results_array['match_5'][$request->p2_m5]['set_3'] = $request->p2_m5_s3;
        $round_one_results_array['match_5'][$request->p2_m5]['total'] = $p2_m5_total; 

        $rslt_chk = [$request->p1_m5, $request->p2_m5];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_5', $find_results)) {
                unset($find_results['match_5']);
            }

            $find_results['match_5'][$request->p1_m5]['set_1'] = $request->p1_m5_s1;
            $find_results['match_5'][$request->p1_m5]['set_2'] = $request->p1_m5_s2;
            $find_results['match_5'][$request->p1_m5]['set_3'] = $request->p1_m5_s3;
            $find_results['match_5'][$request->p1_m5]['total'] = $p1_m5_total;

            $find_results['match_5'][$request->p2_m5]['set_1'] = $request->p2_m5_s1;       
            $find_results['match_5'][$request->p2_m5]['set_2'] = $request->p2_m5_s2;
            $find_results['match_5'][$request->p2_m5]['set_3'] = $request->p2_m5_s3;
            $find_results['match_5'][$request->p2_m5]['total'] = $p2_m5_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_5', $find_status)) {
                unset($find_status['match_5']);
            }

            $find_status['match_5'] = $request->rou_1_mat_5_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_5'] = $request->rou_1_mat_5_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m5_total > $p2_m5_total) {
            $round_one_winners_array['match_5'] = $request->p1_m5;
        } else {
           $round_one_winners_array['match_5'] = $request->p2_m5; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_5', $find_winners)) {
                unset($find_winners['match_5']);
            }

            if($p1_m5_total > $p2_m5_total) {
                $find_winners['match_5'] = $request->p1_m5;
            } else {
                $find_winners['match_5'] = $request->p2_m5; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_six(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m6_s1' => 'required',
            'p1_m6_s2' => 'required',
            'p2_m6_s1' => 'required',
            'p2_m6_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2) {

            $p1_m6_total = 2;
            $p2_m6_total = 0;

        } elseif ($request->p2_m6_s1 > $request->p1_m6_s1 && $request->p2_m6_s2 > $request->p1_m6_s2) {

            $p1_m6_total = 0;
            $p2_m6_total = 2;

        } elseif ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 < $request->p2_m6_s2 && $request->p1_m6_s3 > $request->p2_m6_s3) {

            $p1_m6_total = 2;
            $p2_m6_total = 1;

        } elseif ($request->p1_m6_s1 < $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2 && $request->p1_m6_s3 < $request->p2_m6_s3) {

            $p1_m6_total = 1;
            $p2_m6_total = 2;

        } elseif ($request->p1_m6_s1 < $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2 && $request->p1_m6_s3 > $request->p2_m6_s3) {

            $p1_m6_total = 2;
            $p2_m6_total = 1;

        } elseif ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 < $request->p2_m6_s2 && $request->p1_m6_s3 < $request->p2_m6_s3) {

            $p1_m6_total = 1;
            $p2_m6_total = 2;

        }


        $round_one_results_array['match_6'][$request->p1_m6]['set_1'] = $request->p1_m6_s1;
        $round_one_results_array['match_6'][$request->p1_m6]['set_2'] = $request->p1_m6_s2;
        $round_one_results_array['match_6'][$request->p1_m6]['set_3'] = $request->p1_m6_s3;
        $round_one_results_array['match_6'][$request->p1_m6]['total'] = $p1_m6_total;

        $round_one_results_array['match_6'][$request->p2_m6]['set_1'] = $request->p2_m6_s1;       
        $round_one_results_array['match_6'][$request->p2_m6]['set_2'] = $request->p2_m6_s2;
        $round_one_results_array['match_6'][$request->p2_m6]['set_3'] = $request->p2_m6_s3;
        $round_one_results_array['match_6'][$request->p2_m6]['total'] = $p2_m6_total; 

        $rslt_chk = [$request->p1_m6, $request->p2_m6];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_6', $find_results)) {
                unset($find_results['match_6']);
            }

            $find_results['match_6'][$request->p1_m6]['set_1'] = $request->p1_m6_s1;
            $find_results['match_6'][$request->p1_m6]['set_2'] = $request->p1_m6_s2;
            $find_results['match_6'][$request->p1_m6]['set_3'] = $request->p1_m6_s3;
            $find_results['match_6'][$request->p1_m6]['total'] = $p1_m6_total;

            $find_results['match_6'][$request->p2_m6]['set_1'] = $request->p2_m6_s1;       
            $find_results['match_6'][$request->p2_m6]['set_2'] = $request->p2_m6_s2;
            $find_results['match_6'][$request->p2_m6]['set_3'] = $request->p2_m6_s3;
            $find_results['match_6'][$request->p2_m6]['total'] = $p2_m6_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_6', $find_status)) {
                unset($find_status['match_6']);
            }

            $find_status['match_6'] = $request->rou_1_mat_6_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_6'] = $request->rou_1_mat_6_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m6_total > $p2_m6_total) {
            $round_one_winners_array['match_6'] = $request->p1_m6;
        } else {
           $round_one_winners_array['match_6'] = $request->p2_m6; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_6', $find_winners)) {
                unset($find_winners['match_6']);
            }

            if($p1_m6_total > $p2_m6_total) {
                $find_winners['match_6'] = $request->p1_m6;
            } else {
                $find_winners['match_6'] = $request->p2_m6; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_seven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m7_s1' => 'required',
            'p1_m7_s2' => 'required',
            'p2_m7_s1' => 'required',
            'p2_m7_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2) {

            $p1_m7_total = 2;
            $p2_m7_total = 0;

        } elseif ($request->p2_m7_s1 > $request->p1_m7_s1 && $request->p2_m7_s2 > $request->p1_m7_s2) {

            $p1_m7_total = 0;
            $p2_m7_total = 2;

        } elseif ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 < $request->p2_m7_s2 && $request->p1_m7_s3 > $request->p2_m7_s3) {

            $p1_m7_total = 2;
            $p2_m7_total = 1;

        } elseif ($request->p1_m7_s1 < $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2 && $request->p1_m7_s3 < $request->p2_m7_s3) {

            $p1_m7_total = 1;
            $p2_m7_total = 2;

        } elseif ($request->p1_m7_s1 < $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2 && $request->p1_m7_s3 > $request->p2_m7_s3) {

            $p1_m7_total = 2;
            $p2_m7_total = 1;

        } elseif ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 < $request->p2_m7_s2 && $request->p1_m7_s3 < $request->p2_m7_s3) {

            $p1_m7_total = 1;
            $p2_m7_total = 2;

        }


        $round_one_results_array['match_7'][$request->p1_m7]['set_1'] = $request->p1_m7_s1;
        $round_one_results_array['match_7'][$request->p1_m7]['set_2'] = $request->p1_m7_s2;
        $round_one_results_array['match_7'][$request->p1_m7]['set_3'] = $request->p1_m7_s3;
        $round_one_results_array['match_7'][$request->p1_m7]['total'] = $p1_m7_total;

        $round_one_results_array['match_7'][$request->p2_m7]['set_1'] = $request->p2_m7_s1;       
        $round_one_results_array['match_7'][$request->p2_m7]['set_2'] = $request->p2_m7_s2;
        $round_one_results_array['match_7'][$request->p2_m7]['set_3'] = $request->p2_m7_s3;
        $round_one_results_array['match_7'][$request->p2_m7]['total'] = $p2_m7_total; 

        $rslt_chk = [$request->p1_m7, $request->p2_m7];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_7', $find_results)) {
                unset($find_results['match_7']);
            }

            $find_results['match_7'][$request->p1_m7]['set_1'] = $request->p1_m7_s1;
            $find_results['match_7'][$request->p1_m7]['set_2'] = $request->p1_m7_s2;
            $find_results['match_7'][$request->p1_m7]['set_3'] = $request->p1_m7_s3;
            $find_results['match_7'][$request->p1_m7]['total'] = $p1_m7_total;

            $find_results['match_7'][$request->p2_m7]['set_1'] = $request->p2_m7_s1;       
            $find_results['match_7'][$request->p2_m7]['set_2'] = $request->p2_m7_s2;
            $find_results['match_7'][$request->p2_m7]['set_3'] = $request->p2_m7_s3;
            $find_results['match_7'][$request->p2_m7]['total'] = $p2_m7_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_7', $find_status)) {
                unset($find_status['match_7']);
            }

            $find_status['match_7'] = $request->rou_1_mat_7_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_7'] = $request->rou_1_mat_7_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m7_total > $p2_m7_total) {
            $round_one_winners_array['match_7'] = $request->p1_m7;
        } else {
           $round_one_winners_array['match_7'] = $request->p2_m7; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_7', $find_winners)) {
                unset($find_winners['match_7']);
            }

            if($p1_m7_total > $p2_m7_total) {
                $find_winners['match_7'] = $request->p1_m7;
            } else {
                $find_winners['match_7'] = $request->p2_m7; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_eight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m8_s1' => 'required',
            'p1_m8_s2' => 'required',
            'p2_m8_s1' => 'required',
            'p2_m8_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2) {

            $p1_m8_total = 2;
            $p2_m8_total = 0;

        } elseif ($request->p2_m8_s1 > $request->p1_m8_s1 && $request->p2_m8_s2 > $request->p1_m8_s2) {

            $p1_m8_total = 0;
            $p2_m8_total = 2;

        } elseif ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 < $request->p2_m8_s2 && $request->p1_m8_s3 > $request->p2_m8_s3) {

            $p1_m8_total = 2;
            $p2_m8_total = 1;

        } elseif ($request->p1_m8_s1 < $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2 && $request->p1_m8_s3 < $request->p2_m8_s3) {

            $p1_m8_total = 1;
            $p2_m8_total = 2;

        } elseif ($request->p1_m8_s1 < $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2 && $request->p1_m8_s3 > $request->p2_m8_s3) {

            $p1_m8_total = 2;
            $p2_m8_total = 1;

        } elseif ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 < $request->p2_m8_s2 && $request->p1_m8_s3 < $request->p2_m8_s3) {

            $p1_m8_total = 1;
            $p2_m8_total = 2;

        }


        $round_one_results_array['match_8'][$request->p1_m8]['set_1'] = $request->p1_m8_s1;
        $round_one_results_array['match_8'][$request->p1_m8]['set_2'] = $request->p1_m8_s2;
        $round_one_results_array['match_8'][$request->p1_m8]['set_3'] = $request->p1_m8_s3;
        $round_one_results_array['match_8'][$request->p1_m8]['total'] = $p1_m8_total;

        $round_one_results_array['match_8'][$request->p2_m8]['set_1'] = $request->p2_m8_s1;       
        $round_one_results_array['match_8'][$request->p2_m8]['set_2'] = $request->p2_m8_s2;
        $round_one_results_array['match_8'][$request->p2_m8]['set_3'] = $request->p2_m8_s3;
        $round_one_results_array['match_8'][$request->p2_m8]['total'] = $p2_m8_total; 

        $rslt_chk = [$request->p1_m8, $request->p2_m8];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_8', $find_results)) {
                unset($find_results['match_8']);
            }

            $find_results['match_8'][$request->p1_m8]['set_1'] = $request->p1_m8_s1;
            $find_results['match_8'][$request->p1_m8]['set_2'] = $request->p1_m8_s2;
            $find_results['match_8'][$request->p1_m8]['set_3'] = $request->p1_m8_s3;
            $find_results['match_8'][$request->p1_m8]['total'] = $p1_m8_total;

            $find_results['match_8'][$request->p2_m8]['set_1'] = $request->p2_m8_s1;       
            $find_results['match_8'][$request->p2_m8]['set_2'] = $request->p2_m8_s2;
            $find_results['match_8'][$request->p2_m8]['set_3'] = $request->p2_m8_s3;
            $find_results['match_8'][$request->p2_m8]['total'] = $p2_m8_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_8', $find_status)) {
                unset($find_status['match_8']);
            }

            $find_status['match_8'] = $request->rou_1_mat_8_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_8'] = $request->rou_1_mat_8_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m8_total > $p2_m8_total) {
            $round_one_winners_array['match_8'] = $request->p1_m8;
        } else {
           $round_one_winners_array['match_8'] = $request->p2_m8; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_8', $find_winners)) {
                unset($find_winners['match_8']);
            }

            if($p1_m8_total > $p2_m8_total) {
                $find_winners['match_8'] = $request->p1_m8;
            } else {
                $find_winners['match_8'] = $request->p2_m8; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }


    public function submit_round_one_result_nine(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m9_s1' => 'required',
            'p1_m9_s2' => 'required',
            'p2_m9_s1' => 'required',
            'p2_m9_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m9_s1 > $request->p2_m9_s1 && $request->p1_m9_s2 > $request->p2_m9_s2) {

            $p1_m9_total = 2;
            $p2_m9_total = 0;

        } elseif ($request->p2_m9_s1 > $request->p1_m9_s1 && $request->p2_m9_s2 > $request->p1_m9_s2) {

            $p1_m9_total = 0;
            $p2_m9_total = 2;

        } elseif ($request->p1_m9_s1 > $request->p2_m9_s1 && $request->p1_m9_s2 < $request->p2_m9_s2 && $request->p1_m9_s3 > $request->p2_m9_s3) {

            $p1_m9_total = 2;
            $p2_m9_total = 1;

        } elseif ($request->p1_m9_s1 < $request->p2_m9_s1 && $request->p1_m9_s2 > $request->p2_m9_s2 && $request->p1_m9_s3 < $request->p2_m9_s3) {

            $p1_m9_total = 1;
            $p2_m9_total = 2;

        } elseif ($request->p1_m9_s1 < $request->p2_m9_s1 && $request->p1_m9_s2 > $request->p2_m9_s2 && $request->p1_m9_s3 > $request->p2_m9_s3) {

            $p1_m9_total = 2;
            $p2_m9_total = 1;

        } elseif ($request->p1_m9_s1 > $request->p2_m9_s1 && $request->p1_m9_s2 < $request->p2_m9_s2 && $request->p1_m9_s3 < $request->p2_m9_s3) {

            $p1_m9_total = 1;
            $p2_m9_total = 2;

        }


        $round_one_results_array['match_9'][$request->p1_m9]['set_1'] = $request->p1_m9_s1;
        $round_one_results_array['match_9'][$request->p1_m9]['set_2'] = $request->p1_m9_s2;
        $round_one_results_array['match_9'][$request->p1_m9]['set_3'] = $request->p1_m9_s3;
        $round_one_results_array['match_9'][$request->p1_m9]['total'] = $p1_m9_total;

        $round_one_results_array['match_9'][$request->p2_m9]['set_1'] = $request->p2_m9_s1;       
        $round_one_results_array['match_9'][$request->p2_m9]['set_2'] = $request->p2_m9_s2;
        $round_one_results_array['match_9'][$request->p2_m9]['set_3'] = $request->p2_m9_s3;
        $round_one_results_array['match_9'][$request->p2_m9]['total'] = $p2_m9_total; 

        $rslt_chk = [$request->p1_m9, $request->p2_m9];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_9', $find_results)) {
                unset($find_results['match_9']);
            }

            $find_results['match_9'][$request->p1_m9]['set_1'] = $request->p1_m9_s1;
            $find_results['match_9'][$request->p1_m9]['set_2'] = $request->p1_m9_s2;
            $find_results['match_9'][$request->p1_m9]['set_3'] = $request->p1_m9_s3;
            $find_results['match_9'][$request->p1_m9]['total'] = $p1_m9_total;

            $find_results['match_9'][$request->p2_m9]['set_1'] = $request->p2_m9_s1;       
            $find_results['match_9'][$request->p2_m9]['set_2'] = $request->p2_m9_s2;
            $find_results['match_9'][$request->p2_m9]['set_3'] = $request->p2_m9_s3;
            $find_results['match_9'][$request->p2_m9]['total'] = $p2_m9_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_9', $find_status)) {
                unset($find_status['match_9']);
            }

            $find_status['match_9'] = $request->rou_1_mat_9_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_9'] = $request->rou_1_mat_9_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m9_total > $p2_m9_total) {
            $round_one_winners_array['match_9'] = $request->p1_m9;
        } else {
           $round_one_winners_array['match_9'] = $request->p2_m9; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_9', $find_winners)) {
                unset($find_winners['match_9']);
            }

            if($p1_m9_total > $p2_m9_total) {
                $find_winners['match_9'] = $request->p1_m9;
            } else {
                $find_winners['match_9'] = $request->p2_m9; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_ten(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m10_s1' => 'required',
            'p1_m10_s2' => 'required',
            'p2_m10_s1' => 'required',
            'p2_m10_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m10_s1 > $request->p2_m10_s1 && $request->p1_m10_s2 > $request->p2_m10_s2) {

            $p1_m10_total = 2;
            $p2_m10_total = 0;

        } elseif ($request->p2_m10_s1 > $request->p1_m10_s1 && $request->p2_m10_s2 > $request->p1_m10_s2) {

            $p1_m10_total = 0;
            $p2_m10_total = 2;

        } elseif ($request->p1_m10_s1 > $request->p2_m10_s1 && $request->p1_m10_s2 < $request->p2_m10_s2 && $request->p1_m10_s3 > $request->p2_m10_s3) {

            $p1_m10_total = 2;
            $p2_m10_total = 1;

        } elseif ($request->p1_m10_s1 < $request->p2_m10_s1 && $request->p1_m10_s2 > $request->p2_m10_s2 && $request->p1_m10_s3 < $request->p2_m10_s3) {

            $p1_m10_total = 1;
            $p2_m10_total = 2;

        } elseif ($request->p1_m10_s1 < $request->p2_m10_s1 && $request->p1_m10_s2 > $request->p2_m10_s2 && $request->p1_m10_s3 > $request->p2_m10_s3) {

            $p1_m10_total = 2;
            $p2_m10_total = 1;

        } elseif ($request->p1_m10_s1 > $request->p2_m10_s1 && $request->p1_m10_s2 < $request->p2_m10_s2 && $request->p1_m10_s3 < $request->p2_m10_s3) {

            $p1_m10_total = 1;
            $p2_m10_total = 2;

        }


        $round_one_results_array['match_10'][$request->p1_m10]['set_1'] = $request->p1_m10_s1;
        $round_one_results_array['match_10'][$request->p1_m10]['set_2'] = $request->p1_m10_s2;
        $round_one_results_array['match_10'][$request->p1_m10]['set_3'] = $request->p1_m10_s3;
        $round_one_results_array['match_10'][$request->p1_m10]['total'] = $p1_m10_total;

        $round_one_results_array['match_10'][$request->p2_m10]['set_1'] = $request->p2_m10_s1;       
        $round_one_results_array['match_10'][$request->p2_m10]['set_2'] = $request->p2_m10_s2;
        $round_one_results_array['match_10'][$request->p2_m10]['set_3'] = $request->p2_m10_s3;
        $round_one_results_array['match_10'][$request->p2_m10]['total'] = $p2_m10_total; 

        $rslt_chk = [$request->p1_m10, $request->p2_m10];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_10', $find_results)) {
                unset($find_results['match_10']);
            }

            $find_results['match_10'][$request->p1_m10]['set_1'] = $request->p1_m10_s1;
            $find_results['match_10'][$request->p1_m10]['set_2'] = $request->p1_m10_s2;
            $find_results['match_10'][$request->p1_m10]['set_3'] = $request->p1_m10_s3;
            $find_results['match_10'][$request->p1_m10]['total'] = $p1_m10_total;

            $find_results['match_10'][$request->p2_m10]['set_1'] = $request->p2_m10_s1;       
            $find_results['match_10'][$request->p2_m10]['set_2'] = $request->p2_m10_s2;
            $find_results['match_10'][$request->p2_m10]['set_3'] = $request->p2_m10_s3;
            $find_results['match_10'][$request->p2_m10]['total'] = $p2_m10_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_10', $find_status)) {
                unset($find_status['match_10']);
            }

            $find_status['match_10'] = $request->rou_1_mat_10_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_10'] = $request->rou_1_mat_10_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m10_total > $p2_m10_total) {
            $round_one_winners_array['match_10'] = $request->p1_m10;
        } else {
           $round_one_winners_array['match_10'] = $request->p2_m10; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_10', $find_winners)) {
                unset($find_winners['match_10']);
            }

            if($p1_m10_total > $p2_m10_total) {
                $find_winners['match_10'] = $request->p1_m10;
            } else {
                $find_winners['match_10'] = $request->p2_m10; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_eleven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m11_s1' => 'required',
            'p1_m11_s2' => 'required',
            'p2_m11_s1' => 'required',
            'p2_m11_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m11_s1 > $request->p2_m11_s1 && $request->p1_m11_s2 > $request->p2_m11_s2) {

            $p1_m11_total = 2;
            $p2_m11_total = 0;

        } elseif ($request->p2_m11_s1 > $request->p1_m11_s1 && $request->p2_m11_s2 > $request->p1_m11_s2) {

            $p1_m11_total = 0;
            $p2_m11_total = 2;

        } elseif ($request->p1_m11_s1 > $request->p2_m11_s1 && $request->p1_m11_s2 < $request->p2_m11_s2 && $request->p1_m11_s3 > $request->p2_m11_s3) {

            $p1_m11_total = 2;
            $p2_m11_total = 1;

        } elseif ($request->p1_m11_s1 < $request->p2_m11_s1 && $request->p1_m11_s2 > $request->p2_m11_s2 && $request->p1_m11_s3 < $request->p2_m11_s3) {

            $p1_m11_total = 1;
            $p2_m11_total = 2;

        } elseif ($request->p1_m11_s1 < $request->p2_m11_s1 && $request->p1_m11_s2 > $request->p2_m11_s2 && $request->p1_m11_s3 > $request->p2_m11_s3) {

            $p1_m11_total = 2;
            $p2_m11_total = 1;

        } elseif ($request->p1_m11_s1 > $request->p2_m11_s1 && $request->p1_m11_s2 < $request->p2_m11_s2 && $request->p1_m11_s3 < $request->p2_m11_s3) {

            $p1_m11_total = 1;
            $p2_m11_total = 2;

        }


        $round_one_results_array['match_11'][$request->p1_m11]['set_1'] = $request->p1_m11_s1;
        $round_one_results_array['match_11'][$request->p1_m11]['set_2'] = $request->p1_m11_s2;
        $round_one_results_array['match_11'][$request->p1_m11]['set_3'] = $request->p1_m11_s3;
        $round_one_results_array['match_11'][$request->p1_m11]['total'] = $p1_m11_total;

        $round_one_results_array['match_11'][$request->p2_m11]['set_1'] = $request->p2_m11_s1;       
        $round_one_results_array['match_11'][$request->p2_m11]['set_2'] = $request->p2_m11_s2;
        $round_one_results_array['match_11'][$request->p2_m11]['set_3'] = $request->p2_m11_s3;
        $round_one_results_array['match_11'][$request->p2_m11]['total'] = $p2_m11_total; 

        $rslt_chk = [$request->p1_m11, $request->p2_m11];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_11', $find_results)) {
                unset($find_results['match_11']);
            }

            $find_results['match_11'][$request->p1_m11]['set_1'] = $request->p1_m11_s1;
            $find_results['match_11'][$request->p1_m11]['set_2'] = $request->p1_m11_s2;
            $find_results['match_11'][$request->p1_m11]['set_3'] = $request->p1_m11_s3;
            $find_results['match_11'][$request->p1_m11]['total'] = $p1_m11_total;

            $find_results['match_11'][$request->p2_m11]['set_1'] = $request->p2_m11_s1;       
            $find_results['match_11'][$request->p2_m11]['set_2'] = $request->p2_m11_s2;
            $find_results['match_11'][$request->p2_m11]['set_3'] = $request->p2_m11_s3;
            $find_results['match_11'][$request->p2_m11]['total'] = $p2_m11_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_11', $find_status)) {
                unset($find_status['match_11']);
            }

            $find_status['match_11'] = $request->rou_1_mat_11_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_11'] = $request->rou_1_mat_11_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m11_total > $p2_m11_total) {
            $round_one_winners_array['match_11'] = $request->p1_m11;
        } else {
           $round_one_winners_array['match_11'] = $request->p2_m11; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_11', $find_winners)) {
                unset($find_winners['match_11']);
            }

            if($p1_m11_total > $p2_m11_total) {
                $find_winners['match_11'] = $request->p1_m11;
            } else {
                $find_winners['match_11'] = $request->p2_m11; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twelve(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m12_s1' => 'required',
            'p1_m12_s2' => 'required',
            'p2_m12_s1' => 'required',
            'p2_m12_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m12_s1 > $request->p2_m12_s1 && $request->p1_m12_s2 > $request->p2_m12_s2) {

            $p1_m12_total = 2;
            $p2_m12_total = 0;

        } elseif ($request->p2_m12_s1 > $request->p1_m12_s1 && $request->p2_m12_s2 > $request->p1_m12_s2) {

            $p1_m12_total = 0;
            $p2_m12_total = 2;

        } elseif ($request->p1_m12_s1 > $request->p2_m12_s1 && $request->p1_m12_s2 < $request->p2_m12_s2 && $request->p1_m12_s3 > $request->p2_m12_s3) {

            $p1_m12_total = 2;
            $p2_m12_total = 1;

        } elseif ($request->p1_m12_s1 < $request->p2_m12_s1 && $request->p1_m12_s2 > $request->p2_m12_s2 && $request->p1_m12_s3 < $request->p2_m12_s3) {

            $p1_m12_total = 1;
            $p2_m12_total = 2;

        } elseif ($request->p1_m12_s1 < $request->p2_m12_s1 && $request->p1_m12_s2 > $request->p2_m12_s2 && $request->p1_m12_s3 > $request->p2_m12_s3) {

            $p1_m12_total = 2;
            $p2_m12_total = 1;

        } elseif ($request->p1_m12_s1 > $request->p2_m12_s1 && $request->p1_m12_s2 < $request->p2_m12_s2 && $request->p1_m12_s3 < $request->p2_m12_s3) {

            $p1_m12_total = 1;
            $p2_m12_total = 2;

        }


        $round_one_results_array['match_12'][$request->p1_m12]['set_1'] = $request->p1_m12_s1;
        $round_one_results_array['match_12'][$request->p1_m12]['set_2'] = $request->p1_m12_s2;
        $round_one_results_array['match_12'][$request->p1_m12]['set_3'] = $request->p1_m12_s3;
        $round_one_results_array['match_12'][$request->p1_m12]['total'] = $p1_m12_total;

        $round_one_results_array['match_12'][$request->p2_m12]['set_1'] = $request->p2_m12_s1;       
        $round_one_results_array['match_12'][$request->p2_m12]['set_2'] = $request->p2_m12_s2;
        $round_one_results_array['match_12'][$request->p2_m12]['set_3'] = $request->p2_m12_s3;
        $round_one_results_array['match_12'][$request->p2_m12]['total'] = $p2_m12_total; 

        $rslt_chk = [$request->p1_m12, $request->p2_m12];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_12', $find_results)) {
                unset($find_results['match_12']);
            }

            $find_results['match_12'][$request->p1_m12]['set_1'] = $request->p1_m12_s1;
            $find_results['match_12'][$request->p1_m12]['set_2'] = $request->p1_m12_s2;
            $find_results['match_12'][$request->p1_m12]['set_3'] = $request->p1_m12_s3;
            $find_results['match_12'][$request->p1_m12]['total'] = $p1_m12_total;

            $find_results['match_12'][$request->p2_m12]['set_1'] = $request->p2_m12_s1;       
            $find_results['match_12'][$request->p2_m12]['set_2'] = $request->p2_m12_s2;
            $find_results['match_12'][$request->p2_m12]['set_3'] = $request->p2_m12_s3;
            $find_results['match_12'][$request->p2_m12]['total'] = $p2_m12_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_12', $find_status)) {
                unset($find_status['match_12']);
            }

            $find_status['match_12'] = $request->rou_1_mat_12_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_12'] = $request->rou_1_mat_12_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m12_total > $p2_m12_total) {
            $round_one_winners_array['match_12'] = $request->p1_m12;
        } else {
           $round_one_winners_array['match_12'] = $request->p2_m12; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_12', $find_winners)) {
                unset($find_winners['match_12']);
            }

            if($p1_m12_total > $p2_m12_total) {
                $find_winners['match_12'] = $request->p1_m12;
            } else {
                $find_winners['match_12'] = $request->p2_m12; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_thirteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m13_s1' => 'required',
            'p1_m13_s2' => 'required',
            'p2_m13_s1' => 'required',
            'p2_m13_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m13_s1 > $request->p2_m13_s1 && $request->p1_m13_s2 > $request->p2_m13_s2) {

            $p1_m13_total = 2;
            $p2_m13_total = 0;

        } elseif ($request->p2_m13_s1 > $request->p1_m13_s1 && $request->p2_m13_s2 > $request->p1_m13_s2) {

            $p1_m13_total = 0;
            $p2_m13_total = 2;

        } elseif ($request->p1_m13_s1 > $request->p2_m13_s1 && $request->p1_m13_s2 < $request->p2_m13_s2 && $request->p1_m13_s3 > $request->p2_m13_s3) {

            $p1_m13_total = 2;
            $p2_m13_total = 1;

        } elseif ($request->p1_m13_s1 < $request->p2_m13_s1 && $request->p1_m13_s2 > $request->p2_m13_s2 && $request->p1_m13_s3 < $request->p2_m13_s3) {

            $p1_m13_total = 1;
            $p2_m13_total = 2;

        } elseif ($request->p1_m13_s1 < $request->p2_m13_s1 && $request->p1_m13_s2 > $request->p2_m13_s2 && $request->p1_m13_s3 > $request->p2_m13_s3) {

            $p1_m13_total = 2;
            $p2_m13_total = 1;

        } elseif ($request->p1_m13_s1 > $request->p2_m13_s1 && $request->p1_m13_s2 < $request->p2_m13_s2 && $request->p1_m13_s3 < $request->p2_m13_s3) {

            $p1_m13_total = 1;
            $p2_m13_total = 2;

        }


        $round_one_results_array['match_13'][$request->p1_m13]['set_1'] = $request->p1_m13_s1;
        $round_one_results_array['match_13'][$request->p1_m13]['set_2'] = $request->p1_m13_s2;
        $round_one_results_array['match_13'][$request->p1_m13]['set_3'] = $request->p1_m13_s3;
        $round_one_results_array['match_13'][$request->p1_m13]['total'] = $p1_m13_total;

        $round_one_results_array['match_13'][$request->p2_m13]['set_1'] = $request->p2_m13_s1;       
        $round_one_results_array['match_13'][$request->p2_m13]['set_2'] = $request->p2_m13_s2;
        $round_one_results_array['match_13'][$request->p2_m13]['set_3'] = $request->p2_m13_s3;
        $round_one_results_array['match_13'][$request->p2_m13]['total'] = $p2_m13_total; 

        $rslt_chk = [$request->p1_m13, $request->p2_m13];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_13', $find_results)) {
                unset($find_results['match_13']);
            }

            $find_results['match_13'][$request->p1_m13]['set_1'] = $request->p1_m13_s1;
            $find_results['match_13'][$request->p1_m13]['set_2'] = $request->p1_m13_s2;
            $find_results['match_13'][$request->p1_m13]['set_3'] = $request->p1_m13_s3;
            $find_results['match_13'][$request->p1_m13]['total'] = $p1_m13_total;

            $find_results['match_13'][$request->p2_m13]['set_1'] = $request->p2_m13_s1;       
            $find_results['match_13'][$request->p2_m13]['set_2'] = $request->p2_m13_s2;
            $find_results['match_13'][$request->p2_m13]['set_3'] = $request->p2_m13_s3;
            $find_results['match_13'][$request->p2_m13]['total'] = $p2_m13_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_13', $find_status)) {
                unset($find_status['match_13']);
            }

            $find_status['match_13'] = $request->rou_1_mat_13_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_13'] = $request->rou_1_mat_13_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m13_total > $p2_m13_total) {
            $round_one_winners_array['match_13'] = $request->p1_m13;
        } else {
           $round_one_winners_array['match_13'] = $request->p2_m13; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_13', $find_winners)) {
                unset($find_winners['match_13']);
            }

            if($p1_m13_total > $p2_m13_total) {
                $find_winners['match_13'] = $request->p1_m13;
            } else {
                $find_winners['match_13'] = $request->p2_m13; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_fourteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m14_s1' => 'required',
            'p1_m14_s2' => 'required',
            'p2_m14_s1' => 'required',
            'p2_m14_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m14_s1 > $request->p2_m14_s1 && $request->p1_m14_s2 > $request->p2_m14_s2) {

            $p1_m14_total = 2;
            $p2_m14_total = 0;

        } elseif ($request->p2_m14_s1 > $request->p1_m14_s1 && $request->p2_m14_s2 > $request->p1_m14_s2) {

            $p1_m14_total = 0;
            $p2_m14_total = 2;

        } elseif ($request->p1_m14_s1 > $request->p2_m14_s1 && $request->p1_m14_s2 < $request->p2_m14_s2 && $request->p1_m14_s3 > $request->p2_m14_s3) {

            $p1_m14_total = 2;
            $p2_m14_total = 1;

        } elseif ($request->p1_m14_s1 < $request->p2_m14_s1 && $request->p1_m14_s2 > $request->p2_m14_s2 && $request->p1_m14_s3 < $request->p2_m14_s3) {

            $p1_m14_total = 1;
            $p2_m14_total = 2;

        } elseif ($request->p1_m14_s1 < $request->p2_m14_s1 && $request->p1_m14_s2 > $request->p2_m14_s2 && $request->p1_m14_s3 > $request->p2_m14_s3) {

            $p1_m14_total = 2;
            $p2_m14_total = 1;

        } elseif ($request->p1_m14_s1 > $request->p2_m14_s1 && $request->p1_m14_s2 < $request->p2_m14_s2 && $request->p1_m14_s3 < $request->p2_m14_s3) {

            $p1_m14_total = 1;
            $p2_m14_total = 2;

        }


        $round_one_results_array['match_14'][$request->p1_m14]['set_1'] = $request->p1_m14_s1;
        $round_one_results_array['match_14'][$request->p1_m14]['set_2'] = $request->p1_m14_s2;
        $round_one_results_array['match_14'][$request->p1_m14]['set_3'] = $request->p1_m14_s3;
        $round_one_results_array['match_14'][$request->p1_m14]['total'] = $p1_m14_total;

        $round_one_results_array['match_14'][$request->p2_m14]['set_1'] = $request->p2_m14_s1;       
        $round_one_results_array['match_14'][$request->p2_m14]['set_2'] = $request->p2_m14_s2;
        $round_one_results_array['match_14'][$request->p2_m14]['set_3'] = $request->p2_m14_s3;
        $round_one_results_array['match_14'][$request->p2_m14]['total'] = $p2_m14_total; 

        $rslt_chk = [$request->p1_m14, $request->p2_m14];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_14', $find_results)) {
                unset($find_results['match_14']);
            }

            $find_results['match_14'][$request->p1_m14]['set_1'] = $request->p1_m14_s1;
            $find_results['match_14'][$request->p1_m14]['set_2'] = $request->p1_m14_s2;
            $find_results['match_14'][$request->p1_m14]['set_3'] = $request->p1_m14_s3;
            $find_results['match_14'][$request->p1_m14]['total'] = $p1_m14_total;

            $find_results['match_14'][$request->p2_m14]['set_1'] = $request->p2_m14_s1;       
            $find_results['match_14'][$request->p2_m14]['set_2'] = $request->p2_m14_s2;
            $find_results['match_14'][$request->p2_m14]['set_3'] = $request->p2_m14_s3;
            $find_results['match_14'][$request->p2_m14]['total'] = $p2_m14_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_14', $find_status)) {
                unset($find_status['match_14']);
            }

            $find_status['match_14'] = $request->rou_1_mat_14_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_14'] = $request->rou_1_mat_14_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m14_total > $p2_m14_total) {
            $round_one_winners_array['match_14'] = $request->p1_m14;
        } else {
           $round_one_winners_array['match_14'] = $request->p2_m14; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_14', $find_winners)) {
                unset($find_winners['match_14']);
            }

            if($p1_m14_total > $p2_m14_total) {
                $find_winners['match_14'] = $request->p1_m14;
            } else {
                $find_winners['match_14'] = $request->p2_m14; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_fifteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m15_s1' => 'required',
            'p1_m15_s2' => 'required',
            'p2_m15_s1' => 'required',
            'p2_m15_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m15_s1 > $request->p2_m15_s1 && $request->p1_m15_s2 > $request->p2_m15_s2) {

            $p1_m15_total = 2;
            $p2_m15_total = 0;

        } elseif ($request->p2_m15_s1 > $request->p1_m15_s1 && $request->p2_m15_s2 > $request->p1_m15_s2) {

            $p1_m15_total = 0;
            $p2_m15_total = 2;

        } elseif ($request->p1_m15_s1 > $request->p2_m15_s1 && $request->p1_m15_s2 < $request->p2_m15_s2 && $request->p1_m15_s3 > $request->p2_m15_s3) {

            $p1_m15_total = 2;
            $p2_m15_total = 1;

        } elseif ($request->p1_m15_s1 < $request->p2_m15_s1 && $request->p1_m15_s2 > $request->p2_m15_s2 && $request->p1_m15_s3 < $request->p2_m15_s3) {

            $p1_m15_total = 1;
            $p2_m15_total = 2;

        } elseif ($request->p1_m15_s1 < $request->p2_m15_s1 && $request->p1_m15_s2 > $request->p2_m15_s2 && $request->p1_m15_s3 > $request->p2_m15_s3) {

            $p1_m15_total = 2;
            $p2_m15_total = 1;

        } elseif ($request->p1_m15_s1 > $request->p2_m15_s1 && $request->p1_m15_s2 < $request->p2_m15_s2 && $request->p1_m15_s3 < $request->p2_m15_s3) {

            $p1_m15_total = 1;
            $p2_m15_total = 2;

        }


        $round_one_results_array['match_15'][$request->p1_m15]['set_1'] = $request->p1_m15_s1;
        $round_one_results_array['match_15'][$request->p1_m15]['set_2'] = $request->p1_m15_s2;
        $round_one_results_array['match_15'][$request->p1_m15]['set_3'] = $request->p1_m15_s3;
        $round_one_results_array['match_15'][$request->p1_m15]['total'] = $p1_m15_total;

        $round_one_results_array['match_15'][$request->p2_m15]['set_1'] = $request->p2_m15_s1;       
        $round_one_results_array['match_15'][$request->p2_m15]['set_2'] = $request->p2_m15_s2;
        $round_one_results_array['match_15'][$request->p2_m15]['set_3'] = $request->p2_m15_s3;
        $round_one_results_array['match_15'][$request->p2_m15]['total'] = $p2_m15_total; 

        $rslt_chk = [$request->p1_m15, $request->p2_m15];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_15', $find_results)) {
                unset($find_results['match_15']);
            }

            $find_results['match_15'][$request->p1_m15]['set_1'] = $request->p1_m15_s1;
            $find_results['match_15'][$request->p1_m15]['set_2'] = $request->p1_m15_s2;
            $find_results['match_15'][$request->p1_m15]['set_3'] = $request->p1_m15_s3;
            $find_results['match_15'][$request->p1_m15]['total'] = $p1_m15_total;

            $find_results['match_15'][$request->p2_m15]['set_1'] = $request->p2_m15_s1;       
            $find_results['match_15'][$request->p2_m15]['set_2'] = $request->p2_m15_s2;
            $find_results['match_15'][$request->p2_m15]['set_3'] = $request->p2_m15_s3;
            $find_results['match_15'][$request->p2_m15]['total'] = $p2_m15_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_15', $find_status)) {
                unset($find_status['match_15']);
            }

            $find_status['match_15'] = $request->rou_1_mat_15_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_15'] = $request->rou_1_mat_15_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m15_total > $p2_m15_total) {
            $round_one_winners_array['match_15'] = $request->p1_m15;
        } else {
           $round_one_winners_array['match_15'] = $request->p2_m15; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_15', $find_winners)) {
                unset($find_winners['match_15']);
            }

            if($p1_m15_total > $p2_m15_total) {
                $find_winners['match_15'] = $request->p1_m15;
            } else {
                $find_winners['match_15'] = $request->p2_m15; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_sixteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m16_s1' => 'required',
            'p1_m16_s2' => 'required',
            'p2_m16_s1' => 'required',
            'p2_m16_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m16_s1 > $request->p2_m16_s1 && $request->p1_m16_s2 > $request->p2_m16_s2) {

            $p1_m16_total = 2;
            $p2_m16_total = 0;

        } elseif ($request->p2_m16_s1 > $request->p1_m16_s1 && $request->p2_m16_s2 > $request->p1_m16_s2) {

            $p1_m16_total = 0;
            $p2_m16_total = 2;

        } elseif ($request->p1_m16_s1 > $request->p2_m16_s1 && $request->p1_m16_s2 < $request->p2_m16_s2 && $request->p1_m16_s3 > $request->p2_m16_s3) {

            $p1_m16_total = 2;
            $p2_m16_total = 1;

        } elseif ($request->p1_m16_s1 < $request->p2_m16_s1 && $request->p1_m16_s2 > $request->p2_m16_s2 && $request->p1_m16_s3 < $request->p2_m16_s3) {

            $p1_m16_total = 1;
            $p2_m16_total = 2;

        } elseif ($request->p1_m16_s1 < $request->p2_m16_s1 && $request->p1_m16_s2 > $request->p2_m16_s2 && $request->p1_m16_s3 > $request->p2_m16_s3) {

            $p1_m16_total = 2;
            $p2_m16_total = 1;

        } elseif ($request->p1_m16_s1 > $request->p2_m16_s1 && $request->p1_m16_s2 < $request->p2_m16_s2 && $request->p1_m16_s3 < $request->p2_m16_s3) {

            $p1_m16_total = 1;
            $p2_m16_total = 2;

        }


        $round_one_results_array['match_16'][$request->p1_m16]['set_1'] = $request->p1_m16_s1;
        $round_one_results_array['match_16'][$request->p1_m16]['set_2'] = $request->p1_m16_s2;
        $round_one_results_array['match_16'][$request->p1_m16]['set_3'] = $request->p1_m16_s3;
        $round_one_results_array['match_16'][$request->p1_m16]['total'] = $p1_m16_total;

        $round_one_results_array['match_16'][$request->p2_m16]['set_1'] = $request->p2_m16_s1;       
        $round_one_results_array['match_16'][$request->p2_m16]['set_2'] = $request->p2_m16_s2;
        $round_one_results_array['match_16'][$request->p2_m16]['set_3'] = $request->p2_m16_s3;
        $round_one_results_array['match_16'][$request->p2_m16]['total'] = $p2_m16_total; 

        $rslt_chk = [$request->p1_m16, $request->p2_m16];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_16', $find_results)) {
                unset($find_results['match_16']);
            }

            $find_results['match_16'][$request->p1_m16]['set_1'] = $request->p1_m16_s1;
            $find_results['match_16'][$request->p1_m16]['set_2'] = $request->p1_m16_s2;
            $find_results['match_16'][$request->p1_m16]['set_3'] = $request->p1_m16_s3;
            $find_results['match_16'][$request->p1_m16]['total'] = $p1_m16_total;

            $find_results['match_16'][$request->p2_m16]['set_1'] = $request->p2_m16_s1;       
            $find_results['match_16'][$request->p2_m16]['set_2'] = $request->p2_m16_s2;
            $find_results['match_16'][$request->p2_m16]['set_3'] = $request->p2_m16_s3;
            $find_results['match_16'][$request->p2_m16]['total'] = $p2_m16_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_16', $find_status)) {
                unset($find_status['match_16']);
            }

            $find_status['match_16'] = $request->rou_1_mat_16_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_16'] = $request->rou_1_mat_16_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m16_total > $p2_m16_total) {
            $round_one_winners_array['match_16'] = $request->p1_m16;
        } else {
           $round_one_winners_array['match_16'] = $request->p2_m16; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_16', $find_winners)) {
                unset($find_winners['match_16']);
            }

            if($p1_m16_total > $p2_m16_total) {
                $find_winners['match_16'] = $request->p1_m16;
            } else {
                $find_winners['match_16'] = $request->p2_m16; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }



    public function submit_round_one_result_seventeen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m17_s1' => 'required',
            'p1_m17_s2' => 'required',
            'p2_m17_s1' => 'required',
            'p2_m17_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m17_s1 > $request->p2_m17_s1 && $request->p1_m17_s2 > $request->p2_m17_s2) {

            $p1_m17_total = 2;
            $p2_m17_total = 0;

        } elseif ($request->p2_m17_s1 > $request->p1_m17_s1 && $request->p2_m17_s2 > $request->p1_m17_s2) {

            $p1_m17_total = 0;
            $p2_m17_total = 2;

        } elseif ($request->p1_m17_s1 > $request->p2_m17_s1 && $request->p1_m17_s2 < $request->p2_m17_s2 && $request->p1_m17_s3 > $request->p2_m17_s3) {

            $p1_m17_total = 2;
            $p2_m17_total = 1;

        } elseif ($request->p1_m17_s1 < $request->p2_m17_s1 && $request->p1_m17_s2 > $request->p2_m17_s2 && $request->p1_m17_s3 < $request->p2_m17_s3) {

            $p1_m17_total = 1;
            $p2_m17_total = 2;

        } elseif ($request->p1_m17_s1 < $request->p2_m17_s1 && $request->p1_m17_s2 > $request->p2_m17_s2 && $request->p1_m17_s3 > $request->p2_m17_s3) {

            $p1_m17_total = 2;
            $p2_m17_total = 1;

        } elseif ($request->p1_m17_s1 > $request->p2_m17_s1 && $request->p1_m17_s2 < $request->p2_m17_s2 && $request->p1_m17_s3 < $request->p2_m17_s3) {

            $p1_m17_total = 1;
            $p2_m17_total = 2;

        }


        $round_one_results_array['match_17'][$request->p1_m17]['set_1'] = $request->p1_m17_s1;
        $round_one_results_array['match_17'][$request->p1_m17]['set_2'] = $request->p1_m17_s2;
        $round_one_results_array['match_17'][$request->p1_m17]['set_3'] = $request->p1_m17_s3;
        $round_one_results_array['match_17'][$request->p1_m17]['total'] = $p1_m17_total;

        $round_one_results_array['match_17'][$request->p2_m17]['set_1'] = $request->p2_m17_s1;       
        $round_one_results_array['match_17'][$request->p2_m17]['set_2'] = $request->p2_m17_s2;
        $round_one_results_array['match_17'][$request->p2_m17]['set_3'] = $request->p2_m17_s3;
        $round_one_results_array['match_17'][$request->p2_m17]['total'] = $p2_m17_total; 

        $rslt_chk = [$request->p1_m17, $request->p2_m17];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_17', $find_results)) {
                unset($find_results['match_17']);
            }

            $find_results['match_17'][$request->p1_m17]['set_1'] = $request->p1_m17_s1;
            $find_results['match_17'][$request->p1_m17]['set_2'] = $request->p1_m17_s2;
            $find_results['match_17'][$request->p1_m17]['set_3'] = $request->p1_m17_s3;
            $find_results['match_17'][$request->p1_m17]['total'] = $p1_m17_total;

            $find_results['match_17'][$request->p2_m17]['set_1'] = $request->p2_m17_s1;       
            $find_results['match_17'][$request->p2_m17]['set_2'] = $request->p2_m17_s2;
            $find_results['match_17'][$request->p2_m17]['set_3'] = $request->p2_m17_s3;
            $find_results['match_17'][$request->p2_m17]['total'] = $p2_m17_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_17', $find_status)) {
                unset($find_status['match_17']);
            }

            $find_status['match_17'] = $request->rou_1_mat_17_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_17'] = $request->rou_1_mat_17_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m17_total > $p2_m17_total) {
            $round_one_winners_array['match_17'] = $request->p1_m17;
        } else {
           $round_one_winners_array['match_17'] = $request->p2_m17; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_17', $find_winners)) {
                unset($find_winners['match_17']);
            }

            if($p1_m17_total > $p2_m17_total) {
                $find_winners['match_17'] = $request->p1_m17;
            } else {
                $find_winners['match_17'] = $request->p2_m17; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_eighteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m18_s1' => 'required',
            'p1_m18_s2' => 'required',
            'p2_m18_s1' => 'required',
            'p2_m18_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m18_s1 > $request->p2_m18_s1 && $request->p1_m18_s2 > $request->p2_m18_s2) {

            $p1_m18_total = 2;
            $p2_m18_total = 0;

        } elseif ($request->p2_m18_s1 > $request->p1_m18_s1 && $request->p2_m18_s2 > $request->p1_m18_s2) {

            $p1_m18_total = 0;
            $p2_m18_total = 2;

        } elseif ($request->p1_m18_s1 > $request->p2_m18_s1 && $request->p1_m18_s2 < $request->p2_m18_s2 && $request->p1_m18_s3 > $request->p2_m18_s3) {

            $p1_m18_total = 2;
            $p2_m18_total = 1;

        } elseif ($request->p1_m18_s1 < $request->p2_m18_s1 && $request->p1_m18_s2 > $request->p2_m18_s2 && $request->p1_m18_s3 < $request->p2_m18_s3) {

            $p1_m18_total = 1;
            $p2_m18_total = 2;

        } elseif ($request->p1_m18_s1 < $request->p2_m18_s1 && $request->p1_m18_s2 > $request->p2_m18_s2 && $request->p1_m18_s3 > $request->p2_m18_s3) {

            $p1_m18_total = 2;
            $p2_m18_total = 1;

        } elseif ($request->p1_m18_s1 > $request->p2_m18_s1 && $request->p1_m18_s2 < $request->p2_m18_s2 && $request->p1_m18_s3 < $request->p2_m18_s3) {

            $p1_m18_total = 1;
            $p2_m18_total = 2;

        }


        $round_one_results_array['match_18'][$request->p1_m18]['set_1'] = $request->p1_m18_s1;
        $round_one_results_array['match_18'][$request->p1_m18]['set_2'] = $request->p1_m18_s2;
        $round_one_results_array['match_18'][$request->p1_m18]['set_3'] = $request->p1_m18_s3;
        $round_one_results_array['match_18'][$request->p1_m18]['total'] = $p1_m18_total;

        $round_one_results_array['match_18'][$request->p2_m18]['set_1'] = $request->p2_m18_s1;       
        $round_one_results_array['match_18'][$request->p2_m18]['set_2'] = $request->p2_m18_s2;
        $round_one_results_array['match_18'][$request->p2_m18]['set_3'] = $request->p2_m18_s3;
        $round_one_results_array['match_18'][$request->p2_m18]['total'] = $p2_m18_total; 

        $rslt_chk = [$request->p1_m18, $request->p2_m18];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_18', $find_results)) {
                unset($find_results['match_18']);
            }

            $find_results['match_18'][$request->p1_m18]['set_1'] = $request->p1_m18_s1;
            $find_results['match_18'][$request->p1_m18]['set_2'] = $request->p1_m18_s2;
            $find_results['match_18'][$request->p1_m18]['set_3'] = $request->p1_m18_s3;
            $find_results['match_18'][$request->p1_m18]['total'] = $p1_m18_total;

            $find_results['match_18'][$request->p2_m18]['set_1'] = $request->p2_m18_s1;       
            $find_results['match_18'][$request->p2_m18]['set_2'] = $request->p2_m18_s2;
            $find_results['match_18'][$request->p2_m18]['set_3'] = $request->p2_m18_s3;
            $find_results['match_18'][$request->p2_m18]['total'] = $p2_m18_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_18', $find_status)) {
                unset($find_status['match_18']);
            }

            $find_status['match_18'] = $request->rou_1_mat_18_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_18'] = $request->rou_1_mat_18_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m18_total > $p2_m18_total) {
            $round_one_winners_array['match_18'] = $request->p1_m18;
        } else {
           $round_one_winners_array['match_18'] = $request->p2_m18; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_18', $find_winners)) {
                unset($find_winners['match_18']);
            }

            if($p1_m18_total > $p2_m18_total) {
                $find_winners['match_18'] = $request->p1_m18;
            } else {
                $find_winners['match_18'] = $request->p2_m18; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_nineteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m19_s1' => 'required',
            'p1_m19_s2' => 'required',
            'p2_m19_s1' => 'required',
            'p2_m19_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m19_s1 > $request->p2_m19_s1 && $request->p1_m19_s2 > $request->p2_m19_s2) {

            $p1_m19_total = 2;
            $p2_m19_total = 0;

        } elseif ($request->p2_m19_s1 > $request->p1_m19_s1 && $request->p2_m19_s2 > $request->p1_m19_s2) {

            $p1_m19_total = 0;
            $p2_m19_total = 2;

        } elseif ($request->p1_m19_s1 > $request->p2_m19_s1 && $request->p1_m19_s2 < $request->p2_m19_s2 && $request->p1_m19_s3 > $request->p2_m19_s3) {

            $p1_m19_total = 2;
            $p2_m19_total = 1;

        } elseif ($request->p1_m19_s1 < $request->p2_m19_s1 && $request->p1_m19_s2 > $request->p2_m19_s2 && $request->p1_m19_s3 < $request->p2_m19_s3) {

            $p1_m19_total = 1;
            $p2_m19_total = 2;

        } elseif ($request->p1_m19_s1 < $request->p2_m19_s1 && $request->p1_m19_s2 > $request->p2_m19_s2 && $request->p1_m19_s3 > $request->p2_m19_s3) {

            $p1_m19_total = 2;
            $p2_m19_total = 1;

        } elseif ($request->p1_m19_s1 > $request->p2_m19_s1 && $request->p1_m19_s2 < $request->p2_m19_s2 && $request->p1_m19_s3 < $request->p2_m19_s3) {

            $p1_m19_total = 1;
            $p2_m19_total = 2;

        }


        $round_one_results_array['match_19'][$request->p1_m19]['set_1'] = $request->p1_m19_s1;
        $round_one_results_array['match_19'][$request->p1_m19]['set_2'] = $request->p1_m19_s2;
        $round_one_results_array['match_19'][$request->p1_m19]['set_3'] = $request->p1_m19_s3;
        $round_one_results_array['match_19'][$request->p1_m19]['total'] = $p1_m19_total;

        $round_one_results_array['match_19'][$request->p2_m19]['set_1'] = $request->p2_m19_s1;       
        $round_one_results_array['match_19'][$request->p2_m19]['set_2'] = $request->p2_m19_s2;
        $round_one_results_array['match_19'][$request->p2_m19]['set_3'] = $request->p2_m19_s3;
        $round_one_results_array['match_19'][$request->p2_m19]['total'] = $p2_m19_total; 

        $rslt_chk = [$request->p1_m19, $request->p2_m19];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_19', $find_results)) {
                unset($find_results['match_19']);
            }

            $find_results['match_19'][$request->p1_m19]['set_1'] = $request->p1_m19_s1;
            $find_results['match_19'][$request->p1_m19]['set_2'] = $request->p1_m19_s2;
            $find_results['match_19'][$request->p1_m19]['set_3'] = $request->p1_m19_s3;
            $find_results['match_19'][$request->p1_m19]['total'] = $p1_m19_total;

            $find_results['match_19'][$request->p2_m19]['set_1'] = $request->p2_m19_s1;       
            $find_results['match_19'][$request->p2_m19]['set_2'] = $request->p2_m19_s2;
            $find_results['match_19'][$request->p2_m19]['set_3'] = $request->p2_m19_s3;
            $find_results['match_19'][$request->p2_m19]['total'] = $p2_m19_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_19', $find_status)) {
                unset($find_status['match_19']);
            }

            $find_status['match_19'] = $request->rou_1_mat_19_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_19'] = $request->rou_1_mat_19_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m19_total > $p2_m19_total) {
            $round_one_winners_array['match_19'] = $request->p1_m19;
        } else {
           $round_one_winners_array['match_19'] = $request->p2_m19; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_19', $find_winners)) {
                unset($find_winners['match_19']);
            }

            if($p1_m19_total > $p2_m19_total) {
                $find_winners['match_19'] = $request->p1_m19;
            } else {
                $find_winners['match_19'] = $request->p2_m19; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twenty(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m20_s1' => 'required',
            'p1_m20_s2' => 'required',
            'p2_m20_s1' => 'required',
            'p2_m20_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m20_s1 > $request->p2_m20_s1 && $request->p1_m20_s2 > $request->p2_m20_s2) {

            $p1_m20_total = 2;
            $p2_m20_total = 0;

        } elseif ($request->p2_m20_s1 > $request->p1_m20_s1 && $request->p2_m20_s2 > $request->p1_m20_s2) {

            $p1_m20_total = 0;
            $p2_m20_total = 2;

        } elseif ($request->p1_m20_s1 > $request->p2_m20_s1 && $request->p1_m20_s2 < $request->p2_m20_s2 && $request->p1_m20_s3 > $request->p2_m20_s3) {

            $p1_m20_total = 2;
            $p2_m20_total = 1;

        } elseif ($request->p1_m20_s1 < $request->p2_m20_s1 && $request->p1_m20_s2 > $request->p2_m20_s2 && $request->p1_m20_s3 < $request->p2_m20_s3) {

            $p1_m20_total = 1;
            $p2_m20_total = 2;

        } elseif ($request->p1_m20_s1 < $request->p2_m20_s1 && $request->p1_m20_s2 > $request->p2_m20_s2 && $request->p1_m20_s3 > $request->p2_m20_s3) {

            $p1_m20_total = 2;
            $p2_m20_total = 1;

        } elseif ($request->p1_m20_s1 > $request->p2_m20_s1 && $request->p1_m20_s2 < $request->p2_m20_s2 && $request->p1_m20_s3 < $request->p2_m20_s3) {

            $p1_m20_total = 1;
            $p2_m20_total = 2;

        }


        $round_one_results_array['match_20'][$request->p1_m20]['set_1'] = $request->p1_m20_s1;
        $round_one_results_array['match_20'][$request->p1_m20]['set_2'] = $request->p1_m20_s2;
        $round_one_results_array['match_20'][$request->p1_m20]['set_3'] = $request->p1_m20_s3;
        $round_one_results_array['match_20'][$request->p1_m20]['total'] = $p1_m20_total;

        $round_one_results_array['match_20'][$request->p2_m20]['set_1'] = $request->p2_m20_s1;       
        $round_one_results_array['match_20'][$request->p2_m20]['set_2'] = $request->p2_m20_s2;
        $round_one_results_array['match_20'][$request->p2_m20]['set_3'] = $request->p2_m20_s3;
        $round_one_results_array['match_20'][$request->p2_m20]['total'] = $p2_m20_total; 

        $rslt_chk = [$request->p1_m20, $request->p2_m20];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_20', $find_results)) {
                unset($find_results['match_20']);
            }

            $find_results['match_20'][$request->p1_m20]['set_1'] = $request->p1_m20_s1;
            $find_results['match_20'][$request->p1_m20]['set_2'] = $request->p1_m20_s2;
            $find_results['match_20'][$request->p1_m20]['set_3'] = $request->p1_m20_s3;
            $find_results['match_20'][$request->p1_m20]['total'] = $p1_m20_total;

            $find_results['match_20'][$request->p2_m20]['set_1'] = $request->p2_m20_s1;       
            $find_results['match_20'][$request->p2_m20]['set_2'] = $request->p2_m20_s2;
            $find_results['match_20'][$request->p2_m20]['set_3'] = $request->p2_m20_s3;
            $find_results['match_20'][$request->p2_m20]['total'] = $p2_m20_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_20', $find_status)) {
                unset($find_status['match_20']);
            }

            $find_status['match_20'] = $request->rou_1_mat_20_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_20'] = $request->rou_1_mat_20_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m20_total > $p2_m20_total) {
            $round_one_winners_array['match_20'] = $request->p1_m20;
        } else {
           $round_one_winners_array['match_20'] = $request->p2_m20; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_20', $find_winners)) {
                unset($find_winners['match_20']);
            }

            if($p1_m20_total > $p2_m20_total) {
                $find_winners['match_20'] = $request->p1_m20;
            } else {
                $find_winners['match_20'] = $request->p2_m20; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }


    public function submit_round_one_result_twentyone(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m21_s1' => 'required',
            'p1_m21_s2' => 'required',
            'p2_m21_s1' => 'required',
            'p2_m21_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m21_s1 > $request->p2_m21_s1 && $request->p1_m21_s2 > $request->p2_m21_s2) {

            $p1_m21_total = 2;
            $p2_m21_total = 0;

        } elseif ($request->p2_m21_s1 > $request->p1_m21_s1 && $request->p2_m21_s2 > $request->p1_m21_s2) {

            $p1_m21_total = 0;
            $p2_m21_total = 2;

        } elseif ($request->p1_m21_s1 > $request->p2_m21_s1 && $request->p1_m21_s2 < $request->p2_m21_s2 && $request->p1_m21_s3 > $request->p2_m21_s3) {

            $p1_m21_total = 2;
            $p2_m21_total = 1;

        } elseif ($request->p1_m21_s1 < $request->p2_m21_s1 && $request->p1_m21_s2 > $request->p2_m21_s2 && $request->p1_m21_s3 < $request->p2_m21_s3) {

            $p1_m21_total = 1;
            $p2_m21_total = 2;

        } elseif ($request->p1_m21_s1 < $request->p2_m21_s1 && $request->p1_m21_s2 > $request->p2_m21_s2 && $request->p1_m21_s3 > $request->p2_m21_s3) {

            $p1_m21_total = 2;
            $p2_m21_total = 1;

        } elseif ($request->p1_m21_s1 > $request->p2_m21_s1 && $request->p1_m21_s2 < $request->p2_m21_s2 && $request->p1_m21_s3 < $request->p2_m21_s3) {

            $p1_m21_total = 1;
            $p2_m21_total = 2;

        }


        $round_one_results_array['match_21'][$request->p1_m21]['set_1'] = $request->p1_m21_s1;
        $round_one_results_array['match_21'][$request->p1_m21]['set_2'] = $request->p1_m21_s2;
        $round_one_results_array['match_21'][$request->p1_m21]['set_3'] = $request->p1_m21_s3;
        $round_one_results_array['match_21'][$request->p1_m21]['total'] = $p1_m21_total;

        $round_one_results_array['match_21'][$request->p2_m21]['set_1'] = $request->p2_m21_s1;       
        $round_one_results_array['match_21'][$request->p2_m21]['set_2'] = $request->p2_m21_s2;
        $round_one_results_array['match_21'][$request->p2_m21]['set_3'] = $request->p2_m21_s3;
        $round_one_results_array['match_21'][$request->p2_m21]['total'] = $p2_m21_total; 

        $rslt_chk = [$request->p1_m21, $request->p2_m21];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_21', $find_results)) {
                unset($find_results['match_21']);
            }

            $find_results['match_21'][$request->p1_m21]['set_1'] = $request->p1_m21_s1;
            $find_results['match_21'][$request->p1_m21]['set_2'] = $request->p1_m21_s2;
            $find_results['match_21'][$request->p1_m21]['set_3'] = $request->p1_m21_s3;
            $find_results['match_21'][$request->p1_m21]['total'] = $p1_m21_total;

            $find_results['match_21'][$request->p2_m21]['set_1'] = $request->p2_m21_s1;       
            $find_results['match_21'][$request->p2_m21]['set_2'] = $request->p2_m21_s2;
            $find_results['match_21'][$request->p2_m21]['set_3'] = $request->p2_m21_s3;
            $find_results['match_21'][$request->p2_m21]['total'] = $p2_m21_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_21', $find_status)) {
                unset($find_status['match_21']);
            }

            $find_status['match_21'] = $request->rou_1_mat_21_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_21'] = $request->rou_1_mat_21_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m21_total > $p2_m21_total) {
            $round_one_winners_array['match_21'] = $request->p1_m21;
        } else {
           $round_one_winners_array['match_21'] = $request->p2_m21; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_21', $find_winners)) {
                unset($find_winners['match_21']);
            }

            if($p1_m21_total > $p2_m21_total) {
                $find_winners['match_21'] = $request->p1_m21;
            } else {
                $find_winners['match_21'] = $request->p2_m21; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentytwo(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m22_s1' => 'required',
            'p1_m22_s2' => 'required',
            'p2_m22_s1' => 'required',
            'p2_m22_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m22_s1 > $request->p2_m22_s1 && $request->p1_m22_s2 > $request->p2_m22_s2) {

            $p1_m22_total = 2;
            $p2_m22_total = 0;

        } elseif ($request->p2_m22_s1 > $request->p1_m22_s1 && $request->p2_m22_s2 > $request->p1_m22_s2) {

            $p1_m22_total = 0;
            $p2_m22_total = 2;

        } elseif ($request->p1_m22_s1 > $request->p2_m22_s1 && $request->p1_m22_s2 < $request->p2_m22_s2 && $request->p1_m22_s3 > $request->p2_m22_s3) {

            $p1_m22_total = 2;
            $p2_m22_total = 1;

        } elseif ($request->p1_m22_s1 < $request->p2_m22_s1 && $request->p1_m22_s2 > $request->p2_m22_s2 && $request->p1_m22_s3 < $request->p2_m22_s3) {

            $p1_m22_total = 1;
            $p2_m22_total = 2;

        } elseif ($request->p1_m22_s1 < $request->p2_m22_s1 && $request->p1_m22_s2 > $request->p2_m22_s2 && $request->p1_m22_s3 > $request->p2_m22_s3) {

            $p1_m22_total = 2;
            $p2_m22_total = 1;

        } elseif ($request->p1_m22_s1 > $request->p2_m22_s1 && $request->p1_m22_s2 < $request->p2_m22_s2 && $request->p1_m22_s3 < $request->p2_m22_s3) {

            $p1_m22_total = 1;
            $p2_m22_total = 2;

        }


        $round_one_results_array['match_22'][$request->p1_m22]['set_1'] = $request->p1_m22_s1;
        $round_one_results_array['match_22'][$request->p1_m22]['set_2'] = $request->p1_m22_s2;
        $round_one_results_array['match_22'][$request->p1_m22]['set_3'] = $request->p1_m22_s3;
        $round_one_results_array['match_22'][$request->p1_m22]['total'] = $p1_m22_total;

        $round_one_results_array['match_22'][$request->p2_m22]['set_1'] = $request->p2_m22_s1;       
        $round_one_results_array['match_22'][$request->p2_m22]['set_2'] = $request->p2_m22_s2;
        $round_one_results_array['match_22'][$request->p2_m22]['set_3'] = $request->p2_m22_s3;
        $round_one_results_array['match_22'][$request->p2_m22]['total'] = $p2_m22_total; 

        $rslt_chk = [$request->p1_m22, $request->p2_m22];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_22', $find_results)) {
                unset($find_results['match_22']);
            }

            $find_results['match_22'][$request->p1_m22]['set_1'] = $request->p1_m22_s1;
            $find_results['match_22'][$request->p1_m22]['set_2'] = $request->p1_m22_s2;
            $find_results['match_22'][$request->p1_m22]['set_3'] = $request->p1_m22_s3;
            $find_results['match_22'][$request->p1_m22]['total'] = $p1_m22_total;

            $find_results['match_22'][$request->p2_m22]['set_1'] = $request->p2_m22_s1;       
            $find_results['match_22'][$request->p2_m22]['set_2'] = $request->p2_m22_s2;
            $find_results['match_22'][$request->p2_m22]['set_3'] = $request->p2_m22_s3;
            $find_results['match_22'][$request->p2_m22]['total'] = $p2_m22_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_22', $find_status)) {
                unset($find_status['match_22']);
            }

            $find_status['match_22'] = $request->rou_1_mat_22_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_22'] = $request->rou_1_mat_22_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m22_total > $p2_m22_total) {
            $round_one_winners_array['match_22'] = $request->p1_m22;
        } else {
           $round_one_winners_array['match_22'] = $request->p2_m22; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_22', $find_winners)) {
                unset($find_winners['match_22']);
            }

            if($p1_m22_total > $p2_m22_total) {
                $find_winners['match_22'] = $request->p1_m22;
            } else {
                $find_winners['match_22'] = $request->p2_m22; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentythree(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m23_s1' => 'required',
            'p1_m23_s2' => 'required',
            'p2_m23_s1' => 'required',
            'p2_m23_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m23_s1 > $request->p2_m23_s1 && $request->p1_m23_s2 > $request->p2_m23_s2) {

            $p1_m23_total = 2;
            $p2_m23_total = 0;

        } elseif ($request->p2_m23_s1 > $request->p1_m23_s1 && $request->p2_m23_s2 > $request->p1_m23_s2) {

            $p1_m23_total = 0;
            $p2_m23_total = 2;

        } elseif ($request->p1_m23_s1 > $request->p2_m23_s1 && $request->p1_m23_s2 < $request->p2_m23_s2 && $request->p1_m23_s3 > $request->p2_m23_s3) {

            $p1_m23_total = 2;
            $p2_m23_total = 1;

        } elseif ($request->p1_m23_s1 < $request->p2_m23_s1 && $request->p1_m23_s2 > $request->p2_m23_s2 && $request->p1_m23_s3 < $request->p2_m23_s3) {

            $p1_m23_total = 1;
            $p2_m23_total = 2;

        } elseif ($request->p1_m23_s1 < $request->p2_m23_s1 && $request->p1_m23_s2 > $request->p2_m23_s2 && $request->p1_m23_s3 > $request->p2_m23_s3) {

            $p1_m23_total = 2;
            $p2_m23_total = 1;

        } elseif ($request->p1_m23_s1 > $request->p2_m23_s1 && $request->p1_m23_s2 < $request->p2_m23_s2 && $request->p1_m23_s3 < $request->p2_m23_s3) {

            $p1_m23_total = 1;
            $p2_m23_total = 2;

        }


        $round_one_results_array['match_23'][$request->p1_m23]['set_1'] = $request->p1_m23_s1;
        $round_one_results_array['match_23'][$request->p1_m23]['set_2'] = $request->p1_m23_s2;
        $round_one_results_array['match_23'][$request->p1_m23]['set_3'] = $request->p1_m23_s3;
        $round_one_results_array['match_23'][$request->p1_m23]['total'] = $p1_m23_total;

        $round_one_results_array['match_23'][$request->p2_m23]['set_1'] = $request->p2_m23_s1;       
        $round_one_results_array['match_23'][$request->p2_m23]['set_2'] = $request->p2_m23_s2;
        $round_one_results_array['match_23'][$request->p2_m23]['set_3'] = $request->p2_m23_s3;
        $round_one_results_array['match_23'][$request->p2_m23]['total'] = $p2_m23_total; 

        $rslt_chk = [$request->p1_m23, $request->p2_m23];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_23', $find_results)) {
                unset($find_results['match_23']);
            }

            $find_results['match_23'][$request->p1_m23]['set_1'] = $request->p1_m23_s1;
            $find_results['match_23'][$request->p1_m23]['set_2'] = $request->p1_m23_s2;
            $find_results['match_23'][$request->p1_m23]['set_3'] = $request->p1_m23_s3;
            $find_results['match_23'][$request->p1_m23]['total'] = $p1_m23_total;

            $find_results['match_23'][$request->p2_m23]['set_1'] = $request->p2_m23_s1;       
            $find_results['match_23'][$request->p2_m23]['set_2'] = $request->p2_m23_s2;
            $find_results['match_23'][$request->p2_m23]['set_3'] = $request->p2_m23_s3;
            $find_results['match_23'][$request->p2_m23]['total'] = $p2_m23_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_23', $find_status)) {
                unset($find_status['match_23']);
            }

            $find_status['match_23'] = $request->rou_1_mat_23_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_23'] = $request->rou_1_mat_23_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m23_total > $p2_m23_total) {
            $round_one_winners_array['match_23'] = $request->p1_m23;
        } else {
           $round_one_winners_array['match_23'] = $request->p2_m23; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_23', $find_winners)) {
                unset($find_winners['match_23']);
            }

            if($p1_m23_total > $p2_m23_total) {
                $find_winners['match_23'] = $request->p1_m23;
            } else {
                $find_winners['match_23'] = $request->p2_m23; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentyfour(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m24_s1' => 'required',
            'p1_m24_s2' => 'required',
            'p2_m24_s1' => 'required',
            'p2_m24_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m24_s1 > $request->p2_m24_s1 && $request->p1_m24_s2 > $request->p2_m24_s2) {

            $p1_m24_total = 2;
            $p2_m24_total = 0;

        } elseif ($request->p2_m24_s1 > $request->p1_m24_s1 && $request->p2_m24_s2 > $request->p1_m24_s2) {

            $p1_m24_total = 0;
            $p2_m24_total = 2;

        } elseif ($request->p1_m24_s1 > $request->p2_m24_s1 && $request->p1_m24_s2 < $request->p2_m24_s2 && $request->p1_m24_s3 > $request->p2_m24_s3) {

            $p1_m24_total = 2;
            $p2_m24_total = 1;

        } elseif ($request->p1_m24_s1 < $request->p2_m24_s1 && $request->p1_m24_s2 > $request->p2_m24_s2 && $request->p1_m24_s3 < $request->p2_m24_s3) {

            $p1_m24_total = 1;
            $p2_m24_total = 2;

        } elseif ($request->p1_m24_s1 < $request->p2_m24_s1 && $request->p1_m24_s2 > $request->p2_m24_s2 && $request->p1_m24_s3 > $request->p2_m24_s3) {

            $p1_m24_total = 2;
            $p2_m24_total = 1;

        } elseif ($request->p1_m24_s1 > $request->p2_m24_s1 && $request->p1_m24_s2 < $request->p2_m24_s2 && $request->p1_m24_s3 < $request->p2_m24_s3) {

            $p1_m24_total = 1;
            $p2_m24_total = 2;

        }


        $round_one_results_array['match_24'][$request->p1_m24]['set_1'] = $request->p1_m24_s1;
        $round_one_results_array['match_24'][$request->p1_m24]['set_2'] = $request->p1_m24_s2;
        $round_one_results_array['match_24'][$request->p1_m24]['set_3'] = $request->p1_m24_s3;
        $round_one_results_array['match_24'][$request->p1_m24]['total'] = $p1_m24_total;

        $round_one_results_array['match_24'][$request->p2_m24]['set_1'] = $request->p2_m24_s1;       
        $round_one_results_array['match_24'][$request->p2_m24]['set_2'] = $request->p2_m24_s2;
        $round_one_results_array['match_24'][$request->p2_m24]['set_3'] = $request->p2_m24_s3;
        $round_one_results_array['match_24'][$request->p2_m24]['total'] = $p2_m24_total; 

        $rslt_chk = [$request->p1_m24, $request->p2_m24];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_24', $find_results)) {
                unset($find_results['match_24']);
            }

            $find_results['match_24'][$request->p1_m24]['set_1'] = $request->p1_m24_s1;
            $find_results['match_24'][$request->p1_m24]['set_2'] = $request->p1_m24_s2;
            $find_results['match_24'][$request->p1_m24]['set_3'] = $request->p1_m24_s3;
            $find_results['match_24'][$request->p1_m24]['total'] = $p1_m24_total;

            $find_results['match_24'][$request->p2_m24]['set_1'] = $request->p2_m24_s1;       
            $find_results['match_24'][$request->p2_m24]['set_2'] = $request->p2_m24_s2;
            $find_results['match_24'][$request->p2_m24]['set_3'] = $request->p2_m24_s3;
            $find_results['match_24'][$request->p2_m24]['total'] = $p2_m24_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_24', $find_status)) {
                unset($find_status['match_24']);
            }

            $find_status['match_24'] = $request->rou_1_mat_24_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_24'] = $request->rou_1_mat_24_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m24_total > $p2_m24_total) {
            $round_one_winners_array['match_24'] = $request->p1_m24;
        } else {
           $round_one_winners_array['match_24'] = $request->p2_m24; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_24', $find_winners)) {
                unset($find_winners['match_24']);
            }

            if($p1_m24_total > $p2_m24_total) {
                $find_winners['match_24'] = $request->p1_m24;
            } else {
                $find_winners['match_24'] = $request->p2_m24; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentyfive(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m25_s1' => 'required',
            'p1_m25_s2' => 'required',
            'p2_m25_s1' => 'required',
            'p2_m25_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m25_s1 > $request->p2_m25_s1 && $request->p1_m25_s2 > $request->p2_m25_s2) {

            $p1_m25_total = 2;
            $p2_m25_total = 0;

        } elseif ($request->p2_m25_s1 > $request->p1_m25_s1 && $request->p2_m25_s2 > $request->p1_m25_s2) {

            $p1_m25_total = 0;
            $p2_m25_total = 2;

        } elseif ($request->p1_m25_s1 > $request->p2_m25_s1 && $request->p1_m25_s2 < $request->p2_m25_s2 && $request->p1_m25_s3 > $request->p2_m25_s3) {

            $p1_m25_total = 2;
            $p2_m25_total = 1;

        } elseif ($request->p1_m25_s1 < $request->p2_m25_s1 && $request->p1_m25_s2 > $request->p2_m25_s2 && $request->p1_m25_s3 < $request->p2_m25_s3) {

            $p1_m25_total = 1;
            $p2_m25_total = 2;

        } elseif ($request->p1_m25_s1 < $request->p2_m25_s1 && $request->p1_m25_s2 > $request->p2_m25_s2 && $request->p1_m25_s3 > $request->p2_m25_s3) {

            $p1_m25_total = 2;
            $p2_m25_total = 1;

        } elseif ($request->p1_m25_s1 > $request->p2_m25_s1 && $request->p1_m25_s2 < $request->p2_m25_s2 && $request->p1_m25_s3 < $request->p2_m25_s3) {

            $p1_m25_total = 1;
            $p2_m25_total = 2;

        }


        $round_one_results_array['match_25'][$request->p1_m25]['set_1'] = $request->p1_m25_s1;
        $round_one_results_array['match_25'][$request->p1_m25]['set_2'] = $request->p1_m25_s2;
        $round_one_results_array['match_25'][$request->p1_m25]['set_3'] = $request->p1_m25_s3;
        $round_one_results_array['match_25'][$request->p1_m25]['total'] = $p1_m25_total;

        $round_one_results_array['match_25'][$request->p2_m25]['set_1'] = $request->p2_m25_s1;       
        $round_one_results_array['match_25'][$request->p2_m25]['set_2'] = $request->p2_m25_s2;
        $round_one_results_array['match_25'][$request->p2_m25]['set_3'] = $request->p2_m25_s3;
        $round_one_results_array['match_25'][$request->p2_m25]['total'] = $p2_m25_total; 

        $rslt_chk = [$request->p1_m25, $request->p2_m25];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_25', $find_results)) {
                unset($find_results['match_25']);
            }

            $find_results['match_25'][$request->p1_m25]['set_1'] = $request->p1_m25_s1;
            $find_results['match_25'][$request->p1_m25]['set_2'] = $request->p1_m25_s2;
            $find_results['match_25'][$request->p1_m25]['set_3'] = $request->p1_m25_s3;
            $find_results['match_25'][$request->p1_m25]['total'] = $p1_m25_total;

            $find_results['match_25'][$request->p2_m25]['set_1'] = $request->p2_m25_s1;       
            $find_results['match_25'][$request->p2_m25]['set_2'] = $request->p2_m25_s2;
            $find_results['match_25'][$request->p2_m25]['set_3'] = $request->p2_m25_s3;
            $find_results['match_25'][$request->p2_m25]['total'] = $p2_m25_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_25', $find_status)) {
                unset($find_status['match_25']);
            }

            $find_status['match_25'] = $request->rou_1_mat_25_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_25'] = $request->rou_1_mat_25_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m25_total > $p2_m25_total) {
            $round_one_winners_array['match_25'] = $request->p1_m25;
        } else {
           $round_one_winners_array['match_25'] = $request->p2_m25; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_25', $find_winners)) {
                unset($find_winners['match_25']);
            }

            if($p1_m25_total > $p2_m25_total) {
                $find_winners['match_25'] = $request->p1_m25;
            } else {
                $find_winners['match_25'] = $request->p2_m25; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentysix(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m26_s1' => 'required',
            'p1_m26_s2' => 'required',
            'p2_m26_s1' => 'required',
            'p2_m26_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m26_s1 > $request->p2_m26_s1 && $request->p1_m26_s2 > $request->p2_m26_s2) {

            $p1_m26_total = 2;
            $p2_m26_total = 0;

        } elseif ($request->p2_m26_s1 > $request->p1_m26_s1 && $request->p2_m26_s2 > $request->p1_m26_s2) {

            $p1_m26_total = 0;
            $p2_m26_total = 2;

        } elseif ($request->p1_m26_s1 > $request->p2_m26_s1 && $request->p1_m26_s2 < $request->p2_m26_s2 && $request->p1_m26_s3 > $request->p2_m26_s3) {

            $p1_m26_total = 2;
            $p2_m26_total = 1;

        } elseif ($request->p1_m26_s1 < $request->p2_m26_s1 && $request->p1_m26_s2 > $request->p2_m26_s2 && $request->p1_m26_s3 < $request->p2_m26_s3) {

            $p1_m26_total = 1;
            $p2_m26_total = 2;

        } elseif ($request->p1_m26_s1 < $request->p2_m26_s1 && $request->p1_m26_s2 > $request->p2_m26_s2 && $request->p1_m26_s3 > $request->p2_m26_s3) {

            $p1_m26_total = 2;
            $p2_m26_total = 1;

        } elseif ($request->p1_m26_s1 > $request->p2_m26_s1 && $request->p1_m26_s2 < $request->p2_m26_s2 && $request->p1_m26_s3 < $request->p2_m26_s3) {

            $p1_m26_total = 1;
            $p2_m26_total = 2;

        }


        $round_one_results_array['match_26'][$request->p1_m26]['set_1'] = $request->p1_m26_s1;
        $round_one_results_array['match_26'][$request->p1_m26]['set_2'] = $request->p1_m26_s2;
        $round_one_results_array['match_26'][$request->p1_m26]['set_3'] = $request->p1_m26_s3;
        $round_one_results_array['match_26'][$request->p1_m26]['total'] = $p1_m26_total;

        $round_one_results_array['match_26'][$request->p2_m26]['set_1'] = $request->p2_m26_s1;       
        $round_one_results_array['match_26'][$request->p2_m26]['set_2'] = $request->p2_m26_s2;
        $round_one_results_array['match_26'][$request->p2_m26]['set_3'] = $request->p2_m26_s3;
        $round_one_results_array['match_26'][$request->p2_m26]['total'] = $p2_m26_total; 

        $rslt_chk = [$request->p1_m26, $request->p2_m26];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_26', $find_results)) {
                unset($find_results['match_26']);
            }

            $find_results['match_26'][$request->p1_m26]['set_1'] = $request->p1_m26_s1;
            $find_results['match_26'][$request->p1_m26]['set_2'] = $request->p1_m26_s2;
            $find_results['match_26'][$request->p1_m26]['set_3'] = $request->p1_m26_s3;
            $find_results['match_26'][$request->p1_m26]['total'] = $p1_m26_total;

            $find_results['match_26'][$request->p2_m26]['set_1'] = $request->p2_m26_s1;       
            $find_results['match_26'][$request->p2_m26]['set_2'] = $request->p2_m26_s2;
            $find_results['match_26'][$request->p2_m26]['set_3'] = $request->p2_m26_s3;
            $find_results['match_26'][$request->p2_m26]['total'] = $p2_m26_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_26', $find_status)) {
                unset($find_status['match_26']);
            }

            $find_status['match_26'] = $request->rou_1_mat_26_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_26'] = $request->rou_1_mat_26_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m26_total > $p2_m26_total) {
            $round_one_winners_array['match_26'] = $request->p1_m26;
        } else {
           $round_one_winners_array['match_26'] = $request->p2_m26; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_26', $find_winners)) {
                unset($find_winners['match_26']);
            }

            if($p1_m26_total > $p2_m26_total) {
                $find_winners['match_26'] = $request->p1_m26;
            } else {
                $find_winners['match_26'] = $request->p2_m26; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentyseven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m27_s1' => 'required',
            'p1_m27_s2' => 'required',
            'p2_m27_s1' => 'required',
            'p2_m27_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m27_s1 > $request->p2_m27_s1 && $request->p1_m27_s2 > $request->p2_m27_s2) {

            $p1_m27_total = 2;
            $p2_m27_total = 0;

        } elseif ($request->p2_m27_s1 > $request->p1_m27_s1 && $request->p2_m27_s2 > $request->p1_m27_s2) {

            $p1_m27_total = 0;
            $p2_m27_total = 2;

        } elseif ($request->p1_m27_s1 > $request->p2_m27_s1 && $request->p1_m27_s2 < $request->p2_m27_s2 && $request->p1_m27_s3 > $request->p2_m27_s3) {

            $p1_m27_total = 2;
            $p2_m27_total = 1;

        } elseif ($request->p1_m27_s1 < $request->p2_m27_s1 && $request->p1_m27_s2 > $request->p2_m27_s2 && $request->p1_m27_s3 < $request->p2_m27_s3) {

            $p1_m27_total = 1;
            $p2_m27_total = 2;

        } elseif ($request->p1_m27_s1 < $request->p2_m27_s1 && $request->p1_m27_s2 > $request->p2_m27_s2 && $request->p1_m27_s3 > $request->p2_m27_s3) {

            $p1_m27_total = 2;
            $p2_m27_total = 1;

        } elseif ($request->p1_m27_s1 > $request->p2_m27_s1 && $request->p1_m27_s2 < $request->p2_m27_s2 && $request->p1_m27_s3 < $request->p2_m27_s3) {

            $p1_m27_total = 1;
            $p2_m27_total = 2;

        }


        $round_one_results_array['match_27'][$request->p1_m27]['set_1'] = $request->p1_m27_s1;
        $round_one_results_array['match_27'][$request->p1_m27]['set_2'] = $request->p1_m27_s2;
        $round_one_results_array['match_27'][$request->p1_m27]['set_3'] = $request->p1_m27_s3;
        $round_one_results_array['match_27'][$request->p1_m27]['total'] = $p1_m27_total;

        $round_one_results_array['match_27'][$request->p2_m27]['set_1'] = $request->p2_m27_s1;       
        $round_one_results_array['match_27'][$request->p2_m27]['set_2'] = $request->p2_m27_s2;
        $round_one_results_array['match_27'][$request->p2_m27]['set_3'] = $request->p2_m27_s3;
        $round_one_results_array['match_27'][$request->p2_m27]['total'] = $p2_m27_total; 

        $rslt_chk = [$request->p1_m27, $request->p2_m27];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_27', $find_results)) {
                unset($find_results['match_27']);
            }

            $find_results['match_27'][$request->p1_m27]['set_1'] = $request->p1_m27_s1;
            $find_results['match_27'][$request->p1_m27]['set_2'] = $request->p1_m27_s2;
            $find_results['match_27'][$request->p1_m27]['set_3'] = $request->p1_m27_s3;
            $find_results['match_27'][$request->p1_m27]['total'] = $p1_m27_total;

            $find_results['match_27'][$request->p2_m27]['set_1'] = $request->p2_m27_s1;       
            $find_results['match_27'][$request->p2_m27]['set_2'] = $request->p2_m27_s2;
            $find_results['match_27'][$request->p2_m27]['set_3'] = $request->p2_m27_s3;
            $find_results['match_27'][$request->p2_m27]['total'] = $p2_m27_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_27', $find_status)) {
                unset($find_status['match_27']);
            }

            $find_status['match_27'] = $request->rou_1_mat_27_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_27'] = $request->rou_1_mat_27_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m27_total > $p2_m27_total) {
            $round_one_winners_array['match_27'] = $request->p1_m27;
        } else {
           $round_one_winners_array['match_27'] = $request->p2_m27; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_27', $find_winners)) {
                unset($find_winners['match_27']);
            }

            if($p1_m27_total > $p2_m27_total) {
                $find_winners['match_27'] = $request->p1_m27;
            } else {
                $find_winners['match_27'] = $request->p2_m27; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentyeight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m28_s1' => 'required',
            'p1_m28_s2' => 'required',
            'p2_m28_s1' => 'required',
            'p2_m28_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m28_s1 > $request->p2_m28_s1 && $request->p1_m28_s2 > $request->p2_m28_s2) {

            $p1_m28_total = 2;
            $p2_m28_total = 0;

        } elseif ($request->p2_m28_s1 > $request->p1_m28_s1 && $request->p2_m28_s2 > $request->p1_m28_s2) {

            $p1_m28_total = 0;
            $p2_m28_total = 2;

        } elseif ($request->p1_m28_s1 > $request->p2_m28_s1 && $request->p1_m28_s2 < $request->p2_m28_s2 && $request->p1_m28_s3 > $request->p2_m28_s3) {

            $p1_m28_total = 2;
            $p2_m28_total = 1;

        } elseif ($request->p1_m28_s1 < $request->p2_m28_s1 && $request->p1_m28_s2 > $request->p2_m28_s2 && $request->p1_m28_s3 < $request->p2_m28_s3) {

            $p1_m28_total = 1;
            $p2_m28_total = 2;

        } elseif ($request->p1_m28_s1 < $request->p2_m28_s1 && $request->p1_m28_s2 > $request->p2_m28_s2 && $request->p1_m28_s3 > $request->p2_m28_s3) {

            $p1_m28_total = 2;
            $p2_m28_total = 1;

        } elseif ($request->p1_m28_s1 > $request->p2_m28_s1 && $request->p1_m28_s2 < $request->p2_m28_s2 && $request->p1_m28_s3 < $request->p2_m28_s3) {

            $p1_m28_total = 1;
            $p2_m28_total = 2;

        }


        $round_one_results_array['match_28'][$request->p1_m28]['set_1'] = $request->p1_m28_s1;
        $round_one_results_array['match_28'][$request->p1_m28]['set_2'] = $request->p1_m28_s2;
        $round_one_results_array['match_28'][$request->p1_m28]['set_3'] = $request->p1_m28_s3;
        $round_one_results_array['match_28'][$request->p1_m28]['total'] = $p1_m28_total;

        $round_one_results_array['match_28'][$request->p2_m28]['set_1'] = $request->p2_m28_s1;       
        $round_one_results_array['match_28'][$request->p2_m28]['set_2'] = $request->p2_m28_s2;
        $round_one_results_array['match_28'][$request->p2_m28]['set_3'] = $request->p2_m28_s3;
        $round_one_results_array['match_28'][$request->p2_m28]['total'] = $p2_m28_total; 

        $rslt_chk = [$request->p1_m28, $request->p2_m28];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_28', $find_results)) {
                unset($find_results['match_28']);
            }

            $find_results['match_28'][$request->p1_m28]['set_1'] = $request->p1_m28_s1;
            $find_results['match_28'][$request->p1_m28]['set_2'] = $request->p1_m28_s2;
            $find_results['match_28'][$request->p1_m28]['set_3'] = $request->p1_m28_s3;
            $find_results['match_28'][$request->p1_m28]['total'] = $p1_m28_total;

            $find_results['match_28'][$request->p2_m28]['set_1'] = $request->p2_m28_s1;       
            $find_results['match_28'][$request->p2_m28]['set_2'] = $request->p2_m28_s2;
            $find_results['match_28'][$request->p2_m28]['set_3'] = $request->p2_m28_s3;
            $find_results['match_28'][$request->p2_m28]['total'] = $p2_m28_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_28', $find_status)) {
                unset($find_status['match_28']);
            }

            $find_status['match_28'] = $request->rou_1_mat_28_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_28'] = $request->rou_1_mat_28_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m28_total > $p2_m28_total) {
            $round_one_winners_array['match_28'] = $request->p1_m28;
        } else {
           $round_one_winners_array['match_28'] = $request->p2_m28; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_28', $find_winners)) {
                unset($find_winners['match_28']);
            }

            if($p1_m28_total > $p2_m28_total) {
                $find_winners['match_28'] = $request->p1_m28;
            } else {
                $find_winners['match_28'] = $request->p2_m28; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_twentynine(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m29_s1' => 'required',
            'p1_m29_s2' => 'required',
            'p2_m29_s1' => 'required',
            'p2_m29_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m29_s1 > $request->p2_m29_s1 && $request->p1_m29_s2 > $request->p2_m29_s2) {

            $p1_m29_total = 2;
            $p2_m29_total = 0;

        } elseif ($request->p2_m29_s1 > $request->p1_m29_s1 && $request->p2_m29_s2 > $request->p1_m29_s2) {

            $p1_m29_total = 0;
            $p2_m29_total = 2;

        } elseif ($request->p1_m29_s1 > $request->p2_m29_s1 && $request->p1_m29_s2 < $request->p2_m29_s2 && $request->p1_m29_s3 > $request->p2_m29_s3) {

            $p1_m29_total = 2;
            $p2_m29_total = 1;

        } elseif ($request->p1_m29_s1 < $request->p2_m29_s1 && $request->p1_m29_s2 > $request->p2_m29_s2 && $request->p1_m29_s3 < $request->p2_m29_s3) {

            $p1_m29_total = 1;
            $p2_m29_total = 2;

        } elseif ($request->p1_m29_s1 < $request->p2_m29_s1 && $request->p1_m29_s2 > $request->p2_m29_s2 && $request->p1_m29_s3 > $request->p2_m29_s3) {

            $p1_m29_total = 2;
            $p2_m29_total = 1;

        } elseif ($request->p1_m29_s1 > $request->p2_m29_s1 && $request->p1_m29_s2 < $request->p2_m29_s2 && $request->p1_m29_s3 < $request->p2_m29_s3) {

            $p1_m29_total = 1;
            $p2_m29_total = 2;

        }


        $round_one_results_array['match_29'][$request->p1_m29]['set_1'] = $request->p1_m29_s1;
        $round_one_results_array['match_29'][$request->p1_m29]['set_2'] = $request->p1_m29_s2;
        $round_one_results_array['match_29'][$request->p1_m29]['set_3'] = $request->p1_m29_s3;
        $round_one_results_array['match_29'][$request->p1_m29]['total'] = $p1_m29_total;

        $round_one_results_array['match_29'][$request->p2_m29]['set_1'] = $request->p2_m29_s1;       
        $round_one_results_array['match_29'][$request->p2_m29]['set_2'] = $request->p2_m29_s2;
        $round_one_results_array['match_29'][$request->p2_m29]['set_3'] = $request->p2_m29_s3;
        $round_one_results_array['match_29'][$request->p2_m29]['total'] = $p2_m29_total; 

        $rslt_chk = [$request->p1_m29, $request->p2_m29];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_29', $find_results)) {
                unset($find_results['match_29']);
            }

            $find_results['match_29'][$request->p1_m29]['set_1'] = $request->p1_m29_s1;
            $find_results['match_29'][$request->p1_m29]['set_2'] = $request->p1_m29_s2;
            $find_results['match_29'][$request->p1_m29]['set_3'] = $request->p1_m29_s3;
            $find_results['match_29'][$request->p1_m29]['total'] = $p1_m29_total;

            $find_results['match_29'][$request->p2_m29]['set_1'] = $request->p2_m29_s1;       
            $find_results['match_29'][$request->p2_m29]['set_2'] = $request->p2_m29_s2;
            $find_results['match_29'][$request->p2_m29]['set_3'] = $request->p2_m29_s3;
            $find_results['match_29'][$request->p2_m29]['total'] = $p2_m29_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_29', $find_status)) {
                unset($find_status['match_29']);
            }

            $find_status['match_29'] = $request->rou_1_mat_29_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_29'] = $request->rou_1_mat_29_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m29_total > $p2_m29_total) {
            $round_one_winners_array['match_29'] = $request->p1_m29;
        } else {
           $round_one_winners_array['match_29'] = $request->p2_m29; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_29', $find_winners)) {
                unset($find_winners['match_29']);
            }

            if($p1_m29_total > $p2_m29_total) {
                $find_winners['match_29'] = $request->p1_m29;
            } else {
                $find_winners['match_29'] = $request->p2_m29; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_thirty(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m30_s1' => 'required',
            'p1_m30_s2' => 'required',
            'p2_m30_s1' => 'required',
            'p2_m30_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m30_s1 > $request->p2_m30_s1 && $request->p1_m30_s2 > $request->p2_m30_s2) {

            $p1_m30_total = 2;
            $p2_m30_total = 0;

        } elseif ($request->p2_m30_s1 > $request->p1_m30_s1 && $request->p2_m30_s2 > $request->p1_m30_s2) {

            $p1_m30_total = 0;
            $p2_m30_total = 2;

        } elseif ($request->p1_m30_s1 > $request->p2_m30_s1 && $request->p1_m30_s2 < $request->p2_m30_s2 && $request->p1_m30_s3 > $request->p2_m30_s3) {

            $p1_m30_total = 2;
            $p2_m30_total = 1;

        } elseif ($request->p1_m30_s1 < $request->p2_m30_s1 && $request->p1_m30_s2 > $request->p2_m30_s2 && $request->p1_m30_s3 < $request->p2_m30_s3) {

            $p1_m30_total = 1;
            $p2_m30_total = 2;

        } elseif ($request->p1_m30_s1 < $request->p2_m30_s1 && $request->p1_m30_s2 > $request->p2_m30_s2 && $request->p1_m30_s3 > $request->p2_m30_s3) {

            $p1_m30_total = 2;
            $p2_m30_total = 1;

        } elseif ($request->p1_m30_s1 > $request->p2_m30_s1 && $request->p1_m30_s2 < $request->p2_m30_s2 && $request->p1_m30_s3 < $request->p2_m30_s3) {

            $p1_m30_total = 1;
            $p2_m30_total = 2;

        }


        $round_one_results_array['match_30'][$request->p1_m30]['set_1'] = $request->p1_m30_s1;
        $round_one_results_array['match_30'][$request->p1_m30]['set_2'] = $request->p1_m30_s2;
        $round_one_results_array['match_30'][$request->p1_m30]['set_3'] = $request->p1_m30_s3;
        $round_one_results_array['match_30'][$request->p1_m30]['total'] = $p1_m30_total;

        $round_one_results_array['match_30'][$request->p2_m30]['set_1'] = $request->p2_m30_s1;       
        $round_one_results_array['match_30'][$request->p2_m30]['set_2'] = $request->p2_m30_s2;
        $round_one_results_array['match_30'][$request->p2_m30]['set_3'] = $request->p2_m30_s3;
        $round_one_results_array['match_30'][$request->p2_m30]['total'] = $p2_m30_total; 

        $rslt_chk = [$request->p1_m30, $request->p2_m30];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_30', $find_results)) {
                unset($find_results['match_30']);
            }

            $find_results['match_30'][$request->p1_m30]['set_1'] = $request->p1_m30_s1;
            $find_results['match_30'][$request->p1_m30]['set_2'] = $request->p1_m30_s2;
            $find_results['match_30'][$request->p1_m30]['set_3'] = $request->p1_m30_s3;
            $find_results['match_30'][$request->p1_m30]['total'] = $p1_m30_total;

            $find_results['match_30'][$request->p2_m30]['set_1'] = $request->p2_m30_s1;       
            $find_results['match_30'][$request->p2_m30]['set_2'] = $request->p2_m30_s2;
            $find_results['match_30'][$request->p2_m30]['set_3'] = $request->p2_m30_s3;
            $find_results['match_30'][$request->p2_m30]['total'] = $p2_m30_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_30', $find_status)) {
                unset($find_status['match_30']);
            }

            $find_status['match_30'] = $request->rou_1_mat_30_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_30'] = $request->rou_1_mat_30_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m30_total > $p2_m30_total) {
            $round_one_winners_array['match_30'] = $request->p1_m30;
        } else {
           $round_one_winners_array['match_30'] = $request->p2_m30; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_30', $find_winners)) {
                unset($find_winners['match_30']);
            }

            if($p1_m30_total > $p2_m30_total) {
                $find_winners['match_30'] = $request->p1_m30;
            } else {
                $find_winners['match_30'] = $request->p2_m30; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_thirtyone(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m31_s1' => 'required',
            'p1_m31_s2' => 'required',
            'p2_m31_s1' => 'required',
            'p2_m31_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m31_s1 > $request->p2_m31_s1 && $request->p1_m31_s2 > $request->p2_m31_s2) {

            $p1_m31_total = 2;
            $p2_m31_total = 0;

        } elseif ($request->p2_m31_s1 > $request->p1_m31_s1 && $request->p2_m31_s2 > $request->p1_m31_s2) {

            $p1_m31_total = 0;
            $p2_m31_total = 2;

        } elseif ($request->p1_m31_s1 > $request->p2_m31_s1 && $request->p1_m31_s2 < $request->p2_m31_s2 && $request->p1_m31_s3 > $request->p2_m31_s3) {

            $p1_m31_total = 2;
            $p2_m31_total = 1;

        } elseif ($request->p1_m31_s1 < $request->p2_m31_s1 && $request->p1_m31_s2 > $request->p2_m31_s2 && $request->p1_m31_s3 < $request->p2_m31_s3) {

            $p1_m31_total = 1;
            $p2_m31_total = 2;

        } elseif ($request->p1_m31_s1 < $request->p2_m31_s1 && $request->p1_m31_s2 > $request->p2_m31_s2 && $request->p1_m31_s3 > $request->p2_m31_s3) {

            $p1_m31_total = 2;
            $p2_m31_total = 1;

        } elseif ($request->p1_m31_s1 > $request->p2_m31_s1 && $request->p1_m31_s2 < $request->p2_m31_s2 && $request->p1_m31_s3 < $request->p2_m31_s3) {

            $p1_m31_total = 1;
            $p2_m31_total = 2;

        }


        $round_one_results_array['match_31'][$request->p1_m31]['set_1'] = $request->p1_m31_s1;
        $round_one_results_array['match_31'][$request->p1_m31]['set_2'] = $request->p1_m31_s2;
        $round_one_results_array['match_31'][$request->p1_m31]['set_3'] = $request->p1_m31_s3;
        $round_one_results_array['match_31'][$request->p1_m31]['total'] = $p1_m31_total;

        $round_one_results_array['match_31'][$request->p2_m31]['set_1'] = $request->p2_m31_s1;       
        $round_one_results_array['match_31'][$request->p2_m31]['set_2'] = $request->p2_m31_s2;
        $round_one_results_array['match_31'][$request->p2_m31]['set_3'] = $request->p2_m31_s3;
        $round_one_results_array['match_31'][$request->p2_m31]['total'] = $p2_m31_total; 

        $rslt_chk = [$request->p1_m31, $request->p2_m31];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_31', $find_results)) {
                unset($find_results['match_31']);
            }

            $find_results['match_31'][$request->p1_m31]['set_1'] = $request->p1_m31_s1;
            $find_results['match_31'][$request->p1_m31]['set_2'] = $request->p1_m31_s2;
            $find_results['match_31'][$request->p1_m31]['set_3'] = $request->p1_m31_s3;
            $find_results['match_31'][$request->p1_m31]['total'] = $p1_m31_total;

            $find_results['match_31'][$request->p2_m31]['set_1'] = $request->p2_m31_s1;       
            $find_results['match_31'][$request->p2_m31]['set_2'] = $request->p2_m31_s2;
            $find_results['match_31'][$request->p2_m31]['set_3'] = $request->p2_m31_s3;
            $find_results['match_31'][$request->p2_m31]['total'] = $p2_m31_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_31', $find_status)) {
                unset($find_status['match_31']);
            }

            $find_status['match_31'] = $request->rou_1_mat_31_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_31'] = $request->rou_1_mat_31_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m31_total > $p2_m31_total) {
            $round_one_winners_array['match_31'] = $request->p1_m31;
        } else {
           $round_one_winners_array['match_31'] = $request->p2_m31; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_31', $find_winners)) {
                unset($find_winners['match_31']);
            }

            if($p1_m31_total > $p2_m31_total) {
                $find_winners['match_31'] = $request->p1_m31;
            } else {
                $find_winners['match_31'] = $request->p2_m31; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_one_result_thirtytwo(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m32_s1' => 'required',
            'p1_m32_s2' => 'required',
            'p2_m32_s1' => 'required',
            'p2_m32_s2' => 'required',
        ]);

        $round_one_results_array = [];


        if ($request->p1_m32_s1 > $request->p2_m32_s1 && $request->p1_m32_s2 > $request->p2_m32_s2) {

            $p1_m32_total = 2;
            $p2_m32_total = 0;

        } elseif ($request->p2_m32_s1 > $request->p1_m32_s1 && $request->p2_m32_s2 > $request->p1_m32_s2) {

            $p1_m32_total = 0;
            $p2_m32_total = 2;

        } elseif ($request->p1_m32_s1 > $request->p2_m32_s1 && $request->p1_m32_s2 < $request->p2_m32_s2 && $request->p1_m32_s3 > $request->p2_m32_s3) {

            $p1_m32_total = 2;
            $p2_m32_total = 1;

        } elseif ($request->p1_m32_s1 < $request->p2_m32_s1 && $request->p1_m32_s2 > $request->p2_m32_s2 && $request->p1_m32_s3 < $request->p2_m32_s3) {

            $p1_m32_total = 1;
            $p2_m32_total = 2;

        } elseif ($request->p1_m32_s1 < $request->p2_m32_s1 && $request->p1_m32_s2 > $request->p2_m32_s2 && $request->p1_m32_s3 > $request->p2_m32_s3) {

            $p1_m32_total = 2;
            $p2_m32_total = 1;

        } elseif ($request->p1_m32_s1 > $request->p2_m32_s1 && $request->p1_m32_s2 < $request->p2_m32_s2 && $request->p1_m32_s3 < $request->p2_m32_s3) {

            $p1_m32_total = 1;
            $p2_m32_total = 2;

        }


        $round_one_results_array['match_32'][$request->p1_m32]['set_1'] = $request->p1_m32_s1;
        $round_one_results_array['match_32'][$request->p1_m32]['set_2'] = $request->p1_m32_s2;
        $round_one_results_array['match_32'][$request->p1_m32]['set_3'] = $request->p1_m32_s3;
        $round_one_results_array['match_32'][$request->p1_m32]['total'] = $p1_m32_total;

        $round_one_results_array['match_32'][$request->p2_m32]['set_1'] = $request->p2_m32_s1;       
        $round_one_results_array['match_32'][$request->p2_m32]['set_2'] = $request->p2_m32_s2;
        $round_one_results_array['match_32'][$request->p2_m32]['set_3'] = $request->p2_m32_s3;
        $round_one_results_array['match_32'][$request->p2_m32]['total'] = $p2_m32_total; 

        $rslt_chk = [$request->p1_m32, $request->p2_m32];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_one_results) {
            
            $find_results = json_decode($tournament->round_one_results, true);
            if(array_key_exists('match_32', $find_results)) {
                unset($find_results['match_32']);
            }

            $find_results['match_32'][$request->p1_m32]['set_1'] = $request->p1_m32_s1;
            $find_results['match_32'][$request->p1_m32]['set_2'] = $request->p1_m32_s2;
            $find_results['match_32'][$request->p1_m32]['set_3'] = $request->p1_m32_s3;
            $find_results['match_32'][$request->p1_m32]['total'] = $p1_m32_total;

            $find_results['match_32'][$request->p2_m32]['set_1'] = $request->p2_m32_s1;       
            $find_results['match_32'][$request->p2_m32]['set_2'] = $request->p2_m32_s2;
            $find_results['match_32'][$request->p2_m32]['set_3'] = $request->p2_m32_s3;
            $find_results['match_32'][$request->p2_m32]['total'] = $p2_m32_total; 
            $tournament->round_one_results = json_encode($find_results);

        } else {
            $tournament->round_one_results = json_encode($round_one_results_array);
        }


        $round_one_status_array = [];
        if($tournament->round_one_status) {
            
            $find_status = json_decode($tournament->round_one_status, true);
            if(array_key_exists('match_32', $find_status)) {
                unset($find_status['match_32']);
            }

            $find_status['match_32'] = $request->rou_1_mat_32_status;
            $tournament->round_one_status = json_encode($find_status);

        } else {
            $round_one_status_array['match_32'] = $request->rou_1_mat_32_status;
            $tournament->round_one_status = json_encode($round_one_status_array);
        }

        
        $round_one_winners_array = [];        
        if($p1_m32_total > $p2_m32_total) {
            $round_one_winners_array['match_32'] = $request->p1_m32;
        } else {
           $round_one_winners_array['match_32'] = $request->p2_m32; 
        }

        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            if(array_key_exists('match_32', $find_winners)) {
                unset($find_winners['match_32']);
            }

            if($p1_m32_total > $p2_m32_total) {
                $find_winners['match_32'] = $request->p1_m32;
            } else {
                $find_winners['match_32'] = $request->p2_m32; 
            }
            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }



    public function submit_round_one_winners(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
       
            
        // $this->validate($request, [
        //     'rou_1_mat_1_winner' => 'required',
        //     'rou_1_mat_2_winner' => 'required',
        //     'rou_1_mat_3_winner' => 'required',
        //     'rou_1_mat_4_winner' => 'required',
        //     'rou_1_mat_5_winner' => 'required',
        //     'rou_1_mat_6_winner' => 'required',
        //     'rou_1_mat_7_winner' => 'required',
        //     'rou_1_mat_8_winner' => 'required',
        // ]);


        $round_one_winners_array = [];
        
        $round_one_wnr_chk = [$request->rou_1_mat_1_winner, $request->rou_1_mat_2_winner, $request->rou_1_mat_3_winner, $request->rou_1_mat_4_winner, $request->rou_1_mat_5_winner, $request->rou_1_mat_6_winner, $request->rou_1_mat_7_winner, $request->rou_1_mat_8_winner, $request->rou_1_mat_9_winner, $request->rou_1_mat_10_winner, $request->rou_1_mat_11_winner, $request->rou_1_mat_12_winner, $request->rou_1_mat_13_winner, $request->rou_1_mat_14_winner, $request->rou_1_mat_15_winner, $request->rou_1_mat_16_winner, $request->rou_1_mat_17_winner, $request->rou_1_mat_18_winner, $request->rou_1_mat_19_winner, $request->rou_1_mat_20_winner, $request->rou_1_mat_21_winner, $request->rou_1_mat_22_winner, $request->rou_1_mat_23_winner, $request->rou_1_mat_24_winner, $request->rou_1_mat_25_winner, $request->rou_1_mat_26_winner, $request->rou_1_mat_27_winner, $request->rou_1_mat_28_winner, $request->rou_1_mat_29_winner, $request->rou_1_mat_30_winner, $request->rou_1_mat_31_winner, $request->rou_1_mat_32_winner];

        
        if($request->rou_1_mat_1_winner) {
            $round_one_winners_array['match_1'] = $request->rou_1_mat_1_winner;
        }

        if($request->rou_1_mat_2_winner) {
            $round_one_winners_array['match_2'] = $request->rou_1_mat_2_winner;
        }

        if($request->rou_1_mat_3_winner) {
            $round_one_winners_array['match_3'] = $request->rou_1_mat_3_winner;
        }

        if($request->rou_1_mat_4_winner) {
            $round_one_winners_array['match_4'] = $request->rou_1_mat_4_winner;
        }

        if($request->rou_1_mat_5_winner) {
            $round_one_winners_array['match_5'] = $request->rou_1_mat_5_winner;
        }

        if($request->rou_1_mat_6_winner) {
            $round_one_winners_array['match_6'] = $request->rou_1_mat_6_winner;
        }

        if($request->rou_1_mat_7_winner) {
            $round_one_winners_array['match_7'] = $request->rou_1_mat_7_winner;
        }

        if($request->rou_1_mat_8_winner) {
            $round_one_winners_array['match_8'] = $request->rou_1_mat_8_winner;
        }


        if($request->rou_1_mat_9_winner) {
            $round_one_winners_array['match_9'] = $request->rou_1_mat_9_winner;
        }

        if($request->rou_1_mat_10_winner) {
            $round_one_winners_array['match_10'] = $request->rou_1_mat_10_winner;
        }

        if($request->rou_1_mat_11_winner) {
            $round_one_winners_array['match_11'] = $request->rou_1_mat_11_winner;
        }

        if($request->rou_1_mat_12_winner) {
            $round_one_winners_array['match_12'] = $request->rou_1_mat_12_winner;
        }

        if($request->rou_1_mat_13_winner) {
            $round_one_winners_array['match_13'] = $request->rou_1_mat_13_winner;
        }

        if($request->rou_1_mat_14_winner) {
            $round_one_winners_array['match_14'] = $request->rou_1_mat_14_winner;
        }

        if($request->rou_1_mat_15_winner) {
            $round_one_winners_array['match_15'] = $request->rou_1_mat_15_winner;
        }

        if($request->rou_1_mat_16_winner) {
            $round_one_winners_array['match_16'] = $request->rou_1_mat_16_winner;
        }


        if($request->rou_1_mat_17_winner) {
            $round_one_winners_array['match_17'] = $request->rou_1_mat_17_winner;
        }

        if($request->rou_1_mat_18_winner) {
            $round_one_winners_array['match_18'] = $request->rou_1_mat_18_winner;
        }

        if($request->rou_1_mat_19_winner) {
            $round_one_winners_array['match_19'] = $request->rou_1_mat_19_winner;
        }

        if($request->rou_1_mat_20_winner) {
            $round_one_winners_array['match_20'] = $request->rou_1_mat_20_winner;
        }

        if($request->rou_1_mat_21_winner) {
            $round_one_winners_array['match_21'] = $request->rou_1_mat_21_winner;
        }

        if($request->rou_1_mat_22_winner) {
            $round_one_winners_array['match_22'] = $request->rou_1_mat_22_winner;
        }

        if($request->rou_1_mat_23_winner) {
            $round_one_winners_array['match_23'] = $request->rou_1_mat_23_winner;
        }

        if($request->rou_1_mat_24_winner) {
            $round_one_winners_array['match_24'] = $request->rou_1_mat_24_winner;
        }


        if($request->rou_1_mat_25_winner) {
            $round_one_winners_array['match_25'] = $request->rou_1_mat_25_winner;
        }

        if($request->rou_1_mat_26_winner) {
            $round_one_winners_array['match_26'] = $request->rou_1_mat_26_winner;
        }

        if($request->rou_1_mat_27_winner) {
            $round_one_winners_array['match_27'] = $request->rou_1_mat_27_winner;
        }

        if($request->rou_1_mat_28_winner) {
            $round_one_winners_array['match_28'] = $request->rou_1_mat_28_winner;
        }

        if($request->rou_1_mat_29_winner) {
            $round_one_winners_array['match_29'] = $request->rou_1_mat_29_winner;
        }

        if($request->rou_1_mat_30_winner) {
            $round_one_winners_array['match_30'] = $request->rou_1_mat_30_winner;
        }

        if($request->rou_1_mat_31_winner) {
            $round_one_winners_array['match_31'] = $request->rou_1_mat_31_winner;
        }

        if($request->rou_1_mat_32_winner) {
            $round_one_winners_array['match_32'] = $request->rou_1_mat_32_winner;
        }

        
        $chk_players = max(array_count_values($round_one_wnr_chk));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Announced as Winner Twice !');
            return redirect()->back();
        }

        $tournament->round_one_winners = json_encode($round_one_winners_array);

        $tournament->save();

        Session::flash('success', 'Players Announced as Winners Successfully !');
        return redirect()->back();
        

    }



    // ROUND 2
    public function submit_round_two_match_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_1_player_1' => 'required',
            'rou_2_mat_1_player_2' => 'required',
        ]);
        
        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_1_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_1_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_1_player_1, $request->rou_2_mat_1_player_2];

        $round_two_matches_array['match_1'] = $request->rou_2_mat_1_player_1 . ' VS ' . $request->rou_2_mat_1_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {

            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_1', $find_matches)) {
                unset($find_matches['match_1']);
            }
            $find_matches['match_1'] = $request->rou_2_mat_1_player_1 . ' VS ' . $request->rou_2_mat_1_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_2_player_1' => 'required',
            'rou_2_mat_2_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_2_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_2_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_2_player_1, $request->rou_2_mat_2_player_2];

        $round_two_matches_array['match_2'] = $request->rou_2_mat_2_player_1 . ' VS ' . $request->rou_2_mat_2_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_2', $find_matches)) {
                unset($find_matches['match_2']);
            }
            $find_matches['match_2'] = $request->rou_2_mat_2_player_1 . ' VS ' . $request->rou_2_mat_2_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_3_player_1' => 'required',
            'rou_2_mat_3_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_3_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_3_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_3_player_1, $request->rou_2_mat_3_player_2];

        $round_two_matches_array['match_3'] = $request->rou_2_mat_3_player_1 . ' VS ' . $request->rou_2_mat_3_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_3', $find_matches)) {
                unset($find_matches['match_3']);
            }
            $find_matches['match_3'] = $request->rou_2_mat_3_player_1 . ' VS ' . $request->rou_2_mat_3_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_4_player_1' => 'required',
            'rou_2_mat_4_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_4_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_4_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_4_player_1, $request->rou_2_mat_4_player_2];

        $round_two_matches_array['match_4'] = $request->rou_2_mat_4_player_1 . ' VS ' . $request->rou_2_mat_4_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_4', $find_matches)) {
                unset($find_matches['match_4']);
            }
            $find_matches['match_4'] = $request->rou_2_mat_4_player_1 . ' VS ' . $request->rou_2_mat_4_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_five(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_5_player_1' => 'required',
            'rou_2_mat_5_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_5_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_5_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_5_player_1, $request->rou_2_mat_5_player_2];

        $round_two_matches_array['match_5'] = $request->rou_2_mat_5_player_1 . ' VS ' . $request->rou_2_mat_5_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_5', $find_matches)) {
                unset($find_matches['match_5']);
            }
            $find_matches['match_5'] = $request->rou_2_mat_5_player_1 . ' VS ' . $request->rou_2_mat_5_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_six(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_6_player_1' => 'required',
            'rou_2_mat_6_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_6_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_6_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_6_player_1, $request->rou_2_mat_6_player_2];

        $round_two_matches_array['match_6'] = $request->rou_2_mat_6_player_1 . ' VS ' . $request->rou_2_mat_6_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_6', $find_matches)) {
                unset($find_matches['match_6']);
            }
            $find_matches['match_6'] = $request->rou_2_mat_6_player_1 . ' VS ' . $request->rou_2_mat_6_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_seven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_7_player_1' => 'required',
            'rou_2_mat_7_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_7_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_7_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_7_player_1, $request->rou_2_mat_7_player_2];

        $round_two_matches_array['match_7'] = $request->rou_2_mat_7_player_1 . ' VS ' . $request->rou_2_mat_7_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_7', $find_matches)) {
                unset($find_matches['match_7']);
            }
            $find_matches['match_7'] = $request->rou_2_mat_7_player_1 . ' VS ' . $request->rou_2_mat_7_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_eight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_8_player_1' => 'required',
            'rou_2_mat_8_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_8_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_8_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_8_player_1, $request->rou_2_mat_8_player_2];

        $round_two_matches_array['match_8'] = $request->rou_2_mat_8_player_1 . ' VS ' . $request->rou_2_mat_8_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_8', $find_matches)) {
                unset($find_matches['match_8']);
            }
            $find_matches['match_8'] = $request->rou_2_mat_8_player_1 . ' VS ' . $request->rou_2_mat_8_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_nine(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_9_player_1' => 'required',
            'rou_2_mat_9_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_9_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_9_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_9_player_1, $request->rou_2_mat_9_player_2];

        $round_two_matches_array['match_9'] = $request->rou_2_mat_9_player_1 . ' VS ' . $request->rou_2_mat_9_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_9', $find_matches)) {
                unset($find_matches['match_9']);
            }
            $find_matches['match_9'] = $request->rou_2_mat_9_player_1 . ' VS ' . $request->rou_2_mat_9_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_ten(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_10_player_1' => 'required',
            'rou_2_mat_10_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_10_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_10_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_10_player_1, $request->rou_2_mat_10_player_2];

        $round_two_matches_array['match_10'] = $request->rou_2_mat_10_player_1 . ' VS ' . $request->rou_2_mat_10_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_10', $find_matches)) {
                unset($find_matches['match_10']);
            }
            $find_matches['match_10'] = $request->rou_2_mat_10_player_1 . ' VS ' . $request->rou_2_mat_10_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_eleven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_11_player_1' => 'required',
            'rou_2_mat_11_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_11_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_11_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_11_player_1, $request->rou_2_mat_11_player_2];

        $round_two_matches_array['match_11'] = $request->rou_2_mat_11_player_1 . ' VS ' . $request->rou_2_mat_11_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_11', $find_matches)) {
                unset($find_matches['match_11']);
            }
            $find_matches['match_11'] = $request->rou_2_mat_11_player_1 . ' VS ' . $request->rou_2_mat_11_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_twelve(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_12_player_1' => 'required',
            'rou_2_mat_12_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_12_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_12_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_12_player_1, $request->rou_2_mat_12_player_2];

        $round_two_matches_array['match_12'] = $request->rou_2_mat_12_player_1 . ' VS ' . $request->rou_2_mat_12_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_12', $find_matches)) {
                unset($find_matches['match_12']);
            }
            $find_matches['match_12'] = $request->rou_2_mat_12_player_1 . ' VS ' . $request->rou_2_mat_12_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_thirteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_13_player_1' => 'required',
            'rou_2_mat_13_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_13_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_13_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_13_player_1, $request->rou_2_mat_13_player_2];

        $round_two_matches_array['match_13'] = $request->rou_2_mat_13_player_1 . ' VS ' . $request->rou_2_mat_13_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_13', $find_matches)) {
                unset($find_matches['match_13']);
            }
            $find_matches['match_13'] = $request->rou_2_mat_13_player_1 . ' VS ' . $request->rou_2_mat_13_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_fourteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_14_player_1' => 'required',
            'rou_2_mat_14_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_14_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_14_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_14_player_1, $request->rou_2_mat_14_player_2];

        $round_two_matches_array['match_14'] = $request->rou_2_mat_14_player_1 . ' VS ' . $request->rou_2_mat_14_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_14', $find_matches)) {
                unset($find_matches['match_14']);
            }
            $find_matches['match_14'] = $request->rou_2_mat_14_player_1 . ' VS ' . $request->rou_2_mat_14_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_fifteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_15_player_1' => 'required',
            'rou_2_mat_15_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_15_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_15_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_15_player_1, $request->rou_2_mat_15_player_2];

        $round_two_matches_array['match_15'] = $request->rou_2_mat_15_player_1 . ' VS ' . $request->rou_2_mat_15_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_15', $find_matches)) {
                unset($find_matches['match_15']);
            }
            $find_matches['match_15'] = $request->rou_2_mat_15_player_1 . ' VS ' . $request->rou_2_mat_15_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_two_match_sixteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_2_mat_16_player_1' => 'required',
            'rou_2_mat_16_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/2;

        $plr_1 = User::findOrFail($request->rou_2_mat_16_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_2_mat_16_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou2 = json_decode($tournament->round_two_deadline);
        $endd_r1 = explode(", ", $t_d_rou2->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_two_matches_array = [];
        $match_chk_players  = [$request->rou_2_mat_16_player_1, $request->rou_2_mat_16_player_2];

        $round_two_matches_array['match_16'] = $request->rou_2_mat_16_player_1 . ' VS ' . $request->rou_2_mat_16_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_matches) {
            
            $find_matches = json_decode($tournament->round_two_matches, true);
            if(array_key_exists('match_16', $find_matches)) {
                unset($find_matches['match_16']);
            }
            $find_matches['match_16'] = $request->rou_2_mat_16_player_1 . ' VS ' . $request->rou_2_mat_16_player_2;
            $tournament->round_two_matches = json_encode($find_matches);

        } else {
            $tournament->round_two_matches = json_encode($round_two_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }



    public function submit_round_two_result_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m1_s1' => 'required',
            'p1_m1_s2' => 'required',
            'p2_m1_s1' => 'required',
            'p2_m1_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2) {

            $p1_m1_total = 2;
            $p2_m1_total = 0;

        } elseif ($request->p2_m1_s1 > $request->p1_m1_s1 && $request->p2_m1_s2 > $request->p1_m1_s2) {

            $p1_m1_total = 0;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        }


        $round_two_results_array['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
        $round_two_results_array['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
        $round_two_results_array['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
        $round_two_results_array['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

        $round_two_results_array['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
        $round_two_results_array['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
        $round_two_results_array['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
        $round_two_results_array['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 

        $rslt_chk = [$request->p1_m1, $request->p2_m1];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_1', $find_results)) {
                unset($find_results['match_1']);
            }

            $find_results['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
            $find_results['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
            $find_results['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
            $find_results['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

            $find_results['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
            $find_results['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
            $find_results['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
            $find_results['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_1', $find_status)) {
                unset($find_status['match_1']);
            }

            $find_status['match_1'] = $request->rou_2_mat_1_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_1'] = $request->rou_2_mat_1_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m1_total > $p2_m1_total) {
            $round_two_winners_array['match_1'] = $request->p1_m1;
        } else {
           $round_two_winners_array['match_1'] = $request->p2_m1; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_1', $find_winners)) {
                unset($find_winners['match_1']);
            }

            if($p1_m1_total > $p2_m1_total) {
                $find_winners['match_1'] = $request->p1_m1;
            } else {
                $find_winners['match_1'] = $request->p2_m1; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m2_s1' => 'required',
            'p1_m2_s2' => 'required',
            'p2_m2_s1' => 'required',
            'p2_m2_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2) {

            $p1_m2_total = 2;
            $p2_m2_total = 0;

        } elseif ($request->p2_m2_s1 > $request->p1_m2_s1 && $request->p2_m2_s2 > $request->p1_m2_s2) {

            $p1_m2_total = 0;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        }


        $round_two_results_array['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
        $round_two_results_array['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
        $round_two_results_array['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
        $round_two_results_array['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

        $round_two_results_array['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
        $round_two_results_array['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
        $round_two_results_array['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
        $round_two_results_array['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 

        $rslt_chk = [$request->p1_m2, $request->p2_m2];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_2', $find_results)) {
                unset($find_results['match_2']);
            }

            $find_results['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
            $find_results['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
            $find_results['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
            $find_results['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

            $find_results['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
            $find_results['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
            $find_results['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
            $find_results['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_2', $find_status)) {
                unset($find_status['match_2']);
            }

            $find_status['match_2'] = $request->rou_2_mat_2_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_2'] = $request->rou_2_mat_2_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m2_total > $p2_m2_total) {
            $round_two_winners_array['match_2'] = $request->p1_m2;
        } else {
           $round_two_winners_array['match_2'] = $request->p2_m2; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_2', $find_winners)) {
                unset($find_winners['match_2']);
            }

            if($p1_m2_total > $p2_m2_total) {
                $find_winners['match_2'] = $request->p1_m2;
            } else {
                $find_winners['match_2'] = $request->p2_m2; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m3_s1' => 'required',
            'p1_m3_s2' => 'required',
            'p2_m3_s1' => 'required',
            'p2_m3_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2) {

            $p1_m3_total = 2;
            $p2_m3_total = 0;

        } elseif ($request->p2_m3_s1 > $request->p1_m3_s1 && $request->p2_m3_s2 > $request->p1_m3_s2) {

            $p1_m3_total = 0;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        }


        $round_two_results_array['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
        $round_two_results_array['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
        $round_two_results_array['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
        $round_two_results_array['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

        $round_two_results_array['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
        $round_two_results_array['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
        $round_two_results_array['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
        $round_two_results_array['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 

        $rslt_chk = [$request->p1_m3, $request->p2_m3];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_3', $find_results)) {
                unset($find_results['match_3']);
            }

            $find_results['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
            $find_results['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
            $find_results['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
            $find_results['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

            $find_results['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
            $find_results['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
            $find_results['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
            $find_results['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_3', $find_status)) {
                unset($find_status['match_3']);
            }

            $find_status['match_3'] = $request->rou_2_mat_3_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_3'] = $request->rou_2_mat_3_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m3_total > $p2_m3_total) {
            $round_two_winners_array['match_3'] = $request->p1_m3;
        } else {
           $round_two_winners_array['match_3'] = $request->p2_m3; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_3', $find_winners)) {
                unset($find_winners['match_3']);
            }

            if($p1_m3_total > $p2_m3_total) {
                $find_winners['match_3'] = $request->p1_m3;
            } else {
                $find_winners['match_3'] = $request->p2_m3; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m4_s1' => 'required',
            'p1_m4_s2' => 'required',
            'p2_m4_s1' => 'required',
            'p2_m4_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2) {

            $p1_m4_total = 2;
            $p2_m4_total = 0;

        } elseif ($request->p2_m4_s1 > $request->p1_m4_s1 && $request->p2_m4_s2 > $request->p1_m4_s2) {

            $p1_m4_total = 0;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        }


        $round_two_results_array['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
        $round_two_results_array['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
        $round_two_results_array['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
        $round_two_results_array['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

        $round_two_results_array['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
        $round_two_results_array['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
        $round_two_results_array['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
        $round_two_results_array['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 

        $rslt_chk = [$request->p1_m4, $request->p2_m4];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_4', $find_results)) {
                unset($find_results['match_4']);
            }

            $find_results['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
            $find_results['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
            $find_results['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
            $find_results['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

            $find_results['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
            $find_results['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
            $find_results['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
            $find_results['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_4', $find_status)) {
                unset($find_status['match_4']);
            }

            $find_status['match_4'] = $request->rou_2_mat_4_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_4'] = $request->rou_2_mat_4_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m4_total > $p2_m4_total) {
            $round_two_winners_array['match_4'] = $request->p1_m4;
        } else {
           $round_two_winners_array['match_4'] = $request->p2_m4; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_4', $find_winners)) {
                unset($find_winners['match_4']);
            }

            if($p1_m4_total > $p2_m4_total) {
                $find_winners['match_4'] = $request->p1_m4;
            } else {
                $find_winners['match_4'] = $request->p2_m4; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_five(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m5_s1' => 'required',
            'p1_m5_s2' => 'required',
            'p2_m5_s1' => 'required',
            'p2_m5_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2) {

            $p1_m5_total = 2;
            $p2_m5_total = 0;

        } elseif ($request->p2_m5_s1 > $request->p1_m5_s1 && $request->p2_m5_s2 > $request->p1_m5_s2) {

            $p1_m5_total = 0;
            $p2_m5_total = 2;

        } elseif ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 < $request->p2_m5_s2 && $request->p1_m5_s3 > $request->p2_m5_s3) {

            $p1_m5_total = 2;
            $p2_m5_total = 1;

        } elseif ($request->p1_m5_s1 < $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2 && $request->p1_m5_s3 < $request->p2_m5_s3) {

            $p1_m5_total = 1;
            $p2_m5_total = 2;

        } elseif ($request->p1_m5_s1 < $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2 && $request->p1_m5_s3 > $request->p2_m5_s3) {

            $p1_m5_total = 2;
            $p2_m5_total = 1;

        } elseif ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 < $request->p2_m5_s2 && $request->p1_m5_s3 < $request->p2_m5_s3) {

            $p1_m5_total = 1;
            $p2_m5_total = 2;

        }


        $round_two_results_array['match_5'][$request->p1_m5]['set_1'] = $request->p1_m5_s1;
        $round_two_results_array['match_5'][$request->p1_m5]['set_2'] = $request->p1_m5_s2;
        $round_two_results_array['match_5'][$request->p1_m5]['set_3'] = $request->p1_m5_s3;
        $round_two_results_array['match_5'][$request->p1_m5]['total'] = $p1_m5_total;

        $round_two_results_array['match_5'][$request->p2_m5]['set_1'] = $request->p2_m5_s1;       
        $round_two_results_array['match_5'][$request->p2_m5]['set_2'] = $request->p2_m5_s2;
        $round_two_results_array['match_5'][$request->p2_m5]['set_3'] = $request->p2_m5_s3;
        $round_two_results_array['match_5'][$request->p2_m5]['total'] = $p2_m5_total; 

        $rslt_chk = [$request->p1_m5, $request->p2_m5];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_5', $find_results)) {
                unset($find_results['match_5']);
            }

            $find_results['match_5'][$request->p1_m5]['set_1'] = $request->p1_m5_s1;
            $find_results['match_5'][$request->p1_m5]['set_2'] = $request->p1_m5_s2;
            $find_results['match_5'][$request->p1_m5]['set_3'] = $request->p1_m5_s3;
            $find_results['match_5'][$request->p1_m5]['total'] = $p1_m5_total;

            $find_results['match_5'][$request->p2_m5]['set_1'] = $request->p2_m5_s1;       
            $find_results['match_5'][$request->p2_m5]['set_2'] = $request->p2_m5_s2;
            $find_results['match_5'][$request->p2_m5]['set_3'] = $request->p2_m5_s3;
            $find_results['match_5'][$request->p2_m5]['total'] = $p2_m5_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_5', $find_status)) {
                unset($find_status['match_5']);
            }

            $find_status['match_5'] = $request->rou_2_mat_5_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_5'] = $request->rou_2_mat_5_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m5_total > $p2_m5_total) {
            $round_two_winners_array['match_5'] = $request->p1_m5;
        } else {
           $round_two_winners_array['match_5'] = $request->p2_m5; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_5', $find_winners)) {
                unset($find_winners['match_5']);
            }

            if($p1_m5_total > $p2_m5_total) {
                $find_winners['match_5'] = $request->p1_m5;
            } else {
                $find_winners['match_5'] = $request->p2_m5; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_six(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m6_s1' => 'required',
            'p1_m6_s2' => 'required',
            'p2_m6_s1' => 'required',
            'p2_m6_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2) {

            $p1_m6_total = 2;
            $p2_m6_total = 0;

        } elseif ($request->p2_m6_s1 > $request->p1_m6_s1 && $request->p2_m6_s2 > $request->p1_m6_s2) {

            $p1_m6_total = 0;
            $p2_m6_total = 2;

        } elseif ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 < $request->p2_m6_s2 && $request->p1_m6_s3 > $request->p2_m6_s3) {

            $p1_m6_total = 2;
            $p2_m6_total = 1;

        } elseif ($request->p1_m6_s1 < $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2 && $request->p1_m6_s3 < $request->p2_m6_s3) {

            $p1_m6_total = 1;
            $p2_m6_total = 2;

        } elseif ($request->p1_m6_s1 < $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2 && $request->p1_m6_s3 > $request->p2_m6_s3) {

            $p1_m6_total = 2;
            $p2_m6_total = 1;

        } elseif ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 < $request->p2_m6_s2 && $request->p1_m6_s3 < $request->p2_m6_s3) {

            $p1_m6_total = 1;
            $p2_m6_total = 2;

        }


        $round_two_results_array['match_6'][$request->p1_m6]['set_1'] = $request->p1_m6_s1;
        $round_two_results_array['match_6'][$request->p1_m6]['set_2'] = $request->p1_m6_s2;
        $round_two_results_array['match_6'][$request->p1_m6]['set_3'] = $request->p1_m6_s3;
        $round_two_results_array['match_6'][$request->p1_m6]['total'] = $p1_m6_total;

        $round_two_results_array['match_6'][$request->p2_m6]['set_1'] = $request->p2_m6_s1;       
        $round_two_results_array['match_6'][$request->p2_m6]['set_2'] = $request->p2_m6_s2;
        $round_two_results_array['match_6'][$request->p2_m6]['set_3'] = $request->p2_m6_s3;
        $round_two_results_array['match_6'][$request->p2_m6]['total'] = $p2_m6_total; 

        $rslt_chk = [$request->p1_m6, $request->p2_m6];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_6', $find_results)) {
                unset($find_results['match_6']);
            }

            $find_results['match_6'][$request->p1_m6]['set_1'] = $request->p1_m6_s1;
            $find_results['match_6'][$request->p1_m6]['set_2'] = $request->p1_m6_s2;
            $find_results['match_6'][$request->p1_m6]['set_3'] = $request->p1_m6_s3;
            $find_results['match_6'][$request->p1_m6]['total'] = $p1_m6_total;

            $find_results['match_6'][$request->p2_m6]['set_1'] = $request->p2_m6_s1;       
            $find_results['match_6'][$request->p2_m6]['set_2'] = $request->p2_m6_s2;
            $find_results['match_6'][$request->p2_m6]['set_3'] = $request->p2_m6_s3;
            $find_results['match_6'][$request->p2_m6]['total'] = $p2_m6_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_6', $find_status)) {
                unset($find_status['match_6']);
            }

            $find_status['match_6'] = $request->rou_2_mat_6_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_6'] = $request->rou_2_mat_6_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m6_total > $p2_m6_total) {
            $round_two_winners_array['match_6'] = $request->p1_m6;
        } else {
           $round_two_winners_array['match_6'] = $request->p2_m6; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_6', $find_winners)) {
                unset($find_winners['match_6']);
            }

            if($p1_m6_total > $p2_m6_total) {
                $find_winners['match_6'] = $request->p1_m6;
            } else {
                $find_winners['match_6'] = $request->p2_m6; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_seven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m7_s1' => 'required',
            'p1_m7_s2' => 'required',
            'p2_m7_s1' => 'required',
            'p2_m7_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2) {

            $p1_m7_total = 2;
            $p2_m7_total = 0;

        } elseif ($request->p2_m7_s1 > $request->p1_m7_s1 && $request->p2_m7_s2 > $request->p1_m7_s2) {

            $p1_m7_total = 0;
            $p2_m7_total = 2;

        } elseif ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 < $request->p2_m7_s2 && $request->p1_m7_s3 > $request->p2_m7_s3) {

            $p1_m7_total = 2;
            $p2_m7_total = 1;

        } elseif ($request->p1_m7_s1 < $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2 && $request->p1_m7_s3 < $request->p2_m7_s3) {

            $p1_m7_total = 1;
            $p2_m7_total = 2;

        } elseif ($request->p1_m7_s1 < $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2 && $request->p1_m7_s3 > $request->p2_m7_s3) {

            $p1_m7_total = 2;
            $p2_m7_total = 1;

        } elseif ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 < $request->p2_m7_s2 && $request->p1_m7_s3 < $request->p2_m7_s3) {

            $p1_m7_total = 1;
            $p2_m7_total = 2;

        }


        $round_two_results_array['match_7'][$request->p1_m7]['set_1'] = $request->p1_m7_s1;
        $round_two_results_array['match_7'][$request->p1_m7]['set_2'] = $request->p1_m7_s2;
        $round_two_results_array['match_7'][$request->p1_m7]['set_3'] = $request->p1_m7_s3;
        $round_two_results_array['match_7'][$request->p1_m7]['total'] = $p1_m7_total;

        $round_two_results_array['match_7'][$request->p2_m7]['set_1'] = $request->p2_m7_s1;       
        $round_two_results_array['match_7'][$request->p2_m7]['set_2'] = $request->p2_m7_s2;
        $round_two_results_array['match_7'][$request->p2_m7]['set_3'] = $request->p2_m7_s3;
        $round_two_results_array['match_7'][$request->p2_m7]['total'] = $p2_m7_total; 

        $rslt_chk = [$request->p1_m7, $request->p2_m7];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_7', $find_results)) {
                unset($find_results['match_7']);
            }

            $find_results['match_7'][$request->p1_m7]['set_1'] = $request->p1_m7_s1;
            $find_results['match_7'][$request->p1_m7]['set_2'] = $request->p1_m7_s2;
            $find_results['match_7'][$request->p1_m7]['set_3'] = $request->p1_m7_s3;
            $find_results['match_7'][$request->p1_m7]['total'] = $p1_m7_total;

            $find_results['match_7'][$request->p2_m7]['set_1'] = $request->p2_m7_s1;       
            $find_results['match_7'][$request->p2_m7]['set_2'] = $request->p2_m7_s2;
            $find_results['match_7'][$request->p2_m7]['set_3'] = $request->p2_m7_s3;
            $find_results['match_7'][$request->p2_m7]['total'] = $p2_m7_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_7', $find_status)) {
                unset($find_status['match_7']);
            }

            $find_status['match_7'] = $request->rou_2_mat_7_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_7'] = $request->rou_2_mat_7_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m7_total > $p2_m7_total) {
            $round_two_winners_array['match_7'] = $request->p1_m7;
        } else {
           $round_two_winners_array['match_7'] = $request->p2_m7; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_7', $find_winners)) {
                unset($find_winners['match_7']);
            }

            if($p1_m7_total > $p2_m7_total) {
                $find_winners['match_7'] = $request->p1_m7;
            } else {
                $find_winners['match_7'] = $request->p2_m7; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_eight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m8_s1' => 'required',
            'p1_m8_s2' => 'required',
            'p2_m8_s1' => 'required',
            'p2_m8_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2) {

            $p1_m8_total = 2;
            $p2_m8_total = 0;

        } elseif ($request->p2_m8_s1 > $request->p1_m8_s1 && $request->p2_m8_s2 > $request->p1_m8_s2) {

            $p1_m8_total = 0;
            $p2_m8_total = 2;

        } elseif ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 < $request->p2_m8_s2 && $request->p1_m8_s3 > $request->p2_m8_s3) {

            $p1_m8_total = 2;
            $p2_m8_total = 1;

        } elseif ($request->p1_m8_s1 < $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2 && $request->p1_m8_s3 < $request->p2_m8_s3) {

            $p1_m8_total = 1;
            $p2_m8_total = 2;

        } elseif ($request->p1_m8_s1 < $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2 && $request->p1_m8_s3 > $request->p2_m8_s3) {

            $p1_m8_total = 2;
            $p2_m8_total = 1;

        } elseif ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 < $request->p2_m8_s2 && $request->p1_m8_s3 < $request->p2_m8_s3) {

            $p1_m8_total = 1;
            $p2_m8_total = 2;

        }


        $round_two_results_array['match_8'][$request->p1_m8]['set_1'] = $request->p1_m8_s1;
        $round_two_results_array['match_8'][$request->p1_m8]['set_2'] = $request->p1_m8_s2;
        $round_two_results_array['match_8'][$request->p1_m8]['set_3'] = $request->p1_m8_s3;
        $round_two_results_array['match_8'][$request->p1_m8]['total'] = $p1_m8_total;

        $round_two_results_array['match_8'][$request->p2_m8]['set_1'] = $request->p2_m8_s1;       
        $round_two_results_array['match_8'][$request->p2_m8]['set_2'] = $request->p2_m8_s2;
        $round_two_results_array['match_8'][$request->p2_m8]['set_3'] = $request->p2_m8_s3;
        $round_two_results_array['match_8'][$request->p2_m8]['total'] = $p2_m8_total; 

        $rslt_chk = [$request->p1_m8, $request->p2_m8];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_8', $find_results)) {
                unset($find_results['match_8']);
            }

            $find_results['match_8'][$request->p1_m8]['set_1'] = $request->p1_m8_s1;
            $find_results['match_8'][$request->p1_m8]['set_2'] = $request->p1_m8_s2;
            $find_results['match_8'][$request->p1_m8]['set_3'] = $request->p1_m8_s3;
            $find_results['match_8'][$request->p1_m8]['total'] = $p1_m8_total;

            $find_results['match_8'][$request->p2_m8]['set_1'] = $request->p2_m8_s1;       
            $find_results['match_8'][$request->p2_m8]['set_2'] = $request->p2_m8_s2;
            $find_results['match_8'][$request->p2_m8]['set_3'] = $request->p2_m8_s3;
            $find_results['match_8'][$request->p2_m8]['total'] = $p2_m8_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_8', $find_status)) {
                unset($find_status['match_8']);
            }

            $find_status['match_8'] = $request->rou_2_mat_8_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_8'] = $request->rou_2_mat_8_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m8_total > $p2_m8_total) {
            $round_two_winners_array['match_8'] = $request->p1_m8;
        } else {
           $round_two_winners_array['match_8'] = $request->p2_m8; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_8', $find_winners)) {
                unset($find_winners['match_8']);
            }

            if($p1_m8_total > $p2_m8_total) {
                $find_winners['match_8'] = $request->p1_m8;
            } else {
                $find_winners['match_8'] = $request->p2_m8; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_nine(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m9_s1' => 'required',
            'p1_m9_s2' => 'required',
            'p2_m9_s1' => 'required',
            'p2_m9_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m9_s1 > $request->p2_m9_s1 && $request->p1_m9_s2 > $request->p2_m9_s2) {

            $p1_m9_total = 2;
            $p2_m9_total = 0;

        } elseif ($request->p2_m9_s1 > $request->p1_m9_s1 && $request->p2_m9_s2 > $request->p1_m9_s2) {

            $p1_m9_total = 0;
            $p2_m9_total = 2;

        } elseif ($request->p1_m9_s1 > $request->p2_m9_s1 && $request->p1_m9_s2 < $request->p2_m9_s2 && $request->p1_m9_s3 > $request->p2_m9_s3) {

            $p1_m9_total = 2;
            $p2_m9_total = 1;

        } elseif ($request->p1_m9_s1 < $request->p2_m9_s1 && $request->p1_m9_s2 > $request->p2_m9_s2 && $request->p1_m9_s3 < $request->p2_m9_s3) {

            $p1_m9_total = 1;
            $p2_m9_total = 2;

        } elseif ($request->p1_m9_s1 < $request->p2_m9_s1 && $request->p1_m9_s2 > $request->p2_m9_s2 && $request->p1_m9_s3 > $request->p2_m9_s3) {

            $p1_m9_total = 2;
            $p2_m9_total = 1;

        } elseif ($request->p1_m9_s1 > $request->p2_m9_s1 && $request->p1_m9_s2 < $request->p2_m9_s2 && $request->p1_m9_s3 < $request->p2_m9_s3) {

            $p1_m9_total = 1;
            $p2_m9_total = 2;

        }


        $round_two_results_array['match_9'][$request->p1_m9]['set_1'] = $request->p1_m9_s1;
        $round_two_results_array['match_9'][$request->p1_m9]['set_2'] = $request->p1_m9_s2;
        $round_two_results_array['match_9'][$request->p1_m9]['set_3'] = $request->p1_m9_s3;
        $round_two_results_array['match_9'][$request->p1_m9]['total'] = $p1_m9_total;

        $round_two_results_array['match_9'][$request->p2_m9]['set_1'] = $request->p2_m9_s1;       
        $round_two_results_array['match_9'][$request->p2_m9]['set_2'] = $request->p2_m9_s2;
        $round_two_results_array['match_9'][$request->p2_m9]['set_3'] = $request->p2_m9_s3;
        $round_two_results_array['match_9'][$request->p2_m9]['total'] = $p2_m9_total; 

        $rslt_chk = [$request->p1_m9, $request->p2_m9];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_9', $find_results)) {
                unset($find_results['match_9']);
            }

            $find_results['match_9'][$request->p1_m9]['set_1'] = $request->p1_m9_s1;
            $find_results['match_9'][$request->p1_m9]['set_2'] = $request->p1_m9_s2;
            $find_results['match_9'][$request->p1_m9]['set_3'] = $request->p1_m9_s3;
            $find_results['match_9'][$request->p1_m9]['total'] = $p1_m9_total;

            $find_results['match_9'][$request->p2_m9]['set_1'] = $request->p2_m9_s1;       
            $find_results['match_9'][$request->p2_m9]['set_2'] = $request->p2_m9_s2;
            $find_results['match_9'][$request->p2_m9]['set_3'] = $request->p2_m9_s3;
            $find_results['match_9'][$request->p2_m9]['total'] = $p2_m9_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_9', $find_status)) {
                unset($find_status['match_9']);
            }

            $find_status['match_9'] = $request->rou_2_mat_9_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_9'] = $request->rou_2_mat_9_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m9_total > $p2_m9_total) {
            $round_two_winners_array['match_9'] = $request->p1_m9;
        } else {
           $round_two_winners_array['match_9'] = $request->p2_m9; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_9', $find_winners)) {
                unset($find_winners['match_9']);
            }

            if($p1_m9_total > $p2_m9_total) {
                $find_winners['match_9'] = $request->p1_m9;
            } else {
                $find_winners['match_9'] = $request->p2_m9; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_ten(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m10_s1' => 'required',
            'p1_m10_s2' => 'required',
            'p2_m10_s1' => 'required',
            'p2_m10_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m10_s1 > $request->p2_m10_s1 && $request->p1_m10_s2 > $request->p2_m10_s2) {

            $p1_m10_total = 2;
            $p2_m10_total = 0;

        } elseif ($request->p2_m10_s1 > $request->p1_m10_s1 && $request->p2_m10_s2 > $request->p1_m10_s2) {

            $p1_m10_total = 0;
            $p2_m10_total = 2;

        } elseif ($request->p1_m10_s1 > $request->p2_m10_s1 && $request->p1_m10_s2 < $request->p2_m10_s2 && $request->p1_m10_s3 > $request->p2_m10_s3) {

            $p1_m10_total = 2;
            $p2_m10_total = 1;

        } elseif ($request->p1_m10_s1 < $request->p2_m10_s1 && $request->p1_m10_s2 > $request->p2_m10_s2 && $request->p1_m10_s3 < $request->p2_m10_s3) {

            $p1_m10_total = 1;
            $p2_m10_total = 2;

        } elseif ($request->p1_m10_s1 < $request->p2_m10_s1 && $request->p1_m10_s2 > $request->p2_m10_s2 && $request->p1_m10_s3 > $request->p2_m10_s3) {

            $p1_m10_total = 2;
            $p2_m10_total = 1;

        } elseif ($request->p1_m10_s1 > $request->p2_m10_s1 && $request->p1_m10_s2 < $request->p2_m10_s2 && $request->p1_m10_s3 < $request->p2_m10_s3) {

            $p1_m10_total = 1;
            $p2_m10_total = 2;

        }


        $round_two_results_array['match_10'][$request->p1_m10]['set_1'] = $request->p1_m10_s1;
        $round_two_results_array['match_10'][$request->p1_m10]['set_2'] = $request->p1_m10_s2;
        $round_two_results_array['match_10'][$request->p1_m10]['set_3'] = $request->p1_m10_s3;
        $round_two_results_array['match_10'][$request->p1_m10]['total'] = $p1_m10_total;

        $round_two_results_array['match_10'][$request->p2_m10]['set_1'] = $request->p2_m10_s1;       
        $round_two_results_array['match_10'][$request->p2_m10]['set_2'] = $request->p2_m10_s2;
        $round_two_results_array['match_10'][$request->p2_m10]['set_3'] = $request->p2_m10_s3;
        $round_two_results_array['match_10'][$request->p2_m10]['total'] = $p2_m10_total; 

        $rslt_chk = [$request->p1_m10, $request->p2_m10];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_10', $find_results)) {
                unset($find_results['match_10']);
            }

            $find_results['match_10'][$request->p1_m10]['set_1'] = $request->p1_m10_s1;
            $find_results['match_10'][$request->p1_m10]['set_2'] = $request->p1_m10_s2;
            $find_results['match_10'][$request->p1_m10]['set_3'] = $request->p1_m10_s3;
            $find_results['match_10'][$request->p1_m10]['total'] = $p1_m10_total;

            $find_results['match_10'][$request->p2_m10]['set_1'] = $request->p2_m10_s1;       
            $find_results['match_10'][$request->p2_m10]['set_2'] = $request->p2_m10_s2;
            $find_results['match_10'][$request->p2_m10]['set_3'] = $request->p2_m10_s3;
            $find_results['match_10'][$request->p2_m10]['total'] = $p2_m10_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_10', $find_status)) {
                unset($find_status['match_10']);
            }

            $find_status['match_10'] = $request->rou_2_mat_10_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_10'] = $request->rou_2_mat_10_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m10_total > $p2_m10_total) {
            $round_two_winners_array['match_10'] = $request->p1_m10;
        } else {
           $round_two_winners_array['match_10'] = $request->p2_m10; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_10', $find_winners)) {
                unset($find_winners['match_10']);
            }

            if($p1_m10_total > $p2_m10_total) {
                $find_winners['match_10'] = $request->p1_m10;
            } else {
                $find_winners['match_10'] = $request->p2_m10; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_eleven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m11_s1' => 'required',
            'p1_m11_s2' => 'required',
            'p2_m11_s1' => 'required',
            'p2_m11_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m11_s1 > $request->p2_m11_s1 && $request->p1_m11_s2 > $request->p2_m11_s2) {

            $p1_m11_total = 2;
            $p2_m11_total = 0;

        } elseif ($request->p2_m11_s1 > $request->p1_m11_s1 && $request->p2_m11_s2 > $request->p1_m11_s2) {

            $p1_m11_total = 0;
            $p2_m11_total = 2;

        } elseif ($request->p1_m11_s1 > $request->p2_m11_s1 && $request->p1_m11_s2 < $request->p2_m11_s2 && $request->p1_m11_s3 > $request->p2_m11_s3) {

            $p1_m11_total = 2;
            $p2_m11_total = 1;

        } elseif ($request->p1_m11_s1 < $request->p2_m11_s1 && $request->p1_m11_s2 > $request->p2_m11_s2 && $request->p1_m11_s3 < $request->p2_m11_s3) {

            $p1_m11_total = 1;
            $p2_m11_total = 2;

        } elseif ($request->p1_m11_s1 < $request->p2_m11_s1 && $request->p1_m11_s2 > $request->p2_m11_s2 && $request->p1_m11_s3 > $request->p2_m11_s3) {

            $p1_m11_total = 2;
            $p2_m11_total = 1;

        } elseif ($request->p1_m11_s1 > $request->p2_m11_s1 && $request->p1_m11_s2 < $request->p2_m11_s2 && $request->p1_m11_s3 < $request->p2_m11_s3) {

            $p1_m11_total = 1;
            $p2_m11_total = 2;

        }


        $round_two_results_array['match_11'][$request->p1_m11]['set_1'] = $request->p1_m11_s1;
        $round_two_results_array['match_11'][$request->p1_m11]['set_2'] = $request->p1_m11_s2;
        $round_two_results_array['match_11'][$request->p1_m11]['set_3'] = $request->p1_m11_s3;
        $round_two_results_array['match_11'][$request->p1_m11]['total'] = $p1_m11_total;

        $round_two_results_array['match_11'][$request->p2_m11]['set_1'] = $request->p2_m11_s1;       
        $round_two_results_array['match_11'][$request->p2_m11]['set_2'] = $request->p2_m11_s2;
        $round_two_results_array['match_11'][$request->p2_m11]['set_3'] = $request->p2_m11_s3;
        $round_two_results_array['match_11'][$request->p2_m11]['total'] = $p2_m11_total; 

        $rslt_chk = [$request->p1_m11, $request->p2_m11];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_11', $find_results)) {
                unset($find_results['match_11']);
            }

            $find_results['match_11'][$request->p1_m11]['set_1'] = $request->p1_m11_s1;
            $find_results['match_11'][$request->p1_m11]['set_2'] = $request->p1_m11_s2;
            $find_results['match_11'][$request->p1_m11]['set_3'] = $request->p1_m11_s3;
            $find_results['match_11'][$request->p1_m11]['total'] = $p1_m11_total;

            $find_results['match_11'][$request->p2_m11]['set_1'] = $request->p2_m11_s1;       
            $find_results['match_11'][$request->p2_m11]['set_2'] = $request->p2_m11_s2;
            $find_results['match_11'][$request->p2_m11]['set_3'] = $request->p2_m11_s3;
            $find_results['match_11'][$request->p2_m11]['total'] = $p2_m11_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_11', $find_status)) {
                unset($find_status['match_11']);
            }

            $find_status['match_11'] = $request->rou_2_mat_11_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_11'] = $request->rou_2_mat_11_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m11_total > $p2_m11_total) {
            $round_two_winners_array['match_11'] = $request->p1_m11;
        } else {
           $round_two_winners_array['match_11'] = $request->p2_m11; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_11', $find_winners)) {
                unset($find_winners['match_11']);
            }

            if($p1_m11_total > $p2_m11_total) {
                $find_winners['match_11'] = $request->p1_m11;
            } else {
                $find_winners['match_11'] = $request->p2_m11; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_twelve(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m12_s1' => 'required',
            'p1_m12_s2' => 'required',
            'p2_m12_s1' => 'required',
            'p2_m12_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m12_s1 > $request->p2_m12_s1 && $request->p1_m12_s2 > $request->p2_m12_s2) {

            $p1_m12_total = 2;
            $p2_m12_total = 0;

        } elseif ($request->p2_m12_s1 > $request->p1_m12_s1 && $request->p2_m12_s2 > $request->p1_m12_s2) {

            $p1_m12_total = 0;
            $p2_m12_total = 2;

        } elseif ($request->p1_m12_s1 > $request->p2_m12_s1 && $request->p1_m12_s2 < $request->p2_m12_s2 && $request->p1_m12_s3 > $request->p2_m12_s3) {

            $p1_m12_total = 2;
            $p2_m12_total = 1;

        } elseif ($request->p1_m12_s1 < $request->p2_m12_s1 && $request->p1_m12_s2 > $request->p2_m12_s2 && $request->p1_m12_s3 < $request->p2_m12_s3) {

            $p1_m12_total = 1;
            $p2_m12_total = 2;

        } elseif ($request->p1_m12_s1 < $request->p2_m12_s1 && $request->p1_m12_s2 > $request->p2_m12_s2 && $request->p1_m12_s3 > $request->p2_m12_s3) {

            $p1_m12_total = 2;
            $p2_m12_total = 1;

        } elseif ($request->p1_m12_s1 > $request->p2_m12_s1 && $request->p1_m12_s2 < $request->p2_m12_s2 && $request->p1_m12_s3 < $request->p2_m12_s3) {

            $p1_m12_total = 1;
            $p2_m12_total = 2;

        }


        $round_two_results_array['match_12'][$request->p1_m12]['set_1'] = $request->p1_m12_s1;
        $round_two_results_array['match_12'][$request->p1_m12]['set_2'] = $request->p1_m12_s2;
        $round_two_results_array['match_12'][$request->p1_m12]['set_3'] = $request->p1_m12_s3;
        $round_two_results_array['match_12'][$request->p1_m12]['total'] = $p1_m12_total;

        $round_two_results_array['match_12'][$request->p2_m12]['set_1'] = $request->p2_m12_s1;       
        $round_two_results_array['match_12'][$request->p2_m12]['set_2'] = $request->p2_m12_s2;
        $round_two_results_array['match_12'][$request->p2_m12]['set_3'] = $request->p2_m12_s3;
        $round_two_results_array['match_12'][$request->p2_m12]['total'] = $p2_m12_total; 

        $rslt_chk = [$request->p1_m12, $request->p2_m12];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_12', $find_results)) {
                unset($find_results['match_12']);
            }

            $find_results['match_12'][$request->p1_m12]['set_1'] = $request->p1_m12_s1;
            $find_results['match_12'][$request->p1_m12]['set_2'] = $request->p1_m12_s2;
            $find_results['match_12'][$request->p1_m12]['set_3'] = $request->p1_m12_s3;
            $find_results['match_12'][$request->p1_m12]['total'] = $p1_m12_total;

            $find_results['match_12'][$request->p2_m12]['set_1'] = $request->p2_m12_s1;       
            $find_results['match_12'][$request->p2_m12]['set_2'] = $request->p2_m12_s2;
            $find_results['match_12'][$request->p2_m12]['set_3'] = $request->p2_m12_s3;
            $find_results['match_12'][$request->p2_m12]['total'] = $p2_m12_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_12', $find_status)) {
                unset($find_status['match_12']);
            }

            $find_status['match_12'] = $request->rou_2_mat_12_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_12'] = $request->rou_2_mat_12_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m12_total > $p2_m12_total) {
            $round_two_winners_array['match_12'] = $request->p1_m12;
        } else {
           $round_two_winners_array['match_12'] = $request->p2_m12; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_12', $find_winners)) {
                unset($find_winners['match_12']);
            }

            if($p1_m12_total > $p2_m12_total) {
                $find_winners['match_12'] = $request->p1_m12;
            } else {
                $find_winners['match_12'] = $request->p2_m12; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_thirteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m13_s1' => 'required',
            'p1_m13_s2' => 'required',
            'p2_m13_s1' => 'required',
            'p2_m13_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m13_s1 > $request->p2_m13_s1 && $request->p1_m13_s2 > $request->p2_m13_s2) {

            $p1_m13_total = 2;
            $p2_m13_total = 0;

        } elseif ($request->p2_m13_s1 > $request->p1_m13_s1 && $request->p2_m13_s2 > $request->p1_m13_s2) {

            $p1_m13_total = 0;
            $p2_m13_total = 2;

        } elseif ($request->p1_m13_s1 > $request->p2_m13_s1 && $request->p1_m13_s2 < $request->p2_m13_s2 && $request->p1_m13_s3 > $request->p2_m13_s3) {

            $p1_m13_total = 2;
            $p2_m13_total = 1;

        } elseif ($request->p1_m13_s1 < $request->p2_m13_s1 && $request->p1_m13_s2 > $request->p2_m13_s2 && $request->p1_m13_s3 < $request->p2_m13_s3) {

            $p1_m13_total = 1;
            $p2_m13_total = 2;

        } elseif ($request->p1_m13_s1 < $request->p2_m13_s1 && $request->p1_m13_s2 > $request->p2_m13_s2 && $request->p1_m13_s3 > $request->p2_m13_s3) {

            $p1_m13_total = 2;
            $p2_m13_total = 1;

        } elseif ($request->p1_m13_s1 > $request->p2_m13_s1 && $request->p1_m13_s2 < $request->p2_m13_s2 && $request->p1_m13_s3 < $request->p2_m13_s3) {

            $p1_m13_total = 1;
            $p2_m13_total = 2;

        }


        $round_two_results_array['match_13'][$request->p1_m13]['set_1'] = $request->p1_m13_s1;
        $round_two_results_array['match_13'][$request->p1_m13]['set_2'] = $request->p1_m13_s2;
        $round_two_results_array['match_13'][$request->p1_m13]['set_3'] = $request->p1_m13_s3;
        $round_two_results_array['match_13'][$request->p1_m13]['total'] = $p1_m13_total;

        $round_two_results_array['match_13'][$request->p2_m13]['set_1'] = $request->p2_m13_s1;       
        $round_two_results_array['match_13'][$request->p2_m13]['set_2'] = $request->p2_m13_s2;
        $round_two_results_array['match_13'][$request->p2_m13]['set_3'] = $request->p2_m13_s3;
        $round_two_results_array['match_13'][$request->p2_m13]['total'] = $p2_m13_total; 

        $rslt_chk = [$request->p1_m13, $request->p2_m13];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_13', $find_results)) {
                unset($find_results['match_13']);
            }

            $find_results['match_13'][$request->p1_m13]['set_1'] = $request->p1_m13_s1;
            $find_results['match_13'][$request->p1_m13]['set_2'] = $request->p1_m13_s2;
            $find_results['match_13'][$request->p1_m13]['set_3'] = $request->p1_m13_s3;
            $find_results['match_13'][$request->p1_m13]['total'] = $p1_m13_total;

            $find_results['match_13'][$request->p2_m13]['set_1'] = $request->p2_m13_s1;       
            $find_results['match_13'][$request->p2_m13]['set_2'] = $request->p2_m13_s2;
            $find_results['match_13'][$request->p2_m13]['set_3'] = $request->p2_m13_s3;
            $find_results['match_13'][$request->p2_m13]['total'] = $p2_m13_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_13', $find_status)) {
                unset($find_status['match_13']);
            }

            $find_status['match_13'] = $request->rou_2_mat_13_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_13'] = $request->rou_2_mat_13_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m13_total > $p2_m13_total) {
            $round_two_winners_array['match_13'] = $request->p1_m13;
        } else {
           $round_two_winners_array['match_13'] = $request->p2_m13; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_13', $find_winners)) {
                unset($find_winners['match_13']);
            }

            if($p1_m13_total > $p2_m13_total) {
                $find_winners['match_13'] = $request->p1_m13;
            } else {
                $find_winners['match_13'] = $request->p2_m13; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_fourteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m14_s1' => 'required',
            'p1_m14_s2' => 'required',
            'p2_m14_s1' => 'required',
            'p2_m14_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m14_s1 > $request->p2_m14_s1 && $request->p1_m14_s2 > $request->p2_m14_s2) {

            $p1_m14_total = 2;
            $p2_m14_total = 0;

        } elseif ($request->p2_m14_s1 > $request->p1_m14_s1 && $request->p2_m14_s2 > $request->p1_m14_s2) {

            $p1_m14_total = 0;
            $p2_m14_total = 2;

        } elseif ($request->p1_m14_s1 > $request->p2_m14_s1 && $request->p1_m14_s2 < $request->p2_m14_s2 && $request->p1_m14_s3 > $request->p2_m14_s3) {

            $p1_m14_total = 2;
            $p2_m14_total = 1;

        } elseif ($request->p1_m14_s1 < $request->p2_m14_s1 && $request->p1_m14_s2 > $request->p2_m14_s2 && $request->p1_m14_s3 < $request->p2_m14_s3) {

            $p1_m14_total = 1;
            $p2_m14_total = 2;

        } elseif ($request->p1_m14_s1 < $request->p2_m14_s1 && $request->p1_m14_s2 > $request->p2_m14_s2 && $request->p1_m14_s3 > $request->p2_m14_s3) {

            $p1_m14_total = 2;
            $p2_m14_total = 1;

        } elseif ($request->p1_m14_s1 > $request->p2_m14_s1 && $request->p1_m14_s2 < $request->p2_m14_s2 && $request->p1_m14_s3 < $request->p2_m14_s3) {

            $p1_m14_total = 1;
            $p2_m14_total = 2;

        }


        $round_two_results_array['match_14'][$request->p1_m14]['set_1'] = $request->p1_m14_s1;
        $round_two_results_array['match_14'][$request->p1_m14]['set_2'] = $request->p1_m14_s2;
        $round_two_results_array['match_14'][$request->p1_m14]['set_3'] = $request->p1_m14_s3;
        $round_two_results_array['match_14'][$request->p1_m14]['total'] = $p1_m14_total;

        $round_two_results_array['match_14'][$request->p2_m14]['set_1'] = $request->p2_m14_s1;       
        $round_two_results_array['match_14'][$request->p2_m14]['set_2'] = $request->p2_m14_s2;
        $round_two_results_array['match_14'][$request->p2_m14]['set_3'] = $request->p2_m14_s3;
        $round_two_results_array['match_14'][$request->p2_m14]['total'] = $p2_m14_total; 

        $rslt_chk = [$request->p1_m14, $request->p2_m14];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_14', $find_results)) {
                unset($find_results['match_14']);
            }

            $find_results['match_14'][$request->p1_m14]['set_1'] = $request->p1_m14_s1;
            $find_results['match_14'][$request->p1_m14]['set_2'] = $request->p1_m14_s2;
            $find_results['match_14'][$request->p1_m14]['set_3'] = $request->p1_m14_s3;
            $find_results['match_14'][$request->p1_m14]['total'] = $p1_m14_total;

            $find_results['match_14'][$request->p2_m14]['set_1'] = $request->p2_m14_s1;       
            $find_results['match_14'][$request->p2_m14]['set_2'] = $request->p2_m14_s2;
            $find_results['match_14'][$request->p2_m14]['set_3'] = $request->p2_m14_s3;
            $find_results['match_14'][$request->p2_m14]['total'] = $p2_m14_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_14', $find_status)) {
                unset($find_status['match_14']);
            }

            $find_status['match_14'] = $request->rou_2_mat_14_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_14'] = $request->rou_2_mat_14_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m14_total > $p2_m14_total) {
            $round_two_winners_array['match_14'] = $request->p1_m14;
        } else {
           $round_two_winners_array['match_14'] = $request->p2_m14; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_14', $find_winners)) {
                unset($find_winners['match_14']);
            }

            if($p1_m14_total > $p2_m14_total) {
                $find_winners['match_14'] = $request->p1_m14;
            } else {
                $find_winners['match_14'] = $request->p2_m14; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_fifteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m15_s1' => 'required',
            'p1_m15_s2' => 'required',
            'p2_m15_s1' => 'required',
            'p2_m15_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m15_s1 > $request->p2_m15_s1 && $request->p1_m15_s2 > $request->p2_m15_s2) {

            $p1_m15_total = 2;
            $p2_m15_total = 0;

        } elseif ($request->p2_m15_s1 > $request->p1_m15_s1 && $request->p2_m15_s2 > $request->p1_m15_s2) {

            $p1_m15_total = 0;
            $p2_m15_total = 2;

        } elseif ($request->p1_m15_s1 > $request->p2_m15_s1 && $request->p1_m15_s2 < $request->p2_m15_s2 && $request->p1_m15_s3 > $request->p2_m15_s3) {

            $p1_m15_total = 2;
            $p2_m15_total = 1;

        } elseif ($request->p1_m15_s1 < $request->p2_m15_s1 && $request->p1_m15_s2 > $request->p2_m15_s2 && $request->p1_m15_s3 < $request->p2_m15_s3) {

            $p1_m15_total = 1;
            $p2_m15_total = 2;

        } elseif ($request->p1_m15_s1 < $request->p2_m15_s1 && $request->p1_m15_s2 > $request->p2_m15_s2 && $request->p1_m15_s3 > $request->p2_m15_s3) {

            $p1_m15_total = 2;
            $p2_m15_total = 1;

        } elseif ($request->p1_m15_s1 > $request->p2_m15_s1 && $request->p1_m15_s2 < $request->p2_m15_s2 && $request->p1_m15_s3 < $request->p2_m15_s3) {

            $p1_m15_total = 1;
            $p2_m15_total = 2;

        }


        $round_two_results_array['match_15'][$request->p1_m15]['set_1'] = $request->p1_m15_s1;
        $round_two_results_array['match_15'][$request->p1_m15]['set_2'] = $request->p1_m15_s2;
        $round_two_results_array['match_15'][$request->p1_m15]['set_3'] = $request->p1_m15_s3;
        $round_two_results_array['match_15'][$request->p1_m15]['total'] = $p1_m15_total;

        $round_two_results_array['match_15'][$request->p2_m15]['set_1'] = $request->p2_m15_s1;       
        $round_two_results_array['match_15'][$request->p2_m15]['set_2'] = $request->p2_m15_s2;
        $round_two_results_array['match_15'][$request->p2_m15]['set_3'] = $request->p2_m15_s3;
        $round_two_results_array['match_15'][$request->p2_m15]['total'] = $p2_m15_total; 

        $rslt_chk = [$request->p1_m15, $request->p2_m15];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_15', $find_results)) {
                unset($find_results['match_15']);
            }

            $find_results['match_15'][$request->p1_m15]['set_1'] = $request->p1_m15_s1;
            $find_results['match_15'][$request->p1_m15]['set_2'] = $request->p1_m15_s2;
            $find_results['match_15'][$request->p1_m15]['set_3'] = $request->p1_m15_s3;
            $find_results['match_15'][$request->p1_m15]['total'] = $p1_m15_total;

            $find_results['match_15'][$request->p2_m15]['set_1'] = $request->p2_m15_s1;       
            $find_results['match_15'][$request->p2_m15]['set_2'] = $request->p2_m15_s2;
            $find_results['match_15'][$request->p2_m15]['set_3'] = $request->p2_m15_s3;
            $find_results['match_15'][$request->p2_m15]['total'] = $p2_m15_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_15', $find_status)) {
                unset($find_status['match_15']);
            }

            $find_status['match_15'] = $request->rou_2_mat_15_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_15'] = $request->rou_2_mat_15_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m15_total > $p2_m15_total) {
            $round_two_winners_array['match_15'] = $request->p1_m15;
        } else {
           $round_two_winners_array['match_15'] = $request->p2_m15; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_15', $find_winners)) {
                unset($find_winners['match_15']);
            }

            if($p1_m15_total > $p2_m15_total) {
                $find_winners['match_15'] = $request->p1_m15;
            } else {
                $find_winners['match_15'] = $request->p2_m15; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_two_result_sixteen(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m16_s1' => 'required',
            'p1_m16_s2' => 'required',
            'p2_m16_s1' => 'required',
            'p2_m16_s2' => 'required',
        ]);

        $round_two_results_array = [];


        if ($request->p1_m16_s1 > $request->p2_m16_s1 && $request->p1_m16_s2 > $request->p2_m16_s2) {

            $p1_m16_total = 2;
            $p2_m16_total = 0;

        } elseif ($request->p2_m16_s1 > $request->p1_m16_s1 && $request->p2_m16_s2 > $request->p1_m16_s2) {

            $p1_m16_total = 0;
            $p2_m16_total = 2;

        } elseif ($request->p1_m16_s1 > $request->p2_m16_s1 && $request->p1_m16_s2 < $request->p2_m16_s2 && $request->p1_m16_s3 > $request->p2_m16_s3) {

            $p1_m16_total = 2;
            $p2_m16_total = 1;

        } elseif ($request->p1_m16_s1 < $request->p2_m16_s1 && $request->p1_m16_s2 > $request->p2_m16_s2 && $request->p1_m16_s3 < $request->p2_m16_s3) {

            $p1_m16_total = 1;
            $p2_m16_total = 2;

        } elseif ($request->p1_m16_s1 < $request->p2_m16_s1 && $request->p1_m16_s2 > $request->p2_m16_s2 && $request->p1_m16_s3 > $request->p2_m16_s3) {

            $p1_m16_total = 2;
            $p2_m16_total = 1;

        } elseif ($request->p1_m16_s1 > $request->p2_m16_s1 && $request->p1_m16_s2 < $request->p2_m16_s2 && $request->p1_m16_s3 < $request->p2_m16_s3) {

            $p1_m16_total = 1;
            $p2_m16_total = 2;

        }


        $round_two_results_array['match_16'][$request->p1_m16]['set_1'] = $request->p1_m16_s1;
        $round_two_results_array['match_16'][$request->p1_m16]['set_2'] = $request->p1_m16_s2;
        $round_two_results_array['match_16'][$request->p1_m16]['set_3'] = $request->p1_m16_s3;
        $round_two_results_array['match_16'][$request->p1_m16]['total'] = $p1_m16_total;

        $round_two_results_array['match_16'][$request->p2_m16]['set_1'] = $request->p2_m16_s1;       
        $round_two_results_array['match_16'][$request->p2_m16]['set_2'] = $request->p2_m16_s2;
        $round_two_results_array['match_16'][$request->p2_m16]['set_3'] = $request->p2_m16_s3;
        $round_two_results_array['match_16'][$request->p2_m16]['total'] = $p2_m16_total; 

        $rslt_chk = [$request->p1_m16, $request->p2_m16];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_two_results) {
            
            $find_results = json_decode($tournament->round_two_results, true);
            if(array_key_exists('match_16', $find_results)) {
                unset($find_results['match_16']);
            }

            $find_results['match_16'][$request->p1_m16]['set_1'] = $request->p1_m16_s1;
            $find_results['match_16'][$request->p1_m16]['set_2'] = $request->p1_m16_s2;
            $find_results['match_16'][$request->p1_m16]['set_3'] = $request->p1_m16_s3;
            $find_results['match_16'][$request->p1_m16]['total'] = $p1_m16_total;

            $find_results['match_16'][$request->p2_m16]['set_1'] = $request->p2_m16_s1;       
            $find_results['match_16'][$request->p2_m16]['set_2'] = $request->p2_m16_s2;
            $find_results['match_16'][$request->p2_m16]['set_3'] = $request->p2_m16_s3;
            $find_results['match_16'][$request->p2_m16]['total'] = $p2_m16_total; 
            $tournament->round_two_results = json_encode($find_results);

        } else {
            $tournament->round_two_results = json_encode($round_two_results_array);
        }


        $round_two_status_array = [];
        if($tournament->round_two_status) {
            
            $find_status = json_decode($tournament->round_two_status, true);
            if(array_key_exists('match_16', $find_status)) {
                unset($find_status['match_16']);
            }

            $find_status['match_16'] = $request->rou_2_mat_16_status;
            $tournament->round_two_status = json_encode($find_status);

        } else {
            $round_two_status_array['match_16'] = $request->rou_2_mat_16_status;
            $tournament->round_two_status = json_encode($round_two_status_array);
        }

        
        $round_two_winners_array = [];        
        if($p1_m16_total > $p2_m16_total) {
            $round_two_winners_array['match_16'] = $request->p1_m16;
        } else {
           $round_two_winners_array['match_16'] = $request->p2_m16; 
        }

        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            if(array_key_exists('match_16', $find_winners)) {
                unset($find_winners['match_16']);
            }

            if($p1_m16_total > $p2_m16_total) {
                $find_winners['match_16'] = $request->p1_m16;
            } else {
                $find_winners['match_16'] = $request->p2_m16; 
            }
            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }



    public function submit_round_two_winners(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

            
        // $this->validate($request, [
        //     'rou_2_mat_1_winner' => 'required',
        //     'rou_2_mat_2_winner' => 'required',
        //     'rou_2_mat_3_winner' => 'required',
        //     'rou_2_mat_4_winner' => 'required',
        //     'rou_2_mat_5_winner' => 'required',
        //     'rou_2_mat_6_winner' => 'required',
        //     'rou_2_mat_7_winner' => 'required',
        //     'rou_2_mat_8_winner' => 'required',
        // ]);


        $round_two_winners_array = [];
        $round_two_wnr_chk = [$request->rou_2_mat_1_winner, $request->rou_2_mat_2_winner, $request->rou_2_mat_3_winner, $request->rou_2_mat_4_winner, $request->rou_2_mat_5_winner, $request->rou_2_mat_6_winner, $request->rou_2_mat_7_winner, $request->rou_2_mat_8_winner];

        
        if($request->rou_2_mat_1_winner) {
            $round_two_winners_array['match_1'] = $request->rou_2_mat_1_winner;
        }

        if($request->rou_2_mat_2_winner) {
            $round_two_winners_array['match_2'] = $request->rou_2_mat_2_winner;
        }

        if($request->rou_2_mat_3_winner) {
            $round_two_winners_array['match_3'] = $request->rou_2_mat_3_winner;
        }

        if($request->rou_2_mat_4_winner) {
            $round_two_winners_array['match_4'] = $request->rou_2_mat_4_winner;
        }

        if($request->rou_2_mat_5_winner) {
            $round_two_winners_array['match_5'] = $request->rou_2_mat_5_winner;
        }

        if($request->rou_2_mat_6_winner) {
            $round_two_winners_array['match_6'] = $request->rou_2_mat_6_winner;
        }

        if($request->rou_2_mat_7_winner) {
            $round_two_winners_array['match_7'] = $request->rou_2_mat_7_winner;
        }

        if($request->rou_2_mat_8_winner) {
            $round_two_winners_array['match_8'] = $request->rou_2_mat_8_winner;
        }

        
        $chk_players = max(array_count_values($round_two_wnr_chk));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Announced as Winner Twice !');
            return redirect()->back();
        }

        $tournament->round_two_winners = json_encode($round_two_winners_array);

        $tournament->save();

        Session::flash('success', 'Players Announced as Winners Successfully !');
        return redirect()->back();

    }
    // ROUND 2 END


    // ROUND 3
    public function submit_round_three_match_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_1_player_1' => 'required',
            'rou_3_mat_1_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_1_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_1_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_1_player_1, $request->rou_3_mat_1_player_2];

        $round_three_matches_array['match_1'] = $request->rou_3_mat_1_player_1 . ' VS ' . $request->rou_3_mat_1_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {

            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_1', $find_matches)) {
                unset($find_matches['match_1']);
            }
            $find_matches['match_1'] = $request->rou_3_mat_1_player_1 . ' VS ' . $request->rou_3_mat_1_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_three_match_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_2_player_1' => 'required',
            'rou_3_mat_2_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_2_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_2_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_2_player_1, $request->rou_3_mat_2_player_2];

        $round_three_matches_array['match_2'] = $request->rou_3_mat_2_player_1 . ' VS ' . $request->rou_3_mat_2_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {
            
            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_2', $find_matches)) {
                unset($find_matches['match_2']);
            }
            $find_matches['match_2'] = $request->rou_3_mat_2_player_1 . ' VS ' . $request->rou_3_mat_2_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_three_match_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_3_player_1' => 'required',
            'rou_3_mat_3_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_3_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_3_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_3_player_1, $request->rou_3_mat_3_player_2];

        $round_three_matches_array['match_3'] = $request->rou_3_mat_3_player_1 . ' VS ' . $request->rou_3_mat_3_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {
            
            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_3', $find_matches)) {
                unset($find_matches['match_3']);
            }
            $find_matches['match_3'] = $request->rou_3_mat_3_player_1 . ' VS ' . $request->rou_3_mat_3_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_three_match_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_4_player_1' => 'required',
            'rou_3_mat_4_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_4_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_4_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_4_player_1, $request->rou_3_mat_4_player_2];

        $round_three_matches_array['match_4'] = $request->rou_3_mat_4_player_1 . ' VS ' . $request->rou_3_mat_4_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {
            
            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_4', $find_matches)) {
                unset($find_matches['match_4']);
            }
            $find_matches['match_4'] = $request->rou_3_mat_4_player_1 . ' VS ' . $request->rou_3_mat_4_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_three_match_five(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_5_player_1' => 'required',
            'rou_3_mat_5_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_5_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_5_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_5_player_1, $request->rou_3_mat_5_player_2];

        $round_three_matches_array['match_5'] = $request->rou_3_mat_5_player_1 . ' VS ' . $request->rou_3_mat_5_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {
            
            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_5', $find_matches)) {
                unset($find_matches['match_5']);
            }
            $find_matches['match_5'] = $request->rou_3_mat_5_player_1 . ' VS ' . $request->rou_3_mat_5_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_three_match_six(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_6_player_1' => 'required',
            'rou_3_mat_6_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_6_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_6_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_6_player_1, $request->rou_3_mat_6_player_2];

        $round_three_matches_array['match_6'] = $request->rou_3_mat_6_player_1 . ' VS ' . $request->rou_3_mat_6_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {
            
            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_6', $find_matches)) {
                unset($find_matches['match_6']);
            }
            $find_matches['match_6'] = $request->rou_3_mat_6_player_1 . ' VS ' . $request->rou_3_mat_6_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_three_match_seven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_7_player_1' => 'required',
            'rou_3_mat_7_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_7_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_7_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_7_player_1, $request->rou_3_mat_7_player_2];

        $round_three_matches_array['match_7'] = $request->rou_3_mat_7_player_1 . ' VS ' . $request->rou_3_mat_7_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {
            
            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_7', $find_matches)) {
                unset($find_matches['match_7']);
            }
            $find_matches['match_7'] = $request->rou_3_mat_7_player_1 . ' VS ' . $request->rou_3_mat_7_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_round_three_match_eight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou_3_mat_8_player_1' => 'required',
            'rou_3_mat_8_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);
        $tournament_tree_size = $tournament->tree_size/4;

        $plr_1 = User::findOrFail($request->rou_3_mat_8_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->rou_3_mat_8_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_rou3 = json_decode($tournament->round_three_deadline);
        $endd_r1 = explode(", ", $t_d_rou3->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} R{$tournament_tree_size} is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $round_three_matches_array = [];
        $match_chk_players  = [$request->rou_3_mat_8_player_1, $request->rou_3_mat_8_player_2];

        $round_three_matches_array['match_8'] = $request->rou_3_mat_8_player_1 . ' VS ' . $request->rou_3_mat_8_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_matches) {
            
            $find_matches = json_decode($tournament->round_three_matches, true);
            if(array_key_exists('match_8', $find_matches)) {
                unset($find_matches['match_8']);
            }
            $find_matches['match_8'] = $request->rou_3_mat_8_player_1 . ' VS ' . $request->rou_3_mat_8_player_2;
            $tournament->round_three_matches = json_encode($find_matches);

        } else {
            $tournament->round_three_matches = json_encode($round_three_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }


    public function submit_round_three_result_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m1_s1' => 'required',
            'p1_m1_s2' => 'required',
            'p2_m1_s1' => 'required',
            'p2_m1_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2) {

            $p1_m1_total = 2;
            $p2_m1_total = 0;

        } elseif ($request->p2_m1_s1 > $request->p1_m1_s1 && $request->p2_m1_s2 > $request->p1_m1_s2) {

            $p1_m1_total = 0;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        }


        $round_three_results_array['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
        $round_three_results_array['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
        $round_three_results_array['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
        $round_three_results_array['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

        $round_three_results_array['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
        $round_three_results_array['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
        $round_three_results_array['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
        $round_three_results_array['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 

        $rslt_chk = [$request->p1_m1, $request->p2_m1];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_1', $find_results)) {
                unset($find_results['match_1']);
            }

            $find_results['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
            $find_results['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
            $find_results['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
            $find_results['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

            $find_results['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
            $find_results['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
            $find_results['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
            $find_results['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_1', $find_status)) {
                unset($find_status['match_1']);
            }

            $find_status['match_1'] = $request->rou_3_mat_1_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_1'] = $request->rou_3_mat_1_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m1_total > $p2_m1_total) {
            $round_three_winners_array['match_1'] = $request->p1_m1;
        } else {
           $round_three_winners_array['match_1'] = $request->p2_m1; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_1', $find_winners)) {
                unset($find_winners['match_1']);
            }

            if($p1_m1_total > $p2_m1_total) {
                $find_winners['match_1'] = $request->p1_m1;
            } else {
                $find_winners['match_1'] = $request->p2_m1; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_three_result_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m2_s1' => 'required',
            'p1_m2_s2' => 'required',
            'p2_m2_s1' => 'required',
            'p2_m2_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2) {

            $p1_m2_total = 2;
            $p2_m2_total = 0;

        } elseif ($request->p2_m2_s1 > $request->p1_m2_s1 && $request->p2_m2_s2 > $request->p1_m2_s2) {

            $p1_m2_total = 0;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        }


        $round_three_results_array['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
        $round_three_results_array['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
        $round_three_results_array['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
        $round_three_results_array['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

        $round_three_results_array['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
        $round_three_results_array['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
        $round_three_results_array['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
        $round_three_results_array['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 

        $rslt_chk = [$request->p1_m2, $request->p2_m2];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_2', $find_results)) {
                unset($find_results['match_2']);
            }

            $find_results['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
            $find_results['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
            $find_results['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
            $find_results['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

            $find_results['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
            $find_results['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
            $find_results['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
            $find_results['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_2', $find_status)) {
                unset($find_status['match_2']);
            }

            $find_status['match_2'] = $request->rou_3_mat_2_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_2'] = $request->rou_3_mat_2_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m2_total > $p2_m2_total) {
            $round_three_winners_array['match_2'] = $request->p1_m2;
        } else {
           $round_three_winners_array['match_2'] = $request->p2_m2; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_2', $find_winners)) {
                unset($find_winners['match_2']);
            }

            if($p1_m2_total > $p2_m2_total) {
                $find_winners['match_2'] = $request->p1_m2;
            } else {
                $find_winners['match_2'] = $request->p2_m2; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_three_result_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m3_s1' => 'required',
            'p1_m3_s2' => 'required',
            'p2_m3_s1' => 'required',
            'p2_m3_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2) {

            $p1_m3_total = 2;
            $p2_m3_total = 0;

        } elseif ($request->p2_m3_s1 > $request->p1_m3_s1 && $request->p2_m3_s2 > $request->p1_m3_s2) {

            $p1_m3_total = 0;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        }


        $round_three_results_array['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
        $round_three_results_array['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
        $round_three_results_array['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
        $round_three_results_array['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

        $round_three_results_array['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
        $round_three_results_array['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
        $round_three_results_array['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
        $round_three_results_array['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 

        $rslt_chk = [$request->p1_m3, $request->p2_m3];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_3', $find_results)) {
                unset($find_results['match_3']);
            }

            $find_results['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
            $find_results['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
            $find_results['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
            $find_results['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

            $find_results['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
            $find_results['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
            $find_results['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
            $find_results['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_3', $find_status)) {
                unset($find_status['match_3']);
            }

            $find_status['match_3'] = $request->rou_3_mat_3_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_3'] = $request->rou_3_mat_3_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m3_total > $p2_m3_total) {
            $round_three_winners_array['match_3'] = $request->p1_m3;
        } else {
           $round_three_winners_array['match_3'] = $request->p2_m3; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_3', $find_winners)) {
                unset($find_winners['match_3']);
            }

            if($p1_m3_total > $p2_m3_total) {
                $find_winners['match_3'] = $request->p1_m3;
            } else {
                $find_winners['match_3'] = $request->p2_m3; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_three_result_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m4_s1' => 'required',
            'p1_m4_s2' => 'required',
            'p2_m4_s1' => 'required',
            'p2_m4_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2) {

            $p1_m4_total = 2;
            $p2_m4_total = 0;

        } elseif ($request->p2_m4_s1 > $request->p1_m4_s1 && $request->p2_m4_s2 > $request->p1_m4_s2) {

            $p1_m4_total = 0;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        }


        $round_three_results_array['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
        $round_three_results_array['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
        $round_three_results_array['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
        $round_three_results_array['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

        $round_three_results_array['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
        $round_three_results_array['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
        $round_three_results_array['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
        $round_three_results_array['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 

        $rslt_chk = [$request->p1_m4, $request->p2_m4];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_4', $find_results)) {
                unset($find_results['match_4']);
            }

            $find_results['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
            $find_results['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
            $find_results['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
            $find_results['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

            $find_results['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
            $find_results['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
            $find_results['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
            $find_results['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_4', $find_status)) {
                unset($find_status['match_4']);
            }

            $find_status['match_4'] = $request->rou_3_mat_4_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_4'] = $request->rou_3_mat_4_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m4_total > $p2_m4_total) {
            $round_three_winners_array['match_4'] = $request->p1_m4;
        } else {
           $round_three_winners_array['match_4'] = $request->p2_m4; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_4', $find_winners)) {
                unset($find_winners['match_4']);
            }

            if($p1_m4_total > $p2_m4_total) {
                $find_winners['match_4'] = $request->p1_m4;
            } else {
                $find_winners['match_4'] = $request->p2_m4; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_three_result_five(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m5_s1' => 'required',
            'p1_m5_s2' => 'required',
            'p2_m5_s1' => 'required',
            'p2_m5_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2) {

            $p1_m5_total = 2;
            $p2_m5_total = 0;

        } elseif ($request->p2_m5_s1 > $request->p1_m5_s1 && $request->p2_m5_s2 > $request->p1_m5_s2) {

            $p1_m5_total = 0;
            $p2_m5_total = 2;

        } elseif ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 < $request->p2_m5_s2 && $request->p1_m5_s3 > $request->p2_m5_s3) {

            $p1_m5_total = 2;
            $p2_m5_total = 1;

        } elseif ($request->p1_m5_s1 < $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2 && $request->p1_m5_s3 < $request->p2_m5_s3) {

            $p1_m5_total = 1;
            $p2_m5_total = 2;

        } elseif ($request->p1_m5_s1 < $request->p2_m5_s1 && $request->p1_m5_s2 > $request->p2_m5_s2 && $request->p1_m5_s3 > $request->p2_m5_s3) {

            $p1_m5_total = 2;
            $p2_m5_total = 1;

        } elseif ($request->p1_m5_s1 > $request->p2_m5_s1 && $request->p1_m5_s2 < $request->p2_m5_s2 && $request->p1_m5_s3 < $request->p2_m5_s3) {

            $p1_m5_total = 1;
            $p2_m5_total = 2;

        }


        $round_three_results_array['match_5'][$request->p1_m5]['set_1'] = $request->p1_m5_s1;
        $round_three_results_array['match_5'][$request->p1_m5]['set_2'] = $request->p1_m5_s2;
        $round_three_results_array['match_5'][$request->p1_m5]['set_3'] = $request->p1_m5_s3;
        $round_three_results_array['match_5'][$request->p1_m5]['total'] = $p1_m5_total;

        $round_three_results_array['match_5'][$request->p2_m5]['set_1'] = $request->p2_m5_s1;       
        $round_three_results_array['match_5'][$request->p2_m5]['set_2'] = $request->p2_m5_s2;
        $round_three_results_array['match_5'][$request->p2_m5]['set_3'] = $request->p2_m5_s3;
        $round_three_results_array['match_5'][$request->p2_m5]['total'] = $p2_m5_total; 

        $rslt_chk = [$request->p1_m5, $request->p2_m5];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_5', $find_results)) {
                unset($find_results['match_5']);
            }

            $find_results['match_5'][$request->p1_m5]['set_1'] = $request->p1_m5_s1;
            $find_results['match_5'][$request->p1_m5]['set_2'] = $request->p1_m5_s2;
            $find_results['match_5'][$request->p1_m5]['set_3'] = $request->p1_m5_s3;
            $find_results['match_5'][$request->p1_m5]['total'] = $p1_m5_total;

            $find_results['match_5'][$request->p2_m5]['set_1'] = $request->p2_m5_s1;       
            $find_results['match_5'][$request->p2_m5]['set_2'] = $request->p2_m5_s2;
            $find_results['match_5'][$request->p2_m5]['set_3'] = $request->p2_m5_s3;
            $find_results['match_5'][$request->p2_m5]['total'] = $p2_m5_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_5', $find_status)) {
                unset($find_status['match_5']);
            }

            $find_status['match_5'] = $request->rou_3_mat_5_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_5'] = $request->rou_3_mat_5_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m5_total > $p2_m5_total) {
            $round_three_winners_array['match_5'] = $request->p1_m5;
        } else {
           $round_three_winners_array['match_5'] = $request->p2_m5; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_5', $find_winners)) {
                unset($find_winners['match_5']);
            }

            if($p1_m5_total > $p2_m5_total) {
                $find_winners['match_5'] = $request->p1_m5;
            } else {
                $find_winners['match_5'] = $request->p2_m5; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_three_result_six(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m6_s1' => 'required',
            'p1_m6_s2' => 'required',
            'p2_m6_s1' => 'required',
            'p2_m6_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2) {

            $p1_m6_total = 2;
            $p2_m6_total = 0;

        } elseif ($request->p2_m6_s1 > $request->p1_m6_s1 && $request->p2_m6_s2 > $request->p1_m6_s2) {

            $p1_m6_total = 0;
            $p2_m6_total = 2;

        } elseif ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 < $request->p2_m6_s2 && $request->p1_m6_s3 > $request->p2_m6_s3) {

            $p1_m6_total = 2;
            $p2_m6_total = 1;

        } elseif ($request->p1_m6_s1 < $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2 && $request->p1_m6_s3 < $request->p2_m6_s3) {

            $p1_m6_total = 1;
            $p2_m6_total = 2;

        } elseif ($request->p1_m6_s1 < $request->p2_m6_s1 && $request->p1_m6_s2 > $request->p2_m6_s2 && $request->p1_m6_s3 > $request->p2_m6_s3) {

            $p1_m6_total = 2;
            $p2_m6_total = 1;

        } elseif ($request->p1_m6_s1 > $request->p2_m6_s1 && $request->p1_m6_s2 < $request->p2_m6_s2 && $request->p1_m6_s3 < $request->p2_m6_s3) {

            $p1_m6_total = 1;
            $p2_m6_total = 2;

        }


        $round_three_results_array['match_6'][$request->p1_m6]['set_1'] = $request->p1_m6_s1;
        $round_three_results_array['match_6'][$request->p1_m6]['set_2'] = $request->p1_m6_s2;
        $round_three_results_array['match_6'][$request->p1_m6]['set_3'] = $request->p1_m6_s3;
        $round_three_results_array['match_6'][$request->p1_m6]['total'] = $p1_m6_total;

        $round_three_results_array['match_6'][$request->p2_m6]['set_1'] = $request->p2_m6_s1;       
        $round_three_results_array['match_6'][$request->p2_m6]['set_2'] = $request->p2_m6_s2;
        $round_three_results_array['match_6'][$request->p2_m6]['set_3'] = $request->p2_m6_s3;
        $round_three_results_array['match_6'][$request->p2_m6]['total'] = $p2_m6_total; 

        $rslt_chk = [$request->p1_m6, $request->p2_m6];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_6', $find_results)) {
                unset($find_results['match_6']);
            }

            $find_results['match_6'][$request->p1_m6]['set_1'] = $request->p1_m6_s1;
            $find_results['match_6'][$request->p1_m6]['set_2'] = $request->p1_m6_s2;
            $find_results['match_6'][$request->p1_m6]['set_3'] = $request->p1_m6_s3;
            $find_results['match_6'][$request->p1_m6]['total'] = $p1_m6_total;

            $find_results['match_6'][$request->p2_m6]['set_1'] = $request->p2_m6_s1;       
            $find_results['match_6'][$request->p2_m6]['set_2'] = $request->p2_m6_s2;
            $find_results['match_6'][$request->p2_m6]['set_3'] = $request->p2_m6_s3;
            $find_results['match_6'][$request->p2_m6]['total'] = $p2_m6_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_6', $find_status)) {
                unset($find_status['match_6']);
            }

            $find_status['match_6'] = $request->rou_3_mat_6_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_6'] = $request->rou_3_mat_6_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m6_total > $p2_m6_total) {
            $round_three_winners_array['match_6'] = $request->p1_m6;
        } else {
           $round_three_winners_array['match_6'] = $request->p2_m6; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_6', $find_winners)) {
                unset($find_winners['match_6']);
            }

            if($p1_m6_total > $p2_m6_total) {
                $find_winners['match_6'] = $request->p1_m6;
            } else {
                $find_winners['match_6'] = $request->p2_m6; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_three_result_seven(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m7_s1' => 'required',
            'p1_m7_s2' => 'required',
            'p2_m7_s1' => 'required',
            'p2_m7_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2) {

            $p1_m7_total = 2;
            $p2_m7_total = 0;

        } elseif ($request->p2_m7_s1 > $request->p1_m7_s1 && $request->p2_m7_s2 > $request->p1_m7_s2) {

            $p1_m7_total = 0;
            $p2_m7_total = 2;

        } elseif ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 < $request->p2_m7_s2 && $request->p1_m7_s3 > $request->p2_m7_s3) {

            $p1_m7_total = 2;
            $p2_m7_total = 1;

        } elseif ($request->p1_m7_s1 < $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2 && $request->p1_m7_s3 < $request->p2_m7_s3) {

            $p1_m7_total = 1;
            $p2_m7_total = 2;

        } elseif ($request->p1_m7_s1 < $request->p2_m7_s1 && $request->p1_m7_s2 > $request->p2_m7_s2 && $request->p1_m7_s3 > $request->p2_m7_s3) {

            $p1_m7_total = 2;
            $p2_m7_total = 1;

        } elseif ($request->p1_m7_s1 > $request->p2_m7_s1 && $request->p1_m7_s2 < $request->p2_m7_s2 && $request->p1_m7_s3 < $request->p2_m7_s3) {

            $p1_m7_total = 1;
            $p2_m7_total = 2;

        }


        $round_three_results_array['match_7'][$request->p1_m7]['set_1'] = $request->p1_m7_s1;
        $round_three_results_array['match_7'][$request->p1_m7]['set_2'] = $request->p1_m7_s2;
        $round_three_results_array['match_7'][$request->p1_m7]['set_3'] = $request->p1_m7_s3;
        $round_three_results_array['match_7'][$request->p1_m7]['total'] = $p1_m7_total;

        $round_three_results_array['match_7'][$request->p2_m7]['set_1'] = $request->p2_m7_s1;       
        $round_three_results_array['match_7'][$request->p2_m7]['set_2'] = $request->p2_m7_s2;
        $round_three_results_array['match_7'][$request->p2_m7]['set_3'] = $request->p2_m7_s3;
        $round_three_results_array['match_7'][$request->p2_m7]['total'] = $p2_m7_total; 

        $rslt_chk = [$request->p1_m7, $request->p2_m7];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_7', $find_results)) {
                unset($find_results['match_7']);
            }

            $find_results['match_7'][$request->p1_m7]['set_1'] = $request->p1_m7_s1;
            $find_results['match_7'][$request->p1_m7]['set_2'] = $request->p1_m7_s2;
            $find_results['match_7'][$request->p1_m7]['set_3'] = $request->p1_m7_s3;
            $find_results['match_7'][$request->p1_m7]['total'] = $p1_m7_total;

            $find_results['match_7'][$request->p2_m7]['set_1'] = $request->p2_m7_s1;       
            $find_results['match_7'][$request->p2_m7]['set_2'] = $request->p2_m7_s2;
            $find_results['match_7'][$request->p2_m7]['set_3'] = $request->p2_m7_s3;
            $find_results['match_7'][$request->p2_m7]['total'] = $p2_m7_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_7', $find_status)) {
                unset($find_status['match_7']);
            }

            $find_status['match_7'] = $request->rou_3_mat_7_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_7'] = $request->rou_3_mat_7_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m7_total > $p2_m7_total) {
            $round_three_winners_array['match_7'] = $request->p1_m7;
        } else {
           $round_three_winners_array['match_7'] = $request->p2_m7; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_7', $find_winners)) {
                unset($find_winners['match_7']);
            }

            if($p1_m7_total > $p2_m7_total) {
                $find_winners['match_7'] = $request->p1_m7;
            } else {
                $find_winners['match_7'] = $request->p2_m7; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_round_three_result_eight(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m8_s1' => 'required',
            'p1_m8_s2' => 'required',
            'p2_m8_s1' => 'required',
            'p2_m8_s2' => 'required',
        ]);

        $round_three_results_array = [];


        if ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2) {

            $p1_m8_total = 2;
            $p2_m8_total = 0;

        } elseif ($request->p2_m8_s1 > $request->p1_m8_s1 && $request->p2_m8_s2 > $request->p1_m8_s2) {

            $p1_m8_total = 0;
            $p2_m8_total = 2;

        } elseif ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 < $request->p2_m8_s2 && $request->p1_m8_s3 > $request->p2_m8_s3) {

            $p1_m8_total = 2;
            $p2_m8_total = 1;

        } elseif ($request->p1_m8_s1 < $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2 && $request->p1_m8_s3 < $request->p2_m8_s3) {

            $p1_m8_total = 1;
            $p2_m8_total = 2;

        } elseif ($request->p1_m8_s1 < $request->p2_m8_s1 && $request->p1_m8_s2 > $request->p2_m8_s2 && $request->p1_m8_s3 > $request->p2_m8_s3) {

            $p1_m8_total = 2;
            $p2_m8_total = 1;

        } elseif ($request->p1_m8_s1 > $request->p2_m8_s1 && $request->p1_m8_s2 < $request->p2_m8_s2 && $request->p1_m8_s3 < $request->p2_m8_s3) {

            $p1_m8_total = 1;
            $p2_m8_total = 2;

        }


        $round_three_results_array['match_8'][$request->p1_m8]['set_1'] = $request->p1_m8_s1;
        $round_three_results_array['match_8'][$request->p1_m8]['set_2'] = $request->p1_m8_s2;
        $round_three_results_array['match_8'][$request->p1_m8]['set_3'] = $request->p1_m8_s3;
        $round_three_results_array['match_8'][$request->p1_m8]['total'] = $p1_m8_total;

        $round_three_results_array['match_8'][$request->p2_m8]['set_1'] = $request->p2_m8_s1;       
        $round_three_results_array['match_8'][$request->p2_m8]['set_2'] = $request->p2_m8_s2;
        $round_three_results_array['match_8'][$request->p2_m8]['set_3'] = $request->p2_m8_s3;
        $round_three_results_array['match_8'][$request->p2_m8]['total'] = $p2_m8_total; 

        $rslt_chk = [$request->p1_m8, $request->p2_m8];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->round_three_results) {
            
            $find_results = json_decode($tournament->round_three_results, true);
            if(array_key_exists('match_8', $find_results)) {
                unset($find_results['match_8']);
            }

            $find_results['match_8'][$request->p1_m8]['set_1'] = $request->p1_m8_s1;
            $find_results['match_8'][$request->p1_m8]['set_2'] = $request->p1_m8_s2;
            $find_results['match_8'][$request->p1_m8]['set_3'] = $request->p1_m8_s3;
            $find_results['match_8'][$request->p1_m8]['total'] = $p1_m8_total;

            $find_results['match_8'][$request->p2_m8]['set_1'] = $request->p2_m8_s1;       
            $find_results['match_8'][$request->p2_m8]['set_2'] = $request->p2_m8_s2;
            $find_results['match_8'][$request->p2_m8]['set_3'] = $request->p2_m8_s3;
            $find_results['match_8'][$request->p2_m8]['total'] = $p2_m8_total; 
            $tournament->round_three_results = json_encode($find_results);

        } else {
            $tournament->round_three_results = json_encode($round_three_results_array);
        }


        $round_three_status_array = [];
        if($tournament->round_three_status) {
            
            $find_status = json_decode($tournament->round_three_status, true);
            if(array_key_exists('match_8', $find_status)) {
                unset($find_status['match_8']);
            }

            $find_status['match_8'] = $request->rou_3_mat_8_status;
            $tournament->round_three_status = json_encode($find_status);

        } else {
            $round_three_status_array['match_8'] = $request->rou_3_mat_8_status;
            $tournament->round_three_status = json_encode($round_three_status_array);
        }

        
        $round_three_winners_array = [];        
        if($p1_m8_total > $p2_m8_total) {
            $round_three_winners_array['match_8'] = $request->p1_m8;
        } else {
           $round_three_winners_array['match_8'] = $request->p2_m8; 
        }

        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            if(array_key_exists('match_8', $find_winners)) {
                unset($find_winners['match_8']);
            }

            if($p1_m8_total > $p2_m8_total) {
                $find_winners['match_8'] = $request->p1_m8;
            } else {
                $find_winners['match_8'] = $request->p2_m8; 
            }
            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }


    public function submit_round_three_winners(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

            
        // $this->validate($request, [
        //     'rou_3_mat_1_winner' => 'required',
        //     'rou_3_mat_2_winner' => 'required',
        //     'rou_3_mat_3_winner' => 'required',
        //     'rou_3_mat_4_winner' => 'required',
        //     'rou_3_mat_5_winner' => 'required',
        //     'rou_3_mat_6_winner' => 'required',
        //     'rou_3_mat_7_winner' => 'required',
        //     'rou_3_mat_8_winner' => 'required',
        // ]);


        $round_three_winners_array = [];
        $round_three_wnr_chk = [$request->rou_3_mat_1_winner, $request->rou_3_mat_2_winner, $request->rou_3_mat_3_winner, $request->rou_3_mat_4_winner, $request->rou_3_mat_5_winner, $request->rou_3_mat_6_winner, $request->rou_3_mat_7_winner, $request->rou_3_mat_8_winner];

        
        if($request->rou_3_mat_1_winner) {
            $round_three_winners_array['match_1'] = $request->rou_3_mat_1_winner;
        }

        if($request->rou_3_mat_2_winner) {
            $round_three_winners_array['match_2'] = $request->rou_3_mat_2_winner;
        }

        if($request->rou_3_mat_3_winner) {
            $round_three_winners_array['match_3'] = $request->rou_3_mat_3_winner;
        }

        if($request->rou_3_mat_4_winner) {
            $round_three_winners_array['match_4'] = $request->rou_3_mat_4_winner;
        }

        if($request->rou_3_mat_5_winner) {
            $round_three_winners_array['match_5'] = $request->rou_3_mat_5_winner;
        }

        if($request->rou_3_mat_6_winner) {
            $round_three_winners_array['match_6'] = $request->rou_3_mat_6_winner;
        }

        if($request->rou_3_mat_7_winner) {
            $round_three_winners_array['match_7'] = $request->rou_3_mat_7_winner;
        }

        if($request->rou_3_mat_8_winner) {
            $round_three_winners_array['match_8'] = $request->rou_3_mat_8_winner;
        }

        
        $chk_players = max(array_count_values($round_three_wnr_chk));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Announced as Winner Twice !');
            return redirect()->back();
        }

        $tournament->round_three_winners = json_encode($round_three_winners_array);

        $tournament->save();

        Session::flash('success', 'Players Announced as Winners Successfully !');
        return redirect()->back();

    }

    // ROUND 3 END


    // QUARTER-FINAL
    public function submit_quarter_final_match_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'quar_mat_1_player_1' => 'required',
            'quar_mat_1_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->quar_mat_1_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->quar_mat_1_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_quar = json_decode($tournament->quarter_final_deadline);
        $endd_r1 = explode(", ", $t_d_quar->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $quarter_final_matches_array = [];
        $match_chk_players  = [$request->quar_mat_1_player_1, $request->quar_mat_1_player_2];

        $quarter_final_matches_array['match_1'] = $request->quar_mat_1_player_1 . ' VS ' . $request->quar_mat_1_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_matches) {

            $find_matches = json_decode($tournament->quarter_final_matches, true);
            if(array_key_exists('match_1', $find_matches)) {
                unset($find_matches['match_1']);
            }
            $find_matches['match_1'] = $request->quar_mat_1_player_1 . ' VS ' . $request->quar_mat_1_player_2;
            $tournament->quarter_final_matches = json_encode($find_matches);

        } else {
            $tournament->quarter_final_matches = json_encode($quarter_final_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_quarter_final_match_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'quar_mat_2_player_1' => 'required',
            'quar_mat_2_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->quar_mat_2_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->quar_mat_2_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_quar = json_decode($tournament->quarter_final_deadline);
        $endd_r1 = explode(", ", $t_d_quar->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $quarter_final_matches_array = [];
        $match_chk_players  = [$request->quar_mat_2_player_1, $request->quar_mat_2_player_2];

        $quarter_final_matches_array['match_2'] = $request->quar_mat_2_player_1 . ' VS ' . $request->quar_mat_2_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_matches) {
            
            $find_matches = json_decode($tournament->quarter_final_matches, true);
            if(array_key_exists('match_2', $find_matches)) {
                unset($find_matches['match_2']);
            }
            $find_matches['match_2'] = $request->quar_mat_2_player_1 . ' VS ' . $request->quar_mat_2_player_2;
            $tournament->quarter_final_matches = json_encode($find_matches);

        } else {
            $tournament->quarter_final_matches = json_encode($quarter_final_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_quarter_final_match_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'quar_mat_3_player_1' => 'required',
            'quar_mat_3_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->quar_mat_3_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->quar_mat_3_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_quar = json_decode($tournament->quarter_final_deadline);
        $endd_r1 = explode(", ", $t_d_quar->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $quarter_final_matches_array = [];
        $match_chk_players  = [$request->quar_mat_3_player_1, $request->quar_mat_3_player_2];

        $quarter_final_matches_array['match_3'] = $request->quar_mat_3_player_1 . ' VS ' . $request->quar_mat_3_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_matches) {
            
            $find_matches = json_decode($tournament->quarter_final_matches, true);
            if(array_key_exists('match_3', $find_matches)) {
                unset($find_matches['match_3']);
            }
            $find_matches['match_3'] = $request->quar_mat_3_player_1 . ' VS ' . $request->quar_mat_3_player_2;
            $tournament->quarter_final_matches = json_encode($find_matches);

        } else {
            $tournament->quarter_final_matches = json_encode($quarter_final_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_quarter_final_match_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'quar_mat_4_player_1' => 'required',
            'quar_mat_4_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->quar_mat_4_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->quar_mat_4_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_quar = json_decode($tournament->quarter_final_deadline);
        $endd_r1 = explode(", ", $t_d_quar->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} QF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $quarter_final_matches_array = [];
        $match_chk_players  = [$request->quar_mat_4_player_1, $request->quar_mat_4_player_2];

        $quarter_final_matches_array['match_4'] = $request->quar_mat_4_player_1 . ' VS ' . $request->quar_mat_4_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_matches) {
            
            $find_matches = json_decode($tournament->quarter_final_matches, true);
            if(array_key_exists('match_4', $find_matches)) {
                unset($find_matches['match_4']);
            }
            $find_matches['match_4'] = $request->quar_mat_4_player_1 . ' VS ' . $request->quar_mat_4_player_2;
            $tournament->quarter_final_matches = json_encode($find_matches);

        } else {
            $tournament->quarter_final_matches = json_encode($quarter_final_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }


    public function submit_quarter_final_result_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m1_s1' => 'required',
            'p1_m1_s2' => 'required',
            'p2_m1_s1' => 'required',
            'p2_m1_s2' => 'required',
        ]);

        $quarter_final_results_array = [];


        if ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2) {

            $p1_m1_total = 2;
            $p2_m1_total = 0;

        } elseif ($request->p2_m1_s1 > $request->p1_m1_s1 && $request->p2_m1_s2 > $request->p1_m1_s2) {

            $p1_m1_total = 0;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        }


        $quarter_final_results_array['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
        $quarter_final_results_array['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
        $quarter_final_results_array['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
        $quarter_final_results_array['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

        $quarter_final_results_array['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
        $quarter_final_results_array['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
        $quarter_final_results_array['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
        $quarter_final_results_array['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 

        $rslt_chk = [$request->p1_m1, $request->p2_m1];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_results) {
            
            $find_results = json_decode($tournament->quarter_final_results, true);
            if(array_key_exists('match_1', $find_results)) {
                unset($find_results['match_1']);
            }

            $find_results['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
            $find_results['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
            $find_results['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
            $find_results['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

            $find_results['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
            $find_results['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
            $find_results['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
            $find_results['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 
            $tournament->quarter_final_results = json_encode($find_results);

        } else {
            $tournament->quarter_final_results = json_encode($quarter_final_results_array);
        }


        $quarter_final_status_array = [];
        if($tournament->quarter_final_status) {
            
            $find_status = json_decode($tournament->quarter_final_status, true);
            if(array_key_exists('match_1', $find_status)) {
                unset($find_status['match_1']);
            }

            $find_status['match_1'] = $request->quar_mat_1_status;
            $tournament->quarter_final_status = json_encode($find_status);

        } else {
            $quarter_final_status_array['match_1'] = $request->quar_mat_1_status;
            $tournament->quarter_final_status = json_encode($quarter_final_status_array);
        }

        
        $quarter_final_winners_array = [];        
        if($p1_m1_total > $p2_m1_total) {
            $quarter_final_winners_array['match_1'] = $request->p1_m1;
        } else {
           $quarter_final_winners_array['match_1'] = $request->p2_m1; 
        }

        if($tournament->quarter_final_winners) {
            $find_winners = json_decode($tournament->quarter_final_winners, true);
            if(array_key_exists('match_1', $find_winners)) {
                unset($find_winners['match_1']);
            }

            if($p1_m1_total > $p2_m1_total) {
                $find_winners['match_1'] = $request->p1_m1;
            } else {
                $find_winners['match_1'] = $request->p2_m1; 
            }
            $tournament->quarter_final_winners = json_encode($find_winners);

        } else {
            $tournament->quarter_final_winners = json_encode($quarter_final_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_quarter_final_result_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m2_s1' => 'required',
            'p1_m2_s2' => 'required',
            'p2_m2_s1' => 'required',
            'p2_m2_s2' => 'required',
        ]);

        $quarter_final_results_array = [];


        if ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2) {

            $p1_m2_total = 2;
            $p2_m2_total = 0;

        } elseif ($request->p2_m2_s1 > $request->p1_m2_s1 && $request->p2_m2_s2 > $request->p1_m2_s2) {

            $p1_m2_total = 0;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        }


        $quarter_final_results_array['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
        $quarter_final_results_array['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
        $quarter_final_results_array['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
        $quarter_final_results_array['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

        $quarter_final_results_array['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
        $quarter_final_results_array['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
        $quarter_final_results_array['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
        $quarter_final_results_array['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 

        $rslt_chk = [$request->p1_m2, $request->p2_m2];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_results) {
            
            $find_results = json_decode($tournament->quarter_final_results, true);
            if(array_key_exists('match_2', $find_results)) {
                unset($find_results['match_2']);
            }

            $find_results['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
            $find_results['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
            $find_results['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
            $find_results['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

            $find_results['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
            $find_results['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
            $find_results['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
            $find_results['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 
            $tournament->quarter_final_results = json_encode($find_results);

        } else {
            $tournament->quarter_final_results = json_encode($quarter_final_results_array);
        }


        $quarter_final_status_array = [];
        if($tournament->quarter_final_status) {
            
            $find_status = json_decode($tournament->quarter_final_status, true);
            if(array_key_exists('match_2', $find_status)) {
                unset($find_status['match_2']);
            }

            $find_status['match_2'] = $request->quar_mat_2_status;
            $tournament->quarter_final_status = json_encode($find_status);

        } else {
            $quarter_final_status_array['match_2'] = $request->quar_mat_2_status;
            $tournament->quarter_final_status = json_encode($quarter_final_status_array);
        }

        
        $quarter_final_winners_array = [];        
        if($p1_m2_total > $p2_m2_total) {
            $quarter_final_winners_array['match_2'] = $request->p1_m2;
        } else {
           $quarter_final_winners_array['match_2'] = $request->p2_m2; 
        }

        if($tournament->quarter_final_winners) {
            $find_winners = json_decode($tournament->quarter_final_winners, true);
            if(array_key_exists('match_2', $find_winners)) {
                unset($find_winners['match_2']);
            }

            if($p1_m2_total > $p2_m2_total) {
                $find_winners['match_2'] = $request->p1_m2;
            } else {
                $find_winners['match_2'] = $request->p2_m2; 
            }
            $tournament->quarter_final_winners = json_encode($find_winners);

        } else {
            $tournament->quarter_final_winners = json_encode($quarter_final_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_quarter_final_result_three(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m3_s1' => 'required',
            'p1_m3_s2' => 'required',
            'p2_m3_s1' => 'required',
            'p2_m3_s2' => 'required',
        ]);

        $quarter_final_results_array = [];


        if ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2) {

            $p1_m3_total = 2;
            $p2_m3_total = 0;

        } elseif ($request->p2_m3_s1 > $request->p1_m3_s1 && $request->p2_m3_s2 > $request->p1_m3_s2) {

            $p1_m3_total = 0;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        } elseif ($request->p1_m3_s1 < $request->p2_m3_s1 && $request->p1_m3_s2 > $request->p2_m3_s2 && $request->p1_m3_s3 > $request->p2_m3_s3) {

            $p1_m3_total = 2;
            $p2_m3_total = 1;

        } elseif ($request->p1_m3_s1 > $request->p2_m3_s1 && $request->p1_m3_s2 < $request->p2_m3_s2 && $request->p1_m3_s3 < $request->p2_m3_s3) {

            $p1_m3_total = 1;
            $p2_m3_total = 2;

        }


        $quarter_final_results_array['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
        $quarter_final_results_array['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
        $quarter_final_results_array['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
        $quarter_final_results_array['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

        $quarter_final_results_array['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
        $quarter_final_results_array['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
        $quarter_final_results_array['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
        $quarter_final_results_array['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 

        $rslt_chk = [$request->p1_m3, $request->p2_m3];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_results) {
            
            $find_results = json_decode($tournament->quarter_final_results, true);
            if(array_key_exists('match_3', $find_results)) {
                unset($find_results['match_3']);
            }

            $find_results['match_3'][$request->p1_m3]['set_1'] = $request->p1_m3_s1;
            $find_results['match_3'][$request->p1_m3]['set_2'] = $request->p1_m3_s2;
            $find_results['match_3'][$request->p1_m3]['set_3'] = $request->p1_m3_s3;
            $find_results['match_3'][$request->p1_m3]['total'] = $p1_m3_total;

            $find_results['match_3'][$request->p2_m3]['set_1'] = $request->p2_m3_s1;       
            $find_results['match_3'][$request->p2_m3]['set_2'] = $request->p2_m3_s2;
            $find_results['match_3'][$request->p2_m3]['set_3'] = $request->p2_m3_s3;
            $find_results['match_3'][$request->p2_m3]['total'] = $p2_m3_total; 
            $tournament->quarter_final_results = json_encode($find_results);

        } else {
            $tournament->quarter_final_results = json_encode($quarter_final_results_array);
        }


        $quarter_final_status_array = [];
        if($tournament->quarter_final_status) {
            
            $find_status = json_decode($tournament->quarter_final_status, true);
            if(array_key_exists('match_3', $find_status)) {
                unset($find_status['match_3']);
            }

            $find_status['match_3'] = $request->quar_mat_3_status;
            $tournament->quarter_final_status = json_encode($find_status);

        } else {
            $quarter_final_status_array['match_3'] = $request->quar_mat_3_status;
            $tournament->quarter_final_status = json_encode($quarter_final_status_array);
        }

        
        $quarter_final_winners_array = [];        
        if($p1_m3_total > $p2_m3_total) {
            $quarter_final_winners_array['match_3'] = $request->p1_m3;
        } else {
           $quarter_final_winners_array['match_3'] = $request->p2_m3; 
        }

        if($tournament->quarter_final_winners) {
            $find_winners = json_decode($tournament->quarter_final_winners, true);
            if(array_key_exists('match_3', $find_winners)) {
                unset($find_winners['match_3']);
            }

            if($p1_m3_total > $p2_m3_total) {
                $find_winners['match_3'] = $request->p1_m3;
            } else {
                $find_winners['match_3'] = $request->p2_m3; 
            }
            $tournament->quarter_final_winners = json_encode($find_winners);

        } else {
            $tournament->quarter_final_winners = json_encode($quarter_final_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_quarter_final_result_four(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m4_s1' => 'required',
            'p1_m4_s2' => 'required',
            'p2_m4_s1' => 'required',
            'p2_m4_s2' => 'required',
        ]);

        $quarter_final_results_array = [];


        if ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2) {

            $p1_m4_total = 2;
            $p2_m4_total = 0;

        } elseif ($request->p2_m4_s1 > $request->p1_m4_s1 && $request->p2_m4_s2 > $request->p1_m4_s2) {

            $p1_m4_total = 0;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        } elseif ($request->p1_m4_s1 < $request->p2_m4_s1 && $request->p1_m4_s2 > $request->p2_m4_s2 && $request->p1_m4_s3 > $request->p2_m4_s3) {

            $p1_m4_total = 2;
            $p2_m4_total = 1;

        } elseif ($request->p1_m4_s1 > $request->p2_m4_s1 && $request->p1_m4_s2 < $request->p2_m4_s2 && $request->p1_m4_s3 < $request->p2_m4_s3) {

            $p1_m4_total = 1;
            $p2_m4_total = 2;

        }


        $quarter_final_results_array['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
        $quarter_final_results_array['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
        $quarter_final_results_array['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
        $quarter_final_results_array['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

        $quarter_final_results_array['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
        $quarter_final_results_array['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
        $quarter_final_results_array['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
        $quarter_final_results_array['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 

        $rslt_chk = [$request->p1_m4, $request->p2_m4];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->quarter_final_results) {
            
            $find_results = json_decode($tournament->quarter_final_results, true);
            if(array_key_exists('match_4', $find_results)) {
                unset($find_results['match_4']);
            }

            $find_results['match_4'][$request->p1_m4]['set_1'] = $request->p1_m4_s1;
            $find_results['match_4'][$request->p1_m4]['set_2'] = $request->p1_m4_s2;
            $find_results['match_4'][$request->p1_m4]['set_3'] = $request->p1_m4_s3;
            $find_results['match_4'][$request->p1_m4]['total'] = $p1_m4_total;

            $find_results['match_4'][$request->p2_m4]['set_1'] = $request->p2_m4_s1;       
            $find_results['match_4'][$request->p2_m4]['set_2'] = $request->p2_m4_s2;
            $find_results['match_4'][$request->p2_m4]['set_3'] = $request->p2_m4_s3;
            $find_results['match_4'][$request->p2_m4]['total'] = $p2_m4_total; 
            $tournament->quarter_final_results = json_encode($find_results);

        } else {
            $tournament->quarter_final_results = json_encode($quarter_final_results_array);
        }


        $quarter_final_status_array = [];
        if($tournament->quarter_final_status) {
            
            $find_status = json_decode($tournament->quarter_final_status, true);
            if(array_key_exists('match_4', $find_status)) {
                unset($find_status['match_4']);
            }

            $find_status['match_4'] = $request->quar_mat_4_status;
            $tournament->quarter_final_status = json_encode($find_status);

        } else {
            $quarter_final_status_array['match_4'] = $request->quar_mat_4_status;
            $tournament->quarter_final_status = json_encode($quarter_final_status_array);
        }

        
        $quarter_final_winners_array = [];        
        if($p1_m4_total > $p2_m4_total) {
            $quarter_final_winners_array['match_4'] = $request->p1_m4;
        } else {
           $quarter_final_winners_array['match_4'] = $request->p2_m4; 
        }

        if($tournament->quarter_final_winners) {
            $find_winners = json_decode($tournament->quarter_final_winners, true);
            if(array_key_exists('match_4', $find_winners)) {
                unset($find_winners['match_4']);
            }

            if($p1_m4_total > $p2_m4_total) {
                $find_winners['match_4'] = $request->p1_m4;
            } else {
                $find_winners['match_4'] = $request->p2_m4; 
            }
            $tournament->quarter_final_winners = json_encode($find_winners);

        } else {
            $tournament->quarter_final_winners = json_encode($quarter_final_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }


    public function submit_quarter_final_winners(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

          
        // $this->validate($request, [
        //     'quar_mat_1_winner' => 'required',
        //     'quar_mat_2_winner' => 'required',
        //     'quar_mat_3_winner' => 'required',
        //     'quar_mat_4_winner' => 'required',
        // ]);


        $quarter_final_winners_array = [];
        $quarter_final_wnr_chk = [$request->quar_mat_1_winner, $request->quar_mat_2_winner, $request->quar_mat_3_winner, $request->quar_mat_4_winner];

        
        if($request->quar_mat_1_winner) {
            $quarter_final_winners_array['match_1'] = $request->quar_mat_1_winner;
        }

        if($request->quar_mat_2_winner) {
            $quarter_final_winners_array['match_2'] = $request->quar_mat_2_winner;
        }

        if($request->quar_mat_3_winner) {
            $quarter_final_winners_array['match_3'] = $request->quar_mat_3_winner;
        }

        if($request->quar_mat_4_winner) {
            $quarter_final_winners_array['match_4'] = $request->quar_mat_4_winner;
        }

        
        $chk_players = max(array_count_values($quarter_final_wnr_chk));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Announced as Winner Twice !');
            return redirect()->back();
        }

        $tournament->quarter_final_winners = json_encode($quarter_final_winners_array);

        $tournament->save();

        Session::flash('success', 'Players Announced as Winners Successfully !');
        return redirect()->back();

    }



    // SEMI-FINAL
    public function submit_semi_final_match_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'sem_mat_1_player_1' => 'required',
            'sem_mat_1_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->sem_mat_1_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->sem_mat_1_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_semf = json_decode($tournament->semi_final_deadline);
        $endd_r1 = explode(", ", $t_d_semf->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues SF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues SF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $semi_final_matches_array = [];
        $match_chk_players  = [$request->sem_mat_1_player_1, $request->sem_mat_1_player_2];

        $semi_final_matches_array['match_1'] = $request->sem_mat_1_player_1 . ' VS ' . $request->sem_mat_1_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->semi_final_matches) {

            $find_matches = json_decode($tournament->semi_final_matches, true);
            if(array_key_exists('match_1', $find_matches)) {
                unset($find_matches['match_1']);
            }
            $find_matches['match_1'] = $request->sem_mat_1_player_1 . ' VS ' . $request->sem_mat_1_player_2;
            $tournament->semi_final_matches = json_encode($find_matches);

        } else {
            $tournament->semi_final_matches = json_encode($semi_final_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }


    public function submit_semi_final_match_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'sem_mat_2_player_1' => 'required',
            'sem_mat_2_player_2' => 'required',
        ]);

        // SMS
        $tournament_name = explode(" - ", $tournament->name);

        $plr_1 = User::findOrFail($request->sem_mat_2_player_1);
        $plr_1_name = explode(" ", $plr_1->name);
        $plr_1_phone = str_replace('+357', '', $plr_1->phone);

        $plr_2 = User::findOrFail($request->sem_mat_2_player_2);
        $plr_2_name = explode(" ", $plr_2->name);
        $plr_2_phone = str_replace('+357', '', $plr_2->phone);

        $t_d_semf = json_decode($tournament->semi_final_deadline);
        $endd_r1 = explode(", ", $t_d_semf->end);

        $to = $plr_1->phone;
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message = "Congrats {$plr_1_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues SF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message = "Hello {$plr_1_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_2->name} {$plr_2_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send($to, $message);

        $to_two = $plr_2->phone;

        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $message_two = "Congrats {$plr_2_name[0]} for qualifying to Leagues knock-out stage.\nYour opponent for Leagues SF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nLeagues Supervisor {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match asap as no extension is available. Thanks and Good luck.";
            } else {
                $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
            }
        } else {
            $message_two = "Hello {$plr_2_name[0]}.\nYour opponent for {$tournament_name[0]} SF is {$plr_1->name} {$plr_1_phone}.\nDeadline is {$endd_r1[0]}.\nSupervisor: {$tournament->supervisor_name} {$tournament->supervisor_phone}.\nPlease arrange your match as soon as possible as no extension is available. Thanks and good luck.";
        }

        $this->sms_send_two($to_two, $message_two);
        // END SMS

        $semi_final_matches_array = [];
        $match_chk_players  = [$request->sem_mat_2_player_1, $request->sem_mat_2_player_2];

        $semi_final_matches_array['match_2'] = $request->sem_mat_2_player_1 . ' VS ' . $request->sem_mat_2_player_2;

        $chk_players = max(array_count_values($match_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice !');
            return redirect()->back();
        }

        if($tournament->semi_final_matches) {

            $find_matches = json_decode($tournament->semi_final_matches, true);
            if(array_key_exists('match_2', $find_matches)) {
                unset($find_matches['match_2']);
            }
            $find_matches['match_2'] = $request->sem_mat_2_player_1 . ' VS ' . $request->sem_mat_2_player_2;
            $tournament->semi_final_matches = json_encode($find_matches);

        } else {
            $tournament->semi_final_matches = json_encode($semi_final_matches_array);
        }

        $tournament->save();

        Session::flash('success', 'Players Assigned to Match Successfully !');
        return redirect()->back();

    }

    public function submit_semi_final_result_one(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m1_s1' => 'required',
            'p1_m1_s2' => 'required',
            'p2_m1_s1' => 'required',
            'p2_m1_s2' => 'required',
        ]);

        $semi_final_results_array = [];


        if ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2) {

            $p1_m1_total = 2;
            $p2_m1_total = 0;

        } elseif ($request->p2_m1_s1 > $request->p1_m1_s1 && $request->p2_m1_s2 > $request->p1_m1_s2) {

            $p1_m1_total = 0;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        }


        $semi_final_results_array['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
        $semi_final_results_array['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
        $semi_final_results_array['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
        $semi_final_results_array['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

        $semi_final_results_array['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
        $semi_final_results_array['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
        $semi_final_results_array['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
        $semi_final_results_array['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 

        $rslt_chk = [$request->p1_m1, $request->p2_m1];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->semi_final_results) {
            
            $find_results = json_decode($tournament->semi_final_results, true);
            if(array_key_exists('match_1', $find_results)) {
                unset($find_results['match_1']);
            }

            $find_results['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
            $find_results['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
            $find_results['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
            $find_results['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

            $find_results['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
            $find_results['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
            $find_results['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
            $find_results['match_1'][$request->p2_m1]['total'] = $p2_m1_total; 
            $tournament->semi_final_results = json_encode($find_results);

        } else {
            $tournament->semi_final_results = json_encode($semi_final_results_array);
        }


        $semi_final_status_array = [];
        if($tournament->semi_final_status) {
            
            $find_status = json_decode($tournament->semi_final_status, true);
            if(array_key_exists('match_1', $find_status)) {
                unset($find_status['match_1']);
            }

            $find_status['match_1'] = $request->sem_mat_1_status;
            $tournament->semi_final_status = json_encode($find_status);

        } else {
            $semi_final_status_array['match_1'] = $request->sem_mat_1_status;
            $tournament->semi_final_status = json_encode($semi_final_status_array);
        }

        
        $semi_final_winners_array = [];        
        if($p1_m1_total > $p2_m1_total) {
            $semi_final_winners_array['match_1'] = $request->p1_m1;
        } else {
           $semi_final_winners_array['match_1'] = $request->p2_m1; 
        }

        if($tournament->semi_final_winners) {
            $find_winners = json_decode($tournament->semi_final_winners, true);
            if(array_key_exists('match_1', $find_winners)) {
                unset($find_winners['match_1']);
            }

            if($p1_m1_total > $p2_m1_total) {
                $find_winners['match_1'] = $request->p1_m1;
            } else {
                $find_winners['match_1'] = $request->p2_m1; 
            }
            $tournament->semi_final_winners = json_encode($find_winners);

        } else {
            $tournament->semi_final_winners = json_encode($semi_final_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }

    public function submit_semi_final_result_two(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }
        $this->validate($request, [
            'p1_m2_s1' => 'required',
            'p1_m2_s2' => 'required',
            'p2_m2_s1' => 'required',
            'p2_m2_s2' => 'required',
        ]);

        $semi_final_results_array = [];


        if ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2) {

            $p1_m2_total = 2;
            $p2_m2_total = 0;

        } elseif ($request->p2_m2_s1 > $request->p1_m2_s1 && $request->p2_m2_s2 > $request->p1_m2_s2) {

            $p1_m2_total = 0;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        } elseif ($request->p1_m2_s1 < $request->p2_m2_s1 && $request->p1_m2_s2 > $request->p2_m2_s2 && $request->p1_m2_s3 > $request->p2_m2_s3) {

            $p1_m2_total = 2;
            $p2_m2_total = 1;

        } elseif ($request->p1_m2_s1 > $request->p2_m2_s1 && $request->p1_m2_s2 < $request->p2_m2_s2 && $request->p1_m2_s3 < $request->p2_m2_s3) {

            $p1_m2_total = 1;
            $p2_m2_total = 2;

        }


        $semi_final_results_array['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
        $semi_final_results_array['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
        $semi_final_results_array['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
        $semi_final_results_array['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

        $semi_final_results_array['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
        $semi_final_results_array['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
        $semi_final_results_array['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
        $semi_final_results_array['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 

        $rslt_chk = [$request->p1_m2, $request->p2_m2];
        $chk_rslt = max(array_count_values($rslt_chk));

        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice !');
            return redirect()->back();
        }

        if($tournament->semi_final_results) {
            
            $find_results = json_decode($tournament->semi_final_results, true);
            if(array_key_exists('match_2', $find_results)) {
                unset($find_results['match_2']);
            }

            $find_results['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
            $find_results['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
            $find_results['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
            $find_results['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

            $find_results['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
            $find_results['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
            $find_results['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
            $find_results['match_2'][$request->p2_m2]['total'] = $p2_m2_total; 
            $tournament->semi_final_results = json_encode($find_results);

        } else {
            $tournament->semi_final_results = json_encode($semi_final_results_array);
        }


        $semi_final_status_array = [];
        if($tournament->semi_final_status) {
            
            $find_status = json_decode($tournament->semi_final_status, true);
            if(array_key_exists('match_2', $find_status)) {
                unset($find_status['match_2']);
            }

            $find_status['match_2'] = $request->sem_mat_2_status;
            $tournament->semi_final_status = json_encode($find_status);

        } else {
            $semi_final_status_array['match_2'] = $request->sem_mat_2_status;
            $tournament->semi_final_status = json_encode($semi_final_status_array);
        }

        
        $semi_final_winners_array = [];        
        if($p1_m2_total > $p2_m2_total) {
            $semi_final_winners_array['match_2'] = $request->p1_m2;
        } else {
           $semi_final_winners_array['match_2'] = $request->p2_m2; 
        }

        if($tournament->semi_final_winners) {
            $find_winners = json_decode($tournament->semi_final_winners, true);
            if(array_key_exists('match_2', $find_winners)) {
                unset($find_winners['match_2']);
            }

            if($p1_m2_total > $p2_m2_total) {
                $find_winners['match_2'] = $request->p1_m2;
            } else {
                $find_winners['match_2'] = $request->p2_m2; 
            }
            $tournament->semi_final_winners = json_encode($find_winners);

        } else {
            $tournament->semi_final_winners = json_encode($semi_final_winners_array);
        }

        $tournament->save();
        Session::flash('success', 'Result Published & Winners Selected Successfully !');
        return redirect()->back();
    }


    public function submit_semifinal_matches(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'sem_start' => 'required',
            'sem_end'   => 'required',
            'sem_mat_1_player_1' => 'required',
            'sem_mat_1_player_2' => 'required',
            'sem_mat_2_player_1' => 'required',
            'sem_mat_2_player_2' => 'required',
        ]);


        $semi_final_deadline_array = [];
        $semi_final_matches_array = [];
        $semi_final_chk_players  = [$request->sem_mat_1_player_1, $request->sem_mat_1_player_2, $request->sem_mat_2_player_1, $request->sem_mat_2_player_2];

        $semi_final_deadline_array['start'] = $request->sem_start;
        $semi_final_deadline_array['end'] = $request->sem_end; 
        $tournament->semi_final_deadline = json_encode($semi_final_deadline_array);

        if ($tournament->tree_size == 8) {
            
            if($request->sem_mat_1_player_1 && $request->sem_mat_1_player_2) {
                $semi_final_matches_array['match_1'] = $request->sem_mat_1_player_1 . ' VS ' . $request->sem_mat_1_player_2;
            }

            if($request->sem_mat_2_player_1 && $request->sem_mat_2_player_2) {
                $semi_final_matches_array['match_2'] = $request->sem_mat_2_player_1 . ' VS ' . $request->sem_mat_2_player_2;
            }
        }

        $chk_players = max(array_count_values($semi_final_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice!');
            return redirect()->back();
        }

        $tournament->semi_final_matches = json_encode($semi_final_matches_array);

        $tournament->save();

        Session::flash('success', 'Players Assigned to Semi Final Successfully!');
        return redirect()->back();

    }

    public function submit_semifinal_results(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        if($tournament->tree_size == 8) {
            $this->validate($request, [
                'p1_m1_s1' => 'required',
                'p1_m1_s2' => 'required',
                'p2_m1_s1' => 'required',
                'p2_m1_s2' => 'required',

                'p1_m2_s1' => 'required',
                'p1_m2_s2' => 'required',
                'p2_m2_s1' => 'required',
                'p2_m2_s2' => 'required',
            ]);

            $semi_final_results_array = [];

            $semi_final_results_array['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
            $semi_final_results_array['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
            $semi_final_results_array['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
            $p1_m1_total = $request->p1_m1_s1 + $request->p1_m1_s2 + $request->p1_m1_s3;
            $semi_final_results_array['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

            $semi_final_results_array['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
            $semi_final_results_array['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
            $semi_final_results_array['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
            $p2_m1_total = $request->p2_m1_s1 + $request->p2_m1_s2 + $request->p2_m1_s3;
            $semi_final_results_array['match_1'][$request->p2_m1]['total'] = $p2_m1_total;

            $semi_final_results_array['match_2'][$request->p1_m2]['set_1'] = $request->p1_m2_s1;
            $semi_final_results_array['match_2'][$request->p1_m2]['set_2'] = $request->p1_m2_s2;
            $semi_final_results_array['match_2'][$request->p1_m2]['set_3'] = $request->p1_m2_s3;
            $p1_m2_total = $request->p1_m2_s1 + $request->p1_m2_s2 + $request->p1_m2_s3;
            $semi_final_results_array['match_2'][$request->p1_m2]['total'] = $p1_m2_total;

            $semi_final_results_array['match_2'][$request->p2_m2]['set_1'] = $request->p2_m2_s1;       
            $semi_final_results_array['match_2'][$request->p2_m2]['set_2'] = $request->p2_m2_s2;
            $semi_final_results_array['match_2'][$request->p2_m2]['set_3'] = $request->p2_m2_s3;
            $p2_m2_total = $request->p2_m2_s1 + $request->p2_m2_s2 + $request->p2_m2_s3;
            $semi_final_results_array['match_2'][$request->p2_m2]['total'] = $p2_m2_total;

            
            $semi_final_rslt_chk = [$request->p1_m1, $request->p1_m2, $request->p2_m1, $request->p2_m2];


            $chk_rslt = max(array_count_values($semi_final_rslt_chk));
            
            if($chk_rslt > 1)
            {
                Session::flash('error', '1 Player Result Can not be Announced Twice !');
                return redirect()->back();
            }

            $tournament->semi_final_results = json_encode($semi_final_results_array);

            $semi_final_winners_array = [];
            
            if($p1_m1_total > $p2_m1_total) {
                $semi_final_winners_array['match_1'] = $request->p1_m1;
            } else {
               $semi_final_winners_array['match_1'] = $request->p2_m1; 
            }

            if($p1_m2_total > $p2_m2_total) {
                $semi_final_winners_array['match_2'] = $request->p1_m2;
            } else {
               $semi_final_winners_array['match_2'] = $request->p2_m2; 
            }


            $tournament->semi_final_winners = json_encode($semi_final_winners_array);

            $tournament->save();

            Session::flash('success', 'Players Result Published & Winners Selected Successfully !');
            return redirect()->back();

        }

    }

    public function submit_semifinal_winners(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

            
        $this->validate($request, [
            'sem_mat_1_winner' => 'required',
            'sem_mat_2_winner' => 'required',
        ]);


        $semi_final_winners_array = [];
        $semi_final_wnr_chk = [$request->sem_mat_1_winner, $request->sem_mat_2_winner];

        
        if($request->sem_mat_1_winner) {
            $semi_final_winners_array['match_1'] = $request->sem_mat_1_winner;
        }

        if($request->sem_mat_2_winner) {
            $semi_final_winners_array['match_2'] = $request->sem_mat_2_winner;
        }


        $chk_players = max(array_count_values($semi_final_wnr_chk));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Announced as Winner Twice !');
            return redirect()->back();
        }

        $tournament->semi_final_winners = json_encode($semi_final_winners_array);

        $tournament->save();

        Session::flash('success', 'Players Announced as Winners Successfully!');
        return redirect()->back();


    }


    // FINAL
    public function submit_final_matches(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'final_mat_1_player_1' => 'required',
            'final_mat_1_player_2' => 'required',
        ]);


        $final_deadline_array = [];
        $final_matches_array = [];
        $final_chk_players  = [$request->final_mat_1_player_1, $request->final_mat_1_player_2];

        $final_deadline_array['start'] = $request->final_start;
        $final_deadline_array['end'] = $request->final_end; 
        $tournament->final_deadline = json_encode($final_deadline_array);

        
        if($request->final_mat_1_player_1 && $request->final_mat_1_player_2) {
            $final_matches_array['match_1'] = $request->final_mat_1_player_1 . ' VS ' . $request->final_mat_1_player_2;
        }


        $chk_players = max(array_count_values($final_chk_players));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Assigned Twice!');
            return redirect()->back();
        }

        $tournament->final_matches = json_encode($final_matches_array);

        $tournament->save();

        Session::flash('success', 'Players Assigned to Final Successfully!');
        return redirect()->back();

    }

    public function submit_final_results(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        
        $this->validate($request, [
            'p1_m1_s1' => 'required',
            'p1_m1_s2' => 'required',
            'p2_m1_s1' => 'required',
            'p2_m1_s2' => 'required',
        ]);

        $final_results_array = [];


        if ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2) {

            $p1_m1_total = 2;
            $p2_m1_total = 0;

        } elseif ($request->p2_m1_s1 > $request->p1_m1_s1 && $request->p2_m1_s2 > $request->p1_m1_s2) {

            $p1_m1_total = 0;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        } elseif ($request->p1_m1_s1 < $request->p2_m1_s1 && $request->p1_m1_s2 > $request->p2_m1_s2 && $request->p1_m1_s3 > $request->p2_m1_s3) {

            $p1_m1_total = 2;
            $p2_m1_total = 1;

        } elseif ($request->p1_m1_s1 > $request->p2_m1_s1 && $request->p1_m1_s2 < $request->p2_m1_s2 && $request->p1_m1_s3 < $request->p2_m1_s3) {

            $p1_m1_total = 1;
            $p2_m1_total = 2;

        }


        $final_results_array['match_1'][$request->p1_m1]['set_1'] = $request->p1_m1_s1;
        $final_results_array['match_1'][$request->p1_m1]['set_2'] = $request->p1_m1_s2;
        $final_results_array['match_1'][$request->p1_m1]['set_3'] = $request->p1_m1_s3;
        $final_results_array['match_1'][$request->p1_m1]['total'] = $p1_m1_total;

        $final_results_array['match_1'][$request->p2_m1]['set_1'] = $request->p2_m1_s1;       
        $final_results_array['match_1'][$request->p2_m1]['set_2'] = $request->p2_m1_s2;
        $final_results_array['match_1'][$request->p2_m1]['set_3'] = $request->p2_m1_s3;
        $final_results_array['match_1'][$request->p2_m1]['total'] = $p2_m1_total;

        
        $final_rslt_chk = [$request->p1_m1, $request->p2_m1];


        $chk_rslt = max(array_count_values($final_rslt_chk));
        
        if($chk_rslt > 1)
        {
            Session::flash('error', '1 Player Result Can not be Announced Twice!');
            return redirect()->back();
        }

        
        $tournament->final_results = json_encode($final_results_array);


        $final_status_array = [];
        if($tournament->final_status) {
            
            $find_status = json_decode($tournament->final_status, true);
            if(array_key_exists('match_1', $find_status)) {
                unset($find_status['match_1']);
            }

            $find_status['match_1'] = $request->final_mat_1_status;
            $tournament->final_status = json_encode($find_status);

        } else {
            $final_status_array['match_1'] = $request->final_mat_1_status;
            $tournament->final_status = json_encode($final_status_array);
        }


        $final_winners_array = [];
        if($p1_m1_total > $p2_m1_total) {
            $final_winners_array['match_1'] = $request->p1_m1;
        } else {
           $final_winners_array['match_1'] = $request->p2_m1; 
        }

        
        $tournament->final_winners = json_encode($final_winners_array);

        $tournament->save();

        Session::flash('success', 'Final Result Published & Champion Selected Successfully !');
        return redirect()->back();


    }

    public function submit_final_winners(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

            
        $this->validate($request, [
            'final_mat_1_winner' => 'required',
        ]);


        $final_winners_array = [];
        $final_wnr_chk = [$request->final_mat_1_winner];

        
        if($request->final_mat_1_winner) {
            $final_winners_array['match_1'] = $request->final_mat_1_winner;
        }


        $chk_players = max(array_count_values($final_wnr_chk));
        
        if($chk_players > 1)
        {
            Session::flash('error', '1 Player Can not be Announced as Winner Twice !');
            return redirect()->back();
        }

        $tournament->final_winners = json_encode($final_winners_array);

        $tournament->save();

        Session::flash('success', 'Player Announced as Champion Successfully!');
        return redirect()->back();


    }


    public function submit_round_one_retire(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou1_mat_retire_player' => 'required',
            'rou1_mat_retire'        => 'required',
        ]);


        $retires_array = [];

        if($tournament->round_one_retires) {
            $find_retires = json_decode($tournament->round_one_retires, true);
            
            if(array_key_exists($request->rou1_mat_retire, $find_retires)) {
                unset($find_retires[$request->rou1_mat_retire]);
                $find_retires[$request->rou1_mat_retire] = $request->rou1_mat_retire_player;
            } else {
                $find_retires[$request->rou1_mat_retire] = $request->rou1_mat_retire_player;
            }

            $tournament->round_one_retires = json_encode($find_retires);

        } else {
            $retires_array[$request->rou1_mat_retire] = $request->rou1_mat_retire_player;
            $tournament->round_one_retires = json_encode($retires_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Retired Successfully!');
        return redirect()->back();
    }


    public function submit_round_two_retire(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou2_mat_retire_player' => 'required',
            'rou2_mat_retire'        => 'required',
        ]);


        $retires_array = [];

        if($tournament->round_two_retires) {
            $find_retires = json_decode($tournament->round_two_retires, true);
            
            if(array_key_exists($request->rou2_mat_retire, $find_retires)) {
                unset($find_retires[$request->rou2_mat_retire]);
                $find_retires[$request->rou2_mat_retire] = $request->rou2_mat_retire_player;
            } else {
                $find_retires[$request->rou2_mat_retire] = $request->rou2_mat_retire_player;
            }

            $tournament->round_two_retires = json_encode($find_retires);

        } else {
            $retires_array[$request->rou2_mat_retire] = $request->rou2_mat_retire_player;
            $tournament->round_two_retires = json_encode($retires_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Retired Successfully!');
        return redirect()->back();
    }


    public function submit_round_three_retire(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou3_mat_retire_player' => 'required',
            'rou3_mat_retire'        => 'required',
        ]);


        $retires_array = [];

        if($tournament->round_three_retires) {
            $find_retires = json_decode($tournament->round_three_retires, true);
            
            if(array_key_exists($request->rou3_mat_retire, $find_retires)) {
                unset($find_retires[$request->rou3_mat_retire]);
                $find_retires[$request->rou3_mat_retire] = $request->rou3_mat_retire_player;
            } else {
                $find_retires[$request->rou3_mat_retire] = $request->rou3_mat_retire_player;
            }

            $tournament->round_three_retires = json_encode($find_retires);

        } else {
            $retires_array[$request->rou3_mat_retire] = $request->rou3_mat_retire_player;
            $tournament->round_three_retires = json_encode($retires_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Retired Successfully!');
        return redirect()->back();
    }


    public function submit_quarter_final_retire(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'quar_mat_retire_player' => 'required',
            'quar_mat_retire'        => 'required',
        ]);


        $retires_array = [];

        if($tournament->quarter_final_retires) {
            $find_retires = json_decode($tournament->quarter_final_retires, true);
            
            if(array_key_exists($request->quar_mat_retire, $find_retires)) {
                unset($find_retires[$request->quar_mat_retire]);
                $find_retires[$request->quar_mat_retire] = $request->quar_mat_retire_player;
            } else {
                $find_retires[$request->quar_mat_retire] = $request->quar_mat_retire_player;
            }

            $tournament->quarter_final_retires = json_encode($find_retires);

        } else {
            $retires_array[$request->quar_mat_retire] = $request->quar_mat_retire_player;
            $tournament->quarter_final_retires = json_encode($retires_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Retired Successfully!');
        return redirect()->back();
    }


    public function submit_semi_final_retire(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'sem_mat_retire_player' => 'required',
            'sem_mat_retire'        => 'required',
        ]);


        $retires_array = [];

        if($tournament->semi_final_retires) {
            $find_retires = json_decode($tournament->semi_final_retires, true);
            
            if(array_key_exists($request->sem_mat_retire, $find_retires)) {
                unset($find_retires[$request->sem_mat_retire]);
                $find_retires[$request->sem_mat_retire] = $request->sem_mat_retire_player;
            } else {
                $find_retires[$request->sem_mat_retire] = $request->sem_mat_retire_player;
            }

            $tournament->semi_final_retires = json_encode($find_retires);

        } else {
            $retires_array[$request->sem_mat_retire] = $request->sem_mat_retire_player;
            $tournament->semi_final_retires = json_encode($retires_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Retired Successfully!');
        return redirect()->back();
    }


    public function submit_final_retire(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'final_mat_retire_player' => 'required',
            'final_mat_retire'        => 'required',
        ]);


        $retires_array = [];

        if($tournament->final_retires) {
            $find_retires = json_decode($tournament->final_retires, true);
            
            if(array_key_exists($request->final_mat_retire, $find_retires)) {
                unset($find_retires[$request->final_mat_retire]);
                $find_retires[$request->final_mat_retire] = $request->final_mat_retire_player;
            } else {
                $find_retires[$request->final_mat_retire] = $request->final_mat_retire_player;
            }

            $tournament->final_retires = json_encode($find_retires);

        } else {
            $retires_array[$request->final_mat_retire] = $request->final_mat_retire_player;
            $tournament->final_retires = json_encode($retires_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Retired Successfully!');
        return redirect()->back();
    }


    public function submit_round_one_auto_selection(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou1_mat_auto_player' => 'required',
            'rou1_mat_auto'        => 'required',
        ]);


        $auto_selection_array = [];
        // $auto_selection_array[$request->rou1_mat_auto] = $request->rou1_mat_auto_player;
        // $tournament->round_one_auto_selection = json_encode($auto_selection_array);


        if($tournament->round_one_auto_selection) {
            $find_selection = json_decode($tournament->round_one_auto_selection, true);
            
            if(array_key_exists($request->rou1_mat_auto, $find_selection)) {
                unset($find_selection[$request->rou1_mat_auto]);
                $find_selection[$request->rou1_mat_auto] = $request->rou1_mat_auto_player;
            } else {
                $find_selection[$request->rou1_mat_auto] = $request->rou1_mat_auto_player;
            }

            $tournament->round_one_auto_selection = json_encode($find_selection);

        } else {
            $auto_selection_array[$request->rou1_mat_auto] = $request->rou1_mat_auto_player;
            $tournament->round_one_auto_selection = json_encode($auto_selection_array);
        }

        
        $round_one_winners_array = [];
        
        if($tournament->round_one_winners) {
            $find_winners = json_decode($tournament->round_one_winners, true);
            
            if(array_key_exists($request->rou1_mat_auto, $find_winners)) {
                unset($find_winners[$request->rou1_mat_auto]);
                $find_winners[$request->rou1_mat_auto] = $request->rou1_mat_auto_player;
            } else {
                $find_winners[$request->rou1_mat_auto] = $request->rou1_mat_auto_player;
            }

            $tournament->round_one_winners = json_encode($find_winners);

        } else {
            $round_one_winners_array[$request->rou1_mat_auto] = $request->rou1_mat_auto_player;
            $tournament->round_one_winners = json_encode($round_one_winners_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Auto-Selected Successfully!');
        return redirect()->back();
    }

    public function submit_round_two_auto_selection(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou2_mat_auto_player' => 'required',
            'rou2_mat_auto'        => 'required',
        ]);


        $auto_selection_array = [];
        $auto_selection_array[$request->rou2_mat_auto] = $request->rou2_mat_auto_player;
        $tournament->round_two_auto_selection = json_encode($auto_selection_array);

        
        $round_two_winners_array = [];        
        
        if($tournament->round_two_winners) {
            $find_winners = json_decode($tournament->round_two_winners, true);
            
            if(array_key_exists($request->rou2_mat_auto, $find_winners)) {
                unset($find_winners[$request->rou2_mat_auto]);
            } else {
                $find_winners[$request->rou2_mat_auto] = $request->rou2_mat_auto_player;
            }

            $tournament->round_two_winners = json_encode($find_winners);

        } else {
            $round_two_winners_array[$request->rou2_mat_auto] = $request->rou2_mat_auto_player;
            $tournament->round_two_winners = json_encode($round_two_winners_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Auto-Selected Successfully!');
        return redirect()->back();
    }

    public function submit_round_three_auto_selection(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'rou3_mat_auto_player' => 'required',
            'rou3_mat_auto'        => 'required',
        ]);


        $auto_selection_array = [];
        $auto_selection_array[$request->rou3_mat_auto] = $request->rou3_mat_auto_player;
        $tournament->round_three_auto_selection = json_encode($auto_selection_array);

        
        $round_three_winners_array = [];        
        
        if($tournament->round_three_winners) {
            $find_winners = json_decode($tournament->round_three_winners, true);
            
            if(array_key_exists($request->rou3_mat_auto, $find_winners)) {
                unset($find_winners[$request->rou3_mat_auto]);
            } else {
                $find_winners[$request->rou3_mat_auto] = $request->rou3_mat_auto_player;
            }

            $tournament->round_three_winners = json_encode($find_winners);

        } else {
            $round_three_winners_array[$request->rou3_mat_auto] = $request->rou3_mat_auto_player;
            $tournament->round_three_winners = json_encode($round_three_winners_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Auto-Selected Successfully!');
        return redirect()->back();
    }

    public function submit_quarter_final_auto_selection(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'quar_mat_auto_player' => 'required',
            'quar_mat_auto'        => 'required',
        ]);


        $auto_selection_array = [];
        $auto_selection_array[$request->quar_mat_auto] = $request->quar_mat_auto_player;
        $tournament->quarter_final_auto_selection = json_encode($auto_selection_array);

        
        $quarter_final_winners_array = [];        
        
        if($tournament->quarter_final_winners) {
            $find_winners = json_decode($tournament->quarter_final_winners, true);
            
            if(array_key_exists($request->quar_mat_auto, $find_winners)) {
                unset($find_winners[$request->quar_mat_auto]);
            } else {
                $find_winners[$request->quar_mat_auto] = $request->quar_mat_auto_player;
            }

            $tournament->quarter_final_winners = json_encode($find_winners);

        } else {
            $quarter_final_winners_array[$request->quar_mat_auto] = $request->quar_mat_auto_player;
            $tournament->quarter_final_winners = json_encode($quarter_final_winners_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Auto-Selected Successfully!');
        return redirect()->back();
    }

    public function submit_semi_final_auto_selection(Request $request, $id)
    {
        if ($request->chk_type) {
            if ($request->chk_type == 'League') {
                $tournament = League::findOrFail($id);
            } else {
                $tournament = Tournament::findOrFail($id);
            }
        } else {
            $tournament = Tournament::findOrFail($id);
        }

        $this->validate($request, [
            'sem_mat_auto_player' => 'required',
            'sem_mat_auto'        => 'required',
        ]);


        $auto_selection_array = [];
        $auto_selection_array[$request->sem_mat_auto] = $request->sem_mat_auto_player;
        $tournament->semi_final_auto_selection = json_encode($auto_selection_array);

        
        $semi_final_winners_array = [];        
        
        if($tournament->semi_final_winners) {
            $find_winners = json_decode($tournament->semi_final_winners, true);
            
            if(array_key_exists($request->sem_mat_auto, $find_winners)) {
                unset($find_winners[$request->sem_mat_auto]);
            } else {
                $find_winners[$request->sem_mat_auto] = $request->sem_mat_auto_player;
            }

            $tournament->semi_final_winners = json_encode($find_winners);

        } else {
            $semi_final_winners_array[$request->sem_mat_auto] = $request->sem_mat_auto_player;
            $tournament->semi_final_winners = json_encode($semi_final_winners_array);
        }


        $tournament->save();

        Session::flash('success', 'Player Announced as Auto-Selected Successfully!');
        return redirect()->back();
    }

    public function submit_group_retires(Request $request, $grp_word, $id)
    {
        $league = League::findOrFail($id);
        
        for($i = 1; $i < $league->group_size + 1; $i++) {
            $convert_word = strtolower(ucwords((new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT))->format($i)));

            if($convert_word == $grp_word) {
                
                $this->validate($request, [
                    'group_'.$grp_word.'_mat_retire' => 'required',
                    'group_'.$grp_word.'_mat_retire_player' => 'required',
                ]);

                $retires_array = [];

                if($league->{"group_".$grp_word."_retires"}) {
                    $find_retires = json_decode($league->{"group_".$grp_word."_retires"}, true);
                    
                    if(array_key_exists($request->{"group_".$grp_word."_mat_retire"}, $find_retires)) {
                        unset($find_retires[$request->{"group_".$grp_word."_mat_retire"}]);
                        $find_retires[$request->{"group_".$grp_word."_mat_retire"}] = $request->{"group_".$grp_word."_mat_retire_player"};
                    } else {
                        $find_retires[$request->{"group_".$grp_word."_mat_retire"}] = $request->{"group_".$grp_word."_mat_retire_player"};
                    }

                    $league->{"group_".$grp_word."_retires"} = json_encode($find_retires);

                } else {
                    $retires_array[$request->{"group_".$grp_word."_mat_retire"}] = $request->{"group_".$grp_word."_mat_retire_player"};
                    $league->{"group_".$grp_word."_retires"} = json_encode($retires_array);
                }


                $league->save();

                Session::flash('success', 'Player Announced as Retired Successfully!');
                return redirect()->back();
            }
        }

    }
    
}
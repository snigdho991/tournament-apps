<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tournament;
use App\Models\League;
use App\Models\Payment;
use App\Models\User;

use Session;
use Auth;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function get_tournament_players($id)
    {
        $tour = Tournament::findOrFail($id);
        $players = [];

        $payments = Payment::all();

        $i = 1;
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
                if($tour->id == $value) {
                    $user = User::findOrFail($payment->user_id);
                    
                    if(Auth::check()) {
                        if(Auth::user()->hasRole('Administrator')) {
                            array_push($players, ($i).'. '.$user->name.' - '.$user->phone.'<br><br>');
                        } else {
                            array_push($players, ($i).'. '.$user->name.'<br><br>');
                        }
                    }
                    
                    $i++;  
                }

            }

        }

        return $players;

    }

    public function get_league_players($id)
    {
        $leag = League::findOrFail($id);
        $players = [];

        $payments = Payment::all();

        $i = 1;
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
                if($leag->id == $value) {
                    $user = User::findOrFail($payment->user_id);

                    if(Auth::check()) {
                        if(Auth::user()->hasRole('Administrator')) {
                            array_push($players, ($i).'. '.$user->name.' - '.$user->phone.'<br><br>');
                        } else {
                            array_push($players, ($i).'. '.$user->name.'<br><br>');
                        }
                    }

                    $i++;  
                }

            }

        }

        return $players;
    }

    public function get_tournament_supervisor($id)
    {
        $tournament = Tournament::findOrFail($id);

        return $tournament;

    }

    public function get_league_supervisor($id)
    {
        $league = League::findOrFail($id);
        
        return $league;

    }

}

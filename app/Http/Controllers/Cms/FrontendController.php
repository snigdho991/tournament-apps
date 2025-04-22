<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tournament;
use App\Models\League;
use App\Models\Ranking;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;

class FrontendController extends Controller
{
    public function frontend_index()
    {
        $settings = Settings::findOrFail(1);

        $tournaments = Tournament::where('draw_status', $settings->tournaments_open_for)->whereYear('start', date('Y'))->get()->sortBy('start')->groupBy(function($d) {
                        return Carbon::parse($d->start)->format('Y');
                    });

        $leagues = League::where('draw_status', $settings->leagues_open_for)->whereYear('start', date('Y'))->get()->sortBy('start')->groupBy(function($d) {
                    return Carbon::parse($d->start)->format('Y');
                });

        return view('frontend.index', compact('tournaments', 'leagues'));
    }

    public function frontend_faq()
    {
        return view('frontend.faq');
    }

    public function frontend_rankings()
    {
        $user_rankings = User::where('is_current', 'Yes')->orderBy('total_points', 'asc')->get();
        $settings = Settings::findOrFail(1);
        return view('frontend.rankings', compact('user_rankings', 'settings'));
    }
}

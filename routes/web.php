<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Cms\RoleController;
use App\Http\Controllers\Ums\AdministratorToolsController;
use App\Http\Controllers\Ums\PlayerToolsController;
use App\Http\Controllers\Cms\ThemeController;
use App\Http\Controllers\Ums\ProfileController;
use App\Http\Controllers\Cms\ApiController;
use App\Http\Controllers\Cms\FrontendController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [FrontendController::class, 'frontend_index'])->name('frontend.index');
Route::get('/faq', [FrontendController::class, 'frontend_faq'])->name('frontend.bg.faq');

Route::get('/get/tournament/players/{id}', [ApiController::class, 'get_tournament_players'])->name('get.tournament.players');
Route::get('/get/league/players/{id}', [ApiController::class, 'get_league_players'])->name('get.league.players');

Route::get('/get/tournament/supervisor/{id}', [ApiController::class, 'get_tournament_supervisor'])->name('get.tournament.supervisor');
Route::get('/get/league/supervisor/{id}', [ApiController::class, 'get_league_supervisor'])->name('get.league.supervisor');

// Route::get('/generate-role', [RoleController::class, 'generate_role'])->name('generate.role');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
	Route::post('/save-theme', [ThemeController::class, 'select_theme'])->name('select.theme');
	Route::post('/save/basic-info', [ProfileController::class, 'save_basic_info'])->name('save.basic.info');
	Route::post('/save/change-password', [ProfileController::class, 'change_auth_password'])->name('change.auth.password');
	
	Route::get('/rankings', [FrontendController::class, 'frontend_rankings'])->name('frontend.bg.rankings');
		
	Route::group(['prefix' => 'administrator'], function(){
		Route::get('/dashboard', [AdministratorToolsController::class, 'admin_dashboard'])->name('admin.dashboard');
		Route::get('/head-to-head', [AdministratorToolsController::class, 'head_to_head'])->name('head.to.head');
		Route::get('/head-to-head/find', [AdministratorToolsController::class, 'head_to_head_find'])->name('head.to.head.find');

		Route::get('/players-list', [AdministratorToolsController::class, 'players_list'])->name('players.list');
		Route::get('/approval-list', [AdministratorToolsController::class, 'approval_list'])->name('approval.list');
		Route::post('/approve-player', [AdministratorToolsController::class, 'approve_player'])->name('approve.player');
		Route::post('/decline-player', [AdministratorToolsController::class, 'decline_player'])->name('decline.player');
		Route::get('/edit-player/{id}', [AdministratorToolsController::class, 'edit_player'])->name('edit.player');
		Route::post('/update-player/{id}', [AdministratorToolsController::class, 'update_player'])->name('update.player');

		
		Route::get('/add-new-tournament', [AdministratorToolsController::class, 'add_new_tournament'])->name('add.new.tournament');
		Route::post('/store-tournament', [AdministratorToolsController::class, 'store_tournament'])->name('store.tournament');
		Route::get('/all-tournaments', [AdministratorToolsController::class, 'all_tournaments'])->name('all.tournaments');
		Route::get('/tournament/edit/{id}', [AdministratorToolsController::class, 'edit_tournament'])->name('edit.tournament');
		Route::post('/tournament/update/{id}', [AdministratorToolsController::class, 'update_tournament'])->name('update.tournament');
		Route::get('/tournament/delete/{id}', [AdministratorToolsController::class, 'delete_tournament'])->name('delete.tournament');


		Route::get('/add-new-league', [AdministratorToolsController::class, 'add_new_league'])->name('add.new.league');
		Route::post('/store-league', [AdministratorToolsController::class, 'store_league'])->name('store.league');
		Route::get('/all-leagues', [AdministratorToolsController::class, 'all_leagues'])->name('all.leagues');
		Route::get('/league/edit/{id}', [AdministratorToolsController::class, 'edit_league'])->name('edit.league');
		Route::post('/league/update/{id}', [AdministratorToolsController::class, 'update_league'])->name('update.league');
		Route::get('/league/delete/{id}', [AdministratorToolsController::class, 'delete_league'])->name('delete.league');


		Route::get('/participations/pending/tournaments', [AdministratorToolsController::class, 'admin_pending_tournaments_participations'])->name('admin.pending.tournaments.participations');
		Route::get('/participations/paid/tournaments', [AdministratorToolsController::class, 'admin_paid_tournaments_participations'])->name('admin.paid.tournaments.participations');
		Route::get('/participations/paid/tournaments/1st-draw', [AdministratorToolsController::class, 'admin_paid_tournaments_participations_first'])->name('admin.paid.tournaments.participations.first');
		Route::get('/participations/paid/tournaments/2nd-draw', [AdministratorToolsController::class, 'admin_paid_tournaments_participations_second'])->name('admin.paid.tournaments.participations.second');
		Route::get('/participations/paid/tournaments/3rd-draw', [AdministratorToolsController::class, 'admin_paid_tournaments_participations_third'])->name('admin.paid.tournaments.participations.third');
		Route::get('/participations/paid/tournaments/4th-draw', [AdministratorToolsController::class, 'admin_paid_tournaments_participations_fourth'])->name('admin.paid.tournaments.participations.fourth');
		Route::get('/participations/declined/tournaments', [AdministratorToolsController::class, 'admin_declined_tournaments_participations'])->name('admin.declined.tournaments.participations');

		Route::get('/full-members/preferences/tournaments', [AdministratorToolsController::class, 'admin_full_tournaments_participations'])->name('admin.full.tournaments.participations');
		Route::get('/full-members/preferences/tournaments/1st-draw', [AdministratorToolsController::class, 'admin_full_tournaments_participations_first'])->name('admin.full.tournaments.participations.first');
		Route::get('/full-members/preferences/tournaments/2nd-draw', [AdministratorToolsController::class, 'admin_full_tournaments_participations_second'])->name('admin.full.tournaments.participations.second');
		Route::get('/full-members/preferences/tournaments/3rd-draw', [AdministratorToolsController::class, 'admin_full_tournaments_participations_third'])->name('admin.full.tournaments.participations.third');
		Route::get('/full-members/preferences/tournaments/4th-draw', [AdministratorToolsController::class, 'admin_full_tournaments_participations_fourth'])->name('admin.full.tournaments.participations.fourth');


		Route::get('/full-members/preferences/leagues', [AdministratorToolsController::class, 'admin_full_leagues_participations'])->name('admin.full.leagues.participations');


		Route::get('/participations/pending/leagues', [AdministratorToolsController::class, 'admin_pending_leagues_participations'])->name('admin.pending.leagues.participations');
		Route::get('/participations/paid/leagues', [AdministratorToolsController::class, 'admin_paid_leagues_participations'])->name('admin.paid.leagues.participations');
		Route::get('/participations/declined/leagues', [AdministratorToolsController::class, 'admin_declined_leagues_participations'])->name('admin.declined.leagues.participations');


		Route::get('/get/single-tournament/{id}', [AdministratorToolsController::class, 'get_single_tournament'])->name('get.single.tournament');
		Route::get('/get/single-league/{id}', [AdministratorToolsController::class, 'get_single_league'])->name('get.single.league');

		
		Route::post('/approve/tournament/participation/{id}', [AdministratorToolsController::class, 'approve_tournament_participation'])->name('approve.tournament.participation');
		Route::post('/decline/tournament/participation/{id}', [AdministratorToolsController::class, 'decline_tournament_participation'])->name('decline.tournament.participation');


		Route::post('/approve/league/participation/{id}', [AdministratorToolsController::class, 'approve_league_participation'])->name('approve.league.participation');
		Route::post('/decline/league/participation/{id}', [AdministratorToolsController::class, 'decline_league_participation'])->name('decline.league.participation');


		Route::get('/membership/pending', [AdministratorToolsController::class, 'admin_pending_membership'])->name('admin.pending.membership');
		Route::get('/membership/approved', [AdministratorToolsController::class, 'admin_approved_membership'])->name('admin.approved.membership');
		Route::get('/membership/declined', [AdministratorToolsController::class, 'admin_declined_membership'])->name('admin.declined.membership');
		Route::post('/delete-membership/{id}', [AdministratorToolsController::class, 'delete_membership'])->name('delete.membership');
		Route::post('/approve/membership/{id}', [AdministratorToolsController::class, 'approve_membership'])->name('approve.membership');
		Route::post('/decline/membership/{id}', [AdministratorToolsController::class, 'decline_membership'])->name('decline.membership');

		Route::get('/app-settings', [AdministratorToolsController::class, 'get_settings'])->name('get.settings');
		Route::post('/update/settings', [AdministratorToolsController::class, 'update_settings'])->name('update.settings');

		
		Route::post('/change/tournament/{id}', [AdministratorToolsController::class, 'change_tournaments'])->name('change.tournaments');
		Route::post('/change/league/{id}', [AdministratorToolsController::class, 'change_leagues'])->name('change.leagues');

		Route::get('/send-emails', [AdministratorToolsController::class, 'send_emails'])->name('send.emails');
		Route::post('/send-mail/all-players', [AdministratorToolsController::class, 'send_mail_to_all_players'])->name('send.mail.to.all.players');
		Route::post('/send-mail/membership-players', [AdministratorToolsController::class, 'send_mail_to_all_fullmembers'])->name('send.mail.to.all.fullmembers');

		Route::post('/clear-mail/all-players', [AdministratorToolsController::class, 'clear_mail_to_all_players'])->name('clear.mail.to.all.players');
		Route::post('/clear-mail/membership-players', [AdministratorToolsController::class, 'clear_mail_to_all_fullmembers'])->name('clear.mail.to.all.fullmembers');


		// TREE LEAGUE
		Route::post('/tree/league/{id}', [AdministratorToolsController::class, 'tree_league'])->name('tree.league');

		// LEAGUE DRAW
		Route::get('/league/draw/{id}', [AdministratorToolsController::class, 'draw_league'])->name('draw.league');
		Route::get('/league/group/{id}', [AdministratorToolsController::class, 'group_league'])->name('group.league');
		Route::post('/league/store-group/{id}', [AdministratorToolsController::class, 'store_league_group'])->name('store.league.group');
		Route::post('/submit/group-one/league/{id}', [AdministratorToolsController::class, 'submit_group_one_league'])->name('submit.group.1.league');
		Route::post('/submit/group-two/league/{id}', [AdministratorToolsController::class, 'submit_group_two_league'])->name('submit.group.2.league');
		Route::post('/submit/group-three/league/{id}', [AdministratorToolsController::class, 'submit_group_three_league'])->name('submit.group.3.league');
		Route::post('/submit/group-four/league/{id}', [AdministratorToolsController::class, 'submit_group_four_league'])->name('submit.group.4.league');
		Route::post('/submit/group-five/league/{id}', [AdministratorToolsController::class, 'submit_group_five_league'])->name('submit.group.5.league');
		Route::post('/submit/group-six/league/{id}', [AdministratorToolsController::class, 'submit_group_six_league'])->name('submit.group.6.league');
		Route::post('/submit/group-seven/league/{id}', [AdministratorToolsController::class, 'submit_group_seven_league'])->name('submit.group.7.league');
		Route::post('/submit/group-eight/league/{id}', [AdministratorToolsController::class, 'submit_group_eight_league'])->name('submit.group.8.league');
		Route::post('/submit/group-nine/league/{id}', [AdministratorToolsController::class, 'submit_group_nine_league'])->name('submit.group.9.league');
		Route::post('/submit/group-ten/league/{id}', [AdministratorToolsController::class, 'submit_group_ten_league'])->name('submit.group.10.league');

		Route::post('/submit/deadlines/league/{id}', [AdministratorToolsController::class, 'submit_league_deadlines'])->name('submit.league.deadlines');
        
        Route::get('/draw/league/{id}/8-players', [AdministratorToolsController::class, 'draw_league_eight_players'])->name('draw.league.eight.players');
		Route::get('/draw/league/{id}/16-players', [AdministratorToolsController::class, 'draw_league_sixteen_players'])->name('draw.league.sixteen.players');
		Route::get('/draw/league/{id}/32-players', [AdministratorToolsController::class, 'draw_league_thirtytwo_players'])->name('draw.league.thirtytwo.players');


		// GROUP MATCHES		
		Route::post('/submit/group-one/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_one_matches'])->name('submit.group.one.matches');
		Route::post('/submit/group-two/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_two_matches'])->name('submit.group.two.matches');
		Route::post('/submit/group-three/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_three_matches'])->name('submit.group.three.matches');
		Route::post('/submit/group-four/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_four_matches'])->name('submit.group.four.matches');
		Route::post('/submit/group-five/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_five_matches'])->name('submit.group.five.matches');
		Route::post('/submit/group-six/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_six_matches'])->name('submit.group.six.matches');
		Route::post('/submit/group-seven/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_seven_matches'])->name('submit.group.seven.matches');
		Route::post('/submit/group-eight/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_eight_matches'])->name('submit.group.eight.matches');
		Route::post('/submit/group-nine/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_nine_matches'])->name('submit.group.nine.matches');
		Route::post('/submit/group-ten/match-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_ten_matches'])->name('submit.group.ten.matches');


		// GROUP RESULTS		
		Route::post('/submit/group-one/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_one_results'])->name('submit.group.one.results');
		Route::post('/submit/group-two/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_two_results'])->name('submit.group.two.results');
		Route::post('/submit/group-three/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_three_results'])->name('submit.group.three.results');
		Route::post('/submit/group-four/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_four_results'])->name('submit.group.four.results');
		Route::post('/submit/group-five/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_five_results'])->name('submit.group.five.results');
		Route::post('/submit/group-six/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_six_results'])->name('submit.group.six.results');
		Route::post('/submit/group-seven/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_seven_results'])->name('submit.group.seven.results');
		Route::post('/submit/group-eight/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_eight_results'])->name('submit.group.eight.results');
		Route::post('/submit/group-nine/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_nine_results'])->name('submit.group.nine.results');
		Route::post('/submit/group-ten/result-{match_word}/league/{id}', [AdministratorToolsController::class, 'submit_group_ten_results'])->name('submit.group.ten.results');


		// GROUP RETIRES		
		Route::post('/submit/group-{grp_word}/retires/league/{id}', [AdministratorToolsController::class, 'submit_group_retires'])->name('submit.group.retires');

		
		// TOURNAMENT DRAW
		Route::get('/tournament/draw/{id}', [AdministratorToolsController::class, 'draw_tournament'])->name('draw.tournament');
		Route::get('/tournament/group/{id}', [AdministratorToolsController::class, 'group_tournament'])->name('group.tournament');
		Route::get('/draw/tournament/{id}/8-players', [AdministratorToolsController::class, 'draw_tournament_eight_players'])->name('draw.tournament.eight.players');
		Route::get('/draw/tournament/{id}/16-players', [AdministratorToolsController::class, 'draw_tournament_sixteen_players'])->name('draw.tournament.sixteen.players');
		Route::get('/draw/tournament/{id}/32-players', [AdministratorToolsController::class, 'draw_tournament_thirtytwo_players'])->name('draw.tournament.thirtytwo.players');
		Route::get('/draw/tournament/{id}/64-players', [AdministratorToolsController::class, 'draw_tournament_sixtyfour_players'])->name('draw.tournament.sixtyfour.players');

		// TREE TOURNAMENT
		Route::post('/tree/tournament/{id}', [AdministratorToolsController::class, 'tree_tournament'])->name('tree.tournament');
		Route::post('/submit/deadlines/tournament/{id}', [AdministratorToolsController::class, 'submit_deadlines'])->name('submit.deadlines');

		// AUTO SELECTION
		Route::post('/submit/round-one/auto-selection/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_auto_selection'])->name('submit.round.one.auto.selection');
		Route::post('/submit/round-two/auto-selection/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_auto_selection'])->name('submit.round.two.auto.selection');
		Route::post('/submit/round-three/auto-selection/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_auto_selection'])->name('submit.round.three.auto.selection');
		Route::post('/submit/quarter-final/auto-selection/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_auto_selection'])->name('submit.quarter.final.auto.selection');
		Route::post('/submit/semi-final/auto-selection/tournament/{id}', [AdministratorToolsController::class, 'submit_semi_final_auto_selection'])->name('submit.semi.final.auto.selection');


		// RETIRE
		Route::post('/submit/round-one/retire/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_retire'])->name('submit.round.one.retire');
		Route::post('/submit/round-two/retire/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_retire'])->name('submit.round.two.retire');
		Route::post('/submit/round-three/retire/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_retire'])->name('submit.round.three.retire');
		Route::post('/submit/quarter-final/retire/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_retire'])->name('submit.quarter.final.retire');
		Route::post('/submit/semi-final/retire/tournament/{id}', [AdministratorToolsController::class, 'submit_semi_final_retire'])->name('submit.semi.final.retire');
		Route::post('/submit/final/retire/tournament/{id}', [AdministratorToolsController::class, 'submit_final_retire'])->name('submit.final.retire');


		// ROUND 1
		Route::post('/submit/round-one/match-one/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_one'])->name('submit.round.one.match.one');
		Route::post('/submit/round-one/match-two/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_two'])->name('submit.round.one.match.two');
		Route::post('/submit/round-one/match-three/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_three'])->name('submit.round.one.match.three');
		Route::post('/submit/round-one/match-four/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_four'])->name('submit.round.one.match.four');
		Route::post('/submit/round-one/match-five/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_five'])->name('submit.round.one.match.five');
		Route::post('/submit/round-one/match-six/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_six'])->name('submit.round.one.match.six');
		Route::post('/submit/round-one/match-seven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_seven'])->name('submit.round.one.match.seven');
		Route::post('/submit/round-one/match-eight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_eight'])->name('submit.round.one.match.eight');
		Route::post('/submit/round-one/match-nine/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_nine'])->name('submit.round.one.match.nine');
		Route::post('/submit/round-one/match-ten/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_ten'])->name('submit.round.one.match.ten');
		Route::post('/submit/round-one/match-eleven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_eleven'])->name('submit.round.one.match.eleven');
		Route::post('/submit/round-one/match-twelve/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twelve'])->name('submit.round.one.match.twelve');
		Route::post('/submit/round-one/match-thirteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_thirteen'])->name('submit.round.one.match.thirteen');
		Route::post('/submit/round-one/match-fourteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_fourteen'])->name('submit.round.one.match.fourteen');
		Route::post('/submit/round-one/match-fifteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_fifteen'])->name('submit.round.one.match.fifteen');
		Route::post('/submit/round-one/match-sixteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_sixteen'])->name('submit.round.one.match.sixteen');	
		Route::post('/submit/round-one/match-seventeen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_seventeen'])->name('submit.round.one.match.seventeen');
		Route::post('/submit/round-one/match-eighteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_eighteen'])->name('submit.round.one.match.eighteen');
		Route::post('/submit/round-one/match-nineteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_nineteen'])->name('submit.round.one.match.nineteen');
		Route::post('/submit/round-one/match-twenty/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twenty'])->name('submit.round.one.match.twenty');
		Route::post('/submit/round-one/match-twentyone/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentyone'])->name('submit.round.one.match.twentyone');
		Route::post('/submit/round-one/match-twentytwo/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentytwo'])->name('submit.round.one.match.twentytwo');
		Route::post('/submit/round-one/match-twentythree/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentythree'])->name('submit.round.one.match.twentythree');	
		Route::post('/submit/round-one/match-twentyfour/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentyfour'])->name('submit.round.one.match.twentyfour');
		Route::post('/submit/round-one/match-twentyfive/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentyfive'])->name('submit.round.one.match.twentyfive');
		Route::post('/submit/round-one/match-twentysix/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentysix'])->name('submit.round.one.match.twentysix');
		Route::post('/submit/round-one/match-twentyseven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentyseven'])->name('submit.round.one.match.twentyseven');
		Route::post('/submit/round-one/match-twentyeight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentyeight'])->name('submit.round.one.match.twentyeight');
		Route::post('/submit/round-one/match-twentynine/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_twentynine'])->name('submit.round.one.match.twentynine');
		Route::post('/submit/round-one/match-thirty/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_thirty'])->name('submit.round.one.match.thirty');
		Route::post('/submit/round-one/match-thirtyone/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_thirtyone'])->name('submit.round.one.match.thirtyone');	
		Route::post('/submit/round-one/match-thirtytwo/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_match_thirtytwo'])->name('submit.round.one.match.thirtytwo');	

		Route::post('/submit/round-one/result-one/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_one'])->name('submit.round.one.result.one');
		Route::post('/submit/round-one/result-two/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_two'])->name('submit.round.one.result.two');
		Route::post('/submit/round-one/result-three/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_three'])->name('submit.round.one.result.three');
		Route::post('/submit/round-one/result-four/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_four'])->name('submit.round.one.result.four');
		Route::post('/submit/round-one/result-five/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_five'])->name('submit.round.one.result.five');
		Route::post('/submit/round-one/result-six/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_six'])->name('submit.round.one.result.six');
		Route::post('/submit/round-one/result-seven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_seven'])->name('submit.round.one.result.seven');
		Route::post('/submit/round-one/result-eight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_eight'])->name('submit.round.one.result.eight');
		Route::post('/submit/round-one/result-nine/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_nine'])->name('submit.round.one.result.nine');
		Route::post('/submit/round-one/result-ten/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_ten'])->name('submit.round.one.result.ten');
		Route::post('/submit/round-one/result-eleven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_eleven'])->name('submit.round.one.result.eleven');
		Route::post('/submit/round-one/result-twelve/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twelve'])->name('submit.round.one.result.twelve');
		Route::post('/submit/round-one/result-thirteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_thirteen'])->name('submit.round.one.result.thirteen');
		Route::post('/submit/round-one/result-fourteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_fourteen'])->name('submit.round.one.result.fourteen');
		Route::post('/submit/round-one/result-fifteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_fifteen'])->name('submit.round.one.result.fifteen');
		Route::post('/submit/round-one/result-sixteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_sixteen'])->name('submit.round.one.result.sixteen');
		Route::post('/submit/round-one/result-seventeen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_seventeen'])->name('submit.round.one.result.seventeen');
		Route::post('/submit/round-one/result-eighteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_eighteen'])->name('submit.round.one.result.eighteen');
		Route::post('/submit/round-one/result-nineteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_nineteen'])->name('submit.round.one.result.nineteen');
		Route::post('/submit/round-one/result-twenty/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twenty'])->name('submit.round.one.result.twenty');
		Route::post('/submit/round-one/result-twentyone/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentyone'])->name('submit.round.one.result.twentyone');
		Route::post('/submit/round-one/result-twentytwo/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentytwo'])->name('submit.round.one.result.twentytwo');
		Route::post('/submit/round-one/result-twentythree/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentythree'])->name('submit.round.one.result.twentythree');
		Route::post('/submit/round-one/result-twentyfour/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentyfour'])->name('submit.round.one.result.twentyfour');
		Route::post('/submit/round-one/result-twentyfive/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentyfive'])->name('submit.round.one.result.twentyfive');
		Route::post('/submit/round-one/result-twentysix/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentysix'])->name('submit.round.one.result.twentysix');
		Route::post('/submit/round-one/result-twentyseven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentyseven'])->name('submit.round.one.result.twentyseven');
		Route::post('/submit/round-one/result-twentyeight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentyeight'])->name('submit.round.one.result.twentyeight');
		Route::post('/submit/round-one/result-twentynine/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_twentynine'])->name('submit.round.one.result.twentynine');
		Route::post('/submit/round-one/result-thirty/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_thirty'])->name('submit.round.one.result.thirty');
		Route::post('/submit/round-one/result-thirtyone/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_thirtyone'])->name('submit.round.one.result.thirtyone');
		Route::post('/submit/round-one/result-thirtytwo/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_result_thirtytwo'])->name('submit.round.one.result.thirtytwo');



		// ROUND 2
		Route::post('/submit/round-two/match-one/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_one'])->name('submit.round.two.match.one');
		Route::post('/submit/round-two/match-two/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_two'])->name('submit.round.two.match.two');
		Route::post('/submit/round-two/match-three/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_three'])->name('submit.round.two.match.three');
		Route::post('/submit/round-two/match-four/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_four'])->name('submit.round.two.match.four');
		Route::post('/submit/round-two/match-five/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_five'])->name('submit.round.two.match.five');
		Route::post('/submit/round-two/match-six/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_six'])->name('submit.round.two.match.six');
		Route::post('/submit/round-two/match-seven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_seven'])->name('submit.round.two.match.seven');
		Route::post('/submit/round-two/match-eight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_eight'])->name('submit.round.two.match.eight');
		Route::post('/submit/round-two/match-nine/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_nine'])->name('submit.round.two.match.nine');
		Route::post('/submit/round-two/match-ten/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_ten'])->name('submit.round.two.match.ten');
		Route::post('/submit/round-two/match-eleven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_eleven'])->name('submit.round.two.match.eleven');
		Route::post('/submit/round-two/match-twelve/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_twelve'])->name('submit.round.two.match.twelve');
		Route::post('/submit/round-two/match-thirteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_thirteen'])->name('submit.round.two.match.thirteen');
		Route::post('/submit/round-two/match-fourteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_fourteen'])->name('submit.round.two.match.fourteen');
		Route::post('/submit/round-two/match-fifteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_fifteen'])->name('submit.round.two.match.fifteen');
		Route::post('/submit/round-two/match-sixteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_match_sixteen'])->name('submit.round.two.match.sixteen');	

		Route::post('/submit/round-two/result-one/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_one'])->name('submit.round.two.result.one');
		Route::post('/submit/round-two/result-two/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_two'])->name('submit.round.two.result.two');
		Route::post('/submit/round-two/result-three/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_three'])->name('submit.round.two.result.three');
		Route::post('/submit/round-two/result-four/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_four'])->name('submit.round.two.result.four');
		Route::post('/submit/round-two/result-five/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_five'])->name('submit.round.two.result.five');
		Route::post('/submit/round-two/result-six/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_six'])->name('submit.round.two.result.six');
		Route::post('/submit/round-two/result-seven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_seven'])->name('submit.round.two.result.seven');
		Route::post('/submit/round-two/result-eight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_eight'])->name('submit.round.two.result.eight');
		Route::post('/submit/round-two/result-nine/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_nine'])->name('submit.round.two.result.nine');
		Route::post('/submit/round-two/result-ten/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_ten'])->name('submit.round.two.result.ten');
		Route::post('/submit/round-two/result-eleven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_eleven'])->name('submit.round.two.result.eleven');
		Route::post('/submit/round-two/result-twelve/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_twelve'])->name('submit.round.two.result.twelve');
		Route::post('/submit/round-two/result-thirteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_thirteen'])->name('submit.round.two.result.thirteen');
		Route::post('/submit/round-two/result-fourteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_fourteen'])->name('submit.round.two.result.fourteen');
		Route::post('/submit/round-two/result-fifteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_fifteen'])->name('submit.round.two.result.fifteen');
		Route::post('/submit/round-two/result-sixteen/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_result_sixteen'])->name('submit.round.two.result.sixteen');



		// ROUND 3
		Route::post('/submit/round-three/match-one/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_one'])->name('submit.round.three.match.one');
		Route::post('/submit/round-three/match-two/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_two'])->name('submit.round.three.match.two');
		Route::post('/submit/round-three/match-three/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_three'])->name('submit.round.three.match.three');
		Route::post('/submit/round-three/match-four/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_four'])->name('submit.round.three.match.four');
		Route::post('/submit/round-three/match-five/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_five'])->name('submit.round.three.match.five');
		Route::post('/submit/round-three/match-six/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_six'])->name('submit.round.three.match.six');
		Route::post('/submit/round-three/match-seven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_seven'])->name('submit.round.three.match.seven');
		Route::post('/submit/round-three/match-eight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_match_eight'])->name('submit.round.three.match.eight');

		Route::post('/submit/round-three/result-one/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_one'])->name('submit.round.three.result.one');
		Route::post('/submit/round-three/result-two/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_two'])->name('submit.round.three.result.two');
		Route::post('/submit/round-three/result-three/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_three'])->name('submit.round.three.result.three');
		Route::post('/submit/round-three/result-four/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_four'])->name('submit.round.three.result.four');
		Route::post('/submit/round-three/result-five/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_five'])->name('submit.round.three.result.five');
		Route::post('/submit/round-three/result-six/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_six'])->name('submit.round.three.result.six');
		Route::post('/submit/round-three/result-seven/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_seven'])->name('submit.round.three.result.seven');
		Route::post('/submit/round-three/result-eight/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_result_eight'])->name('submit.round.three.result.eight');



		// QUARTER FINAL
		Route::post('/submit/quarter-final/match-one/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_match_one'])->name('submit.quarter.final.match.one');
		Route::post('/submit/quarter-final/match-two/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_match_two'])->name('submit.quarter.final.match.two');
		Route::post('/submit/quarter-final/match-three/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_match_three'])->name('submit.quarter.final.match.three');
		Route::post('/submit/quarter-final/match-four/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_match_four'])->name('submit.quarter.final.match.four');

		Route::post('/submit/quarter-final/result-one/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_result_one'])->name('submit.quarter.final.result.one');
		Route::post('/submit/quarter-final/result-two/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_result_two'])->name('submit.quarter.final.result.two');
		Route::post('/submit/quarter-final/result-three/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_result_three'])->name('submit.quarter.final.result.three');
		Route::post('/submit/quarter-final/result-four/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_result_four'])->name('submit.quarter.final.result.four');

		Route::post('/submit/quarter-final/winners/tournament/{id}', [AdministratorToolsController::class, 'submit_quarter_final_winners'])->name('submit.quarter.final.winners');


		Route::post('/submit/semi-final/match-one/tournament/{id}', [AdministratorToolsController::class, 'submit_semi_final_match_one'])->name('submit.semi.final.match.one');
		Route::post('/submit/semi-final/match-two/tournament/{id}', [AdministratorToolsController::class, 'submit_semi_final_match_two'])->name('submit.semi.final.match.two');

		Route::post('/submit/semi-final/result-one/tournament/{id}', [AdministratorToolsController::class, 'submit_semi_final_result_one'])->name('submit.semi.final.result.one');
		Route::post('/submit/semi-final/result-two/tournament/{id}', [AdministratorToolsController::class, 'submit_semi_final_result_two'])->name('submit.semi.final.result.two');

		Route::post('/submit/round-one/matches/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_matches'])->name('submit.round.one.matches');
		Route::post('/submit/round-one/results/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_results'])->name('submit.round.one.results');
		
		Route::post('/submit/round-one/winners/tournament/{id}', [AdministratorToolsController::class, 'submit_round_one_winners'])->name('submit.round.one.winners');
		Route::post('/submit/round-two/winners/tournament/{id}', [AdministratorToolsController::class, 'submit_round_two_winners'])->name('submit.round.two.winners');
		Route::post('/submit/round-three/winners/tournament/{id}', [AdministratorToolsController::class, 'submit_round_three_winners'])->name('submit.round.three.winners');

		Route::post('/submit/semifinal/matches/tournament/{id}', [AdministratorToolsController::class, 'submit_semifinal_matches'])->name('submit.semifinal.matches');
		Route::post('/submit/semifinal/results/tournament/{id}', [AdministratorToolsController::class, 'submit_semifinal_results'])->name('submit.semifinal.results');
		Route::post('/submit/semifinal/winners/tournament/{id}', [AdministratorToolsController::class, 'submit_semifinal_winners'])->name('submit.semifinal.winners');

		Route::post('/submit/final/matches/tournament/{id}', [AdministratorToolsController::class, 'submit_final_matches'])->name('submit.final.matches');
		Route::post('/submit/final/results/tournament/{id}', [AdministratorToolsController::class, 'submit_final_results'])->name('submit.final.results');
		Route::post('/submit/final/winners/tournament/{id}', [AdministratorToolsController::class, 'submit_final_winners'])->name('submit.final.winners');

	});

	Route::group(['prefix' => 'player'], function(){
		Route::get('/wait-for-approval', [PlayerToolsController::class, 'wait_approval'])->name('wait.approval');
		Route::middleware(['approved'])->group(function () {
			Route::get('/dashboard', [PlayerToolsController::class, 'player_dashboard'])->name('player.dashboard');
			Route::get('/participate', [PlayerToolsController::class, 'participate'])->name('player.participate');

			Route::post('/store-tournaments-participation', [PlayerToolsController::class, 'store_tournaments_participation'])->name('store.tournaments.participation');
			Route::post('/store-leagues-participation', [PlayerToolsController::class, 'store_leagues_participation'])->name('store.leagues.participation');

			Route::get('/store/tournaments/online-participation', [PlayerToolsController::class, 'tournaments_online_participation'])->name('tournaments.online.participation');
			Route::get('/store/leagues/online-participation', [PlayerToolsController::class, 'leagues_online_participation'])->name('leagues.online.participation');
			Route::get('/cancel/online-participation', [PlayerToolsController::class, 'cancel_online_participation'])->name('cancel.online.participation');

			Route::get('/participations/pending/tournaments', [PlayerToolsController::class, 'player_pending_tournaments_participations'])->name('player.pending.tournaments.participations');
			Route::get('/participations/paid/tournaments', [PlayerToolsController::class, 'player_paid_tournaments_participations'])->name('player.paid.tournaments.participations');
			Route::get('/participations/declined/tournaments', [PlayerToolsController::class, 'player_declined_tournaments_participations'])->name('player.declined.tournaments.participations');

			Route::get('/participations/pending/leagues', [PlayerToolsController::class, 'player_pending_leagues_participations'])->name('player.pending.leagues.participations');
			Route::get('/participations/paid/leagues', [PlayerToolsController::class, 'player_paid_leagues_participations'])->name('player.paid.leagues.participations');
			Route::get('/participations/declined/leagues', [PlayerToolsController::class, 'player_declined_leagues_participations'])->name('player.declined.leagues.participations');

			Route::get('/full-membership', [PlayerToolsController::class, 'get_full_free'])->name('get.full.free');
			Route::post('/store-full', [PlayerToolsController::class, 'store_full_free'])->name('store.full.free');
			Route::get('/store/full-membership/online', [PlayerToolsController::class, 'online_membership'])->name('online.membership');


			Route::get('/participate/full-member/preferences', [PlayerToolsController::class, 'full_member_preferences'])->name('full.member.preferences');
			Route::post('/store-tournaments-preferences', [PlayerToolsController::class, 'store_tournaments_preferences'])->name('store.tournaments.preferences');
			Route::post('/store-leagues-preferences', [PlayerToolsController::class, 'store_leagues_preferences'])->name('store.leagues.preferences');


			Route::get('/tournaments/view-draws/tree', [PlayerToolsController::class, 'view_tournament_draws'])->name('view.tournament.draws');
			Route::get('/view-tournament/tree/{id}', [PlayerToolsController::class, 'view_tournament_tree'])->name('view.tournament.tree');
			Route::get('/tournaments/previous-winners', [PlayerToolsController::class, 'previous_tournament_winners'])->name('previous.tournament.winners');


			Route::get('/leagues/view-draws', [PlayerToolsController::class, 'view_league_draws'])->name('view.league.draws');
			Route::get('/view-league/{id}', [PlayerToolsController::class, 'view_league_tree'])->name('view.league.tree');
			Route::get('/leagues/previous-winners', [PlayerToolsController::class, 'previous_league_winners'])->name('previous.league.winners');


		});
	});

});

include 'custom-route/rankings.php';

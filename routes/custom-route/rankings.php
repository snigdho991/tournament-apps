<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Cms\RoleController;
use App\Http\Controllers\Ums\AdministratorToolsController;
use App\Http\Controllers\Ums\PlayerToolsController;
use App\Http\Controllers\Cms\ThemeController;
use App\Http\Controllers\Ums\ProfileController;
use App\Http\Controllers\Cms\ApiController;
use App\Http\Controllers\Cms\FrontendController;
use App\Http\Controllers\Ums\RankingsController;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
	Route::group(['prefix' => 'administrator'], function(){
        Route::post('/submit/round-one/points/tournament/{id}', [RankingsController::class, 'submit_round_one_points'])->name('submit.round.one.points');
        Route::post('/submit/round-two/points/tournament/{id}', [RankingsController::class, 'submit_round_two_points'])->name('submit.round.two.points');
        Route::post('/submit/round-three/points/tournament/{id}', [RankingsController::class, 'submit_round_three_points'])->name('submit.round.three.points');
        Route::post('/submit/quarter-final/points/tournament/{id}', [RankingsController::class, 'submit_quarter_final_points'])->name('submit.quarter.final.points');
        Route::post('/submit/semi-final/points/tournament/{id}', [RankingsController::class, 'submit_semi_final_points'])->name('submit.semi.final.points');
        Route::post('/submit/final/points/tournament/{id}', [RankingsController::class, 'submit_final_points'])->name('submit.final.points');
        Route::get('/preview-rankings', [RankingsController::class, 'previewRankings'])->name('previewRankings');
        Route::post('/publish-rankings', [RankingsController::class, 'publishRankings'])->name('publishRankings');
        Route::post('/publish-rankings/submit', [RankingsController::class, 'publishRankingsSubmit'])->name('publishRankingsSubmit');

        // GROUP POINTS		
		Route::post('/submit/group-{grp_word}/points/{id}', [RankingsController::class, 'submit_group_points'])->name('submit.group.points');
    });

});
@extends('layouts.master')
@section('title', 'Administrator Dashboard')

@section('content')

	<div class="page-content">
	    <div class="container-fluid">

	        <!-- start page title -->
	        <div class="row">
	            <div class="col-12">
	                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
	                    <h4 class="mb-sm-0 font-size-18">{{ Auth::user()->role }} Dashboard</h4>                      
	                </div>
	            </div>
	        </div>

	        <div class="row">
                <div class="col-xl-12">


                    <div class="row" id="deviceStandard" style="margin-top: -44px;">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Tour. Participation Stats <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Tour. Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $all_tour_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $pend_tour_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.pending.tournaments.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-bolt-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Approved Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $paid_tour_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.paid.tournaments.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Declined Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $dec_tour_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.declined.tournaments.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-x-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="row mt-1">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">League Participation Stats <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">League Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $all_league_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $pend_league_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.pending.leagues.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-bolt-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Approved Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $paid_league_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.paid.leagues.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Declined Participations</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $dec_league_par }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.declined.leagues.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-x-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="row mt-1">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Membership Stats <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">All Memberships</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $all_mem }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Memberships</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $pend_mem }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.pending.membership') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-bolt-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Approved Memberships</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $appr_mem }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.approved.membership') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Declined Memberships</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $dec_mem }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.declined.membership') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-x-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    
                    <br><br>


                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Membership Preferences <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">All Preferences</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $all_pre }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Tournament Preferences</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $tour_pre }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.full.tournaments.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-tennis-ball font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">League Preferences</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $leag_pre }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('admin.full.leagues.participations') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-basketball font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Player Stats <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">All Players - {{ $admin_count }} Administrator</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $all_players }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Players/Approval</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $pend_players }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('approval.list') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-pause-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Approved Players</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $appr_players }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('players.list') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Tournament Stats <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">All Tournaments</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $all_tours }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('all.tournaments') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>

                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Active Tournaments</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $on_tours }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>

                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Inactive Tournaments</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $off_tours }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                            
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-archive-in font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">League Stats <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">All Leagues</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $all_leags }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="{{ route('all.leagues') }}" style="position: relative;top: -5px;left: 5px;">
                                                    <span class="small text-dark"> View All</span> <i class="mdi mdi-arrow-right-circle me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Active Leagues</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $on_leags }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Inactive Leagues</p>
                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $off_leags }}</h4>
                                                <a class="btn btn-sm font-size-14 text-center" href="javascript:void(0)" style="position: relative;top: -5px;left: 5px;pointer-events: none;">
                                                    <span class="small text-dark"> </span> <i class="me-1 btn-link" style="position: relative;top: 1.5px;"></i> 
                                                </a>
                                            </span>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-archive-in font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="theme_value" data-theme="{{ Auth::user()->theme }}"></div>
                    </div>
                    <br>

                    <br><hr><br>


                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Tournament Earnings <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_tour_pend }} €</h4>
                                            </span>

                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Paid Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_tour_paid }} €</h4>
                                            </span>

                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Declined Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_tour_dec }} €</h4>
                                            </span>
                                            
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-archive-in font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-1">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">League Earnings <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_leag_pend }} €</h4>
                                            </span>

                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Paid Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_leag_paid }} €</h4>
                                            </span>

                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Declined Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_leag_dec }} €</h4>
                                            </span>
                                            
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-archive-in font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="row mt-1">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Membership Earnings <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_mem_pend * 120 }} €</h4>
                                            </span>

                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Paid Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_mem_appr * 120  }} €</h4>
                                            </span>

                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Declined Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0">{{ $am_mem_dec * 120 }} €</h4>
                                            </span>
                                            
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-archive-in font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-1">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="text-align: center !important;">
                            <span class="badge bg-dark font-size-12">Total Earnings <i class="bx bx-caret-down"></i></span><br><br>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Pending Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0 text-info">{{ $am_tour_pend + $am_leag_pend + $am_mem_pend * 120  }} €</h4>
                                            </span>

                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Paid Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0 text-success">{{ $am_tour_paid + $am_leag_paid + $am_mem_appr * 120  }} €</h4>
                                            </span>

                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-check-shield font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted fw-medium">Declined Fees</p>

                                            <span style="display: inline-flex;">
                                                <h4 class="mb-0 text-danger">{{ $am_tour_dec + $am_leag_dec + $am_mem_dec * 120 }} €</h4>
                                            </span>
                                            
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-archive-in font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- end row -->

	    </div>
	</div>

@endsection

@section('styles')
    <style type="text/css">
        @media screen and (max-width: 1199px) and (min-width: 300px) {
            #deviceStandard {
                margin-top: 10px !important;
            }
        }
    </style>
@endsection


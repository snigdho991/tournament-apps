<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
        @if(Auth::check())
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                        <span key="t-charts">Quick Menu</span>
                    </a>

                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="@if(Auth::user()->hasRole('Administrator')) {{ route('admin.dashboard') }} @elseif(Auth::user()->hasRole('Player')) {{ route('player.dashboard') }} @endif" class="waves-effect">
                                <i class="bx bx-home-circle"></i>
                                <span key="t-calendar">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/') }}" target="_blank" class="waves-effect">
                                <i class="bx bx-globe"></i>
                                <span key="t-calendar">Visit Website</span>
                            </a>
                        </li>
                        
                        {{-- <li>
                            <a href="{{ route('head.to.head') }}" class="waves-effect">
                                <i class="fas fa-users-cog"></i>
                                <span class="badge rounded-pill bg-danger float-end" key="t-new">Dev.</span>
                                <span key="t-calendar">Head to Head</span>
                            </a>
                        </li> --}}

                        @if(Auth::user()->hasRole('Administrator'))
                            <li>
                                <a href="{{ route('head.to.head') }}" class="waves-effect">
                                    <i class="fas fa-users-cog"></i>
                                    <span class="badge rounded-pill bg-danger float-end" key="t-new">Dev.</span>
                                    <span key="t-calendar">Head to Head</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('send.emails') }}" class="waves-effect">
                                    <i class="bx bx-envelope"></i>
                                    <span key="t-calendar">Send E-mails</span>
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>
  
                    @if(Auth::user()->hasRole('Player'))
                        <li class="menu-title" key="t-menu">View Draw</li>

                        <li>
                            <a href="{{ route('view.tournament.draws') }}" class="waves-effect">
                                <i class="bx bx-trophy"></i>
                                <span key="t-calendar">Tournaments</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('view.league.draws') }}" class="waves-effect">
                                <i class="bx bx-crown"></i>
                                <span key="t-calendar">Leagues</span>
                            </a>
                        </li>


                        <li class="menu-title" key="t-menu">Previous Winners</li>

                        <li>
                            <a href="{{ route('previous.tournament.winners') }}" class="waves-effect">
                                <i class="bx bx-trophy"></i>
                                <span class="badge rounded-pill bg-danger float-end" key="t-new">New</span>
                                <span key="t-calendar">Tournaments</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('previous.league.winners') }}" class="waves-effect">
                                <i class="bx bx-crown"></i>
                                <span class="badge rounded-pill bg-primary float-end" key="t-new">New</span>
                                <span key="t-calendar">Leagues</span>
                            </a>
                        </li>
                    @endif

                @if(Auth::user()->hasRole('Administrator'))

                    <li class="menu-title" key="t-apps">Tools</li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">Tournament Tools</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('all.tournaments') }}" class="waves-effect">
                                        <i class="bx bx-receipt"></i>
                                        <span key="t-calendar">Tournaments List</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('add.new.tournament') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Add Tournament</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">League Tools</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('all.leagues') }}" class="waves-effect">
                                        <i class="bx bx-receipt"></i>
                                        <span key="t-calendar">Leagues List</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('add.new.league') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Add League</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    
                    <li class="menu-title" key="t-apps">Player Area</li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">Manage players</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('players.list') }}" class="waves-effect">
                                        <i class="mdi mdi-table-clock"></i>
                                        <span key="t-calendar">Approved players</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('approval.list') }}" class="waves-effect">
                                        <i class="bx bx-shield-quarter"></i>
                                        <span key="t-calendar">Waiting Approval</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">Tour. Participations</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('admin.pending.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-receipt"></i>
                                        <span key="t-calendar">Pending</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.paid.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Paid</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.declined.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-x-circle"></i>
                                        <span key="t-calendar">Declined</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">League Participations</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('admin.pending.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-receipt"></i>
                                        <span key="t-calendar">Pending</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.paid.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Paid</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.declined.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-x-circle"></i>
                                        <span key="t-calendar">Declined</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    <li class="menu-title" key="t-apps">Membership Area</li>
                        
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">Manage Membership</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('admin.pending.membership') }}" class="waves-effect">
                                        <i class="bx bx-receipt"></i>
                                        <span key="t-calendar">Pending</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.approved.membership') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Approved</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.declined.membership') }}" class="waves-effect">
                                        <i class="bx bx-x-circle"></i>
                                        <span key="t-calendar">Declined</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">Preferences</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('admin.full.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Tournament</span>
                                    </a>
                                </li>

                                    <li>
                                    <a href="{{ route('admin.full.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-check-shield"></i>
                                        <span key="t-calendar">League</span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                    <li class="menu-title" key="t-apps">Configurations</li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">Settings</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li>
                                    <a href="{{ route('get.settings') }}" class="waves-effect">
                                        <i class="bx bx-lock-open"></i>
                                        <span key="t-calendar">App Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/user/profile') }}" class="waves-effect">
                                        <i class="bx bx-news"></i>
                                        <span key="t-calendar">Update Credentials</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="@if(request()->route()->getName() == 'publishRankings') mm-active @endif">
                            <a href="{{ route('previewRankings') }}" class="waves-effect @if(request()->route()->getName() == 'publishRankings') active @endif">
                                <i class="mdi mdi-tennis" style="color: #cadb2f !important;"></i>
                                <span key="t-none">Publish Rankings</span>
                            </a>
                        </li>

                    @elseif(Auth::user()->hasRole('Player'))

                        <li class="menu-title" key="t-apps">Full Membership</li>
                            <li>
                                <a href="{{ route('get.full.free') }}" class="waves-effect">
                                    <i class="bx bx-aperture"></i>
                                    <span key="t-calendar">@if(Auth::user()->status == 'Full Member') Your @else Request @endif Membership</span>
                                </a>
                            </li>

                        @if(Auth::user()->status == 'Full Member')
                            <li class="menu-title" key="t-apps">Full Membership Tools</li>
                                <li>
                                    <a href="{{ route('player.paid.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Tournament Preferences</span>
                                    </a>
                                </li>

                                 <li>
                                    <a href="{{ route('player.paid.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-check-shield"></i>
                                        <span key="t-calendar">League Preferences</span>
                                    </a>
                                </li>
                            
                        @else

                            <li class="menu-title" key="t-apps">My Tournaments</li>
                                <li>
                                    <a href="{{ route('player.pending.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-receipt"></i>
                                        <span key="t-calendar">Pending Participation</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('player.paid.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Paid Participation</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('player.declined.tournaments.participations') }}" class="waves-effect">
                                        <i class="bx bx-x-circle"></i>
                                        <span key="t-calendar">Declined Participation</span>
                                    </a>
                                </li>


                            <li class="menu-title" key="t-apps">My Leagues</li>
                                <li>
                                    <a href="{{ route('player.pending.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-receipt"></i>
                                        <span key="t-calendar">Pending Participation</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('player.paid.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-task"></i>
                                        <span key="t-calendar">Paid Participation</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('player.declined.leagues.participations') }}" class="waves-effect">
                                        <i class="bx bx-x-circle"></i>
                                        <span key="t-calendar">Declined Participation</span>
                                    </a>
                                </li>
                           
                        @endif 

                        <li class="menu-title" key="t-apps">Profile Tools</li>
                            <li>
                                <a href="{{ url('/user/profile') }}" class="waves-effect">
                                    <i class="bx bx-news"></i>
                                    <span key="t-calendar">Update Credentials</span>
                                </a>
                            </li>

                    @endif
                
            </ul>
            
        @endif
        </div>
        <!-- Sidebar -->
    </div>
    
</div>
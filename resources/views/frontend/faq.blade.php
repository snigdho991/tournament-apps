@extends('layouts.frontend-master')

@section('title', 'FAQ')

@section('content')
        
    <section class="section" id="faqs" style="margin-top: 50px !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5">
                        <div class="small-title">FAQs</div>
                        <h4>Frequently asked questions</h4>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="vertical-nav">
                        <div class="row">
                            
                            <div class="col-lg-12 col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="v-pills-gen-ques" role="tabpanel">
                                                <h4 class="card-title mb-4">General Questions</h4>
                                                
                                                <div>
                                                    <div id="gen-ques-accordion" class="accordion custom-accordion">
                                                        <div class="mb-3">
                                                            <a href="#general-collapseOne" class="accordion-list" data-bs-toggle="collapse"
                                                                                            aria-expanded="true"
                                                                                            aria-controls="general-collapseOne">
                                                                
                                                                <div>1. What is Tennis4All and how it works?</div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                                
                                                            </a>
                                    
                                                            <div id="general-collapseOne" class="collapse show" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Tennis4all is a tennis community of players who like to play tennis for fun and exercise. Our tournaments base is Limassol. Players in tennis4all tournaments are amateurs and separated in 3 categories of strength: beginners, intermediate and experts (not professionals). <br> <br> Players who are interested to participate just have to visit our website <a href="{{ url('/') }}">www.tennis4allcyprus.com</a>, create a profile and register in any tournament and league they want. Once they pay the fee, they receive a confirmation email of their registration. When the draw is out the player receives a message with his opponent’s name, contact details and the deadline of their match. It’s the player’s responsibility to arrange the match within the deadline and inform the tournament’s supervisor for any issues.</p>
                                                                </div>
                                                            </div>
                                                        </div>
        
                                                        <div class="mb-3">
                                                            <a href="#general-collapseTwo" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseTwo">
                                                                <div>2. What categories of players exist?</div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseTwo" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Players are separated in 3 categories: beginners, intermediate and experts (not professionals). Based on those categories the relevant tournaments are taking place: Rookie100, INT250, ADV500, PRO1000 and ELITE1500. Tournaments are knock-out rounds and points allocation is based on ATP points system. <br> A player is set in a category, the first time he enters the tournament, based on how long he has been playing tennis and according to his/her estimations. Once the player receives ranking after the end of the first tournament the organisers can switch his category in order to have a better fit. Players with ranking can play: <br> 
                                                                        1-4 can play only in ELITE1500 <br> 4-10 can play only in ELITE1500/PRO1000 <br> 11-20 can play in ELITE1500/PRO1000/ADV500 <br> 21-35 can play in ELITE1500/PRO1000/ADV500/INT250 <br> 36 and below in ROOKIE100 and any other category (as mentioned above)
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseThree" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseThree">
                                                                <div>3. What are “leagues”?</div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseThree" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">“Leagues” is a group stage organisation. Players who are interested to play in Leagues are separated in Categories based on their level of game(Cat “A”, Cat “B” and Cat “C”). According to the number of players participating the relevant groups and categories are created. Once the draw is out players are informed on their group members and deadline and can play their matches in any turn. 2 to 4 first players of each group (depends on the participation) are qualified in the knock-out stage playing with the other qualifying players. At the end of the Leagues the Final of each category is set with the winner and finalist winning a trophy. </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseFour" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseFour">
                                                                <div>4. How do I get notified about my opponent?</div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseFour" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Once the draw of the tournament is out you will receive a message with your opponent’s name, contact details and deadline of the round. Each player has responsibility to contact his opponent and agree on the date, time and place to play the match. After the end of the match the winner has obligation to inform the tournament supervisor with a message about the score of the match.</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseFive" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseFive">
                                                                <div>5. In which courts do we play our matches?</div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseFive" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Tennis4All does not provide courts to play your matches. Opponents are playing to any court of their choice as long as they both agree on it. Players with membership in a court have priority to play in their court instead of paying extra to play a match. </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseSix" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseSix">
                                                                <div>6. Can I have an extension to the deadline of my match?</div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseSix" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">No extension is available if the deadline has been reached. Extension is only granted in case of extreme weather conditions, government’s covid-19 restrictions or organisers fault. You should try to arrange your match as soon as you find out your opponent to avoid reaching the deadline. </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseSeven" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseSeven">
                                                                <div>7. What happens if I did not play my match until the deadline? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseSeven" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">If a player didn’t play his/her match by the deadline is responsible to notify the tournament supervisor in due time. If there is reasonable cause he/she can get an extension otherwise if the match has not been played due to his/her fault, then the match goes to his/her opponent. If the two players did not play their match due to both players inability, then the tournament’s supervisor toss the coin to decide which player proceeds to the next round. </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseEight" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseEight">
                                                                <div>8. What happens if my opponent and I do not agree on the date and time or court of our match? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseEight" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Make sure you have contacted your opponent as soon as you receive the message from the tournament supervisor. This way you will both have plenty of dates as a choice and you will not face many limitations. Remember both players are responsible to arrange a game so don’t wait for your opponent to make first contact. Nevertheless, if you contacted your opponent on time and didn’t find a matching date and time, inform the tournament supervisor immediately to involve and find common ground. In case you do not agree where to play, again inform the tournament supervisor immediately to resolve the issue. Be Careful! Staying last minute to inform the tournament supervisor about any disagreements may result loosing the match in cards. </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseNine" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseNine">
                                                                <div>9. How many times a year I can participate in the tournaments and when are the tournaments taking place throughout the year? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseNine" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">You can participate as many times as you want but only in 2 categories each time! For example, you can play in all 4 tournaments’ organisations but in max 2 categories each time. In addition to the tournaments, you can also participate in the “leagues” organised once every 6 months, the 1-day tournament and the TOP16 finals, if you qualify. <br><br>Tournaments’ schedule:<br>1st tournament: February - April<br>2nd tournament: April - June<br>3rd tournament: June - September<br>4th tournament: September - November<br><br>leagues schedule:<br>1st league: February - June<br>2nd league: July - November<br><br>top16: November - December<br> </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseTen" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseTen">
                                                                <div>10. What is the tournaments entry fee? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseTen" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Below you can see how much it costs to enter our tournaments: <br><br>
                                                                        - 1 Tournament / 1 Category €20 <br>
                                                                        - 1 tournament / 2 Categories €35 <br>
                                                                        - Leagues €30 <br>
                                                                        - Top16 Finals €20 <br>
                                                                        - 1 Day Tournament €20 <br>
                                                                        - FULL MEMBERSHIP €120 <br>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseEleven" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseEleven">
                                                                <div>11. What is the tournaments entry fee? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseEleven" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Full Membership is suitable for players who will play throughout the year. It includes: <br><br>
                                                                        4 Tournaments (max 2 categories each time)<br>
                                                                        2 Leagues<br>
                                                                        Top16 Finals<br>
                                                                        50% discount in 1 Day Tournament<br><br>
                                                                        Full Membership costs only €100 for 1 year.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseTweleve" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseTweleve">
                                                                <div>12. How can I pay my fees? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseTweleve" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">To make it easier for you we offer a variety of ways to pay your fees. <br><br>
                                                                        Bank of Cyprus deposit Acc No. 357021543018 (referencing your name) <br>
                                                                        Revolut <br>
                                                                        QuickPay <br>
                                                                        Cash
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseThirteen" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseThirteen">
                                                                <div>13. Are there any trophies for the winners? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseThirteen" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Winners and Finalists of Tennis4All organisation win trophies and gifts in every tournament and League. Trophies and gifts value depends on the tournament/League. 
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseFourteen" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseFourteen">
                                                                <div>14. How can I see the rankings and how it works? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseFourteen" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Rankings are being published at the end of each tournament and are posted on our Facebook Page and website. Players points are allocated based on the ATP system of ranking.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseFifteen" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseFifteen">
                                                                <div>15. What is TOP16 finals and how it works? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseFifteen" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Top16 Finals is the last tournament of the year. In this tournament only the first 16 players are allowed to play. In case that any player do not wish to participate then he/she is replaced by the next in line. 
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <a href="#general-collapseSixteen" class="accordion-list collapsed" data-bs-toggle="collapse"
                                                                            aria-expanded="false"
                                                                            aria-controls="general-collapseSixteen">
                                                                <div>16. How many sets/games are played in a match? </div>
                                                                <i class="mdi mdi-minus accor-plus-icon"></i>
                                                            </a>
                                                            <div id="general-collapseSixteen" class="collapse" data-bs-parent="#gen-ques-accordion">
                                                                <div class="card-body">
                                                                    <p class="mb-0">Our tournaments format is Best of 3. This means that in order for a player to win a match he/she has to win 2 sets. Each set finishes when a player wins 6 games and having minimum 2 games ahead of his/her opponent. If the set is in 5-5 then the set goes to 7 winning games to finish. If the set is 6-6 then then a tie brake up to 7 points decides who gets the set.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end vertical nav -->
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </section>
        
@endsection
@extends('layouts.master')
@section('title', 'League Draw')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">League Draw</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard </a></li>
                                <li class="breadcrumb-item active" style="color: #74788d;">League Draw</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <form class="needs-validation" action="{{ route('store.tournament') }}" method="post" novalidate="">
                            @csrf

                                <div class="row">

                                    <div class="col-xl-6">
                                        <div class="mb-3 position-relative">
                                            <label for="validationTooltip01" class="form-label">Number of Players</label>
                                                                                        
                                            <select class="form-control select2" id="validationTooltip01" name="car_type" required="">
                                    
                                            <option value="">Select Number of Players</option>
                                            
                                            <option value="8">8</option>
                                            <option value="16">16</option>
                                            <option value="32">32</option>
                                        
                                        </select>

                                        <div class="valid-tooltip">
                                            Looks good!
                                        </div>

                                        <div class="invalid-tooltip">
                                            Please select number of players.
                                        </div>                                          

                                        </div>
                                    </div>

                                   
                                    <div class="col-xl-6">

                                        <div class="mb-3 position-relative">                                            
                                            <label for="" class="form-label"></label>
                                            
                                            <button class="btn btn-primary" style="margin-top: 6px !important; width: 100% !important" type="submit">Generate Draw Tree</button>

                                        </div>
                                    </div>

                                </div>

                            </form>

                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->


            <header class="hero">
                <div class="hero-wrap">
                 <p class="intro" id="intro">Tennis4All</p>
                     <h1 id="headline">League</h1>
                     <p class="year"><i class="fa fa-star"></i> {{ $league->name }} <i class="fa fa-star"></i></p>
                 <p>Fixture Tree</p>
               </div>
            </header>


            <section id="bracket">
                <div class="container">
                    <div class="split split-one">
                        <div class="round round-one current">
                            <div class="round-details">Round 1<br/><span class="date">March 16</span></div>
                            <ul class="matchup">
                                <li class="team team-top">Duke<span class="score">76</span></li>
                                <li class="team team-bottom">Virginia<span class="score">82</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">Wake Forest<span class="score">64</span></li>
                                <li class="team team-bottom">Clemson<span class="score">56</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">North Carolina<span class="score">68</span></li>
                                <li class="team team-bottom">Florida State<span class="score">54</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">NC State<span class="score">74</span></li>
                                <li class="team team-bottom">Maryland<span class="score">92</span></li>
                            </ul>           
                            <ul class="matchup">
                                <li class="team team-top">Georgia Tech<span class="score">78</span></li>
                                <li class="team team-bottom">Georgia<span class="score">80</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">Auburn<span class="score">64</span></li>
                                <li class="team team-bottom">Florida<span class="score">63</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">Kentucky<span class="score">70</span></li>
                                <li class="team team-bottom">Alabama<span class="score">59</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">Vanderbilt<span class="score">64</span></li>
                                <li class="team team-bottom">Gonzaga<span class="score">68</span></li>
                            </ul>                                       
                        </div>  <!-- END ROUND ONE -->

                        <div class="round round-two">
                            <div class="round-details">Round 2<br/><span class="date">March 18</span></div>         
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>                                       
                        </div>  <!-- END ROUND TWO -->
                        
                        <div class="round round-three">
                            <div class="round-details">Round 3<br/><span class="date">March 22</span></div>         
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>                                       
                        </div>  <!-- END ROUND THREE -->        
                    </div> 

                    <div class="champion">
                        <div class="semis-l">
                            <div class="round-details">west semifinals <br/><span class="date">March 26-28</span></div>     
                            <ul class ="matchup championship">
                                <li class="team team-top">&nbsp;<span class="vote-count">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="vote-count">&nbsp;</span></li>
                            </ul>
                        </div>
                        <div class="final">
                            <i class="fa fa-trophy"></i>
                            <div class="round-details">championship <br/><span class="date">March 30 - Apr. 1</span></div>      
                            <ul class ="matchup championship">
                                <li class="team team-top">&nbsp;<span class="vote-count">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="vote-count">&nbsp;</span></li>
                            </ul>
                        </div>
                        <div class="semis-r">       
                            <div class="round-details">east semifinals <br/><span class="date">March 26-28</span></div>     
                            <ul class ="matchup championship">
                                <li class="team team-top">&nbsp;<span class="vote-count">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="vote-count">&nbsp;</span></li>
                            </ul>
                        </div>  
                    </div>


                    <div class="split split-two">


                        <div class="round round-three">
                            <div class="round-details">Round 3<br/><span class="date">March 22</span></div>                     
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>                                       
                        </div>  <!-- END ROUND THREE -->    

                        <div class="round round-two">
                            <div class="round-details">Round 2<br/><span class="date">March 18</span></div>                     
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">&nbsp;<span class="score">&nbsp;</span></li>
                                <li class="team team-bottom">&nbsp;<span class="score">&nbsp;</span></li>
                            </ul>                                       
                        </div>  <!-- END ROUND TWO -->
                        <div class="round round-one current">
                            <div class="round-details">Round 1<br/><span class="date">March 16</span></div>
                            <ul class="matchup">
                                <li class="team team-top">Minnesota<span class="score">62</span></li>
                                <li class="team team-bottom">Northwestern<span class="score">54</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">Michigan<span class="score">68</span></li>
                                <li class="team team-bottom">Iowa<span class="score">66</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">Illinois<span class="score">64</span></li>
                                <li class="team team-bottom">Wisconsin<span class="score">56</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">Purdue<span class="score">36</span></li>
                                <li class="team team-bottom">Boise State<span class="score">40</span></li>
                            </ul>           
                            <ul class="matchup">
                                <li class="team team-top">Penn State<span class="score">38</span></li>
                                <li class="team team-bottom">Indiana<span class="score">44</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">Ohio State<span class="score">52</span></li>
                                <li class="team team-bottom">VCU<span class="score">80</span></li>
                            </ul>   
                            <ul class="matchup">
                                <li class="team team-top">USC<span class="score">58</span></li>
                                <li class="team team-bottom">Cal<span class="score">59</span></li>
                            </ul>
                            <ul class="matchup">
                                <li class="team team-top">Virginia Tech<span class="score">74</span></li>
                                <li class="team team-bottom">Dartmouth<span class="score">111</span></li>
                            </ul>                                       
                        </div>  <!-- END ROUND ONE -->                          
                    </div>
                </div>
            </section>

        </div>
    </div>

@endsection


@section('styles')
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script|Herr+Von+Muellerhoff' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Istok+Web|Roboto+Condensed:700' rel='stylesheet' type='text/css'>

    <style type="text/css">

        .hero {
          font-family: "Istok Web", sans-serif;
          background: url("http://picjumbo.com/wp-content/uploads/HNCK2189-1300x866.jpg")
            no-repeat #000;
          background-size: cover;
          min-height: 100%;
          margin: 0;

          position: relative;
          text-align: center;
          overflow: hidden;
          color: #fcfcfc;
        }
        .hero h1 {
          font-family: "Holtwood One SC", serif;
          font-weight: normal;
          font-size: 5.4em;
          margin-top: 40px !important;
          color: #fff;
          margin: 0 0 20px;
          text-shadow: 0 0 12px rgba(0, 0, 0, 0.5);
          text-transform: uppercase;
          letter-spacing: -1px;
        }
        .hero p {
          font-family: "Abel", sans-serif;
          text-transform: uppercase;
          color: #5cca87;
          letter-spacing: 6px;
          text-shadow: 0 0 12px rgba(0, 0, 0, 0.5);
          font-size: 1.2em;
        }
        .hero-wrap {
          padding: 3.5em 10px;
        }
        .hero p.intro {
          font-family: "Holtwood One SC", serif;
          text-transform: uppercase;
          letter-spacing: 1px;
          font-size: 3em;
          margin-bottom: -40px;
        }
        .hero p.year {
          color: #fff;
          letter-spacing: 20px;
          font-size: 34px;
          margin: -25px 0 25px;
        }
        .hero p.year i {
          font-size: 14px;
          vertical-align: middle;
        }
        #bracket {
          overflow: hidden;
          background-color: #e1e1e1;
          background-color: rgba(225, 225, 225, 0.9);
          padding-top: 20px;
          font-size: 12px;
          padding: 40px 0;
        }
        .container {
          max-width: 1100px;
          margin: 0 auto;
          display: block;
          display: -webkit-box;
          display: -moz-box;
          display: -ms-flexbox;
          display: -webkit-flex;
          display: -webkit-flex;
          display: flex;
          -webkit-flex-direction: row;
          flex-direction: row;
        }
        .split {
          display: block;
          float: left;
          display: -webkit-box;
          display: -moz-box;
          display: -ms-flexbox;
          display: -webkit-flex;
          display: flex;
          width: 42%;
          -webkit-flex-direction: row;
          -moz-flex-direction: row;
          flex-direction: row;
        }
        .champion {
          float: left;
          display: block;
          width: 16%;
          -webkit-flex-direction: row;
          flex-direction: row;
          -webkit-align-self: center;
          align-self: center;
          margin-top: -15px;
          text-align: center;
          padding: 230px 0\9;
        }
        .champion i {
          color: #a0a6a8;
          font-size: 45px;
          padding: 10px 0;
        }
        .round {
          display: block;
          float: left;
          display: -webkit-box;
          display: -moz-box;
          display: -ms-flexbox;
          display: -webkit-flex;
          display: flex;
          -webkit-flex-direction: column;
          flex-direction: column;
          width: 95%;
          width: 30.8333%\9;
        }
        .split-two {
        }
        .split-one .round {
          margin: 0 2.5% 0 0;
        }
        .split-two .round {
          margin: 0 0 0 2.5%;
        }
        .matchup {
          margin: 0;
          width: 100%;
          padding: 10px 0;
          -webkit-transition: all 0.2s;
          transition: all 0.2s;
        }
        .score {
          font-size: 11px;
          text-transform: uppercase;
          float: right;
          color: #2c7399;
          font-weight: bold;
          font-family: "Roboto Condensed", sans-serif;
          position: absolute;
          right: 5px;
        }
        .team {
          padding: 0 5px;
          margin: 3px 0;
          height: 25px;
          line-height: 25px;
          white-space: nowrap;
          overflow: hidden;
          position: relative;
        }
        .round-two .matchup {
          margin: 0;
          padding: 50px 0;
        }
        .round-three .matchup {
          margin: 0;
          padding: 130px 0;
        }
        .round-details {
          font-family: "Roboto Condensed", sans-serif;
          font-size: 13px;
          color: #2c7399;
          text-transform: uppercase;
          text-align: center;
          height: 40px;
        }
        .champion li,
        .round li {
          background-color: #fff;
          box-shadow: none;
          opacity: 0.45;
        }
        .current li {
          opacity: 1;
        }
        .current li.team {
          background-color: #fff;
          box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
          opacity: 1;
        }
        .vote-options {
          display: block;
          height: 52px;
        }
        .share .container {
          margin: 0 auto;
          text-align: center;
        }
        .share-icon {
          font-size: 24px;
          color: #fff;
          padding: 25px;
        }
        .share-wrap {
          max-width: 1100px;
          text-align: center;
          margin: 60px auto;
        }
        .final {
          margin: 4.5em 0;
        }

        @-webkit-keyframes pulse {
          0% {
            -webkit-transform: scale(1);
            transform: scale(1);
          }

          50% {
            -webkit-transform: scale(1.3);
            transform: scale(1.3);
          }

          100% {
            -webkit-transform: scale(1);
            transform: scale(1);
          }
        }

        @keyframes pulse {
          0% {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
          }

          50% {
            -webkit-transform: scale(1.3);
            -ms-transform: scale(1.3);
            transform: scale(1.3);
          }

          100% {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
          }
        }

        .share-icon {
          color: #fff;
          opacity: 0.35;
        }
        .share-icon:hover {
          opacity: 1;
          -webkit-animation: pulse 0.5s;
          animation: pulse 0.5s;
        }
        .date {
          font-size: 10px;
          letter-spacing: 2px;
          font-family: "Istok Web", sans-serif;
          color: #3f915f;
        }

        @media screen and (min-width: 981px) and (max-width: 1099px) {
          .container {
            margin: 0 1%;
          }
          .champion {
            width: 14%;
          }
          .split {
            width: 43%;
          }
          .split-one .vote-box {
            margin-left: 138px;
          }
          .hero p.intro {
            font-size: 28px;
          }
          .hero p.year {
            margin: 5px 0 10px;
          }
        }

        @media screen and (max-width: 980px) {
          .container {
            -webkit-flex-direction: column;
            -moz-flex-direction: column;
            flex-direction: column;
          }
          .split,
          .champion {
            width: 90%;
            margin: 35px 5%;
          }
          .champion {
            -webkit-box-ordinal-group: 3;
            -moz-box-ordinal-group: 3;
            -ms-flex-order: 3;
            -webkit-order: 3;
            order: 3;
          }
          .split {
            border-bottom: 1px solid #b6b6b6;
            padding-bottom: 20px;
          }
          .hero p.intro {
            font-size: 24px;
          }
          .hero h1 {
            font-size: 3em;
            margin: 15px 0;
          }
          .hero p {
            font-size: 1em;
          }
        }

        @media screen and (max-width: 400px) {
          .split {
            width: 95%;
            margin: 25px 2.5%;
          }
          .round {
            width: 21%;
          }
          .current {
            -webkit-flex-grow: 1;
            -moz-flex-grow: 1;
            flex-grow: 1;
          }
          .hero h1 {
            font-size: 2.15em;
            letter-spacing: 0;
            margin: 0;
          }
          .hero p.intro {
            font-size: 1.15em;
            margin-bottom: -10px;
          }
          .round-details {
            font-size: 90%;
          }
          .hero-wrap {
            padding: 2.5em;
          }
          .hero p.year {
            margin: 5px 0 10px;
            font-size: 18px;
          }
        }

    </style>
@endsection

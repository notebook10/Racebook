{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
{{--<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">--}}
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>--}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
{{--<script src="{{ asset('js/sweetalert.min.js') }}"></script>--}}
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<script src="{{ asset('js/dashboard.js') }}"></script>
<?php
    date_default_timezone_set('America/Los_Angeles');
    $currentDate = date('mdy',time() + 86400);
?>
<noscript>
    <h1 class="noScript" style="text-align: center">Please enable the JavaScript in your web browser.</h1>
</noscript>
<style>
    .upcomingRace:hover{text-decoration: underline;cursor: pointer;}
</style>
<?php
    if (!isset($_SESSION)) session_start();
    if(!isset($_SESSION["username"])){
        $message = "<div class='jumbotron text-center'><h1>Session Expired! Please login again.</h1></div>";
        echo $message;
    }else{
        $odbc = odbc_connect($_SESSION["dsn"],'','');
        $query = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($_SESSION["username"]) ."'";
        $queryResult = odbc_exec($odbc,$query);
        while($row = odbc_fetch_array($queryResult)){
            $_SESSION["CURRENTBET"] = $row["CURRENTBET"];
            $_SESSION["BALANCE"] = $row["BALANCE"] + $row["CAP"] + $row["CURRENTBET"] + $row["MON_RSLT"] + $row["TUE_RSLT"] + $row["WED_RSLT"] + $row["THU_RSLT"] + $row["FRI_RSLT"] + $row["SAT_RSLT"] + $row["SUN_RSLT"];
        }
?>
<input type="hidden" id="hiddenURL" value="{{ URL::to('/') }}">
<input type="hidden" name="_token" value="{{csrf_token()}}">
<div class="container">
    <div class="row">
        <div class="col-md-4 col-tracks">
            <h3 id="date" data-date="<?php echo date('mdy',time()); ?>">TRACKS RACING TODAY - <?php echo date('m/d/y h:i:s', time()); ?></h3>
            <h5 id="pdt" class="clock"></h5>
            <h5 id="mdt" class="clock"></h5>
            <h5 id="cdt" class="clock"></h5>
            <h5 id="edt" class="clock"></h5>
            {{-- ---------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                @foreach($tracks as $value)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="collapsed trkName" role="button" data-toggle="collapse" data-code="{!! $value->code !!}" data-parent="#accordion" href="#{!! $value->code !!}" aria-expanded="false" aria-controls="{!! $value->code !!}" data-date="{!! $value->date !!}">
                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                    <h5 class="title-menu">{!! $value->name !!}</h5>
                                </a>
                            </h4>
                        </div>
                        <div id="{!! $value->code !!}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body {!! $value->code !!}">

                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- Tracks Tomorrow -->
                @foreach($tomorrow as $value)
                    @if($value->date == $currentDate)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="collapsed trkName" role="button" data-toggle="collapse" data-code="{!! $value->code !!}" data-parent="#accordion" href="#{!! $value->code !!}" aria-expanded="false" aria-controls="{!! $value->code !!}" data-date="{!! $value->date !!}">
                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                    <h5 class="title-menu">{!! $value->name !!}</h5>
                                </a>
                            </h4>
                        </div>
                        <div id="{!! $value->code !!}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body {!! $value->code !!}">

                            </div>
                        </div>
                    </div>
                    @else

                    @endif
                @endforeach
                <!-- Tracks Tomorrow -->
            </div>
            {{-- ---------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
        </div>
        <div class="col-md-8">
            <!-- Upcoming Races -->
            <div id="upcomingRacesDiv">
                <h1>Upcoming Races</h1>
                <div class="loader"></div>
                <table class="table table-bordered table-striped" id="tblUpcomingRace">
                    <thead>
                        <tr>
                            <th>Track</th>
                            <th>Race</th>
                            <th>MTP</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="test"></div>
            </div>
            <div id="raceDiv">
                <!-- Race Info -->
                <div id="board">
                    <div id="raceTrackName"></div>
                    <div id="raceNumberAndPostTime"></div>
                </div>
                <!-- Wager -->
                <div>
                    <label class="s-wager">Select Wager Type: </label>
                    <select id="selectWager" class="form-control">
                    </select>
                    <input type="hidden" id="selectedTrkAndRace" data-trk="" data-raceNum="">
                    <input type="hidden" id="selectedTrack">
                    <input type="hidden" id="selectedRaceNum">
                    <input type="hidden" id="selectedRacePostTime">
                    <input type="hidden" id="selectedDate">
                </div>
                <div id="tempRaces"></div>
                <div class="loader"></div>
                <div id="submitBet" style="display: none;text-align: center;">
                    <input type="text" id="betAmount" class="form-control" placeholder="Put your bet">
                    <button id="submitBetButton" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> SUBMIT BET</button>
                    <button id="clearAll" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span> CLEAR ALL</button>
                </div>
            </div>
            <div id="betTicket">
                <!-- Confirm Bet -->
                <h1 id="confirmationHeader">Confirmation</h1>
                <div id="ticket" class="loader2">
                    <table id="ticketTbl" class="table table-bordered table-striped ">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="betActions">
                        <div class="loader3"></div>
                        <button class="btn btn-success" id="confirmBet"><span class="glyphicon glyphicon-ok"></span> CONFIRM BET</button>
                            <a href="{{ URL::to('/dashboard') }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span> ABORT BET</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    }
?>
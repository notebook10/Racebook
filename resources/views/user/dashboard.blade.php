<?php
date_default_timezone_set('America/Los_Angeles');
use App\Horses;
?>


<input type="hidden" id="hiddenURL" value="{{ URL::to('/') }}">
<input type="hidden" name="_token" value="{{csrf_token()}}">
<div class="container">
    <div class="row">
        <div class="col-md-4 col-tracks">
            <h3 id="date" data-date="<?php echo date('mdy',time()); ?>">TRACKS RACING TODAY - <?php echo date('F d, Y h:i:s', time()); ?></h3>
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
                                <a class="collapsed trkName" role="button" data-toggle="collapse" data-code="{!! $value->code !!}" data-parent="#accordion" href="#{!! $value->code !!}" aria-expanded="false" aria-controls="{!! $value->code !!}">
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
            </div>
            {{-- ---------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            {{--@foreach($tracks as $value)--}}
            {{--<div class="trkName" data-code="{!! $value->code !!}">{!! $value->name !!}</div>--}}
            {{--@endforeach--}}
        </div>
        <div class="col-md-8">
            <!-- Upcoming Races -->
            <div id="upcomingRacesDiv">
                <h1>Upcoming Races</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Track</th>
                            <th>Race</th>
                            <th>MTP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Camamiro</td>
                            <td>2</td>
                            <td>5</td>
                        </tr>
                        <tr>
                            <td>Arlington</td>
                            <td>2</td>
                            <td>5</td>
                        </tr>
                    </tbody>
                </table>
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
                        <option value="wps">Win/Place/Show</option>
                        <option value="dailydouble">Daily Double</option>
                        <option value="superfecta">Superfecta</option>
                        <option value="exacta">Exacta</option>
                        <option value="exactabox">Exacta Box</option>
                        <option value="trifecta">Trifecta</option>
                        <option value="trifectabox">Trifecta Box</option>
                    </select>
                    <input type="hidden" id="selectedTrkAndRace" data-trk="" data-raceNum="">
                    <input type="hidden" id="selectedTrack">
                    <input type="hidden" id="selectedRaceNum">
                    <input type="hidden" id="selectedRacePostTime">
                </div>
                <div id="tempRaces"></div>
                <div id="submitBet" style="display: none">
                    <input type="text" id="betAmount" class="form-control" placeholder="Put your bet">
                    <button id="submitBetButton" class="btn btn-success">SUBMIT BET</button>
                </div>
            </div>
            <div id="betTicket">
                <!-- Confirm Bet -->
                <div id="ticket"></div>
            </div>
        </div>
    </div>
</div>

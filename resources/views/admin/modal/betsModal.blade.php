<div id="betModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">BETS</h4>
            </div>
            <div class="modal-body">
                <form id="frmBets" class="form-group" method="post" action="submitNewBet">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="betsOperation" id="betsOperation" value="0">
                    <input type="hidden" name="betId" id="betId">
                    <input type="hidden" name="grading_statua" id="grading_status">
                    <input type="hidden" name="race_date" id="race_date">
                    <div>
                        <select name="dsn" id="dsn" class="form-control">
                            <option selected disabled>-- SELECT DSN --</option>
                            <option value="floyd">Floyd</option>
                            <option value="webbet">Webbet</option>
                            <option value="nanc">123confirm</option>
                            <option value="mqc">myqualitychoice</option>
                            <option value="abc">abconfirm</option>
                            <option value="myoptions">myoptions123</option>
                            <option value="backdoor">backdoorbets</option>
                            <option value="daveyk">daveyk</option>
                            <option value="luck">luckspeed</option>
                            <option value="lowpro">playlowpro</option>
                            <option value="duckhook">duckhook365</option>
                        </select>
                    </div>
                    <div>
                        <label for="playerID">Player ID:</label>
                        <input type="text" class="form-control" id="player_id" name="player_id" placeholder="Player ID">
                    </div>
                    <div>
                        <label for="amount">Amount:</label>
                        <input type="text" class="form-control" id="amount" name="amount" placeholder="Bet amount">
                    </div>
                    <div>
                        <label for="raceTrack">Race Track:</label>
                        <select id="raceTrack" name="raceTrack" class="form-control">
                            <option selected disabled>-- SELECT TRACK --</option>
                        </select>
                    </div>
                    <div>
                        <label for="raceNum">Race Number:</label>
                        <select id="raceNum" name="raceNum" class="form-control" disabled>
                            <option selected disabled>-- RACE NUMBER --</option>
                        </select>
                    </div>
                    <div>
                        <label for="wager">Wager Type:</label>
                        <select id="wager" name="wager" class="form-control" disabled>
                            <option disabled selected>-- WAGER --</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnSubmitNewBet">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
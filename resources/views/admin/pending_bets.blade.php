<?php
date_default_timezone_set('America/Los_Angeles');
//echo date('mdy',time());
?>
<style>
    th{text-align: center}
    .defeat, .lose{color: #000;background: #f5856e !important;}
    .null{color: #020202;background: #faf7ed !important;}
    .victory, .win{color: #000;background: #90de70 !important;}
    .scratched, .aborted{color:#fff;background: #000 !important;}
    .nopayout{color:#fff;background: orange !important;}
    label.error{color:red;font-size: 9px;}
    .sa-errors-container{display: none !important;}
    th,td{text-align: center;white-space: nowrap;}
</style>
<script src="{{ asset('js/bets.js') }}"></script>
{{--<input id="date">--}}
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" id="tempDate" value="<?php echo date('mdy',time()); ?>">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <input type="button" class="btn btn-primary" value="Add Bet" id="btnAddBet">
                <div>
                    <input type="text" id="datepickerPending" value="<?php echo date('Y-m-d',time()) ?>">
                    <table id="tblPendingBets" class="table table-bordered table-responsive table-stripped">
                        <thead>
                        <tr>
                            <th>Player ID</th>
                            <th>Race Number</th>
                            <th>Race Track</th>
                            <th>Bet Type</th>
                            <th>Bet</th>
                            <th>Bet Amount</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Return</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@include("admin/modal/betsModal")
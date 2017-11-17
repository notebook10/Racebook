<?php date_default_timezone_set('America/Los_Angeles'); ?>
<style>
    .defeat, .lose{color: #000;background: #f5856e !important;}
    .null{color: #020202;background: #faf7ed !important;}
    .victory, .win{color: #000;background: #90de70 !important;}
    .scratched, .aborted{color:#fff;background: #000 !important;}
    .nopayout{color:#fff;background: orange !important;}
    th,td{text-align: center;white-space: nowrap;}
    thead{background: #e6efff;}
    #datepickerPast{text-align: center;cursor: pointer;}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <?php
                if (!isset($_SESSION)) session_start();
                if(!isset($_SESSION["username"])){
                    echo "<h1>Session Expired! Please login again.</h1>";
                }else{
                    echo "<h1>Past Bets</h1>";
                }
                ?>
            </div>
            <div>
                <div style="text-align: center;">
                    View bets for : <input type="text" id="datepickerPast"  value="<?php echo date('Y-m-d',time()) ?>">
                </div>
                <table class="table table-responsive table-bordered table-striped" id="tblNewPast">
                    <thead>
                        <tr>
                            <th>Wager Type</th>
                            <th>Race Track</th>
                            <th>Race Number</th>
                            <th>Horses</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Return</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $("document").ready(function(){
        var BASE_URL = $("#hdnURL").val();
        var userID = $("#userId").val();
        var CURRENT_DATE = $("#datepickerPast").val();
        $("#datepickerPast").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate : -1
        });
        loadNewPastDataTable(BASE_URL,CURRENT_DATE,userID);
        $("#datepickerPast").on("change",function(){
            var selectedDate = $(this).val();
            loadNewPastDataTable(BASE_URL,selectedDate,userID);
        });
        function loadNewPastDataTable(url,date,id){
            $("#tblNewPast").dataTable().fnDestroy();
            $("#tblNewPast").DataTable({
                "aaSorting": [],
                "pageLength": 10,
                "ajax" : {
                    "url" : url + '/dashboard/getPastHome',
                    "type" : "POST",
                    "data" : {
                        _token: $('[name="_token"]').val(),
                        date : date,
                        id : id
                    }
                },
                "columns": [
                    { "data": "bet_type" },
                    { "data": "race_track" },
                    { "data": "race_number" },
                    { "data": "bet" },
                    { "data": "bet_amount" },
                    { "data": "status" },
                    { "data": "result" },
                    { "data": "win_amount" },
                    { "data": "created_at" }
                ],
                "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                    console.log(aData["result"]);
                    $(nRow).addClass(aData["result"].toLowerCase());
                }
            });
        }
    });
</script>
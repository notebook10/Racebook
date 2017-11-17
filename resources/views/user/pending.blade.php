<?php date_default_timezone_set('America/Los_Angeles'); ?>
<style>
    .defeat, .lose{color: #fff;background: #ff4d28 !important;}
    .null{color: #020202;background: #faf7ed !important;}
    .victory, .win{color: #fff;background: #00724b  !important;}
    th,td{text-align: center;white-space: nowrap;}
    thead{background: #e6efff;}
    #datepickerPending{text-align: center;cursor: pointer;}
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
                        echo "<h1>Pending Bets</h1>";
                    }
                ?>
            </div>
            <div>
                <div style="text-align: center">
                    View pending bets for : <input type="text" id="datepickerPending" value="<?php echo date('Y-m-d',time()) ?>">
                </div>
                <table class="table table-responsive table-bordered table-striped" id="tblNewPending">
                    <thead>
                        <tr>
                            <th>Wager Type</th>
                            <th>Race Track</th>
                            <th>Race Number</th>
                            <th>Horses</th>
                            <th>Amount</th>
                            <th>Status</th>
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
        var CURRENT_DATE = $("#datepickerPending").val();
        $("#datepickerPending").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate : -1
        });
        loadNewPendingDataTable(CURRENT_DATE,BASE_URL,userID);
        $("#datepickerPending").on("change",function(){
            var date = $(this).val();
            loadNewPendingDataTable(date,BASE_URL,userID);
        });
        function loadNewPendingDataTable(date,url,id){
            $("#tblNewPending").dataTable().fnDestroy();
            $("#tblNewPending").DataTable({
                "aaSorting": [],
                "pageLength": 10,
                "ajax" : {
                    "url" : url + '/dashboard/getPendingHome',
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
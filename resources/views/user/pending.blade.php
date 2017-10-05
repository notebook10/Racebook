{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
{{--<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">--}}
{{--<link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">--}}
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>--}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
{{--<script src="{{ asset('js/sweetalert.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/jquery.datatables.js') }}"></script>--}}
<?php date_default_timezone_set('America/Los_Angeles'); ?>
<style>
    .defeat, .lose{color: #fff;background: #ff4d28 !important;}
    .null{color: #020202;background: #faf7ed !important;}
    .victory, .win{color: #fff;background: #00724b  !important;}
    th,td{text-align: center;white-space: nowrap;}
    thead{background: #e6efff;}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1>Pending Bets</h1>
            </div>
            <div>
                <div style="text-align: center">
                    <input type="text" id="datepickerPending" value="<?php echo date('Y-m-d',time()) ?>">
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
//        loadPendingDataTable();
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
//        function loadPendingDataTable(){
//            $("#tblPending").DataTable({
//                "aaSorting": []
//            });
//        }
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
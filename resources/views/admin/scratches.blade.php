<?php date_default_timezone_set('America/Los_Angeles'); ?>
<div class="container">
    <?php
        session_start();
        $_SESSION["test"] = "B1588";
        echo $_SESSION["test"];
    ?>
    <div class="jumbotron text-center">
        <h1>SCRATCHES</h1>
    </div>
    <div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="text" id="date" value="<?php echo date('Y-m-d',time()) ?>">
        <table id="tblScratches" class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Race Track</th>
                    <th>Race Number</th>
                    <th>Race Date</th>
                    <th>p#</th>
                    <th>Horse Name</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $("document").ready(function(){
        var BASE_URL = $("#hiddenURL").val();
        loadScratchesDataTable(BASE_URL,$("#date").val());
        $("#date").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate : -1
        });
        $("#date").on("change",function(){
            var date = $(this).val();
            $("#currentDate").val(formatDate(new Date(date)));
            $("#tblScratches").dataTable().fnClearTable();
            loadScratchesDataTable(BASE_URL,date);
        });
    });
    function formatDate(date){
        var day = date.getDate();
        var month = date.getMonth();
        var year = date.getFullYear().toString().substr(2,2);
        return minTwoDigits(month + 1) + minTwoDigits(day) + year
    }
    function minTwoDigits(number){
        return (number < 10 ? '0' : '') + number;
    }
    function loadScratchesDataTable(url,date){
        $("#tblScratches").dataTable().fnDestroy();
        $("#tblScratches").DataTable({
            "aaSorting": [],
            "pageLength": 100,
            "dom": '<"top"flp<"clear">>rt<"bottom"ifp<"clear">>',
            "ajax": {
                "url" : url + '/getScratchesToday',
                "type" : "POST",
                "data" : {
                    _token: $('[name="_token"]').val(),
                    date : date
                }
            },
            "columns": [
                { "data": "race_track" },
                { "data": "race_number" },
                { "data": "race_date" },
                { "data": "pnumber" },
                { "data": "horsename" },
            ]
        });
    }
</script>
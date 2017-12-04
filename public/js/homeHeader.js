$("document").ready(function(){
    var BASE_URL = $("#hdnURL").val();
    var CURRENT_DATE = $("#historyDatePicker").val();
    $("#historyDatePicker").datepicker({
        dateFormat: "yy-mm-dd",
        maxDate : -1
    });
    if($("#userId").val() != ""){
        loadHistory(BASE_URL,CURRENT_DATE);
    }
    $("#btnHistory").on("click",function(){
        $("#historyModal").modal("show");
    });
    $("#historyDatePicker").on("change",function(){
        var selectedDate = $(this).val();
        loadHistory(BASE_URL,selectedDate);
    });
    function loadHistory(url,date){
        $.ajax({
            "url" : url + '/dashboard/getWeek',
            type : "POST",
            data : {
                _token : $('[name="_token"]').val(),
                date : date,
                dsn : $("#data").val()
            },
            success : function (response) {
                console.log(response);
                $("#week").text("WEEK SUMMARY: FROM " +response['start'] + " TO " +response['end']);
                $("#monday").text(response["monday"]);
                $("#tuesday").text(response["tuesday"]);
                $("#wednesday").text(response["wednesday"]);
                $("#thursday").text(response["thursday"]);
                $("#friday").text(response["friday"]);
                $("#saturday").text(response["saturday"]);
                $("#sunday").text(response["sunday"]);
                $("#balance").text("WEEKLY BALANCE: " + response["balance"]);
            }
        });
    }
});

$("document").ready(function(){
    var BASE_URL = $("#hiddenURL").val();
    // loadBetsDataTable();
    loadTestDataTable(BASE_URL,$("#datepicker").val());
    loadPendingDataTable(BASE_URL,$("#datepickerPending").val());
    $("#date").datepicker({
        dateFormat: "yy-mm-dd"
    });
    $("#datepicker, #datepickerPending").datepicker({
        dateFormat: "yy-mm-dd",
        maxDate : -1
    });
    // $("#date").on("change", function(){
    //     var date = $(this).val();
    //     $.ajax({
    //         "url" : BASE_URL + '/getBets',
    //         type : "POST",
    //         data : {
    //             _token : $('[name="_token"]').val(),
    //             date : date
    //         },
    //         success : function(response){
    //             var t = $("#tblBets").DataTable();
    //             if(!$.trim(response)){
    //                 t.rows().remove().draw();
    //             }else{
    //                 t.rows().remove().draw();
    //                 $.each(response, function(index, value){
    //                     var status = response[index]["status"] == 0 ? "Pending" : "Graded";
    //                     var result = response[index]["result"] == 0 ? "Defeat" : "Victory";
    //                     t.row.add([
    //                         response[index]["player_id"],
    //                         response[index]["race_number"],
    //                         response[index]["race_track"],
    //                         response[index]["bet_type"],
    //                         response[index]["bet"],
    //                         response[index]["bet_amount"],
    //                         response[index]["post_time"],
    //                         status,
    //                         result,
    //                         response[index]["win_amount"]
    //                     ]).draw(false);
    //                 });
    //             }
    //         },
    //         error : function(xhr, status, error){
    //             // alert(error);
    //         }
    //     });
    // });
    $("#btnAddBet").on("click", function(evt){
        var date = $("#tempDate").val();
        $("#betsOperation").val(0);
        $.ajax({
            "url" : BASE_URL + '/getTracksToday',
            type : "post",
            data : {
                _token : $('[name="_token"]').val(),
                date : date
            },
            success : function(data){
                clearFrm();
                $.each(data, function(i,v){
                    $("#raceTrack").append("<option value='"+ data[i]["code"] +"'>"+ data[i]["name"] +"</option>");
                });
                $("#betModal").modal("show");
            },
            error : function(xhr, status,error){
                alert(error);
            }
        });
    });
    $("#raceTrack").on("change", function(){
        var trk = $(this).val();
        $.ajax({
            "url" : BASE_URL + '/getRaces',
            type : "POST",
            data : {
                _token : $('[name="_token"]').val(),
                code : trk,
                date : $("#tempDate").val()
            },
            success : function(data){
                $("#raceNum").attr("disabled",false);
                var od = JSON.stringify(data);
                var obj = JSON.parse(od);
                var raceArr = [];
                $("#raceNum").attr("disabled",false).empty();
                $("#raceNum").append("<option selected disabled>-- RACE NUMBER --</option>");
                $.each(obj, function(index, value){
                    if(raceArr.indexOf(obj[index].race_number) > -1){}else{
                        raceArr.push(obj[index].race_number);
                    }
                });
                for(var i = 1; i <= raceArr.length; i++){
                    $("#raceNum").append("<option value='"+ i +"'> Race "+  i +"</option>");
                }
            },
            error : function(xhr, status, error){
                alert(error);
            }
        });
    });
    $("#raceNum").on("change", function(){
        var raceNum = $(this).val();
        $.ajax({
            "url": BASE_URL + "/getWagerForRace",
            type: "POST",
            data: {
                _token: $('[name="_token"]').val(),
                trk: $("#raceTrack").val(),
                date: $("#tempDate").val(),
                num: raceNum
            },
            success : function(respo){
                console.log(respo);
                $("#wager").attr("disabled",false);
                $("#wager").attr("disabled",false).empty();
                $("#wager").append("<option disabled selected>-- SELECT WAGER --</option>");
                $.each(respo, function(index, value){
                    switch(value){
                        case "Exacta":
                            $("#wager").append("<option value='exacta'>Exacta</option>");
                            break;
                       // case "Exacta Box":
                       //     $("#wager").append("<option value='exactabox'>Exacta Box</option>");
                       //     break;
                        case "Trifecta":
                            $("#wager").append("<option value='trifecta'>Trifecta</option>");
                            break;
                        case "Superfecta":
                            $("#wager").append("<option value='superfecta'>Superfecta</option>");
                            break;
                        case "Daily Double":
                            $("#wager").append("<option value='dailydouble'>Daily Double</option>");
                            break;
                        case "WPS":
                            $("#wager").append("<option value='w'>Win</option>");
                            $("#wager").append("<option value='p'>Place</option>");
                            $("#wager").append("<option value='s'>Show</option>");
                            break;
                        default:

                            break;
                    }
                });
            },
            error : function(xhr, status, error){
                alert(error);
            }
        });
    });
    $("#wager").on("change", function(){
        $(".horse,  .horseLabel").remove();
        var wager = $(this).val();
        wagerSwitch(wager);
//            switch (wager){
//                case "exacta":
//                    $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
//                    $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
//                    break;
//                case "trifecta":
//                    $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
//                    $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
//                    $("#frmBets").append("<div><label for='third' class='horseLabel'>Third Horse:</label><input type='text' class='form-control horse' placeholder='THIRD' id='third' name='third'></div>");
//                    break;
//                case "superfecta":
//                    $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
//                    $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
//                    $("#frmBets").append("<div><label for='third' class='horseLabel'>Third Horse:</label><input type='text' class='form-control horse' placeholder='THIRD' id='third' name='third'></div>");
//                    $("#frmBets").append("<div><label for='fourth' class='horseLabel'>Fourth Horse:</label><input type='text' class='form-control horse' placeholder='FOURTH' id='fourth' name='fourth'></div>");
//                    break;
//                case "dailydouble":
//                    $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST RACE' id='first' name='first'></div>");
//                    $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND RACE' id='second' name='second'></div>");
//                    break;
//                case "w":
//                    $("#frmBets").append("<div><label for='first' class='horseLabel'>Win:</label><input type='text' class='form-control horse' placeholder='Win' id='first' name='first'></div>");
//                    break;
//                case "p":
//                    $("#frmBets").append("<div><label for='first' class='horseLabel'>Place:</label><input type='text' class='form-control horse' placeholder='Place' id='first' name='first'></div>");
//                    break;
//                case "s":
//                    $("#frmBets").append("<div><label for='first' class='horseLabel'>Show:</label><input type='text' class='form-control horse' placeholder='Show' id='first' name='first'></div>");
//                    break;
//                default:
//                    break;
//            }
    });
    $("#frmBets").validate({
        rules : {
            player_id : "required",
            raceTrack : "required",
            raceNum : "required",
            wager : "required",
            first : {required:true,maxlength:2},
            second : {required:true,maxlength:2},
            third : {required:true,maxlength:2},
            fourth : {required:true,maxlength:2},
        }
    });
    $("#btnSubmitNewBet").on("click", function(){
        $("#frmBets").submit();
    });
    var optionsBets = {
        success: function(response){
            if(response == 0){
                swal("Bet Saved!","","success");
            }else{
                swal("Failed!","","error");
            }
            $("button.confirm").on("click", function(){
                $("#betModal").modal("hide");
                clearFrm();
                location.reload();
            });
        }
    };
    $("#frmBets").ajaxForm(optionsBets);
    $("body").delegate(".editBet","click",function(){
        var id = $(this).data("id");
        var resultVar = "";
        var winAmountVar = "";
        $("#betsOperation").val(1);
        $("#betId").val(id);
        $.ajax({
            "url" : BASE_URL + '/getBetInfo',
            type : "POST",
            data : {
                _token: $('[name="_token"]').val(),
                id : id
            },
            success : function(respo){
                resultVar = respo["result"];
                winAmountVar = respo["win_amount"];
                $.ajax({
                    "url" : BASE_URL + '/getTracksToday',
                    type : "post",
                    data : {
                        _token : $('[name="_token"]').val(),
                        date :  $("#tempDate").val()
                    },
                    success : function(data){
                        $.each(data, function(i,v){
                            $("#raceTrack").append("<option value='"+ data[i]["code"] +"'>"+ data[i]["name"] +"</option>");
                        });
                        $("#betId").val(respo["id"]);
                        $("#player_id").val(respo["player_id"]);
                        $("#amount").val(respo["bet_amount"]);
                        $("#raceTrack").val(respo["race_track"]);
                        // $("#result").val($.cookie("result"));
                        // $("#winamount").val($.cookie("winamount"));
                        $.ajax({
                            "url" : BASE_URL + '/getRaces',
                            type : "POST",
                            data : {
                                _token : $('[name="_token"]').val(),
                                code : respo["race_track"],
                                date : $("#tempDate").val()
                            },
                            success : function(data){
                                $("#raceNum").attr("disabled",false);
                                var od = JSON.stringify(data);
                                var obj = JSON.parse(od);
                                var raceArr = [];
                                $("#raceNum").attr("disabled",false).empty();
                                $("#raceNum").append("<option selected disabled>-- RACE NUMBER --</option>");
                                $.each(obj, function(index, value){
                                    if(raceArr.indexOf(obj[index].race_number) > -1){}else{
                                        raceArr.push(obj[index].race_number);
                                    }
                                });
                                for(var i = 1; i <= raceArr.length; i++){
                                    $("#raceNum").append("<option value='"+ i +"'> Race "+  i +"</option>");
                                }
                                $("#raceNum").val(respo["race_number"]).attr("disabled",false);
                            },
                            error : function(xhr, status, error){
                                alert(error);
                            }
                        });
                        $.ajax({
                            "url": BASE_URL + "/getWagerForRace",
                            type: "POST",
                            data: {
                                _token: $('[name="_token"]').val(),
                                trk: respo["race_track"],
                                date: $("#tempDate").val(),
                                num: respo["race_number"]
                            },
                            success : function(response){
                                console.log(response);
                                $("#wager").attr("disabled",false);
                                $("#wager").attr("disabled",false).empty();
                                $("#wager").append("<option disabled selected>-- SELECT WAGER --</option>");
                                $.each(response, function(index, value){
                                    switch(value){
                                        case "Exacta":
                                            $("#wager").append("<option value='exacta'>Exacta</option>");
                                            $("#wager").append("<option value='exactabox'>Exacta Box</option>");
                                            break;
                                        case "Trifecta":
                                            $("#wager").append("<option value='trifecta'>Trifecta</option>");
                                            $("#wager").append("<option value='trifectabox'>Trifecta Box</option>");
                                            break;
                                        case "Superfecta":
                                            $("#wager").append("<option value='superfecta'>Superfecta</option>");
                                            break;
                                        case "Daily Double":
                                            $("#wager").append("<option value='dailydouble'>Daily Double</option>");
                                            break;
                                        case "WPS":
                                            $("#wager").append("<option value='w'>Win</option>");
                                            $("#wager").append("<option value='p'>Place</option>");
                                            $("#wager").append("<option value='s'>Show</option>");
                                            break;
                                        default:
                                            break;
                                    }
                                });
                                if(respo["bet_type"] == "wps"){
                                    $("#wager").val(respo["type"]);
                                }else{
                                    $("#wager").val(respo["bet_type"]);
                                }
                            },
                            error : function(xhr, status, error){
                                alert(error);
                            }
                        });
                        $(".horse,  .horseLabel").remove();
                        $("#wager").val(respo["bet_type"]).attr("disabled",false);
                        if(respo["bet_type"] == "wps"){
                            wagerSwitch(respo["type"]);
                        }else{
                            wagerSwitch(respo["bet_type"]);
                        }
                        var bet = respo["bet"].split(',');
                        $("#first").val(bet[0]);
                        $("#second").val(bet[1]);
                        $("#third").val(bet[2]);
                        $("#fourth").val(bet[3]);
                        $("#betModal").modal("show");
                    },
                    error : function(xhr, status,error){
                        alert(error);
                    }
                });
//                    $("#betId").val(respo["id"]);
//                    $("#player_id").val(respo["player_id"]);
//                    $("#amount").val(respo["bet_amount"]);
            },
            error : function(xhr, status, error){
                alert(error);
            }
        });
        $(document).ajaxStop(function(){
            $("#frmBets").find("div.tempDiv").remove();
            $("#frmBets").append("<div class='tempDiv'><label for='result'>Result:</label><select id='result' name='result' class='form-control'><option disabled selected>-- RESULT --</option>" +
                "<option value='0'>Pending</option><option value='1'>Win</option><option value='2'>Lose</option><option value='3'>Aborted</option></select>" +
                "<label for='winamount'>WinAmount:</label><input type='text' name='winamount' id='winamount' class='form-control' placeholder='Win Amount'></div>");
            $("#result").val(resultVar);
            $("#winamount").val(winAmountVar);
            // <div id="statusDiv">
            //     <label for="result">Result:</label>
            // <select id="result" name="result" class="form-control">
            //     <option disabled selected>-- RESULT --</option>
            // <option value="0">Pending</option>
            //     <option value="1">Win</option>
            //     <option value="2">Lose</option>
            //     <option value="3">Aborted</option>
            //     </select>
            //     <label for="winamount">WinAmount:</label>
            // <input type="text" name="winamount" id="winamount" class="form-control" placeholder="Win Amount">
            //     </div>
        });
    });
    $("#datepicker").on("change",function(){
        var date = $(this).val();
        $("#tempDate").val(formatDate(new Date(date)));
        $("#tblBets").dataTable().fnClearTable();
        loadTestDataTable(BASE_URL,date);
    });
    $("#datepickerPending").on("change",function(){
        var date = $(this).val();
        $("#tempDate").val(formatDate(new Date(date)));
        $("#tblBets").dataTable().fnClearTable();
        loadPendingDataTable(BASE_URL,date);
    });
});
function loadBetsDataTable(){
    // $("#tblBets").DataTable({
    //     "aaSorting": [],
    //     "pageLength": 100
    // });
}
function loadTestDataTable(url,date){
    $("#tblPastBets").dataTable().fnDestroy();
    $("#tblPastBets").DataTable({
        "aaSorting": [],
        "pageLength": 100,
        "dom": '<"top"flp<"clear">>rt<"bottom"ifp<"clear">>',
        "ajax": {
            "url" : url + '/getPastBets',
            "type" : "POST",
            "data" : {
                _token: $('[name="_token"]').val(),
                date : date
            }
        },
        "columns": [
            { "data": "player_id" },
            { "data": "race_number" },
            { "data": "race_track" },
            { "data": "bet_type" },
            { "data": "bet" },
            { "data": "bet_amount" },
            { "data": "status" },
            { "data": "result" },
            { "data": "win_amount" },
            { "data": "created_at" },
            { "data": "action" }
        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            console.log(aData["result"]);
            $(nRow).addClass(aData["result"].toLowerCase());
        }
    });
}
function loadPendingDataTable(url,date){
    $("#tblPendingBets").dataTable().fnDestroy();
    $("#tblPendingBets").DataTable({
        "aaSorting": [],
        "pageLength": 100,
        "dom": '<"top"flp<"clear">>rt<"bottom"ifp<"clear">>',
        // "sDom": '<"top"<"actions">lfpi<"clear">><"clear">rt<"bottom">'
        "ajax": {
            "url" : url + '/getPendingBets',
            "type" : "POST",
            "data" : {
                _token: $('[name="_token"]').val(),
                date : date
            }
        },
        "columns": [
            { "data": "player_id" },
            { "data": "race_number" },
            { "data": "race_track" },
            { "data": "bet_type" },
            { "data": "bet" },
            { "data": "bet_amount" },
            { "data": "status" },
            { "data": "result" },
            { "data": "win_amount" },
            { "data": "created_at" },
            { "data": "action" }
        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            console.log(aData["result"]);
            $(nRow).addClass(aData["result"].toLowerCase());
        }
    });
}
function clearFrm(){
    var form = $("#frmBets");
    form[0].reset();
    $("label.errors").css("display","none");
}
function wagerSwitch(wager){
    switch (wager){
        case "exacta":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
            $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
            break;
        case "exactabox":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
            $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
            break;
        case "trifecta":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
            $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
            $("#frmBets").append("<div><label for='third' class='horseLabel'>Third Horse:</label><input type='text' class='form-control horse' placeholder='THIRD' id='third' name='third'></div>");
            break;
        case "trifectabox":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
            $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
            $("#frmBets").append("<div><label for='third' class='horseLabel'>Third Horse:</label><input type='text' class='form-control horse' placeholder='THIRD' id='third' name='third'></div>");
            break;
        case "superfecta":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
            $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
            $("#frmBets").append("<div><label for='third' class='horseLabel'>Third Horse:</label><input type='text' class='form-control horse' placeholder='THIRD' id='third' name='third'></div>");
            $("#frmBets").append("<div><label for='fourth' class='horseLabel'>Fourth Horse:</label><input type='text' class='form-control horse' placeholder='FOURTH' id='fourth' name='fourth'></div>");
            break;
        case "dailydouble":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST RACE' id='first' name='first'></div>");
            $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND RACE' id='second' name='second'></div>");
            break;
        case "w":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>Win:</label><input type='text' class='form-control horse' placeholder='Win' id='first' name='first'></div>");
            break;
        case "p":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>Place:</label><input type='text' class='form-control horse' placeholder='Place' id='first' name='first'></div>");
            break;
        case "s":
            $("#frmBets").append("<div><label for='first' class='horseLabel'>Show:</label><input type='text' class='form-control horse' placeholder='Show' id='first' name='first'></div>");
            break;
        default:
            break;
    }
}
function formatDate(date){
    var day = date.getDate();
    var month = date.getMonth();
    var year = date.getFullYear().toString().substr(2,2);
    // alert(minTwoDigits(day) + ' ' + minTwoDigits(month + 1) + ' ' + year);
    return minTwoDigits(month + 1) + minTwoDigits(day) + year
}
function minTwoDigits(number){
    return (number < 10 ? '0' : '') + number;
}
<?php date_default_timezone_set('America/Los_Angeles'); ?>
<input type="hidden" value="<?php echo date('mdy', time()) ?>" id="racedate">
<style>
    th,td{text-align: center}
    #tblResults{margin-bottom: 30px;}
    label.error{color:red;font-size: 9px;}
    #submitMinimum{margin-top: 30px;width:100%;}
    .sa-errors-container{display: none !important;}
    #cancelDiv{margin-top: 10px;}
    #cancelDiv > .btn{width: 30%;margin: 5px;}
    .btn-primary{font-weight: bold;}
</style>
<input type="hidden" value="{{ csrf_token() }}" name="_token">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            {{--<div class="text-center">--}}
                {{--<table class="table table-responsive table-stripped" id="tblResults">--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th>Track Code</th>--}}
                        {{--<th>Race Number</th>--}}
                        {{--<th>Race Data</th>--}}
                        {{--<th>Winning Combination</th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}
                    {{--<tbody>--}}
                        {{--@foreach($results as $key => $value)--}}
                            {{--<tr>--}}
                                {{--<td>{!! $value->track_code !!}</td>--}}
                                {{--<td>{!! $value->race_number !!}</td>--}}
                                {{--<td>{!! $value->race_date !!}</td>--}}
                                {{--<td>{!! $value->race_winners !!}</td>--}}
                            {{--</tr>--}}
                        {{--@endforeach--}}
                    {{--</tbody>--}}
                {{--</table>--}}

            {{--</div>--}}
        </div>
        {{--</div>--}}
    </div>
</div>
<div class="container-fluid">
    <div>
        <form id="frmResults" action="submitResults" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="operation" id="operation" value="0">
            <div class="col-md-3">
                <div class="jumbotron text-center">
                    <h1>TRACKS</h1>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <label>Race Date</label>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="text" id="raceDateInp" name="raceDateInp" class="form-control" placeholder="Race Date">
                    </div>
                    <div class="col-md-12">
                        <label>Race Tracks</label>
                        <select class="form-control" id="tracksToday" name="tracksToday">
                            <option selected disabled>-- Select Track --</option>
                            @foreach($tracks as $key => $val)
                                <option data-code="{{ $val->code }}" value="{{ $val->code }}">{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label>Races</label>
                        <select class="form-control" id="racePerTrack" disabled name="racePerTrack">
                            <option selected disabled>-- Select Race Number --</option>
                        </select>
                        <div id="loader" style="background:url({{asset("images/ajax-loader.gif")}}) no-repeat center center;width:100%;height:100px;position: absolute;z-index: 999;display: none"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="jumbotron text-center">
                    <h1>PAYOUT</h1>
                </div>
                <div class="" id="payoutDiv">
                </div>
                <input type="hidden" id="cancelOperation" value="0">
                <div id="cancelDiv"></div>
            </div>
            <div class="col-md-3">
                <div class="jumbotron text-center">
                    <h1>HORSES</h1>
                </div>
                <div>
                    <label for="first">First Horse PP:</label>
                    <input type="text" class="form-control" id="first" name="first">
                </div>
                <div>
                    <label for="second">Second Horse PP:</label>
                    <input type="text" class="form-control" id="second" name="second">
                </div>
                <div>
                    <label for="third">Third Horse PP:</label>
                    <input type="text" class="form-control" id="third" name="third">
                </div>
               <div>
                   <label for="fourth">Fourth Horse PP:</label>
                   <input type="text" class="form-control" id="fourth" name="fourth">
               </div>
                <div>
                    <input type="button" class="form-control btn btn-primary" value="SAVE" style="margin-top: 30px;" id="btnSubmitResults">
                    <div id="resultsAlert"></div>
                </div>
            </div>
        </form>
        <div class="col-md-3">
            <div class="jumbotron text-center">
                <h1>MINIMUM</h1>
            </div>
            <div class="" id="minimumDiv">

            </div>
        </div>
    </div>

</div>
<script>
    $("document").ready(function(){
        var BASE_URL = $("#hiddenURL").val();
        $("#raceDateInp").datepicker({
            dateFormat : 'mmddy',
            maxDate : 0
        }).val($("#racedate").val());
        loadResultsDataTable();
        $("#tracksToday").on("change", function(){
            var trkCode = $(this).val();
            $("#first, #second, #third, #fourth").val("");
            $("#payoutDiv, #minimumDiv, #cancelDiv").html("");
            $.ajax({
                "url" : BASE_URL + "/getRaces",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    code : trkCode,
//                    date : $("#racedate").val()
                    date : $("#raceDateInp").val()
                },
                success : function(response){
                    var od = JSON.stringify(response);
                    var obj = JSON.parse(od);
                    var raceArr = [];
                    $.each(obj, function(index, value){
                        if(raceArr.indexOf(obj[index].race_number) > -1){}else{
                            raceArr.push(obj[index].race_number);
                        }
                    });
                    $("#racePerTrack").attr("disabled",false).empty();
                    $("#racePerTrack").append("<option selected disabled>-- Select Race Number --</option>");
                    for(var i = 1; i <= raceArr.length; i++){
                        $("#racePerTrack").append("<option value='"+ i +"'> Race "+  i +"</option>");
                    }
                    $.ajax({
                        "url": BASE_URL + "/getWagerForRace",
                        type: "POST",
                        data: {
                            _token: $('[name="_token"]').val(),
                            trk: trkCode,
//                            date: $("#racedate").val(),
                            date : $("#raceDateInp").val(),
                            num : 1 // TEMPORARY
                        },
                        success: function (data) {
                            $("#payoutDiv, #minimumDiv").html("");
                            $.each(data, function(key,val) {
                                switch (val) {
                                    case "Exacta" :
                                        $("#minimumDiv").append("<label for='exactaMinimum'>Exacta Minimum:</label><input type='text' class='form-control' placeholder='EXACTA' id='exactaMinimum' name='exactaMinimum'>");
                                        break;
                                    case "Trifecta" :
                                        $("#minimumDiv").append("<label for='trifectaMinimum'>Trifecta Minimum:</label><input type='text' class='form-control' placeholder='TRIFECTA' id='trifectaMinimum' name='trifectaMinimum'>");
                                        break;
                                    case "Superfecta" :
                                        $("#minimumDiv").append("<label for='superfectaMinimum'>Superfecta Minimum:</label><input type='text' class='form-control' placeholder='SUPERFECTA' id='superfectaMinimum' name='superfectaMinimum'>");
                                        break;
                                    case "Daily Double":
                                        $("#minimumDiv").append("<label for='ddMinimum'>Daily Double Minimum:</label><input type='text' class='form-control' placeholder='DAILY DOUBLE' id='ddMinimum' name='ddMinimum'>");
                                        break;
                                    case "WPS":
                                        $("#minimumDiv").append("<label for='wpsMinimum'>WPS Minimum:</label><input type='text' class='form-control' placeholder='WPS MINIMUM' id='wpsMinimum' name='wpsMinimum'>");
                                        break;
                                }
                            });
                            $("#minimumDiv").append("<button id='submitMinimum' class='btn btn-primary' data-operation='0'>SUBMIT</button><input type='hidden' id='minOperation'> ");
                            $.ajax({
                                "url" : BASE_URL + '/checkMinimum',
                                type : "POST",
                                data : {
                                    _token : $('[name="_token"]').val(),
                                    trk : trkCode,
//                                    date : $("#racedate").val(),
                                    date : $("#raceDateInp").val(),
                                },
                                success : function(respo){
                                    if(respo != 1){
                                        $("#exactaMinimum").val(respo["exacta"]);
                                        $("#trifectaMinimum").val(respo["trifecta"]);
                                        $("#superfectaMinimum").val(respo["superfecta"]);
                                        $("#ddMinimum").val(respo["dailydouble"]);
                                        $("#wpsMinimum").val(respo["wps"]);
                                        $("#minOperation").val("1");
                                        $("#submitMinimum").val("UPDATE").removeClass("btn-primary").addClass("btn-success");
                                    }else{
                                        // Add / save
                                        $("#minOperation").val("0");
                                        $("#submitMinimum").val("SUBMIT").removeClass("btn-success").addClass("btn-primary");
                                    }
                                },
                                error : function(xhr, status, error){
                                    alert(error);
                                }
                            });
                        },
                        error : function(xhr, status, error){
                            alert(error);
                        }
                    });
                },
                error : function(xhr, status, error){
                    alert(error);
                }
            });

        });
        $("#frmResults").validate({
            rules : {
                // required Removed!!!
                racePerTrack : "required",
                tracksToday : "required",
//                exactaPayout : {"required":true,number:true},
                exactaPayout : {number:true},
                trifectaPayout : {number:true},
                superfectaPayout : {number:true},
                ddPayout : {number:true},
//                wPayout : {"required":true,number:true},
                wPayout : {number:true},
                "1pPayout" : {number:true},
                "2pPayout" : {number:true},
//                "1sPayout" : {"required":true,number:true},
//                "2sPayout" : {"required":true,number:true},
//                "3sPayout" : {"required":true,number:true},
                first : "required",
                second : "required",
//                third : "required",
//                fourth : "required",
            },
            messages : {
                racePerTrack : "This is required!",
                tracksToday : "This is required!"
            }
        });
        $("#btnSubmitResults").on("click", function(){
            $("#frmResults").submit();
        });
        var optionsResults = {
            success: function(response){
                alert(response);
                if(response != 1){
                    $("#payoutOperation").val("1"); // for suddeb=n update
                    var lastId = response;
                    $("#resultsAlert div").remove("");
                    swal("Success",lastId,"success");
                    // SUCCESSFULLY SAVED / UPDATED RESULT
                    $.ajax({
                        "url" : BASE_URL + "/getLatestResultID",
                        type : "POST",
                        data : {
                            _token : $('[name="_token"]').val(),
                            lastId : lastId,
                            trk : $('#tracksToday').val(),
                            num : $('#racePerTrack').val(),
                            exacta : $('[name="exacta"]').is(':checked') ? 1 : 0,
                            trifecta : $('[name="trifecta"]').is(':checked') ? 1 : 0,
                            superfecta : $('[name="superfecta"]').is(':checked') ? 1 : 0,
                            dailydouble : $('[name="dailydouble"]').is(':checked') ? 1 : 0,
                            wps : $('[name="wps"]').is(':checked') ? 1 : 0,
                            noshow : $('[name="noshow"]').is(':checked') ? 1 : 0,
//                        date : $("#racedate").val(),
                            date : $("#raceDateInp").val(),
                            cancelOperation : $("#cancelOperation").val()
                        },
                        success : function(response){
                            console.log(response);
                        },
                        error : function(xhr,status,error){
                            swal("Something went wrong!",error,"error");
                        }
                    });
                }else{
                    $("#operation").val(1);
                    $("#payoutOperation").val("1");
                    swal("MISMATCHED");
                }
            }
        };
        $("#frmResults").ajaxForm(optionsResults);
        $("#racePerTrack").on("change",function(){
            $("#loader").css("display","block");
            var trk = $("#tracksToday").val();
            var date = $("#racedate").val();
            var num = $(this).val();
            var resultsOperation = "";
            $.ajax({
                "url" : BASE_URL + "/checkResults",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trkCode : trk,
//                    raceDate : date,
                    raceDate : $("#raceDateInp").val(),
                    raceNum : num
                },
                success : function(response){
                    if($.type(response) === "array"){
                        if(response[0] == 1) {
                            if (response[1] != response[2]) {
                                resultsOperation = 2; // For checkPayout
                                $("#operation").val(2); // For entering second results
                                $("#first").val("");
                                $("#second").val("");
                                $("#third").val("");
                                $("#fourth").val("");
                                $("#btnSubmitResults").removeClass("btn-success").addClass("btn-primary").val("SAVE");
                                $("#resultsAlert div").remove("");
                                $("#resultsAlert").append("<div class='jumbotron'><h1>Unmatched</h1></div>");
                            } else if (response[1] == response[2]) {
                                alert("Same User");
                                // If same user is loggedIn
                                var res = response[3].split(",");
                                $("#operation").val(1);
                                $("#first").val(res[0]);
                                $("#second").val(res[1]);
                                $("#third").val(res[2]);
                                $("#fourth").val(res[3]);
                                $("#resultsAlert div").remove("");
                                $("#btnSubmitResults").removeClass("btn-primary").addClass("btn-success").val("REGRADE");
                                $("#resultsAlert div").remove("");
                                $("#resultsAlert").append("<div class='jumbotron'><h1>Unmatched</h1></div>");
                            }
                        }
                    }else{
                        // If there is a match
                        if(!$.trim(response)){
                            $("#operation").val(0);
                            $("#first").val("");
                            $("#second").val("");
                            $("#third").val("");
                            $("#fourth").val("");
                            $("#resultsAlert div").remove("");
                            $("#btnSubmitResults").removeClass("btn-success").addClass("btn-primary").val("SAVE");
                        }else{
                            var res = response.split(",");
                            $("#operation").val(1);
                            $("#first").val(res[0]);
                            $("#second").val(res[1]);
                            $("#third").val(res[2]);
                            $("#fourth").val(res[3]);
                            $("#resultsAlert div").remove("");
                            $("#btnSubmitResults").removeClass("btn-primary").addClass("btn-success").val("REGRADE");
                        }
                    }
                },
                error : function(xhr,status,err){
                    swal("Something went wrong!",err,"error");
                }
            });
            $.ajax({
                "url" : BASE_URL + "/getWagerForRaceAdmin",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trk : trk,
//                    date : date,
                    date : $("#raceDateInp").val(),
                    num : num
                },
                success : function(data){
                    $("#payoutDiv, #minimumDiv, #cancelDiv").html("");
                    $.each(data, function(key,val){
                        switch (val){
                            case "Exacta" :
                                $("#payoutDiv").append("<div><label for='exactaPayout'>Exacta:</label><input type='text' class='form-control' placeholder='EXACTA' id='exactaPayout' name='exactaPayout'></div>");
                                $("#minimumDiv").append("<label for='exactaMinimum'>Exacta Minimum:</label><input type='text' class='form-control' placeholder='EXACTA' id='exactaMinimum' name='exactaMinimum'>");
//                                $("#cancelDiv").append("<input type='button' class='btn btn-danger cancelWager' data-type='exacta' value='Cancel Exacta'>");
                                $("#cancelDiv").append("<div class='checkbox'><label><input type='checkbox' class='checkbox' data-type='exacta' value='exacta' name='exacta'>Cancel Exacta</label></div>");
                                break;
                            case "Trifecta":
                                $("#payoutDiv").append("<div><label for='trifectaPayout'>Trifecta:</label><input type='text' class='form-control' placeholder='TRIFECTA' id='trifectaPayout' name='trifectaPayout'></div>");
                                $("#minimumDiv").append("<label for='trifectaMinimum'>Trifecta Minimum:</label><input type='text' class='form-control' placeholder='TRIFECTA' id='trifectaMinimum' name='trifectaMinimum'>");
//                                $("#cancelDiv").append("<input type='button' class='btn btn-danger cancelWager' data-type='trifecta' value='Cancel Trifecta'>");
                                $("#cancelDiv").append("<div class='checkbox'><label><input type='checkbox' class='checkbox' data-type='trifecta' value='trifecta' name='trifecta'>Cancel Trifecta</label></div>");
                                break;
                            case "Superfecta":
                                $("#payoutDiv").append("<div><label for='superfectaPayout'>Superfecta:</label><input type='text' class='form-control' placeholder='SUPERFECTA' id='superfectaPayout' name='superfectaPayout'></div>");
                                $("#minimumDiv").append("<label for='superfectaMinimum'>Superfecta Minimum:</label><input type='text' class='form-control' placeholder='SUPERFECTA' id='superfectaMinimum' name='superfectaMinimum'>");
//                                $("#cancelDiv").append("<input type='button' class='btn btn-danger cancelWager' data-type='superfecta' value='Cancel Superfecta'>");
                                $("#cancelDiv").append("<div class='checkbox'><label><input type='checkbox' class='checkbox' data-type='superfecta' value='superfecta' name='superfecta'>Cancel Superfecta</label></div>");
                                break;
                            case "Daily Double":
                                $("#payoutDiv").append("<div><label for='ddPayout'>Daily Double:</label><input type='text' class='form-control' placeholder='DAILY DOUBLE' id='ddPayout' name='ddPayout'></div>");
                                $("#minimumDiv").append("<label for='ddMinimum'>Daily Double Minimum:</label><input type='text' class='form-control' placeholder='DAILY DOUBLE' id='ddMinimum' name='ddMinimum'>");
//                                $("#cancelDiv").append("<input type='button' class='btn btn-danger cancelWager' data-type='dailydouble'  value='Cancel DD'>");
                                $("#cancelDiv").append("<div class='checkbox'><label><input type='checkbox' class='checkbox' data-type='dailydouble' value='dailydouble' name='dailydouble'>Cancel Daily Double</label></div>");
                                break;
                            case "WPS":
                                $("#minimumDiv").append("<label for='wpsMinimum'>WPS Minimum:</label><input type='text' class='form-control' placeholder='WPS MINIMUM' id='wpsMinimum' name='wpsMinimum'>");
                                $("#payoutDiv").append("<div><label for='wPayout'>WPS:</label><input type='text' class='form-control' placeholder='W Payout' id='wPayout' name='wPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='1st P Payout' id='1pPayout' name='1pPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='2nd P Payout' id='2pPayout' name='2pPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='1st S Payout' id='1sPayout' name='1sPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='2nd S Payout' id='2sPayout' name='2sPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='3rd S Payout' id='3sPayout' name='3sPayout'></div>");
//                                $("#cancelDiv").append("<input type='button' class='btn btn-danger cancelWager' data-type='wps'  value='Cancel WPS'>");
                                $("#cancelDiv").append("<div class='checkbox'><label><input type='checkbox' class='checkbox' data-type='wps' value='wps' name='wps'>Cancel WPS</label></div>");
//                                $("#cancelDiv").append("<input type='button' class='btn btn-default noShow' data-type='wps'  value='No Show'>");
                                $("#cancelDiv").append("<div class='checkbox'><label><input type='checkbox' class='checkbox' value='noshow' name='noshow'>No SHOW</label></div>");
                                break;
                            case "Quinella" :
                                $("#payoutDiv").append("<div><label for='quinellaPayout'>Quinella:</label><input type='text' class='form-control' placeholder='QUINELLA' id='quinellaPayout' name='quinellaPayout'></div>");
                                $("#minimumDiv").append("<label for='quinellaMinimum'>Quinella Minimum:</label><input type='text' class='form-control' placeholder='QUINELLA' id='quinellaMinimum' name='quinellaMinimum'>");
//                                $("#cancelDiv").append("<input type='button' class='btn btn-danger cancelWager' data-type='exacta' value='Cancel Exacta'>");
                                $("#cancelDiv").append("<div class='checkbox'><label><input type='checkbox' class='checkbox' data-type='quinella' value='quinella' name='quinella'>Cancel Quinella</label></div>");
                                break;
                            default:

                                break;
                        }
                    });
                    $("#payoutDiv").append("<input type='hidden' id='payoutOperation' name='payoutOperation' value='0'>");
                    $("#minimumDiv").append("<button id='submitMinimum' class='btn btn-primary' data-operation='0'>SUBMIT</button><input type='hidden' id='minOperation'> ");
                    $.ajax({
                        "url" : BASE_URL + '/checkMinimum',
                        type : "POST",
                        data : {
                            _token : $('[name="_token"]').val(),
                            trk : trk,
//                            date : date,
                            date : $("#raceDateInp").val(),
//                            num : 1 // TEMP
                            num : num
                        },
                        success : function(respo){
                            if(respo != 1){
                                $("#exactaMinimum").val(respo["exacta"]);
                                $("#trifectaMinimum").val(respo["trifecta"]);
                                $("#superfectaMinimum").val(respo["superfecta"]);
                                $("#ddMinimum").val(respo["dailydouble"]);
                                $("#wpsMinimum").val(respo["wps"]);
                                $("#quinellaMinimum").val(respo["quinella"]);
                                $("#minOperation").val("1");
                                $("#submitMinimum").val("UPDATE").removeClass("btn-primary").addClass("btn-success");
                            }else{
                                // Add / save
                                $("#minOperation").val("0");
                                $("#submitMinimum").val("SUBMIT").removeClass("btn-success").addClass("btn-primary");
                            }
                        },
                        error : function(xhr, status, error){
                            alert(error);
                        }
                    });
                    setTimeout(function(){
                        if(resultsOperation != 2){
                            $.ajax({
                                "url" : BASE_URL + '/checkPayout',
                                type : 'POST',
                                data : {
                                    _token : $('[name="_token"]').val(),
                                    trk : trk,
//                            date : date,
                                    date : $("#raceDateInp").val(),
                                    num : num
                                },
                                success : function(datum){
                                    if(datum != 1){
                                        $("#wPayout").val(datum["wPayout"]);
                                        $("#1pPayout").val(datum["1pPayout"]);
                                        $("#2pPayout").val(datum["2pPayout"]);
                                        $("#1sPayout").val(datum["1sPayout"]);
                                        $("#2sPayout").val(datum["2sPayout"]);
                                        $("#3sPayout").val(datum["3sPayout"]);
                                        $("#ddPayout").val(datum["ddPayout"]);
                                        $("#exactaPayout").val(datum["exactaPayout"]);
                                        $("#trifectaPayout").val(datum["trifectaPayout"]);
                                        $("#superfectaPayout").val(datum["superfectaPayout"]);
                                        $("#quinellaPayout").val(datum["quinellaPayout"]);
                                        $("#payoutOperation").val("1");
                                    }else{
                                        $("#payoutOperation").val("0");
                                    }
                                },
                                error : function(xhr, status, err){
                                    alert(err);
                                }
                            });
                        }else{
                            // IF results is still unmatched
                            $("#wPayout").val("");
                            $("#1pPayout").val("");
                            $("#2pPayout").val("");
                            $("#1sPayout").val("");
                            $("#2sPayout").val("");
                            $("#3sPayout").val("");
                            $("#ddPayout").val("");
                            $("#exactaPayout").val("");
                            $("#trifectaPayout").val("");
                            $("#superfectaPayout").val("");
                            $("#quinellaPayout").val("");
                        }
                    },1000);
//                    if(resultsOperation != 2){
//                        $.ajax({
//                            "url" : BASE_URL + '/checkPayout',
//                            type : 'POST',
//                            data : {
//                                _token : $('[name="_token"]').val(),
//                                trk : trk,
////                            date : date,
//                                date : $("#raceDateInp").val(),
//                                num : num
//                            },
//                            success : function(datum){
//                                if(datum != 1){
//                                    $("#wPayout").val(datum["wPayout"]);
//                                    $("#1pPayout").val(datum["1pPayout"]);
//                                    $("#2pPayout").val(datum["2pPayout"]);
//                                    $("#1sPayout").val(datum["1sPayout"]);
//                                    $("#2sPayout").val(datum["2sPayout"]);
//                                    $("#3sPayout").val(datum["3sPayout"]);
//                                    $("#ddPayout").val(datum["ddPayout"]);
//                                    $("#exactaPayout").val(datum["exactaPayout"]);
//                                    $("#trifectaPayout").val(datum["trifectaPayout"]);
//                                    $("#superfectaPayout").val(datum["superfectaPayout"]);
//                                    $("#quinellaPayout").val(datum["quinellaPayout"]);
//                                    $("#payoutOperation").val("1");
//                                }else{
//                                    $("#payoutOperation").val("0");
//                                }
//                            },
//                            error : function(xhr, status, err){
//                                alert(err);
//                            }
//                        });
//                    }else{
//                        // IF results is still unmatched
//                        $("#wPayout").val("");
//                        $("#1pPayout").val("");
//                        $("#2pPayout").val("");
//                        $("#1sPayout").val("");
//                        $("#2sPayout").val("");
//                        $("#3sPayout").val("");
//                        $("#ddPayout").val("");
//                        $("#exactaPayout").val("");
//                        $("#trifectaPayout").val("");
//                        $("#superfectaPayout").val("");
//                        $("#quinellaPayout").val("");
//                    }
                    $.ajax({
                        "url" : BASE_URL + '/checkCancelled',
                        type : "POST",
                        data : {
                            _token : $('[name="_token"]').val(),
                            trk : $('#tracksToday').val(),
                            num : $('#racePerTrack').val(),
                            date : $("#raceDateInp").val(),
                        },
                        success : function(data){
                            console.log(data);
                            if(!$.trim(data)){
                                $("#cancelOperation").val(0);
                            }else{
                                $("#cancelOperation").val(1);
                            }
                            $.each(data, function(i,v){
                                switch(data[i].wager){
                                    case "exacta":
                                        $("input[type=checkbox][value=exacta]").attr("checked",true);
                                        break;
                                    case "trifecta":
                                        $("input[type=checkbox][value=trifecta]").attr("checked",true);
                                        break;
                                    case "superfecta":
                                        $("input[type=checkbox][value=superfecta]").attr("checked",true);
                                        break;
                                    case "dailydouble":
                                        $("input[type=checkbox][value=dailydouble]").attr("checked",true);
                                        break;
                                    case "wps":
                                        $("input[type=checkbox][value=wps]").attr("checked",true);
                                        break;
                                    case "s":
                                        $("input[type=checkbox][value=noshow]").attr("checked",true);
                                        break;
                                    case "quinella":
                                        $("input[type=checkbox][value=quinella]").attr("checked",true);
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
                },
                error : function(xhr, status, error){
                    alert(error);
                }
            });
        });
        $("body").delegate("#submitMinimum","click", function(evt){
           $.ajax({
               "url" : BASE_URL + '/saveMinimum',
               type : "POST",
               data : {
                   _token : $('[name="_token"]').val(),
                   trk : $("#tracksToday").val(),
                   num : $("#racePerTrack").val(),
//                   date : $("#racedate").val(),
                   date : $("#raceDateInp").val(),
                   wps : $("#wpsMinimum").val(),
                   exacta : $("#exactaMinimum").val(),
                   trifecta : $("#trifectaMinimum").val(),
                   superfecta : $("#superfectaMinimum").val(),
                   dailydouble : $("#ddMinimum").val(),
                   quinella : $("#quinellaMinimum").val(),
                   operation : $("#minOperation").val()
               },
               success : function(response){
                   if(response == 0){
                       swal("Successfully Saved!","","success");
                   }else if(response == 1){
                       swal("Successfully Updated!","","success");
                   }
                   else{
                       swal("Error","","error");
                   }
               },
               error : function(xhr,status,error){
                   alert(error);
               }
           });
        });
        function loadResultsDataTable(){
            $("#tblResults").DataTable();
        }
        $("body").delegate(".cancelWager","click", function(){
            var type = $(this).data("type");
            $.ajax({
                "url" : BASE_URL + '/cancelWager',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trk : $("#tracksToday").val(),
                    num : $("#racePerTrack").val(),
                    wagerType : type,
//                    date : $("#racedate").val()
                    date : $("#raceDateInp").val(),
                },
                success : function(respo){
                    if(respo == 0){
                        swal({title:type + " bets updated!",text:"",type:"warning"});
                    }else{
                        swal({title:"No bets updated!",text:"",type:"warning"});
                    }
                    switch(type){
                        case "exacta":
                            $("#exactaPayout").val(0);
                            break;
                        case "trifecta":
                            $("#trifectaPayout").val(0);
                            break;
                        case "superfecta":
                            $("#superfectaPayout").val(0);
                            break;
                        case "dailydouble":
                            $("#ddPayout").val(0);
                            break;
                        case "wps":
                            $("#wPayout, #1pPayout, #2pPayout, #1sPayout, #2sPayout, #3sPayout").val(0);
                            break;
                        case "quinella":
                            $("#quinellaPayout").val(0);
                            break;
                    }
                },
                error : function(xhr,status,error){
                    alert("Error: " + error);
                }
            });
        });
        $("body").delegate(".noShow","click", function(){
            $.ajax({
                "url" : BASE_URL + '/noShow',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trk : $("#tracksToday").val(),
                    num : $("#racePerTrack").val(),
                    date : $("#racedate").val()
                },
                success : function(respo){
                    $("#1sPayout, #2sPayout, #3sPayout").val(0);
                },
                error : function(xhr, status, error){
                    swal({title:"Something went wrong!",text:"",type:"warning"});
                }
            });
        });
        $("body").delegate(".checkbox","change",function(){
            var type = this.value;
            if(this.checked){
                switch(type){
                    case "exacta":
                        $("#exactaPayout").val(0.00);
                        break;
                    case "trifecta":
                        $("#trifectaPayout").val(0.00);
                        break;
                    case "superfecta":
                        $("#superfectaPayout").val(0.00);
                        break;
                    case "dailydouble":
                        $("#ddPayout").val(0.00);
                        break;
                    case "wps":
                        $("#wPayout, #1pPayout, #2pPayout, #1sPayout, #2sPayout, #3sPayout").val(0.00);
                        break;
                    case "noshow":
                        $("#1sPayout, #2sPayout, #3sPayout").val(0.00);
                        break;
                    case "quinella":
                        $("#quinellaPayout").val(0.00);
                        break;
                }
            }else{
                switch(type){
                    case "exacta":
                        $("#exactaPayout").val("");
                        break;
                    case "trifecta":
                        $("#trifectaPayout").val("");
                        break;
                    case "superfecta":
                        $("#superfectaPayout").val("");
                        break;
                    case "dailydouble":
                        $("#ddPayout").val("");
                        break;
                    case "wps":
                        $("#wPayout, #1pPayout, #2pPayout, #1sPayout, #2sPayout, #3sPayout").val("");
                        break;
                    case "noshow":
                        $("#1sPayout, #2sPayout, #3sPayout").val("");
                        break;
                    case "quinella":
                        $("#quinellaPayout").val("");
                        break;
                }
            }
        });
        $("#raceDateInp").on("change",function(){
            var selectedDate = $(this).val();
            $("#first, #second, #third, #fourth").val("");
            $("#payoutDiv, #minimumDiv, #cancelDiv").html("");
            $("#racePerTrack").attr("disabled",true).empty();
            $("#racePerTrack").append("<option selected disabled>-- Select Race Number --</option>");
            $.ajax({
                "url" : BASE_URL + '/getTracksWithDate',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : selectedDate
                },
                success : function(respo){
                    console.log(respo);
                    $("#tracksToday option").remove();
                    $("#tracksToday").append("<option selected disabled>-- Select Track --</option>");
                    $.each(respo, function(i,v){
                        $("#tracksToday").append("<option data-code='"+ respo[i]["code"] +"' value='"+ respo[i]["code"] +"'>"+ respo[i]["name"] +"</option>");
                    });
                },
                error : function(xhr, status, error){
                    alert("Error " + error);
                }
            });
        });
    });
    $(document).ajaxStop(function(){
        setTimeout(function(){
            $("#loader").css("display","none");
        },1000);

    });
</script>
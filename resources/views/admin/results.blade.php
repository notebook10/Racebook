<?php date_default_timezone_set('America/Los_Angeles'); ?>
<input type="text" value="<?php echo date('mdy', time()) ?>" id="racedate">
<style>
    th,td{text-align: center}
    #tblResults{margin-bottom: 30px;}
    label.error{color:red;font-size: 9px;}
    #submitMinimum{margin-top: 30px;width:100%;}
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
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="jumbotron text-center">
                    <h1>PAYOUT</h1>
                </div>
                <div class="" id="payoutDiv">

                </div>
            </div>
            <div class="col-md-3">
                <div class="jumbotron text-center">
                    <h1>HORSES</h1>
                </div>
                <div>
                    <label for="first">First Horse PP:</label>
                    <input type="number" class="form-control" id="first" name="first">
                </div>
                <div>
                    <label for="second">Second Horse PP:</label>
                    <input type="number" class="form-control" id="second" name="second">
                </div>
                <div>
                    <label for="third">Third Horse PP:</label>
                    <input type="number" class="form-control" id="third" name="third">
                </div>
               <div>
                   <label for="fourth">Fourth Horse PP:</label>
                   <input type="number" class="form-control" id="fourth" name="fourth">
               </div>
                <div>
                    <input type="button" class="form-control btn btn-primary" value="SAVE" style="margin-top: 30px;" id="btnSubmitResults">
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
        loadResultsDataTable();
        $("#tracksToday").on("change", function(){
            var trkCode = $(this).val();
            $("#first, #second, #third, #fourth").val("");
            $("#payoutDiv, #minimumDiv").html("");
            $.ajax({
                "url" : BASE_URL + "/getRaces",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    code : trkCode,
                    date : $("#racedate").val()
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
                            date: $("#racedate").val(),
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
                                    date : $("#racedate").val(),
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
                racePerTrack : "required",
                tracksToday : "required",
                exactaPayout : {"required":true,number:true},
                trifectaPayout : {"required":true,number:true},
                superfectaPayout : {"required":true,number:true},
                ddPayout : {"required":true,number:true},
                wPayout : {"required":true,number:true},
                "1pPayout" : {"required":true,number:true},
                "2pPayout" : {"required":true,number:true},
                "1sPayout" : {"required":true,number:true},
                "2sPayout" : {"required":true,number:true},
                "3sPayout" : {"required":true,number:true},
                first : "required",
                second : "required",
                third : "required",
                fourth : "required",
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
                var lastId = response;
//                alert("success: " + lastId);
                swal("Success",lastId,"success");
                // SUCCESSFULLY SAVED / UPDATED RESULT
                $.ajax({
                    "url" : BASE_URL + "/getLatestResultID",
                    type : "POST",
                    data : {
                        _token : $('[name="_token"]').val(),
                        lastId : lastId
                    },
                    success : function(response){
                        console.log(response);
                    },
                    error : function(xhr,status,error){
                        swal("Something went wrong!",error,"error");
                    }
                });
            }
        };
        $("#frmResults").ajaxForm(optionsResults);
        $("#racePerTrack").on("change",function(){
            var trk = $("#tracksToday").val();
            var date = $("#racedate").val();
            var num = $(this).val();
            $.ajax({
                "url" : BASE_URL + "/checkResults",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trkCode : trk,
                    raceDate : date,
                    raceNum : num
                },
                success : function(response){
                    if(!$.trim(response)){
                        $("#operation").val(0);
                        $("#first").val("");
                        $("#second").val("");
                        $("#third").val("");
                        $("#fourth").val("");
                        $("#btnSubmitResults").removeClass("btn-success").addClass("btn-primary").val("SAVE");
                    }else{
                        var res = response.split(",");
                        $("#operation").val(1);
                        $("#first").val(res[0]);
                        $("#second").val(res[1]);
                        $("#third").val(res[2]);
                        $("#fourth").val(res[3]);
                        $("#btnSubmitResults").removeClass("btn-primary").addClass("btn-success").val("REGRADE");
                    }
                },
                error : function(xhr,status,err){
                    swal("Something went wrong!",err,"error");
                }
            });
            $.ajax({
                "url" : BASE_URL + "/getWagerForRace",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trk : trk,
                    date : date,
                    num : num
                },
                success : function(data){
                    $("#payoutDiv, #minimumDiv").html("");
                    $.each(data, function(key,val){
                        switch (val){
                            case "Exacta" :
                                $("#payoutDiv").append("<div><label for='exactaPayout'>Exacta:</label><input type='text' class='form-control' placeholder='EXACTA' id='exactaPayout' name='exactaPayout'></div>");
                                $("#minimumDiv").append("<label for='exactaMinimum'>Exacta Minimum:</label><input type='text' class='form-control' placeholder='EXACTA' id='exactaMinimum' name='exactaMinimum'>");
                                break;
                            case "Trifecta":
                                $("#payoutDiv").append("<div><label for='trifectaPayout'>Trifecta:</label><input type='text' class='form-control' placeholder='TRIFECTA' id='trifectaPayout' name='trifectaPayout'></div>");
                                $("#minimumDiv").append("<label for='trifectaMinimum'>Trifecta Minimum:</label><input type='text' class='form-control' placeholder='TRIFECTA' id='trifectaMinimum' name='trifectaMinimum'>");
                                break;
                            case "Superfecta":
                                $("#payoutDiv").append("<div><label for='superfectaPayout'>Superfecta:</label><input type='text' class='form-control' placeholder='SUPERFECTA' id='superfectaPayout' name='superfectaPayout'></div>");
                                $("#minimumDiv").append("<label for='superfectaMinimum'>Superfecta Minimum:</label><input type='text' class='form-control' placeholder='SUPERFECTA' id='superfectaMinimum' name='superfectaMinimum'>");
                                break;
                            case "Daily Double":
                                $("#payoutDiv").append("<div><label for='ddPayout'>Daily Double:</label><input type='text' class='form-control' placeholder='DAILY DOUBLE' id='ddPayout' name='ddPayout'></div>");
                                $("#minimumDiv").append("<label for='ddMinimum'>Daily Double Minimum:</label><input type='text' class='form-control' placeholder='DAILY DOUBLE' id='ddMinimum' name='ddMinimum'>");
                                break;
                            case "WPS":
                                $("#minimumDiv").append("<label for='wpsMinimum'>WPS Minimum:</label><input type='text' class='form-control' placeholder='WPS MINIMUM' id='wpsMinimum' name='wpsMinimum'>");
                                $("#payoutDiv").append("<div><label for='wPayout'>WPS:</label><input type='text' class='form-control' placeholder='W Payout' id='wPayout' name='wPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='1st P Payout' id='1pPayout' name='1pPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='2nd P Payout' id='2pPayout' name='2pPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='1st S Payout' id='1sPayout' name='1sPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='2nd S Payout' id='2sPayout' name='2sPayout'></div>");
                                $("#payoutDiv").append("<div><input type='text' class='form-control' placeholder='3rd S Payout' id='3sPayout' name='3sPayout'></div>");
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
                            date : date,
                            num : 1 // TEMP
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
                    $.ajax({
                        "url" : BASE_URL + '/checkPayout',
                        type : 'POST',
                        data : {
                            _token : $('[name="_token"]').val(),
                            trk : trk,
                            date : date,
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
                                $("#payoutOperation").val("1");
                            }else{
                                $("#payoutOperation").val("0");
                            }
                        },
                        error : function(xhr, status, err){
                            alert(err);
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
                   date : $("#racedate").val(),
                   wps : $("#wpsMinimum").val(),
                   exacta : $("#exactaMinimum").val(),
                   trifecta : $("#trifectaMinimum").val(),
                   superfecta : $("#superfectaMinimum").val(),
                   dailydouble : $("#ddMinimum").val(),
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
    });
</script>
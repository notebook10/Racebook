<?php date_default_timezone_set('America/Los_Angeles'); ?>
<input type="text" value="<?php echo date('mdy',time()); ?>" id="racedate">
<input type="hidden" value="{{ csrf_token() }}" name="_token">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1>Results</h1>
            </div>
            <form id="frmResults" action="submitResults" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="operation" id="operation" value="0">
                <div class="col-md-5">
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
                <div class="col-md-7">
                    <label for="first">First Horse PP:</label>
                    <input type="number" class="form-control" id="first" name="first">
                    <label for="second">Second Horse PP:</label>
                    <input type="number" class="form-control" id="second" name="second">
                    <label for="third">Third Horse PP:</label>
                    <input type="number" class="form-control" id="third" name="third">
                    <label for="fourth">Fourth Horse PP:</label>
                    <input type="number" class="form-control" id="fourth" name="fourth">
                    <div>
                        <input type="button" class="form-control btn btn-primary" value="SAVE" style="margin-top: 30px;" id="btnSubmitResults">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $("document").ready(function(){
        var BASE_URL = $("#hiddenURL").val();
        $("#tracksToday").on("change", function(){
            var trkCode = $(this).val();
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

                },
                error : function(xhr, status, error){
                    alert(error);
                }
            });
        });
        $("#frmResults").validate({
            rules : {
                racePerTrack : "required",
                tracksToday : "required"
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
                alert("success");
                // SUCCESSFULLY SAVED / UPDATED RESULT
                $.ajax({
                    "url" : BASE_URL + "/getLatestResultID",
                    type : "POST",
                    data : {
                        _token : $('[name="_token"]').val()
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
        });
    });
</script>
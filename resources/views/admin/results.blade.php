<?php date_default_timezone_set('America/Los_Angeles'); ?>
<input type="text" value="<?php echo date('mdy', time()); ?>" id="racedate">
<input type="hidden" value="{{ csrf_token() }}" name="_token">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1>Results</h1>
            </div>
            <form id="frmResults">
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
    });
</script>
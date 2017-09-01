<?php
date_default_timezone_set('America/Los_Angeles');
?>
<style>
    .trkDiv{padding: 15px;text-align: center;border: 1px solid #fff;}
    .trkDiv:hover{cursor: pointer;}
    .show{background: #00724b;color:#fff;}
    .tago{background: #ff6c64;color:#000;font-weight:900;}
    .addDiv{background: #faf7ed;color:#b6a361;border: 1px solid #b6a361;font-weight: bold;padding: 15px;text-align: center;}
    .addDiv:hover{background:#9c8948;color: #fff;cursor: pointer;}
    .sa-errors-container{display: none !important;}
</style>
<div class="container">
    <input type="hidden" id="date" value="<?php echo date('mdy',time()); ?>">
    <input type="hidden" id="hiddenURL" value="{{ URL::to('/') }}">
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1>TRACKS</h1>
            </div>
        </div>
        <div class="row">
            <?php
            foreach($tracks as $key => $value){
                $visibility = $value->visibility == 0 ? "show" : "tago";
                echo '<div class="col-sm-3 trkDiv '. $visibility .'" data-code="'. $value->code .'">' . $value->name . '</div>';
            }
            ?>
            <div class="col-sm-3 addDiv" data-code="'. $value->code .'" id="addDiv"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;ADD TRACK</div>
        </div>
        <div class="row" style="display: block;">
            <div style="width: 100%;text-align: center;padding: 20px;"><h1>TRACKS TOMORROW</h1></div>
            <?php
                foreach ($tracksTomorrow as $key => $value){
                    $visibility = $value->_showTemp == 1 ? "show" : "tago";
                    echo '<div class="col-sm-3 trkDiv tomorrow '. $visibility .'" data-code="'. $value->code .'">' . $value->name . '</div>';
                }
            ?>
        </div>
    </div>
</div>
<script>
    $("document").ready(function(){
        var BASE_URL = $("#hiddenURL").val();
        var CURRENT_DATE = $("#date").val();
        $("#addDiv").on("click",function(){
            $.ajax({
                "url" : BASE_URL + '/getTracksForNewTrack',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : CURRENT_DATE
                },
                success : function(data){
                    $.each(data, function(i,v){
                        $("#selectNewTrack").append("<option value='"+ v["track_code"] +"'>"+ v["track_name"] +"</option>");
                    });
                },
                error : function(error){
                    alert(error);
                }
            });
            $("#tracksModal").modal("show");
        });
        $("body").delegate(".trkDiv","click",function(){
            var trkCode = $(this).data("code");
            if($(this).hasClass("tomorrow")){
                $.ajax({
                    "url" : BASE_URL + '/showTemp',
                    type : 'POST',
                    data : {
                        _token : $('[name="_token"]').val(),
                        trk : trkCode,
                        operation : $(this).hasClass("tago") == true ? 1 : 0
                    },
                    success : function(response){
                        if(response == 1){
                            swal("Something went wrong!","","error");
                        }else{
                            location.reload();
                        }
                    },
                    error : function(xhr, status, error){
                        alert("Error : " + error);
                    }
                });
            }else{
                $.ajax({
                    "url" : BASE_URL + '/removeTrack',
                    type : "POST",
                    data : {
                        _token : $('[name="_token"]').val(),
                        trk : trkCode,
                        operation : $(this).hasClass("show") == true ? 1 : 0
                    },
                    success : function(respo){
                        if(respo == 1){
                            swal("Something went wrong!","","error");
                        }else{
                            location.reload();
                        }
                    },
                    error : function(xhr, status, error){
                        alert("Error: " + error);
                    }
                });
            }
        });
        $("#submitNewTrack").on("click", function(){
            var trkCode = $("#selectNewTrack").val();
            var trkName = $("#selectNewTrack option:selected").text();
            $.ajax({
                "url" : BASE_URL + '/submitNewTrack',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : CURRENT_DATE,
                    trkCode : trkCode,
                    trkName : trkName
                },
                success : function(response){
                    if(response == 0){
                        swal("Success","SAVED!!!","success");
                    }else{
                        swal("Error","Not Saved!","error");
                    }
                    $("button.confirm").on("click", function(){
                        location.reload();
                    });
                },
                error : function(xhr, status, error){
                    alert("Error " + error);
                }
            });
        });
    });
</script>

<div id="tracksModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ADD TRACKS</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <select class="form-control" id="selectNewTrack">
                        <option selected disabled>-- SELECT TRACK --</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="submitNewTrack">Submit</button>
            </div>
        </div>

    </div>
</div>
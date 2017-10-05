<?php
    date_default_timezone_set('America/Los_Angeles');
?>
<style>
    table tr,th{text-align: center}
    .sa-errors-container{display: none !important;}
</style>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" id="date" value="<?php echo date('mdy',time()); ?>">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h1>Horses</h1>
                <button class="btn btn-primary" id="btnHorses">Add Horse</button>
            </div>
            <div>
                <table class="table table-responsive table-bordered table-striped table-hover" id="tblHorses">
                    <thead>
                        <tr>
                            <th>PP</th>
                            <th>Horse Name</th>
                            <th>Jockey</th>
                            <th>Race Time</th>
                            <th>Race Number</th>
                            <th>Race Track</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($horses as $key => $value)
                            <tr>
                                <td>{{ $value->pp }}</td>
                                <td>{{ $value->horse }}</td>
                                <td>{{ $value->jockey }}</td>
                                <td>{{ $value->race_time }}</td>
                                <td>{{ $value->race_number }}</td>
                                <td>{{ $value->race_track }}</td>
                                <td>
                                    <button class="btn btn-success editHorse" data-id="{{ $value->id }}">EDIT</button>
                                    <?php
                                        if($value->pp != "SCRATCHED"){
                                            echo '<button class="btn btn-danger btnScratch" data-id="'. $value->id .'" data-num="'. $value->race_number .'" data-track="'. $value->race_track .'" data-date="'. $value->race_date .'" data-pp="'. $value->pp .'">SCRATCH</button>';
                                        }else{
                                            if(is_numeric($value->pnumber)){
                                                echo '<button class="btn btn-primary undo" data-id="'. $value->id .'" data-num="'. $value->race_number .'" data-track="'. $value->race_track .'" data-date="'. $value->race_date .'" data-pnumber="'. $value->pnumber .'">UNDO</button>';
                                            }
                                        }
                                    ?>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $("document").ready(function(){
        loadHorsesDataTable();
        var BASE_URL = $("#hiddenURL").val();

        $("body").delegate(".btnScratch","click", function(e){
            var id = $(this).data("id");
            var num = $(this).data("num");
            var date = $(this).data("date");
            var trk = $(this).data("track");
            var pp = $(this).data("pp");
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this after the changes!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, scratch it!",
                    cancelButtonText: "No, cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        // SCRATCHED
                        $.ajax({
                            "url" : BASE_URL + '/scratch',
                            type : "POST",
                            data : {
                                _token : $('[name="_token"]').val(),
                                id : id,
                                pp : pp
                            },
                            success : function(response){
                                $.ajax({
                                    "url" : BASE_URL + '/scratchBets',
                                    type : "POST",
                                    data : {
                                        _token : $('[name="_token"]').val(),
                                        id : id,
                                        trk : trk,
                                        num : num.replace(/\D/g,''),
                                        date : date,
                                        pp : pp
                                    },
                                    success : function(respo){
                                        if(respo != 1){
                                            swal("SCRATCH IT","Success","success");
                                            $("button.confirm").on("click",function(){location.reload();});
                                        }else{
                                            swal("Do nothing!!!");
                                        }
                                    },
                                    error : function(xhr, status, err){
                                        alert(err);
                                    }
                                });
                            },
                            error : function(xhr, status, err){
                                swal("Error",err,"error");
                            }
                        });
                    } else {
                        swal("Cancelled", "Your imaginary file is safe :)", "error");
                    }
                });
        });
        $("#btnHorses").on("click", function(){
            $.ajax({
                "url" : BASE_URL + '/getTracksToday',
                type : "post",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : $("#date").val()
                },
                success : function(data){
                    clearFrm();
                    $("#horsePP").attr("readonly",false);
                    $.each(data, function(i,v){
                        $("#selectTrack").append("<option value='"+ data[i]["code"] +"'>"+ data[i]["name"] +"</option>");
                    });
                    $("#horseOperation").val(0);
                    $("#horseModal").modal("show");
                },
                error : function(xhr, status,error){
                    alert(error);
                }
            });
        });
        $("#btnSubmitNewHorse").on("click", function(){
            $.ajax({
                "url" : BASE_URL + '/submitHorse',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : $("#date").val(),
                    frm : $("#frmHorses").serializeArray(),
                    operation : $("#horseOperation").val(),
                    id : $("#horseId").val()
                },
                success : function(response){
                    if(response == 0){
                        swal("SUCCESS");
                    }else{
                        swal("ERROR");
                    }
                    $("button.confirm").on("click",function(){
                        location.reload();
                    });
                },
                error : function(error){
                    alert(error);
                }
            });
        });
        $("body").delegate(".editHorse","click", function(){
            var id = $(this).data("id");
            $.ajax({
                "url" : BASE_URL + '/getTracksToday',
                type : "post",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : $("#date").val()
                },
                success : function(data){
                    $.each(data, function(i,v){
                        $("#selectTrack").append("<option value='"+ data[i]["code"] +"'>"+ data[i]["name"] +"</option>");
                    });
                    $.ajax({
                        "url" : BASE_URL + '/getHorseData',
                        type : "POST",
                        data : {
                            _token : $('[name="_token"]').val(),
                            id : id
                        },
                        success : function(respo){
                            console.log(respo);
                            clearFrm();
                            $("#horseId").val(respo["id"]);
                            $("#horsePP").val(respo["pp"]).attr("readonly",true);
                            $("#horseName").val(respo["horse"]);
                            $("#jockeyName").val(respo["jockey"]);
                            $("#raceNumber").val(respo["race_number"].replace(/\D/g,''));
                            $("#postTime").val($.trim(respo["race_time"]));
                            $("#selectTrack").val(respo["race_track"]);
                            $("#horseOperation").val(1);
                        },
                        error : function(xhr, status, error){
                            alert(error);
                        }
                    });
                    $("#horseModal").modal("show");
                },
                error : function(xhr, status,error){
                    alert(error);
                }
            });
//            $.ajax({
//                "url" : BASE_URL + '/getHorseData',
//                type : "POST",
//                data : {
//                    _token : $('[name="_token"]').val(),
//                    id : id
//                },
//                success : function(respo){
//                    console.log(respo);
//                    $("#horsePP").val(respo["pp"]);
//                    $("#horseName").val(respo["horse"]);
//                    $("#jockeyName").val(respo["jockey"]);
//                    $("#raceNumber").val(respo["race_number"].replace(/\D/g,''));
//                    $("#postTime").val($.trim(respo["race_time"]));
//                    $("#selectTrack").val(respo["race_track"]);
//                },
//                errors : function(xhr, status, errors){
//                    alert(errors);
//                }
//            });
//            $("#horseModal").modal("show");
        });
        $("body").delegate(".undo","click", function(){
            var id = $(this).data("id");
            var pnum = $(this).data("pnumber");
            var trk = $(this).data("track");
            var raceDate = $(this).data("date");
            var raceNum = $(this).data("num").replace(/\D/g,'');
            $.ajax({
                "url" : BASE_URL + '/undoScratch',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    id : id,
                    pnum : pnum,
                    date : raceDate,
                    num : raceNum,
                    trk : trk
                },
                success : function(data){
                    if(data == 0){
                        swal("Success","","success");
                    }
                    else{
                        swal("Failed!","","error");
                    }
                    $("button.confirm").on("click", function(){
                        location.reload();
                    });
                },
                error : function(xhr, status, err){
                    alert("Error : " + err);
                }
            });
        });
    });
    function loadHorsesDataTable(){
        $("#tblHorses").DataTable({
            "aaSorting": [],
            "aoColumnDefs": [
//                { "aTargets": [ 4 ], "bSortable": false }
            ]
        });
    }
    function clearFrm(){
        var form = $("#frmHorses");
        form[0].reset();
    }
</script>

<div id="horseModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">HORSE</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" id="horseOperation" name="horseOperation" value="0">
                    <input type="hidden" id="horseId" name="horseId">
                    <form id="frmHorses">
                        <label for="horsePP">PP :</label>
                        <input type="text" class="form-control" id="horsePP" name="horsePP" placeholder="PP">
                        <label for="horseName">Horse Name :</label>
                        <input type="text" class="form-control" id="horseName" name="horseName" placeholder="Horse Name">
                        <label for="jockeyName">Jockey Name :</label>
                        <input type="text" class="form-control" id="jockeyName" name="jockeyName" placeholder="Jockey Name">
                        <label for="raceNumber">Race Number :</label>
                        <input type="text" class="form-control" id="raceNumber" name="raceNumber" placeholder="Race Number">
                        <label for="postTime">Post Time :</label>
                        <input type="text" class="form-control" id="postTime" name="postTime" placeholder="Post Time">
                        <label for="selectTrack">Select Track:</label>
                        <select id="selectTrack" name="selectTrack" class="form-control">
                            <option>-- SELECT TRACK --</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btnSubmitNewHorse">Save</button>
            </div>
        </div>

    </div>
</div>
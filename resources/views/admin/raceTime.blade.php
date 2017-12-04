<?php
    date_default_timezone_set('America/Los_Angeles');
?>
<style>
    .editTime{width: 100%;}
    td,th{text-align: center}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h1>RaceTime</h1>
                {{--<input type="text" id="date">--}}
                <input type="hidden" id="date" value="<?php echo date("mdy",time()) ?>">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
            <div>
                <table class="table table-responsive table-bordered table-striped table-hover" id="tblRaceTime">
                    <thead>
                    <tr>
                        <th>Race Track</th>
                        <th>Race Number</th>
                        <th>Race Time</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                        @foreach($raceTime as $index => $value)
                        <tr>
                            <td>{{ \App\Tracks::getTrackNameWithCode($value->race_track)->name }}</td>
                            <td>{{ $value->race_number }}</td>
                            <td>{{ htmlspecialchars($value->race_time) }}</td>
                            <td>
                                <button class="btn btn-success editTime" data-trk="{{ $value->race_track }}" data-num="{{ $value->race_number }}" data-time="{{ $value->race_time }}">EDIT</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="raceTimeModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Race Time</h4>
            </div>
            <div class="modal-body">
                <form id="frmRaceTime" class="form-group">
                    <label for="trk">RaceTrack: </label> <input type="text" name="trk" id="trk" class="form-control" placeholder="Race Track" disabled>
                    <label for="num">RaceNumber: </label> <input type="text" name="num" id="num" class="form-control" placeholder="Race Number" disabled>
                    <label for="time">RaceTime: </label> <input type="text" name="time" id="time" class="form-control" placeholder="Race Time">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="submitRaceTime">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    $("document").ready(function(){
         var BASE_URL = $("#hiddenURL").val();
//        $("#date").datepicker({
//            dateFormat: "yy-mm-dd"
//        });
        loadDataTable();
        $("body").delegate(".editTime","click",function(){
            $(".form-control").val("");
            $("#trk").val($(this).data("trk"));
            $("#num").val($(this).data("num"));
            $("#time").val($(this).data("time"));
            $("#raceTimeModal").modal("show");
        });
        function loadDataTable(){
            $("#tblRaceTime").dataTable();
        }
        $("#submitRaceTime").on("click",function(){
            $.ajax({
                "url" : BASE_URL + '/submitRaceTime',
                type : "POST",
                data : {
                    date : $("#date").val(),
                    trk : $("#trk").val(),
                    num : $("#num").val(),
                    time : $("#time").val(),
                    _token : $('[name="_token"]').val(),
                },
                success : function(response){
                    if(response == 0){
                        swal("Success");
                    }else{
                        swal("Failed");
                    }
                },
                error : function(xhr,status,error){
                    alert(error);
                }
            });
        });
    });
</script>
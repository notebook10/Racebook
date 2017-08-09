<?php
    date_default_timezone_set('America/Los_Angeles');
?>
<style>
    th,td{text-align: center}
</style>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" id="date" value="<?php echo date('mdy',time()); ?>">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h1>Wager Type</h1>
                <button class="btn btn-primary" id="btnAddWager">Add Wager</button>
            </div>
            <div>
                <table class="table table-responsive table-striped table-bordered table-hover" id="tblWager">
                    <thead>
                        <tr>
                            <th>Track Code</th>
                            <th>Race Date</th>
                            <th>Race Number</th>
                            <th>Post Time</th>
                            <th>Wager</th>
                            <th>Aksyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wager as $key => $value)
                            <tr>
                                <td>{{ $value->track_code }}</td>
                                <td>{{ $value->race_date }}</td>
                                <td>{{ $value->race_number }}</td>
                                <td>{{ $value->race_time }}</td>
                                <td>
                                    <?php
                                        $wagerArr = unserialize($value->extracted);
                                        $temp = "";
                                        foreach ($wagerArr as $index => $val){
                                            $temp .= $val . ', ';
                                        }
                                        $trim = rtrim($temp,",");
                                        echo $trim;
                                    ?>
                                </td>
                                <td><input type="button" class="btn btn-primary editWager" value="EDIT" data-id="{{ $value->id }}"> </td>
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
        loadWagerDataTable();
        var BASE_URL = $("#hiddenURL").val();
        $("#btnAddWager").on("click", function(){
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
                    clearFrm();
                    $("#wagerModal").modal("show");
                },
                error : function(xhr, status,error){
                    alert(error);
                }
            });
        });
        $("#btnSubmitWager").on("click", function(){
            $.ajax({
                "url" : BASE_URL + '/submitNewWager',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : $("#date").val(),
                    frm : $("#frmWager").serializeArray(),
                    operation : $("#wagerOperation").val(),
                    id : $("#updateID").val()
                },
                success : function(data){
                    if(data == 0){
                        swal("Success","","success");
                    }else if(data == 1){
                        swal("Error","","error");
                    }
                    $("button.confirm").on("click", function(){location.reload();});
                },
                error : function(xhr,status,error){
                    alert("errors: " + error);
                }
            });
        });
        $("body").delegate(".editWager","click", function(){
            var id = $(this).data("id");
            $("#updateID").val(id);
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
                        "url" : BASE_URL + '/getWagerByRace',
                        type : "POST",
                        data : {
                            _token : $('[name="_token"]').val(),
                            id : id
                        },
                        success : function(respo){
                            clearFrm();
                            $("#wagerOperation").val(1);
                            $("#selectTrack").val(respo["track_code"]);
                            $("#raceNumber").val(respo["race_number"]);
                            $.each(respo["extracted"], function(i,v){
                                switch (v){
                                    case "Exacta":
                                        $("#exacta").attr("checked",true);
                                        break;
                                    case "Trifecta":
                                        $("#trifecta").attr("checked",true);
                                    case "Superfecta":
                                        $("#superfecta").attr("checked",true);
                                        break;
                                    case "Daily Double":
                                        $("#dd").attr("checked",true);
                                        break;
                                    case "WPS":
                                        $("#wps").attr("checked",true);
                                        break;
                                    default:break;
                                }
                            });
                            $("#wagerModal").modal("show");
                        },
                        error : function(xhr, status, error){
                            alert(error);
                        }
                    });
                },
                error : function(xhr, status,error){
                    alert(error);
                }
            });
        });
    });
    function loadWagerDataTable(){
        $("#tblWager").DataTable({
            "aaSorting": [],
            "aoColumnDefs": [
                { "aTargets": [ 1 ], "bSortable": false },
                { "aTargets": [ 4 ], "bSortable": false },
                { "aTargets": [ 5 ], "bSortable": false }
            ]
        });
    }
    function clearFrm(){
        var form = $("#frmWager");
        form[0].reset();
        $("label.errors").css("display","none");
        $("input[type='checkbox']").each(function(){
            $(this).attr("checked",false);
        });
    }
</script>

<div id="wagerModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">WAGERRRRRRRRRRRRRRRRRR!!!!!!!!!!!!!!!!!!!!!!!</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="wagerOperation" name="wagerOperation" value="0">
                <input type="hidden" id="updateID" name="updateID">
                <form id="frmWager" name="frmWager">
                    <div class="form-group">
                        <label for="selectTrack">Track :</label>
                        <select id="selectTrack" name="selectTrack" class="form-control">
                            <option selected disabled>-- SELECT TRACK --</option>
                        </select>
                        <label for="raceNumber">Race Number:</label>
                        <input type="text" class="form-control" id="raceNumber" name="raceNumber">
                        <p>Follow up date here <--</p>
                        <label>Wager:</label>
                        <div class="checkbox">
                            <label><input type="checkbox" value="exacta" id="exacta" name="exacta">Exacta</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="trifecta" id="trifecta" name="trifecta">Trifecta</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="superfecta" id="superfecta" name="superfecta">Superfecta</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="dd" id="dd" name="dd">Daily Double</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="wps" id="wps" name="wps">Win / Place / Show</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="btnSubmitWager">Submit</button>
            </div>
        </div>

    </div>
</div>
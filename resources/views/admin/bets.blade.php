<?php
date_default_timezone_set('America/Los_Angeles');
//echo date('mdy',time());
?>
<style>
    th{text-align: center}
    .defeat{color: #fff;background: #ff4d28 !important;}
    .null{color: #020202;background: #fffdcb !important;}
    .victory{color: #fff;background: #00724b  !important;}
    .scratched{color:#fff;background: #000 !important;}
    label.error{color:red;font-size: 9px;}
</style>
{{--<input id="date">--}}
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" id="tempDate" value="<?php echo date('mdy',time()); ?>">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <input type="button" class="btn btn-primary" value="Add Bet" id="btnAddBet">
                <div>
                    <table class="table table-bordered table-responsive table-stripped" id="tblBets">
                        <thead>
                            <tr>
                                <th>Player ID</th>
                                <th>Race Number</th>
                                <th>Race Track</th>
                                <th>Bet Type</th>
                                <th>Bet</th>
                                <th>Bet Amount</th>
                                {{--<th>Post Time</th>--}}
                                <th>Status</th>
                                <th>Result</th>
                                <th>Return</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($betsToday as $key => $value)
                                <?php
                                    $resultVar = "";
                                    if($value->result == 0){
                                        $resultVar = "Null";
                                    }else if($value->result == 1){
                                        $resultVar = "Victory";
                                    }else if($value->result == 2){
                                        $resultVar = "Defeat";
                                    }else if($value->result == 3){
                                        $resultVar = "Scratched";
                                    }
                                ?>
                                <tr class="<?php echo strtolower($resultVar) ?>">
                                    <td>{{ $value->player_id }}</td>
                                    <td>{{ "Race " . $value->race_number }}</td>
                                    <td>{{ \App\Tracks::getTrackNameWithCode($value->race_track)->name }}</td>
                                    <td>
                                        <?php
                                            if($value->bet_type === "wps"){
                                                echo $value->type;
                                            }else{
                                                echo $value->bet_type;
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo str_replace(',','-',$value->bet) ?></td>
                                    <td>{{ $value->bet_amount }}</td>
                                    {{--<td>{{ $value->post_time }}</td>--}}
                                    <td>
                                        <?php
                                            if($value->status === 0){
                                                echo "Pending";
                                            }else{
                                                echo "Graded";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $resultVar; ?>
                                    </td>
                                    <td>{{ $value->win_amount }}</td>
                                    <td>{{ $value->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("document").ready(function(){
        var BASE_URL = $("#hiddenURL").val();
        loadBetsDataTable();
        $("#date").datepicker({
            dateFormat: "yy-mm-dd"
        });
        $("#date").on("change", function(){
            var date = $(this).val();
            $.ajax({
                "url" : BASE_URL + '/getBets',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : date
                },
                success : function(response){
                    var t = $("#tblBets").DataTable();
                    if(!$.trim(response)){
                        t.rows().remove().draw();
                    }else{
                        t.rows().remove().draw();
                        $.each(response, function(index, value){
                            var status = response[index]["status"] == 0 ? "Pending" : "Graded";
                            var result = response[index]["result"] == 0 ? "Defeat" : "Victory";
                            t.row.add([
                                response[index]["player_id"],
                                response[index]["race_number"],
                                response[index]["race_track"],
                                response[index]["bet_type"],
                                response[index]["bet"],
                                response[index]["bet_amount"],
                                response[index]["post_time"],
                                status,
                                result,
                                response[index]["win_amount"]
                            ]).draw(false);
                        });
                    }
                },
                error : function(xhr, status, error){
                    alert(error);
                }
            });
        });
        $("#btnAddBet").on("click", function(evt){
            var date = $("#tempDate").val();
            $.ajax({
                "url" : BASE_URL + '/getTracksToday',
                type : "post",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : date
                },
                success : function(data){
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
//                            case "Exacta Box":
//                                $("#wager").append("<option value='exactabox'>Exacta Box</option>");
//                                break;
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
            switch (wager){
                case "exacta":
                    $("#frmBets").append("<div><label for='first' class='horseLabel'>First Horse:</label><input type='text' class='form-control horse' placeholder='FIRST' id='first' name='first'></div>");
                    $("#frmBets").append("<div><label for='second' class='horseLabel'>Second Horse:</label><input type='text' class='form-control horse' placeholder='SECOND' id='second' name='second'></div>");
                    break;
                case "trifecta":
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
                    location.reload();
                });
            }
        };
        $("#frmBets").ajaxForm(optionsBets);
    });
    function loadBetsDataTable(){
        $("#tblBets").DataTable({
            "aaSorting": [],
        });
    }
</script>


<div id="betModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">BETTTTTTTTTTTTTTTTTTSSSSSSSSSSSSSSSSSS!!!!!!!!!!!!!!!</h4>
            </div>
            <div class="modal-body">
                <form id="frmBets" class="form-group" method="post" action="submitNewBet">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div>
                        <label for="playerID">Player ID:</label>
                        <input type="text" class="form-control" id="player_id" name="player_id" placeholder="Player ID">
                    </div>
                    <div>
                        <label for="amount">Amount:</label>
                        <input type="text" class="form-control" id="amount" name="amount" placeholder="Bet amount">
                    </div>
                    <div>
                        <label for="raceTrack">Race Track:</label>
                        <select id="raceTrack" name="raceTrack" class="form-control">
                            <option selected disabled>-- SELECT TRACK --</option>
                        </select>
                    </div>
                    <div>
                        <label for="raceNum">Race Number:</label>
                        <select id="raceNum" name="raceNum" class="form-control" disabled>
                            <option selected disabled>-- RACE NUMBER --</option>
                        </select>
                    </div>
                    <div>
                        <label for="wager">Wager Type:</label>
                        <select id="wager" name="wager" class="form-control" disabled>
                            <option disabled selected>-- WAGER --</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnSubmitNewBet">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
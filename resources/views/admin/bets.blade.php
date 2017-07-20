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
</style>
<input id="date">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
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
                                <th>Post Time</th>
                                <th>Status</th>
                                <th>Result</th>
                                <th>Return</th>
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
                                    <td>{{ $value->bet }}</td>
                                    <td>{{ $value->bet_amount }}</td>
                                    <td>{{ $value->post_time }}</td>
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
//                        $("table#tblBets tbody").append("<tr><td>"+ response[index]["player_id"] +"</td><td> Race "+ response[index]["race_number"] +"</td><td>"+ response[index]["race_track"] +"</td>" +
//                            "<td>"+ response[index]["bet_type"] +"</td><td>"+ response[index]["bet_amount"] +"</td><td>"+ response[index]["post_time"] +"</td>" +
//                            "<td>"+  status +"</td></tr>");

                        });
                    }
                },
                error : function(xhr, status, error){
                    alert(error);
                }
            });
        });
        function loadBetsDataTable(){
            $("#tblBets").DataTable();
        }
    });
</script>
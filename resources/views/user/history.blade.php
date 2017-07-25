{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
{{--<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">--}}
{{--<link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">--}}
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>--}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
{{--<script src="{{ asset('js/sweetalert.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/jquery.datatables.js') }}"></script>--}}
<style>
    .defeat{color: #fff;background: #ff4d28 !important;}
    .null{color: #020202;background: #faf7ed !important;}
    .victory{color: #fff;background: #00724b  !important;}
    .scratched{color:#fff;background: #000 !important;}
    th,td{text-align: center;}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1>Past Bets</h1>
            </div>
            <div>
                <table class="table table-responsive table-bordered table-striped" id="tblHistory">
                    <thead>
                        <tr>
                            <th>Wager Type</th>
                            <th>Race Track</th>
                            <th>Race Number</th>
                            <th>Horses</th>
                            <th>Amount</th>
                            <th>Post Time</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Return</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $key => $value)
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
                            <tr class="<?php echo strtolower($resultVar); ?>">
                                <td>
                                    <?php
                                    if($value->bet_type === "wps"){
                                        echo $value->type;
                                    }else{
                                        echo $value->bet_type;
                                    }
                                    ?>
                                </td>
                                <td>{{ \App\Http\Controllers\HomeController::getTrack($value->race_track) }}</td>
                                <td>{{ $value->race_number }}</td>
                                <td>{{ $value->bet }}</td>
                                <td>{{ $value->bet_amount }}</td>
                                <td>{{ $value->post_time }}</td>
                                <td>
                                    <?php
                                        switch ($value->status){
                                            case 0:
                                                echo "Pending";
                                                break;
                                            case 1:
                                                echo "Graded";
                                                break;
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        echo $resultVar;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        if($value->win_amount <= 0 || $value->win_amount == ""){
                                            echo "---";
                                        }else{
                                            echo $value->win_amount;
                                        }
                                    ?>
                                </td>
                                <td>{{ $value->created_at }}</td>
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
        loadHistoryDataTable();
        function loadHistoryDataTable(){
            $("#tblHistory").on('draw.dt',function(){
                console.log("Loading");
            }).DataTable({
                "aaSorting": [],
                oLanguage: {
                    sProcessing: "TEST"
                },
            });
        }
    });
</script>
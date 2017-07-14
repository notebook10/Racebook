{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
{{--<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">--}}
{{--<link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">--}}
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>--}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
{{--<script src="{{ asset('js/sweetalert.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/jquery.datatables.js') }}"></script>--}}

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
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $key => $value)
                            <tr>
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
                                        if($value->result == 0){
                                            echo "Null";
                                        }else if($value->result == 1){
                                            echo "Victory";
                                        }else if($value->result == 2){
                                            echo "Defeat";
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
            $("#tblHistory").DataTable({
                "aaSorting": []
            });
        }
        $("tr.trRed").css("color","red");
        $("tr.trGreen").css("color","green");
    });
</script>
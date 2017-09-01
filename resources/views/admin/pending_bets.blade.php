<?php
date_default_timezone_set('America/Los_Angeles');
//echo date('mdy',time());
?>
<style>
    th{text-align: center}
    .defeat, .lose{color: #000;background: #f5856e !important;}
    .null{color: #020202;background: #faf7ed !important;}
    .victory, .win{color: #000;background: #90de70 !important;}
    .scratched, .aborted{color:#fff;background: #000 !important;}
    .nopayout{color:#fff;background: orange !important;}
    label.error{color:red;font-size: 9px;}
    .sa-errors-container{display: none !important;}
</style>
<script src="{{ asset('js/bets.js') }}"></script>
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
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($betsToday as $key => $value)
                            <?php
                            $resultVar = "";
                            if($value->result == 0){
                                $resultVar = "Null";
                            }else if($value->result == 1){
                                $resultVar = "Win";
                            }else if($value->result == 2){
                                $resultVar = "Lose";
                            }else if($value->result == 3){
                                $resultVar = "Aborted";
                            }else if($value->result == 4){
                                $resultVar = "NoPayout";
                            }
                            ?>
                            <tr class="<?php echo strtolower($resultVar) ?>">
                                <td>
                                    {{ \App\Http\Controllers\AdminController::getUsernameById($value->player_id)->firstname }}
                                </td>
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
                                <td>{{ number_format($value->win_amount,2) }}</td>
                                <td>{{ $value->created_at }}</td>
                                <td><input type="button" class="btn btn-primary editBet" data-id="{{ $value->id }}" value="Edit"> </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include("admin/modal/betsModal")
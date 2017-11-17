<?php
date_default_timezone_set('America/Los_Angeles');
?>
<script src="{{ asset("js/homeHeader.js") }}"></script>
<header>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="{{ URL::to('dashboard') }}">HOME</a></li>
                    <li><a href="{{ URL::to('dashboard') }}/past">PAST BETS</a></li>
                    <li><a href="{{ URL::to('dashboard') }}/pending">PENDING</a></li>
                    <li><a href="{{ URL::to('dashboard') }}/results">RESULTS</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <?php
                            if (!isset($_SESSION)) session_start();
                            if (!isset($_SESSION["username"])){
                                echo "<a>Current Bet: $<small class='red'> 0.00</small></a>";
                            }else{
                                $odbc = odbc_connect($_SESSION["dsn"],'','');
                                $query = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($_SESSION["username"]) ."'";
                                $queryResult = odbc_exec($odbc,$query);
                                while($row = odbc_fetch_array($queryResult)){
                                    $_SESSION["CURRENTBET"] = $row["CURRENTBET"];
                                }
                                echo "<a>Current Bet: <small class='gold'>" . htmlspecialchars(number_format($_SESSION["CURRENTBET"],2)) . "</small></a>";
                            }
                        ?>
                    </li>
                    <li>
                        <?php
                        if (!isset($_SESSION)) session_start();
                        if (!isset($_SESSION["username"])){
                            echo "<a>Balance: $<small class='red'> 0.00</small></a>";
                        }else{
                            $odbc = odbc_connect($_SESSION["dsn"],'','');
                            $query = "select * from cust.dbf as a where ucase(NAME) = '". strtoupper($_SESSION["username"]) ."'";
                            $queryResult = odbc_exec($odbc,$query);
                            while($row = odbc_fetch_array($queryResult)){
                                $_SESSION["BALANCE"] = $row["BALANCE"] + $row["CAP"] + $row["CURRENTBET"] + $row["MON_RSLT"] + $row["TUE_RSLT"] + $row["WED_RSLT"] + $row["THU_RSLT"] + $row["FRI_RSLT"] + $row["SAT_RSLT"] + $row["SUN_RSLT"];
                            }
                            echo "<a> Balance: $ <small class='gold'>" . htmlspecialchars(number_format($_SESSION["BALANCE"],2)) . "</small></a>";
                        }
                        ?>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Welcome
                            <?php
                                if (!isset($_SESSION["username"])){
                                    echo "<small class='red'>???</small>";
                                }else{
                                    echo "<small class='gold'>" . htmlspecialchars($_SESSION["username"]) . "</small>";
                                }
                            ?><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" id="btnHistory"><span class="glyphicon glyphicon-list-alt"></span> Weekly Record</a> </li>
                            <li><a href="logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<style>
    small.red{color:red;}
    small.gold{color:gold;}
    nav.navbar.navbar-inverse {
        background: #00724b !important;
        border: none !important;
        border-radius: 0 !important;
    }
    .navbar-inverse .navbar-nav>li>a {
        color: #ffffff !important;
    }
    .navbar-inverse .navbar-nav>.open>a, .navbar-inverse .navbar-nav>.open>a:focus, .navbar-inverse .navbar-nav>.open>a:hover {
        color: #fff !important;
        background-color: transparent !important;
    }
    #week{text-align: center}
    #historyDatePicker{text-align: center;}
</style>

<div id="historyModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Racebook Weekly Record</h4>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 80px;text-align: center;">Select Week: <input type="text" id="historyDatePicker" value="<?php echo date('Y-m-d',time()) ?>"></div>
                <div>
                    <h3 id="week"></h3>
                </div>
                <div class="text-center">
                    <table class="table table-responsive table-striped table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                                <th>Saturday</th>
                                <th>Sunday</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Win / Loss</td>
                                <td id="monday"></td>
                                <td id="tuesday"></td>
                                <td id="wednesday"></td>
                                <td id="thursday"></td>
                                <td id="friday"></td>
                                <td id="saturday"></td>
                                <td id="sunday"></td>
                            </tr>
                        </tbody>
                    </table>
                    <h3 id="balance"></h3>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

{{--<script>--}}
    {{--$("document").ready(function(){--}}
        {{--var BASE_URL = $("#hdnURL").val();--}}
        {{--var CURRENT_DATE = $("#historyDatePicker").val();--}}
        {{--$("#historyDatePicker").datepicker({--}}
            {{--dateFormat: "yy-mm-dd",--}}
            {{--maxDate : -1--}}
        {{--});--}}
        {{--loadHistory(BASE_URL,CURRENT_DATE);--}}
        {{--$("#btnHistory").on("click",function(){--}}
            {{--$("#historyModal").modal("show");--}}
        {{--});--}}
        {{--$("#historyDatePicker").on("change",function(){--}}
            {{--var selectedDate = $(this).val();--}}
            {{--loadHistory(BASE_URL,selectedDate);--}}
        {{--});--}}
        {{--function loadHistory(url,date){--}}
            {{--$.ajax({--}}
                {{--"url" : url + '/dashboard/getWeek',--}}
                {{--type : "POST",--}}
                {{--data : {--}}
                    {{--_token : $('[name="_token"]').val(),--}}
                    {{--date : date--}}
                {{--},--}}
                {{--success : function (response) {--}}
                    {{--console.log(response);--}}
                    {{--$("#week").text("WEEK SUMMARY: FROM " +response['start'] + " TO " +response['end']);--}}
                    {{--$("#monday").text(response["monday"]);--}}
                    {{--$("#tuesday").text(response["tuesday"]);--}}
                    {{--$("#wednesday").text(response["wednesday"]);--}}
                    {{--$("#thursday").text(response["thursday"]);--}}
                    {{--$("#friday").text(response["friday"]);--}}
                    {{--$("#saturday").text(response["saturday"]);--}}
                    {{--$("#sunday").text(response["sunday"]);--}}
                    {{--$("#balance").text("WEEKLY BALANCE: " + response["balance"]);--}}
                {{--}--}}
            {{--});--}}
        {{--}--}}
    {{--});--}}
{{--</script>--}}
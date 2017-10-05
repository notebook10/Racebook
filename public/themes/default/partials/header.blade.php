<?php
date_default_timezone_set('America/Los_Angeles');
?>
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
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <?php
                            if (!isset($_SESSION)) session_start();
                            echo "<a>Balance: $" . htmlspecialchars(number_format($_SESSION["BALANCE"],2)) . "</a>";
                        ?>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Welcome
                            <?php
                                echo htmlspecialchars($_SESSION["username"]);
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

<script>
    $("document").ready(function(){
        var BASE_URL = $("#hdnURL").val();
        var CURRENT_DATE = $("#historyDatePicker").val();
        $("#historyDatePicker").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate : -1
        });
        loadHistory(BASE_URL,CURRENT_DATE);
        $("#btnHistory").on("click",function(){
            $("#historyModal").modal("show");
        });
        $("#historyDatePicker").on("change",function(){
            var selectedDate = $(this).val();
            loadHistory(BASE_URL,selectedDate);
//            $.ajax({
//                "url" : BASE_URL + '/dashboard/getWeek',
//                type : "POST",
//                data : {
//                    _token : $('[name="_token"]').val(),
//                    date : selectedDate
//                },
//                success : function (response) {
//                    console.log(response);
//                    $("#week").text("WEEK SUMMARY: " +response['start'] + " TO " +response['end']);
//                    $("#monday").text(response["monday"]);
//                    $("#tuesday").text(response["tuesday"]);
//                    $("#wednesday").text(response["wednesday"]);
//                    $("#thursday").text(response["thursday"]);
//                    $("#friday").text(response["friday"]);
//                    $("#saturday").text(response["saturday"]);
//                    $("#sunday").text(response["sunday"]);
//                }
//            });
        });
        function loadHistory(url,date){
            $.ajax({
                "url" : url + '/dashboard/getWeek',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : date
                },
                success : function (response) {
                    console.log(response);
                    $("#week").text("WEEK SUMMARY: FROM " +response['start'] + " TO " +response['end']);
                    $("#monday").text(response["monday"]);
                    $("#tuesday").text(response["tuesday"]);
                    $("#wednesday").text(response["wednesday"]);
                    $("#thursday").text(response["thursday"]);
                    $("#friday").text(response["friday"]);
                    $("#saturday").text(response["saturday"]);
                    $("#sunday").text(response["sunday"]);
                    $("#balance").text("WEEKLY BALANCE: " + response["balance"]);
                }
            });
        }
    });
</script>
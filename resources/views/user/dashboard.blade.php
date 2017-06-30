<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>
<?php
date_default_timezone_set('America/Los_Angeles');
use App\Horses;
?>

<style>
    #selectWager {
        width: 178px;
    }
    .panel-group .panel {
        border-radius: 10px;
        box-shadow: none;
        border-color: #EEEEEE;
        background: #00724b;
    }

    .panel-default > .panel-heading {
        padding: 0;
        border-radius: 0;
        color: #212121;
        background-color: #FAFAFA;
        border-color: #EEEEEE;
    }

    .panel-title {
        font-size: 14px;
    }
    .s-wager, #selectWager {
        display: inline;
        margin-bottom: 15px;
    }
    .panel-title > a {
        display: block;
        padding: 0 15px;
        text-decoration: none;
        background: #00724b;
        color: #ffffff;
    }
    .panel-body {
        background: #ffffff;
        color: #328e6e;
        border-bottom-right-radius: 6px;
        border-bottom-left-radius: 6px;
        padding:0;
    }
    .more-less {
        float: right;
        color: #ffffff;
    }
    .form-control:focus {
        border-color: #00724b;
        box-shadow: inset 0 1px 1px rgba(76,192,152, 0.75), 0 0 8px rgba(0,114,75, 0.7);
    }
    select option:hover {
        color: #000;
        box-shadow: inset 20px 20px #00f;
    }

    .panel-default > .panel-heading + .panel-collapse > .panel-body {
        border-top-color: #EEEEEE;
    }
    .horse-track {
        padding-top: 60px;
        padding-bottom: 60px;
    }
    .title-menu {
        font-weight: bold;
    }
    .raceNum:hover, .raceNum:focus, .raceNum:active{cursor: pointer; color: #00724b;}
    .raceNum{padding: 5px 25px; color: #333;}
    #date {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    #betAmount {
        width: 200px;
    }
    #betAmount, #submitBetButton {
        display: inline;
    }
    .clock, #raceDiv, #betTicket{display: none;}
    div#raceNumberAndPostTime {
        margin: 20px;
        text-align: center;
        font-size: 18px;
    }
    div#raceTrackName {
        text-align: center;
        font-size: 23px;
        font-weight: bold;
        background: #ededed;
        border: 1px solid #dcdcdc;
    }
    .pp-class,.tdPP  {
        width: 100px;
        text-align: center;
    }
    .col-tracks {
        background: #ededed;padding: 10px 20px; border: 1px solid #dcdcdc; max-height: 756px; overflow: auto;
    }
    .col-tracks::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        background-color: #F5F5F5;
        width: 65px;
        background: #F5F5F5;
        overflow-y: scroll;
    }
    #betActions{text-align: center;}
    #confirmBet{margin-right:40px;}
    #ddBoard{margin:20px;text-align:center;font-size:18px;}
    #tempRaces table tr,th{text-align: center;}
    th.pp-class{width:6%;}
    td.pp-class{font-weight: bold;}
    table.dailydouble th:nth-child(2){width:6%;}
</style>

<input type="hidden" id="hiddenURL" value="{{ URL::to('/') }}">
<input type="hidden" name="_token" value="{{csrf_token()}}">
<div class="container">
    <div class="row">
        <div class="col-md-4 col-tracks">
            <h3 id="date" data-date="<?php echo date('mdy',time()); ?>">TRACKS RACING TODAY - <?php echo date('F d, Y h:i:s', time()); ?></h3>
            <h5 id="pdt" class="clock"></h5>
            <h5 id="mdt" class="clock"></h5>
            <h5 id="cdt" class="clock"></h5>
            <h5 id="edt" class="clock"></h5>
            {{-- ---------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                @foreach($tracks as $value)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="collapsed trkName" role="button" data-toggle="collapse" data-code="{!! $value->code !!}" data-parent="#accordion" href="#{!! $value->code !!}" aria-expanded="false" aria-controls="{!! $value->code !!}">
                                    <i class="more-less glyphicon glyphicon-plus"></i>
                                    <h5 class="title-menu">{!! $value->name !!}</h5>
                                </a>
                            </h4>
                        </div>
                        <div id="{!! $value->code !!}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body {!! $value->code !!}">

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- ---------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            {{--@foreach($tracks as $value)--}}
            {{--<div class="trkName" data-code="{!! $value->code !!}">{!! $value->name !!}</div>--}}
            {{--@endforeach--}}
        </div>
        <div class="col-md-8">
            <!-- Upcoming Races -->
            <div id="upcomingRacesDiv">
                <h1>Upcoming Races</h1>
                <table class="table table-bordered table-striped" id="tblUpcomingRace">
                    <thead>
                        <tr>
                            <th>Track</th>
                            <th>Race</th>
                            <th>MTP</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="raceDiv">
                <!-- Race Info -->
                <div id="board">
                    <div id="raceTrackName"></div>
                    <div id="raceNumberAndPostTime"></div>
                </div>
                <!-- Wager -->
                <div>
                    <label class="s-wager">Select Wager Type: </label>
                    <select id="selectWager" class="form-control">
                        <option value="wps">Win/Place/Show</option>
                        <option value="dailydouble">Daily Double</option>
                        <option value="superfecta">Superfecta</option>
                        <option value="exacta">Exacta</option>
                        <option value="exactabox">Exacta Box</option>
                        <option value="trifecta">Trifecta</option>
                        <option value="trifectabox">Trifecta Box</option>
                    </select>
                    <input type="hidden" id="selectedTrkAndRace" data-trk="" data-raceNum="">
                    <input type="hidden" id="selectedTrack">
                    <input type="hidden" id="selectedRaceNum">
                    <input type="hidden" id="selectedRacePostTime">
                </div>
                <div id="tempRaces"></div>
                <div id="submitBet" style="display: none">
                    <input type="text" id="betAmount" class="form-control" placeholder="Put your bet">
                    <button id="submitBetButton" class="btn btn-success">SUBMIT BET</button>
                </div>
            </div>
            <div id="betTicket">
                <!-- Confirm Bet -->
                <div id="ticket">
                    <table id="ticketTbl" class="table table-bordered table-striped ">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="betActions">
                        <button class="btn btn-success" id="confirmBet">CONFIRM BET</button>
                        <a href="{{ URL::to('/') }}" class="btn btn-danger">ABORT BET</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("document").ready(function(){
        var BASE_URL = $("#hiddenURL").val();
        var CURRENT_DATE = $("#date").data("date");
        setTimeout(getUpcomingRaces,3000);
        $(".trkName").on("click",function(){
            if($(this).hasClass("collapsed")){
                //                $(".trkName > div.panel").css("color","red");
                $(this).next("div.panel-body").find("div.raceNum").remove();
                var code = $(this).data("code");
                $.ajax({
                    "url" : BASE_URL + "/dashboard/getRaces",
                    type : "POST",
                    data : {
                        _token : $('[name="_token"]').val(),
                        code : code,
                        date : CURRENT_DATE
                    },
                    dataType: "json",
                    success : function(response){
                        var od = JSON.stringify(response);
                        var obj = JSON.parse(od);
                        var raceCount = "";
                        var raceTime = "";
                        var raceTimeArr = [];
                        var race = []; //array to get first race number
                        $.each(obj, function(index, value){
                            // For race number
                            if(raceTimeArr.indexOf(obj[index].race_time) > -1){}else{
                                raceTimeArr.push(obj[index].race_time);
                            }
                            // For race number
                            if(race.indexOf(obj[index].race_number) > -1){}else{
                                race.push(obj[index].race_number);
                            }
                        });
                        console.log(race);
                        var firstRace = race[0].replace(/\D/g,'');
                        for(var i = firstRace; i <= raceTimeArr.length; i++){
                            $(".panel-body."+ code).append("<div class='raceNum' data-number='" + i + "' data-track='"+ code+"' data-post='"+ raceTimeArr[i-1] +"'>RACE "+ i +" : "+ raceTimeArr[i-1] +" </div>").addClass("on");
                        }
                    },
                    error : function(){
                        alert("error");
                    }
                });

            }else{
                $(".panel-body div.raceNum").remove();
            }
            $(".panel-body div.raceNum").remove();
        });
//        $("body").delegate(".raceNum","dblclick", function(evt){
//            evt.preventDefault();
//            return false;
//        });
        $(".raceNum").unbind("dblclick");
        $("body").delegate(".raceNum","click",function(){
            $("#tempRaces table").remove();
            $("#betTicket").css("display","none");
            $("#betAmount").val("");
            var wager = $("#selectWager").val();
            var trk = $(this).data("track");
            var num = $(this).data("number");
            var post = $(this).data("post");
            var ddselectedRaceNum = parseInt(num) + parseInt(1);
//            $("#tempRaces").append("<ul class='"+ trk + num +"'></ul>");
            switch(wager){
                case "wps":
                    $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>W</th><th>P</th><th>S</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "superfecta":
                    $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>4</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "exacta":
                    $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "exactabox":
                    $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "trifecta":
                    $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "trifectabox":
                    $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "dailydouble":
                    $("#tempRaces div#ddBoard").html("");
                    $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    $("#tempRaces").append("<div id='ddBoard'><div> Race "+ ddselectedRaceNum +" </div></div><table class=' table table-bordered table-striped "+ trk + ddselectedRaceNum + " dailydouble'><thead><tr><th>1</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    ajaxGetHorsesPerRace(BASE_URL,trk,CURRENT_DATE, ddselectedRaceNum);
                    break;
                default:
                    break;
            }
//            $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
            $.ajax({
                "url" : BASE_URL + "/dashboard/getHorsesPerRace",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    code : trk,
                    date : CURRENT_DATE,
                    number : num
                },
                success : function(response){
                    var od = JSON.stringify(response);
                    var obj = JSON.parse(od);
                    console.log(obj);
                    $("table."+ trk + num +" tbody tr").html("");
                    $.each(obj, function(index, value){
                        switch(wager){
                            case "wps":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ trk + num + "wps"+ obj[index].pp + "-W" +"' data-val='W' data-pp='"+ obj[index].pp +"'></td>" + // TrackCode + RaceNumber + WagerType + HorsePP
                                        "<td><input type='checkbox' class='wps' data-id='"+ trk + num + "wps"+ obj[index].pp + "-P" +"' data-val='P' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ trk + num + "wps"+ obj[index].pp + "-S" +"' data-val='S' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "superfecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-4" +"' data-val='4' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exacta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ trk + num + "exacta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ trk + num + "exacta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exactabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exactabox' data-id='"+ trk + num + "exactabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ trk + num + "trifecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ trk + num + "trifecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ trk + num + "trifecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifectabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifectabox' data-id='"+ trk + num + "trifectabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "dailydouble":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='dailydouble' data-id='"+ trk + num + "dailydouble"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            default:
                                console.log("Select a Wager");
                                break;
                        }
                    });
                    $(".tdPP").each(function(key,val){
                        if($(this).data("key") == 1){$(this).css({"background":"#FF0000","color":"#fff"});}
                        else if($(this).data("key") == 2){$(this).css({"background":"#fff","color":"#000"});}
                        else if($(this).data("key") == 3){$(this).css({"background":"#0000FF","color":"#fff"});}
                        else if($(this).data("key") == 4){$(this).css({"background":"#FFFF00","color":"#000"});}
                        else if($(this).data("key") == 5){$(this).css({"background":"#008000","color":"#fff"});}
                        else if($(this).data("key") == 6){$(this).css({"background":"#000","color":"#ff0"});}
                        else if($(this).data("key") == 7){$(this).css({"background":"#ff711f","color":"#000"});}
                        else if($(this).data("key") == 8){$(this).css({"background":"#ff52b1","color":"#000"});}
                        else if($(this).data("key") == 9){$(this).css({"background":"#3badad","color":"#fff"});}
                        else if($(this).data("key") == 10){$(this).css({"background":"#9900ff","color":"#fff"});}
                        else if($(this).data("key") == 11){$(this).css({"background":"#b7b7b7","color":"#f00"});}
                        else if($(this).data("key") == 12){$(this).css({"background":"#8A2BE2","color":"#fff"});}
                        else if($(this).data("key") == 13){$(this).css({"background":"#808000","color":"#fff"});}
                        else if($(this).data("key") == 14){$(this).css({"background":"#f0e68c","color":"#fff"});}
                        else if($(this).data("key") == 15){$(this).css({"background":"#2B547E","color":"#fff"});}
                        else if($(this).data("key") == 16){$(this).css({"background":"#000080","color":"#fff"});}
                        else if($(this).data("key") == 17){$(this).css({"background":"#228b22","color":"#fff"});}
                        else if($(this).data("key") == 18){$(this).css({"background":"#4169e1","color":"#fff"});}
                        else if($(this).data("key") == 19){$(this).css({"background":"#FF00FF","color":"#fff"});}
                        else if($(this).data("key") == 20){$(this).css({"background":"#9932CC","color":"#fff"});}
                    });
                    $("#raceDiv").css("display","block");
                    $("#upcomingRacesDiv").css("display","none");
                },
                error : function(){
                    swal("Something went wrong!","Please try again.","error");
                    $("#tempRaces table").remove();
                }
            });
//            $("#selectedTrkAndRace").attr("data-trk",trk).attr("data-raceNum",num);
            $("#selectedTrack").val(trk);
            $("#selectedRaceNum").val(num);
            $("#selectedRacePostTime").val(post);
            $("#submitBet").css("display","block");
            $.ajax({
                'url' : BASE_URL + '/dashboard/getTrackName',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trk : trk
                },
                success : function(response){
                    $("#raceTrackName, #raceNumberAndPostTime").html("");
                    $("#raceTrackName").append(response);
                    $("#raceNumberAndPostTime").append("Race " + num + " POST TIME: " + post);
                },
                error : function(){
                    swal("Something went wrong!","Please try again.","error");
                    $("#tempRaces table").remove();
                }
            });
        });

        //ACCORDION
        var acc = document.getElementsByClassName("accordion");
        var i;
        for (i = 0; i < acc.length; i++) {
            acc[i].onclick = function(){
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            }
        }
        $("#selectWager").on("change",function(){
            var selectedWager = $(this).val();
            var selectedTrack = $("#selectedTrack").val();
            var selectedRaceNum = $("#selectedRaceNum").val();
            var ddselectedRaceNum = parseInt($("#selectedRaceNum").val()) + parseInt(1);
            $.ajax({
                "url" : BASE_URL + '/dashboard/getHorsesPerRace',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    code : selectedTrack,
                    date : CURRENT_DATE,
                    number : selectedRaceNum
                },
                success : function(response){
                    $("#tempRaces table").remove();
                    $("#tempRaces div#ddBoard").html("");
                    switch(selectedWager){
                        case "wps":
                            $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>W</th><th>P</th><th>S</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "superfecta":
                            $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>4</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "exacta":
                            $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th>2</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "exactabox":
                            $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "trifecta":
                            $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th>2</th><th>3</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "trifectabox":
                            $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "dailydouble":
                            $("#tempRaces div#ddBoard").html("");
                            $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            $("#tempRaces").append("<div id='ddBoard'><div> Race "+ ddselectedRaceNum +" </div></div><table class=' table table-bordered table-striped "+ selectedTrack + ddselectedRaceNum + " dailydouble'><thead><tr><th>1</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            ajaxGetHorsesPerRace(BASE_URL,selectedTrack,CURRENT_DATE, ddselectedRaceNum);
                            break;
                        default:
                            break;
                    }
                    var od = JSON.stringify(response);
                    var obj = JSON.parse(od);
                    $.each(obj, function(index, value){
                        switch(selectedWager){
                            case "wps":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ selectedTrack + selectedRaceNum + "wps"+ obj[index].pp + "-W" +"' data-val='W' data-pp='"+ obj[index].pp +"'></td>" + // TrackCode + RaceNumber + WagerType + HorsePP
                                        "<td><input type='checkbox' class='wps' data-id='"+ selectedTrack + selectedRaceNum + "wps"+ obj[index].pp + "-P" +"' data-val='P' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ selectedTrack + selectedRaceNum + "wps"+ obj[index].pp + "-S" +"' data-val='S' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "superfecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-4" +"' data-val='4' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exacta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ selectedTrack + selectedRaceNum + "exacta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ selectedTrack + selectedRaceNum + "exacta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exactabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exactabox' data-id='"+ selectedTrack + selectedRaceNum + "exactabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ selectedTrack + selectedRaceNum + "trifecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ selectedTrack + selectedRaceNum + "trifecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ selectedTrack + selectedRaceNum + "trifecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifectabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifectabox' data-id='"+ selectedTrack + selectedRaceNum + "trifectabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "dailydouble":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='dailydouble' data-id='"+ selectedTrack + selectedRaceNum + "dailydouble"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            default:
                                console.log("Select a Wager");
                                break;
                        }
                    });
                    $(".tdPP").each(function(key,val){
                        if($(this).data("key") == 1){$(this).css({"background":"#FF0000","color":"#fff"});}
                        else if($(this).data("key") == 2){$(this).css({"background":"#fff","color":"#000"});}
                        else if($(this).data("key") == 3){$(this).css({"background":"#0000FF","color":"#fff"});}
                        else if($(this).data("key") == 4){$(this).css({"background":"#FFFF00","color":"#000"});}
                        else if($(this).data("key") == 5){$(this).css({"background":"#008000","color":"#fff"});}
                        else if($(this).data("key") == 6){$(this).css({"background":"#000","color":"#ff0"});}
                        else if($(this).data("key") == 7){$(this).css({"background":"#ff711f","color":"#000"});}
                        else if($(this).data("key") == 8){$(this).css({"background":"#ff52b1","color":"#000"});}
                        else if($(this).data("key") == 9){$(this).css({"background":"#3badad","color":"#fff"});}
                        else if($(this).data("key") == 10){$(this).css({"background":"#9900ff","color":"#fff"});}
                        else if($(this).data("key") == 11){$(this).css({"background":"#b7b7b7","color":"#f00"});}
                        else if($(this).data("key") == 12){$(this).css({"background":"#8A2BE2","color":"#fff"});}
                        else if($(this).data("key") == 13){$(this).css({"background":"#808000","color":"#fff"});}
                        else if($(this).data("key") == 14){$(this).css({"background":"#f0e68c","color":"#fff"});}
                        else if($(this).data("key") == 15){$(this).css({"background":"#2B547E","color":"#fff"});}
                        else if($(this).data("key") == 16){$(this).css({"background":"#000080","color":"#fff"});}
                        else if($(this).data("key") == 17){$(this).css({"background":"#228b22","color":"#fff"});}
                        else if($(this).data("key") == 18){$(this).css({"background":"#4169e1","color":"#fff"});}
                        else if($(this).data("key") == 19){$(this).css({"background":"#FF00FF","color":"#fff"});}
                        else if($(this).data("key") == 20){$(this).css({"background":"#9932CC","color":"#fff"});}
                    });
                },
                error : function(){
                    alert("error");
                }
            });
        });
        $("#submitBetButton").on("click",function(){
            var betType = $("#selectWager").val();
            var trk = $("#selectedTrack").val();
            var raceNumber = $("#selectedRaceNum").val();
            var racePostTime = $("#selectedRacePostTime").val();
            var amount = $("#betAmount").val();
            var betArray = [];
            var ppArray = [];
            $("table#ticketTbl tbody tr").remove();
            if($("#betAmount").val() != ""){
//                $("#raceDiv").css("display","none");
//                $("#betTicket").css("display","block");
                $("input[type=checkbox]").each(function(){
                    if(this.checked){
                        betArray.push($(this).data("id"));
                        ppArray.push({
                            'pp' : $(this).data("pp"),
                            'val' : $(this).data("val")
                        });
                    }else{

                    }
                });
                var betString = "";
                $.each(ppArray, function(index, value){
                    betString += ppArray[index]["pp"] + ", ";
                });
                if(betType === "wps"){
                    console.log(ppArray);
                    if(ppArray.length < 1){
                        swal("There was a problem!","An Win/Place/Show requires atleast one selection.","error");
                    }else{
                        var w = [];
                        var p = [];
                        var s = [];
                        var wString = "(";
                        var pString = "(";
                        var sString = "(";
                        var wpsTotal = "";
                        $.each(ppArray, function(index, value){
                            if(ppArray[index]["val"] == "W"){
                                w.push(ppArray[index]["pp"]);
                                wString += ppArray[index]["pp"] + ",";
                            }else if(ppArray[index]["val"] == "P"){
                                p.push(ppArray[index]["pp"]);
                                pString += ppArray[index]["pp"] + ",";
                            }else if(ppArray[index]["val"] == "S"){
                                s.push(ppArray[index]["pp"]);
                                sString += ppArray[index]["pp"] + ",";
                            }
                        });
                        wpsTotal = (w.length + p.length + s.length) * amount;
                        if(w.length != 0){
                            $("table#ticketTbl tbody").append("<tr><td>" + "Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " WIN:" + wString.substring(0,wString.length - 1) +")</td>" +
                                "<td>"+ amount * w.length +"</td></tr>");
                        }
                        if(p.length != 0){
                            $("table#ticketTbl tbody").append("<tr><td>" + "Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " PLACE:" + pString.substring(0,pString.length - 1) +")</td>" +
                                "<td>"+ amount * p.length +"</td></tr>");
                        }
                        if(s.length != 0){
                            $("table#ticketTbl tbody").append("<tr><td>" + "Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " SHOW:" + sString.substring(0,sString.length - 1) +")</td>" +
                                "<td>"+ amount * s.length +"</td></tr>");
                        }
                        $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + wpsTotal + "</td></tr>");
                        displayConfirmationDiv();
                    }
                }
                else if(betType === "exacta"){
                    console.log(ppArray);
                    if(ppArray.length <= 1){
                        swal("There was a problem!","An Exacta requires two selection.","error");
                    }else{
                        var first = [];
                        var second = [];
                        $.each(ppArray,function(index, value){
                            if(ppArray[index]["val"] == 1){
                                first.push(ppArray[index]["pp"]);
                            }else if(ppArray[index]["val"] == 2){
                                second.push(ppArray[index]["pp"]);
                            }
                        });
                        if(second.length <= 0 || first.length <= 0){
                            swal("There was a problem!","There must be atleast one selection to finish first and atleast one selection to finish second!","error");
                        }else{
                            if(first.length == 1 && second.length == 1){
                                if(first[0] == second[0]){
                                    swal("Invalid selections"," Please check that your selections include at least one different runner per leg","error");
                                }else{
                                    var exactaBets = [];
                                    var totalBetAmount = "";
                                    $.each(first,function(index, value){
                                        $.each(second, function(key, val){
                                            if(value == val){

                                            }else{
                                                exactaBets.push(value + "/" +val);
                                            }
                                        });
                                    });
                                    console.log(exactaBets);
                                    $.each(exactaBets,function(i,v){
                                        $("table#ticketTbl tbody").append("<tr><td> Race: "+ raceNumber +" BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + v +")</td><td>"+ amount +"</td></tr>");
                                    });
                                    totalBetAmount = exactaBets.length * amount;
                                    $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + totalBetAmount + "</td></tr>");
                                    displayConfirmationDiv();
                                }
                            }else{
                                var exactaBets = [];
                                var totalBetAmount = "";
                                $.each(first,function(index, value){
                                    $.each(second, function(key, val){
                                        if(value == val){

                                        }else{
                                            exactaBets.push(value + "/" +val);
                                        }
                                    });
                                });
                                console.log(exactaBets);
                                $.each(exactaBets,function(i,v){
                                    $("table#ticketTbl tbody").append("<tr><td> Race: "+ raceNumber +" BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + v +")</td><td>"+ amount +"</td></tr>");
                                });
                                totalBetAmount = exactaBets.length * amount;
                                $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + totalBetAmount + "</td></tr>");
                                displayConfirmationDiv();
                            }
                        }
                    }
                }
                else if(betType === "superfecta"){
                    if(ppArray.length <= 1){
                        swal("There was a problem!","Ticket has no selection.","error");
                    }else{
                        var firstArr = [];
                        var secondArr = [];
                        var thirdArr = [];
                        var fourthArr = [];
                        var superfectaArray = [];
                        $.each(ppArray, function(index, value){
                            if(ppArray[index]["val"] == 1){
                                firstArr.push(ppArray[index]["pp"]);
                            }else if(ppArray[index]["val"] == 2){
                                secondArr.push(ppArray[index]["pp"]);
                            }else if(ppArray[index]["val"] == 3){
                                thirdArr.push(ppArray[index]["pp"]);
                            }else if(ppArray[index]["val"] == 4){
                                fourthArr.push(ppArray[index]["pp"]);
                            }
                        });
                        // Cool loop
                        $.each(firstArr, function(firstKey, firstVal){
                            $.each(secondArr, function(secondKey,secondVal){
                                if(firstVal === secondVal){

                                }else{
                                    $.each(thirdArr, function(thirdKey, thirdVal){
                                        if(secondVal === thirdVal){

                                        }else{
                                            if(thirdVal === firstVal){

                                            }else{
                                                $.each(fourthArr, function(fourthKey, fourthVal){
                                                    if(thirdVal === fourthVal){

                                                    }else{
                                                        if(fourthVal === secondVal || fourthVal === firstVal){

                                                        }else{
                                                            superfectaArray.push(firstVal + "," + secondVal + "," + thirdVal + "," + fourthVal);
                                                            console.log(firstVal + "," + secondVal + "," + thirdVal + "," + fourthVal);
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            });
                        });
                        // Error trap for empty || invalid selection
                        if(superfectaArray.length <= 0){
                            swal("There was a problem!","Invalid Combination","error");
                        }else{
                            var superfectaTotalAmount = superfectaArray.length * amount;
                            $.each(superfectaArray, function(index, value){
                                $("table#ticketTbl tbody").append("<tr><td> Race: "+ raceNumber +" BetType: " + betType + " Track: " + trk + " Amount: " + amount + " ("+ superfectaArray[index] + ")</td><td>"+ amount +"</td></tr>");
                            });
                            $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>"+ superfectaTotalAmount +"</td></tr>");
                            displayConfirmationDiv();

                        }
                    }
                }
                else if(betType === "trifecta"){
                    var firstArr = [];
                    var secondArr = [];
                    var thirdArr = [];
                    var trifectaArray = [];
                    $.each(ppArray, function(index, value){
                        if(ppArray[index]["val"] == 1){
                            firstArr.push(ppArray[index]["pp"]);
                        }else if(ppArray[index]["val"] == 2){
                            secondArr.push(ppArray[index]["pp"]);
                        }else if(ppArray[index]["val"] == 3){
                            thirdArr.push(ppArray[index]["pp"]);
                        }
                    });
                    $.each(firstArr, function(firstKey, firstVal){
                        $.each(secondArr, function(secondKey,secondVal){
                            if(firstVal === secondVal){

                            }else{
                                $.each(thirdArr, function(thirdKey, thirdVal){
                                    if(secondVal === thirdVal){

                                    }else{
                                        if(thirdVal === firstVal){

                                        }else{
                                            trifectaArray.push(firstVal + "," + secondVal + "," + thirdVal);
                                            console.log(firstVal + "," + secondVal + "," + thirdVal);
                                        }
                                    }
                                });
                            }
                        });
                    });
                    if(trifectaArray.length <= 0){
                        swal("There was a problem!","Please select three horses for first, second and third place.","error");
                    }else{
                        $.each(trifectaArray, function(index, value){
                            $("table#ticketTbl tbody").append("<tr><td> Race: "+ raceNumber +" BetType: " + betType + " Track: " + trk + " Amount: " + amount + " ("+ trifectaArray[index] + ")</td><td>"+ amount +"</td></tr>");
                        });
                        var trifectaTotalAmount = trifectaArray.length * amount;
                        $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>"+ trifectaTotalAmount +"</td></tr>");
                        displayConfirmationDiv();
                    }
                }
                else if(betType === "dailydouble"){
                    var firstTblArray = [];
                    var ddArray = [];
                    $("table:not(.dailydouble) input[type=checkbox]").each(function(){
                        if(this.checked){
                            firstTblArray.push({
                                'pp' : $(this).data("pp"),
                                'val' : $(this).data("val")
                            });
                        }
                    });
                    $("table.dailydouble input[type=checkbox]").each(function(){
                        if(this.checked){
                            ddArray.push({
                                'pp' : $(this).data("pp"),
                                'val' : $(this).data("val")
                            });
                        }
                    });
                    if(ppArray.length <= 0 || ddArray.length <= 0){
                        swal("There was a problem!","Please select your first horse and second horse","error");
                    }else{
                        var firstDD = [];
                        var secondDD = [];
                        var ddBets = [];
                        $.each(firstTblArray, function(index, value){
                            if(firstTblArray[index]["val"] == 1){
                                firstDD.push(firstTblArray[index]["pp"]);
                            }
                        });
                        $.each(ddArray, function(index, value){
                            if(ddArray[index]["val"] == 1){
                                secondDD.push(ddArray[index]["pp"]);
                            }
                        });
                        $.each(firstDD, function(firstDDKey, firstDDVal){
                            $.each(secondDD, function(secondDDKey, secondDDVal){
                                ddBets.push(firstDDVal + "," + secondDDVal);
                            });
                        });
                        var ddTotalBets = ddBets.length * amount;
                        $.each(ddBets,function(index, value){
                            $("table#ticketTbl tbody").append("<tr><td> Race: "+ raceNumber +" BetType: " + betType + " Track: " + trk + " Amount: " + amount + " ("+ ddBets[index] + ")</td><td>"+ amount +"</td></tr>");
                        });
                        $("table#ticketTbl tbody").append("<tr><td>Total Wagers</td><td>"+ ddTotalBets +"</td></tr>");
                        displayConfirmationDiv();
                    }
                }
                else if(betType === "exactabox"){
                    var extBoxArr = [];
                    var extBoxArr2 = [];
                    var betArr = [];
                    $.each(ppArray, function(index, value){
                        extBoxArr.push(ppArray[index]["pp"]);
                        extBoxArr2.push(ppArray[index]["pp"]);
                    });
                    if(extBoxArr.length <= 1){
                        swal("There was a problem!","Exacta Box requires atleast two selection","error");
                    }else{
                        $.each(extBoxArr, function(index, value){
                            $.each(extBoxArr2, function(key, val){
                                if(value === val){

                                }else{
                                    betArr.push(value +"/" + val);
                                }
                            });
                        });
                        var extBoxTotalBet = betArr.length * amount;
                        $.each(betArr,function(index, value){
                            $("table#ticketTbl tbody").append("<tr><td> Race: "+ raceNumber +" BetType: " + betType + " Track: " + trk + " Amount: " + amount + " ("+ betArr[index] + ")</td><td>"+ amount +"</td></tr>");
                        });
                        $("table#ticketTbl tbody").append("<tr><td>Total Wagers</td><td>"+ extBoxTotalBet +"</td></tr>");
                        displayConfirmationDiv();
                    }
                }
                else if(betType === "trifectabox"){
                    var fArr = [];
                    var sArr = [];
                    var tArr = [];
                    var triBoxArr = [];
                    $.each(ppArray, function(index, value){
                        if(ppArray[index]["val"] == 1){
                            fArr.push(ppArray[index]["pp"]);
                            sArr.push(ppArray[index]["pp"]);
                            tArr.push(ppArray[index]["pp"]);
                        }
                    });
                    if(fArr.length <= 2){
                        swal("There was an error!","Trifecta Box requires atleast three selections.","error");
                    }else{
                        $.each(fArr, function(fKey, fVal){
                            $.each(sArr, function(sKey,sVal){
                                if(fVal === sVal){

                                }else{
                                    $.each(tArr, function(tKey, tVal){
                                        if(sVal === tVal){

                                        }else{
                                            if(tVal === fVal){

                                            }else{
                                                triBoxArr.push(fVal + "," + sVal + "," + tVal);
                                                console.log(fVal + "," + sVal + "," + tVal);
                                            }
                                        }
                                    });
                                }
                            });
                        });
                        var triBoxTotal = "";
                        triBoxTotal = triBoxArr.length * amount;
                        $.each(triBoxArr,function(index, value){
                            $("table#ticketTbl tbody").append("<tr><td> Race: "+ raceNumber +" BetType: " + betType + " Track: " + trk + " Amount: " + amount + " ("+ triBoxArr[index] + ")</td><td>"+ amount +"</td></tr>");
                        });
                        $("table#ticketTbl tbody").append("<tr><td>Total Wagers</td><td>"+ triBoxTotal +"</td></tr>");
                        displayConfirmationDiv();
                    }
                }
                else{
                    $("table#ticketTbl tbody").append("<tr><td>"+ "Race " + raceNumber + " " + betType + " (" +
                        betString.substring(0,betString.length - 2) +")</td><td>"+ amount + "</td></tr><tr><td>Total Wager:</td><td>"+ amount +"</td></tr>");
                    displayConfirmationDiv();
                }
            }else{
                swal("Empty Wager","Please enter a wager amount.","error");
            }
        });
        $("#confirmBet").on("click",function(){
            var betType = $("#selectWager").val();
            var trk = $("#selectedTrack").val();
            var raceNumber = $("#selectedRaceNum").val();
            var racePostTime = $("#selectedRacePostTime").val();
            var amount = $("#betAmount").val();
            var betArray = [];
            var ppArray = [];
            $("input[type=checkbox]").each(function(){
                if(this.checked){
                    betArray.push($(this).data("id"));
                    ppArray.push({
                        'pp' : $(this).data("pp"),
                        'val' : $(this).data("val")
                    });
                }else{

                }
            });
            $.ajax({
                    "url" : BASE_URL + '/dashboard/saveBet',
                    type : "POST",
                    data : {
                        _token : $('[name="_token"]').val(),
                        bettype : betType,
                        track : trk,
                        raceNum : raceNumber,
                        racePost : racePostTime,
                        betamount : amount,
                        bet : betArray,
                        pp : ppArray
                    },
                    success : function(response){
                        alert("Success");
                        location.reload();
                        console.log(ppArray);
                    },
                    error : function(){
                        alert("error123");
                    }
                });
        });
        function toggleIcon(e) {
            $(e.target)
                .prev('.panel-heading')
                .find(".more-less")
                .toggleClass('glyphicon-plus glyphicon-minus');
        }
        $('.panel-group').on('hidden.bs.collapse', toggleIcon);
        $('.panel-group').on('shown.bs.collapse', toggleIcon);

        setInterval(getServerTime, 1000);
        setInterval(getUpcomingRaces,20000);
        function getServerTime(){
            $.ajax({
                "url" : BASE_URL + "/dashboard/getServerTime",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val()
                },
                success : function(response){
                    $("h3#date").html("");
                    $("h3#date").append("TRACKS RACING TODAY - " + response["dateTimePDT"]);
                    $("h5#pdt, h5#mdt, h5#cdt, h5#edt").html("");
                    $("h5#pdt").append(response["pdt"]);
                    $("h5#mdt").append(response["mdt"]);
                    $("h5#cdt").append(response["cdt"]);
                    $("h5#edt").append(response["edt"]);
                },
                error : function(xhr,status,err){
                    console.log(err);
                }
            });
        }
        function getUpcomingRaces(){
            $.ajax({
                "url" : BASE_URL + "/dashboard/getUpcomingRaces",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    date : CURRENT_DATE,
                    pdt : $("h5#pdt").text(),
                    mdt : $("h5#mdt").text(),
                    cdt : $("h5#cdt").text(),
                    edt : $("h5#edt").text()
                },
                success : function(response){
                    var trackName,raceNumber,mtp = "";
                    $("table#tblUpcomingRace tbody tr").remove();
                    $.each(response, function(index, value){
                        trackName = response[index].substring(response[index].lastIndexOf("|")+1,response[index].lastIndexOf("@"));
                        raceNumber = response[index].substr(response[index].indexOf("&") + 1);
                        mtp = response[index].substring(response[index].lastIndexOf("@")+1,response[index].lastIndexOf("&"));
                        $("table#tblUpcomingRace tbody").append("<tr><td>"+ trackName +"</td><td>"+ raceNumber +"</td><td>"+ mtp +"</td></tr>");
                    });
                    console.log(response);
                },
                error : function(xhr, status, err){

                }
            });
        }
    });
    function displayConfirmationDiv(){
        $("#raceDiv").css("display","none");
        $("#betTicket").css("display","block");
    }
    function ajaxGetHorsesPerRace($url,trk,currentDate, num){
        $.ajax({
            "url" : $url + "/dashboard/getHorsesPerRace",
            type : "POST",
            data : {
                _token : $('[name="_token"]').val(),
                code : trk,
                date : currentDate,
                number : num
            },
            success : function(response){
                var od = JSON.stringify(response);
                var obj = JSON.parse(od);
                $.each(obj, function(index, value){
                    if(obj[index].pp === "SCRATCHED"){
                        $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ " " +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                    }else if(obj[index].pp === "Foo"){

                    }else{
                        $("table."+ trk + num +" tbody").append("<tr>" +
                            "<td><input type='checkbox' class='dailydouble' data-id='"+ trk + num + "dailydouble"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                            "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                    }
                });
                $(".tdPP").each(function(key,val){
                    if($(this).data("key") == 1){$(this).css({"background":"#FF0000","color":"#fff"});}
                    else if($(this).data("key") == 2){$(this).css({"background":"#fff","color":"#000"});}
                    else if($(this).data("key") == 3){$(this).css({"background":"#0000FF","color":"#fff"});}
                    else if($(this).data("key") == 4){$(this).css({"background":"#FFFF00","color":"#000"});}
                    else if($(this).data("key") == 5){$(this).css({"background":"#008000","color":"#fff"});}
                    else if($(this).data("key") == 6){$(this).css({"background":"#000","color":"#ff0"});}
                    else if($(this).data("key") == 7){$(this).css({"background":"#ff711f","color":"#000"});}
                    else if($(this).data("key") == 8){$(this).css({"background":"#ff52b1","color":"#000"});}
                    else if($(this).data("key") == 9){$(this).css({"background":"#3badad","color":"#fff"});}
                    else if($(this).data("key") == 10){$(this).css({"background":"#9900ff","color":"#fff"});}
                    else if($(this).data("key") == 11){$(this).css({"background":"#b7b7b7","color":"#f00"});}
                    else if($(this).data("key") == 12){$(this).css({"background":"#8A2BE2","color":"#fff"});}
                    else if($(this).data("key") == 13){$(this).css({"background":"#808000","color":"#fff"});}
                    else if($(this).data("key") == 14){$(this).css({"background":"#f0e68c","color":"#fff"});}
                    else if($(this).data("key") == 15){$(this).css({"background":"#2B547E","color":"#fff"});}
                    else if($(this).data("key") == 16){$(this).css({"background":"#000080","color":"#fff"});}
                    else if($(this).data("key") == 17){$(this).css({"background":"#228b22","color":"#fff"});}
                    else if($(this).data("key") == 18){$(this).css({"background":"#4169e1","color":"#fff"});}
                    else if($(this).data("key") == 19){$(this).css({"background":"#FF00FF","color":"#fff"});}
                    else if($(this).data("key") == 20){$(this).css({"background":"#9932CC","color":"#fff"});}
                });
            },
            error:function(){
                alert("Error");
            }
        });
    }
</script>
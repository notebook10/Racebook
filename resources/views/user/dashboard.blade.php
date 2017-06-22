<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{{--<script src="{{ asset('js/date.js') }}"></script>--}}
DASHBOARD
<div id="auth">{{ Auth::user()->id }}</div>
<a href="logout">Logout</a>
<?php
date_default_timezone_set('America/Los_Angeles');
use App\Horses;
?>

<style>
    button.accordion {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        transition: 0.4s;
    }

    button.accordion.active, button.accordion:hover {
        background-color: #ddd;
    }

    div.panel {
        padding: 0 18px;
        display: none;
        background-color: white;
    }
    .raceNum:hover{cursor: pointer;}
    .raceNum{padding:8px;}
</style>

<input type="hidden" id="hiddenURL" value="{{ URL::to('/') }}">
<input type="hidden" name="_token" value="{{csrf_token()}}">
<div class="container">
    <div class="row">
        <div class="col-md-4" style="background: darkgrey;padding: 20px;">
            <h3 id="date" data-date="<?php echo date('mdy',time()) ?>">TRACKS RACING TODAY - <?php echo date('F d, Y h:i:s', time()); ?></h3>
            {{-- ---------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            @foreach($tracks as $value)
                <button class="accordion trkName" data-code="{!! $value->code !!}">{!! $value->name !!}</button>
                <div class="panel {!! $value->code !!}">
                    {{--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>--}}
                </div>
            @endforeach
            {{-- ---------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
            {{--@foreach($tracks as $value)--}}
            {{--<div class="trkName" data-code="{!! $value->code !!}">{!! $value->name !!}</div>--}}
            {{--@endforeach--}}
        </div>
        <div class="col-md-8">
            <div id="board">
                <div id="raceTrackName"></div>
                <div id="raceNumberAndPostTime"></div>
            </div>
            <div>
                <select id="selectWager">
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
                <input type="text" id="betAmount" placeholder="Put your bet">
                <button id="submitBetButton">SUBMIT BET</button>
            </div>
            <div id="betTicket">
                <!-- Confirm Bet -->
                <div id="ticket"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $("document").ready(function(){
        var BASE_URL = $("#hiddenURL").val();
        var CURRENT_DATE = $("#date").data("date");
        $(".trkName").on("click",function(){
            if($(this).hasClass("active")){
//                $(".trkName.on div.raceNum").html("");
//                $(".panel.on div.raceNum").html("");
//                $(this).removeClass("on");
            }else{
//                $(".trkName > div.panel").css("color","red");
                $(this).next("div.panel").find("div.raceNum").remove();
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
                            $(".panel."+ code).append("<div class='raceNum' data-number='" + i + "' data-track='"+ code+"' data-post='"+ raceTimeArr[i-1] +"'>RACE "+ i +" : "+ raceTimeArr[i-1] +" </div>").addClass("on");
                        }
                    },
                    error : function(){
                        alert("error");
                    }
                });
            }
        });
        $("body").delegate(".raceNum","click",function(){
            $("#tempRaces table").remove();
            var wager = $("#selectWager").val();
            var trk = $(this).data("track");
            var num = $(this).data("number");
            var post = $(this).data("post");
//            $("#tempRaces").append("<ul class='"+ trk + num +"'></ul>");
            switch(wager){
                case "wps":
                    $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>W</th><th>P</th><th>S</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "superfecta":
                    $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>4</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "exacta":
                    $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "exactabox":
                    $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>BOX</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "trifecta":
                    $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "trifectabox":
                    $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>BOX</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                    break;
                case "dailydouble":
                    $("#tempRaces").append("<table class=' table "+ trk + num +"'><thead><tr><th>1</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
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
                    $.each(obj, function(index, value){
                        switch(wager){
                            case "wps":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ trk + num + "wps"+ obj[index].pp + "-W" +"' data-val='W' data-pp='"+ obj[index].pp +"'></td>" + // TrackCode + RaceNumber + WagerType + HorsePP
                                        "<td><input type='checkbox' class='wps' data-id='"+ trk + num + "wps"+ obj[index].pp + "-P" +"' data-val='P' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ trk + num + "wps"+ obj[index].pp + "-S" +"' data-val='S' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "superfecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ trk + num + "superfecta"+ obj[index].pp + "-4" +"' data-val='4' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exacta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ trk + num + "exacta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ trk + num + "exacta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exactabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exactabox' data-id='"+ trk + num + "exactabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ trk + num + "trifecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ trk + num + "trifecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ trk + num + "trifecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifectabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifectabox' data-id='"+ trk + num + "trifectabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "dailydouble":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ trk + num +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='dailydouble' data-id='"+ trk + num + "dailydouble"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            default:
                                console.log("Select a Wager");
                                break;
                        }
                    });
                    $(".tdPP").each(function(key,val){
                        switch(key){
                            case 0:$(this).css({"background":"#FF0000","color":"#fff"});break;
                            case 1:$(this).css({"background":"#fff","color":"#000"});break;
                            case 2:$(this).css({"background":"#0000FF","color":"#fff"});break;
                            case 3:$(this).css({"background":"#FFFF00","color":"#000"});break;
                            case 4:$(this).css({"background":"#008000","color":"#fff"});break;
                            case 5:$(this).css({"background":"#000","color":"#fff"});break;
                            case 6:$(this).css({"background":"#FFA500","color":"#fff"});break;
                            case 7:$(this).css({"background":"#FFC0CB","color":"#000"});break;
                            case 8:$(this).css({"background":"#40E0D0","color":"#000"});break;
                            case 9:$(this).css({"background":"#800080","color":"#000"});break;
                            case 10:$(this).css({"background":"#C0C0C0","color":"#fff"});break;
                            case 11:$(this).css({"background":"#32CD32","color":"#fff"});break;
                            case 12:$(this).css({"background":"#8A2BE2","color":"#fff"});break;
                            case 13:$(this).css({"background":"#808000","color":"#fff"});break;
                            case 14:$(this).css({"background":"#f0e68c","color":"#fff"});break;
                            case 15:$(this).css({"background":"#2B547E","color":"#fff"});break;
                            case 16:$(this).css({"background":"#000080","color":"#fff"});break;
                            case 17:$(this).css({"background":"#228b22","color":"#fff"});break;
                            case 18:$(this).css({"background":"#4169e1","color":"#fff"});break;
                            case 19:$(this).css({"background":"#FF00FF","color":"#fff"});break;
                            case 20:$(this).css({"background":"#9932CC","color":"#fff"});break;
                            default: break;
                        }
                    });
                },
                error : function(){
                    alert("Error");
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
                    alert("Error");
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
                    switch(selectedWager){
                        case "wps":
                            $("#tempRaces").append("<table class=' table "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>W</th><th>P</th><th>S</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "superfecta":
                            $("#tempRaces").append("<table class=' table "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>4</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "exacta":
                            $("#tempRaces").append("<table class=' table "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th>2</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "exactabox":
                            $("#tempRaces").append("<table class=' table "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>BOX</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "trifecta":
                            $("#tempRaces").append("<table class=' table "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "trifectabox":
                            $("#tempRaces").append("<table class=' table "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>BOX</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                            break;
                        case "dailydouble":
                            $("#tempRaces").append("<table class=' table "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
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
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ selectedTrack + selectedRaceNum + "wps"+ obj[index].pp + "-W" +"' data-val='W' data-pp='"+ obj[index].pp +"'></td>" + // TrackCode + RaceNumber + WagerType + HorsePP
                                        "<td><input type='checkbox' class='wps' data-id='"+ selectedTrack + selectedRaceNum + "wps"+ obj[index].pp + "-P" +"' data-val='P' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='wps' data-id='"+ selectedTrack + selectedRaceNum + "wps"+ obj[index].pp + "-S" +"' data-val='S' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "superfecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='superfecta' data-id='"+ selectedTrack + selectedRaceNum + "superfecta"+ obj[index].pp + "-4" +"' data-val='4' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exacta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ selectedTrack + selectedRaceNum + "exacta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='exacta' data-id='"+ selectedTrack + selectedRaceNum + "exacta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "exactabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='exactabox' data-id='"+ selectedTrack + selectedRaceNum + "exactabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifecta":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td>*</td><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ selectedTrack + selectedRaceNum + "trifecta"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ selectedTrack + selectedRaceNum + "trifecta"+ obj[index].pp + "-2" +"' data-val='2' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td><input type='checkbox' class='trifecta' data-id='"+ selectedTrack + selectedRaceNum + "trifecta"+ obj[index].pp + "-3" +"' data-val='3' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "trifectabox":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='trifectabox' data-id='"+ selectedTrack + selectedRaceNum + "trifectabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            case "dailydouble":
                                if(obj[index].pp === "SCRATCHED"){
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }else if(obj[index].pp === "Foo"){

                                }else{
                                    $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                        "<td><input type='checkbox' class='dailydouble' data-id='"+ selectedTrack + selectedRaceNum + "dailydouble"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                        "<td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                                }
                                break;
                            default:
                                console.log("Select a Wager");
                                break;
                        }
                    });
                    $(".tdPP").each(function(key,val){
                        switch(key){
                            case 0:$(this).css({"background":"#FF0000","color":"#fff"});break;
                            case 1:$(this).css({"background":"#fff","color":"#000"});break;
                            case 2:$(this).css({"background":"#0000FF","color":"#fff"});break;
                            case 3:$(this).css({"background":"#FFFF00","color":"#000"});break;
                            case 4:$(this).css({"background":"#008000","color":"#fff"});break;
                            case 5:$(this).css({"background":"#000","color":"#fff"});break;
                            case 6:$(this).css({"background":"#FFA500","color":"#fff"});break;
                            case 7:$(this).css({"background":"#FFC0CB","color":"#000"});break;
                            case 8:$(this).css({"background":"#40E0D0","color":"#000"});break;
                            case 9:$(this).css({"background":"#800080","color":"#000"});break;
                            case 10:$(this).css({"background":"#C0C0C0","color":"#fff"});break;
                            case 11:$(this).css({"background":"#32CD32","color":"#fff"});break;
                            case 12:$(this).css({"background":"#8A2BE2","color":"#fff"});break;
                            case 13:$(this).css({"background":"#808000","color":"#fff"});break;
                            case 14:$(this).css({"background":"#f0e68c","color":"#fff"});break;
                            case 15:$(this).css({"background":"#2B547E","color":"#fff"});break;
                            case 16:$(this).css({"background":"#000080","color":"#fff"});break;
                            case 17:$(this).css({"background":"#228b22","color":"#fff"});break;
                            case 18:$(this).css({"background":"#4169e1","color":"#fff"});break;
                            case 19:$(this).css({"background":"#FF00FF","color":"#fff"});break;
                            case 20:$(this).css({"background":"#9932CC","color":"#fff"});break;
                            default: break;
                        }
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
            $("input[type=checkbox]").each(function(){
                if(this.checked){
                    betArray.push($(this).data("id"));
                }else{

                }
            });
            console.log(betArray);
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
                    bet : betArray
                },
                success : function(response){
                    alert(response);
                },
                error : function(){
                    alert("error123");
                }
            });
        });
    });

</script>
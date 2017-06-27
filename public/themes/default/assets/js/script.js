$("document").ready(function(){
    var BASE_URL = $("#hiddenURL").val();
    var CURRENT_DATE = $("#date").data("date");
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
    $("body").delegate(".raceNum","click",function(){
        $("#tempRaces table").remove();
        var wager = $("#selectWager").val();
        var trk = $(this).data("track");
        var num = $(this).data("number");
        var post = $(this).data("post");
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
                $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }
                            break;
                        case "exactabox":
                            if(obj[index].pp === "SCRATCHED"){
                                $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }else if(obj[index].pp === "Foo"){

                            }else{
                                $("table."+ trk + num +" tbody").append("<tr>" +
                                    "<td><input type='checkbox' class='exactabox' data-id='"+ trk + num + "exactabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }
                            break;
                        case "trifectabox":
                            if(obj[index].pp === "SCRATCHED"){
                                $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }else if(obj[index].pp === "Foo"){

                            }else{
                                $("table."+ trk + num +" tbody").append("<tr>" +
                                    "<td><input type='checkbox' class='trifectabox' data-id='"+ trk + num + "trifectabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }
                            break;
                        case "dailydouble":
                            if(obj[index].pp === "SCRATCHED"){
                                $("table."+ trk + num +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                        $("#tempRaces").append("<table class=' table table-bordered table-striped "+ selectedTrack + selectedRaceNum +"'><thead><tr><th>1</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }
                            break;
                        case "exactabox":
                            if(obj[index].pp === "SCRATCHED"){
                                $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }else if(obj[index].pp === "Foo"){

                            }else{
                                $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                    "<td><input type='checkbox' class='exactabox' data-id='"+ selectedTrack + selectedRaceNum + "exactabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }
                            break;
                        case "trifectabox":
                            if(obj[index].pp === "SCRATCHED"){
                                $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }else if(obj[index].pp === "Foo"){

                            }else{
                                $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr>" +
                                    "<td><input type='checkbox' class='trifectabox' data-id='"+ selectedTrack + selectedRaceNum + "trifectabox"+ obj[index].pp + "-1" +"' data-val='1' data-pp='"+ obj[index].pp +"'></td>" +
                                    "<td class='tdPP' data-key='"+ obj[index].pp +"'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
                            }
                            break;
                        case "dailydouble":
                            if(obj[index].pp === "SCRATCHED"){
                                $("table."+ selectedTrack + selectedRaceNum +" tbody").append("<tr><td>*</td><td class='tdPP'>"+ obj[index].pp +"</td><td>"+ obj[index].horse + "</td><td>"+ obj[index].jockey +"</td></tr>");
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
                edt : $("h5#edt").text(),
            },
            success : function(response){
                var trackName,raceNumber,mtp = "";
                $.each(response, function(index, value){
                    trackName = response[index].substring(response[index].lastIndexOf("|")+1,response[index].lastIndexOf("@"));
                    mtp = response[index].substring(response[index].lastIndexOf("@")+1,response[index].lastIndexOf("&"));
                    $("table#tblUpcomingRace tbody").append("<tr><td>"+ trackName +"</td><td></td><td>"+ mtp +"</td></tr>");
                });
            },
            error : function(xhr, status, err){

            }
        });
    }
});
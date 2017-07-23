
$("document").ready(function(){
    var BASE_URL = $("#hiddenURL").val();
    var CURRENT_DATE = $("#date").data("date");
    var submitArray = []; // Array to be submitted to confirmation
    var wArray = [];
    var pArray = [];
    var sArray = [];
    var userId = $("#userId").val();
    var selectedWagerPrev = "";
    var openRaces2 = [];
    setTimeout(getUpcomingRaces,3000);
    $(".trkName").on("click",function(){
        if($(this).hasClass("collapsed")){
            $(this).next("div.panel-body").find("div.raceNum").remove();
            var code = $(this).data("code");
            var raceDate = $(this).data("date");
            // Validate track code if it have a timezone
            $.ajax({
                "url" : BASE_URL + "/dashboard/validateTrackTmz",
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    code : code
                },
                success : function(response){
                    if(response == ""){
                        swal("Unavailable","This Race Track is currently unavailable.","error");
                        // Timezone field unavailable sa tbl timezone
                    }else{
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
                                // Test run here
                                var firstRace = race[0].replace(/\D/g,'');
                                var tempArr = "";
                                $.ajax({
                                    "url" : BASE_URL + '/dashboard/checkIfOpen',
                                    type : "get",
                                    data : {
                                        _token : $('[name="_token"]').val(),
                                        trk : code,
                                        postTime : raceTimeArr,
                                        date : raceDate
                                    },
                                    success : function(respo){
                                        console.log(respo);
                                        for(var i = firstRace; i <= raceTimeArr.length; i++){
                                            if(respo[i-1] == "lt"){
                                                // $(".panel-body."+ code).append("<div class='raceNum closed' data-number='" + i + "' data-track='"+ code+"' data-post='"+ raceTimeArr[i-1] +"'> Race "+ i +": CLOSED</div>");
                                            }else if(respo[i-1] == "gt"){
                                                $(".panel-body."+ code).append("<div class='raceNum' data-number='" + i + "' data-track='"+ code+"' data-post='"+ raceTimeArr[i-1] +"'>RACE "+ i +" : "+ raceTimeArr[i-1] +" </div>").addClass("on");
                                            }
                                        }
                                        if(jQuery.inArray("gt",respo) != -1){

                                        }else{
                                            // No open race
                                            $(".panel-body."+ code).html("");
                                            $(".panel-body."+ code).append("<div class='noRace'>No races available</div>");
                                        }
                                        $(".closed").css("display","none");
                                    },
                                    error : function(xhr, status, error){
                                        alert("Error: " + error);
                                    }
                                });
                                // for(var i = firstRace; i <= raceTimeArr.length; i++){
                                //     // $.ajax({
                                //     //     "url" : BASE_URL + '/dashboard/checkPostTime',
                                //     //     type : "get",
                                //     //     async: false,
                                //     //     data : {
                                //     //         _token : $('[name="_token"]').val(),
                                //     //         trk : code,
                                //     //         postTime : raceTimeArr[i-1]
                                //     //     },
                                //     //     success : function(data){
                                //     //         if(data === "gt"){
                                //     //             $(".panel-body."+ code).append("<div class='raceNum' data-number='" + i + "' data-track='"+ code+"' data-post='"+ raceTimeArr[i-1] +"'>RACE "+ i +" : "+ raceTimeArr[i-1] +" </div>").addClass("on");
                                //     //         }else{
                                //     //             $(".panel-body."+ code).append("<div class='raceNum'>RACE "+ i +" : " + " CLOSED</div>");
                                //     //         }
                                //     //     },
                                //     //     error : function(xhr, status, error){
                                //     //         alert("Error: " + error);
                                //     //     }
                                //     // });
                                //     $(".panel-body."+ code).append("<div class='raceNum' data-number='" + i + "' data-track='"+ code+"' data-post='"+ raceTimeArr[i-1] +"'>RACE "+ i +" : "+ raceTimeArr[i-1] +" </div>").addClass("on");
                                // }
                            },
                            error : function(){
                                alert("error");
                            }
                        });
                    }
                },
                error : function(xhr, status, err){
                    alert("Error: " + err);
                }
            });

        }else{
            $(".panel-body div.raceNum").remove();
        }
        $(".panel-body div.raceNum").remove();
    });
    $(".raceNum").unbind("dblclick");
    $("body").delegate(".raceNum","click",function(event){
        if($(this).hasClass("closed")){
            swal("CLOSED!","Race is closed!","error");
            event.preventDefault();
            return false;
        }
        $("#tempRaces table").remove();
        $("#betTicket").css("display","none");
        $("#betAmount").val("");
        selectedWagerPrev = $("#selectWager option:selected").text();
        var trk = $(this).data("track");
        var num = $(this).data("number");
        var post = $(this).data("post");
        var ddselectedRaceNum = parseInt(num) + parseInt(1);
        // GET WAGER
        // getWagerForRace(BASE_URL, trk, num, CURRENT_DATE);
        // var wager = $("#selectWager").val();
        var wager = "";
        $.ajax({
            "url" : BASE_URL + "/dashboard/getWagerForRace",
            type : "POST",
            data : {
                _token : $('[name="_token"]').val(),
                trk : trk,
                num : num,
                date : CURRENT_DATE
            },
            success : function(response){
                $("#selectWager").empty();
                $.each(response, function(index, value){
                    switch(value){
                        case "WPS":
                            $('#selectWager').append($('<option>', {value : 'wps', text : 'Win/Place/Show'}));
                            break;
                        case "Exacta":
                            $('#selectWager').append($('<option>', {value : 'exacta', text : value}));
                            break;
                        case "Exacta Box":
                            $('#selectWager').append($('<option>', {value : 'exactabox', text : value}));
                            break;
                        case "Daily Double":
                            $('#selectWager').append($('<option>', {value : 'dailydouble', text : value}));
                            break;
                        case "Trifecta":
                            $('#selectWager').append($('<option>', {value : 'trifecta', text : value}));
                            break;
                        case "Trifecta Box":
                            $('#selectWager').append($('<option>', {value : 'trifectabox', text : value}));
                            break;
                        case "Superfecta":
                            $('#selectWager').append($('<option>', {value : 'superfecta', text : value}));
                            break;
                    }
                });
                if($.inArray(selectedWagerPrev, response) > -1){
                    switch (selectedWagerPrev){
                        case "Win/Place/Show":
                            wager = "wps";
                            $('#selectWager').val("wps");
                            break;
                        case "Exacta":
                            wager = "exacta";
                            $('#selectWager').val("exacta");
                            break;
                        case "Exacta Box":
                            wager = "exactabox";
                            $('#selectWager').val("exactabox");
                            break;
                        case "Trifecta":
                            wager = "trifecta";
                            $('#selectWager').val("trifecta");
                            break;
                        case "Trifecta Box":
                            wager = "trifectabox";
                            $('#selectWager').val("trifectabox");
                            break;
                        case "Superfecta":
                            wager = "superfecta";
                            $('#selectWager').val("superfecta");
                            break;
                        case "Daily Double":
                            wager = "dailydouble";
                            $('#selectWager').val("dailydouble");
                            break;
                        default:break;
                    }
                }else{
                    wager = $("#selectWager").val();
                }
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
                fooFunction(BASE_URL,trk,CURRENT_DATE,num,wager);
            },
            error : function(xhr,status, error){
                swal("Error","Error: " + error,"error");
            }
        });
        // switch(wager){
        //     case "wps":
        //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>W</th><th>P</th><th>S</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         break;
        //     case "superfecta":
        //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>4</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         break;
        //     case "exacta":
        //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         break;
        //     case "exactabox":
        //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         break;
        //     case "trifecta":
        //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         break;
        //     case "trifectabox":
        //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         break;
        //     case "dailydouble":
        //         $("#tempRaces div#ddBoard").html("");
        //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         $("#tempRaces").append("<div id='ddBoard'><div> Race "+ ddselectedRaceNum +" </div></div><table class=' table table-bordered table-striped "+ trk + ddselectedRaceNum + " dailydouble'><thead><tr><th>1</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
        //         ajaxGetHorsesPerRace(BASE_URL,trk,CURRENT_DATE, ddselectedRaceNum);
        //         break;
        //     default:
        //
        //         break;
        // }
        // fooFunction(BASE_URL,trk,CURRENT_DATE,num,wager);
        fooFunction2(BASE_URL,trk,num,post);
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
        selectedWagerPrev = $("#selectWager option:selected").text();
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
                    else if($(this).data("key") == 6){$(this).css({"background":"#000","color":"#fff"});}
                    else if($(this).data("key") == 7){$(this).css({"background":"#ff711f","color":"#000"});}
                    else if($(this).data("key") == 8){$(this).css({"background":"#ff52b1","color":"#000"});}
                    else if($(this).data("key") == 9){$(this).css({"background":"#3badad","color":"#fff"});}
                    else if($(this).data("key") == 10){$(this).css({"background":"#800080","color":"#fff"});}
                    else if($(this).data("key") == 11){$(this).css({"background":"#b7b7b7","color":"#fff"});}
                    else if($(this).data("key") == 12){$(this).css({"background":"#32CD32","color":"#000"});}
                    else if($(this).data("key") == 13){$(this).css({"background":"#8A2BE2","color":"#fff"});}
                    else if($(this).data("key") == 14){$(this).css({"background":"#808000","color":"#fff"});}
                    else if($(this).data("key") == 15){$(this).css({"background":"#ADA96E","color":"#fff"});}
                    else if($(this).data("key") == 16){$(this).css({"background":"#2B547E","color":"#fff"});}
                    else if($(this).data("key") == 17){$(this).css({"background":"#228b22","color":"#fff"});}
                    else if($(this).data("key") == 18){$(this).css({"background":"#4169e1","color":"#fff"});}
                    else if($(this).data("key") == 19){$(this).css({"background":"#FF00FF","color":"#fff"});}
                    else if($(this).data("key") == 20){$(this).css({"background":"#9932CC","color":"#fff"});}
                });
            },
            error : function(){
                swal("Something went wrong!","Please try again.","error");
            }
        });
    });
    $("#submitBetButton").on("click",function(evt){
        var betType = $("#selectWager").val();
        var trk = $("#selectedTrack").val();
        var raceNumber = $("#selectedRaceNum").val();
        var racePostTime = $("#selectedRacePostTime").val();
        var amount = $("#betAmount").val();
        var betArray = [];
        var ppArray = [];
        var minimumBet = "";
        $("table#ticketTbl tbody tr").remove();
        if($("#betAmount").val() != ""){
            $.ajax({
                "url" : BASE_URL + '/dashboard/getMinimum',
                type : "POST",
                data : {
                    _token : $('[name="_token"]').val(),
                    trk : trk,
                    date : CURRENT_DATE
                },
                success : function(respo){
                    if(respo != 1){
                        switch (betType){
                            case "exacta":
                                minimumBet = respo["exacta"];
                                break;
                            case "exactabox":
                                minimumBet = respo["exacta"];
                                break;
                            case "trifecta":
                                minimumBet = respo["trifecta"];
                                break;
                            case "trifectabox":
                                minimumBet = respo["trifecta"];
                                break;
                            case "superfecta":
                                minimumBet = respo["superfecta"];
                                break;
                            case "dailydouble":
                                minimumBet = respo["dailydouble"];
                                break;
                            case "wps":
                                minimumBet = respo["wps"];
                                break;
                            default:

                                break;
                        }
                    }else{
                        // Default Minimum bet = 2
                        minimumBet = 2;
                    }
                    if(parseInt($("#betAmount").val()) >= parseInt(minimumBet)){
                        $("input[type=checkbox]").each(function () {
                            if (this.checked) {
                                betArray.push($(this).data("id"));
                                ppArray.push({
                                    'pp': $(this).data("pp"),
                                    'val': $(this).data("val")
                                });
                            } else {

                            }
                        });
                        var betString = "";
                        $.each(ppArray, function (index, value) {
                            betString += ppArray[index]["pp"] + ", ";
                        });
                        if (betType === "wps") {
                            console.log(ppArray);
                            if (ppArray.length < 1) {
                                swal("There was a problem!", "An Win/Place/Show requires atleast one selection.", "error");
                            } else {
                                var w = [];
                                var p = [];
                                var s = [];
                                var wString = "(";
                                var pString = "(";
                                var sString = "(";
                                var wpsTotal = "";
                                $.each(ppArray, function (index, value) {
                                    if (ppArray[index]["val"] == "W") {
                                        w.push(ppArray[index]["pp"]);
                                        wString += ppArray[index]["pp"] + ",";
                                    } else if (ppArray[index]["val"] == "P") {
                                        p.push(ppArray[index]["pp"]);
                                        pString += ppArray[index]["pp"] + ",";
                                    } else if (ppArray[index]["val"] == "S") {
                                        s.push(ppArray[index]["pp"]);
                                        sString += ppArray[index]["pp"] + ",";
                                    }
                                });
                                wpsTotal = (w.length + p.length + s.length) * amount;
                                if (w.length != 0) {
                                    $("table#ticketTbl tbody").append("<tr><td>" + "Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " WIN:" + wString.substring(0, wString.length - 1) + ")</td>" +
                                        "<td>" + amount * w.length + "</td></tr>");
                                }
                                if (p.length != 0) {
                                    $("table#ticketTbl tbody").append("<tr><td>" + "Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " PLACE:" + pString.substring(0, pString.length - 1) + ")</td>" +
                                        "<td>" + amount * p.length + "</td></tr>");
                                }
                                if (s.length != 0) {
                                    $("table#ticketTbl tbody").append("<tr><td>" + "Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " SHOW:" + sString.substring(0, sString.length - 1) + ")</td>" +
                                        "<td>" + amount * s.length + "</td></tr>");
                                }
                                $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + Math.round(wpsTotal * 100) / 100 + "</td></tr>");
                                wArray = w;
                                pArray = p;
                                sArray = s;
                                displayConfirmationDiv();
                            }
                        }
                        else if (betType === "exacta") {
                            console.log(ppArray);
                            if (ppArray.length <= 1) {
                                swal("There was a problem!", "An Exacta requires two selection.", "error");
                            } else {
                                var first = [];
                                var second = [];
                                $.each(ppArray, function (index, value) {
                                    if (ppArray[index]["val"] == 1) {
                                        first.push(ppArray[index]["pp"]);
                                    } else if (ppArray[index]["val"] == 2) {
                                        second.push(ppArray[index]["pp"]);
                                    }
                                });
                                if (second.length <= 0 || first.length <= 0) {
                                    swal("There was a problem!", "There must be atleast one selection to finish first and atleast one selection to finish second!", "error");
                                } else {
                                    if (first.length == 1 && second.length == 1) {
                                        if (first[0] == second[0]) {
                                            swal("Invalid selections", " Please check that your selections include at least one different runner per leg", "error");
                                        } else {
                                            var exactaBets = [];
                                            var totalBetAmount = "";
                                            $.each(first, function (index, value) {
                                                $.each(second, function (key, val) {
                                                    if (value == val) {

                                                    } else {
                                                        exactaBets.push(value + "," + val);
                                                    }
                                                });
                                            });
                                            console.log(exactaBets);
                                            $.each(exactaBets, function (i, v) {
                                                $("table#ticketTbl tbody").append("<tr><td> Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + v + ")</td><td>" + amount + "</td></tr>");
                                            });
                                            totalBetAmount = exactaBets.length * amount;
                                            $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + Math.round(totalBetAmount * 100) / 100 + "</td></tr>");
                                            submitArray = exactaBets;
                                            displayConfirmationDiv();
                                        }
                                    } else {
                                        var exactaBets = [];
                                        var totalBetAmount = "";
                                        $.each(first, function (index, value) {
                                            $.each(second, function (key, val) {
                                                if (value == val) {

                                                } else {
                                                    exactaBets.push(value + "," + val);
                                                }
                                            });
                                        });
                                        console.log(exactaBets);
                                        $.each(exactaBets, function (i, v) {
                                            $("table#ticketTbl tbody").append("<tr><td> Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + v + ")</td><td>" + amount + "</td></tr>");
                                        });
                                        totalBetAmount = exactaBets.length * amount;
                                        $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + Math.round(totalBetAmount * 100) / 100 + "</td></tr>");
                                        submitArray = exactaBets;
                                        displayConfirmationDiv();
                                    }
                                }
                            }
                        }
                        else if (betType === "superfecta") {
                            if (ppArray.length <= 1) {
                                swal("There was a problem!", "Ticket has no selection.", "error");
                            } else {
                                var firstArr = [];
                                var secondArr = [];
                                var thirdArr = [];
                                var fourthArr = [];
                                var superfectaArray = [];
                                $.each(ppArray, function (index, value) {
                                    if (ppArray[index]["val"] == 1) {
                                        firstArr.push(ppArray[index]["pp"]);
                                    } else if (ppArray[index]["val"] == 2) {
                                        secondArr.push(ppArray[index]["pp"]);
                                    } else if (ppArray[index]["val"] == 3) {
                                        thirdArr.push(ppArray[index]["pp"]);
                                    } else if (ppArray[index]["val"] == 4) {
                                        fourthArr.push(ppArray[index]["pp"]);
                                    }
                                });
                                // Cool loop
                                $.each(firstArr, function (firstKey, firstVal) {
                                    $.each(secondArr, function (secondKey, secondVal) {
                                        if (firstVal === secondVal) {

                                        } else {
                                            $.each(thirdArr, function (thirdKey, thirdVal) {
                                                if (secondVal === thirdVal) {

                                                } else {
                                                    if (thirdVal === firstVal) {

                                                    } else {
                                                        $.each(fourthArr, function (fourthKey, fourthVal) {
                                                            if (thirdVal === fourthVal) {

                                                            } else {
                                                                if (fourthVal === secondVal || fourthVal === firstVal) {

                                                                } else {
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
                                if (superfectaArray.length <= 0) {
                                    swal("There was a problem!", "Invalid Combination", "error");
                                } else {
                                    var superfectaTotalAmount = superfectaArray.length * amount;
                                    $.each(superfectaArray, function (index, value) {
                                        $("table#ticketTbl tbody").append("<tr><td> Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + superfectaArray[index] + ")</td><td>" + amount + "</td></tr>");
                                    });
                                    $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + Math.round(superfectaTotalAmount * 100) / 100 + "</td></tr>");
                                    submitArray = superfectaArray;
                                    displayConfirmationDiv();

                                }
                            }
                        }
                        else if (betType === "trifecta") {
                            var firstArr = [];
                            var secondArr = [];
                            var thirdArr = [];
                            var trifectaArray = [];
                            $.each(ppArray, function (index, value) {
                                if (ppArray[index]["val"] == 1) {
                                    firstArr.push(ppArray[index]["pp"]);
                                } else if (ppArray[index]["val"] == 2) {
                                    secondArr.push(ppArray[index]["pp"]);
                                } else if (ppArray[index]["val"] == 3) {
                                    thirdArr.push(ppArray[index]["pp"]);
                                }
                            });
                            $.each(firstArr, function (firstKey, firstVal) {
                                $.each(secondArr, function (secondKey, secondVal) {
                                    if (firstVal === secondVal) {

                                    } else {
                                        $.each(thirdArr, function (thirdKey, thirdVal) {
                                            if (secondVal === thirdVal) {

                                            } else {
                                                if (thirdVal === firstVal) {

                                                } else {
                                                    trifectaArray.push(firstVal + "," + secondVal + "," + thirdVal);
                                                    console.log(firstVal + "," + secondVal + "," + thirdVal);
                                                }
                                            }
                                        });
                                    }
                                });
                            });
                            if (trifectaArray.length <= 0) {
                                swal("There was a problem!", "Please select three horses for first, second and third place.", "error");
                            } else {
                                $.each(trifectaArray, function (index, value) {
                                    $("table#ticketTbl tbody").append("<tr><td> Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + trifectaArray[index] + ")</td><td>" + amount + "</td></tr>");
                                });
                                var trifectaTotalAmount = trifectaArray.length * amount;
                                $("table#ticketTbl tbody").append("<tr><td>Total Wager</td><td>" + Math.round(trifectaTotalAmount * 100) / 100 + "</td></tr>");
                                submitArray = trifectaArray;
                                displayConfirmationDiv();
                            }
                        }
                        else if (betType === "dailydouble") {
                            var firstTblArray = [];
                            var ddArray = [];
                            $("table:not(.dailydouble) input[type=checkbox]").each(function () {
                                if (this.checked) {
                                    firstTblArray.push({
                                        'pp': $(this).data("pp"),
                                        'val': $(this).data("val")
                                    });
                                }
                            });
                            $("table.dailydouble input[type=checkbox]").each(function () {
                                if (this.checked) {
                                    ddArray.push({
                                        'pp': $(this).data("pp"),
                                        'val': $(this).data("val")
                                    });
                                }
                            });
                            if (ppArray.length <= 0 || ddArray.length <= 0) {
                                swal("There was a problem!", "Please select your first horse and second horse", "error");
                            } else {
                                var firstDD = [];
                                var secondDD = [];
                                var ddBets = [];
                                $.each(firstTblArray, function (index, value) {
                                    if (firstTblArray[index]["val"] == 1) {
                                        firstDD.push(firstTblArray[index]["pp"]);
                                    }
                                });
                                $.each(ddArray, function (index, value) {
                                    if (ddArray[index]["val"] == 1) {
                                        secondDD.push(ddArray[index]["pp"]);
                                    }
                                });
                                $.each(firstDD, function (firstDDKey, firstDDVal) {
                                    $.each(secondDD, function (secondDDKey, secondDDVal) {
                                        ddBets.push(firstDDVal + "," + secondDDVal);
                                    });
                                });
                                var ddTotalBets = ddBets.length * amount;
                                $.each(ddBets, function (index, value) {
                                    $("table#ticketTbl tbody").append("<tr><td> Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + ddBets[index] + ")</td><td>" + amount + "</td></tr>");
                                });
                                $("table#ticketTbl tbody").append("<tr><td>Total Wagers</td><td>" + Math.round(ddTotalBets * 100) / 100 + "</td></tr>");
                                submitArray = ddBets;
                                displayConfirmationDiv();
                            }
                        }
                        else if (betType === "exactabox") {
                            var extBoxArr = [];
                            var extBoxArr2 = [];
                            var betArr = [];
                            $.each(ppArray, function (index, value) {
                                extBoxArr.push(ppArray[index]["pp"]);
                                extBoxArr2.push(ppArray[index]["pp"]);
                            });
                            if (extBoxArr.length <= 1) {
                                swal("There was a problem!", "Exacta Box requires atleast two selection", "error");
                            } else {
                                $.each(extBoxArr, function (index, value) {
                                    $.each(extBoxArr2, function (key, val) {
                                        if (value === val) {

                                        } else {
                                            betArr.push(value + "," + val);
                                        }
                                    });
                                });
                                var extBoxTotalBet = betArr.length * amount;
                                $.each(betArr, function (index, value) {
                                    $("table#ticketTbl tbody").append("<tr><td> Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + betArr[index] + ")</td><td>" + amount + "</td></tr>");
                                });
                                $("table#ticketTbl tbody").append("<tr><td>Total Wagers</td><td>" + Math.round(extBoxTotalBet * 100) / 100 + "</td></tr>");
                                submitArray = betArr;
                                displayConfirmationDiv();
                            }
                        }
                        else if (betType === "trifectabox") {
                            var fArr = [];
                            var sArr = [];
                            var tArr = [];
                            var triBoxArr = [];
                            $.each(ppArray, function (index, value) {
                                if (ppArray[index]["val"] == 1) {
                                    fArr.push(ppArray[index]["pp"]);
                                    sArr.push(ppArray[index]["pp"]);
                                    tArr.push(ppArray[index]["pp"]);
                                }
                            });
                            if (fArr.length <= 2) {
                                swal("There was an error!", "Trifecta Box requires atleast three selections.", "error");
                            } else {
                                $.each(fArr, function (fKey, fVal) {
                                    $.each(sArr, function (sKey, sVal) {
                                        if (fVal === sVal) {

                                        } else {
                                            $.each(tArr, function (tKey, tVal) {
                                                if (sVal === tVal) {

                                                } else {
                                                    if (tVal === fVal) {

                                                    } else {
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
                                $.each(triBoxArr, function (index, value) {
                                    $("table#ticketTbl tbody").append("<tr><td> Race: " + raceNumber + " BetType: " + betType + " Track: " + trk + " Amount: " + amount + " (" + triBoxArr[index] + ")</td><td>" + amount + "</td></tr>");
                                });
                                $("table#ticketTbl tbody").append("<tr><td>Total Wagers</td><td>" + Math.round(triBoxTotal * 100) / 100 + "</td></tr>");
                                submitArray = triBoxArr;
                                displayConfirmationDiv();
                            }
                        }
                        else {
                            $("table#ticketTbl tbody").append("<tr><td>" + "Race " + raceNumber + " " + betType + " (" +
                                betString.substring(0, betString.length - 2) + ")</td><td>" + amount + "</td></tr><tr><td>Total Wager:</td><td>" + amount + "</td></tr>");
                            displayConfirmationDiv();
                        }
                    }else{
                        swal("There was a problem!", "The minimum stake for this wager is $" + minimumBet,"error");
                        $("#betAmount").val("").focus();
                    }
                },
                error : function(xhr, status, error){
                    swal("Something went wrong!","Please try again","error");
                }
            });

        }else{
            swal("Empty Wager","Please enter a wager amount.","error");
        }
    });
    $("#confirmBet").unbind("dblclick");
    $("#confirmBet").on("click",function(){
        $("#confirmBet").attr("disabled",true);
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
            "url" : BASE_URL + '/dashboard/checkPostTime',
            type : "get",
            data : {
                _token : $('[name="_token"]').val(),
                trk : trk,
                postTime : racePostTime,
                date : CURRENT_DATE
            },
            success : function(response){
                console.log(submitArray); // gt or lt
                if(response === "lt"){
                    swal("Race Closed","Race is closed.","error");
                }else if(response === "gt"){
                    var allBetsArr = [];
                    switch(betType){
                        case "wps":
                            var w = wArray;
                            var p = pArray;
                            var s = sArray;
                            $.each(w, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'w','bet':value,'created_at':'','updated_at':''});
                            });
                            $.each(p, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'p','bet':value,'created_at':'','updated_at':''});
                            });
                            $.each(s, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'s','bet':value,'created_at':'','updated_at':''});
                            });
                            break;
                        case "exacta":
                            $.each(submitArray, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'x','bet':value,'created_at':'','updated_at':''});
                            });
                            console.log("<<<<<");
                            console.log(submitArray);
                            break;
                        case "superfecta":
                            $.each(submitArray, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'x','bet':value,'created_at':'','updated_at':''});
                            });
                            break;
                        case "trifecta":
                            $.each(submitArray, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'x','bet':value,'created_at':'','updated_at':''});
                            });
                            break;
                        case "dailydouble":
                            $.each(submitArray, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'x','bet':value,'created_at':'','updated_at':''});
                            });
                            break;
                        case "exactabox":
                            $.each(submitArray, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'x','bet':value,'created_at':'','updated_at':''});
                            });
                            break;
                        case "trifectabox":
                            $.each(submitArray, function(index, value){
                                allBetsArr.push({'player_id': userId ,'race_number':raceNumber,'race_track':trk,'bet_type':betType,'bet_amount':amount,'post_time':racePostTime,'status':0,'type':'x','bet':value,'created_at':'','updated_at':''});
                            });
                            break;
                        default:
                            break;
                    }
                    $.ajax({
                        "url" : BASE_URL + '/dashboard/insertBets',
                        type : "POST",
                        data : {
                            _token : $('[name="_token"]').val(),
                            dataArray : allBetsArr
                        },
                        success : function(response){
                            if(response == 0){
                                swal("Success","Bet successfully saved!","success");
                                $("button.confirm").on("click", function(){
                                    location.reload();
                                });
                            }
                        },
                        error : function(xhr,status,error){
                            alert(error);
                        }
                    });
                    // ##############################################
                }
            },
            error : function(xhr,status,err){
                alert(err);
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
    setInterval(getUpcomingRaces,60000);
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
                    // raceNumber = response[index].substr(response[index].indexOf("&") + 1);
                    raceNumber = response[index].substring(response[index].lastIndexOf("&")+1,response[index].lastIndexOf("/"));
                    mtp = response[index].substring(response[index].lastIndexOf("@")+1,response[index].lastIndexOf("&"));
                    if(mtp > 30){
                        // 30 mins
                    }else {
                        $("table#tblUpcomingRace tbody").append("<tr><td class='upcomingRace' data-track='" + trackName + "' data-number='" + raceNumber + "'>" + trackName + "</td><td>" + raceNumber + "</td><td>" + mtp + "</td></tr>");
                    }
                    });
                console.log(response);
            },
            error : function(xhr, status, err){

            }
        });
    }
    $("body").delegate(".upcomingRace","click", function(){
        var trkName = $(this).data("track");
        var num = $(this).data("number").replace(/\D/g,'');
        $.ajax({
            "url" : BASE_URL + "/dashboard/getTrackCode",
            type : "POST",
            data : {
                _token : $('[name="_token"]').val(),
                name : trkName,
                raceNum : num,
                date : CURRENT_DATE
            },
            success : function(response){
                var trk = response['trkCode'];
                var firstRacePostTime = response['firstRacePostTime'];
                $("#selectedTrack").val(response['trkCode']);
                $("#tempRaces table").remove();
                $("#betTicket").css("display","none");
                $("#betAmount").val("");
                $("#upcomingRacesDiv").css("display","none");
                $("#raceDiv").css("display","block");
                var ddselectedRaceNum = parseInt(num) + parseInt(1);
                var wager = "";
                $.ajax({
                    "url" : BASE_URL + "/dashboard/getWagerForRace",
                    type : "POST",
                    data : {
                        _token : $('[name="_token"]').val(),
                        trk : trk,
                        num : num,
                        date : CURRENT_DATE
                    },
                    success : function(response){
                        $("#selectWager").empty();
                        $.each(response, function(index, value){
                            switch(value){
                                case "WPS":
                                    $('#selectWager').append($('<option>', {value : 'wps', text : 'Win/Place/Show'}));
                                    break;
                                case "Exacta":
                                    $('#selectWager').append($('<option>', {value : 'exacta', text : value}));
                                    break;
                                case "Exacta Box":
                                    $('#selectWager').append($('<option>', {value : 'exactabox', text : value}));
                                    break;
                                case "Daily Double":
                                    $('#selectWager').append($('<option>', {value : 'dailydouble', text : value}));
                                    break;
                                case "Trifecta":
                                    $('#selectWager').append($('<option>', {value : 'trifecta', text : value}));
                                    break;
                                case "Trifecta Box":
                                    $('#selectWager').append($('<option>', {value : 'trifectabox', text : value}));
                                    break;
                                case "Superfecta":
                                    $('#selectWager').append($('<option>', {value : 'superfecta', text : value}));
                                    break;
                            }
                        });
                        if($.inArray(selectedWagerPrev, response) > -1){
                            switch (selectedWagerPrev){
                                case "Win/Place/Show":
                                    wager = "wps";
                                    $('#selectWager').val("wps");
                                    break;
                                case "Exacta":
                                    wager = "exacta";
                                    $('#selectWager').val("exacta");
                                    break;
                                case "Trifecta":
                                    wager = "trifecta";
                                    $('#selectWager').val("trifecta");
                                    break;
                                case "Superfecta":
                                    wager = "superfecta";
                                    $('#selectWager').val("superfecta");
                                    break;
                                case "Daily Double":
                                    wager = "dailydouble";
                                    $('#selectWager').val("dailydouble");
                                    break;
                                default:break;
                            }
                        }else{
                            wager = $("#selectWager").val();
                        }
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
                        fooFunction(BASE_URL,trk,CURRENT_DATE,num,wager);
                    },
                    error : function(xhr,status, error){
                        swal("Error","Error: " + error,"error");
                    }
                });
                // var ddselectedRaceNum = parseInt(num) + parseInt(1);
                // getWagerForRace(BASE_URL,trk,num,CURRENT_DATE);
                // // var wager = $("#selectWager").val();
                // switch(wager){
                //     case "wps":
                //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>W</th><th>P</th><th>S</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         break;
                //     case "superfecta":
                //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th>4</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         break;
                //     case "exacta":
                //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         break;
                //     case "exactabox":
                //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         break;
                //     case "trifecta":
                //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th>2</th><th>3</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         break;
                //     case "trifectabox":
                //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>BOX</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         break;
                //     case "dailydouble":
                //         $("#tempRaces div#ddBoard").html("");
                //         $("#tempRaces").append("<table class=' table table-bordered table-striped "+ trk + num +"'><thead><tr><th>1</th><th class='pp-class'>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         $("#tempRaces").append("<div id='ddBoard'><div> Race "+ ddselectedRaceNum +" </div></div><table class=' table table-bordered table-striped "+ trk + ddselectedRaceNum + " dailydouble'><thead><tr><th>1</th><th>PP</th><th>Horse</th><th>Jockey</th></tr></thead><tbody></tbody></table>");
                //         ajaxGetHorsesPerRace(BASE_URL,response['trkCode'],CURRENT_DATE, ddselectedRaceNum);
                //         break;
                //     default:
                //         break;
                // }
                // fooFunction(BASE_URL,response['trkCode'],CURRENT_DATE,num,wager);
                fooFunction2(BASE_URL,response['trkCode'],num,firstRacePostTime);
            },
            error : function(xhr, status, error){
                alert(error);
            }
        });
    });
    // $(".col-tracks").mCustomScrollbar();
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
            $("table."+ trk + num +" tbody tr").html("");
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
                else if($(this).data("key") == 6){$(this).css({"background":"#000","color":"#fff"});}
                else if($(this).data("key") == 7){$(this).css({"background":"#ff711f","color":"#000"});}
                else if($(this).data("key") == 8){$(this).css({"background":"#ff52b1","color":"#000"});}
                else if($(this).data("key") == 9){$(this).css({"background":"#3badad","color":"#fff"});}
                else if($(this).data("key") == 10){$(this).css({"background":"#800080","color":"#fff"});}
                else if($(this).data("key") == 11){$(this).css({"background":"#b7b7b7","color":"#fff"});}
                else if($(this).data("key") == 12){$(this).css({"background":"#32CD32","color":"#000"});}
                else if($(this).data("key") == 13){$(this).css({"background":"#8A2BE2","color":"#fff"});}
                else if($(this).data("key") == 14){$(this).css({"background":"#808000","color":"#fff"});}
                else if($(this).data("key") == 15){$(this).css({"background":"#ADA96E","color":"#fff"});}
                else if($(this).data("key") == 16){$(this).css({"background":"#2B547E","color":"#fff"});}
                else if($(this).data("key") == 17){$(this).css({"background":"#228b22","color":"#fff"});}
                else if($(this).data("key") == 18){$(this).css({"background":"#4169e1","color":"#fff"});}
                else if($(this).data("key") == 19){$(this).css({"background":"#FF00FF","color":"#fff"});}
                else if($(this).data("key") == 20){$(this).css({"background":"#9932CC","color":"#fff"});}
            });
        },
        error:function(){
            swal("Something went wrong!","Please try again.","error");
        }
    });
}
function ajaxSaveBets(url,betType,trk,raceNumber,racePostTime,amount,betArray,ppArray,wpstype,value){
    $.ajax({
        "url" : url + '/dashboard/saveBet',
        type : "POST",
        data : {
            _token : $('[name="_token"]').val(),
            bettype : betType,
            track : trk,
            raceNum : raceNumber,
            racePost : racePostTime,
            betamount : amount,
//                bet : betArray,
//                pp : ppArray,
            wpsType : wpstype,
            value : value
        },
        success : function(response){
            alert(jQuery.type(response));
            console.log("Saved!");
        },
        error : function(xhr,status,err){
            console.log("Error: " + xhr + status + err);
            swal("Error in saving!","Something went wrong","error");
        }
    });
}
function fooFunction($url,trk,date,num,wager){
    $.ajax({
        "url" : $url + "/dashboard/getHorsesPerRace",
        type : "POST",
        data : {
            _token : $('[name="_token"]').val(),
            code : trk,
            date : date,
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
                else if($(this).data("key") == 6){$(this).css({"background":"#000","color":"#fff"});}
                else if($(this).data("key") == 7){$(this).css({"background":"#ff711f","color":"#000"});}
                else if($(this).data("key") == 8){$(this).css({"background":"#ff52b1","color":"#000"});}
                else if($(this).data("key") == 9){$(this).css({"background":"#3badad","color":"#fff"});}
                else if($(this).data("key") == 10){$(this).css({"background":"#800080","color":"#fff"});}
                else if($(this).data("key") == 11){$(this).css({"background":"#b7b7b7","color":"#fff"});}
                else if($(this).data("key") == 12){$(this).css({"background":"#32CD32","color":"#000"});}
                else if($(this).data("key") == 13){$(this).css({"background":"#8A2BE2","color":"#fff"});}
                else if($(this).data("key") == 14){$(this).css({"background":"#808000","color":"#fff"});}
                else if($(this).data("key") == 15){$(this).css({"background":"#ADA96E","color":"#fff"});}
                else if($(this).data("key") == 16){$(this).css({"background":"#2B547E","color":"#fff"});}
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
}

function fooFunction2(url,trk,num,post){
    $("#selectedTrack").val(trk);
    $("#selectedRaceNum").val(num);
    $("#selectedRacePostTime").val(post);
    $("#submitBet").css("display","block");
    $.ajax({
        'url' : url + '/dashboard/getTrackName',
        type : "POST",
        data : {
            _token : $('[name="_token"]').val(),
            trk : trk
        },
        success : function(response){
            $("#raceTrackName, #raceNumberAndPostTime").html("");
            $("#raceTrackName").append(response[0]);
            var tmz = response[1];
            if(tmz === "PDT"){
                tmz = "Pacific";
            }else if(tmz === "MDT"){
                tmz = "Mountain";
            }else if(tmz === "CDT"){
                tmz = "Central";
            }else if(tmz === "EDT"){
                tmz = "Eastern";
            }
            $("#raceNumberAndPostTime").append("Race " + num + " POST TIME: " + post + "("+ tmz +")");
        },
        error : function(){
            swal("Something went wrong!","Please try again.","error");
            $("#tempRaces table").remove();
        }
    });
}
function getWagerForRace(url ,trkCode, raceNum, date){
    $.ajax({
        "url" : url + "/dashboard/getWagerForRace",
        type : "POST",
        data : {
            _token : $('[name="_token"]').val(),
            trk : trkCode,
            num : raceNum,
            date : date
        },
        success : function(response){
            $("#selectWager").empty();
            $.each(response, function(index, value){
                switch(value){
                    case "WPS":
                        $('#selectWager').append($('<option>', {value : 'wps', text : 'Win/Place/Show'}));
                        break;
                    case "Exacta":
                        $('#selectWager').append($('<option>', {value : 'exacta', text : value}));
                        break;
                    case "Daily Double":
                        $('#selectWager').append($('<option>', {value : 'dailydouble', text : value}));
                        break;
                    case "Trifecta":
                        $('#selectWager').append($('<option>', {value : 'trifecta', text : value}));
                        break;
                    case "Superfecta":
                        $('#selectWager').append($('<option>', {value : 'superfecta', text : value}));
                        break;
                }
            });
        },
        error : function(xhr,status, error){
            swal("Error","Error: " + error,"error");
        }
    });
}
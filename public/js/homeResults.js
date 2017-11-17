$("document").ready(function(){
    var BASE_URL = $("#hdnURL").val();
    var userID = $("#userId").val();
    var CURRENT_DATE = $("#datepickerResults").val();
    $("#datepickerResults").datepicker({
        dateFormat: "yy-mm-dd",
        maxDate : -1
    });
    $("#datepickerResults").on("change",function(){
        var date = $(this).val();
        $("#resultsDiv, #trkHeader").html("");
        $(".loader").css("display","block");
        $.ajax({
            "url" : BASE_URL + '/dashboard/getTracksByDate',
            type : "POST",
            data : {
                _token : $('[name="_token"]').val(),
                date : date
            },
            success : function(response){
                console.log(response);
                $("#resultsDiv").append("<div class='jumbotron text-center'><h1>Select a Race Track</h1></div>");
                $(".loader").css("display","none");
                $("#trkDiv, #trackHeader").html("");
                $("#trackHeader").text("Tracks " + date);
                $.each(response,function(key,value){
                    $("#trkDiv").append("<div class='trk'><h5 data-date='"+ response[key]["date"] +"' data-code='"+ response[key]["code"] +"'>"+ response[key]["name"] +"</h5></div>");
                });
            },
            error : function(xhr,status,error){
                console.log(error);
                alert(error);
            }
        });
    });
    $("body").delegate(".trk","click",function(){
        var date = $(this).find("h5").data("date");
        var code = $(this).find("h5").data("code");
        $("#trkHeader").text($(this).text());
        $(".loader").css("display","block");
        $.ajax({
            "url" : BASE_URL + "/dashboard/getResultsForDisplay",
            type : "POST",
            data : {
                _token : $('[name="_token"]').val(),
                date : date,
                trk : code
            },
            success : function(respo){
                console.log(respo);
                $(".loader").css("display","none");
                if(respo.length == 0){
                    $("#resultsDiv").html("");
                    $("#resultsDiv").append("<div class='jumbotron text-center'><h1>No Results</h1></div>");
                }else{
                    $("#resultsDiv").html("");
                    $.each(respo, function(index,value){
                        if($("#" + respo[index]["trk"] + respo[index]["num"]).length == 0) {
                            $("#resultsDiv").append("<div id='"+ respo[index]["trk"] + respo[index]["num"] +"'><h3 class='raceHeader'> Race " + respo[index]["num"] + "</h3>" +
                                "<table  class='table table-bordered table-responsive text-center'>" +
                                "<thead><tr><th>p#</th><th>Win</th><th>Place</th><th>Show</th></tr></thead><tbody></tbody></table>" +
                                "<table class='table table-bordered table-responsive text-center'>" +
                                "<thead><tr><th>Wager Type</th><th>Winning Numbers</th><th>Payoff</th></tr></thead>" +
                                "<tbody></tbody></table>" +
                                "</div>");
                            if(respo[index]["wager"] == "exacta"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " + respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "trifecta"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " + respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "superfecta"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" +respo[index]["minimum"] + " " +  respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "dailydouble"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " + respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "quinella"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " +  respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }
                            // else if(respo[index]["wager"] == "wps"){
                            //     var strExplode = respo[index]["result"].split(",");
                            //     $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(0) tbody").append("<tr><td bgcolor='"+ changeColor(strExplode[0]) +"'><font color='"+ changeFontColor(strExplode[0]) +"'>"+ strExplode[0] +"</font></td><td>"+ respo[index]["horse"][0] +"</td><td>"+ respo[index]["payout"][0] +"</td><td>"+ respo[index]["payout"][1] +"</td><td>"+ respo[index]["payout"][3] +"</td></tr>" +
                            //         "<tr><td bgcolor='"+ changeColor(strExplode[1]) +"'><font color='"+ changeFontColor(strExplode[1]) +"'>"+ strExplode[1] +"</font></td><td>"+ respo[index]["horse"][1] +"</td><td></td><td>"+ respo[index]["payout"][2] +"</td><td>"+ respo[index]["payout"][4] +"</td></tr>" +
                            //         "<tr><td bgcolor='"+ changeColor(strExplode[2]) +"'><font color='"+ changeFontColor(strExplode[2]) +"'>"+ strExplode[2] +"</font></td><td>"+ respo[index]["horse"][2] +"</td><td></td><td></td><td>"+ respo[index]["payout"][5] +"</td></tr>");
                            // }
                            else if(respo[index]["wager"] == "wps"){
                                var strExplode = respo[index]["result"].split(",");
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(0) tbody").append("<tr><td bgcolor='"+ changeColor(strExplode[0]) +"' class='pnumTD'><font color='"+ changeFontColor(strExplode[0]) +"'>"+ strExplode[0] +"</font></td><td>"+ respo[index]["payout"][0] +"</td><td>"+ respo[index]["payout"][1] +"</td><td>"+ respo[index]["payout"][3] +"</td></tr>" +
                                    "<tr><td bgcolor='"+ changeColor(strExplode[1]) +"' class='pnumTD'><font color='"+ changeFontColor(strExplode[1]) +"'>"+ strExplode[1] +"</font></td><td></td><td>"+ respo[index]["payout"][2] +"</td><td>"+ respo[index]["payout"][4] +"</td></tr>" +
                                    "<tr><td bgcolor='"+ changeColor(strExplode[2]) +"' class='pnumTD'><font color='"+ changeFontColor(strExplode[2]) +"'>"+ strExplode[2] +"</font></td><td></td><td></td><td>"+ respo[index]["payout"][5] +"</td></tr>");
                            }
                        }else{
                            if(respo[index]["wager"] == "exacta"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " + respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "trifecta"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " + respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "superfecta"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" +respo[index]["minimum"] + " " +  respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "dailydouble"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " + respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "quinella"){
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(1) tbody").append("<tr><td> $" + respo[index]["minimum"] + " " +  respo[index]["wager"] +  "</td><td>" + respo[index]["result"].replace(/,/g,'-') + "</td><td>"+  respo[index]["payout"][0] +"</td></tr>");
                            }else if(respo[index]["wager"] == "wps"){
                                var strExplode = respo[index]["result"].split(",");
                                $("#resultsDiv").find("div#" + respo[index]["trk"] + respo[index]["num"] + " table:eq(0) tbody").append("<tr><td>"+ strExplode[0] +"</td><td>HorseName</td><td>"+ respo[index]["payout"][0] +"</td><td>"+ respo[index]["payout"][1] +"</td><td>"+ respo[index]["payout"][3] +"</td></tr>" +
                                    "<tr><td>"+ strExplode[1] +"</td><td>HorseName</td><td></td><td>"+ respo[index]["payout"][2] +"</td><td>"+ respo[index]["payout"][4] +"</td></tr>" +
                                    "<tr><td>"+ strExplode[2] +"</td><td>HorseName</td><td></td><td></td><td>"+ respo[index]["payout"][5] +"</td></tr>");
                            }
                        }
                    });
                }
            },
            error : function(xhr,status,error){
                alert(error);
            }
        });
    });
});
function changeColor(pnumber){
    switch (pnumber){
        case "1":
            return "#FF0000";
            break;
        case "1A":
            return "#FF0000";
            break;
        case "2":
            return "#fff";
            break;
        case "2B":
            return "#fff";
            break;
        case "3":
            return "#0000FF";
            break;
        case "3C":
            return "#0000FF";
            break;
        case "4":
            return "#FFFF00";
            break;
        case "4D":
            return "#FFFF00";
            break;
        case "5":
            return "#008000";
            break;
        case "5E":
            return "#008000";
            break;
        case "6":
            return "#000";
            break;
        case "7":
            return "#ff711f";
            break;
        case "8":
            return "#ff52b1";
            break;
        case "9":
            return "#3badad";
            break;
        case "10":
            return "#800080";
            break;
        case "11":
            return "#b7b7b7";
            break;
        case "12":
            return "#32CD32";
            break;
        case "13":
            return "#8A2BE2";
            break;
        case "14":
            return "#808000";
            break;
        case "15":
            return "#ADA96E";
            break;
        case "16":
            return "#2B547E";
            break;
        case "17":
            return "#228b22";
            break;
        case "18":
            return "#4169e1";
            break;
        case "19":
            return "#FF00FF";
            break;
        case "20":
            return "#9932CC";
            break;
        default:
            return "#000;";
            break;
    }
}
function changeFontColor(pnumber){
    switch (pnumber){
        case "1":
            return "#fff";
            break;
        case "1A":
            return "#fff";
            break;
        case "2":
            return "#000";
            break;
        case "2B":
            return "#000";
            break;
        case "3":
            return "#fff";
            break;
        case "3C":
            return "#fff";
            break;
        case "4":
            return "#000";
            break;
        case "4D":
            return "#000";
            break;
        case "5":
            return "#fff";
            break;
        case "5E":
            return "#fff";
            break;
        case "6":
            return "#fff";
            break;
        case "7":
            return "#000";
            break;
        case "8":
            return "#000";
            break;
        case "9":
            return "#fff";
            break;
        case "10":
            return "#fff";
            break;
        case "11":
            return "#fff";
            break;
        case "12":
            return "#000";
            break;
        case "13":
            return "#fff";
            break;
        case "14":
            return "#fff";
            break;
        case "15":
            return "#fff";
            break;
        case "16":
            return "#fff";
            break;
        case "17":
            return "#fff";
            break;
        case "18":
            return "#fff";
            break;
        case "19":
            return "#fff";
            break;
        case "20":
            return "#fff";
            break;
        default:
            return "#000;";
            break;
    }
}
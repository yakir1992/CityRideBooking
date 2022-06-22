/*** this document is the script for the Reservations tab ***/

// 2 variables for navigation arrows in the general result (with no search code) 
var res_page_number = 1;
var total_pages_res = get_total_pages_res();

// uses the main table to show "Shared Shuttle" as "Shuttle" & "Private Service" as "Private"
function serviceType(type) {
    if (type == "Shared Shuttle") {
        return "Shuttle";
    } else if (type == "Private Service") {
        return "Private";
    } else {
        return type
    }
}

// check if its only 'return' service so depart date is return date, else ist arrival date. also shows the date with no year
function departDate(arrDate, depDate, quantity) {
    if (quantity != "One Way(Departure)") {
        var dateArray = arrDate.split("/");
        return dateArray[0] + "/" + dateArray[1];
    } else {
        var dateArray = depDate.split("/");
        return dateArray[0] + "/" + dateArray[1];
    }
}

// change the format of the creation date & time
function creationDate(createDate) {
    var dateTime = createDate.split(" ");
    var date = dateTime[0].split("-");
    var time = dateTime[1].split(":");
    return date[2] + "/" + date[1] + " - " + time[0] + ":" + time[1];
}

// check if the the reservation made by agent with sabre session - if yes so mark a V in the sabre column
function isSabreBooking(SabreUser) {
    if (SabreUser == 1) {
        return "glyphicon glyphicon-ok";
    } else if (SabreUser == 0) {
        return;
    }
}

// 'on load' functions
$(function () {
    // get all reservations results into main table
    getAllRes();

    // on click on the search button/enter key up triggers function resSearch that switch the results on 
    // the main table according to res num/pax name/agent - no limit there, display all the results
    $("#res_searchBtn").on("click", resSearch);
     $("#res_search").keyup(function (e) {
     if (e.which == 13) {
     resSearch();
     }
     });
});

// get all the results from reservation DB to main table but limited, page number uses for offset
// this function is trigered 'onload' and on empty search code
function getAllRes() {

    // return the number of total pages accordind to the 'PAGE SIZE' definition in the BusinessLogic
    get_total_pages_res();

    // ajax request to get results from data base and build the main table with those results
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_all_res", page_number: res_page_number},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (res) {
            res = JSON.parse(res);
            $("#res_info").empty();
            $("#res_info").append("<tr class='info'><th style='width:10%;text-align:center;'>Booking ID</th>" +
                    "<th style='width:15%;text-align:center;'>Pax Name</th>" +
                    "<th style='width:8%;text-align:center;'>Depart</th>" +
                    "<th style='width:6%;text-align:center;'>A/P</th>" +
                    "<th style='width:9%;text-align:center;'>Type</th>" +
                    "<th style='width:10%;text-align:center;'>Created</th>" +
                    "<th style='width:9%;text-align:center;'>Total</th>" +
                    "<th style='width:11%;text-align:center;'>Agency</th>" +
                    "<th style='width:6%;text-align:center;'>Status</th>" +
                    "<th style='width:6%;text-align:center;'>Sabre</th>" +
                    "<th style='width:10%;text-align:center;'>View</th></tr>");
            $("#res_arrows").empty();
            $("#res_arrows").append("<a href='javascript:res_prev()'>&lt;</a>&nbsp;&nbsp; Page " + res_page_number + " out of " + total_pages_res +
                    " &nbsp;&nbsp;<a href='javascript:res_next()'>&gt;</a>&nbsp;&nbsp;");
            for (var i = 0; i < res.length; i++) {
                $("#res_info").append(
                        "<tr><td>" + res[i].r_res_num + "</td><td>" + res[i].r_pax_name +
                        "</td><td>" + departDate(res[i].r_arr_date, res[i].r_dep_date, res[i].r_quantity) + "</td><td>" +
                        res[i].r_airport_code + "</td><td>" + serviceType(res[i].r_type) + "</td>" +
                        "<td>" + creationDate(res[i].r_created) + "</td><td>" + res[i].r_total_cost + " " + res[i].r_currency +
                        "</td><td>" + res[i].r_agency + "</td><td>" + res[i].r_status + "</td><td><span class='" + isSabreBooking(res[i].sabre_user) + "'></span></td>" +
                        "<td><button data-toggle='modal' data-target='#viewRes' class='glyphicon glyphicon-pencil'" +
                        "style='margin-right:6%; height:25px;' onclick='viewRes(\"" +
                        res[i].r_res_num + "\", \"" + res[i].r_status +
                        "\", \"" + res[i].r_quantity + "\", \"" + res[i].r_type + "\", \"" + res[i].r_total_cost +
                        "\", \"" + res[i].r_currency + "\", \"" + res[i].r_min_pax + "\", \"" + res[i].r_max_pax +
                        "\", \"" + res[i].r_description + "\", \"" + res[i].r_arr_date + "\", \"" + res[i].r_arr_flight +
                        "\", \"" + res[i].r_arr_dest + "\", \"" + res[i].r_dep_date + "\", \"" + res[i].r_dep_flight +
                        "\", \"" + res[i].r_dep_dest + "\", \"" + res[i].r_airport_code + "\", \"" + res[i].r_pax_name +
                        "\", \"" + res[i].r_pax_num + "\", \"" + res[i].r_pax_phone + "\", \"" + res[i].r_remark +
                        "\", \"" + res[i].r_agency + "\", \"" + res[i].r_agent_name + "\", \"" + res[i].r_agent_email +
                        "\", \"" + res[i].r_agent_phone + "\", \"" + res[i].r_id + "\")'> View</button>" + "</td></tr>");
            }
        }
    });
}

// function that get by ajax the amount of pages according to limit per page in BusinessLogic (uses 
// only the main getAllUsers function)
function get_total_pages_res() {
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_total_pages_res"},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (total_pages_res) {
            window.total_pages_res = total_pages_res;
        }
    });
}

// 2 functions for prev & next arrows
function res_prev() {
    if (res_page_number != 1) {
        res_page_number--;
        getAllRes();
    }
}

function res_next() {
    if (res_page_number != total_pages_res) {
        res_page_number++;
        getAllRes();
    }
}

// function that is triggered by clicking the 'search' button (or enter btn), returning result only where 
// res num/pax name/agent has partial match (res num only full match) to the text that is in the search line
function resSearch() {
    // gets the value in the search line
    var res_search = $("#res_search").val();
    
    // if the search line is empty triggers again the main function of getAllRes with page number 1
    if (res_search == "") {
        res_page_number = 1;
        return getAllRes();
    }
    
    // ajax to get the result for table according to search
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_res_by_search", res_search: res_search},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (res) {
            res = JSON.parse(res);
            $("#res_info").empty();
            $("#res_arrows").empty();
            $("#res_info").append("<tr class='info'><th style='width:10%;text-align:center;'>Booking ID</th>" +
                    "<th style='width:15%;text-align:center;'>Pax Name</th>" +
                    "<th style='width:8%;text-align:center;'>Depart</th>" +
                    "<th style='width:6%;text-align:center;'>A/P</th>" +
                    "<th style='width:9%;text-align:center;'>Type</th>" +
                    "<th style='width:10%;text-align:center;'>Created</th>" +
                    "<th style='width:9%;text-align:center;'>Total</th>" +
                    "<th style='width:11%;text-align:center;'>Agency</th>" +
                    "<th style='width:6%;text-align:center;'>Status</th>" +
                    "<th style='width:6%;text-align:center;'>Sabre</th>" +
                    "<th style='width:10%;text-align:center;'>View</th></tr>");
            for (var i = 0; i < res.length; i++) {
                $("#res_info").append(
                        "<tr><td>" + res[i].r_res_num + "</td><td>" + res[i].r_pax_name +
                        "</td><td>" + departDate(res[i].r_arr_date, res[i].r_dep_date, res[i].r_quantity) + "</td><td>" +
                        res[i].r_airport_code + "</td><td>" + serviceType(res[i].r_type) + "</td>" +
                        "<td>" + creationDate(res[i].r_created) + "</td><td>" + res[i].r_total_cost + " " + res[i].r_currency +
                        "</td><td>" + res[i].r_agency + "</td><td>" + res[i].r_status + "</td><td><span class='" + isSabreBooking(res[i].sabre_user) + "'></span></td>" +
                        "<td><button data-toggle='modal' data-target='#viewRes' class='glyphicon glyphicon-pencil'" +
                        "style='margin-right:6%; height:25px;' onclick='viewRes(\"" +
                        res[i].r_res_num + "\", \"" + res[i].r_status +
                        "\", \"" + res[i].r_quantity + "\", \"" + res[i].r_type + "\", \"" + res[i].r_total_cost +
                        "\", \"" + res[i].r_currency + "\", \"" + res[i].r_min_pax + "\", \"" + res[i].r_max_pax +
                        "\", \"" + res[i].r_description + "\", \"" + res[i].r_arr_date + "\", \"" + res[i].r_arr_flight +
                        "\", \"" + res[i].r_arr_dest + "\", \"" + res[i].r_dep_date + "\", \"" + res[i].r_dep_flight +
                        "\", \"" + res[i].r_dep_dest + "\", \"" + res[i].r_airport_code + "\", \"" + res[i].r_pax_name +
                        "\", \"" + res[i].r_pax_num + "\", \"" + res[i].r_pax_phone + "\", \"" + res[i].r_remark +
                        "\", \"" + res[i].r_agency + "\", \"" + res[i].r_agent_name + "\", \"" + res[i].r_agent_email +
                        "\", \"" + res[i].r_agent_phone + "\", \"" + res[i].r_id + "\")'> View</button>" + "</td></tr>");
            }
        }
    });
}

// this function is triggerd by clicking the 'view' button on the table
// this function appending to the modal window (that is triggered by the same button) all the booking details & 
// a form to update res num & status
function viewRes(resNum, status, quantity, type, totalCost, currency, minPax, maxPax, description, arrDate, arrFlight,
        arrDest, depDate, depFlight, depDest, apCode, paxName, paxNum, paxPhone, remark, agency, agentName, agentEmail,
        agentPhone, id) {

    var paxTypeNum = maxPax;
    if (minPax != maxPax) {
        paxTypeNum = minPax + "-" + maxPax;
    }

    if (quantity == "One Way(Arrival)") {
        var resDetails = '<div class="resViewP"><span class="transDet"><b>Arrival: </b></span>' + arrDate + ' - <u>From</u>: ' + apCode + ' Airport (flight - ' + arrFlight +
                ') <u>To</u>: ' + arrDest + '</div>';
    } else if (quantity == "One Way(Departure)") {
        var resDetails = '<div class="resViewP"><span class="transDet"><b>Departure: </b></span>' + depDate + ' - <u>From</u>: ' + depDest + ' <u>To</u>: ' + apCode +
                ' Airport (flight - ' + depFlight + ')</div>';
    } else if (quantity == "Round Trip") {
        var resDetails = '<div class="resViewP"><span class="transDet"><b>Arrival: </b></span>' + arrDate + ' - <u>From</u>: ' + apCode + ' Airport (flight - ' + arrFlight +
                ') <u>To</u>: ' + arrDest + '</div><div class="resViewP"><span class="transDet"><b>Departure: </b></span>' + depDate + ' - <u>From</u>: ' + depDest + ' <u>To</u>: ' +
                apCode + ' Airport (flight - ' + depFlight + ')</div>';
    }

    $("#resHead").empty();
    $("#resHead").append('Reservation No. ' + resNum);
    $("#editViewRes").empty();
    $("#editViewRes").append(
            '<div class="panel panel-default"><div class="panel-body"><form method="POST" action="API.php" id="updateResForm">' +
            '<div class="form-inline"><div class="form-group"><label for="resNum">Booking Number: &nbsp;</label>' +
            '<input id="resNum" name="resNum" type="text" class="form-control" value="' + resNum + '" /></div>' +
            '<div class="form-group"><label for="resStatus">&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Status: &nbsp;</label>' +
            '<select name="resStatus" id="resStatus" class="form-control"><option value="RQ">RQ</option>' +
            '<option value="OK">OK</option><option value="CXL">CXL</option></select></div><div class="form-group" style="float:right">' +
            '<label> Total Cost: &nbsp</label><span class="form-control">' + totalCost + ' ' + currency + '</span>' +
            '</div></div><input type="hidden" name="command" value="update_res" /><input type="hidden" name="id" value="' + id + '" /></form></div></div>' + '<div class="panel panel-default"><div class="panel-heading">' +
            '<h5 class="panel-title">Transfer Details</h5></div><div class="panel-body"><div class="resViewP"><span class="transDet"><b>Service Type: </b></span>' +
            quantity + ' ' + type + ' for ' + paxTypeNum + ' Passengers</div><div class="resViewP"><span class="transDet"><b>Description: </b></span>' +
            description + '</div>' + resDetails + '</div></div><div class="panel panel-default">' +
            '<div class="panel-heading"><h5 class="panel-title">Passenger\'s Details</h5></div>' +
            '<div class="panel-body"><div class="resViewP"><b>Lead Name:</b> ' + paxName + '&nbsp &nbsp <span class="divider"></span> &nbsp &nbsp <b>Pax No.:</b> ' +
            paxNum + '&nbsp &nbsp <span class="divider"></span> &nbsp &nbsp <b>Phone:</b> ' + paxPhone + '&nbsp &nbsp <span class="divider"></span>&nbsp &nbsp <b>Remark:</b> ' +
            remark + '</div></div></div><div class="panel panel-default"><div class="panel-heading">' +
            '<h5 class="panel-title">Agent Details</h5></div><div class="panel-body"><div class="resViewP"><b>Agency:</b> ' +
            agency + '&nbsp &nbsp <span class="divider"></span> &nbsp &nbsp <b>Agent:</b> ' + agentName + '&nbsp &nbsp <span class="divider"></span>&nbsp &nbsp<b>Email:</b> ' +
            agentEmail + '&nbsp &nbsp <span class="divider"></span>&nbsp &nbsp <b>Phone:</b> ' + agentPhone + '</div></div></div>' +
            '<button type="button" class="btn btn-success" style="margin-left: 91%" onclick="updateRes()">Update</button>');

    $("#resStatus").val(status);

}

// check that res num is not empty & submiting the form
function updateRes() {
    if ($("#resNum").val() === "") {
        $("#resNum").parent().addClass("has-error");
    } else {
        $("#updateResForm").submit();
    }
    $("#resNum").on("change or keyup", function(){ $("#resNum").parent().removeClass("has-error");});
}




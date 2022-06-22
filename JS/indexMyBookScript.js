/*** this document is the script for the My Bookings tab ***/

// 2 variables for navigation arrows in the general result (with no search code) 
var agent_bookings_page_number = 1;
var total_pages_agent_bookings = get_total_pages_agent_bookings();

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

// 'on load' functions
$(function () {
    // get all agents booking results into main table
    get_all_agent_bookings();

    // on click on the search button/enter key up triggers function agentBookingsSearch that switch the results on 
    // the main table according to res num/pax name/airport - no limit there, display all the results
    $("#agent_bookings_searchBtn").on("click", agentBookingsSearch);
    $("#agent_bookings_search").keyup(function (e) {
        if (e.which == 13) {
            agentBookingsSearch();
        }
    });
});

// get all the results matching user id from reservation DB to main table but limited, page number uses for offset
// this function is trigered 'onload' and on empty search code
function get_all_agent_bookings() {

    // return the number of total pages accordind to the 'PAGE SIZE' definition in the BusinessLogic
    get_total_pages_agent_bookings();

    // ajax request to get results from data base and build the main table with those results
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_all_agent_bookings", page_number: agent_bookings_page_number},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (res) {
            res = JSON.parse(res);
            $("#agent_bookings_info").empty();
            $("#agent_bookings_info").append("<tr class='info'><th style='width:10%;text-align:center;'>Booking ID</th>" +
                    "<th style='width:18%;text-align:center;'>Pax Name</th>" +
                    "<th style='width:10%;text-align:center;'>Depart</th>" +
                    "<th style='width:6%;text-align:center;'>A/P</th>" +
                    "<th style='width:9%;text-align:center;'>Type</th>" +
                    "<th style='width:11%;text-align:center;'>Created</th>" +
                    "<th style='width:9%;text-align:center;'>Total</th>" +
                    "<th style='width:13%;text-align:center;'>Service</th>" +
                    "<th style='width:6%;text-align:center;'>Status</th>" +
                    "<th style='width:8%;text-align:center;'>View</th></tr>");
            $("#agent_bookings_arrows").empty();
            $("#agent_bookings_arrows").append("<a href='javascript:res_prev()'>&lt;</a>&nbsp;&nbsp; Page " + agent_bookings_page_number + " out of " + total_pages_agent_bookings +
                    " &nbsp;&nbsp;<a href='javascript:res_next()'>&gt;</a>&nbsp;&nbsp;");
            for (var i = 0; i < res.length; i++) {
                $("#agent_bookings_info").append(
                        "<tr><td>" + res[i].r_res_num + "</td><td>" + res[i].r_pax_name +
                        "</td><td>" + departDate(res[i].r_arr_date, res[i].r_dep_date, res[i].r_quantity) + "</td><td>" +
                        res[i].r_airport_code + "</td><td>" + serviceType(res[i].r_type) + "</td>" +
                        "<td>" + creationDate(res[i].r_created) + "</td><td>" + res[i].r_total_cost + " " + res[i].r_currency +
                        "</td><td>" + res[i].r_quantity + "</td><td>" + res[i].r_status + "</td>" +
                        "<td><button data-toggle='modal' data-target='#view_agent_booking' class='btn btn-primary btn-xs'" +
                        "onclick='viewRes(\"" +
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
// only the main get_all_agent_bookings function)
function get_total_pages_agent_bookings() {
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_total_pages_agent_bookings"},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (total_pages_agent_bookings) {
            window.total_pages_agent_bookings = total_pages_agent_bookings;
        }
    });
}

// 2 functions for prev & next arrows
function res_prev() {
    if (agent_bookings_page_number != 1) {
        agent_bookings_page_number--;
        get_all_agent_bookings();
    }
}

function res_next() {
    if (agent_bookings_page_number != total_pages_agent_bookings) {
        agent_bookings_page_number++;
        get_all_agent_bookings();
    }
}

// function that is triggered by clicking the 'search' button (or enter btn), returning result only where 
// res num/pax name/airport has partial match (res num only full match) to the text that is in the search line
function agentBookingsSearch() {
    // gets the value in the search line
    var agent_bookings_search = $("#agent_bookings_search").val();

    // if the search line is empty triggers again the main function of getAllRes with page number 1
    if (agent_bookings_search == "") {
        agent_bookings_page_number = 1;
        return get_all_agent_bookings();
    }

    // ajax to get the result for table according to search
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_agent_bookings_by_search", agent_bookings_search: agent_bookings_search},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (res) {
            res = JSON.parse(res);
            $("#agent_bookings_info").empty();
            $("#agent_bookings_arrows").empty();
            $("#agent_bookings_info").append("<tr class='info'><th style='width:10%;text-align:center;'>Booking ID</th>" +
                    "<th style='width:18%;text-align:center;'>Pax Name</th>" +
                    "<th style='width:10%;text-align:center;'>Depart</th>" +
                    "<th style='width:6%;text-align:center;'>A/P</th>" +
                    "<th style='width:9%;text-align:center;'>Type</th>" +
                    "<th style='width:11%;text-align:center;'>Created</th>" +
                    "<th style='width:9%;text-align:center;'>Total</th>" +
                    "<th style='width:13%;text-align:center;'>Service</th>" +
                    "<th style='width:6%;text-align:center;'>Status</th>" +
                    "<th style='width:8%;text-align:center;'>View</th></tr>");
            for (var i = 0; i < res.length; i++) {
                $("#agent_bookings_info").append(
                        "<tr><td>" + res[i].r_res_num + "</td><td>" + res[i].r_pax_name +
                        "</td><td>" + departDate(res[i].r_arr_date, res[i].r_dep_date, res[i].r_quantity) + "</td><td>" +
                        res[i].r_airport_code + "</td><td>" + serviceType(res[i].r_type) + "</td>" +
                        "<td>" + creationDate(res[i].r_created) + "</td><td>" + res[i].r_total_cost + " " + res[i].r_currency +
                        "</td><td>" + res[i].r_quantity + "</td><td>" + res[i].r_status + "</td>" +
                        "<td><button data-toggle='modal' data-target='#view_agent_booking' class='btn btn-primary btn-xs'" +
                        "onclick='viewRes(\"" +
                        res[i].r_res_num + "\", \"" + res[i].r_status +
                        "\", \"" + res[i].r_quantity + "\", \"" + res[i].r_type + "\", \"" + res[i].r_total_cost +
                        "\", \"" + res[i].r_currency + "\", \"" + res[i].r_min_pax + "\", \"" + res[i].r_max_pax +
                        "\", \"" + res[i].r_description + "\", \"" + res[i].r_arr_date + "\", \"" + res[i].r_arr_flight +
                        "\", \"" + res[i].r_arr_dest + "\", \"" + res[i].r_dep_date + "\", \"" + res[i].r_dep_flight +
                        "\", \"" + res[i].r_dep_dest + "\", \"" + res[i].r_airport_code + "\", \"" + res[i].r_pax_name +
                        "\", \"" + res[i].r_pax_num + "\", \"" + res[i].r_pax_phone + "\", \"" + res[i].r_remark +
                        "\", \"" + res[i].r_agency + "\", \"" + res[i].r_agent_name + "\", \"" + res[i].r_agent_email +
                        "\", \"" + res[i].r_agent_phone + "\")'> View</button>" + "</td></tr>");
            }
        }
    });
}

// this function is triggerd by clicking the 'view' button on the table
// this function appending to the modal window (that is triggered by the same button) all the booking details 
function viewRes(resNum, status, quantity, type, totalCost, currency, minPax, maxPax, description, arrDate, arrFlight,
        arrDest, depDate, depFlight, depDest, apCode, paxName, paxNum, paxPhone, remark, agency, agentName, agentEmail,
        agentPhone) {

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

    $("#agent_booking_head").empty();
    $("#agent_booking_head").append('Reservation No. ' + resNum);
    $("#agent_booking_details").empty();
    $("#agent_booking_details").append(
            '<div class="panel panel-default"><div class="panel-heading">' +
            '<h5 class="panel-title">General Details</h5></div><div class="panel-body"><div class="resViewP"><span class="transDet"><b>Booking ID: </b></span>' +
            resNum + '</div><div class="resViewP"><span class="transDet"><b>Status: </b></span>' + status + '</div><div class="resViewP"><span class="transDet">' +
            '<b>Total Cost: </b></span>' + totalCost + ' ' + currency + '</div></div></div><div class="panel panel-default"><div class="panel-heading">' +
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
            '<button type="button" class="btn btn-danger" style="margin-left: 91%" data-dismiss="modal">Back</button>');
}





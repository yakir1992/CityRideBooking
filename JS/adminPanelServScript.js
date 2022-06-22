/*** this document is the script for the services tab + some general issues in the adminPanel.php  ***/

// 2 variables for navigation arrows in the general result (with no search code) 
var page_number = 1;
var total_pages = get_total_pages();

// check if the min & max pax are same show only one of them, in not shows min xx max yy - will 
// be used in the functions that get results to table (getPage, searchCode)
function minMax(min, max) {
    if (min == max) {
        return max
    } else {
        return "min " + min + " max " + max
    }
}

// 'on load' functions
$(function () {
    // function that checks if a service was update so return to page with the same search code, else triggers the getPage() function that get all transfer results into main table
    lastSearchCode();
 
    // on click on the search button/enter key up triggers function searchCode that switch the results on 
    // the main table according to airport/destination code - no limit there, display all the results
    $("#searchBtn").on("click", searchCode);
    $("#search_code").keyup(function (e) {
        if (e.which == 13) {
            searchCode();
        }
    });

    // next code is for displaying the latest tab (after refresh etc.)
    // save the latest tab into session storage
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        sessionStorage.setItem('lastTab', $(e.target).attr('href'));
    });
    //go to the latest tab, if it exists:
    var lastTab = sessionStorage.getItem('lastTab');
    if (lastTab) {
        $('#adminTabs a[href="' + lastTab + '"]').tab('show');
    }

    // check for integer only fields when adding new service and alertin the client (validation itself is later)
    $("#service_t_min_pax, #service_t_max_pax, #service_t_price").on("blur", function () {
        var spanId = "span_" + $(this).attr("name");
        if (isNaN($(this).val())) {
            $("#" + spanId).empty();
            $("#" + spanId).append(" Must be a number");
        } else {
            $("#" + spanId).empty();
        }
    });

    // check for max 3 letters when adding new service and alertin the client (validation itself is later)
    $("#service_t_airport_code, #service_t_destination_code").on("blur", function () {
        var spanId = "span_" + $(this).attr("name");
        if ($(this).val().length > 3) {
            $("#" + spanId).empty();
            $("#" + spanId).append(" Up to 3 letters");
        } else {
            $("#" + spanId).empty();
        }
    });
});

// get all the results from transfers DB to main table but limited, page number uses for offset
// this function is trigered 'onload' and on empty search code
function getPage() {

    // return the number of total pages accordind to the 'PAGE SIZE' definition in the BusinessLogic
    get_total_pages();

    // ajax request to get results from data base and build the main table with those results
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_all_transfers", page_number: page_number},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (transfers) {
            transfers = JSON.parse(transfers);
            $("#transfer_info").empty();
            $("#editServiceDetails").empty();
            $("#transfer_info").append("<tr class='info'><th style='width:10%;text-align:center;'>Airport Code</th>" +
                    "<th style='width:43%;text-align:center;'>Service Description</th>" +
                    "<th style='width:10%;text-align:center;'>Transfer Type</th>" +
                    "<th style='width:10%;text-align:center;'>Pax Number</th>" +
                    "<th style='width:6%;text-align:center;'>Price</th>" +
                    "<th style='width:6%;text-align:center;'>Currency</th>" +
                    "<th style='width:15%;text-align:center;'>Actions</th></tr>");
            $("#arrows").empty();
            $("#arrows").append("<a href='javascript:prev()'>&lt;</a>&nbsp;&nbsp; Page " + page_number + " out of " + total_pages +
                    " &nbsp;&nbsp;<a href='javascript:next()'>&gt;</a>&nbsp;&nbsp;");

            for (var i = 0; i < transfers.length; i++) {
                $("#transfer_info").append(
                        "<tr><td>" + transfers[i].t_airport_code + "</td><td>" + transfers[i].t_description +
                        "</td><td>" + transfers[i].t_type + "</td><td>" +
                        minMax(transfers[i].t_min_pax, transfers[i].t_max_pax) +
                        "</td><td>" + transfers[i].t_price + "</td><td>" + transfers[i].t_currency + "</td>" +
                        "<td><button data-toggle='modal' data-target='#editService' class='glyphicon glyphicon-pencil'" +
                        "style='margin-right:6%; height:25px;' onclick='editServiceDetails(\"" +
                        transfers[i].t_airport_code + "\", \"" + transfers[i].t_destination_code +
                        "\", \"" + transfers[i].t_description + "\", \"" + transfers[i].t_type + "\", \"" +
                        transfers[i].t_min_pax + "\", \"" + transfers[i].t_max_pax + "\", \"" +
                        transfers[i].t_price + "\", \"" + transfers[i].t_currency + "\", \"" +
                        transfers[i].t_id + "\")'> Edit</button>" +
                        "<button style='margin-right:6%; height:25px;' class='glyphicon glyphicon-trash' onclick='deleteConf(\"" + transfers[i].t_id + "\")'>" +
                        " Delete</button></td></tr>");
            }
        }
    });
}

// function that is triggered by clicking the 'search' button (or enter btn), returning result only where 
// airport/destination code match to the code that is in the search line
function searchCode() {
    // gets the value in the search line
    var search_code = $("#search_code").val();

    // if the search line is empty triggers again the main function of getPage with page number 1
    if (search_code == "") {
        page_number = 1;
        return getPage();
    }

    // ajax to get the result for table according to search
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_transfer_by_code", search_code: search_code},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (transfers) {
            transfers = JSON.parse(transfers);
            $("#transfer_info").empty();
            $("#transfer_info").append("<tr class='info'><th style='width:10%;text-align:center;'>Airport Code</th>" +
                    "<th style='width:43%;text-align:center;'>Service Description</th>" +
                    "<th style='width:10%;text-align:center;'>Transfer Type</th>" +
                    "<th style='width:10%;text-align:center;'>Pax Number</th>" +
                    "<th style='width:6%;text-align:center;'>Price</th>" +
                    "<th style='width:6%;text-align:center;'>Currency</th>" +
                    "<th style='width:15%;text-align:center;'>Actions</th></tr>");
            $("#arrows").empty();

            for (var i = 0; i < transfers.length; i++) {
                $("#transfer_info").append(
                        "<tr><td>" + transfers[i].t_airport_code + "</td><td>" + transfers[i].t_description +
                        "</td><td>" + transfers[i].t_type + "</td><td>" +
                        minMax(transfers[i].t_min_pax, transfers[i].t_max_pax) +
                        "</td><td>" + transfers[i].t_price + "</td><td>" + transfers[i].t_currency + "</td>" +
                        "<td><button data-toggle='modal' data-target='#editService' class='glyphicon glyphicon-pencil'" +
                        "style='margin-right:6%; height:25px;' onclick='editServiceDetails(\"" +
                        transfers[i].t_airport_code + "\", \"" + transfers[i].t_destination_code +
                        "\", \"" + transfers[i].t_description + "\", \"" + transfers[i].t_type + "\", \"" +
                        transfers[i].t_min_pax + "\", \"" + transfers[i].t_max_pax + "\", \"" +
                        transfers[i].t_price + "\", \"" + transfers[i].t_currency + "\", \"" +
                        transfers[i].t_id + "\")'> Edit</button>" +
                        "<button style='margin-right:6%; height:25px;' class='glyphicon glyphicon-trash'" +
                        "onclick='deleteConf(\"" + transfers[i].t_id + "\")'> Delete</button></td></tr>");
            }
        }
    });
}

// function that get by ajax the amount of pages according to limit per page in BusinessLogic (uses only the main getPage function)
function get_total_pages() {
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_total_pages"},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (total_pages) {
            window.total_pages = total_pages;
        }
    });
}

// 2 functions for prev & next arrows
function prev() {
    if (page_number != 1) {
        page_number--;
        getPage();
    }
}

function next() {
    if (page_number != total_pages) {
        page_number++;
        getPage();
    }
}

// this function is triggerd by clicking the 'edit' button on the table
// this function appending to the modal window (that is triggered by the same button) a form to update a service
// with all relevant data
function editServiceDetails(apCode, destCode, description, type, minPax, maxPax, price, currency, id) {
    $("#editServiceDetails").empty();
    $("#editServiceDetails").append(
            '<div><article>' +
            '<form method="POST" action="API.php" class="form-horizontal" id="updateServiceForm"><div class="form-group form-inline">' +
            '<label class="addServiceLabel" for="update_service_t_airport_code">Airport Code:</label>' +
            '<input id="update_service_t_airport_code" name="t_airport_code" type="text" class="form-control" style="width:10%" value="' + apCode + '">' +
            '<span id="update_span_t_airport_code" style="color:red"></span></div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_destination_code">Destination Code:</label>' +
            '<input id="update_service_t_destination_code" name="t_destination_code" type="text" class="form-control" style="width:10%" value="' + destCode + '">' +
            '<span id="update_span_t_destination_code" style="color:red"></span></div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_description">Service Description:</label>' +
            '<input id="update_service_t_description" name="t_description" type="text" class="form-control" style="width:40%" value="' + description + '">' +
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_type">Transfer Type:</label>' +
            '<select name="t_type" id="update_service_t_type" class="form-control"><option value="Private Service">Private</option>' +
            '<option value="Shared Shuttle">Shuttle</option></select></div><div class="form-group form-inline">' +
            '<label class="addServiceLabel" for="update_service_t_min_pax">Min Pax:</label>' +
            '<input id="update_service_t_min_pax" name="t_min_pax" class="form-control" type="text" style="width:10%" value="' + minPax + '">' +
            '<span id="update_span_t_min_pax" style="color:red"></span></div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_max_pax">Max Pax:</label>' +
            '<input id="update_service_t_max_pax" name="t_max_pax" type="text" class="form-control" style="width:10%" value="' + maxPax + '"><span id="update_span_t_max_pax" style="color:red"></span></div>' +
            '<div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_price">Price:</label>' +
            '<input id="update_service_t_price" name="t_price" type="text" class="form-control" style="width:10%" value="' + price + '">' +
            '<span id="update_span_t_price" style="color:red"></span></div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_currency">Currency:</label>' +
            '<select name="t_currency" id="update_service_t_currency" class="form-control"><option value="EUR">EUR</option>' +
            '<option value="USD">USD</option><option value="GBP">GBP</option></select>' +
            '</div></br></br><button type="button" class="btn btn-success" onclick="submitTransfer(\'update\')" style="margin-left: 40%">' +
            'Update</button><input type="hidden" name="command" value="update_transfer" />' +
            '<input type="hidden" name="t_id" value="' + id + '" /></form></article></div>');

    $("#update_service_t_type").val(type);
    $("#update_service_t_currency").val(currency);

    // alerting client for number only
    $("#update_service_t_min_pax, #update_service_t_max_pax, #update_service_t_price").on("blur", function () {
        var spanId = "update_span_" + $(this).attr("name");
        if (isNaN($(this).val())) {
            $("#" + spanId).empty();
            $("#" + spanId).append(" Must be a number");
        } else {
            $("#" + spanId).empty();
        }
    });

    // alerting client for max 3 letters
    $("#update_service_t_airport_code, #update_service_t_destination_code").on("blur", function () {
        var spanId = "update_span_" + $(this).attr("name");
        if ($(this).val().length > 3) {
            $("#" + spanId).empty();
            $("#" + spanId).append(" Up to 3 letters");
        } else {
            $("#" + spanId).empty();
        }
    });

}

// triggered by 'delete' button, confirms with user before delete & then send to API witch id service to delete
function deleteConf(id) {
    var conf = confirm('Are you sure you want to delete this service?');
    if (conf) {
        window.location = "API.php?command=delete_transfer&t_id=" + id;
    }

}

// JS validation function for the add/edit form - checks that there is no empty field
// and that ap/dest code are up to 3 letters and number fields are numbers
function submitTransfer(status) {
    var transferFormValid = true;
    if (status == "add") {
        var service_form_data = $("#serviceForm").serializeArray();
        if (isNaN($("#service_t_min_pax").val()) || isNaN($("#service_t_max_pax").val()) || isNaN($("#service_t_price").val()) 
                || $("#service_t_airport_code").val().length > 3 || $("#service_t_destination_code").val().length > 3) {
            transferFormValid = false;
        }
    } else if (status == "update") {
        var service_form_data = $("#updateServiceForm").serializeArray();
        if (isNaN($("#update_service_t_min_pax").val()) || isNaN($("#update_service_t_max_pax").val()) ||
                isNaN($("#update_service_t_price").val()) || $("#update_service_t_airport_code").val().length > 3 || $("#update_service_t_airport_code").val().length > 3) {
            transferFormValid = false;
        }
    }
    for (var input in service_form_data) {
        if (status == "add") {
            var element = $("#service_" + service_form_data[input]['name']);
        } else if (status == "update") {
            var element = $("#update_service_" + service_form_data[input]['name']);
        }
        if (element.val() === "") {
            transferFormValid = false;
            element.parent().addClass("has-error");
            function elementValidation(e) {
                return function () {
                    if (e.val() !== "") {
                        e.parent().removeClass("has-error");
                    }
                };
            }
            element.on("change or keyup", elementValidation(element));
        }
    }



    if (transferFormValid) {
        if (status == "add") {
            $("#serviceForm").submit();
        } else if (status == "update") {
            sessionStorage.lastSearchCode = $("#search_code").val();
            $("#updateServiceForm").submit();
        }
    }
}

// triggered onload - if a service was updated so the search code stored into sessiom storage and the page return on refresh to same search code results
function lastSearchCode() {
    var lastSearchCode = sessionStorage.lastSearchCode;
    if(lastSearchCode) {
        $("#search_code").val(lastSearchCode);
        searchCode();
        sessionStorage.lastSearchCode = "" ;
    } else {
        getPage();
    }
}

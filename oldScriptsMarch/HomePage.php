<?php

include_once 'includes/global.php';
include 'includes/header.php';

if (!isset($_SESSION['auth'])) {
    redirect('logIn.php');
}
?>

<script src="jquery-1.11.3.js"></script>

<!-- this next js file and css file are for the date picker widget, i downloaded all the jquery-ui-1.11.4.custom for this -->
<script src="jquery-ui-1.11.4.custom/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="jquery-ui-1.11.4.custom/jquery-ui.css">

<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
<!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
<script>


    // for the the arrows in the general result (with no search code) 

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

    // on load triggers the getPage function that get general limited results for the main table

    $(function () {
        get_total_pages();
        getPage();

        // on click on the search button/enter key up triggers function searchCode that switch the results on 
        // the main table according to airport/destination code - no limit there, display all the results

        $("#searchBtn").on("click", searchCode);
        $("#search_code").keyup(function (e) {
            if (e.which == 13) {
                searchCode();
            }
        });

        // t_quantity is a select box of quantity of transfer (one way/round trip)
        // t_quantity function is the function that responsible to show the price and number of segments
        // according to quantity of transfer

        $("#t_quantity").change(t_quantity);

        /*$(":input").on("blur", function () {
         alert($(this).val()); 
         if (event.val()===""){
         alert("hi");
         }
         });*/


    });

    // get all the results from transfers DB to main table but limited, page number uses for offset
    // this function is trigered 'onload' and on empty search code

    function getPage() {

        // return a number that is the total of pages accordind to the result limit in the BusinessLogic

        get_total_pages();

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
                $("#transfer_info").append("<tr class='info'><th style='width:10%;text-align:center;'>Airport Code</th>" +
                        "<th style='width:43%;text-align:center;'>Service Description</th>" +
                        "<th style='width:10%;text-align:center;'>Transfer Type</th>" +
                        "<th style='width:10%;text-align:center;'>Pax Number</th>" +
                        "<th style='width:6%;text-align:center;'>Price</th>" +
                        "<th style='width:6%;text-align:center;'>Currency</th>" +
                        "<th style='width:15%;text-align:center;'>Book</th></tr>");
                $("#arrows").empty();
                $("#arrows").append("<a href='javascript:prev()'>&lt;</a>&nbsp;&nbsp; Page " + page_number + " out of " + total_pages +
                        " &nbsp;&nbsp;<a href='javascript:next()'>&gt;</a>&nbsp;&nbsp;");

                for (var i = 0; i < transfers.length; i++) {
                    $("#transfer_info").append(
                            "<tr><td>" + transfers[i].t_airport_code + "</td><td>" + transfers[i].t_description +
                            "</td><td>" + transfers[i].t_type + "</td><td>" +
                            minMax(transfers[i].t_min_pax, transfers[i].t_max_pax) +
                            "</td><td>" + transfers[i].t_price + "</td><td>" + transfers[i].t_currency + "</td><td>" +
                            "<button type='button' onclick='book_btn(this)' airport_code='" + transfers[i].t_airport_code +
                            "' service_type='" + transfers[i].t_type + "' max_pax='" + transfers[i].t_max_pax + "' " +
                            "min_pax='" + transfers[i].t_min_pax + "' service_description='" + transfers[i].t_description +
                            "' pricePerWay='" + transfers[i].t_price + "' currency='" + transfers[i].t_currency + "' " +
                            "class='btn btn-primary btn-xs' data-toggle='modal'" +
                            "data-target='#booking_form'> Book</button></td></tr>");
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

        // ajax call to get the result for table according to search

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
                        "<th style='width:15%;text-align:center;'>Book</th></tr>");
                $("#arrows").empty();

                for (var i = 0; i < transfers.length; i++) {
                    $("#transfer_info").append(
                            "<tr><td>" + transfers[i].t_airport_code + "</td><td>" + transfers[i].t_description +
                            "</td><td>" + transfers[i].t_type + "</td><td>" +
                            minMax(transfers[i].t_min_pax, transfers[i].t_max_pax) +
                            "</td><td>" + transfers[i].t_price + "</td><td>" + transfers[i].t_currency + "</td><td>" +
                            "<button type='button' onclick='book_btn(this)' airport_code='" + transfers[i].t_airport_code +
                            "' service_type='" + transfers[i].t_type + "' max_pax='" + transfers[i].t_max_pax + "' " +
                            "min_pax='" + transfers[i].t_min_pax + "' service_description='" + transfers[i].t_description +
                            "' pricePerWay='" + transfers[i].t_price + "' currency='" + transfers[i].t_currency + "' " +
                            "class='btn btn-primary btn-xs' data-toggle='modal'" +
                            "data-target='#booking_form'> Book</button></td></tr>");
                }
            }
        });
    }

    // function that get by ajax the amount of pages according to limit per page (uses only the main getPage function)

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

    // this function is triggerd by clicking the 'book' button on the table
    // all relevant data that returns from DB is stored into the 'book' button attributes
    // this function save this data into window variables that are used for the booking process
    // also appending service description and select box of number of passengers for private service into the booking form

    function book_btn(btn) {
        window.airport_code = $(btn).attr("airport_code");
        window.service_type = $(btn).attr("service_type");
        window.min_pax = $(btn).attr("min_pax");
        window.max_pax = $(btn).attr("max_pax");
        window.service_description = $(btn).attr("service_description");
        window.pricePerWay = $(btn).attr("pricePerWay");
        window.currency = $(btn).attr("currency");
        t_quantity();
        $("#service_description").empty();
        $("#service_description").append(
                'Description: ' + service_description
                );
        $("#infoToServer").empty();
        $("#infoToServer").append(
                '<input type="hidden" name="currency" value="' + currency + '"/>' +
                '<input type="hidden" name="min_pax" value="' + min_pax + '"/>' +
                '<input type="hidden" name="max_pax" value="' + max_pax + '"/>' +
                '<input type="hidden" name="service_description" value="' + service_description + '"/>' +
                '<input type="hidden" name="airport_code" value="' + airport_code + '"/>' +
                '<input type="hidden" name="service_type" value="' + service_type + '"/>' +
                '<input type="hidden" name="pricePerWay" value="' + pricePerWay + '"/>'
                );

        $("#parent_booking_paxNum").empty();
        if (service_type == "Private Service") {
            $("#parent_booking_paxNum").append(
                    '&nbsp;&nbsp;<label for="booking_paxNum">Number Of Passengers: &nbsp;</label>' +
                    '<select id="booking_paxNum" name="paxNum" class = "form-control"><option></option></select>');

            for (i = min_pax; i <= max_pax; ++i) {
                $("#booking_paxNum").append(
                        '<option value="' + i + '">' + i + '</option>'
                        );
            }
        }
    }

    // this function is triggered by book_btn func and by change in the t_quantity select box that checks if its one way 
    // service (arrival or departure) or 2 way service and adjusting the booking form accordingly 
    // (the 'transfer details' lines and the price)

    function t_quantity() {
        var table_Header = '<tr style="background-color:#e7edee;"><th style="text-align:center;">Date</th><th></th><th>From</th><th></th><th>To</th></tr>';
        var arrival = '<tr><div class="form-inline"><div class="form-group">' +
                '<td id="parent_booking_arrDate"><input id="booking_arrDate" type="text" name="arrDate" class="form-control"></td></div><div class="form-group">' +
                '<td style="vertical-align:middle;"><label for="booking_arrFlightNumber">' + window.airport_code + ' Airport</label></td><td id="parent_booking_arrFlightNumber"><input id="booking_arrFlightNumber" name="arrFlightNumber" type = "text" class = "form-control" placeholder="Arrival Flight Number"></td></div>' +
                '<div class = "form-group"><td style="vertical-align:middle;"><label>Drop-Off:</label></td>' +
                '<td id="parent_booking_arrDropOff"><input id="booking_arrDropOff" type = "text" name="arrDropOff" class = "form-control" placeholder="Hotel/Address"></td></div></div></tr>';
        var departure = '<tr><div class="form-inline"><div class="form-group">' +
                '<td id="parent_booking_depDate"><input id="booking_depDate" type="text" name="depDate" class="form-control"></td></div><div class = "form-group"><td style="vertical-align:middle;"><label>Pick-Up:</label></td>' +
                '<td id="parent_booking_depPickUp"><input id="booking_depPickUp" type = "text" name="depPickUp" class = "form-control" placeholder="Hotel/Address"></td></div><div class="form-group">' +
                '<td style="vertical-align:middle;"><label>' + window.airport_code + ' Airport</label></td><td id="parent_booking_depFlightNumber"><input id="booking_depFlightNumber" type = "text" name="depFlightNumber" class = "form-control" placeholder="Departure Flight Number"></td></div>' +
                '</div></tr>';
        $("#total_cost").empty();
        $("#details_rows").empty();
        $("#details_rows").append(table_Header);
        if ($("#t_quantity").val() == "ona") {
            $("#details_rows").append(arrival);
            $("#details_rows").append('<input type="hidden" name="t_quantity" value="One Way(Arrival)"/>');
            $("#total_cost").append(
                    '<b>Total Cost: ' + pricePerWay + ' ' + currency + '</b>'
                    );
        }
        if ($("#t_quantity").val() == "rt") {
            $("#details_rows").append(arrival);
            $("#details_rows").append(departure);
            $("#details_rows").append('<input type="hidden" name="t_quantity" value="Round Trip"/>');
            $("#total_cost").append(
                    '<b>Total Cost: ' + pricePerWay * 2 + ' ' + currency + '</b>'
                    );
        }
        if ($("#t_quantity").val() == "ond") {
            $("#details_rows").append(departure);
            $("#details_rows").append('<input type="hidden" name="t_quantity" value="One Way(Departure)"/>');
            $("#total_cost").append(
                    '<b>Total Cost: ' + pricePerWay + ' ' + currency + '</b>'
                    );
        }

        $("#booking_arrDate").datepicker({dateFormat: 'dd/M/yy'});
        $("#booking_depDate").datepicker({dateFormat: 'dd/M/yy'});
    }

    function submitBookingForm() {
        var formValid = true;
        var form_data = $("#bookingForm").serializeArray();
        for (var input in form_data) {
            var element = $("#booking_" + form_data[input]['name']);
            //var element_parent = $("#parent_booking_" + form_data[input]['name']);
            if (element.val() === "") {
                formValid = false;
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
        if (formValid) {
            $("#bookingForm").submit();
        }
    }



</script>
<link rel="stylesheet" type="text/css" href="style/HomePageStyle.css">
<a href="API.php?command=logout"  id="logout">Logout</a>
<?php 
if(isset($_SESSION['admin'])) {
echo '<a href="adminPanel.php" id="login" class="glyphicon glyphicon-cog"></a>';
}else {
    echo '<a id="login"></a>';
} ?>
<div id="booking_form" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Service Details</h4>
                <br>
                <div id="total_cost"></div>
                <div id="quantity_select">
                    <select id="t_quantity">
                        <option value="ona" selected="selected">One Way(Arrival)</option>
                        <option value="rt">Round Trip</option>
                        <option value="ond">One Way(Departure)</option>
                    </select>
                </div>

            </div>
            <div class="modal-body">
                <div id="transfer_details">
                    <form method="POST" action="bookingDetails.php" id="bookingForm">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">Transfer Details</h5>
                            </div>
                            <div class="panel-body">
                                <div id="service_description">
                                </div>
                                <br>
                                <table class="table borderless" id="details_rows">
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">Passenger's Details</h5>
                            </div>
                            <div class="panel-body">
                                <div class="form-inline">
                                    <div id="parent_booking_firstName" class="form-group">
                                        <label for="booking_firstName">Lead Passenger: &nbsp;</label>
                                        <input id="booking_firstName" name="firstName" type="text" class="form-control" placeholder="First Name">
                                    </div>
                                    <div id="parent_booking_lastName" class="form-group">
                                        <label for="booking_lastName">&nbsp;</label>
                                        <input id="booking_lastName" name="lastName" type="text" class="form-control" placeholder="Last Name">
                                    </div>
                                    <div class="form-group" id="parent_booking_paxNum">
                                    </div>
                                </div>
                                <br>
                                <div class="form-inline">
                                    <div id="parent_booking_paxPhone" class="form-group">
                                        <label for="booking_paxPhone">Passenger's Phone: &nbsp;</label>
                                        <input id="booking_paxPhone" name="paxPhone" type="text" class="form-control" placeholder="Phone Number">
                                        <!--<span class="help-block">This field is required</span>-->
                                    </div>
                                    <div class="form-group required">
                                        <label for="remark">&nbsp; Remark: &nbsp;</label>
                                        <input id="remark" name="remark" type="text" class="form-control" required="required">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">Agent Details</h5>
                            </div>
                            <div class="panel-body">
                                <div class="form-inline">
                                    <div id="parent_booking_agencyName" class="form-group">
                                        <label for="booking_agencyName">Agency Name: &nbsp;</label>
                                        <input id="booking_agencyName"  type="text" name="agencyName" class="form-control" placeholder="Agency Name" value="<?= $_SESSION['user_det']->u_agency ?>" disabled>
                                    </div>
                                    <div id="parent_booking_agentName" class="form-group">
                                        <label for="booking_agentName">&nbsp; Agent Name: &nbsp;</label>
                                        <input type="text" id="booking_agentName" name="agentName" class="form-control" placeholder="Your Name" value="<?= $_SESSION['user_det']->u_agent_name ?>" disabled>
                                    </div>
                                </div>
                                <br>
                                <div class="form-inline">
                                    <div id="parent_booking_agentEmail" class="form-group">
                                        <label for="booking_agentEmail">Agent Email: &nbsp;</label>
                                        <input type="text" id="booking_agentEmail" name="agentEmail" class="form-control" placeholder="Your Email" value="<?= $_SESSION['user_det']->u_email ?>">
                                    </div>
                                    <div id="parent_booking_agentPhone" class="form-group">
                                        <label for="booking_agentPhone">&nbsp; Agent Phone Number: &nbsp;</label>
                                        <input type="text" id="booking_agentPhone"  name="agentPhone" class="form-control" placeholder="Your Phone Number" value="<?= $_SESSION['user_det']->u_phone ?>" disabled>
                                    </div>
                                    <div id="infoToServer">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>

                        </div>
                    </form> 
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" style="float:left;" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" style="float:right;" onclick="submitBookingForm()">Book</button>
            </div>
        </div>

    </div>
</div>

<div class="search-bar form-inline">
    <label>Search:</label>
    <input type="text" name="search" class="form-control" id="search_code">
    <button  class="btn btn-info" id="searchBtn">
        Search
    </button> 
</div>
</br>
<div class="arrows" id="arrows"></div>

<div class="transfer_table">        
    <table  class="table table-hover table-bordered" id="transfer_info"></table>
</div>


<?php

include_once 'includes/footer.php';

<?php
/* * * this page recieves the booking form from index.php, get relevant data into php variables, sends email with 
  booking summary to user + admin & get the reservation into DB  ** */
require_once 'includes/mailCon.php';
include_once 'includes/global.php';
require_once 'BusinessLogic.php';

// get the data from form into php variables
$t_quantity = $_POST["t_quantity"];
$pricePerWay = $_POST["pricePerWay"];
$currency = $_POST["currency"];
$apCode = $_POST["airport_code"];
$service_description = $_POST["service_description"];
$service_type = $_POST["service_type"];
$min_pax = $_POST["min_pax"];
$max_pax = $_POST["max_pax"];
$firstName = $_POST["firstName"];
$lastName = $_POST["lastName"];
$paxPhone = $_POST["paxPhone"];
$remark = $_POST["remark"];
$agencyName = $_SESSION['user_det']->u_agency;
$agentName = $_SESSION['user_det']->u_agent_name;
$agentEmail = $_POST["agentEmail"];
$agentPhone = $_SESSION['user_det']->u_phone;
$arrDate = null;
$arrFlightNumber = null;
$arrDropOff = null;
$depDate = null;
$depFlightNumber = null;
$depPickUp = null;

// there are some different fields for arrival only / round trip / departure only service ($t_quantity)
// check the quantity of service and recieving the data into variables accordingly
if ($t_quantity == "One Way(Arrival)") {
    $arrDate = $_POST["arrDate"];
    $arrFlightNumber = $_POST["arrFlightNumber"];
    $arrDropOff = $_POST["arrDropOff"];
    $totalCost = $pricePerWay;
    $service_details = "Arrival Date: " . $arrDate . "<br>" .
            "Pick-Up: " . $apCode . " Airport - Flight: " . $arrFlightNumber . "<br>" .
            "Drop-Off: " . $arrDropOff;
} else if ($t_quantity == "Round Trip") {
    $arrDate = $_POST["arrDate"];
    $arrFlightNumber = $_POST["arrFlightNumber"];
    $arrDropOff = $_POST["arrDropOff"];
    $depDate = $_POST["depDate"];
    $depFlightNumber = $_POST["depFlightNumber"];
    $depPickUp = $_POST["depPickUp"];
    $totalCost = $pricePerWay * 2;
    $service_details = "Arrival Date: " . $arrDate . "<br>" .
            "Pick-Up: " . $apCode . " Airport - Flight: " . $arrFlightNumber . "<br>" .
            "Drop-Off: " . $arrDropOff . "<br><br>" .
            "Departure Date: " . $depDate . "<br>" .
            "Pick-Up: " . $depPickUp . "<br>" .
            "Drop-Off: " . $apCode . " Airport - Flight: " . $depFlightNumber;
} else if ($t_quantity == "One Way(Departure)") {
    $depDate = $_POST["depDate"];
    $depFlightNumber = $_POST["depFlightNumber"];
    $depPickUp = $_POST["depPickUp"];
    $totalCost = $pricePerWay;
    $service_details = "Departure Date: " . $depDate . "<br>" .
            "Pick-Up: " . $depPickUp . "<br>" .
            "Drop-Off: " . $apCode . " Airport - Flight: " . $depFlightNumber;
}

// check the type of service and adjust the pax number for description, because shared shuttle is always fixed number 
// of passengers and in private service the capicity is range (for example 1-3 passengers)
if ($service_type == "Private Service") {
    $paxNum = $_POST["paxNum"];
    $service_type_pax_num = $min_pax . "-" . $max_pax;
} else {
    $paxNum = $max_pax;
    $service_type_pax_num = $max_pax;
}

// bla
if(isset($_SESSION['sabre_user'])) {
    $sabre_user = 1;
} else {
    $sabre_user = 0;
}

$r_pax_name = $firstName . " " . $lastName;
// insert reservation data into DB
$booking_id = insert_reservation($t_quantity, $service_description, $service_type, $totalCost, $currency, $apCode, $arrDate, $arrFlightNumber, $arrDropOff, $depDate, $depFlightNumber, $depPickUp, $r_pax_name, $paxNum, $paxPhone, $remark, $agencyName, $agentName, $_SESSION['user_det']->u_id, $agentEmail, $agentPhone, $min_pax, $max_pax, $sabre_user);

$subject = "Cityride Booking Request No. " . $booking_id;

$body = "<html><body style='font-size: 16px;padding-right: 0px;padding-left: 0px;font-family: Arial, serif;'>" .
        "<div style='margin-left: 8px;margin-right: 8px;line-height: 125%;'>Dear " . $agentName . ",<br><br>" .
        "Your reservation has been received, confirmation email with travel voucher will follow shortly, " .
        "after your order details will be verified.<br><br>" .
        "Reservation Details:<br><br>" .
        "<b>Service Type:</b> " . $service_type . " for " . $service_type_pax_num . " Passengers<br><br>" .
        "<b>Service Description:</b> " . $service_description . "<br><br>" .
        "<b>Service Details:</b><br><br>" .
        $t_quantity . "<br><br>" . $service_details . "<br><br>" .
        "Remark: " . $remark . "<br><br>" .
        "Total Cost: " . $totalCost . " " . $currency . "<br><br>" .
        "<b>Passenger Details:</b><br><br>" .
        "Lead Passenger Name: " . $firstName . " " . $lastName . "<br>" .
        "Passenger Phone Number: " . $paxPhone . "<br>" .
        "Number Of Passengers: " . $paxNum . "<br><br>" .
        "<b>Agent Details:</b><br><br>" .
        "Booked By: " . $agentName . "<br>" .
        "Agency: " . $agencyName . "<br>" .
        "Agent Phone Number: " . $agentPhone . "<br><br><br>" .
        "<img style='float:left;padding-bottom:20px;' src='http://www.bookcityride.co.il/includes/logo.png' /><h3 style='padding-top:30px;'>Thank you for booking Cityride!</h3></div></body></html>";
        // "<img style='float:left;padding-bottom:20px;' src='https://fbcdn-sphotos-a-a.akamaihd.net/hphotos-ak-xft1/v/t1.0-9/12391967_10153365471462075_2635328375152435737_n.jpg?oh=675fb45d70245424f8719e63948a5cba&oe=571A0251&__gda__=1460369059_b642689b3da0c4e29df207a26933562d' /><h3 style='padding-top:30px;'>Thank you for booking Cityride!</h3></div></body></html>";

// send email to user & admin usung PHPMailer library
smtpmailer($agentEmail, "info@cityride.co.il", "info@cityride.co.il", "Cityride", $subject, $body);

/*$r_pax_name = $firstName . " " . $lastName;
// insert reservation data into DB
insert_reservation($t_quantity, $service_description, $service_type, $totalCost, $currency, $apCode, $arrDate, $arrFlightNumber, $arrDropOff, $depDate, $depFlightNumber, $depPickUp, $r_pax_name, $paxNum, $paxPhone, $remark, $agencyName, $agentName, $_SESSION['user_det']->u_id, $agentEmail, $agentPhone, $min_pax, $max_pax); */

// at last - html thank you page, after a few seconds redirect to index.php
?>

<!DOCTYPE html>
<html>
    <head>
        <link href="media/css/bootstrap.min.css" rel="stylesheet" />
        <meta http-equiv="refresh" content="7; url=index.php" />

    </head>
    <body>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h1>Thank you for booking with cityride!</h1>
            </div>
            <div class="panel-body">
                <h3>Email with your booking details has been sent to your email address and confirmation email will be follow shortly.</h3>
            </div>

        </div>
    </body>
</html>
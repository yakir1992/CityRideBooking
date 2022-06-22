<?php

include_once 'includes/global.php';
include 'includes/header.php';

// check if authorized user is loged in, if not redirects to log-in page
if (!isset($_SESSION['auth'])) {
    redirect('logIn.php');
}
?>

<!-- this 2 next js file and css file are for the date picker widget, i downloaded all the jquery-ui-1.11.4.custom for this -->
<script src="jquery-ui-1.11.4.custom/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="jquery-ui-1.11.4.custom/jquery-ui.css">

<script src="JS/HomePageScript.js"></script>
<link rel="stylesheet" type="text/css" href="style/HomePageStyle.css">

<a href="API.php?command=logout"  id="logout">Logout</a>


<?php 

// if admin user is loged-in so shows a link to admin panel
if(isset($_SESSION['admin'])) {
echo '<a style="float: left" href="adminPanel.php" id="login" class="glyphicon glyphicon-cog"></a>';
}else {
    echo '<a id="login"></a>';
} ?>

<div style="text-align: center;">
<img src='http://www.bookcityride.co.il/includes/logo.png' />
</div>

<!-- bootstrap modal for booking form, triggered by book button on main table -->
<div id="booking_form" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content -->
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
                    <!-- bookingDetails.php page responsible of getting all relevant values, sending email (with phpMailer 
                    library)to client + admin and store the booking into reservations DB  -->
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
    <input type="text" name="search" class="form-control" id="search_code" placeholder="Airport / Destination Code">
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

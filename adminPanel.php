<?php

include 'includes/header.php';
require_once 'BusinessLogic.php';
require_once 'DAL.php';
include_once 'includes/global.php';

// check the session for admin, only admin allowed into 'adminPanel'
if (!isset($_SESSION['admin'])) {
    redirect('index.php');
    die();
}
?>

<script src="JS/adminPanelServScript.js"></script>
<script src="JS/adminPanelUsersScript.js"></script>
<script src="JS/adminPanelResScript.js"></script>

<link rel="stylesheet" type="text/css" href="style/adminPanelStyle.css">

<a href="API.php?command=logout" style="float:right" id="logout">Logout</a>
<a href="index.php" id="login" class="glyphicon glyphicon-home"></a>

<div class="adminPanel">
    <ul class="nav nav-tabs" id="adminTabs">
        <li class="active"><a data-toggle="tab" href="#reservation">Reservations</a></li>
        <li><a data-toggle="tab" href="#services">Services</a></li>
        <li><a data-toggle="tab" href="#users">Users</a></li>
    </ul>

    <div class="tab-content">

        <div id="reservation" class="tab-pane fade in active">
            <div class="search-bar form-inline">
                <label>Search:</label>
                <input type="text" name="search" class="form-control" id="res_search" placeholder="ID, Pax Name, Agent">
                <button  class="btn btn-info" id="res_searchBtn">
                    Search
                </button> 
            </div>
            </br>
            <div class="arrows" id="res_arrows"></div>

            <div class="transfer_table">        
                <table class="table table-hover table-bordered" id="res_info"></table>
            </div>

            <div id="viewRes" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">

                    <!-- Modal content-->
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title" id="resHead" style="text-align: center"></h3>
                        </div>
                        <div class="modal-body" id="editViewRes">
                            Reservation
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div id="services" class="tab-pane fade">
            <button data-toggle='modal' data-target='#addServiceModal' class="btn btn-success">Add Service</button>
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

            <div id="editService" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">

                    <!-- Modal content-->
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Edit Transfer</h3>
                        </div>
                        <div class="modal-body" id="editServiceDetails">

                        </div>
                    </div>
                </div>
            </div>

            <div id="addServiceModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">

                    <!-- Modal content-->
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Add New Transfer</h3>
                        </div>
                        <div class="modal-body" id="editServiceDetails">
                            <div>
                                <article>
                                    <form method="POST" action="API.php" class="form-horizontal" id="serviceForm">
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_airport_code">Airport Code:</label>
                                            <input id="service_t_airport_code" name="t_airport_code" type="text" class="form-control" style="width:12%" />
                                            <span id="span_t_airport_code" style="color:red"></span>
                                        </div>
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_destination_code">Destination Code:</label>
                                            <input id="service_t_destination_code" name="t_destination_code" type="text" class="form-control" style="width:12%" />
                                            <span id="span_t_destination_code" style="color:red"></span>
                                        </div>
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_description">Service Description:</label>
                                            <input id="service_t_description" name="t_description" type="text" class="form-control" style="width:46%" />
                                        </div>
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_type">Transfer Type:</label>
                                            <select id="service_t_type" name="t_type" class="form-control">
                                                <option value="Private Service">Private</option>
                                                <option value="Shared Shuttle">Shuttle</option>
                                            </select>
                                        </div>
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_min_pax">Min Pax:</label>
                                            <input id="service_t_min_pax" name="t_min_pax" type="text" class="form-control" style="width:12%" />
                                            <span id="span_t_min_pax" style="color:red"></span>
                                        </div>
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_max_pax">Max Pax:</label>
                                            <input id="service_t_max_pax" name="t_max_pax" type="text" class="form-control" style="width:12%" />
                                            <span id="span_t_max_pax" style="color:red"></span>
                                        </div>
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_price">Price:</label>
                                            <input id="service_t_price" name="t_price" type="text" class="form-control" style="width:12%" />
                                            <span id="span_t_price" style="color:red"></span>
                                        </div>
                                        <div class="form-group form-inline">
                                            <label class="addServiceLabel" for="service_t_currency">Currency:</label>
                                            <select id="service_t_currency" name="t_currency" class="form-control">
                                                <option value="EUR" selected>EUR</option>
                                                <option value="USD">USD</option>
                                                <option value="GBP">GBP</option>
                                            </select>
                                        </div>
                                        </br></br>
                                        <button type='button' class='btn btn-success addServiceBtn' style='margin-left: 30%' onclick="submitTransfer('add')">Add</button>
                                        <input type='hidden' name='command' value='add_transfer' />
                                    </form>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div id="users" class="tab-pane fade">

            <button data-toggle='modal' data-target='#addUser' class="btn btn-success">Add User</button> 
            <div class="search-bar form-inline">
                <label>Search:</label>
                <input type="text" name="search" class="form-control" id="user_search" placeholder="Agent or Agency">
                <button  class="btn btn-info" id="user_searchBtn">
                    Search
                </button> 
            </div>
            </br>
            <div class="arrows" id="user_arrows"></div>

            <div class="transfer_table">        
                <table  class="table table-hover table-bordered" id="user_info"></table>
            </div>

            <div id="addUser" class="modal fade" role="dialog">


                <div class="modal-dialog modal-lg">

                    <!-- Modal content-->
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Add User</h3>
                        </div>
                        <div class="modal-body">
                            <article>
                                <form method="POST" action="API.php" class="form-horizontal" id="userForm">
                                    <div class="form-group form-inline">
                                        <label class="addServiceLabel" for="user_u_agent_name">Agent Name:</label>
                                        <input id="user_u_agent_name" name="u_agent_name" type="text" class="form-control" style="width:20%" />
                                    </div>
                                    <div class="form-group form-inline">
                                        <label class="addServiceLabel" for="user_u_agency">Agency:</label>
                                        <input id="user_u_agency" name="u_agency" type="text" class="form-control" style="width:20%" />
                                    </div>
                                    <div class="form-group form-inline">
                                        <label class="addServiceLabel" for="user_u_phone">Agent Phone:</label>
                                        <input id="user_u_phone" name="u_phone" type="text" class="form-control" style="width:20%" />
                                    </div>
                                    <div class="form-group form-inline">
                                        <label class="addServiceLabel" for="user_u_email">Agent Email:</label>
                                        <input id="user_u_email" name="u_email" type="text" class="form-control" style="width:20%" />
                                        <span class="span_u_email" isValid="yes" style="color:red"></span>
                                    </div>
                                    <div class="form-group form-inline">
                                        <label class="addServiceLabel" for="user_u_password">Password:</label>
                                        <input id="user_u_password" name="u_password" type="text" class="form-control" style="width:20%" />
                                    </div>
                                    </br></br>
                                    <button type="button" class="btn btn-success" style="margin-left: 30%" onclick="submitUser('add')">Add</button>
                                    <input type="hidden" name="command" value="add_user" />
                                </form>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
            <div id="editUser" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">

                    <!-- Modal content-->
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Edit User</h3>
                        </div>
                        <div class="modal-body" id="editUserDetails">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php

include_once 'includes/footer.php';

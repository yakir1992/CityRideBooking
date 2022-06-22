<?php
include 'includes/header.php';
include_once 'includes/global.php';

if(!isset($_SESSION['auth'])) {
   redirect('HomePage.php');
}
?>

<script src="jquery-1.11.3.js"></script>
<script>

    // for the the arrows in the general result (with no code) 
    var page_number = 1;
    var total_pages;

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

        // on click on the search button triggers function searchCode that switch the results on 
        // the main table according to airport/destination code
        $("#searchBtn").on("click", searchCode);


        $("#search_code").keyup(function (e) {
            if (e.which == 13) {
                searchCode();
            }
        });

    });

    // get all the results from transfers DB to main table but limited, page number uses for offset
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
                        "<th style='width:15%;text-align:center;'>Actions</th></tr>");
                $("#arrows").empty();
                $("#arrows").append("<a href='javascript:prev()'>&lt;</a>&nbsp;&nbsp; Page " + page_number + " out of " + total_pages +
                        " &nbsp;&nbsp;<a href='javascript:next()'>&gt;</a>&nbsp;&nbsp;")

                for (var i = 0; i < transfers.length; i++) {
                    $("#transfer_info").append(
                            "<tr><td>" + transfers[i].t_airport_code + "</td><td>" + transfers[i].t_description +
                            "</td><td>" + transfers[i].t_type + "</td><td>" +
                            minMax(transfers[i].t_min_pax, transfers[i].t_max_pax) +
                            "</td><td>" + transfers[i].t_price + "</td><td>" + transfers[i].t_currency + "</td>" +
                            "<td><a href='setTransfer.php?t_id=" + transfers[i].t_id + "' class='glyphicon glyphicon-pencil' style='padding-right:20%;'> Edit</a>" +
                            "<a href='API.php?command=delete_transfer&t_id=" + transfers[i].t_id + "' class='glyphicon glyphicon-trash'> Delete</a></td></tr>");
                }
            }
        });
    }

    // function that is triggered by clicking the 'search' button, returning result only where 
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
                            "<td><a href='setTransfer.php?t_id=" + transfers[i].t_id + "' class='glyphicon glyphicon-pencil' style='padding-right:20%;'> Edit</a>" +
                            "<a href='API.php?command=delete_transfer&t_id=" + transfers[i].t_id + "' class='glyphicon glyphicon-trash'> Delete</a></td></tr>");
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

    // 2 functions foe prev & next arrows
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




</script>

<style>
    .transfer_table {
        margin-left: 8%;
        margin-right: 8%;
        margin-top: 2%;
        text-align: center;
    }

    .search-bar {
        margin-top: 1%;
        text-align:center;
    }
    .arrows {
        text-align:center;
        font-size: 115%;
        height: 20px;
    }

    #setTransfer {
        margin-top: 3%;
        margin-left: 8%;
    }
    a {
        color: black;
    }
    a:hover {
        color: #ffa838;
    }
    #logout {
        margin-top: 5px;
        margin-right: 7px;
        color: blue;
    }

</style>
<a href="API.php?command=logout" style="float:right" id="logout">Logout</a>
<a href="setTransfer.php" class="btn btn-success" id="setTransfer">Add Service</a>
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

<!--<i class="fa fa-pencil-square-o"></i>
<a href="setCustomer.php?c_id=<?= $customer['c_id'] ?>">Edit</a>
<i class="fa fa-trash-o"></i>
<a href="delcustomer.php?c_id=<?= $customer['c_id'] ?>">Delete</a>-->

<?php
include_once 'includes/footer.php';

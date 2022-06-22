// for the the arrows in the general result (with no code) 
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
        //get_total_pages();
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

    /* // get all the results from transfers DB to main table but limited, page number uses for offset
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
    }*/










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

function editServiceDetails(apCode, destCode, description, type, minPax, maxPax, price, currency, id) {
    $("#editServiceDetails").empty();
    $("#editServiceDetails").append(
            '<div><article>' +
            '<form method="POST" action="API.php" class="form-horizontal" id="updateServiceForm"><div class="form-group form-inline">' + 
            '<label class="addServiceLabel" for="update_service_t_airport_code">Airport Code:</label>' + 
            '<input id="update_service_t_airport_code" name="t_airport_code" type="text" class="form-control" style="width:10%" value="' + apCode + '">' + 
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_destination_code">Destination Code:</label>' +
            '<input id="update_service_t_destination_code" name="t_destination_code" type="text" class="form-control" style="width:10%" value="' + destCode  + '">' +
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_description">Service Description:</label>' + 
            '<input id="update_service_t_description" name="t_description" type="text" class="form-control" style="width:40%" value="' + description + '">' + 
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_type">Transfer Type:</label>' + 
            '<select name="t_type" id="update_service_t_type" class="form-control"><option value="Private Service">Private</option>' + 
            '<option value="Shared Shuttle">Shuttle</option></select></div><div class="form-group form-inline">' + 
            '<label class="addServiceLabel" for="update_service_t_min_pax">Min Pax:</label>' + 
            '<input id="update_service_t_min_pax" name="t_min_pax" class="form-control" type="text" style="width:10%" value="' + minPax + '">' + 
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_max_pax">Max Pax:</label>' + 
            '<input id="update_service_t_max_pax" name="t_max_pax" type="text" class="form-control" style="width:10%" value="' + maxPax + '"></div>' + 
            '<div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_price">Price:</label>' + 
            '<input id="update_service_t_price" name="t_price" type="text" class="form-control" style="width:10%" value="' + price + '">' +
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_service_t_currency">Currency:</label>' +
            '<select name="t_currency" id="update_service_t_currency" class="form-control"><option value="EUR">EUR</option>' +
            '<option value="USD">USD</option><option value="GBP">GBP</option></select>' +
            '</div></br></br><button type="button" class="btn btn-success" onclick="submitTransfer(\'update\')" style="margin-left: 40%">' + 
            'Update</button><input type="hidden" name="command" value="update_transfer" />' + 
            '<input type="hidden" name="t_id" value="' + id + '" /></form></article></div>');
    
    $("#update_service_t_type").val(type);
    $("#update_service_t_currency").val(currency);
}

function deleteConf(id) {
    var conf = confirm('Are you sure you want to delete this service?');
    if(conf) {
        window.location = "API.php?command=delete_transfer&t_id=" + id;
    }
    
}

function submitTransfer(status) {
    var transferFormValid = true;
    if (status == "add") {
        var service_form_data = $("#serviceForm").serializeArray();
    } else if (status == "update") {
        var service_form_data = $("#updateServiceForm").serializeArray();
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
            $("#updateServiceForm").submit();
        }
    }
}

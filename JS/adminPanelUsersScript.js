/*** this document is the script for the users tab ***/

// 2 variables for navigation arrows in the general result (with no search code) 
var users_page_number = 1;
var total_pages_users = get_total_pages_users();

// 'on load' functions
$(function () {
    // get all user results into main table
    getAllUsers();

    // on click on the search button/enter key up triggers function userSearch that switch the results on 
    // the main table according to agent/agency code - no limit there, display all the results
    $("#user_searchBtn").on("click", userSearch);
    $("#user_search").keyup(function (e) {
        if (e.which == 13) {
            userSearch();
        }
    });

});

// get all the results from users DB to main table but limited, page number uses for offset
// this function is trigered 'onload' and on empty search code
function getAllUsers() {

    // return the number of total pages accordind to the 'PAGE SIZE' definition in the BusinessLogic
    get_total_pages_users();

    // ajax request to get results from data base and build the main table with those results
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_all_users", page_number: users_page_number},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (users) {
            users = JSON.parse(users);
            $("#user_info").empty();
            $("#editUserDetails").empty();
            $("#user_info").append("<tr class='info'><th style='width:20%;text-align:center;'>Agent Name</th>" +
                    "<th style='width:20%;text-align:center;'>Agency</th>" +
                    "<th style='width:25%;text-align:center;'>Agent Email</th>" +
                    "<th style='width:20%;text-align:center;'>Agent Phone</th>" +
                    "<th style='width:15%;text-align:center;'>Actions</th></tr>");
            $("#user_arrows").empty();
            $("#user_arrows").append("<a href='javascript:users_prev()'>&lt;</a>&nbsp;&nbsp; Page " + users_page_number + " out of " + total_pages_users +
                    " &nbsp;&nbsp;<a href='javascript:users_next()'>&gt;</a>&nbsp;&nbsp;");
            for (var i = 0; i < users.length; i++) {
                $("#user_info").append(
                        "<tr><td>" + users[i].u_agent_name + "</td><td>" + users[i].u_agency +
                        "</td><td>" + users[i].u_email + "</td><td>" +
                        users[i].u_phone + "</td>" +
                        "<td><button data-toggle='modal' data-target='#editUser' class='glyphicon glyphicon-pencil'" +
                        "style='margin-right:6%; height:25px;' onclick='editUserDetails(\"" +
                        users[i].u_agent_name + "\", \"" + users[i].u_agency +
                        "\", \"" + users[i].u_email + "\", \"" + users[i].u_phone + "\", \"" + users[i].u_id + "\", \"" + users[i].u_sabre_code + "\")'> Edit</button>" +
                        "<button style='margin-right:6%; height:25px;' class='glyphicon glyphicon-trash'" +
                        "onclick='deleteUserConf(\"" + users[i].u_id + "\", \"" + users[i].u_admin + "\")'> Delete</button></td></tr>");
            }
        }
    });
}

// function that get by ajax the amount of pages according to limit per page in BusinessLogic (uses 
// only the main getAllUsers function)
function get_total_pages_users() {
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_total_pages_users"},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (total_pages_users) {
            window.total_pages_users = total_pages_users;
        }
    });
}




// 2 functions for prev & next arrows
function users_prev() {
    if (users_page_number != 1) {
        users_page_number--;
        getAllUsers();
    }
}

function users_next() {
    if (users_page_number != total_pages_users) {
        users_page_number++;
        getAllUsers();
    }
}

// function that is triggered by clicking the 'search' button (or enter btn), returning result only where 
// agent/agency has partial match to the text that is in the search line
function userSearch() {
    // gets the value in the search line
    var user_search = $("#user_search").val();

    // if the search line is empty triggers again the main function of getAllUsers with page number 1
    if (user_search == "") {
        users_page_number = 1;
        return getAllUsers();
    }

    // ajax to get the result for table according to search
    $.ajax({
        type: "GET",
        url: "API.php",
        data: {command: "get_users_by_search", user_search: user_search},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (users) {
            users = JSON.parse(users);
            $("#user_info").empty();
            $("#editUserDetails").empty();
            $("#user_arrows").empty();
            $("#user_info").append("<tr class='info'><th style='width:20%;text-align:center;'>Agent Name</th>" +
                    "<th style='width:20%;text-align:center;'>Agency</th>" +
                    "<th style='width:25%;text-align:center;'>Agent Email</th>" +
                    "<th style='width:20%;text-align:center;'>Agent Phone</th>" +
                    "<th style='width:15%;text-align:center;'>Actions</th></tr>");
            for (var i = 0; i < users.length; i++) {
                $("#user_info").append(
                        "<tr><td>" + users[i].u_agent_name + "</td><td>" + users[i].u_agency +
                        "</td><td>" + users[i].u_email + "</td><td>" +
                        users[i].u_phone + "</td>" +
                        "<td><button data-toggle='modal' data-target='#editUser' class='glyphicon glyphicon-pencil'" +
                        "style='margin-right:6%; height:25px;' onclick='editUserDetails(\"" +
                        users[i].u_agent_name + "\", \"" + users[i].u_agency +
                        "\", \"" + users[i].u_email + "\", \"" + users[i].u_phone + "\", \"" + users[i].u_id + "\", \"" + users[i].u_sabre_code + "\")'> Edit</button>" +
                        "<button style='margin-right:6%; height:25px;' class='glyphicon glyphicon-trash'" +
                        "onclick='deleteUserConf(\"" + users[i].u_id + "\", \"" + users[i].u_admin + "\")'> Delete</button></td></tr>");
            }
        }
    });
}

// this function is triggerd by clicking the 'edit' button on the table
// this function appending to the modal window (that is triggered by the same button) a form to update user
// with all relevant data
function editUserDetails(agent_name, agency, email, phone, id, sabre_code) {

    $("#editUserDetails").empty();
    $("#editUserDetails").append(
            '<article><form method="POST" action="API.php" class="form-horizontal" id="updateUserForm">' +
            '<div class="form-group form-inline"><label class="addServiceLabel" for="update_user_u_agent_name">Agent Name:</label>' +
            '<input id="update_user_u_agent_name" name="u_agent_name" type="text" class="form-control" style="width:20%" value="' + agent_name + '" />' +
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_user_u_agency">Agency:</label>' +
            '<input id="update_user_u_agency" name="u_agency" type="text" class="form-control" style="width:20%" value="' + agency + '" />' +
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_user_u_phone">Agent Phone:</label>' +
            '<input id="update_user_u_phone" name="u_phone" type="text" class="form-control" style="width:20%" value="' + phone + '" />' +
            '</div><div class="form-group form-inline"><label class="addServiceLabel" for="update_user_u_email">Agent Email:</label>' +
            '<input id="update_user_u_email" name="u_email" type="text" class="form-control" style="width:20%" value="' + email + '" />' +
            '<span class="span_u_email" isValid="yes" style="color:red"></span></div><div class="form-group form-inline"><label class="addServiceLabel" for="update_user_u_password">Password:</label>' +
            '<input name="u_password" class="form-control u_password" type="text" style="width:20%" />&nbsp &nbsp' +
            '<input type="checkbox" id="changePass"> change password</div><div id="commandVal"></div>' +
            '<div class="form-group form-inline" id="sabreCodeDiv"><label class="addServiceLabel">Sabre Code:</label><div class="form-control" style="width:20%" id="sabreCode"></div>&nbsp &nbsp</div>' +
            '<input type="hidden" name="u_id" value="' + id + '" /></br></br>' +
            '<button type="button" class="btn btn-success" style="margin-left: 30%" onclick="submitUser(\'update\', \'' + email + '\')">Update</button>' +
            '</form></article>');

    isSabreClient(sabre_code, id);

    changePass();
    $("#changePass").change(changePass);
}

// check if a user is associated to Sabre, if no so display a button that will generate a unique sabre code, if yes display the agent's unique and a button to remove code & disassociate from Sabre
function isSabreClient(sabre_code, id) {
    if (sabre_code == 0) {
        $("#sabreCode").attr("disabled", true);
        $(".sabreCodeBtn").remove();
        $("#sabreCode").empty();
        $("#sabreCodeDiv").append('<button type="button" class="btn-primary sabreCodeBtn" onclick="genSabreCode(\'generate_code\',' + id + ')">Generate Code</button>');
    } else {
        $("#sabreCode").attr("disabled", false);
        $(".sabreCodeBtn").remove();
        $("#sabreCode").empty();
        $("#sabreCode").append(sabre_code);
        $("#sabreCodeDiv").append('<button type="button" class="btn-warning sabreCodeBtn" onclick="genSabreCode(\'delete_code\',' + id + ')">Disassociate</button>');
    }
}

// generate / disassociate Sabre code with ajax according to command
function genSabreCode(command, u_id) {
    $.ajax({
        type: "POST",
        url: "API.php",
        data: {command: command, u_id: u_id},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (sabre_code) {
          isSabreClient(sabre_code, u_id);
        }
    });
}

function changePass() {
    $("#commandVal").empty();
    // if user check the 'change pass' check box so abaling the pass text box and change the command to 
    // correct function in API.php to update with password. 
    if ($('#changePass').prop('checked')) {
        $('.u_password').attr('id', 'update_user_u_password');
        $(".u_password").attr("disabled", false);
        $("#commandVal").append('<input type="hidden" name="command" value="update_user_with_pass" />');
    } else {
        $('.u_password').attr('id', 'u_password');
        $(".u_password").attr("disabled", true);
        $('.u_password').parent().removeClass("has-error");
        $("#commandVal").append('<input type="hidden" name="command" value="update_user_no_pass" />');
    }
}

// triggered by 'delete' button, first blocking from deleting admin user and thrn confirms with user before 
// delete & then send to API witch id user to delete
function deleteUserConf(id, isAdmin) {
    if (isAdmin != 0) {
        return alert("Cannot delete Admin user!");
    } else {
        var conf = confirm('Are you sure you want to delete this user?');
        if (conf) {
            window.location = "API.php?command=delete_user&u_id=" + id;
        }
    }

}

// this function is checking if a new/edited user email is not already exists. next validation function
// triggers this one. the ajax is not async because we want to get the answer before submiting
function emailValidation(email) {
    return $.ajax({
        type: "GET",
        url: "API.php",
        async: false,
        data: {command: "is_email_exist", email: email},
        error: function (err) {
            alert("Error: " + err.status);
        },
        success: function (isMail) {
            if (isMail > 0) {
                $(".span_u_email").empty();
                $(".span_u_email").append(" Email is already exist");
                $(".span_u_email").attr("isValid", "no");
            } else {
                $(".span_u_email").empty();
                $(".span_u_email").attr("isValid", "yes");
            }
        }
    });
}

// JS validation function for the add/edit form - checks that there is no empty field
// and no email duplication in DB
function submitUser(status, email) {
    var userFormValid = true;
    if (status == "add") {
        var user_form_data = $("#userForm").serializeArray();
        emailValidation($("#user_u_email").val());
        if ($(".span_u_email").attr("isValid") == "no") {
            userFormValid = false;
        }
    } else if (status == "update") {
        var user_form_data = $("#updateUserForm").serializeArray();
        emailValidation($("#update_user_u_email").val());
        if ($(".span_u_email").attr("isValid") == "no" && email != $("#update_user_u_email").val()) {
            userFormValid = false;
        } else {
            $(".span_u_email").empty();
        }
    }
    for (var input in user_form_data) {
        if (status == "add") {
            var element = $("#user_" + user_form_data[input]['name']);
        } else if (status == "update") {
            var element = $("#update_user_" + user_form_data[input]['name']);
        }
        if (element.val() === "") {
            userFormValid = false;
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
    if (userFormValid) {
        if (status == "add") {
            $("#userForm").submit();
        } else if (status == "update") {
            $("#updateUserForm").submit();
        }
    }
}


<?php

require_once "DAL.php";
require_once 'includes/mailCon.php';

define("PAGE_SIZE", 20);

// next 6 functions handle with transfers (reservation tab in index.php & services tab in adminPanel.php)

function get_total_pages() {
    $sql = "select count(*) as total_rows from transfers";
    $total_pages = ceil(get_object($sql)->total_rows / PAGE_SIZE);
    return $total_pages;
}

function get_all_transfers($page_number) {
    $offset = ($page_number - 1) * PAGE_SIZE;
    if (is_numeric($offset)) {
        $sql = "select * from transfers order by t_price limit " . PAGE_SIZE . " offset $offset";
        $transfers = get_array($sql);
        return json_encode($transfers);
    }
}

function get_transfer_by_code($code) {
    $code = strip_tags($code);
    $code = addslashes($code);
    if (strlen($code) < 4) {
        $sql = "select * from transfers where t_airport_code = '$code' or t_destination_code = '$code' order by t_price";
        $transfers = get_array($sql);
        return json_encode($transfers);
    }
}

function insert_transfer($t_airport_code, $t_destination_code, $t_description, $t_type, $t_min_pax, $t_max_pax, $t_price, $t_currency) {
    $t_airport_code = strip_tags($t_airport_code);
    $t_airport_code = addslashes($t_airport_code);
    $t_destination_code = strip_tags($t_destination_code);
    $t_destination_code = addslashes($t_destination_code);
    $t_description = strip_tags($t_description);
    $t_description = addslashes($t_description);
    $t_type = strip_tags($t_type);
    $t_type = addslashes($t_type);
    $t_currency = strip_tags($t_currency);
    $t_currency = addslashes($t_currency);
    if (is_numeric($t_min_pax) && is_numeric($t_max_pax) && is_numeric($t_price) && strlen($t_airport_code) < 4 &&
            strlen($t_destination_code) < 4) {
        $sql = "INSERT INTO transfers "
                . "(t_airport_code, t_destination_code, t_description, t_type, t_min_pax, t_max_pax, t_price, t_currency)"
                . "VALUES"
                . " ('$t_airport_code', '$t_destination_code', '$t_description', '$t_type', '$t_min_pax', '$t_max_pax', '$t_price', '$t_currency')";

        insert($sql);
    }
}

function update_transfer($t_id, $t_airport_code, $t_destination_code, $t_description, $t_type, $t_min_pax, $t_max_pax, $t_price, $t_currency) {
    $t_airport_code = strip_tags($t_airport_code);
    $t_airport_code = addslashes($t_airport_code);
    $t_destination_code = strip_tags($t_destination_code);
    $t_destination_code = addslashes($t_destination_code);
    $t_description = strip_tags($t_description);
    $t_description = addslashes($t_description);
    $t_type = strip_tags($t_type);
    $t_type = addslashes($t_type);
    $t_currency = strip_tags($t_currency);
    $t_currency = addslashes($t_currency);
    if (is_numeric($t_min_pax) && is_numeric($t_max_pax) && is_numeric($t_price) && strlen($t_airport_code) < 4 &&
            strlen($t_destination_code) < 4 && is_numeric($t_id)) {
        $sql = "update transfers set t_airport_code = '$t_airport_code', t_destination_code = '$t_destination_code', "
                . "t_description = '$t_description', t_type = '$t_type', t_min_pax = '$t_min_pax', t_max_pax = '$t_max_pax', "
                . "t_price = '$t_price', t_currency = '$t_currency' where t_id = $t_id";
        update($sql);
    }
}

function delete_transfer($t_id) {
    if (is_numeric($t_id)) {
        $sql = "delete from transfers where t_id = '$t_id'";
        delete($sql);
    }
}

// next 12 functions handle with users (users tab in adminPanel.php & login issues)
// u_create_date is used for salting the password
// u_admin = 1 means its admin & u_admin = 0 is no admin

function user_login($u_email, $u_password) {
    $u_email = strip_tags($u_email);
    $u_password = strip_tags($u_password);
    $u_email = addslashes($u_email);
    $u_password = addslashes($u_password);
    $sql = "select * from users where u_email = '$u_email'";
    $user = get_object($sql);
    if ($user && crypt($u_password, $user->u_create_date) == $user->u_password) {
        return json_encode($user);
    } else {
        return null;
    }
}

function insert_user($u_agent_name, $u_agency, $u_phone, $u_email, $u_password, $u_admin) {
    $u_agent_name = strip_tags($u_agent_name);
    $u_agent_name = addslashes($u_agent_name);
    $u_agency = strip_tags($u_agency);
    $u_agency = addslashes($u_agency);
    $u_phone = strip_tags($u_phone);
    $u_phone = addslashes($u_phone);
    $u_email = strip_tags($u_email);
    $u_email = addslashes($u_email);
    $u_password = strip_tags($u_password);
    $u_password = addslashes($u_password);
    if (is_numeric($u_admin)) {
        $sql = "INSERT INTO users "
                . "(u_agent_name, u_agency, u_phone, u_email, u_password, u_admin)"
                . "VALUES"
                . " ('$u_agent_name', '$u_agency', '$u_phone', '$u_email', '$u_password', '$u_admin')";

        $insert_id = insert($sql);
        $create_date = get_object("select * from users where u_id = '$insert_id'")->u_create_date;
        $u_password = crypt($u_password, $create_date);

        $upSql = "update users set u_password = '$u_password' where u_id = '$insert_id'";
        update($upSql);
    }
}

function get_total_pages_users() {
    $sql = "select count(*) as total_rows from users";
    $total_pages = ceil(get_object($sql)->total_rows / PAGE_SIZE);
    return $total_pages;
}

function get_all_users($page_number) {
    $offset = ($page_number - 1) * PAGE_SIZE;
    if (is_numeric($offset)) {
        $sql = "select * from users order by u_agent_name limit " . PAGE_SIZE . " offset $offset";
        $users = get_array($sql);
        return json_encode($users);
    }
}

function get_users_by_search($search) {
    $search = strip_tags($search);
    $search = addslashes($search);
    $sql = "select * from users where u_agent_name like '%$search%' or u_agency like '%$search%' order by u_agent_name";
    $users = get_array($sql);
    return json_encode($users);
}

function update_user_with_pass($u_id, $u_agent_name, $u_agency, $u_phone, $u_email, $u_password) {
    $u_agent_name = strip_tags($u_agent_name);
    $u_agent_name = addslashes($u_agent_name);
    $u_agency = strip_tags($u_agency);
    $u_agency = addslashes($u_agency);
    $u_phone = strip_tags($u_phone);
    $u_phone = addslashes($u_phone);
    $u_email = strip_tags($u_email);
    $u_email = addslashes($u_email);
    $u_password = strip_tags($u_password);
    $u_password = addslashes($u_password);
    $create_date = get_object("select * from users where u_id = '$u_id'")->u_create_date;
    $u_password = crypt($u_password, $create_date);
    if (is_numeric($u_id)) {
        $sql = "update users set u_agent_name = '$u_agent_name', u_agency = '$u_agency', "
                . "u_phone = '$u_phone', u_email = '$u_email', u_password = '$u_password' where u_id = $u_id";
        update($sql);
    }
}

function update_user_no_pass($u_id, $u_agent_name, $u_agency, $u_phone, $u_email) {
    $u_agent_name = strip_tags($u_agent_name);
    $u_agent_name = addslashes($u_agent_name);
    $u_agency = strip_tags($u_agency);
    $u_agency = addslashes($u_agency);
    $u_phone = strip_tags($u_phone);
    $u_phone = addslashes($u_phone);
    $u_email = strip_tags($u_email);
    $u_email = addslashes($u_email);
    if (is_numeric($u_id)) {
        $sql = "update users set u_agent_name = '$u_agent_name', u_agency = '$u_agency', "
                . "u_phone = '$u_phone', u_email = '$u_email' where u_id = $u_id";
        update($sql);
    }
}

function is_email_exist($email) {
    $email = strip_tags($email);
    $email = addslashes($email);
    $sql = "select count(*) as total_rows from users where `u_email` = '$email'";
    return get_object($sql)->total_rows;
}

// generate a unique code per user for user that is defined as Sabre user
function generate_sabre_code($u_id) {
    if (is_numeric($u_id)) {
        $create_date = get_object("select * from users where u_id = '$u_id'")->u_create_date;
        $sabre_code = substr($u_id, 0, 1) . substr($create_date, 5, 2) . substr($create_date, 11, 2) . (100000 - $u_id) . substr($create_date, 8, 2);
        $sabre_code = $sabre_code * 1;
        $sql = "update users set u_sabre_code = '$sabre_code', u_issabre = '1' where u_id = '$u_id'";
        update($sql);
        return $sabre_code;
    }
}

// delete the unique code for user that is no longer defined as Sabre users
function delete_sabre_code($u_id) {
    if (is_numeric($u_id)) {
        $sql = "update users set u_sabre_code = '0', u_issabre = '0' where u_id = '$u_id'";
        update($sql);
        return null;
    }
}

// bypass the login for users that was refered by Sabre and has a matching code
function sabre_login($sabre_code) {
    $sabre_code = strip_tags($sabre_code);
    $sabre_code = addslashes($sabre_code);
    if (is_numeric($sabre_code)) {
        $sql = "select * from users where u_sabre_code = '$sabre_code'";
        $user = get_object($sql);
        if ($user) {
            return json_encode($user);
        } else {
            return null;
        }
    }
}

function delete_user($u_id) {
    if (is_numeric($u_id)) {
        $sql = "delete from users where u_id = '$u_id' and u_admin = 0";
        delete($sql);
    }
}

// next 5 functions handle the reservation tab in adminPanel.php

function insert_reservation($r_quantity, $r_description, $r_type, $r_total_cost, $r_currency, $r_airport_code, $r_arr_date = null, $r_arr_flight = null, $r_arr_dest = null, $r_dep_date = null, $r_dep_flight = null, $r_dep_dest = null, $r_pax_name, $r_pax_num, $r_pax_phone, $r_remark, $r_agency, $r_agent_name, $r_agent_id, $r_agent_email, $r_agent_phone, $r_min_pax, $r_max_pax, $sabre_user) {
    $r_quantity = strip_tags($r_quantity);
    $r_quantity = addslashes($r_quantity);
    $r_description = strip_tags($r_description);
    $r_description = addslashes($r_description);
    $r_type = strip_tags($r_type);
    $r_type = addslashes($r_type);
    $r_currency = strip_tags($r_currency);
    $r_currency = addslashes($r_currency);
    $r_airport_code = strip_tags($r_airport_code);
    $r_airport_code = addslashes($r_airport_code);
    $r_arr_date = strip_tags($r_arr_date);
    $r_arr_date = addslashes($r_arr_date);
    $r_arr_flight = strip_tags($r_arr_flight);
    $r_arr_flight = addslashes($r_arr_flight);
    $r_arr_dest = strip_tags($r_arr_dest);
    $r_arr_dest = addslashes($r_arr_dest);
    $r_dep_date = strip_tags($r_dep_date);
    $r_dep_date = addslashes($r_dep_date);
    $r_dep_flight = strip_tags($r_dep_flight);
    $r_dep_flight = addslashes($r_dep_flight);
    $r_dep_dest = strip_tags($r_dep_dest);
    $r_dep_dest = addslashes($r_dep_dest);
    $r_pax_name = strip_tags($r_pax_name);
    $r_pax_name = addslashes($r_pax_name);
    $r_pax_phone = strip_tags($r_pax_phone);
    $r_pax_phone = addslashes($r_pax_phone);
    $r_remark = strip_tags($r_remark);
    $r_remark = addslashes($r_remark);
    $r_agency = strip_tags($r_agency);
    $r_agency = addslashes($r_agency);
    $r_agent_name = strip_tags($r_agent_name);
    $r_agent_name = addslashes($r_agent_name);
    $r_agent_email = strip_tags($r_agent_email);
    $r_agent_email = addslashes($r_agent_email);
    $r_agent_phone = strip_tags($r_agent_phone);
    $r_agent_phone = addslashes($r_agent_phone);
    if (is_numeric($r_total_cost) && is_numeric($r_pax_num) && is_numeric($r_agent_id) && is_numeric($r_min_pax) && is_numeric($r_max_pax) && is_numeric($sabre_user)) {
        $sql = "INSERT INTO reservations "
                . "(r_quantity, r_description, r_type, r_total_cost, r_currency, r_airport_code, r_arr_date, r_arr_flight, "
                . "r_arr_dest, r_dep_date, r_dep_flight, r_dep_dest, r_pax_name, r_pax_num, r_pax_phone, r_remark, r_agency, "
                . "r_agent_name, r_agent_id, r_agent_email, r_agent_phone, r_min_pax, r_max_pax, sabre_user) "
                . "VALUES"
                . " ('$r_quantity', '$r_description', '$r_type', '$r_total_cost', '$r_currency', '$r_airport_code', "
                . "'$r_arr_date', '$r_arr_flight', '$r_arr_dest', '$r_dep_date', '$r_dep_flight', '$r_dep_dest', '$r_pax_name', "
                . "'$r_pax_num', '$r_pax_phone', '$r_remark', '$r_agency', '$r_agent_name', '$r_agent_id', '$r_agent_email', "
                . "'$r_agent_phone', '$r_min_pax', '$r_max_pax', '$sabre_user')";

        $insert_id = insert($sql);

        $upSql = "update reservations set r_res_num = 10000 + r_id where r_id = $insert_id";
        update($upSql);
        return $insert_id + 10000;
    }
}

function get_total_pages_res() {
    $sql = "select count(*) as total_rows from reservations";
    $total_pages = ceil(get_object($sql)->total_rows / PAGE_SIZE);
    return $total_pages;
}

function get_all_res($page_number) {
    $offset = ($page_number - 1) * PAGE_SIZE;
    if (is_numeric($offset)) {
        $sql = "select * from reservations order by r_created DESC limit " . PAGE_SIZE . " offset $offset";
        $res = get_array($sql);
        return json_encode($res);
    }
}

function get_res_by_search($search) {
    $search = strip_tags($search);
    $search = addslashes($search);
    $sql = "select * from reservations where r_res_num like '$search' or r_pax_name like '%$search%'" .
            "or r_agent_name like '%$search%' order by r_created DESC";
    $res = get_array($sql);
    return json_encode($res);
}

function update_res($r_id, $r_res_num, $r_status) {
    $r_res_num = strip_tags($r_res_num);
    $r_res_num = addslashes($r_res_num);
    $r_status = strip_tags($r_status);
    $r_status = addslashes($r_status);
    if (is_numeric($r_id)) {
        $sql = "update reservations set r_res_num = '$r_res_num', r_status = '$r_status' where r_id = $r_id";
        update($sql);
    }
}

function logout() {
    session_destroy();
    session_unset();
    $_SESSION = null;
}

// next 3 functions handle the 'My Bookings' tab in index.php
function get_total_pages_agent_bookings($r_agent_id) {
    if (is_numeric($r_agent_id)) {
        $sql = "select count(*) as total_rows from reservations where r_agent_id = '$r_agent_id'";
        $total_pages = ceil(get_object($sql)->total_rows / PAGE_SIZE);
        return $total_pages;
    }
}

function get_all_agent_bookings($page_number, $r_agent_id) {
    $offset = ($page_number - 1) * PAGE_SIZE;
    if (is_numeric($offset) && is_numeric($r_agent_id)) {
        $sql = "select * from reservations where r_agent_id = '$r_agent_id' order by r_created DESC limit " . PAGE_SIZE . " offset $offset";
        $res = get_array($sql);
        return json_encode($res);
    }
}

function get_agent_bookings_by_search($search, $r_agent_id) {
    $search = strip_tags($search);
    $search = addslashes($search);
    if(is_numeric($r_agent_id)) {
        $sql = "select * from reservations where r_agent_id = '$r_agent_id' and (r_res_num like '$search' or r_pax_name like '%$search%'" .
                "or r_airport_code like '%$search%') order by r_created DESC";
        $res = get_array($sql);
        return json_encode($res);
    }
}

// handle the 'Contact Us' tab in index.php - sending an email to admin with form details
function contact_us($agent_name, $agency, $email, $phone, $message) {
    $agent_name = strip_tags($agent_name);
    $agency = strip_tags($agency);
    $email = strip_tags($email);
    $phone = strip_tags($phone);
    $message = strip_tags($message);
    
    $subject = "Contact us request from cityride booking system";
    $body = "<html><body style='font-size: 16px;padding-right: 0px;padding-left: 0px;font-family: Arial, serif;'>" . 
            "<div style='margin-left: 8px;margin-right: 8px;line-height: 125%;white-space:pre-wrap;'><b>Agent Name:</b> " . $agent_name . "<br><br>" . 
            "<b>Agency:</b> " . $agency . "<br><br><b>Email Address:</b> " . $email . "<br><br>" . 
            "<b>Phone Number:</b> " . $phone . "<br><br><b>Message:</b> <span style='display: inline-flex;'>" . $message . "</span></div></body></html>";
    
    smtpmailer_contact_us("info@cityride.co.il", $email, "Contact Us", $subject, $body);
}

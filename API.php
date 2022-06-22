<?php

require_once 'BusinessLogic.php';
include_once 'includes/global.php';

$command = $_REQUEST["command"];

switch ($command) {

    case "user_login":
        session_unset();
        $user = json_decode(user_login($_POST["user_email"], $_POST["user_password"]));
        if ($user) {
            $_SESSION['auth'] = true;
            $_SESSION['user_det'] = $user;
            if ($user->u_admin) {
                $_SESSION['admin'] = true;
            }
            if ($_POST["remember_me"] == 1) {
                $remember = array("email" => $_POST["user_email"], "password" => $_POST["user_password"]);
                setcookie('remember_me', json_encode($remember), time() + 8640000);
            } elseif ($_POST['remember_me'] == 0) {
                if (isset($_COOKIE['remember_me'])) {
                    setcookie('remember_me', "", time() - 100);
                }
            }
        }
        echo user_login($_POST["user_email"], $_POST["user_password"]);
        break;

    case "get_total_pages":
        echo get_total_pages();
        break;

    case "get_total_filtered_pages":
        echo get_total_filtered_pages($_GET["search_code"]);
        break;

    case "get_all_transfers":
        echo get_all_transfers($_GET["page_number"]);
        break;

    case "get_transfer_by_code":
        echo get_transfer_by_code($_GET["search_code"]);
        break;

    case "add_transfer":
        insert_transfer($_POST["t_airport_code"], $_POST["t_destination_code"], $_POST["t_description"], $_POST["t_type"], $_POST["t_min_pax"], $_POST["t_max_pax"], $_POST["t_price"], $_POST["t_currency"]);
        redirect("ThankYou.php");
        break;

    case "update_transfer":
        update_transfer($_POST["t_id"], $_POST["t_airport_code"], $_POST["t_destination_code"], $_POST["t_description"], $_POST["t_type"], $_POST["t_min_pax"], $_POST["t_max_pax"], $_POST["t_price"], $_POST["t_currency"]);
        redirect("ThankYou.php");
        break;

    case "delete_transfer":
        delete_transfer($_GET["t_id"]);
        redirect("ThankYou.php");
        break;

    case "get_total_pages_users":
        echo get_total_pages_users();
        break;

    case "get_all_users":
        echo get_all_users($_GET["page_number"]);
        break;

    case "get_users_by_search":
        echo get_users_by_search($_GET["user_search"]);
        break;

    case "add_user":
        insert_user($_POST["u_agent_name"], $_POST["u_agency"], $_POST["u_phone"], $_POST["u_email"], $_POST["u_password"], 0);
        redirect("ThankYou.php");
        break;

    case "update_user_with_pass":
        update_user_with_pass($_POST["u_id"], $_POST["u_agent_name"], $_POST["u_agency"], $_POST["u_phone"], $_POST["u_email"], $_POST["u_password"]);
        redirect("ThankYou.php");
        break;

    case "update_user_no_pass":
        update_user_no_pass($_POST["u_id"], $_POST["u_agent_name"], $_POST["u_agency"], $_POST["u_phone"], $_POST["u_email"]);
        redirect("ThankYou.php");
        break;

    case "generate_code":
        echo generate_sabre_code($_POST["u_id"]);
        break;

    case "delete_code":
        echo delete_sabre_code($_POST["u_id"]);
        break;

    case "sabre_login":
        session_unset();
        $user = sabre_login($_POST["sabre_code"]);
        if ($user) {
            $_SESSION['auth'] = true;
            $_SESSION['sabre_user'] = true;
            $_SESSION['user_det'] = json_decode($user);
        } else {
            $_SESSION['sabre_guest'] = true;
        }
        echo $user;
        break;

    case "delete_user":
        delete_user($_GET["u_id"]);
        redirect("ThankYou.php");
        break;

    case "logout":
        logout();
        redirect("logIn.php");
        break;

    case "get_total_pages_res":
        echo get_total_pages_res();
        break;

    case "get_all_res":
        echo get_all_res($_GET["page_number"]);
        break;

    case "update_res":
        echo update_res($_POST["id"], $_POST["resNum"], $_POST["resStatus"]);
        redirect("ThankYou.php");
        break;

    case "get_res_by_search":
        echo get_res_by_search($_GET["res_search"]);
        break;

    case "is_email_exist":
        echo is_email_exist($_GET["email"]);
        break;
    
    case "get_total_pages_agent_bookings":
        echo get_total_pages_agent_bookings($_SESSION['user_det']->u_id);
        break;

    case "get_all_agent_bookings":
        echo get_all_agent_bookings($_GET["page_number"], $_SESSION['user_det']->u_id);
        break;

    case "get_agent_bookings_by_search":
        echo get_agent_bookings_by_search($_GET["agent_bookings_search"], $_SESSION['user_det']->u_id);
        break;
    
    case "contact_us":
        contact_us($_POST["con_agent_name"], $_POST["con_agency"], $_POST["con_email"], $_POST["con_phone"], $_POST["con_message"]);
        redirect("ThankYou.php?action=contact");
        break;
}

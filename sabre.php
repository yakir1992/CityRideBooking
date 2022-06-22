<?php
// this page handle with Sabre clients that refer to the system through Sabre website
// if the Sabre user has cityride user with matching sabre_code so he will auto loged-in else he will login as a guest with limited access to index.php

include_once '/includes/global.php';
include '/includes/header.php';

// store the website referer into php variable
$ref = null;
if (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
}

// check if the client was refered by one of Sabre websites, if not kill the session, redirecting to index.php & kill the script
if ($ref != "https://ldsu.sabre.co.il" && $ref != "https://ldsg.sabre.co.il") {
    session_unset();
    redirect("index.php");
    die();
}
?>

<script>
    // store the sabre_code sent with $_GET into JS variable
    var sabre_code = <?php
if (isset($_GET["sabre_code"]) && is_numeric($_GET["sabre_code"])) {
    echo $_GET["sabre_code"];
} else {
    echo "null";
}
?>;

    $(function () {
        // onload triggers the isSabreUser function - sending by ajax call to the server the sabre_code and the server handaling the permisions with sessions  
        isSabreUser(sabre_code);
    });

    function isSabreUser(sabre_code) {
        $.ajax({
            type: "POST",
            url: "API.php",
            data: {command: "sabre_login", sabre_code: sabre_code},
            error: function (err) {
                alert("Error: " + err.status);
            },
            success: function (user) {

                window.location.replace("index.php");

            }
        });
    }
</script>

<?php
include_once 'includes/footer.php';

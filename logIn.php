<?php

include_once 'includes/global.php';
include 'includes/header.php';

// check if a 'remember me' cookie is set, if so storing the user email & password into php variables 
if(isset($_COOKIE['remember_me'])) {
    $remember_email = json_decode($_COOKIE['remember_me'])->email;
    $remember_pass = json_decode($_COOKIE['remember_me'])->password;
}  else {
   $remember_email = "";
   $remember_pass = "";
}

?>

<script>

    $(function () {
        // click on login btn or enter key triggers the login function
        $("#loginBtn").on("click", login);
        $("#user_password").keyup(function (e) {
            if (e.which == 13) {
                login();
            }
        });
    });

    // ajax call to check inserted email & password, if there's a match on DB, user is redirected to index.php
    // and API store all user details as an object in a session
    function login() {
        var user_email = $("#user_email").val();
        var user_password = $("#user_password").val();
        // check if the remember_me check box is marked - if yes store a cookie, if no kill the cookie (even if a cookie is already set)
        if ($('#remember_me').prop('checked')) {
            var remember_me = 1;
        } else {
            var remember_me = 0;
        }
        $.ajax({
            type: "POST",
            url: "API.php",
            data: {command: "user_login", user_email: user_email, user_password: user_password, remember_me: remember_me},
            error: function (err) {
                alert("Error: " + err.status);
            },
            success: function (user) {
                if (user) {
                    window.location.replace("index.php");
                } else {
                    $("#wrongPass").addClass("alert alert-danger loginPanel");
                    $("#wrongPass").html("Wrong email address or password");
                }
            }
        });
    }
</script>

<style>
    .loginPanel {
        width: 60%;
        margin-left: 20%;
        margin-right: 20%;
    } 

</style>

<br/><br/><br/><br/>
<div class="panel panel-info loginPanel">
    <div class="panel-heading">Login</div>
    <div class="panel-body">
        <form method="POST" action="API.php">
            <br/>
            <div class="form-group">
                <label for="userName">Email:</label>
                <input type="text" name="user_email" class="form-control" id="user_email" placeholder="Enter email" value="<?= $remember_email ?>" />
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="user_password" class="form-control" id="user_password" placeholder="Enter password" value="<?= $remember_pass ?>" />
            </div>
            <div class="form-group">
                <input type="checkbox" name="remember_me" id="remember_me" <?php if(isset($_COOKIE['remember_me'])){echo 'checked';} ?> /> Remember Me
            </div>
            <br/>
            <button type="button" class="btn btn-info" id="loginBtn" style="float:right;">Login</button>
        </form>
    </div>
</div>
<div id="wrongPass"></div>

<?php

include_once 'includes/footer.php';

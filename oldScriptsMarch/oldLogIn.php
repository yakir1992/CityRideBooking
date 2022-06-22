<?php
include_once 'includes/global.php';
include 'includes/header.php';

// var to use later and check if a wrong user name or password was entered and alert it
$showLoginErr = false;

/*if ($_POST) {
    if ($_POST['userName'] == 'cityride' && $_POST['password'] == '1234') {
        $_SESSION['auth'] = true;
        redirect('adminPanel.php');
    } else {
        $showLoginErr = true;
    }
}*/

/*
  // var to use later and check if a wrong user name or password was entered and alert it
  $showLoginErr = false;
  if ($_POST) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $password = md5($password);
  $sql = "SELECT * FROM `userinfo` WHERE `u_email` = '$email' and `u_password` = '$password'";
  $result = $dbCon->query($sql);
  if (!$result) {
  die('Query failed: ' . $dbCon->error);
  }
  $userInfo = $result->fetch_assoc();
  if ($userInfo) {
  $_SESSION['auth'] = true;
  $_SESSION['userLogedIn'] = $userInfo;
  redirect('index.php');
  } else {//failed login
  $showLoginErr = true;
  }
  } */
?>
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
        <form method="POST">
            <br/>
            <div class="form-group">
                <label for="userName">User Name:</label>
                <input type="text" name="userName" class="form-control" id="userName" placeholder="Enter user name" />
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" />
            </div>
            <br/>
            <button type="submit" class="btn btn-info" style="float:right;">Login</button>
        </form>

    </div>
</div>
<?php if ($showLoginErr): ?>
    <div class="alert alert-danger loginPanel">
        Wrong user name or password
    </div>
<?php endif;
?>


<?php
include_once 'includes/footer.php';

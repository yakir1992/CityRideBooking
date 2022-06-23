<?php
//include 'includes/header.php';
include_once 'includes/global.php';

// new - if it refered by the contact me so its a longer & different messages
?>

<!DOCTYPE html>
<html>
    <head>
        <link href="media/css/bootstrap.min.css" rel="stylesheet" />
        <?php
        if (isset($_GET["action"]) && $_GET["action"] == "contact") {
            echo '<meta http-equiv="refresh" content="6; url=index.php" />';
        } else {
            echo '<meta http-equiv="refresh" content="1; url=adminPanel.php" /> ';
        }
        ?>
    </head>
    <body>
        <div class="panel panel-primary">
                <?php
                if (isset($_GET["action"]) && $_GET["action"] == "contact") {
                    echo '<div class="panel-heading"><h1>Thank you for Contacting Cityride</h1></div>' . 
                            '<div class="panel-body"><h3>We received your request and we will reply to your email as soon as possible.</h3></div>';
                } else {
                    echo '<div class="panel-heading"><h1>Done :-)</h1></div>';
                }
                ?>
        </div>
    </body>
</html>

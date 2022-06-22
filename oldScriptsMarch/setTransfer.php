<?php

include 'includes/header.php';
require_once 'businesslogic.php';
require_once 'DAL.php';
include_once 'includes/global.php';

if(!isset($_SESSION['auth'])) {
   redirect('HomePage.php');
}

$update = false;
if ($_GET) {
    $t_id = (int) @$_GET['t_id'];
    $dbCon = connect();
    $sql = "SELECT * FROM transfers WHERE t_id=$t_id";
    $result = $dbCon->query($sql);
    if (!$result) {
        die('Query failed: ' . $dbCon->error);
    }
    $transfers = $result->fetch_assoc();
    $type_selected = @$transfers['t_type'];
    $currency_selected = @$transfers['t_currency'];
    $update = true;
}

?>

<style>
    label{
        display:inline-block;
        width:200px;
        margin-right:0px;
        margin-left:15px;
        text-align:left;
    }

    input{

    }

    fieldset{
        border:none;
        width:500px;
        margin:0px auto;
    }
</style>

<div class="panel panel-default">
    <div class="well well-lg">
        <article>
            <div class="page-header"><h3>Create / Edit Transfer</h3></div>
            <form method="POST" action="API.php" class="form-horizontal">
                <div class="form-group">
                    <label>Airport Code:</label>
                    <input name="t_airport_code" type="text" style="width:10%" value = "<?= @$transfers['t_airport_code'] ?>">
                </div>
                <div class="form-group">
                    <label>Destination Code:</label>
                    <input name="t_destination_code" type="text" style="width:10%" value = "<?= @$transfers['t_destination_code'] ?>">
                </div>
                <div class="form-group">
                    <label>Service Description:</label>
                    <input name="t_description" type="text" style="width:40%" value = "<?= @$transfers['t_description'] ?>">
                </div>
                <div class="form-group">
                    <label>Transfer Type:</label>
                    <select name="t_type">
                        <option value="Private Service" <?php if(@$type_selected == 'Private Service'){echo("selected");}?>>Private</option>
                        <option value="Shared Shuttle" <?php if(@$type_selected == 'Shared Shuttle'){echo("selected");}?>>Shuttle</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Min Pax:</label>
                    <input name="t_min_pax" type="text" style="width:10%" value = "<?= @$transfers['t_min_pax'] ?>">
                </div>
                <div class="form-group">
                    <label>Max Pax:</label>
                    <input name="t_max_pax" type="text" style="width:10%" value = "<?= @$transfers['t_max_pax'] ?>">
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input name="t_price" type="text" style="width:10%" value = "<?= @$transfers['t_price'] ?>">
                </div>
                <div class="form-group">
                    <label>Currency:</label>
                    <select name="t_currency">
                        <option value="EUR" <?php if(@$currency_selected == 'EUR'){echo("selected");}?>>EUR</option>
                        <option value="USD" <?php if(@$currency_selected == 'USD'){echo("selected");}?>>USD</option>
                        <option value="GBP" <?php if(@$currency_selected == 'GBP'){echo("selected");}?>>GBP</option>
                    </select>
                </div>
                </br></br>
                <a href="adminPanel.php" class="btn btn-danger">Cancel</a>
                <?php if($update) {
                    echo "<button type='submit' class='btn btn-success' style='margin-left: 30%'>";
                    echo "Update";
                    echo "</button>";
                    echo "<input type='hidden' name='command' value='update_transfer' />";
                    echo "<input type='hidden' name='t_id' value= " . $t_id . " />";
                } else {
                    echo "<button type='submit' class='btn btn-success' style='margin-left: 30%'>";
                    echo "Add";
                    echo "</button>";
                    echo "<input type='hidden' name='command' value='add_transfer' />";
                }
                ?>
            </form>
        </article>
    </div>
</div>


<?php

include_once 'includes/footer.php';

<?php
include 'functions.php';
if(mysession_check()==false) {
    header("location: login.php");
    exit;
}

$errori = NULL;
if(isset($_POST['oldpass']) && isset($_POST['newpass']) && isset($_POST['rnewpass'])){

    if($_POST['newpass']!=$_POST['rnewpass']){
        $errori = "New password does not match, ";
    }

    if($_POST['oldpass']==$_POST['newpass']){
        $errori = "You are using the same password, ";
    }

    if(mysession_get_user()==$_POST['newpass']){
        $errori = "The password can't be the username, ";
    }

    if(strlen($_POST['newpass'])<8){
        $errori = "Password too short and must be at least 8 characters long, ";
    }

    if(mycredential_check(mysession_get_user(), $_POST['oldpass'])){
        $errori = "Old password not correct, ";
    }
    
    if(empty($errori)){
        update_user_password(mysession_get_user(), $_POST['newpass']);
        echo "PASS CHANGED";
        mysession_close();
    }
    else {
        ?>
        <h2><?=$errori ?></h2>
    <?php
    }


}


?>

<html>
    <head>
        <?php include 'header.php'; ?>
    </head>
    <body>
        <div class="container">
            <h1>︎️Settings</h1>


                <?php include 'navbar.php'; ?>



            <div class="row">
                <div class="col-6">
                    <h2>Account</h2>

                    <form action="settings.php" method="post">
                        <fieldset id="forms__input">
                            <legend>Change password</legend>

                            <p>
                                <label for="oldpass">Old Password</label>
                                <input id="oldpass" name="oldpass" type="password" placeholder="Type your Password" autocomplete="off" required>
                            </p>
                            <p>
                                <label for="newpass">New Password</label>
                                <input id="newpass" name="newpass" type="password" placeholder="Type your Password" autocomplete="off" required>
                            </p>
                            <p>
                                <label for="rnewpass">Retype New password</label>
                                <input id="rnewpass" name="rnewpass" type="password" placeholder="Type your Password" autocomplete="off" required>
                            </p>
                            <button type="submit" class="button primary pull-right">Change</button><br><br>
                        </fieldset>
                    </form>
                </div>


            </div>
        </div>
    </body>
</html>




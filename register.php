<?php
include 'functions.php';
if(mysession_check()==true){
    header("location: index.php");
    exit;
}
?>
<html>
    <head>
        <?php include 'header.php'; ?>
        <style>
            .bg_img {
                /* The image used */
                background-image: url("img/bg_register.jpg");

                /* Full height */
                height: 100%;

                /* Center and scale the image nicely */
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
        </style>
    </head>
    <body class="bg_img">
        <div class="container is-center">
            <div class="row is-center is-full-screen">
                <div class="col-6">

                    <?php if(isset($_GET['message'])){
                        ?>

                        <div class="card bg-error" style="opacity: 0.95; ">
                            <header>
                                <h4>Ehy</h4>
                            </header>
                            <p><?=strip_tags($_GET['message']); ?></p>
                        </div>
                        <br>
                        <?php
                    }
                    ?>

                    <div class="card bg-dark" style="opacity: 0.95; ">
                        <form action="useradd.php" method="post">
                            <header>
                                <h4>Register</h4>
                            </header>
                            <label for="username">Username</label> <input type="text" id="username" name="username" autocomplete="off" autocapitalize="none" autofocus required><br>
                            <label for="password">Password</label> <input type="password" id="password" name="password" autocomplete="off" required><br>
                            <label for="password_check">Repeat Password</label> <input type="password" id="password_repeat" name="password_repeat" autocomplete="off" required><br>
                            <input type="checkbox" id="terms_check" name="terms_check" value="yes" required><label for="terms_check"> I accept <a href="terms.php">this</a>.</label>
                            <br><br>
                            <footer class="is-right">
                                <a href="login.php" class="button outline success">Login</a>
                                <button type="submit" class="button primary">Register</button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
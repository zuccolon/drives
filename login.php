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
                background-image: url("img/bg_login.jpg");

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

                        <div class="card bg-primary" style="opacity: 0.95; ">
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
                        <form action="auth.php" method="post">
                            <header>
                                <h4>Login</h4>
                            </header>
                            <label for="username">Username</label> <input type="text" id="username" name="username" required autocomplete="off" autocapitalize="none" autofocus><br>
                            <label for="password">Password</label> <input type="password" id="password" name="password" required autocomplete="off"><br>
                            <input type="checkbox" id="remember" name="remember" value="yes" checked><label for="remember"> Remember Me</label>
                            <br><br>
                            <footer class="is-right">
                                <a href="register.php" class="button outline success">Register</a>
                                <button type="submit" class="button primary">Login</button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
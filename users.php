<?php
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}




// DB_USERS
$query="SELECT `users`.`username`, `users`.`score`, `users`.`date_registration` FROM `users` ORDER BY `username` ASC";
$data=$db_conn->query($query);
$db_users = $data->fetchAll(PDO::FETCH_ASSOC);

// DB_TOP_USERS
$query="SELECT `users`.`username`, `users`.`score` FROM `users` ORDER BY `users`.`score` DESC LIMIT 10";
$data=$db_conn->query($query);
$db_top_users = $data->fetchAll(PDO::FETCH_ASSOC);

// DB_LAST_USERS
$query="SELECT  `users`.`username`, `users`.`date_registration` FROM `users` ORDER BY `users`.`date_registration` DESC LIMIT 10";
$data=$db_conn->query($query);
$db_last_users = $data->fetchAll(PDO::FETCH_ASSOC);


?>

<html>
<head>
    <?php include 'header.php'; ?>
</head>

<body>
<div class="container">
    <h1>Users</h1>

    <?php include 'navbar.php'; ?>

    <div class="row">
        <div class="col-8">
            <h2>All Users</h2>
            <div class="card">
                <div class="row">
                    <div class="col-12">



                <table class="striped">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Level</th>
                            <th>Score</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        foreach ($db_users as $db_user)
                        {
                        ?>
                        <tr>
                            <td><a href="user.php?user=<?=$db_user['username'] ?>"><b>@<?=$db_user['username'] ?></b></a></td>
                            <td><?=get_user_level($db_user['username']) ?></td>
                            <td><?=round($db_user['score']) ?></td>
                            <td class="is-right"><a href="user.php?user=<?=$db_user['username'] ?>">&nbsp;View&nbsp;</a></td>

                        </tr>
                        <?php
                                            }
                                            ?>


                    </tbody>
                </table>




                    </div>
                </div>

            </div>






            <p></p>
            <h2>Top 10</h2>
                <div class="card">
                    <div class="row">
                        <div class="col-12">
                            <header>
                                <h4>Best Users by score</h4>
                            </header>


                            <table class="striped">
                                <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Username</th>
                                    <th>Score</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>







                                <?php
                                $top_users  = array_slice($db_top_users, 0, 10);
                                $i=0;
                                foreach ($top_users as $top_user)
                                {
                                    $i++;
                                ?>
                                <tr>
                                    <td><?=ordinal($i) ?></td>
                                    <td><a href="user.php?user=<?=$top_user['username'] ?>"><b>@<?=$top_user['username'] ?></b></a></td>
                                    <td><?=$top_user['score'] ?></td>
                                    <td class="is-right"><a href="user.php?user=<?=$top_user['username'] ?>">&nbsp;View&nbsp;</a></td>

                                </tr>
                                <?php
                                                }
                                                ?>






                                </tbody>
                            </table>


                    </div>
                </div>
            </div>







        </div>


        <div class="col-4">





            <h2>Find User</h2>
            <div class="card bg-light">
                <header>
                    <h4>Go to</h4>
                </header>


                <form action="user.php" method="get">

                    <p>
                        <label for="input-user">Username:</label>
                        <input type="text" id="input-user" name="user" placeholder="Example: <?= mysession_get_user()?>" required>
                    </p>

                    <footer class="is-right">
                        <button type="submit" class="button primary">Go</button>
                    </footer>
                </form>

            </div>






            <p></p>
            <h2>New Users</h2>
            <div class="card">
                <div class="row">
                    <div class="col-12">
                <header>
                    <h4>Last 5 Subscribed Users</h4>
                </header>






                        <table class="striped">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>






                            <?php
                            $last_users  = array_slice($db_last_users, 0, 5);
                            $i=0;
                            foreach ($last_users as $last_user)
                            {
                                $i++;
                                ?>
                                <tr>
                                    <td><a href="user.php?user=<?=$last_user['username'] ?>"><b>@<?=$last_user['username'] ?></b></a></td>

                                    <td class="is-right"><?=time2string(strtotime($last_user['date_registration'])) ?></td>

                                </tr>
                                <?php
                            }
                            ?>




                            </tbody>
                        </table>


                    </div>
                </div>
            </div>


            <p></p>
            <h2>Invite some friends</h2>
            <div class="card bg-light">
                <header>
                    <h4>Will be sended a magic mail</h4>
                </header>


                <form action="rideadd.php" method="post">

                    <p>
                        <label for="car">Friend email:</label>
                        <input type="text" id="car" placeholder="friend@mail.com" required>
                    </p>

                    <footer class="is-right">
                        <button type="submit" class="button primary">Invite</button>
                    </footer>
                </form>

            </div>

        </div>
    </div>
</div>
</body>
</html>




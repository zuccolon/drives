<?php
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}


$db_maps = getListOfLocations();



// DB_RIDE
$query="SELECT * FROM `rides` ORDER BY `id_ride` ASC";
$data=$db_conn->query($query);
$db_ride = $data->fetchAll(PDO::FETCH_ASSOC);

// DB_LAST_USERS
$query="SELECT  `users`.`username`, `users`.`date_registration` FROM `users` ORDER BY `users`.`date_registration` DESC LIMIT 10";
$data=$db_conn->query($query);
$db_last_users = $data->fetchAll(PDO::FETCH_ASSOC);

//DB_USER_OFFING_RIDES

$stmt = $db_conn->prepare("SELECT * FROM `rides` WHERE `username` = (:username) AND `rides`.`date` > NOW() ORDER BY `date` ASC");
$stmt->bindParam(':username', $userx);

$userx = $_GET['user'];
$stmt->execute();

$db_user_offing_rides = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<html>
<head>
    <?php include 'header.php'; ?>
</head>

<body>
<div class="container">
    <h1><a href="users.php">Users</a> → @<?= $_GET['user'];?></h1>

    <?php include 'navbar.php'; ?>

    <div class="row">
        <div class="col-8">
<?php
            if(!user_exist($_GET['user'])){
                ?>
                <blockquote>
                    <h4>User not found</h4>
                    <p>Unfortunately we have not found the user <?= $_GET['user'];?></p>
                    <cite><a href="users.php">Find some users here.</a></cite>
                </blockquote>
                <?php
            }
            else {

                ?>
                <h2>About User: @<?= $_GET['user']; ?></h2>
                <div class="card">
                    <div class="row">
                        <div class="col-12">
                            <header>
                                <h5><?php if(!is_mobile()){ ?><b>@<?= $_GET['user']; ?></b><?php } ?>
                                    <div class="pull-right">Level: <?= get_user_level($_GET['user']) ?> |
                                        Score: <?= get_user_score($_GET['user']) ?> |
                                        Rank: <?= get_user_rank($_GET['user']) ?></div>
                                </h5>
                            </header>

                            <br>
                            <h3 class="is-center text-grey">Magic Badgess are Coming Soon</h3>
                            <!--- BADGES ---
                            <div class="row is-center">
                                <div class="col-4">
                                    <div class="card bg-primary">
                                        <header>
                                            <h4>Subscribed</h4>
                                        </header>
                                        <p>Siccome ti sei iscritto ti meriti questo badge.</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-primary">
                                        <header>
                                            <h4>Background Photo Created</h4>
                                        </header>
                                        <p>Siccome hai caricato una foto profilo, vinci.</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-primary">
                                        <header>
                                            <h4>You are an hacker</h4>
                                        </header>
                                        <p>Lorem ipsum dolor sit amet, consectetur/</p>
                                    </div>
                                </div>
                            </div>
                            --->
                        </div>
                    </div>

                </div>


                <p></p>
                <?php if (!empty($db_user_offing_rides)) {

                    ?>

                    <h2>@<?= $_GET['user']; ?>'s offing rides</h2>
                    <div class="card">
                        <div class="row">
                            <div class="col-12">


                                <table class="striped">
                                    <thead>
                                    <tr>
                                        <th>Map</th>
                                        <th>When</th>
                                        <th>With</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    <?php


                                    foreach ($db_user_offing_rides as $db_user_offing_ride) {
                                        ?>
                                        <tr>
                                            <td><?= ucfirst($db_user_offing_ride['origin']) ?>
                                                ➫ <?= ucfirst($db_user_offing_ride['destination']) ?></td>
                                            <td><?= date("d.m.Y - H:i", strtotime($db_user_offing_ride['date'])) ?></td>
                                            <td><?php
                                                $participants = unserialize($db_user_offing_ride['members']);
                                                if (!empty($participants)) {
                                                    foreach ($participants as $participant) {
                                                        echo strtolower("<a href='user.php?user=$participant'>@" . $participant . "</a>, ");
                                                    }
                                                } else {
                                                    echo "Anybody";
                                                }


                                                ?></td>
                                            <td class="is-right"><a
                                                        href="ride.php?ride=<?= $db_user_offing_ride['id_ride'] ?>">&nbsp;•••&nbsp;</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>

                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>

                <?php } else {
                    ?>
                    <blockquote><h5>User does not offer rides now... <a href="rides.php">Find more here.</a></h5>
                    </blockquote>
                    <?php
                }
                ?>


                <?php
            }
?>
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


        </div>
    </div>
</div>
</body>
</html>




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
$db_rides = $data->fetchAll(PDO::FETCH_ASSOC);


$stmt = $db_conn->prepare("SELECT * FROM `rides` WHERE `id_ride` = (:getted_id_ride) ORDER BY `id_ride` DESC");
$stmt->bindParam(':getted_id_ride', $getted_id_ride);

// insert one row
$getted_id_ride = intval($_GET['ride'] ?? 0);

$stmt->execute();
if(ride_exist($_GET['ride'])) {
    $current_ride = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];




$stmt = $db_conn->prepare("SELECT * FROM `rides` WHERE `username` = (:username) AND `rides`.`date` > NOW() ORDER BY `date` ASC");
$stmt->bindParam(':username', $userx);

$userx = $current_ride['username'];
$stmt->execute();

$db_user_offing_rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$db_ride_comments = get_ride_comments($_GET['ride']);


?>

<html>
<head>
    <?php include 'header.php'; ?>
</head>

<body>
<div class="container">
    <h1><a href="rides.php">Rides</a> → #<?= $_GET['ride'];?></h1>


    <?php include 'navbar.php'; ?>

    <div class="row">
        <div class="col-8">

<?php
            if(!ride_exist($_GET['ride'])){
                ?>
                <blockquote>
                    <h4>Ride not found</h4>
                    <p>Unfortunately we have not found the ride #<?= $_GET['ride'];?></p>
                    <cite><a href="rides.php">Find some ride here.</a></cite>
                </blockquote>
            <?php
            }
            else{
?>

            <h2>About Ride #<?= $_GET['ride']; ?></h2>
            <div class="card">

                <div class="row">
                    <div class="col-12">
                        <header>
                            <h3>
                                <b><a href="user.php?user=<?= $current_ride['username'] ?>">@<?= $current_ride['username']; ?></a></b>
                                • <?= ucwords($current_ride['car']); ?>
                                <div class="pull-right">#<?= $_GET['ride']; ?></div>
                            </h3>
                            <h4><b>At:</b> <?= date("d.m.Y - H:i", strtotime($current_ride['date'])) ?></h4>
                        </header>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <?= $current_ride['description']; ?>
                    </div>

                    <div class="col-6">
                        <b><?= ucfirst($current_ride['origin']); ?> ➫ <?= ucfirst($current_ride['destination']); ?></b>
                        <?php
                        $paths = unserialize($current_ride['through']);
                        ?>
                        <div class="is-text-left text-grey">
                            <?php
                            foreach ($paths as $path) {
                                echo ucfirst($path) . ", ";
                            } ?>.
                        </div>
                    </div>
                    <div class="col">
                        <b>Total Seats:</b> <?= $current_ride['seats']; ?><br>
                        <b>Seats Available:</b> <?= get_ride_seats_available($_GET['ride']) ?><br>
                        <b>Partecipants:</b> <?php

                        $ride_members = get_ride_members($_GET['ride']);
                        if (!empty($ride_members)) {
                            foreach ($ride_members as $ride_member) {
                                echo "<a href='user.php?user=$ride_member'>@" . strtolower($ride_member) . "</a> ";
                            }
                        } else {
                            echo "Anybody";
                        }


                        ?>
                    </div>

                </div>
                <form action="participate.php" method="POST">
                <div class="is-right">

                        <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2F<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"
                           target="_blank" rel="noopener noreferrer" class="button icon-only">
                            <img src="https://icongr.am/material/facebook.svg?size=16">
                        </a>

                        <?php
                        if (is_mobile()) {
                            ?>
                            &nbsp;&nbsp;<a href="whatsapp://send?text=Hey%20check%20out%20this%20awesome%20ride%20at%20http%3A%2F%2F<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"
                               class="button icon-only">
                                <img src="https://icongr.am/material/whatsapp.svg?size=16">
                            </a>
                            <?php
                        }
                        ?>

                        <?php

                        if (mysession_get_user() == $current_ride['username']){
                        ?>

                            <a class="button success">Made by you</a>



                <?php
                }
                else if (strtotime($current_ride['date']) < time()) {
                    ?>



                    <button type="button" class="button" disabled>Archived</button>




                    <?php
                } else if (is_member_in_ride($_GET['ride'], mysession_get_user())) {
                    ?>



                    <a class="button success">You are in!</a>




                    <?php
                } else {
                    ?>



                            <input type="hidden" name="ride_id" value="<?= $_GET['ride'] ?>"/>

                    <button type="submit" class="button primary">Add Me</button>





                    <?php
                }
                ?>

            </div>
                </form>
        </div>



            <?php if (!empty($db_user_offing_rides)) {

                ?>





                <h2>Ride Discussion</h2>

                    <div class="card bg-light">
                        <header>
                            <h4>Add a comment</h4>
                        </header>
                        <form action="commentride.php" method="post">
                            <textarea style="resize: none;" id="comment" name="comment" rows="5" placeholder="Enter your message here"></textarea>
                            <input type="hidden" name="ride_id" value="<?= $_GET['ride'] ?>"/>
                            <p></p>
                            <footer class="is-right">
                                <button type="submit" class="button primary">Comment</button>
                            </footer>
                        </form>
                    </div>


                    <!-- STAMPO I COMMENTI -->

                    <?php
                    if (!empty($db_ride_comments)) {
                        foreach ($db_ride_comments as $db_ride_comment)
                            ?>
                            <br>

                            <div class="card" style="background-color: #FBFFB5;">
                            <header>
                            <h4><a href="user.php?user=<?= $db_ride_comment['user'] ?>">
                        @<?= $db_ride_comment['user'] ?></a> • <?= time2string($db_ride_comment['date']) ?>
                        </h4>
                        </header>
                        <p><?= $db_ride_comment['comment'] ?></p>
                        </div>

                        <?php
                    }
                    ?>









                    <h2>Other rides from this user <a href="user.php?user=<?= $current_ride['username'] ?>">@<?= $current_ride['username']; ?></a></h2>
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


            <!--
            <p></p>
            <h2>Other rides in this Date</h2>
            <div class="card">
                <div class="row">
                    <div class="col-12">



                        <table class="striped">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Map</th>
                                <th>When</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>








                            <?php


            $reversed_db_ride = array_reverse($db_rides);
            $last_rides = array_slice($reversed_db_ride, 0, 5);

            foreach ($last_rides as $last_ride) {
                ?>
                                <tr>
                                    <td><b>@user</b></td>
                                    <td><?= $last_ride['origin'] ?> ➫ <?= $last_ride['destination'] ?></td>
                                    <td><?= date("d.m.Y - H:i", strtotime($last_ride['date'])) ?></td>
                                    <td class="is-right"><a href="ride.php">&nbsp;•••&nbsp;</a></td>
                                </tr>
                                <?php
            }
            ?>







                            </tbody>
                        </table>


                    </div>
                </div>
            </div>


--->
                <?php
            }
            ?>

        </div>


        <div class="col-4">





            <h2>Create Ride</h2>
            <div class="card bg-light">
                <header>
                    <h4>Create Ride</h4>
                </header>


                <form action="rideadd.php" method="post">

                    <p>
                        <label for="origin">From</label>
                        <select name="origin" id="origin">

                            <?php for ($i=0; $i< count($db_maps); $i++){
                                echo "<option value='$db_maps[$i]'>" . ucfirst($db_maps[$i])."</option>";
                            }?>

                        </select>
                    </p>
                    <p>
                        <label for="destination">To</label>
                        <select name="destination" id="destination">

                            <?php for ($i=0; $i< count($db_maps); $i++){
                                echo "<option value='$db_maps[$i]'>" . ucfirst($db_maps[$i])."</option>";
                            }?>

                        </select>
                    </p>
                    <p>
                        <label for="description">Description</label>
                        <textarea style="resize: none;" name="description" id="description" rows="3" placeholder="Vado a Besazio a mangiare la polenta" required></textarea>
                    </p>
                    <p>
                        <label for="date">Date</label>
                        <input name="date" id="date" value="<?php echo date("Y-m-d");?>" type="date" required>
                    </p>
                    <p>
                        <label for="time">Time</label>
                        <input name="time" id="time" value="<?php echo date("H:i");?>"type="time" required>
                    </p>
                    <p>
                        <label for="car">Car</label>
                        <input type="text" name="car" id="car" placeholder="Renault Clio RS" required>
                    </p>
                    <p>
                        <label for="seats">Seats Available</label>
                        <input name="seats" id="seats" min="1" max="13" value="3" type="number" required>
                    </p>

                    <footer class="is-right">
                        <button type="submit" class="button primary">Create</button>
                    </footer>
                </form>





            </div>










            <h2>Find Ride</h2>
            <div class="card bg-light">
                <header><h4>Search Here</h4></header>
                <form action="index.php" method="get">
                    <div class="row">
                        <div class="col-6">
                            <p>
                                <label for="find_from">From</label>
                                <select name="find_from" id="find_from">
                                    <option selected disabled>...</option>
                                    <?php for ($i=0; $i< count($db_maps); $i++){
                                        echo "<option value='$db_maps[$i]'>" . ucfirst($db_maps[$i])."</option>";
                                    }?>

                                </select>
                            </p>
                        </div>
                        <div class="col-6">
                            <p>
                                <label for="find_to">To</label>
                                <select name="find_to" id="find_to">
                                    <option selected disabled>...</option>
                                    <?php for ($i=0; $i< count($db_maps); $i++){
                                        echo "<option value='$db_maps[$i]'>" . ucfirst($db_maps[$i])."</option>";
                                    }?>

                                </select>
                            </p>
                        </div>
                    </div>
                    <footer class="is-right">
                        <button type="submit" class="button primary">Find</button>
                    </footer>
                </form>

            </div>



        </div>


        </div>
    </div>
</div>
</body>
</html>




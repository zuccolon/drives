<?php
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}


// DB_MAPS
$query="SELECT DISTINCT `origin` FROM `maps` ORDER BY `maps`.`origin` ASC";
$data=$db_conn->query($query);
$results = $data->fetchAll(PDO::FETCH_ASSOC);

$db_maps = [];


// Aggiungo i posti all'array db_maps
foreach ($results as $row) {
    $db_maps[] = $row['origin'];
}


// DB_RIDES_DATE_ASC
$query="SELECT * FROM `rides` WHERE `rides`.`date` > NOW() ORDER BY `rides`.`date` ASC LIMIT 10";
$data=$db_conn->query($query);
$db_rides_date_asc = $data->fetchAll(PDO::FETCH_ASSOC);

// DB_RIDES_FUTURE
$query="SELECT * FROM `rides` WHERE `rides`.`date` > NOW()";
$data=$db_conn->query($query);
$db_rides_future = $data->fetchAll(PDO::FETCH_ASSOC);

// DB_RIDES_last_added
$query="SELECT * FROM `rides` WHERE `rides`.`date` > NOW() ORDER BY `rides`.`id_ride` DESC LIMIT 10";
$data=$db_conn->query($query);
$db_rides_last_added = $data->fetchAll(PDO::FETCH_ASSOC);

//print_r($db_ride);
//echo sizeof($db_ride[0]);
//echo $db_ride[0][0]['user'];




?>

<html>
<head>
    <?php include 'header.php'; ?>
</head>

<body>
<div class="container">
    <h1>Rides</h1>


    <?php include 'navbar.php'; ?>



    <div class="row">
        <div class="col-8">




            <h2>All Rides</h2>
            <?php
            if (!empty($db_rides_future)){
                ?>
            <div class="card">
                <div class="row">
                    <div class="col-12">



                        <table class="striped">
                            <thead>
                            <tr>
                                <th>Organizer</th>
                                <th>Map</th>
                                <th>When</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>



                            <?php


                            foreach ($db_rides_future as $db_ride_future)
                            {
                                ?>
                                <tr>
                                    <td><a href="user.php?user=<?=$db_ride_future['username'] ?>"><b>@<?=$db_ride_future['username'] ?></b></a></td>
                                    <td><?=ucfirst($db_ride_future['origin']) ?> ➫ <?=ucfirst($db_ride_future['destination']) ?></td>
                                    <td><?=time2string(strtotime($db_ride_future['date'])) ?></td>
                                    <td class="is-right"><a href="ride.php?ride=<?=$db_ride_future['id_ride'] ?>">&nbsp;•••&nbsp;</a></td>

                                </tr>
                                <?php
                            }
                            ?>





                            </tbody>
                        </table>




                    </div>
                </div>

            </div>

            <?php
            }
            else{
                echo "<blockquote><h5>There aren't rides in the future</h5></blockquote>";
            }
            ?>



            <p></p>
            <h2>Coming Rides</h2>
            <?php
            if (!empty($db_rides_future)){
            ?>
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <table class="striped">
                            <thead>
                            <tr>
                                <th>Organizer</th>
                                <th>Map</th>
                                <th>When</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>





                            <?php

                            $last_rides_date         = array_slice($db_rides_date_asc, 0, 5);

                            foreach ($last_rides_date as $last_ride_date)
                            {
                                ?>
                                <tr>
                                    <td><a href="user.php?user=<?=$last_ride_date['username'] ?>"><b>@<?=$last_ride_date['username'] ?></b></a></td>
                                    <td><?=ucfirst($last_ride_date['origin']) ?> ➫ <?=ucfirst($last_ride_date['destination']) ?></td>
                                    <td><?=time2string(strtotime($last_ride_date['date'])) ?></td>
                                    <td class="is-right"><a href="ride.php?ride=<?=$last_ride_date['id_ride'] ?>">&nbsp;•••&nbsp;</a></td>

                                </tr>
                                <?php
                            }
                            ?>









                            </tbody>
                        </table>


                    </div>
                </div>
            </div>

                <?php
            }
            else{
                echo "<blockquote><h5>There aren't rides in the future</h5></blockquote>";
            }
            ?>



            <p></p>
            <h2>New Rides</h2>
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <table class="striped">
                            <thead>
                            <tr>
                                <th>Organizer</th>
                                <th>Map</th>
                                <th>When</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>




                            <?php

                            $last_rides_added         = array_slice($db_rides_last_added, 0, 5);

                            foreach ($last_rides_added  as $last_ride_added )
                            {
                                ?>
                                <tr>
                                    <td><a href="user.php?user=<?=$last_ride_added['username'] ?>"><b>@<?=$last_ride_added['username'] ?></b></a></td>
                                    <td><?=ucfirst($last_ride_added['origin']) ?> ➫ <?=ucfirst($last_ride_added['destination']) ?></td>
                                    <td><?=time2string(strtotime($last_ride_added['date'])) ?></td>
                                    <td class="is-right"><a href="ride.php?ride=<?=$last_ride_added['id_ride'] ?>">&nbsp;•••&nbsp;</a></td>

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
            <h2>Archive</h2>

            <blockquote><h5>To enter into archive go <a href="ridearchive.php">Here.</a></h5></blockquote>










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




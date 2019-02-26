<?php
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}

$db_maps = getListOfLocations();



// DB_RIDE_id_ride_ASC
$query="SELECT * FROM `rides` ORDER BY `id_ride` ASC";
$data=$db_conn->query($query);
$db_rides_id_ride_asc = $data->fetchAll(PDO::FETCH_ASSOC);

// DB_RIDE_DATE_ASC
$query="SELECT * FROM `rides` WHERE `rides`.`date` > NOW() ORDER BY `rides`.`date` ASC LIMIT 10";
$data=$db_conn->query($query);
$db_rides_date_asc = $data->fetchAll(PDO::FETCH_ASSOC);


if(isset($_GET['find_from']) && isset($_GET['find_to'])){
    $search_results = get_results($_GET['find_from'], $_GET['find_to']);
    $search_results = SearchUtils::get_rides_between_areas($_GET['find_from'], $_GET['find_to'], $db_conn);
}



?>

<html>
        <head>
            <?php include 'header.php'; ?>
            <style>
                .bg_img {
                    /* The image used */
                    background-image: url("img/2home-find-here.jpg");

                    /* Full height */


                    /* Center and scale the image nicely */
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                }
            </style>
        </head>

        <body>


            <div class="container">

                <h1>︎️Welcome <a href="user.php?user=<?= mysession_get_user() ?>">@<?= mysession_get_user() ?></a></h1>


                <?php include 'navbar.php'; ?>




                <div class="row">
                        <div class="col-8">
                            <h2>Find Ride</h2>
                            <div class="card bg-light bg_img">
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



                            <?php if(!empty($search_results)){
                                ?>




                                <p></p>
                                <h2>Find <?php echo ucfirst($_GET['find_from']);?> ➫ <?php echo ucfirst($_GET['find_to']);?></h2>


                                <?php
                                foreach ($search_results as $search_result){
                                ?>

                                    <div class="card">
                                        <div class="row">
                                                <div class="col-12">
                                                <header>
                                        <b><a href="user.php?user=<?=$search_result['username']?>">@<?=$search_result['username']?></a></b> • <?=$search_result['car']?> <div class="pull-right"><?=date("d.m.Y - H:i", strtotime($search_result['date']))?></div>
                                                </header>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-8">
                                                <?=$search_result['description']?>
                                                <br>
                                            </div>

                                            <div class="col-4">
                                                <b><?php echo ucfirst($search_result['origin'])?>  ➫ <?php echo ucfirst($search_result['destination'])?></b>
                                                <br>
                                                <!--- <div class="is-text-left text-grey">Minusio, Tenero, Giubiasco, Rivera, Taverne, Lugano, Paradiso, Maroggia, Melano, Mendrisio</div> --->
                                            </div>
                                        </div>
                                        <form action="participate.php" method="post">
                                            <footer>
                                                <input type="hidden" name="ride_id" value="<?= $search_result['id_ride'] ?>"/>
                                                <div class="pull-right text-grey">
                                                    <?php

                                                    $ride_members = unserialize($search_result['members']);
                                                    if(!empty($ride_members)){
                                                        foreach ($ride_members as $ride_member){
                                                            echo "<a href='user.php?user=$ride_member'>@" . strtolower($ride_member) . "</a> ";
                                                        }
                                                    }
                                                    else{
                                                        echo "Anybody";
                                                    }
                                                    ?>

                                                    <button type="submit" class="button primary">Add Me</button></div><br>
                                            </footer>
                                        </form>
                                    </div>





                            <?php
                                }
                            }
                            else if (isset($_GET['find_from']) && isset($_GET['find_to'])){
                                ?>
                                <p></p>
                                <h2>Find <?php echo ucfirst($_GET['find_from']);?> ➫ <?php echo ucfirst($_GET['find_to']);?></h2>
                                <blockquote>
                                    <div class="row">
                                        <div class="col-8">
                                            <h4 class="text-error">Rides from <?=ucfirst($_GET['find_from'])?> to <?=ucfirst($_GET['find_to'])?> not found</h4>
                                            <h5 class="text-success">Some solutions here:</h5>
                                        </div>
                                        <div class="col-4">
                                            <ul>
                                                <li>Create a Ride</li>
                                                <li><a href="rides.php">See some rides Here</a></li>
                                                <li><a href="ridearchive.php">Go to archive</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </blockquote>





                                <?php
                            }
                            ?>





                            <?php
                            $last_rides_date = array_slice($db_rides_date_asc, 0, 5);
                            if (!empty($last_rides_date)){
                            ?>


                            <p></p>
                            <h2>Coming Rides</h2>
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
                                ?>
                                <blockquote><h5>There aren't coming rides...<a href="rides.php">Find more here.</a></h5></blockquote>
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
                                        <textarea style="resize: none;" name="description" id="description" rows="3" placeholder="Vado a Besazio a mangiare la polenta"></textarea>
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
                        </div>
                    </div>
            </div>
        </body>
    </html>




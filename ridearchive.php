<?php
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}


// DB_RIDES
$query="SELECT * FROM `rides` WHERE `rides`.`date` < NOW() ORDER BY `rides`.`id_ride` ASC";
$data=$db_conn->query($query);
$db_rides = $data->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
    <?php include 'header.php'; ?>
</head>

<body>
<div class="container">
    <h1>Ride Archive</h1>

    <?php include 'navbar.php'; ?>


    <div class="row">
        <div class="col-12">
            <h2>All Rides until now</h2>
            <div class="card">
                <div class="row">
                    <div class="col-12">



                        <table class="striped">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Organizer</th>
                                <th>Car</th>
                                <th>Description</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Through</th>
                                <th>Seats</th>
                                <th>Date</th>
                                <th>Members</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            foreach ($db_rides as $db_ride)
                            {
                                $paths = unserialize($db_ride['through']);
                                $members = unserialize($db_ride['members']);
                                ?>
                                <tr>
                                    <td><a href="ride.php?ride=<?=$db_ride['id_ride'] ?>"><b>#<?=$db_ride['id_ride'] ?></b></a></td>
                                    <td><a href="user.php?user=<?=$db_ride['username'] ?>"><b>@<?=$db_ride['username'] ?></b></a></td>
                                    <td><?=$db_ride['car'] ?></td>
                                    <td><?=$db_ride['description'] ?></td>
                                    <td><?=ucfirst($db_ride['origin']) ?></td>
                                    <td><?=ucfirst($db_ride['destination']) ?></td>
                                    <td><?php
                                        foreach ($paths as $path) {
                                            echo ucfirst($path) . ", ";
                                        }
                                        ?></td>
                                    <td><?=$db_ride['seats'] ?></td>
                                    <td><?=date("d.m.Y - H:i", strtotime($db_ride['date'])) ?></td>
                                    <td>
                                        <?php
                                        if(!empty($members)){
                                            foreach ($members as $member){
                                                echo "<a href='user.php?user=$member'>@$member</a>, ";
                                            }
                                        }
                                        else{
                                            echo "Anybody";
                                        }

                                        ?>
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
        </div>
    </div>
</div>
</body>
</html>




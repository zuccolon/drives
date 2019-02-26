<?php
error_reporting(E_ALL);
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}
// CONTROLLO CHE LE VARIABILI SIANO PASSATE
$missing_data = null;
if(isset($_POST['origin'])==false){
    $missing_data = $missing_data . "From, ";
}
if(isset($_POST['destination'])==false){
    $missing_data = $missing_data . "To, ";
}
if(isset($_POST['description'])==false){
    $missing_data = $missing_data . "Description, ";
}
if(isset($_POST['date'])==false){
    $missing_data = $missing_data . "Date, ";
}
if(isset($_POST['time'])==false){
    $missing_data = $missing_data . "Time, ";
}
if(isset($_POST['car'])==false){
    $missing_data = $missing_data . "Car, ";
}
if(isset($_POST['seats'])==false){
    $missing_data = $missing_data . "Seats, ";
}
if(isset($missing_data)){
    die ("You don't have setted: " . $missing_data);
}

// CONTROLLO CHE NELLE VARIAIBLI CI SIANO DATI SODDISFACENTI
$errors = null;
if($_POST['origin']==$_POST['destination']){
    $errors = $errors . "La destinazione non può essere l'origine ";
}
if(strlen($_POST['description'])<10){
    $errors = $errors . "La descrizione è troppo corta, ";
}
if(strlen($_POST['car'])<3){
    $errors = $errors . "metti un nome di un'auto come si deve, ";
}
if(isset($errors)){
    header("location:javascript://history.go(-1)");

    die ("Errors: " . $errors);
}


$stmt = $db_conn->prepare("INSERT INTO `rides` (`id_ride`, `username`, `car`, `description`, `origin`, `destination`, `through`, `seats`, `date`, `members`, `comments`) VALUES (NULL, :organizator, :car, :description, :origin, :destination, :through, :seats, :ride_date, '', 'a:0:{}')");
$stmt->bindParam(':organizator', $var_organizator);
$stmt->bindParam(':car', $var_car);
$stmt->bindParam(':description', $var_description);
$stmt->bindParam(':origin', $var_origin);
$stmt->bindParam(':destination', $var_destination);
$stmt->bindParam(':through', $var_through);
$stmt->bindParam(':seats', $var_seats);
$stmt->bindParam(':ride_date', $ride_date);

// insert one row
$var_organizator = mysession_get_user();
$var_car = $_POST['car'];
$var_description = $_POST['description'];
$var_origin = $_POST['origin'];
$var_destination = $_POST['destination'];
$var_through = serialize(getPath($_POST['origin'], $_POST['destination'])); // STEFANO
$var_seats = $_POST['seats'];
$ride_date = $_POST['date'] . " " . $_POST['time'] . ":00";

score_incarse(mysession_get_user(), '2');

$stmt->execute();

echo "<h1>Added, Go <a href='rides.php'>Here</a></h1>";
header("location: ride.php?ride=" . get_last_ride_added());
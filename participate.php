<?php
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}
$userino = mysession_get_user();
$rideino = $_POST['ride_id'];



// CONTROLLO CHE ESISTONO LE VARIABILI PASSATE
$cosa_manca = null;
if(!isset($userino)){
    $cosa_manca = $cosa_manca . "user, ";
}
if(!isset($rideino)){
    $cosa_manca = $cosa_manca . "id_ride, ";
}
print_r($cosa_manca);
echo "<br><br>";
if(!empty($cosa_manca)){
    echo "manca " . $cosa_manca;
    die();
}

// CONTROLLO CHE LE VARIABILI PASSATE SIANO CONSISTENTI (ID SOLO NUMERI E USERNAME SOLO LETTERE E NUMERI)
if(!is_numeric($rideino)){
    echo $rideino . " non è un numero";
    die();
}

if(!preg_match('/^([a-zA-Z0-9]{3,12})$/', $userino)){
    die("L'username puo' solo avere lettere o numeri e deve essere lungo dai 3 ai 12 caratteri");
}

// CONTROLLO CHE L'UTENTE ESISTE
if (!user_exist($userino)) {
    die("user does not exist");
}



//CONTROLLO CHE IL RIDE ESISTE


$stmt = $db_conn->prepare("SELECT *  FROM `rides` WHERE `id_ride` = (:id_ride) LIMIT 1");
$stmt->bindParam(':id_ride', $id_xride);

$id_xride = $rideino;

$stmt->execute();
$ride_from_database = $stmt->fetch(PDO::FETCH_ASSOC);

if(!is_array($ride_from_database)){
    die("Ride not found");
}


// CONTROLLO CHE IL RIDE NON SIA NEL PASSATO
if (strtotime($ride_from_database['date'])<time()){
    die("Il ride è nel passato");

}

// ESTRAGGO DAL DATABASE L'ARRAY E LO ASSEGNO A UNA VARIABILE

/////////// GIA FATTO, LA VARIABILE È $ride_from_database

// CONTROLLO CHE CI SIA ALMENO UN POSTO LIBERO
if(get_ride_seats_available($rideino)<1){
    die("There aren't enough seats");
}


// CONTROLLO CHE L'UTENTE NON SIA IL PROPRIETARIO
if($ride_from_database['username']==$userino){
    die("User is the owner of the ride");
}


// CONTROLLO CHE L'UTENTE NON CI SIA GIA
if(is_member_in_ride($rideino, $userino)){
    die("User already participates in the ride");
}


// UNSERIALIZZO L'ARRAY
$array_participants = unserialize($ride_from_database['members']);


if ($array_participants==null){
    echo "Sei il primo utente che si iscrive a questo ride!";
    $array_participants = array();
}

print_r($array_participants);


// APPENDO L'UTENTE ALL'ARRAY
$user_to_add = $userino;
array_push($array_participants, $user_to_add);
print_r($array_participants);


// SERIALIZZO L'ARRAY
$array_participants_serialized = serialize($array_participants);

// INSERISCO NEL DATABASE L'ARRAY COL NUOVO UTENTE


$stmt = $db_conn->prepare("UPDATE `rides` SET `members` = (:members) WHERE `rides`.`id_ride` = (:id_ride);");
$stmt->bindParam(':id_ride', $id_xride);
$stmt->bindParam(':members', $xmembers);

// insert one row
$id_xride = $rideino;
$xmembers = $array_participants_serialized;
$stmt->execute();


// AGGIUNGO PUNTI A CHI SI AGGIUNGE AL RIDE
score_incarse(mysession_get_user(), '1');


echo "fatto.";
header("location: ride.php?ride=$rideino");

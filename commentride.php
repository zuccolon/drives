<?php
include 'functions.php';
if(mysession_check()==false){
    header("location: login.php");
    exit;
}

$ride = $_POST['ride_id'];
$comment = $_POST['comment'];


//estraggo dal database i commenti
$qualcosa = get_ride_comments($ride);

//appendo il commento
array_push($qualcosa, array("user"=>"melania", "comment"=>"Ciao questo fa un psuhs", "date"=>1546505128));
print_r($qualcosa);
//serializzo

//reinserisco il commento
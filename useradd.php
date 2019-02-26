<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'functions.php';


$cosa_manca = null;
if(isset($_POST['password'])==false){
    $cosa_manca = $cosa_manca . "Password, ";
}
if(isset($_POST['password_repeat'])==false){
    $cosa_manca = $cosa_manca . "Repeat Password, ";
}
if(isset($_POST['username'])==false){
    $cosa_manca = $cosa_manca . "username. ";
}

if(isset($cosa_manca)){
    header("location: register.php?message=Non hai passato abbastanza dati, manca: \" . $cosa_manca");
    die ("Non hai passato abbastanza dati, manca: " . $cosa_manca);
}

if(!preg_match('/^([a-zA-Z0-9]{3,12})$/', $_POST['username'])){
    header("location: register.php?message=L'username puo' solo avere lettere o numeri e deve essere lungo dai 3 ai 12 caratteri");
    die("L'username puo' solo avere lettere o numeri e deve essere lungo dai 3 ai 12 caratteri");
}

if(user_exist(strtolower($_POST['username']))){
    header("location: register.php?message=User \"" . $_POST['username'] . "\" is not available.");
    die("User \"" . $_POST['username'] . "\" is not available.");
}

if(strlen($_POST['password']) < 8){
    header("location: register.php?message=Password too short, must be at least 8 characters long.");
    die("La password e' troppo corta, deve essere di almeno 8 caratteri.");
}

if($_POST['password'] != $_POST['password_repeat']){
    header("location: register.php?message=Password does not match.");
    die ("Le password non corrispondono.");
}

if($_POST['username'] == $_POST['password']){
    die("The password and username can't be the same.");
}

echo "Hasho la password...<br>";

$user=$_POST['username'];
$pass = hash('sha512', ($_POST['password'] . "EssrbW5bmKZq"));

echo "Aggiungo utente...<br>";
$user = strtolower($user);

$sql = "INSERT INTO `users` (`username`, `password`, `score`) VALUES ('$user', '$pass', '0')";

if ($db_conn->query($sql) != TRUE) {
    die( "Error: " . $sql);
}

echo "<h3>Fatto! prego per di quà: <a href='login.php'>Al Login</a></h3>";
header("location: login.php?message=User @$user created.");


<?php
include "functions.php";

//if (isset($_POST['username']) == false || strlen($_POST['username']) < 3){
 //   header("location: login.php?message=Non hai passato alcun dato oppure l'username e' troppo corto.");
  //  die("Non hai passato alcun dato oppure l'username e' troppo corto");
//}

//if (!user_exist($_POST['username'])){
  //  header("location: login.php?message=User not found.");
    //die("Utente non trovato");
//}

if (mycredential_check($_POST['username'], $_POST['password'])){
    echo "Entrato<br>";

    mysession_start($_POST['username'], isset($_POST['remember']));
    header("location: index.php");

}

else {
    echo "incorrect login";
    header("location: login.php?message=The user and password you entered did not match our records.");
}
?>
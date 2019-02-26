<?php
/**
Piccola prova, ora sono in beta e non vengono usate (forse)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Zurich');

// connessione al server sql
$db_host = "localhost";
$db_name = "THE DB NAMEEEE";
$db_user = "YOUR USERNAME HEREEEEE";
$db_pass = "YOUR PASSWORD HEREEEEE";

try{
    $db_conn = new PDO("mysql:host=$db_host; dbname=$db_name", $db_user, $db_pass);
    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    die("Connection failed: " . $e->getMessage());
}

$query="SELECT * FROM `users`";
$data=$db_conn->query($query);
$results = $data->fetchAll(PDO::FETCH_ASSOC);

$db_users = [];

// Aggiungo gli utenti all'array users
foreach ($results as $row) {
    $db_users[$row['username']] = $row['password'];
}


include 'SearchUtils.php';


//INIZIO LE FUNZIONI
function user_exist($username){

    global $db_users;
    return isset($db_users[$username]);
}

function ride_exist($ride){
    global $db_conn;


    $stmt = $db_conn->prepare("SELECT * FROM `rides` WHERE `id_ride` = (:ride) ORDER BY `members` DESC");
    $stmt->bindParam(':ride', $xride);
    $xride = $ride;
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(empty($data)){
        return false;
    }
    else{
        return true;
    }
    return "qualquadra non cosa";
}

function mycredential_check($username, $password){

    global $db_users;

    if(strcasecmp($db_users[$username], hash('sha512', ($password . "EssrbW5bmKZq")))==0){
        return true;
    }

    return false;
}

function mysession_start($username, $save){

    global $db_conn;

    $piece_1 = "Dqe4RPW3GVqlNzd";
    $piece_2 = microtime(true);
    $piece_3 = $username;
    $fused_piece = $piece_1 . $piece_2 . $piece_3;

    $token = hash('sha512', $fused_piece);


    $sql = "INSERT INTO sessions (username, token) VALUES ('$username', '$token')";

    if ($db_conn->query($sql) != TRUE) {
        die( "Error: " . $sql);
    }

    if($save==true){
        return setcookie("logged_user", $token, time()+3600*24*8);
    }
    else{
        return setcookie("logged_user", $token, 0);
    }
}


function mysession_check(){
    global $db_users, $db_conn;

    if(array_key_exists("logged_user", $_COOKIE ))
    {
        $token = $_COOKIE['logged_user'];

        $query = "SELECT username FROM `sessions` WHERE token = '$token'";
        $data = $db_conn->query($query);
        $results = $data->fetch(PDO::FETCH_ASSOC);

        return isset($results['username']);
    }

    return false;
}


function mysession_close(){
    return setcookie("logged_user", "", time()-5);  /* delete */;
}



function mysession_get_user(){
    if(array_key_exists("logged_user", $_COOKIE )) {
        global $db_users, $db_conn;
        $token = $_COOKIE['logged_user'];

        $query = "SELECT username FROM `sessions` WHERE token = '$token'";
        $data = $db_conn->query($query);
        $results = $data->fetch(PDO::FETCH_ASSOC);

        return isset($results['username']) ? $results['username'] : "Guest";

    }

    return "Guest";

}

function time2string($timestamp){
    if(!ctype_digit($timestamp))
        $timestamp = strtotime($timestamp);

    $diff = time() - $timestamp;
    if($diff == 0)
        return 'now';
    elseif($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'last month';
        return date('F Y', $timestamp);
    }
    else
    {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 120) return 'in a minute';
            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if($diff < 7200) return 'in an hour';
            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if($day_diff == 1) return 'Tomorrow';
        if($day_diff < 4) return date('l', $timestamp);
        if($day_diff < 7 + (7 - date('w'))) return 'next week';
        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if(date('n', $timestamp) == date('n') + 1) return 'next month';
        return date('F Y', $timestamp);
    }
}

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function getListOfLocations()
{
    global $db_conn;

    // DB_MAPS
    $query="SELECT DISTINCT `origin` FROM `maps` ORDER BY `maps`.`origin` ASC";
    $data=$db_conn->query($query);
    $results = $data->fetchAll(PDO::FETCH_ASSOC);

    $db_maps = [];

    foreach ($results as $row) {
        $db_maps[] = $row['origin'];
    }

    return $db_maps;
}

$all_valid_paths_found = [];
function getPath($from, $to, $path_so_far = []){
    global $db_conn, $all_valid_paths_found;

    // First call only
    static $grafo;
    if ($grafo == null) {
        $grafo = [];
        foreach($db_conn->query("SELECT origin, destination FROM maps ORDER BY origin")->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($grafo[$row['origin']]) == false)
                $grafo[$row['origin']] = [];
            $grafo[$row['origin']][] = $row['destination'];
        }
    }

    // Add initial location
    $path_so_far[] = $from;

    // Check if arrived
    if ($from == $to)
    {
        return count($path_so_far) == 1 ? false : $path_so_far;
    }

    // If not arrived
    $valid_paths_found = [];
    $best_path_found = false;
    foreach ($grafo[$from] as $possible_destination)
    {
        // If possible destination already visited
        if (in_array($possible_destination, $path_so_far))
            continue;

        $search_result = getPath($possible_destination, $to, $path_so_far);

        if ($search_result != false) {
            $valid_paths_found[] = $search_result;
            $all_valid_paths_found[] = implode(', ', $search_result);
        }

    }

    foreach($valid_paths_found as $valid_path_found) {
        if ($best_path_found == false || count($valid_path_found) < count($best_path_found))
            $best_path_found = $valid_path_found;
    }

    return $best_path_found;
}

function get_user_score($username){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT score FROM users WHERE users.username = :user");
    $stmt->bindParam(':user', $user);

    $user = $username;
    $stmt->execute();

    $score = $stmt->fetch(PDO::FETCH_ASSOC)['score'];

    return $score;
}

function get_user_level($username){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT score FROM users WHERE users.username = :user");
    $stmt->bindParam(':user', $user);

    $user = $username;
    $stmt->execute();

    $score = $stmt->fetch(PDO::FETCH_ASSOC)['score'];

    if ($score < 3){
        return "Beginner";
    }
    if ($score < 6){
        return "Learner";
    }
    if ($score < 9){
        return "Rookie";
    }
    if ($score < 14){
        return "Novice";
    }
    if ($score < 19){
        return "Amateur";
    }
    if ($score < 27){
        return "Graduate";
    }
    if ($score < 33){
        return "Skilled";
    }
    if ($score < 40){
        return "Experienced";
    }
    if ($score < 49){
        return "Professional";
    }
    if ($score < 58){
        return "Hotshot";
    }
    if ($score < 69){
        return "Expert";
    }
    if ($score < 80){
        return "Wizard";
    }
    if ($score < 91){
        return "Ninja";
    }
    if ($score < 104){
        return "Super Star";
    }
    if ($score < 117){
        return "Barry";
    }
    if ($score < 128){
        return "Badger";
    }

return "Zuck";

}

function get_user_rank($username){
    global $db_conn;

    $query="SELECT users.username, users.score FROM `users` ORDER BY `users`.`score` DESC";
    $data=$db_conn->query($query);
    $results = $data->fetchAll(PDO::FETCH_ASSOC);

    for ($i=1; $i>0; $i++){
        if ($results[$i-1]['username']==$username){
            return $i;
        }

    }


}



function get_ride_seats_total($id_ride){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT `rides`.seats FROM `rides` WHERE id_ride = (:id_ride)");
    $stmt->bindParam(':id_ride', $id_xride);

    $id_xride = $id_ride;

    $stmt->execute();
    $total_seats = $stmt->fetch(PDO::FETCH_ASSOC)['seats'];

    return $total_seats;
}



function get_ride_seats_busy($id_ride){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT `rides`.members FROM `rides` WHERE `id_ride` = (:id_ride) ORDER BY `id_ride` ASC");
    $stmt->bindParam(':id_ride', $id_xride);

    $id_xride = $id_ride;

    $stmt->execute();
    $members = $stmt->fetch(PDO::FETCH_ASSOC)['members'];
    if(!empty($members)) {
        return sizeof(unserialize($members));
    }
    else{
        return 0;
    }
}



function get_ride_seats_available($id_ride){
    $seats_available = get_ride_seats_total($id_ride)-get_ride_seats_busy($id_ride);
    return $seats_available;
}



function get_ride_members($id_ride){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT `rides`.members FROM `rides` WHERE `id_ride` = (:id_ride) ORDER BY `id_ride` ASC");
    $stmt->bindParam(':id_ride', $id_xride);

    $id_xride = $id_ride;

    $stmt->execute();
    $members = $stmt->fetch(PDO::FETCH_ASSOC)['members'];
    return unserialize($members);
}

function is_member_in_ride($id_ride, $username){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT `rides`.members FROM `rides` WHERE `id_ride` = (:id_ride) ORDER BY `id_ride` ASC");
    $stmt->bindParam(':id_ride', $id_xride);

    $id_xride = $id_ride;

    $stmt->execute();
    $members = unserialize($stmt->fetch(PDO::FETCH_ASSOC)['members']);

    if ($members==null){
        $members = array();
    }


    if(in_array($username, $members)){
        return true;
    }
    else{
        return false;
    }

}

function score_incarse($username, $quantity){
    // ESTRAI LO SCORE DAL DATABASE
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT `users`.`score`  FROM `users` WHERE `username` LIKE (:username) ORDER BY `score`  DESC");
    $stmt->bindParam(':username', $userxname);
    $userxname = $username;
    $stmt->execute();
    $user_score = $stmt->fetch(PDO::FETCH_ASSOC)['score'];
    $new_user_score = $user_score + $quantity;

    // INSERISCI LO SCORE NEL DATABASE
    $stmt = $db_conn->prepare("UPDATE `users` SET `score` = (:new_score) WHERE `users`.`username` LIKE (:username)");
    $stmt->bindParam(':username', $userxname);
    $stmt->bindParam(':new_score', $new_score);
    $userxname = $username;
    $new_score = $new_user_score;
    $stmt->execute();


    return true;
}


function get_last_ride_added(){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT MAX(id_ride) AS ultimo FROM rides");
    $stmt->execute();
    $ultimo = $stmt->fetch(PDO::FETCH_ASSOC)['ultimo'];

    return $ultimo;
}


function get_results($origin, $destination){
    $rides = [];
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT *  FROM `rides` WHERE `origin` LIKE (:origin) AND `destination` LIKE (:destination) AND `date`>= NOW() ORDER BY `date` ASC");
    $stmt->bindParam(':origin', $xorigin);
    $stmt->bindParam(':destination', $xdestination);
    $xorigin = $origin;
    $xdestination = $destination;
    $stmt->execute();
    $qualcosa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $qualcosa;

}

function is_mobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function update_user_password($username, $newpassword){
    global $db_conn;

    $stmt = $db_conn->prepare("UPDATE `users` SET `password` = (:pass) WHERE `users`.`username` = (:user);");
    $stmt->bindParam(':user', $xuser);
    $stmt->bindParam(':pass', $xpass);
    $xuser = $username;
    $xpass = hash('sha512', ($newpassword . "EssrbW5bmKZq"));
    $stmt->execute();

}
/*function check_user_password($username, $password){

    global $db_conn;
    $stmt = $db_conn->prepare("SELECT users.password FROM `users` WHERE `username` LIKE (:user) ORDER BY `score` DESC");
    $stmt->bindParam(':user', $xuser);
    $xuser = $username;
    $stmt->execute();
    $pass_from_db = $stmt->fetch(PDO::FETCH_ASSOC)['password'];
    $pass_from_fn = hash('sha512', ($password . "EssrbW5bmKZq"));
    if($pass_from_db==$pass_from_fn){
        return true;
    }
    else{
        return false;
    }
}
*/



function get_ride_comments($id_ride){
    global $db_conn;
    $stmt = $db_conn->prepare("SELECT comments  FROM `rides` WHERE `id_ride` = (:id_ride)");
    $stmt->bindParam(':id_ride', $id_xride);
    $id_xride = $id_ride;
    $stmt->execute();
    $qualcosa = $stmt->fetchAll(PDO::FETCH_ASSOC)['0']['comments'];

    return unserialize($qualcosa);
}








/***
function notify_mail($mail_destination, $mail_subject, $mail_message){
        // Pear Mail Library
    require_once "Mail.php";

    $from = '<noreply@notify.cow>';
    $to = '<'$mail_destination'>';
    $subject = $mail_subject;
    $body = $mail_message;

    $headers = array(
        'From' => $from,
        'To' => $to,
        'Subject' => $subject
    );

    $smtp = Mail::factory('smtp', array(
        'host' => 'ssl://smtp.gmail.com',
        'port' => '465',
        'auth' => true,
        'username' => 'zuccolon@gmail.com',
        'password' => 'pasolini'
    ));

    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail)) {
        echo('<p>' . $mail->getMessage() . '</p>');
    } else {
        echo('<p>Message successfully sent!</p>');
    }
}
 ***/

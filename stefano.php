<?php
ini_set(‘display_errors’, 1);
ini_set(‘display_startup_errors’, 1);
error_reporting(E_ALL);

$grafo = [];

$grafo[‘chiasso’] = [‘mendrisio’];
$grafo[‘mendrisio’] = [‘chiasso’, ‘stabio’, ‘lugano’];
$grafo[‘stabio’] = [‘mendrisio’];
$grafo[‘lugano’] = [‘mendrisio’, ‘giubiasco’];
$grafo[‘giubiasco’] = [‘lugano’, ‘locarno’, ‘bellinzona’];
$grafo[‘locarno’] = [‘giubiasco’];
$grafo[‘bellinzona’] = [‘giubiasco’];
$grafo[‘bellinzona’] = [‘airolo’];
$grafo[‘airolo’] = [‘bellinzona’];

$final_path = [];

function get_path($origin, $destination, $previous = ""){
    global $grafo, $final_path;

    if ($origin == $destination)
    {
        echo "Arrivato a " . $destination . "!!!<br>\\";
       $final_path[] = $destination;
       return true;
   }

    foreach ($grafo[$origin] as $next_possible_location)
    {
        if ($next_possible_location == $previous)
            continue;

        if (get_path($next_possible_location, $destination, $origin) == true)
        {
            echo $origin . "<br>";
           $final_path[] = $origin;
           return true;
       }
    }

    return false;
}


get_path(‘stabio’, ‘locarno’);

print_r(array_reverse($final_path));

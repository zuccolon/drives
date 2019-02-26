<?php
include 'functions.php';



$tmp = array(array("user"=>"niccolo", "comment"=>"Ciao a tutti questo e un commento di prova", "date"=>1546502128), array("user"=>"nik", "comment"=>"Ciao a ssdadsasda questo e un codsafmmento di prova", "date"=>1546562548));

print_r($tmp);



print_r(serialize($tmp));











































die();






















$fruits = array (
        array("user" => "niccolo", "comment" => "bel viaggio", "date" => "2019-12-14 14:59:00"),
        array("user" => "niccolo", "comment" => "bel viaggio", "date" => "2019-04-14 14:59:00"));
print_r(serialize($fruits));





















die();

print_r(ride_exist('45'));


die();



score_incarse("zuccolon", '1');

















print_r(get_ride_members(19));

exit;
print_r(add_ride_member(19, "zuccolon"));




exit;

print_r(unserialize(getPath("bissone", "locarno")));

echo "\n";

print_r(array_unique($all_valid_paths_found));



$array = array("foo", "bar", "hello", "world");

print_r($array);

$b=serialize($array);
print_r($b);

print_r(unserialize($b));

$grafo = [];

$grafo['chiasso'] = ['mendrisio'];
$grafo['mendrisio'] = ['chiasso', 'stabio', 'lugano'];
$grafo['stabio'] = ['mendrisio'];
$grafo['lugano'] = ['mendrisio', 'giubiasco'];
$grafo['giubiasco'] = ['lugano', 'locarno', 'bellinzona'];
$grafo['locarno'] = ['giubiasco'];
$grafo['bellinzona'] = ['giubiasco'];
$grafo['bellinzona'] = ['airolo'];
$grafo['airolo'] = ['bellinzona'];
print_r($grafo);
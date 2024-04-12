<?php

$db_host = 'localhost';
$db_name = 'u_210066311_aproject';
$username = 'u-210066311';
$password = '1pITSSTUUTOpXVL';


$db = new mysqli($db_host, $username, $password, $db_name); 


if ($db-> connect_error){
	die("Connection failed: " . $db->connect_error);
}
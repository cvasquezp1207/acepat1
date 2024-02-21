<?php
$host="localhost";
$port=5432;
$user="postgres";
$pass="1235";
$db1="sistema1";
// $db2="BD_Smarti";
$c1 = pg_connect("host=$host port=$port user=$user password=$pass dbname=$db1");
// $c2 = pg_connect("host=$host port=$port user=$user password=$pass dbname=$db2");
// $cnx=$c2;

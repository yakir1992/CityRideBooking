<?php

include_once 'includes/global.php';
include 'includes/header.php';
$ref = null;
if (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
}
/* $refData = parse_url($ref);
  if($ref == "http://localhost/CityRideBooking/test2.php") {
  echo $ref . "<br>";
  echo $refData["scheme"] . "<br>";
  echo $refData["host"] . "<br>";
  // echo $refData["port"] . "<br>";
  // echo $refData["user"] . "<br>";
  // echo $refData["pass"] . "<br>";
  echo $refData["path"];
  }else {
  echo 'balls';
  } */

if ($ref == "http://localhost/CityRideBooking/test2.php") {
    echo 'Yeah Baby!';
} else {
    echo 'Oh No :(';
}
?>



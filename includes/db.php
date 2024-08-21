<?php

$conn = mysqli_connect("localhost","root","12345","tms");

if(!$conn){
    die("Connection failed: ".mysqli_connect_error());
}

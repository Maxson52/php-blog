<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tully";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

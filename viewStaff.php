<?php
session_start();

// First, we test if user is logged. If not, goto main.php (login page).
if(!isset($_SESSION['user'])){
  header("Location: main.php");
  //echo "problem with user";
  exit();
}

include('pdo.inc.php');

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $staff=0;
    if(isset($_GET['id'])){
      $staffID = (int)($_GET['id']);
    }
    if($patientID >0){

      $sql0 = "SELECT name, first_name
  FROM staff
  WHERE staff.staffID = :staffID";

    $statement0 = $dbh->prepare($sql0);
    $statement0->bindParam(':staffID', $staffID, PDO::PARAM_INT);
    $result0 = $statement0->execute();

    while($line = $statement0->fetch()){
      echo "<h1> Mitarbeiter : ".$line['first_name']."  ".$line['name']."</h1>";

      echo "<br>\n";
    }





  ?>

<br />
<i><a href="logout.php">Logout</a></i>

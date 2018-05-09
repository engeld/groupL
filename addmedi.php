<?php

session_start();
$medID = (int)$_POST['medID'];
$quantity= (int)$_POST['quantity'];
$physID = (int)$_POST['physID'];
$nurseID = (int)$_POST['NursID'];
$note = $_POST['note'];
$patientID = (int)$_POST['patientID'];
include('pdo.inc.php');



try {
  $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);

  /*** prepare the SQL Statement ***/
  $preps = $dbh->prepare ("INSERT INTO `medicine` (`time`, `quantity`, `medicamentID`, `patientID`, `staffID_nurse`, `staffID_physician`, `note`)
  VALUES (CURRENT_TIMESTAMP, :quantity, :medID, :patientID, :nurseID, :physID, :note);");

  /*** bind the parameters ***/
  $preps->bindParam(':quantity', $quantity, PDO::PARAM_INT);
	$preps->bindParam(':medID', $medID, PDO::PARAM_INT);
	$preps->bindParam(':patientID', $patientID, PDO::PARAM_INT);
  $preps->bindParam(':nurseID', $nurseID, PDO::PARAM_INT);
  $preps->bindParam(':physID', $physID, PDO::PARAM_INT);
  $preps->bindParam(':note', $note, PDO::PARAM_INT);

  /*** execute the statement ***/
  $preps.execute();

  /*** end database connection ***/
  $dhb = null;

  /*** redirect to "homepage" ***/
  header('location: patient.php?id='.$patientID);



} catch(PDOException $e) {
  echo $e->getMessage();
}

 ?>

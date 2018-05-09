<?php
session_start();

// First, we test if user is logged. If not, goto main.php (login page).
if(!isset($_SESSION['user'])){
  header("Location: main.php");
  exit();
}

include('pdo.inc.php');

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    /*** echo a message saying we have connected ***/
    echo '<h1>Liste der Mitarbeiter</h1>';
    $sql = "select * from staff";

    $result = $dbh->query($sql);

    while($line = $result->fetch()){
      echo "<a href='viewStaff.php?id=".$line['staffID']."'>";
      echo $line['first_name']." ".$line['name'];

      echo "</a><br>\n";
    }

    $dbh = null;
}
catch(PDOException $e)
{

    /*** echo the sql statement and error message ***/
    echo $e->getMessage();
}

echo "<br>User =".$_SESSION['user'];
?>
<br />
<i><a href="logout.php">Logout</a></i>

<?php
/**
 * This page is the main application page
 * 
 */
session_start();
// First, we test if user is logged. If not, goto index.php (login page).
if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include('pdo.inc.php');
echo "<body class='nav'>\n";
echo "Hallo Dr. ";
?>

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
  <a class="navbar-brand mb-0 h1" href="#">Klinik Mondschein</a>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarCollapse">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Patienten<span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Mitarbeiter</a>
      </li>
    </ul>

    <span class="navbar-text p-2 bd-highlight">
    Dr. <?php echo $_SESSION['user']?>
    </span>
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
  </div>
</nav>


<?php
try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    /*** echo a message saying we have connected ***/

    echo '<h1>List of patients</h1>';
    $sql = "select * from patient";

    $result = $dbh->query($sql);

    while($line = $result->fetch()){
      echo "<a href='viewPatient.php?id=".$line['patientID']."'>";
      echo $line['first_name']." ".$line['name'];

      echo "</a><br>\n";
    }

    $dbh = null;
} catch(PDOException $e) {
    /*** echo the sql statement and error message ***/
    echo $e->getMessage();
}

?>
<br />
<hr />
<i><a href="logout.php">Logout</a></i>

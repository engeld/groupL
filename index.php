<?php
/**
 * This page is the main application page
 * 
 */
session_start();
// First, we test if user is logged. If not, goto login.php (login page).
if(!isset($_SESSION['user'])){
  header("Location: login.php");
  exit();
}

include('pdo.inc.php');
$dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);

echo "<body class='nav'>\n";
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

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-3 col-lg-2">
      <!-- normal collapsible navbar markup -->
      <h3>Patienten</h3>
       <?php
          try {
              $result = $dbh->query("select * from patient");

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
    </div>
    <div class="col-sm-9 col-lg-10">
      <!-- your page content -->
      Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
      tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
      quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
      consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
      cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
      proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </div>
  </div>
</div>
<br />
<hr />
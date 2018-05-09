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
$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$patientID=0;
if(isset($_GET['id'])){
  $patientID = (int)($_GET['id']);
}
    
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
        <a class="nav-link" href="index.php">Patienten<span class="sr-only">(current)</span></a>
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
                echo "<a href='index.php?id=".$line['patientID']."'>";
                echo $line['first_name']." ".$line['name'];
                echo "</a><br>\n";
              }
          } catch(PDOException $e) {
              echo $e->getMessage();
          }
          ?>
    </div>
    <div class="col-sm-9 col-lg-10">
        <?php
        if($patientID >0){
          $statement0 = $dbh->prepare("SELECT name, first_name FROM patient WHERE patient.patientID = :patientID");
          $statement0->bindParam(':patientID', $patientID, PDO::PARAM_INT);
          $result0 = $statement0->execute();

          while($line = $statement0->fetch()){
            echo "<h2>Patient: ".$line['first_name']."  ".$line['name']."</h2>";
            echo "<br>\n";
          }

          /** vital signs */
          echo "<div id='vital_signs'>";
          echo "<h3>Vital Signs</h3>";
          echo "<hr />";
          echo "<div class='card-columns'>";
          $all_sign_names = $dbh->query("SELECT signID, sign_name FROM sign")->fetchAll();
          foreach ($all_sign_names as $key => $value) {
            $signID = $value['signID']; ?>

            <div class="card">
              <h4 class="card-header"><?php echo $value['sign_name']; ?></h4>
              <div class="card-body">
                <table class="table table-striped table-bordered table-sm">
                  <thead>
                    <tr>
                      <th scope="col">Zeit</th>
                      <th scope="col">Wert</th>
                      <th scope="col">Notiz</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $sql = "SELECT value, time, note FROM vital_sign WHERE vital_sign.patientID = :patientID AND vital_sign.signID = :signID";
                    $statement1 = $dbh->prepare($sql);
                    $statement1->bindParam(':patientID', $patientID, PDO::PARAM_INT);
                    $statement1->bindParam(':signID', $signID, PDO::PARAM_INT);
                    $vital_values = $statement1->execute();

                    while($vital_values = $statement1->fetch()) { 
                      echo "<tr>\n";
                      echo "<td scope='row'>".$vital_values['time']."</td>\n";
                      echo "<td>".$vital_values['value']."</td>\n";
                      echo "<td>".$vital_values['note']."</td>\n";
                      echo "</tr>\n";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
            <br>
          <?php
          }
          echo "</div>";
          echo "</div><br />";

          /** medicine */
          echo "<div id='medicine'>";
          echo "<h3>Medicine</h3>";
          echo "<hr />";

          $sql = "SELECT m.time, m.quantity, me.medicament_name, m.note FROM medicine m, medicament me 
          WHERE m.medicineID = me.medicamentID AND m.medicineID = :patientID";
          $statement3 = $dbh->prepare($sql);
          $statement3->bindParam(':patientID', $patientID, PDO::PARAM_INT);
          $medicine_user = $statement3->execute();
          ?>

          <table class="table table-striped table-bordered table-sm">
            <thead>
              <tr>
                <th scope="col">Zeit</th>
                <th scope="col">Medikament</th>
                <th scope="col">Menge</th>
                <th scope="col">Notiz</th>
              </tr>
            </thead>

            <tbody>
              <?php
              while($medicine_user = $statement3->fetch()) { 
                echo "<tr>\n";
                echo "<td scope='row'>".$medicine_user['time']."</td>\n";
                echo "<td>".$medicine_user['medicament_name']."</td>\n";
                echo "<td>".$medicine_user['quantity']."</td>\n";
                echo "<td>".$medicine_user['note']."</td>\n";
                echo "</tr>\n";
              }
              ?>
              </tbody>
            </table>
            <?php
          echo "</div>";
        } else{
          echo "<h1>The patient does not exist</h1>";
        }?>
      </div>
    </div>
  </div>
</div>
<br />
<hr />
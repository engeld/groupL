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
          echo "<div class='card-decks'>";
          $all_sign_names = $dbh->query("SELECT signID, sign_name FROM sign")->fetchAll();

          foreach ($all_sign_names as $key => $value) {
            $signID = $value['signID']; ?>
            <div class="row">
              <div class="col-6">
                <div class="card">
                  <div class="card-header p-1">
                    <nav class="navbar">
                      <span class="mr-auto">
                        <h4><?php echo $value['sign_name'] ?></h4>
                      </span>
                      <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#addVitalsign<?php echo $value['signID'] ?>">
                        Wert hinzufügen <i class="fas fa-plus-circle"></i>
                      </button>
                    </nav>
                  </div>
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

                    <!-- Modal -->
                    <div class="modal fade" id="addVitalsign<?php echo $value['signID'] ?>" tabindex="-1" role="dialog" aria-labelledby="addVitalsign<?php echo $value['signID'] ?>" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="addVitalsign<?php echo $value['signID'] ?>Label">Add <?php echo $value['sign_name'] ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            
                            <!--- @MANU: DA CHUNT S FORMULAR FUER NEUE VITALSIGN ANE --->




                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-6">
                <div id="chartContainer<?php echo $signID ?>" ></div>
                <script>
                window.onload = function () {

                var options = {
                  animationEnabled: true,
                  title:{
                    text: "Temparaturverlauf"
                  },
                  axisX: {
                    valueFormatString: "DD.MM.YY - HH:MM"
                  },
                  axisY: {
                    title: "Temparatur (in Grad Celcius)",
                    suffix: "°C",
                    includeZero: false
                  },
                  data: [{
                    yValueFormatString: "##.## °C",
                    xValueFormatString: "DD.MM.YY - HH:MM",
                    type: "spline",

                    dataPoints: [
                      { x: new Date("2014-03-01 08:20:21"), y: 37 },
                      { x: new Date("2014-03-01 15:20:45"), y: 37.5 },
                      { x: new Date("2014-03-01 22:04:51"), y: 38.2 },
                      { x: new Date("2014-03-02 08:22:27"), y: 39 },
                      { x: new Date("2014-03-02 12:32:47"), y: 38.2 },
                      { x: new Date("2014-03-02 22:04:51"), y: 38 }
                    ]
                  }]
                };
                $("#chartContainer1").CanvasJSChart(options);
                $("#chartContainer2").CanvasJSChart(options);

                }
                </script>
              </div>
            </div>

            <br>
          <?php
          }
          echo "</div>";
          echo "</div><br />";

          /** medicine */
          echo "<div id='medicine'>";
          ?>

          <nav class="navbar">
            <span class="mr-auto">
              <h3>Medikamente</h3>
            </span>
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#addMedicament">
            Medikament hinzufügen <i class="fas fa-plus-circle"></i>
            </button>
          </nav>

          <!-- Modal Medikament-->
          <div class="modal fade" id="addMedicament" tabindex="-1" role="dialog" aria-labelledby="addMedicament" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">

                  <!--- @MANU: DA CHUNT S FORMULAR FUER NEUE MEDIS ANE --->


                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>

          <?php
          $sql = "SELECT m.time, m.quantity, me.medicament_name, m.note FROM medicine m, medicament me
          WHERE m.medicineID = me.medicamentID AND m.medicineID = :patientID";
          $statement3 = $dbh->prepare($sql);
          $statement3->bindParam(':patientID', $patientID, PDO::PARAM_INT);
          $medicine_user = $statement3->execute();
          ?>

          <table class="table table-striped table-bordered table-sm">
            <thead>
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
          echo "<h1>Please select a patient</h1>";
        }?>
      </div>
    </div>
  </div>
</div>


<br />
<hr />

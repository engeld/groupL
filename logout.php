<?php
session_start();

unset($_SESSION['user']);

?>
<h1>Logout done</h1>
<p> redirecting to Login</p>
<p>Falls Sie nicht auf das Login weitergeleitet werden benutzen Sie bitten diesen Link</p>
<a href="index.php">Login</a>

<?php
sleep(1);
header("Location: index.php");
?>

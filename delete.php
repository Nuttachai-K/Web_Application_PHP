<?php
session_start();
require_once "pdo.php";
if(!isset($_SESSION["name"])){
  die('ACCESS DENIED');
}
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}
if(isset($_POST['delete'])&&isset($_POST['profile_id'])){
  $sql = "DELETE FROM profile WHERE profile_id = :zip AND user_id = :abc";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':zip' => $_POST['profile_id'], ':abc' => $_SESSION['user_id']));
  $_SESSION['success'] = 'Record deleted';
  header('Location: index.php');
  return;
}
$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM profile WHERE profile_id = :xyz AND user_id = :abc");
$stmt->execute(array(  ":xyz" => $_GET['profile_id'], ':abc' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
  $_SESSION['error'] = 'Bad value for profile_id';
  header('Location: index.php');
  return;
}
?>
<html>
<head><title>cd3d8047</title></head>
<body>
<h1>Deleting Profile</h1>
<p>First name: <?= htmlentities($row['first_name'])?></p>
<p>Last name: <?= htmlentities($row['last_name'])?></p>
<form method="POST">
  <input type="hidden" name="profile_id" value="<?= $row['profile_id']?>">
  <input type="submit" name="delete" value="Delete">
  <a href="index.php">Cancel</a>
</form>
</body>
</html>

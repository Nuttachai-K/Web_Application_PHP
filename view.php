<?php
session_start();
require_once "pdo.php";
require_once "utill.php";
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz AND user_id = :abc");
$stmt->execute(array(':xyz' => $_GET['profile_id'], ':abc' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
  $_SESSION['error'] = 'Bad value for profile_id';
  header('Location: index.php');
  return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$position = loadPos($pdo,$_REQUEST['profile_id']);
?>
<!DOCTYPE html>
<html>
<head>
<title>cd3d8047</title>
</head>
<body>
<h1>Profile Information</h1>
<p>First name: <?php echo($fn) ?></p>
<p>Last name: <?php echo($ln) ?></p>
<p>email: <?php echo($em) ?></p>
<p>headline:</br><?php echo($he) ?></p>
<p>summary:</br><?php echo($su) ?></p>
<?php
if(! $position === false){
  echo('<ul>');
  foreach($position as $r) {
    echo('<li>'.$r['year'].': '.$r['description'].'</li>');
  }
  echo('</ul>');
}
 ?>
<p><a href="index.php">Done</a></p>
</form>
</body>
</html>

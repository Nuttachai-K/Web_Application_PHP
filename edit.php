<?php
session_start();
require_once "pdo.php";
require_once "utill.php";
if (!isset($_SESSION['name']) ) {
    die('Not logged in');
}
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}
if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){

  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
    return;
  }

  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
    return;
  }

  $sql = 'UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :profile_id AND user_id = :user_id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary'],
    ':profile_id' => $_POST['profile_id'],
    ':user_id' => $_SESSION['user_id'])
  );
  $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
  $stmt->execute(array(':pid' => $_POST['profile_id']));

  $rank = 1;
  for($i = 1; $i  <= 9 ;$i++){
    if(! isset($_POST['year'.$i]) ) continue;
    if(! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    $stmt = $pdo->prepare('INSERT INTO position
      (profile_id, rank, year, description)
      VALUES (:pid, :rank, :year, :desc)');
    $stmt->execute(array(
      ':pid' => $_POST['profile_id'],
      ':rank' => $rank,
      ':year' => $year,
      'desc' => $desc)
    );
    $rank++;
  }
  $_SESSION['success'] = 'Edited';
  header('Location: index.php');
  return;
}
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz AND user_id = :abc");
$stmt->execute(array(':xyz' => $_REQUEST['profile_id'], ':abc' => $_SESSION['user_id']));
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
$profile_id = $row['profile_id'];

$position = loadPos($pdo, $profile_id);
?>
<!DOCTYPE html>
<html>
<head>
  <title>cd3d8047</title>
  <link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
      integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
      crossorigin="anonymous">

  <link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
      integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
      crossorigin="anonymous">

  <script
    src="https://code.jquery.com/jquery-3.2.1.js"
    integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
    crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
<h1>Adding Profile for <?php $_SESSION['name'] ?></h1>
<?php flashMessage(); ?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60" value="<?= $fn ?>"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60" value="<?= $ln ?>"/></p>
<p>Email:
<input type="text" name="email" size="30" value="<?= $em ?>"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80" value="<?= $he ?>"/></p>
<p>Summary:<br/>
<textarea type="text" name="summary" rows="8" cols="80"><?php echo($su)?>
</textarea>
<input type="hidden" name="profile_id" value="<?= $profile_id?>">
<p>
Position: <input type="submit" id="addPos" value="+">
<?php
$pos = 0;
foreach($position as $r){
  echo('<div id="position'.$r['rank'].'">
  <p>Year: <input type="text" name="year'.$r['rank'].'" value="'.$r['year'].'" />
  <input type="button" value="-"
    onclick="$(\'#position'.$r['rank'].'\').remove();return false;"></p>
  <textarea name="desc'.$r['rank'].'" rows="8" cols="80" >'.$r['description'].'</textarea>\
  </div>');
  $pos++;
}
?>
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
    countPos = <?= $pos ?>;
    $(document).ready(function(){
      console.log('document ready');
      $('#addPos').click(function(event){
        event.preventDefault();
        if(countPos >= 9){
          alert('maximum exceed');
          return;
        }
        countPos++;
        console.log('adding'+countPos);
        $('#position_fields').append(
          '<div id="position'+countPos+'"> \
          <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
          <input type="button" value="-" \
            onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
          <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
          </div>');
      });
    });
</script>
</body>
</html>

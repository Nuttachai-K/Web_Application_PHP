<?php
session_start();
require_once 'pdo.php';
if(isset($_POST['cancel'])){
  header('location: logout.php');
  return;
}
$salt = 'XyZzy12*_';
if(isset($_POST['email']) && isset($_POST['pass'])){
  unset($_SESSION['name']);
  unset($_SESSION['user_id']);
  if(strlen($_POST['email'])<1 || strlen($_POST['pass']) <1){
    $_SESSION['error'] = 'Incorrect password';
    header('Location: login.php');
    return;
  }
  $check = hash('md5', $salt.$_POST['pass']);
  $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
  $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($data as $row) {
    if($row !== false){
      $_SESSION['name'] = $row['name'];
      $_SESSION['user_id'] = $row['user_id'];
      header('Location: index.php');
      return;
    } else{
      $_SESSION['error'] = 'Incorrect password';
      header('Location: login.php');
      return;
    }
  }
}

?>
<html>
<head>
  <title>5f6c16eb</title>
</head>
<body style="font-family: sans-serif;">
<h1>Please Log In</h1>
<?php
    if ( isset($_SESSION["error"]) ) {
        echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
        unset($_SESSION["error"]);
    }
?>
<form method="post">
  User Name <input type="text" name="email" id="email"><br/>
  Password <input type="password" name="pass" id="pass"><br/>
  <input type="submit" onclick="return doValidate();" value="Log In">
  <input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find an account and password hint
in the HTML comments.
</p>
<script>
function doValidate(){
  console.log('Validating...');
  try{
    addr = document.getElementById('email').value;
    pw = document.getElementById('pass').value;
    console.log('Validating addr = '+addr+' pw = '+pw);
    if(addr == null || addr == '' || pw == null || pw == ''){
      alert('Both field must be filled out');
      return false;
    }
    if(addr.indexOf('@') == -1 ){
      alert('Invalid email address');
      return false;
    }
    return true;
  } catch(e){
    return false;
  }
  return false;
}
</script>
</body>

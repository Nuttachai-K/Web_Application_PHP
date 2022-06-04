<?php
session_start();
require_once "pdo.php";
require_once "utill.php";
?>
<html>
<head><title>cd3d8047</title></head>
<body>
<div class="container">
<h2>Nuttachai's Resume Registry</h2>
<?php
if(isset($_SESSION['name'])){
  echo('<p><a href="logout.php">Logout</a></p>'."\n");
}
else{
  echo("<p><a href=\"login.php\">Please log in</a></p>");
}
flashMessage();
$data = $pdo->query('SELECT profile_id, first_name,	last_name, headline FROM profile')->fetchall();
if(!empty($data)){
  echo('<table border="1">'."\n");
  echo('<thead><tr>
<th>Name</th>
<th>Headline</th>
<th>Action</th>
</tr></thead>');
  foreach($data as $row){
    echo("<tr><td><a href=\"view.php?profile_id=".$row['profile_id']."\">");
    echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']));
    echo("</a></td><td>");
    echo(htmlentities($row['headline']));
    echo("</td><td>");
    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo("</td></tr>"."\n");
    }
  echo("</table>\n");
}
if(isset($_SESSION['name'])){
  echo("<p><a href=\"add.php\">Add New Entry</a></p>");
}
?>

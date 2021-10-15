<?php
include_once 'dbConnection.php';
ob_start();
$name = $_POST['name'];
$name= ucwords(strtolower($name));
$gender = $_POST['gender'];
$username = $_POST['username'];
$batch = $_POST['batch'];
$mob = $_POST['mob'];
$password = $_POST['password'];
$name = stripslashes($name);
$name = addslashes($name);
$name = ucwords(strtolower($name));
$gender = stripslashes($gender);
$gender = addslashes($gender);
$username = stripslashes($username);
$username = addslashes($username);
$batch = stripslashes($batch);
$batch = addslashes($batch);
$mob = stripslashes($mob);
$mob = addslashes($mob);
$ref=@$_GET['q'];

$password = stripslashes($password);
$password = addslashes($password);
$password = md5($password);

$q3=mysqli_query($con,"INSERT INTO user VALUES  ('$name' , '$gender' , '$batch','$username' ,'$mob', '$password')");
if($q3)
{
header("location:dash.php?w=Student registered");

}
else
{
header("location:dash.php?w=Warning : User exists");
}
ob_end_flush();
?>
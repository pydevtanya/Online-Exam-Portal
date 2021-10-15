<?php
include_once 'dbConnection.php';
session_start();
$username=$_SESSION['username'];
//delete feedback
if(isset($_SESSION['key'])){
if(@$_GET['fdid'] && $_SESSION['key']=='iamadmin') {
$id=@$_GET['fdid'];
$result = mysqli_query($con,"DELETE FROM feedback WHERE id='$id' ") or die('Error');
header("location:dash.php?q=3");
}
}

//delete user
if(isset($_SESSION['key'])){
if(@$_GET['dusername'] && $_SESSION['key']=='iamadmin') {
$dusername=@$_GET['dusername'];
$r1 = mysqli_query($con,"DELETE FROM rank WHERE username='$dusername' ") or die('Error');
$r2 = mysqli_query($con,"DELETE FROM history WHERE username='$dusername' ") or die('Error');
$result = mysqli_query($con,"DELETE FROM user WHERE username='$dusername' ") or die('Error');
header("location:dash.php?q=1");
}
}
//remove quiz
if(isset($_SESSION['key'])){
if(@$_GET['q']== 'rmquiz' && $_SESSION['key']=='iamadmin') {
$examid=@$_GET['examid'];
$result = mysqli_query($con,"SELECT * FROM questions WHERE examid='$examid' ") or die('Error');
while($row = mysqli_fetch_array($result)) {
	$qid = $row['qid'];
$r1 = mysqli_query($con,"DELETE FROM options WHERE qid='$qid'") or die('Error');
$r2 = mysqli_query($con,"DELETE FROM answer WHERE qid='$qid' ") or die('Error');
}
$r3 = mysqli_query($con,"DELETE FROM questions WHERE examid='$examid' ") or die('Error');
$r4 = mysqli_query($con,"DELETE FROM quiz WHERE examid='$examid' ") or die('Error');
$r4 = mysqli_query($con,"DELETE FROM history WHERE examid='$examid' ") or die('Error');

header("location:dash.php?q=5");
}
}

//add quiz
if(isset($_SESSION['key'])){
if(@$_GET['q']== 'addquiz' && $_SESSION['key']=='iamadmin') {
$name = $_POST['name'];
$name= ucwords(strtolower($name));
$total = $_POST['total'];
$right = $_POST['right'];
$wrong = $_POST['wrong'];
$time = $_POST['time'];
$tag = $_POST['tag'];
$desc = $_POST['desc'];
$id=uniqid();
$q3=mysqli_query($con,"INSERT INTO quiz VALUES  ('$id','$name' , '$right' , '$wrong','$total','$time' ,'$desc','$tag', NOW())");

header("location:dash.php?q=4&step=2&examid=$id&n=$total");
}
}

//add question
if(isset($_SESSION['key'])){
if(@$_GET['q']== 'addqns' && $_SESSION['key']=='iamadmin') {
$n=@$_GET['n'];
$examid=@$_GET['examid'];
$ch=@$_GET['ch'];

for($i=1;$i<=$n;$i++)
 {
 $qid=uniqid();
 $qns=$_POST['qns'.$i];
$q3=mysqli_query($con,"INSERT INTO questions VALUES  ('$examid','$qid','$qns' , '$ch' , '$i')");
  $oaid=uniqid();
  $obid=uniqid();
$ocid=uniqid();
$odid=uniqid();
$a=$_POST[$i.'1'];
$b=$_POST[$i.'2'];
$c=$_POST[$i.'3'];
$d=$_POST[$i.'4'];
$qa=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$a','$oaid')") or die('Error61');
$qb=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$b','$obid')") or die('Error62');
$qc=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$c','$ocid')") or die('Error63');
$qd=mysqli_query($con,"INSERT INTO options VALUES  ('$qid','$d','$odid')") or die('Error64');
$e=$_POST['ans'.$i];
switch($e)
{
case 'a':
$ansid=$oaid;
break;
case 'b':
$ansid=$obid;
break;
case 'c':
$ansid=$ocid;
break;
case 'd':
$ansid=$odid;
break;
default:
$ansid=$oaid;
}


$qans=mysqli_query($con,"INSERT INTO answer VALUES  ('$qid','$ansid')");

 }
header("location:dash.php?q=0");
}
}

//quiz start
if(@$_GET['q']== 'quiz' && @$_GET['step']== 2) {
$examid=@$_GET['examid'];
$sn=@$_GET['n'];
$total=@$_GET['t'];
$ans=$_POST['ans'];
$qid=@$_GET['qid'];
$q=mysqli_query($con,"SELECT * FROM answer WHERE qid='$qid' " );
while($row=mysqli_fetch_array($q) )
{
$ansid=$row['ansid'];
}
if($ans == $ansid)
{
$q=mysqli_query($con,"SELECT * FROM quiz WHERE examid='$examid' " );
while($row=mysqli_fetch_array($q) )
{
$right=$row['right'];
}
if($sn == 1)
{
$q=mysqli_query($con,"INSERT INTO history VALUES('$username','$examid' ,'0','0','0','0',NOW())")or die('Error');
}
$q=mysqli_query($con,"SELECT * FROM history WHERE examid='$examid' AND username='$username' ")or die('Error115');

while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
$r=$row['right'];
}
$r++;
$s=$s+$right;
$q=mysqli_query($con,"UPDATE `history` SET `score`=$s,`level`=$sn,`right`=$r, date= NOW()  WHERE  username = '$username' AND examid = '$examid'")or die('Error124');

} 
else
{
$q=mysqli_query($con,"SELECT * FROM quiz WHERE examid='$examid' " )or die('Error129');

while($row=mysqli_fetch_array($q) )
{
$wrong=$row['wrong'];
}
if($sn == 1)
{
$q=mysqli_query($con,"INSERT INTO history VALUES('$username','$examid' ,'0','0','0','0',NOW() )")or die('Error137');
}
$q=mysqli_query($con,"SELECT * FROM history WHERE examid='$examid' AND username='$username' " )or die('Error139');
while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
$w=$row['wrong'];
}
$w++;
$s=$s-$wrong;
$q=mysqli_query($con,"UPDATE `history` SET `score`=$s,`level`=$sn,`wrong`=$w, date=NOW() WHERE  username = '$username' AND examid = '$examid'")or die('Error147');
}
if($sn != $total)
{
$sn++;
header("location:account.php?q=quiz&step=2&examid=$examid&n=$sn&t=$total")or die('Error152');
}
else if( $_SESSION['key']!='iamadmin')
{
$q=mysqli_query($con,"SELECT score FROM history WHERE examid='$examid' AND username='$username'" )or die('Error156');
while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
}
$q=mysqli_query($con,"SELECT * FROM rank WHERE username='$username'" )or die('Error161');
$rowcount=mysqli_num_rows($q);
if($rowcount == 0)
{
$q2=mysqli_query($con,"INSERT INTO rank VALUES('$username','$s','$examid',NOW())")or die('Error165');
}
else
{
while($row=mysqli_fetch_array($q) )
{
$sun=$row['score'];
}
$sun=$s+$sun;
$q=mysqli_query($con,"UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE username= '$username'")or die('Error174');
}
header("location:account.php?q=result&examid=$examid");
}
else
{
header("location:account.php?q=result&examid=$examid");
}
}

//restart quiz
if(@$_GET['q']== 'quizre' && @$_GET['step']== 25 ) {
  $examid=@$_GET['examid'];
  $n=@$_GET['n'];
  $t=@$_GET['t'];
  $q=mysqli_query($con,"SELECT score FROM history WHERE examid='$examid' AND username='$username'" )or die('Error156');
  while($row=mysqli_fetch_array($q) )
  {
  $s=$row['score'];
  }
  $q=mysqli_query($con,"DELETE FROM `history` WHERE examid='$examid' AND username='$username' " )or die('Error184');
  $q=mysqli_query($con,"SELECT * FROM rank WHERE username='$username'" )or die('Error161');
  while($row=mysqli_fetch_array($q) )
  {
  $sun=$row['score'];
  }
  $sun=$sun-$s;
  $q=mysqli_query($con,"UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE username= '$username'")or die('Error174');
  header("location:account.php?q=quiz&step=2&examid=$examid&n=1&t=$t");
  }
  
  ?>
  
  
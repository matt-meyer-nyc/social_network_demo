<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Social Network Mock</title>
    <link rel="stylesheet" href="includes/styles/style.css">
    <script type="text/javascript" src="includes/jquery/jquery.js"></script>
  </head>
  <body>
    <div id="page">
      <div class="header">
        <div class="logo">
  <a href="index.php" style="color:#fff; text-decoration:none;">SOCIAL NETWORK</a>
</div>

<div class="nav">
<?php
  if( !isset($_SESSION['member_id']) or $_SESSION['member_id']=='' ){
?>
  <ul>
    <a href="login.php"><li>Login</li></a>
    <a href="signup.php"><li>Register</li></a>
  </ul>
<?php
}else{
?>
  <ul>
    <a href="index.php"><li>Home</li></a>
    <a href="user.php?name=<?=$_SESSION['name']?>"><li>Timeline</li></a>
    <a href="notifications.php"><li>Notifications</li></a>
    <a href="profile.php"><li>Profile</li></a>
    <a href="messages.php"><li>Messages</li></a>
    <a href="settings.php"><li>Settings</li></a>
    <a href="logout.php"><li>Logout</li></a>
  </ul>
<?php
}
?>
</div>
<div class="clear"></div>
</div>

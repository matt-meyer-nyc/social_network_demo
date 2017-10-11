<?php session_start();

  // if person no logged in and tries to navigate or type in url for this page, redirect them to login page
  if (!isset($_SESSION['member_id'])) { header("Location: login.php"); }

  include('header.php'); include('includes/config.php');

?>

<script src="includes/jquery/jquery.validate.js"></script>
<script src="includes/jquery/ajaxForm.js"></script>

  <?php

    if (isset($_REQUEST['user']) and $_REQUEST['user'] != '') {

      $name = $_REQUEST['user'];

      $userInfo = mysqli_query($connection, "SELECT * FROM members WHERE name='".$name."'");

      $rsuserInfo = mysqli_fetch_array($userInfo);

    }



   ?>

  <div class="allUsers"></div>
  <div class="messages">
    <div id="message"></div>
    <?php if (isset($_REQUEST['user']) and $_REQUEST['user'] != '') { ?>
      <form id="messageForm_<?=$rsuserInfo['id']?>" class="" action="" method="POST">
        <textarea name="message_<?=$rsuserInfo['id']?>" class="required fields_textarea" style="height:100px;"></textarea>
        <input type="submit" name="" value="Send" class="btn">
      </form>
    <?php
      }
     ?>
  </div>

  <script>
  <?php if (isset($_REQUEST['user']) and $_REQUEST['user'] != '') { ?>

    loadMessages(<?=$rsuserInfo['id']?>);

  <?php
    }
   ?>

  loadInboxUsers();


  <?php if (isset($_REQUEST['user']) and $_REQUEST['user'] != '') { ?>
    $('#messageForm_<?=$rsuserInfo['id']?>').validate({
         submitHandler: function(form) {
           $.post('handler/actions.php?action=sendMessage&user_id=<?=$rsuserInfo['id']?>', $('#messageForm_<?=$rsuserInfo['id']?>').serialize(),
            function showInfo(responseData) {

             if (responseData == 'success_message' ) {

               document.getElementById('messageForm_<?=$rsuserInfo['id']?>').reset();

               loadMessages(<?=$rsuserInfo['id']?>);

             }

           });

         }

      });
  <?php
  }
  ?>

    function loadMessages(userid) {

      $.post('handler/actions.php?action=getMessages&user_id='+userid, function (responseData) {

        $('#message').html(responseData);
        $('#message').scrollTop($('#message').height());

      });

    }

    function loadInboxUsers() {

      $.post('handler/actions.php?action=getInboxUsers', function (responseData) {

        console.log("response: ", responseData);
        $('.allUsers').html(responseData);

      });

    }

  </script>

 <?php include('footer.php'); ?>

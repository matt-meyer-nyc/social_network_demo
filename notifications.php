<?php session_start();

  // if person no logged in and tries to navigate or type in url for this page, redirect them to login page
  if (!isset($_SESSION['member_id'])) { header("Location: login.php"); }

  include('header.php'); include('includes/config.php');

?>

  <div class="friendsList">
    <h1>Friends</h1>
    <?php

      $friends = mysqli_query($connection, "SELECT * FROM friends WHERE user1='".$_SESSION['member_id']."' OR user2='".$_SESSION['member_id']."'");

      while($results = mysqli_fetch_array($friends)) {
        ?>

          <div class="notification">
            <?php

              if ($results['user1'] != $_SESSION['member_id']) {

                $userInfo = mysqli_query($connection, "SELECT name FROM members WHERE id='".$results['user1']."'");

                $rsUserInfo = mysqli_fetch_array($userInfo);

                $userInfoP = mysqli_query($connection, "SELECT ppicture FROM profile WHERE user_id='".$results['user1']."'");

                $rsUserInfoP = mysqli_fetch_array($userInfo);

                if (mysqli_num_rows($userInfoP) == 0) {
                  echo '<img src="images/images.jpeg" width="100px" height="100px" alt="" class="ppicture"/>';

                } else {

                $rsUserInfoP = mysqli_fetch_array($userInfoP);

                echo '<img src="uploads/'.$rsUserInfoP['ppicture'].'" width="100px" height="100px" alt="" class="ppicture"/>';

                }

                echo $rsUserInfo['name'];

              }

              if ($results['user2'] != $_SESSION['member_id']) {

                $userInfo1 = mysqli_query($connection, "SELECT name FROM members WHERE id='".$results['user2']."'");

                $rsUserInfo1 = mysqli_fetch_array($userInfo1);

                $userInfoP1 = mysqli_query($connection, "SELECT ppicture FROM profile WHERE user_id='".$results['user2']."'");

                if (mysqli_num_rows($userInfoP1) == 0) {
                  echo '<img src="images/images.jpeg" width="100px" height="100px" alt="" class="ppicture"/>';

                } else {

                $rsUserInfoP1 = mysqli_fetch_array($userInfoP1);

                echo '<img src="uploads/'.$rsUserInfoP1['ppicture'].'" width="100px" height="100px" alt="" class="ppicture here"/>';

                }

                echo '<a href="user.php?name='.$rsUserInfo1['name'].'">'.$rsUserInfo1['name'].'</a>';

              }

             ?>

             (<?=$results['date_added']?>)

          </div>

      <?php
      }
     ?>

  </div>

  <div class="notificationsList">

    <?php

      $user_id = $_SESSION['member_id'];

      $notifications = mysqli_query($connection, "SELECT * FROM notifications WHERE notif_to='$user_id'");

      while($results = mysqli_fetch_array($notifications)) {

        $friends = mysqli_query($connection, "SELECT * FROM friends WHERE user1='".$results['notif_from']."' and user2='".$results['notif_to']."'
                                                                       OR
                                                                         user1='".$results['notif_to']."' and user2='".$results['notif_from']."'
                                                                         ");

        if (mysqli_num_rows($friends) == 0) {
          ?>
          <div class="notification">
            <?=$results['date_added']?> <!-- ?= shorthand echos what follows it -->
            ---
            <?=$results['message']?>
          </div>

        <?php
        }
        ?>

      <?php
      }
      ?>



  </div>

<script type="text/javascript">
/*
  function ActionRequest(type, from) {
    $.post('handler/actions.php?action=requestHandling&type='+type+'&from='+from, function(response) {

      alert(response);

      if(response = 'success_accept') {

        $('.notification').html("You and "+from+" are now friends!");


      } else if (response = 'success_reject') {

        $('.notification').html(from+" has denied your friend request.");

      }



    });
  }
*/

$('.actionBtn').click(function() {

  currentBtn = $(this);

  var type = currentBtn.attr('data-type');
  var user = currentBtn.attr('data-user');

  $.post('handler/actions.php?action=requestHandling&type='+type+'&from='+user, function(response) {

    if(response == 'success_accept') {

      currentBtn.parent().html("You and "+user+" are now friends!");

    } else if (response == 'success_reject') {

      currentBtn.parent().html(user+" has denied your friend request.");

    }

  });

});

</script>


 <?php include('footer.php'); ?>

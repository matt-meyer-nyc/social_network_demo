<?php session_start();

  // if person no logged in and tries to navigate or type in url for this page, redirect them to login page
  if (!isset($_SESSION['member_id'])) { header("Location: login.php"); }

  include('header.php'); include('includes/config.php');

?>

   <h1>

     <?php

        $name = $_REQUEST['name'];

        $userInfo = mysqli_query($connection, "SELECT * FROM members WHERE name='$name'");
        $rsuserInfo = mysqli_fetch_array($userInfo);

        $friends = mysqli_query($connection, "SELECT * FROM friends WHERE user1='".$rsuserInfo['id']."' or user2='".$rsuserInfo['id']."'");

        $friendsArray = array();
        while ($results = mysqli_fetch_array($friends)) {

          // both these following if statements set up $friendsArray to only push inside id that's not their own
          if ($results['user1'] == $rsuserInfo['id']) {

            array_push($friendsArray, $results['user2']);

          }

          if ($results['user2'] == $rsuserInfo['id']) {

            array_push($friendsArray, $results['user1']);

          }

        }

        if (in_array($_SESSION['member_id'], $friendsArray)) {

          $friend = 1;

        } else {

          $friend = 0;

        }


        $userPInfo = mysqli_query($connection, "SELECT * FROM profile WHERE user_id='".$rsuserInfo['id']."'");
        $rsuserPInfo = mysqli_fetch_array($userPInfo);

        $userSettings = mysqli_query($connection, "SELECT * FROM settings WHERE user_id='".$rsuserInfo['id']."'");
        $rsuserSettings = mysqli_fetch_array($userSettings);

      ?>

   </h1>

   <div class="userInfo">
     <table width="100%">
       <tr>
         <td width="40%">
           <?php

              if (!isset($rsuserPInfo['ppicture'])) {

                echo "no profile";

              } else {

              ?>

              <img src="uploads/<?=$rsuserPInfo['ppicture']?>" width="100px" height="100px" alt="" class="ppicture"/>

            <?php

              }

             ?>
         </td>
         <td width="60%" valign="top"><h1><b><?=$rsuserInfo['name']?></h1></b></td>
       </tr>

       <?php

         $canSeeProfile = 0;

         if($_SESSION['member_id'] == $rsuserInfo['id']) {

           $canSeeProfile = 1;

         } else if ($rsuserSettings['see_posts'] == 2) {

           if ($friend == 1) {

             $canSeeProfile = 1;

           }

         } else if ($rsuserSettings['see_posts'] == 1) {

             $canSeeProfile = 1;

          }

       ?>

       <?php

        if($canSeeProfile == 1) {

       ?>

         <tr>
           <td width="40%">Email</td>
           <td width="60%"><?=$rsuserInfo['email']?></td>
         </tr>
         <tr>
           <td width="40%">Country</td>
           <?php
              $country = mysqli_query($connection, "SELECT * FROM countries WHERE id='".$rsuserPInfo['country']."'");
              $rscountry = mysqli_fetch_array($country);
            ?>
           <td width="60%"><?=$rscountry['country_name']?></td>
         </tr>
         <tr>
           <?php
              if (isset($rsuserPInfo['gender']) and $rsuserPInfo['gender'] == 'm') {
                $gender = "Male";
              } else if (isset($rsuserPInfo['gender']) and $rsuserPInfo['gender'] == 'f') {
                $gender = "Female";
              } else {
                $gender =  "-";
              }
            ?>
           <td width="40%">Gender</td>
           <td width="60%"><?=$gender?></td>
         </tr>
         <tr>
           <td width="40%">About</td>
           <td width="60%"><?=$rsuserPInfo['about']?></td>
         </tr>
         <tr>
           <td width="40%">Birthday</td>
           <td width="60%"><?=$rsuserPInfo['dob']?></td>
         </tr>

      <?php
        }
      ?>

       <tr>
         <td colspan="2">

           <?php

            $canSendMessage = 0;

            if ($_SESSION['member_id'] == $rsuserInfo['id']) {

              $canSendMessage = 1;

            } else if ($rsuserSettings['send_message'] == 2) {

              if ($friend == 1) {

                $canSendMessage = 1;

              }

            } else if ($rsuserSettings['send_message'] == 1) {

              $canSendMessage = 1;

            }

              $requested = mysqli_query($connection, "SELECT * FROM requests WHERE
              sendingTo='".$rsuserInfo['id']."' and sentBy='".$_SESSION['member_id']."' and accepted='0'");

              if (mysqli_num_rows($requested) >=1) {
                echo "Friend Request Sent";

              } else {

             ?>

              <?php

                if ($_SESSION['member_id'] != $rsuserInfo['id']) {

              ?>

                <input type="button" value="Send Friend Request" class="btn friend_request_btn" id="requestBtn" onclick="sendAction(1, '<?=$name?>')" />

              <?php

                $friends = mysqli_query($connection, "SELECT * FROM friends WHERE
                (user1='".$rsuserInfo['id']."' and user2='".$_SESSION['member_id']."') OR
                (user2='".$rsuserInfo['id']."' and user1='".$_SESSION['member_id']."')
                ");

                $rsFriends = mysqli_fetch_array($friends);

                if (($rsFriends['user1'] == $rsuserInfo['id'] && $rsFriends['user2'] == $_SESSION['member_id']) ||
                    ($rsFriends['user2'] == $rsuserInfo['id'] && $rsFriends['user1'] == $_SESSION['member_id'])) {

                  $dateAdded = $rsFriends['date_added'];
                  $dateNormalized = date("M d Y", strtotime($dateAdded));

                  echo '<script>$(".friend_request_btn").hide();</script> <br><br><b><p style="text-align:center;">Your friend as of '.$dateNormalized.'</p></b>';



                }



                }

              ?>

            <?php

             }


            ?>

            <?php

             if ($canSendMessage == 1 and $_SESSION['member_id'] != $rsuserInfo['id']) {

            ?>

              <input type="button" value="Send Private Message" class="btn" onclick="sendMessageButton('<?=$name?>')" />

            <?php

             }

            ?>
         </td>
       </tr>
     </table>
   </div>
   <div class="posts">
     <?php

        $canPost = 0;

        if ($_SESSION['member_id'] == $rsuserInfo['id']) {

          $canPost = 1;

        } else if ($rsuserSettings['post_wall'] == 2 ){

          if($friend == 1) {

            $canPost = 1;

          }

        } else if ($rsuserSettings['post_wall'] == 1) {

          $canPost = 1;

        }

        if ($canPost == 1) {

      ?>

          <form id="statusForm" name="statusForm" method="POST">
            <table width="100%">
              <tr>
                <td>
                  <textarea name="status" class="required" rows="6" cols="30" style="width: 90%;"></textarea>
                </td>
              </tr>
              <tr>
                <td>
                  <input type="submit" value="POST STATUS" name="name" class="" rows="6" cols="30" />
                </td>
              </tr>
            </table>
          </form>

      <?php

        }

      ?>



      <?php

        $canSeePosts = 0;

        if ($_SESSION['member_id'] == $rsuserInfo['id']) {

          $canSeePosts = 1;

        } else if ($rsuserSettings['see_posts'] == 2) {

          if ($friend == 1) {

            $canSeePosts = 1;

          }

        } else if ($rsuserSettings['see_posts'] == 1) {

          $canSeePosts = 1;

        }

        if ($canSeePosts == 1) {

      ?>

          <div id="allPosts">
            Loading...
          </div>

      <?php

        }

      ?>

   </div>


   <script src="includes/jquery/jquery.validate.js"></script>
   <script src="includes/jquery/ajaxForm.js"></script>

   </script>

   <script>
    $(document).ready(function() {

      loadPosts();

    });

     function sendAction(type, name) {

       $.post('handler/actions.php?action=sendFriendRequest&name='+name, function(response) {

          if (response == 'success_friend_request') {

            // $('#requestBtn').html("Friend reqeust sent");
        //    $('#requestBtn').parent().html("Friend Request Sent");

          }

       });

     }

     $('#statusForm').validate({
          submitHandler: function(form) {
            $.post('handler/actions.php?action=savePost&user_id='+<?=$rsuserInfo['id']?>, $('#statusForm').serialize(), function showInfo(responseData) {

              if (responseData == 'Success') {
                document.getElementById("statusForm").reset();
                loadPosts();
              }

            });
          }
     });

     function loadPosts() {

       $.post('handler/actions.php?action=fetchPosts&user_id='+<?=$rsuserInfo['id']?>, function(responseData) {

         //  comes from above div around line 112
         $('#allPosts').html(responseData);

       });
     }

     function loadComment(postid) {

       $('#commentsLoading_'+postid).show();

       $.post('handler/actions.php?action=loadAllComments&post_id='+postid, function(responseData) {

         //  comes from $Post in actions.php file
         $('#allComments_'+postid).html(responseData);
         $('#commentsLoading_'+postid).hide();
         $('#viewComment_'+postid).hide();

        //  $('.commentsContainer').css('background-color', 'seashell');

       });

     }

     function deletePost(postid) {

       $.post('handler/actions.php?action=delete_post&post_id='+postid, function(responseData) {

         if (responseData == 'delete_success') {

           loadPosts();

           deleteComments(postid);

         }

       });

     }

     function deleteComments(postid) {

       $.post('handler/actions.php?action=deletePostComments&post_id='+postid);

     }

     function deleteComment(commentid, postid) {

       $.post('handler/actions.php?action=delete_comment&comment_id='+commentid, function(responseData) {

         if (responseData == 'comment_success') {

           loadComment(postid);

         }

       });

     }

     function sendMessageButton(username) {

       window.location = 'messages.php?user='+username;

     }

   </script>

 <?php include('footer.php'); ?>

<?php
    session_start();

    include("../includes/config.php");

    if ($_REQUEST['action'] == 'sendFriendRequest') {

      $sendingTo = $_REQUEST['name'];

      $userInfo = mysqli_query($connection, "SELECT * FROM members WHERE name='$sendingTo'");
      $rsuserInfo = mysqli_fetch_array($userInfo);

      // redefine sendingTo variable to whome request will be sent to
      $sendingTo = $rsuserInfo['id'];

      $sentBy = $_SESSION['member_id'];

      $requests = mysqli_query($connection, "INSERT INTO requests SET id='', sendingTo='$sendingTo', sentBy='$sentBy', date_added=NOW() ");


      //  onclick='ActionRequest(1, ".$_SESSION['member_id'].")'  initially part of update, replaced with data-type and date-user to pass info
      $message = $_SESSION['name']." sent you a friend request.
                  <input type='button' value='Accept' class='actionBtn accept' data-type='1' data-user='".$_SESSION['member_id']."' />
                  <input type='button' value='Reject' class='actionBtn reject' data-type='2' data-user='".$_SESSION['member_id']."'/>
                  ";

      $notifications = mysqli_query($connection, "INSERT INTO notifications SET id='', notif_to='".$sendingTo."',
                                notif_from='".$_SESSION['member_id']."', message='".addslashes($message)."', date_added=NOW() ");

      echo 'success_friend_request';

    }  else if ($_REQUEST['action'] == 'requestHandling') {

      if($_REQUEST['type'] == 1) {

      $requestsUpdate = mysqli_query($connection, "UPDATE requests SET accepted='1' WHERE
                        sendingTo='".$_SESSION['member_id']."' and sentBy='".$_REQUEST['from']."'");


      //  friends table
      $friends = mysqli_query($connection, "INSERT INTO friends SET id='',
                                                                    user1='".$_SESSION['member_id']."',
                                                                    user2 = '".$_REQUEST['from']."',
                                                                    date_added=NOW() ");

        if ($requestsUpdate and $friends) {
          echo "success_accept";
        }

      } else {

        $requestsUpdate = mysqli_query($connection, "UPDATE requests SET accepted='2' WHERE
                          sendingTo='".$_SESSION['member_id']."' and sentBy='".$_REQUEST['from']."'");

        if ($requestsUpdate) {
          echo "success_reject";
        }

      }

    }

    else if($_REQUEST['action'] == 'savePost') {

      $posts = mysqli_query($connection, "INSERT INTO posts SET id='', user_id='".$_SESSION['member_id']."', post_to='".$_REQUEST['user_id']."',
                                                                status='".$_REQUEST['status']."', date_added=NOW()");

      echo 'Success';

    }

    else if ($_REQUEST['action'] == 'fetchPosts') {

      if ($_SESSION['member_id'] == $_REQUEST['user_id']) {

        $query = "SELECT * FROM posts WHERE user_id='".$_SESSION['member_id']."' OR
                                            post_to='".$_SESSION['member_id']."' order by id desc ";

      } else {

        $query = "SELECT * FROM posts WHERE user_id='".$_REQUEST['user_id']."' OR
                                            post_to='".$_REQUEST['user_id']."' order by id desc ";

      }



      $posts = mysqli_query($connection, $query);

      $Post = '';

      while($post = mysqli_fetch_array($posts)) {

        $userInfo = mysqli_query($connection, "SELECT * FROM members WHERE id='".$post['user_id']."'");
        $rsuserInfo = mysqli_fetch_array($userInfo);

        $profile = mysqli_query($connection, "SELECT * FROM profile WHERE user_id='".$post['user_id']."'");
			  $rsProfile = mysqli_fetch_array($profile);

        if (isset($rsprofile['ppicture']) and $rsprofile['ppicture'] != '') {

          $img = '<img src="uploads/'.$rsProfile['ppicture'].'" height="30px" width="30px" class="ppicture"/>';

        } else {

          $img = '<img src="images/images.jpeg" height="30px" width="30px" class="ppicture"/>';

        }

        // posted from someone else, showing on 'my' profile
        $postingTo = '';

        if ($post['post_to'] != 0) {

          $userToInfo = mysqli_query($connection, "SELECT * FROM members WHERE id='".$post['post_to']."'");
          $rsuserToInfo = mysqli_fetch_array($userToInfo);

          $postingTo = ' > <a href="user.php?name='.$rsuserToInfo['name'].'">'.$rsuserToInfo['name'].'</a>';

        }

        // if currently logged in user is actual owner of a given post
        if ($_SESSION['member_id'] == $post['user_id']) {

          $deleteIcon = '<a href="javascript:void(0)" onclick="deletePost('.$post['id'].')">&#x2716;</a>';

        } else {

          $deleteIcon = '';

        }

        $Post .= '<div class="singlePost">
                    <table width="100%">
                      <tr>
                        <td width="5%">
                          '.$img.'
                        </td>
                        <td width="90%" align="left" style="background-color: #cedbd4;">
                          <a href="user.php?name='.$rsuserInfo['name'].'">'.$rsuserInfo['name'].'</a> '.$postingTo .'
                        </td>
                        <td width="5%" align="left">
                          '.$deleteIcon.'
                        </td>
                      </tr>
                      <tr>
                        <td colspan="3" class="postMessage">'.$post['status'].'</td>
                      </tr>
                      <tr>
                        <td colspan="3" align="right">
                          Posted On: '.date('d-m-Y h:i a', strtotime($post['date_added'])).'
                        </td>
                      </tr>
                      <tr>
                        <td colspan="3" align="right" style="background-color: seashell;">
                          <form id="commentForm_'.$post['id'].'" method="POST">
                            <input type="text" name="comment_'.$post['id'].'" class="commentField"/>
                            <input type="submit" value="Submit" />
                          </form>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="3" align="left">
                          <a href="javascript:void(0)" id="viewComment_'.$post['id'].'"
                             onclick="loadComment('.$post['id'].')">View Comments</a>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="3" align="left">
                          <div id="allComments_'.$post['id'].'">
                            <img height="50px" width="50px" style="border-radius: 50%;" src="images/giphy.gif" class="hidden" id="commentsLoading_'.$post['id'].'"/>
                          </div>
                        </td>
                      </tr>
                    </table>
                    <hr/>
                  </div>

                  <script>
                  $("#commentForm_'.$post['id'].'").validate({
                       submitHandler: function(form) {
                         $.post("handler/actions.php?action=commentPost&post_id='.$post['id'].'", $("#commentForm_'.$post['id'].'").serialize(), function showInfo(responseData) {

                           if (responseData = "comment_success") {

                             document.getElementById("commentForm_'.$post['id'].'").reset();
                             loadComment('.$post['id'].');

                           }

                         });
                       }
                  });
                  </script>';

      }

      echo $Post;

    } else if($_REQUEST['action'] == 'commentPost') {

      $post_id = $_REQUEST['post_id'];

      $comment = "INSERT INTO comments SET id='', user_id='".$_SESSION['member_id']."', post_id='".$post_id."', comment='".$_REQUEST["comment_$post_id"]."', date_added=NOW()";


      mysqli_query($connection, $comment);

      echo "comment_success";

    } else if ($_REQUEST['action'] == 'loadAllComments') {
                                                                                              //  post_id from loadAllComments function in user.php
      $comments = mysqli_query($connection, "SELECT * FROM comments WHERE post_id='".$_REQUEST['post_id']."' order by id desc");
      $row_cnt = $comments->num_rows;

      // printf("Result set has %d rows.\n", $row_cnt);

      $strComments = '';

                              //  alternative way of writing mysqli_fetch_array($comments);
      while($comment = $comments->fetch_assoc()) {

        $userInfo = mysqli_query($connection, "SELECT * FROM members WHERE id='".$comment['user_id']."'");
        $rsuserInfo = mysqli_fetch_array($userInfo);

        $profile = mysqli_query($connection, "SELECT * FROM profile WHERE user_id='".$comment['user_id']."'");
        $rsProfile = mysqli_fetch_array($profile);

        if (isset($rsprofile['ppicture']) and $rsprofile['ppicture'] != '') {

          $img = '<img src="uploads/'.$rsProfile['ppicture'].'" height="30px" width="30px" class="ppicture"/>';

        } else {

          $img = '<img src="images/images.jpeg" height="30px" width="30px" class="ppicture"/>';

        }

        if ($_SESSION['member_id'] == $comment['user_id']) {

          $deleteIcon = '<a href="javascript:void(0)" onclick="deleteComment('.$comment['id'].', '.$comment['post_id'].')">&#x2716;</a>';

        } else {

          $deleteIcon = '';

        }

        $strComments .= '<div>
                          <table width="100%">
                            <tr>
                              <td width="5%">
                                '.$img.'
                              </td>
                              <td width="90%" align="left">
                                <a href="user.php?name='.$rsuserInfo['name'].'">'.$rsuserInfo['name'].'</a>
                              </td>
                              <td width="5%" align="left">
                                '.$deleteIcon.'
                              </td>
                            </tr>
                            <tr>
                              <td colspan="3" class="postMessage">'.$comment['comment'].'</td>
                            </tr>
                            <tr>
                              <td colspan="3" align="right">
                                Posted On: '.date('d-m-Y h:i a', strtotime($comment['date_added'])).'
                              </td>
                            </tr>
                          </table>
                        </div>';

      }

      echo $strComments;
      exit;

    } else if ($_REQUEST['action'] == 'delete_post') {

      $deletePost = mysqli_query($connection, "DELETE from posts WHERE id='".$_REQUEST['post_id']."'");

      if ($deletePost) {

        echo 'delete_success';
        exit;

      }

    } else if ($_REQUEST['action'] == 'deletePostComments') {

      $deletePost = mysqli_query($connection, "DELETE from comments WHERE post_id='".$_REQUEST['post_id']."'");

      if ($deletePost) {

        echo 'delete_success';
        exit;

      }

    } else if ($_REQUEST['action'] == 'delete_comment') {

      $deleteComment = mysqli_query($connection, "DELETE from comments WHERE id='".$_REQUEST['comment_id']."'");

      if ($deleteComment) {

        echo 'comment_success';
        exit;

      }

    } else if ($_REQUEST['action'] == 'sendMessage') {

      $inboxUsers = mysqli_query($connection, "SELECT * FROM inbox_user WHERE (user1='".$_SESSION['member_id']."' and
                                                                               user2='".$_REQUEST['user_id']."')
                                                                              OR
                                                                              (user2='".$_SESSION['member_id']."' and
                                                                              user1='".$_REQUEST['user_id']."')
                                                                                                                ");

      if (mysqli_num_rows($inboxUsers) == 0) {

        $insertInbox = mysqli_query($connection, "INSERT INTO inbox_user SET id='',
                                                                               user1='".$_SESSION['member_id']."',
                                                                               user2='".$_REQUEST['user_id']."',
                                                                               date_added=NOW();
                                                                                                  ");

      }

      // used below to SET 'message'
      $user_id = $_REQUEST['user_id'];

      $message = mysqli_query($connection, "INSERT INTO messages SET id='',
                                                                     sending_from='".$_SESSION['member_id']."',
                                                                     sending_to='".$_REQUEST['user_id']."',
                                                                     message='".$_REQUEST["message_$user_id"]."',
                                                                     date_added=NOW()
                                                                      ");

                                                                      echo 'success_message';

    } else if($_REQUEST['action'] == 'getMessages') {

      $messages = mysqli_query($connection, "SELECT * FROM messages WHERE (sending_from='".$_SESSION['member_id']."' and
                                                                           sending_to='".$_REQUEST['user_id']."')
                                                                          OR
                                                                          (sending_to='".$_SESSION['member_id']."' and
                                                                           sending_from='".$_REQUEST['user_id']."')
                                                                            ");

      $messageList = '';

      while($message = $messages->fetch_assoc()) {

        $userInfo = mysqli_query($connection, "SELECT * FROM members WHERE id='".$message ['sending_from']."'");
        $rsuserInfo = mysqli_fetch_array($userInfo);

        $profile = mysqli_query($connection, "SELECT * FROM profile WHERE user_id='".$message['sending_from']."'");
			  $rsProfile = mysqli_fetch_array($profile);

        if (isset($rsprofile['ppicture']) and $rsprofile['ppicture'] != '') {

          $img = '<img src="uploads/'.$rsProfile['ppicture'].'" height="30px" width="30px" class="ppicture"/>';

        } else {

          $img = '<img src="images/images.jpeg" height="30px" width="30px" class="ppicture"/>';

        }

        $messageList .= '<div class="singleComment">
                          <table width="100%">
                            <tr>
                              <td width="5%">
                                '.$img.'
                              </td>
                              <td width="95%" align="left">
                                '.$rsuserInfo['name'].'
                                <div class="commentMessage">'.$message['message'].'</div>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" align="right">
                                <div class="postedOn">'.date('d-m-Y h:i a', strtotime($message['date_added'])).'</div>
                              </td>
                            </tr>
                          </table>
                        </div>';


      }

      echo $messageList;
      exit;

    } else if ($_REQUEST['action'] == 'getInboxUsers') {

      $inboxUsers = mysqli_query($connection, "SELECT * FROM inbox_user WHERE user1='".$_SESSION['member_id']."' or
                                                                              user2='".$_SESSION['member_id']."'");

      $inboxUsers1 = '<ul class="usersList">';

      while ($result = mysqli_fetch_array($inboxUsers)) {

        if ($result['user1'] != $_SESSION['member_id'] ) {
          $userName = $result['user1'];
        }
        if ($result['user2'] != $_SESSION['member_id'] ) {
          $userName = $result['user2'];
        }

        $userInfo = mysqli_query($connection, "SELECT * FROM members WHERE id='".$userName."'");
        $rsuserInfo = mysqli_fetch_array($userInfo);

        $inboxUsers1 .= '<li><a href="messages.php?user='.$rsuserInfo['name'].'">'.$rsuserInfo['name'].'</a></li>';

      }

      $inboxUsers1 .= '</ul>';

      echo $inboxUsers1;
      exit;

    }



 ?>

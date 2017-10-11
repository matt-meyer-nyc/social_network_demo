<?php

  session_start();

  if (isset($_SESSION['member_id'])) { header("Location: index.php"); }

  include('header.php');
  include('includes/config.php');

  $errors = '';
  $success = '';

  if (isset($_POST['registerBtn'])) {

    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    if (empty($name) or empty($email) or empty($password)) {

      $errors .= 'Please fill out all fields';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

      $errors .= 'Invalid email address';

    } else {

      $select = mysqli_query($connection, "SELECT * FROM members WHERE email='$email'");

      if (mysqli_num_rows($select) >= 1) {

        $errors .= "Sorry...this email already exists";

      } else {

      $token = md5($name);

      $query = "INSERT INTO members SET
                  id='',
                  name='$name',
                  email='$email',
                  password='$password',
                  date_added=NOW(),
                  token='$token'
                  ";

        $to = $email;
        $subject = "Verification email";
        $message = "Please click on the link below to activate your account";
        $message .= "<a href='http://localhost:8888/Social/activate.php?token=".$token."'>Click Here</a>";

        $headers = "From: matt@mattjacobnyc.com\n";
        $headers .= "MIME-version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";

        mail($to, $subject, $message, $headers);

        if (mysqli_query($connection, $query)) {

          // Returns the auto generated id used in the latest query
          $user_id = mysqli_insert_id($connection);

          // have settings set to 1 (public) as default
          $settings = "INSERT INTO settings SET
                id='',
                user_id='$user_id',
                post_wall='1',
                see_posts='1',
                see_profile='1',
                send_message='1',
                date_added=NOW()
              ";

          mysqli_query($connection, $settings);

          $success = 'You have successfully registered in the system...time to login!';

        } else {
          $_SESSION['message'] = 'Problem Registering!';
        }

        $name = '';
        $email = '';
        $password = '';

      }

    }

  }

?>




  <fieldset class="regFieldset">
    <?php echo $errors; ?>
    <?php echo $success; ?>
    <legend>Signup from Here</legend>
    <form class="" action="" method="post">
      <table width-"100%" cellpadding="0" cellpadding="0">
        <tr>
          <td width="30%">Name</td>
          <td width="70%">
            <input type="text" name="name" class="fields" value="<?=(( isset($name
            ) )?$name: '')?>" />
          </td>
        </tr>
        <tr>
          <td width="30%">Email</td>
          <td width="70%">
            <input type="text" name="email" class="fields" value="<?=(( isset($email
              ) )?$email: '')?>" />
          </td>
        </tr>
        <tr>
          <td width="30%">Password</td>
          <td width="70%">
            <input type="password" name="password" class="fields" value="<?=(( isset($password
              ) )?$password: '')?>" />
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="submit" name="registerBtn" value="Signup" class="btn">
          </td>
        </tr>
      </table>
    </form>
  </fieldset>


<?php include('footer.php'); ?>

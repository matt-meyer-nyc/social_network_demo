<?php
	session_start();

	// if person no logged in and tries to navigate or type in url for this page, redirect them to login page
  if (!isset($_SESSION['member_id'])) { header("Location: login.php"); }

	include('header.php');
	include('includes/config.php');

	$errors = ''; $success = '';
	$user_id = $_SESSION['member_id'];

  $settingsData = mysqli_query($connection, "SELECT * FROM settings WHERE user_id='".$user_id."'");
  $rsSettingsData = mysqli_fetch_array($settingsData);


  if( isset($_REQUEST['settingsSaveBtn']) ){

			$s1 = $_REQUEST['post_wall'];
			$s2 = $_REQUEST['see_posts'];
			$s3 = $_REQUEST['see_profile'];
			$s4 = $_REQUEST['send_message'];



      $insert = "UPDATE settings SET

            post_wall='$s1',
            see_posts='$s2',
            see_profile='$s3',
            send_message='$s4',
						date_added=NOW()

          WHERE user_id='".$user_id."'";

			mysqli_query($connection, $insert);

			echo mysqli_error($connection);

			$success = "Updated successfully";


	} else {

		$errors .= "Please fill any one of the field";

	}

?>



	<fieldset class="customFormWrap"><legend>Welcome to Me</legend>
		<?php echo $errors; ?>
		<?php echo $success; ?>
		<form action="" method="POST" enctype="multipart/form-data">

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="30%">Who can post on wall</td>
				<td width="70%">
					<select class="fields" name="post_wall">
            <option <?=(($rsSettingsData['post_wall'] == 1) ? 'selected': '')?> value="1">Public</option>
            <option <?=(($rsSettingsData['post_wall'] == 2) ? 'selected': '')?> value="2">Friends</option>
          <select/>
				</td>
			</tr>
			<tr>
				<td width="30%">Who can see my posts</td>
				<td width="70%">
					<select class="fields" name="see_posts">
            <option <?=(($rsSettingsData['see_posts'] == 1) ? 'selected': '')?> value="1">Public</option>
            <option <?=(($rsSettingsData['see_posts'] == 2) ? 'selected': '')?> value="2">Friends</option>
          <select/>
				</td>
			</tr>
			<tr>
				<td width="30%">Who can see my profile details</td>
				<td width="70%">
					<select class="fields" name="see_profile">
            <option <?=(($rsSettingsData['see_profile'] == 1) ? 'selected': '')?>  value="1">Public</option>
            <option <?=(($rsSettingsData['see_profile'] == 2) ? 'selected': '')?>  value="2">Friends</option>
          <select/>
				</td>
			</tr>
			<tr>
				<td width="30%">Who can send a private message</td>
				<td width="70%">
					<select class="fields" name="send_message">
            <option <?=(($rsSettingsData['send_message'] == 1) ? 'selected': '')?>  value="1">Public</option>
            <option <?=(($rsSettingsData['send_message'] == 2) ? 'selected': '')?>  value="2">Friends</option>
          <select/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Save" class="btn" name="settingsSaveBtn" />
				</td>
			</tr>
		</table>

		</form>

	</fieldset>

<?php include('footer.php'); ?>

<?php
	session_start();


	if( isset($_SESSION['member_id']) ){ header("Location: index.php"); }

	include('header.php');
	include("includes/config.php");

	$errors='';
	$success='';

	if( isset($_POST['loginBtn']) ){

		$email = $_REQUEST['email'];
		$password = $_REQUEST['password'];

		if( empty($email) or empty($password) ){
			$errors .= 'Please fill out all fields';
		}else{

			$select = mysqli_query($connection, "SELECT * FROM members WHERE email='$email' and password='$password' and activated='1'");

			if( mysqli_num_rows($select)==1 ){

				$result = mysqli_fetch_array($select);

				$_SESSION['member_id'] = $result['id'];
				$_SESSION['name'] = $result['name'];

				header("Location: index.php");


			}else{

				$errors .= "Email and password combination does not match. <br /> OR You need to activate your account by going through activation link in your email box.";
			}



		}

	}

?>

	<fieldset class="regFieldset"><legend>Login from here</legend>
	<?php echo $errors; ?>
	<?php echo $success; ?>
		<form method="POST" action="" id="" autocomplete="0">

			<table width="100%" cellpadding="0" cellpadding="0">
				<tr>
					<td width="30%">Email</td>
					<td width="70%">
						<input type="text" name="email" class="fields" value="<?=(( isset($email) )?$email:'')?>" />
					</td>
				</tr>
				<tr>
					<td width="30%">Password</td>
					<td width="70%">
						<input type="password" name="password" class="fields" value="<?=(( isset($password) )?$password:'')?>" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="Login" class="btn" name="loginBtn" />
					</td>
				</tr>
			</table>


		</form>

	</fieldset>

<?php include('footer.php'); ?>

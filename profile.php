<?php
	session_start();

	// if person no logged in and tries to navigate or type in url for this page, redirect them to login page
  if (!isset($_SESSION['member_id'])) { header("Location: login.php"); }

	include('header.php');
	include('includes/config.php');

	$errors = ''; $success = '';
	$user_id = $_SESSION['member_id'];

	$about = '';
	$gender = '';
	$dob = '';
	$education1 = '';
	$education2 = '';
	$education3 = '';
	$country = '';


	$select = mysqli_query($connection, "SELECT * FROM profile WHERE user_id='$user_id'");

	if( mysqli_num_rows($select)==1 ){

		$result = mysqli_fetch_array($select);

    $ppicture = $result['ppicture'];
		$about = $result['about'];
		$gender = $result['gender'];
		$dob = $result['dob'];
		$education1 = $result['education1'];
		$education2 = $result['education2'];
		$education3 = $result['education3'];
		$country = $result['country'];



		if( !empty($_REQUEST['about']) and !empty($_REQUEST['gender']) and !empty($_REQUEST['dob']) and !empty($_REQUEST['education1']) and !empty($_REQUEST['education2']) and !empty($_REQUEST['education3']) and !empty($_REQUEST['country']) ){

      //corresponds to <td> below with name="ppicture" AND 'name' below is pulled off array
      //of info stored in a given file (can see by adding var_dump($FILE); and uploading an img file) ...outputs info)
      //'tmp_name' also pulled from this data array
      $filename = $_FILES['ppicture']['name'];
      $tmp_name = $_FILES['ppicture']['tmp_name'];

      // update to give every file uploaded a random number encoded unique 'name'
      $filename = rand(9999,10000).date('Ymdhis').$filename;

      // function, 1st paramter location, 2nd parameter file (location here will be the root of this project actually)
      // move_uploaded_file($tmp_name, $filename);
      //to upload to 'uploads' folder in project directory do this:
      move_uploaded_file($tmp_name, 'uploads/'.$filename);

			$about = $_REQUEST['about'];
			// $about = $mysqli->escape_string($about);
			$gender = $_REQUEST['gender'];
			$dob = $_REQUEST['dob'];
			$education1 = $_REQUEST['education1'];
			$education2 = $_REQUEST['education2'];
			$education3 = $_REQUEST['education3'];
			$country = $_REQUEST['country'];

			$update = "UPDATE profile SET
            ppicture = '$filename',
						-- allows for apostrohpes, etc.
						about='".addslashes($about)."',
						gender='$gender',
						dob='$dob',
						education1='$education1',
						education2='$education2',
						education3='$education3',
						country='$country'
					WHERE user_id='$user_id'";

			mysqli_query($connection, $update);

			$success = "Updated successfully";

		}

	}else{


		if( isset($_REQUEST['SaveBtn']) ){

			$about = $_REQUEST['about'];
			$gender = $_REQUEST['gender'];
			$dob = $_REQUEST['dob'];
			$education1 = $_REQUEST['education1'];
			$education2 = $_REQUEST['education2'];
			$education3 = $_REQUEST['education3'];
			$country = $_REQUEST['country'];

			if( !empty($about) and !empty($gender) and !empty($dob) and !empty($education1) and !empty($education2) and !empty($education3) and !empty($country) ){

        $filename = $_FILES['ppicture']['name'];
        $tmp_name = $_FILES['ppicture']['tmp_name'];
        $filename = rand(9999,10000).date('Ymdhis').$filename;
        move_uploaded_file($tmp_name, 'uploads/'.$filename);

        $insert = "INSERT INTO profile SET
							id='',
              ppicture = '$filename',
							user_id='$user_id',
								-- allows for apostrohpes, etc.
							about='".addslashes($about)."',
							gender='$gender',
							dob='$dob',
							education1='$education1',
							education2='$education2',
							education3='$education3',
							country='$country',
							date_added=NOW()
						";

				mysqli_query($connection, $insert);

				echo mysqli_error($connection);

				$success = "Saved";


				}else{

					$errors .= "Please fill any one of the field";

				}
			}

	}





	$options = '';
	$countries = mysqli_query($connection, "SELECT * FROM countries");
	while( $rs = mysqli_fetch_array($countries) ){
		$options .= '<option value="'.$rs['id'].'" '.(( $country == $rs['id'] )?'selected':'').' >'.$rs['country_name'].'</option>';
	}

?>



	<fieldset class="customFormWrap"><legend>Welcome to Me</legend>
		<?php echo $errors; ?>
		<?php echo $success; ?>
		<form action="" method="POST" enctype="multipart/form-data">

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
				<td colspn="2">
					<img src="uploads/<?=$ppicture?>" width="300px" height="240px" />
				</td>
			</tr>
			<tr>
				<td width="30%">Profile Picture</td>
				<td width="70%">
					<input type="file" name="ppicture" class="fields" value="" />
				</td>
			</tr>
			<tr>
				<td width="30%">About</td>
				<td width="70%">
					<textarea name="about" class="fields_textarea"><?=(( isset($about) )?$about:'')?></textarea>
				</td>
			</tr>
			<tr>

				<td width="30%">Gender</td>
				<td width="70%">
					<input type="radio" name="gender" value="m" <?=(( isset($gender) and $gender=='m' )?'checked':'')?> /> Male
					<input type="radio" name="gender" value="f" <?=(( isset($gender) and $gender=='f' )?'checked':'')?> /> Female
				</td>
			</tr>
			<tr>
				<td width="30%">Date of birth</td>
				<td width="70%">
					<input type="date" name="dob" class="fields" value="<?=(( isset($dob) )?$dob:'')?>" />
				</td>
			</tr>
			<tr>
				<td width="30%">Education</td>
				<td width="70%">
					<input type="text" name="education1" class="fields" value="<?=(( isset($education1) )?$education1:'')?>" />
					<input type="text" name="education2" class="fields" value="<?=(( isset($education2) )?$education2:'')?>" />
					<input type="text" name="education3" class="fields" value="<?=(( isset($education3) )?$education3:'')?>" />
				</td>
			</tr>

			<tr>
				<td width="30%">Country</td>
				<td width="70%">
					<select class="fields" name="country">

						<?php
							echo $options;
						?>

					</select>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<input type="submit" value="Save" class="btn" name="SaveBtn" />
				</td>
			</tr>

		</table>

		</form>

	</fieldset>

<?php include('footer.php'); ?>

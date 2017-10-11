<?php session_start(); include('header.php'); include("includes/config.php"); ?>

	<br />
	<br />
  <?php

    $members = mysqli_query($connection, "SELECT * FROM members WHERE id='".$_SESSION['member_id']."'");
    $rsMembers = mysqli_fetch_array($members);

    echo '<h1>'.$rsMembers['name'].'\'s Network</h1>';

   ?>

	<br />
	<hr />
	<br />
	<br />
	<br />
	<br />
	<?php




		$connections = mysqli_query($connection, "SELECT * FROM friends WHERE user1='".$_SESSION['member_id']."' OR user2='".$_SESSION['member_id']."'");


    $allmembers = mysqli_query($connection, "SELECT * FROM members WHERE id != '".$_SESSION['member_id']."' ");
    $rsallmembers = mysqli_fetch_array($allmembers);

		echo '<h1>'.mysqli_num_rows($connections).' Connections</h1>';

		// $connectionsList = '';
		while( $rsConnections = mysqli_fetch_array($connections) ){

      $allmembers = mysqli_query($connection, "SELECT * FROM members WHERE id='".$rsConnections['user1']."' OR id='".$rsConnections['user1']."'");
      $rsallmembers = mysqli_fetch_array($allmembers);


			$userPInfo = mysqli_query($connection, "SELECT * FROM profile WHERE user_id='".$rsConnections['user1']."' OR user_id='".$rsConnections['user2']."'");

			$rsuserPInfo = mysqli_fetch_array($userPInfo);


			$CountryName = mysqli_query($connection, "SELECT * FROM countries WHERE id='".$rsuserPInfo['country']."'");

			$rsCountryName = mysqli_fetch_array($CountryName);


			if( !isset($rsuserPInfo['ppicture']) ){

				$dp = '<img src="images/images.jpeg" width="150px" height="150px" class="ppicture" />';

			} else {

				$dp = '<img src="uploads/'.$rsuserPInfo['ppicture'].'" width="150px" height="150px" class="ppicture" />';

			}


			$MembersList .= '<div class="memberBox">

								<div class="">
									'.$dp.'
								</div>
								<h3 class="">
									<a href="user.php?name='.$rsallmembers['name'].'">'.ucfirst($rsallmembers['name']).'</a>
								</h3>
                <h4>
                  <i>'. ((isset($rsCountryName['country_name']))?'<br>from '.$rsCountryName['country_name']:'') .'</i>
                </h4>


							</div> <br>';

		}

		echo $MembersList;

	?>



<?php include('footer.php'); ?>

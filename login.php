<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$_maxFileSize = 1000000;
	$_validFileTypes = array("jpg", "jpeg");
	$_currDir = getcwd();

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
	$pic = isset($_POST["picToUpload"]) ? $_POST["picToUpload"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Sithembiso Nyandeni">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				if (isset($_FILES["picToUpload"])) {
					$picName = $_FILES["picToUpload"]["name"];
					$picFolderName = "gallery/";
					$picDir  = $picFolderName . basename($picName);
					$picExt = strtolower(pathinfo($picDir, PATHINFO_EXTENSION));
					$picSize = $_FILES["picToUpload"]["size"];
	
					if ($picSize <= $_maxFileSize && in_array($picExt, $_validFileTypes)) {
	
						move_uploaded_file($_FILES["picToUpload"]["tmp_name"], $_currDir . "/" . addslashes($picDir));
	
						$sql = "SELECT user_id FROM tbusers WHERE email = '$email' AND password = '$pass'";
						$res = $mysqli->query($sql);
	
						if ($row = $res->fetch_array()) {
	
							$id = $row["user_id"];
							$sql = "INSERT INTO tbgallery (user_id, filename) VALUES ('$id', '$picName')";
							$res = $mysqli->query($sql);
	
							if ($res) {
								 ?> <div class="alert alert-success" role="alert">
									 Picture was uploaded successfully!
								 </div> <?php 
							}
							else {
								?> <div class="alert alert-danger" role="alert">
									Picture was not uploaded successfully.
								</div> <?php
							}
						}
					}
					else {
						?> <div class="alert alert-danger" role="alert">
								   Picture was not uploaded successfully.
						   </div> 
						<?php
					}
				}

				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='' method='POST' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='hidden' value='$email' name='loginEmail'/> 
									<input type='hidden' value='$pass' name='loginPass'/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
							</form>";
					$id = $row["user_id"];
					$query = "SELECT * FROM tbgallery WHERE user_id = $id";
					$res = $mysqli->query($query);

					if ($res->num_rows > 0) {
						?> 
						<h1>Image Gallery</h1>
						<div class="row imageGallery">
						<?php
						while($row = $res->fetch_assoc()) {
							$filename = $row["filename"];
							?>
							<div class='col-3' style='background-image: url("gallery/<?php echo $filename; ?>")'></div>
							<?php
						}
						?> </div> <?php
					}
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			}
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>
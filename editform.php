<?php

	error_reporting( ~E_NOTICE );
	
	require_once 'dbconfig.php';
	
	if(isset($_GET['edit_id']) && !empty($_GET['edit_id']))
	{
		$id = $_GET['edit_id'];
		$stmt_edit = $DB_con->prepare('SELECT driverName, driverDetail, rcPic , dlPic FROM tbl_users WHERE driverID =:uid');
		$stmt_edit->execute(array(':uid'=>$id));
		$edit_row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
		extract($edit_row);
	}
	else
	{
		header("Location: index.php");
	}
	
	
	
	if(isset($_POST['btn_save_updates']))
	{
		$username = $_POST['driver_name'];// user name
		$userjob = $_POST['driver_detail'];// user email
			
		$imgFile = $_FILES['rc_img']['name'];
		$tmp_dir = $_FILES['rc_img']['tmp_name'];
		$imgSize = $_FILES['rc_img']['size'];
					
		$imgFile2 = $_FILES['dl_img']['name'];
		$tmp_dir2 = $_FILES['dl_img']['tmp_name'];
		$imgSize2 = $_FILES['dl_img']['size'];
					

		if($imgFile || $imgFile2)
		{
			$upload_dir = 'rc_images/'; // upload directory	

			$upload_dir2 = 'dl_images/'; // upload directory	
            $imgExt='jpg';

			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions

			$userpic = $username."_RC.jpg";

			$userpic2 = $username."_DL.jpg";
			
			      

			if(in_array($imgExt, $valid_extensions))
			{		
				if(!isset($_FILES['rc_img']) || $_FILES['rc_img']['error'] == UPLOAD_ERR_NO_FILE)
					
					{		
						
					}
					else
					{
						if($imgSize < 5000000)
						{
							unlink($upload_dir.$edit_row['rcPic']);
							move_uploaded_file($tmp_dir,$upload_dir.$userpic);
						}
						else
						{
							$errMSG = "Sorry, your RC IMAGE file is too large it should be less then 5MB";
						}
					}
			}
			if(in_array($imgExt, $valid_extensions))
			{		
				if(!isset($_FILES['dl_img']) || $_FILES['dl_img']['error'] == UPLOAD_ERR_NO_FILE)
				{	
					return;
				}
				else
				{
					if($imgSize2 < 5000000)
					{
						unlink($upload_dir2. $edit_row['dlPic']);
						move_uploaded_file($tmp_dir2,$upload_dir2.$userpic2);
					}
					else
					{
						$errMSG = "Sorry, your DL IMAGE  file is too large it should be less then 5MB";
					}
				}
			}
			else
			{
				$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed in DL IMAGE .";		
			}	
		}
		else
		{
			// if no image selected the old image remain as it is.
			$userpic = $edit_row['rcPic']; // old image from database

			$userpic2 = $edit_row['dlPic']; // old image from database
		}	
						
		
		// if no error occured, continue ....
		if(!isset($errMSG))
		{
			$stmt = $DB_con->prepare('UPDATE tbl_users 
									     SET driverName=:uname, 
										     driverDetail=:ujob, 
										     rcPic=:upic ,
										     dlPic=:upic2
								       WHERE driverID=:uid');
			$stmt->bindParam(':uname',$username);
			$stmt->bindParam(':ujob',$userjob);
			$stmt->bindParam(':upic',$userpic);
			$stmt->bindParam(':upic2',$userpic2);
			$stmt->bindParam(':uid',$id);
				
			if($stmt->execute()){
				?>
                <script>
				alert('Successfully Updated ...');
				window.location.href='index.php';
				</script>
                <?php
			}
			else{
				$errMSG = "Sorry Data Could Not Updated !";
			}
		
		}
		
						
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit</title>

<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

<script  src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script> 

<!-- custom stylesheet -->
<link rel="stylesheet" href="style.css">

<!-- Latest compiled and minified JavaScript -->
<script src="bootstrap/js/bootstrap.min.js"></script>

<script src="jquery-1.11.3-jquery.min.js"></script>
</head>
<body>

<div class="container">


	<div class="page-header">
    	<h1 class="h2">Update Driver profile. <a class="btn btn-danger pull-right" href="index.php"> All Driver </a></h1>
    </div>

<div class="clearfix"></div>

<form method="post" enctype="multipart/form-data" class="form-horizontal">
	
    
    <?php
	if(isset($errMSG)){
		?>
        <div class="alert alert-danger">
          <span class="glyphicon glyphicon-info-sign"></span> &nbsp; <?php echo $errMSG; ?>
        </div>
        <?php
	}
	?>
   
    
	<table class="table table-bordered table-responsive">
	
    <tr>
    	<td><label class="control-label">Driver Name.</label></td>
        <td><input class="form-control" type="text" name="driver_name" value="<?php echo $edit_row['driverName']; ?>" required /></td>
    </tr>
    
    <tr>
    	<td><label class="control-label">Driver Detail</label></td>
        <td><input class="form-control" type="text" name="driver_detail" value="<?php echo $edit_row['driverDetail']; ?>" required /></td>
    </tr>
    
    <tr>
    	<td><label class="control-label">RC Img.</label></td>
        <td>
        	<p><img src="rc_images/<?php echo $edit_row['rcPic'] ?>" height="150" width="150" id="uploaded_rc"/></p>
        	<input class="input-group" type="file" id="rc_img" name="rc_img" accept="image/*" onchange="PreviewRCImage();" />
        </td>
    </tr>

     <tr>
    	<td><label class="control-label">DL Img.</label></td>
        <td>
        	<p><img src="dl_images/<?php echo $edit_row['dlPic'] ?>" height="150" width="150" id="uploaded_dl" /></p>
        	<input class="input-group" type="file" id="dl_img" name="dl_img" accept="image/*" onchange="PreviewDLImage();" />
        </td>
    </tr>
    
    <tr>
        <td colspan="2"><button type="submit" name="btn_save_updates" class="btn btn-primary">
        <span class="glyphicon glyphicon-save"></span> Update
        </button>
        
        <a class="btn btn-danger" href="index.php"> <span class="glyphicon glyphicon-backward"></span> cancel </a>
        
        </td>
    </tr>
    
    </table>
    
</form>

</div>

<script type="text/javascript">

    function PreviewDLImage() {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("dl_img").files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploaded_dl").src = oFREvent.target.result;
        };
    };
     function PreviewRCImage() {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("rc_img").files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploaded_rc").src = oFREvent.target.result;
        };
    };

</script>
</body>
</html>
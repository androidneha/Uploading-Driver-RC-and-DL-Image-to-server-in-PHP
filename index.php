<?php

	require_once 'dbconfig.php';
	
	if(isset($_GET['delete_id']))
	{
		// select image from db to delete
		$stmt_select = $DB_con->prepare('SELECT rcPic FROM tbl_users WHERE driverID =:uid');
		$stmt_select->execute(array(':uid'=>$_GET['delete_id']));
		$imgRow=$stmt_select->fetch(PDO::FETCH_ASSOC);

		$stmt_select2 = $DB_con->prepare('SELECT dlPic FROM tbl_users WHERE driverID =:uid');
		$stmt_select2->execute(array(':uid'=>$_GET['delete_id']));
		$imgRow2=$stmt_select2->fetch(PDO::FETCH_ASSOC);  

		unlink("rc_images/".$imgRow['rcPic']);
		unlink("dl_images/".$imgRow2['dlPic']);
		
		// it will delete an actual record from db
		$stmt_delete = $DB_con->prepare('DELETE FROM tbl_users WHERE driverID =:uid');
		$stmt_delete->bindParam(':uid',$_GET['delete_id']);
		$stmt_delete->execute();
		
		header("Location: index.php");
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
<title>Upload, Insert, Update, Delete an Image using PHP MySQL - Coding Cage</title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
</head>

<body>


<div class="container">

	<div class="page-header">
    	<h1 class="h2">All Driver Detail  <a class="btn btn-default pull-right" href="addnew.php"> <span class="glyphicon glyphicon-plus"></span> &nbsp; add new </a></h1> 
    </div>
    
<br />

<div class="row">
<?php
	
	$stmt = $DB_con->prepare('SELECT driverID, driverName, driverDetail, rcPic , dlPic FROM tbl_users ORDER BY driverID DESC');
	$stmt->execute();
	
	if($stmt->rowCount() > 0)
	{
		while($row=$stmt->fetch(PDO::FETCH_ASSOC))
		{
			$count=1;
			extract($row);
			?>

			<div class="table-responsive">
			  <table class="table text-center" border="1">
			    <th>S.I</th>
			    <th>Driver Name</th>
			    <th>Driver Detail</th>
			    <th>RC</th>
			    <th>Driving License</th>
			     <th>Action</th>
			    <tr>
			    	<td><?php echo $count ?> </td>
			    	<td><?php echo $row['driverName'] ?> </td>
			    	<td><?php echo $row['driverDetail'] ?> </td>
			    	<td><img src="rc_images/<?php echo $row['rcPic']; ?>" class="img-rounded" width="70px" height="70px" /></td>
			    	<td><img src="dl_images/<?php echo $row['dlPic']; ?>" class="img-rounded" width="70px" height="70px" /></td>
			    	<td>
			    		<span>
						<a class="btn btn-info" href="editform.php?edit_id=<?php echo $row['driverID']; ?>" title="click for edit" onclick="return confirm('sure to edit ?')"><span class="glyphicon glyphicon-edit"></span> Edit</a> 
						<a class="btn btn-danger" href="?delete_id=<?php echo $row['driverID']; ?>" title="click for delete" onclick="return confirm('sure to delete ?')"><span class="glyphicon glyphicon-remove-circle"></span> Delete</a>
						</span>
			    	</td>
			    </tr>
			  </table>
			</div>
		      
			<?php
			$count++;
		}
	}
	else
	{
		?>
        <div class="col-xs-12">
        	<div class="alert alert-warning">
            	<span class="glyphicon glyphicon-info-sign"></span> &nbsp; No Data Found ...
            </div>
        </div>
        <?php
	}
	
?>
</div>
<!-- Latest compiled and minified JavaScript -->
<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
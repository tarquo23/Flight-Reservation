<?php //home.php
	include 'header.php';
	include 'functions/homeFunctions.php';
	$errorText="";
	if(isset($_GET['msg'])){
		$errorText=$_GET['msg'];		
	}
	$success=(isset($_GET['success']) && $_GET['success']);
	if($loggedin){
		$booked=checkUserBooking($_SESSION['user']);
	}	
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>MyShuttle</title>
		<link href="css/home.css" rel="stylesheet" type="text/css">
		<?php if($success){?>
		<style>
		#bookingError{
			color: green;
		}
		</style>
		<?php }?>
		<script type="text/javascript" src="js/jquery-3.3.1.min.js">
		</script>
		<script type="text/javascript" src="js/home.js">
		</script>
	</head>
	<body>
	
	<div id="main">
	<?php if($loggedin){?>
		<h2>Hello <?php echo htmlentities($user);?></h2>
	<?php }?>
	<p class="errorMsg" id="bookingError"><?php echo htmlentities($errorText);?></p><br>
	
	<h2>MyShuttle itinerary</h2>
	<?php printList();?>
	<br><br>
	<h2>MyShuttle bookings</h2>
	<table id="table">
			<thead>
				<tr>
					<th>From</th>
					<th>To</th>
					<th>Passengers</th>
					<?php if($loggedin){?>
					<th>Details</th>
					<?php }?>
				</tr>
			</thead>
			<tbody>
				<?php getItinerary();?>
			</tbody>
		</table>
		<!-- BOOKING -->
		<br><br>
		<?php if($loggedin){
				if(!$booked){?>	
					<div>				
						<h2>Book MyShuttle!</h2>
						<p>Insert (new) departure and arrival addresses in the textboxs below or click
						elements in the from and to column for selecting addresses which are already
						in the system. Remember that the addresses are visited in alphabetic
						order and so the departure address has to precede the arrival address</p>
						<form id="bookForm" method="POST" action="booking.php">
							<p class="errorMsg" id="errorMsg"></p>
							<label>From: <input required type="text" name="from" id="from" placeholder="Departure"><br><br></label>
							<label>To: <input required type="text" name="to" id="to" placeholder="Arrival"><br><br></label>
							<label>Number of passengers who travel with you: <input required type="number" id="people" name="people" min="1" step="1" max="<?php echo htmlentities($capacity);?>" value="1"><br><br></label>
							<input class="button" id="bookButton" type="button" value="Book" disabled>
						</form>
					</div>
					<!-- DELETE BOOKING -->
					<?php }else{?>
						<br><br><input class="button" type="button" id="delete" value="Delete my booking"> 		
					<?php }?>
					<!-- MODAL -->
					<div id="homeModal" class="modal">
				  		<div class="modal-content">
				    		<span id="close">&times;</span>
				   			<h3 id="modalTitle"></h3>
				   			<p id="modalText"></p>
				   			<input class="button" type="button" id="confirm" value="Yes">
				   			<input class="button" type="button" id="cancel" value="No">
				  		</div>
					</div>
		<?php }?>
	</div>
	</body>
</html>
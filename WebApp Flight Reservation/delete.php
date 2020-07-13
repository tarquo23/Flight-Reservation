<?php
	include 'functions/common.php';
	include 'functions/bookingFunctions.php';
	session_start();	
	testCookie();	
	
	// If not logged user rediret
	if(!isset($_SESSION['user'])){
		redirect("home.php");
	}
	
	// Check authentication time
	checkTime();
	
	// Delete the booking
	$result=deleteBooking($_SESSION['user']);
	$success=$result=="You have successfully deleted your booking";
	redirect("home.php?msg=".$result."&success=".$success);
?>
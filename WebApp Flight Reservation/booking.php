<?php
	include 'functions/common.php';	
	include 'functions/bookingFunctions.php';
	include 'functions/homeFunctions.php';
	session_start();
			
	// If not logged user, rediret to home
	if(!isset($_SESSION['user'])){
		redirect("home.php");
	}
	
	// Check authentication time
	checkTime();
		
	// Retrieve parameters and put the addresses to upper case
	$from=strip_tags(strtoupper($_POST['from']));
	$to=strip_tags(strtoupper($_POST['to']));
	$people=$_POST['people'];
	$username=$_SESSION['user'];
	
	// If the parameters are not set, redirect with error message
	if(!isset($from) || !isset($to) || !isset($people)){
		redirect("home.php?msg=Error in setting the booking parameters, try again");
	}
	
	// If the departure is before the arrival, redirect with error message
	if($from>=$to){
		redirect("home.php?msg=Departure address has to be before the arrival one");
	}
	
	// If the number of people is not a number or exceeds the capacity
	if(is_nan($people) || $people>$capacity){
		redirect("home.php?msg=The number of passengers has to be a number less than the capacity of the shuttle(".$capacity." people), try again");
	}
	
	// Do the booking
	$result=book($username, $from, $to, $people);
	$success=($result=="Your booking has been done successfully");
	redirect("home.php?msg=".$result."&success=".$success);
	
?>
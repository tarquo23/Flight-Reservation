<?php // HOME FUNCTIONS

// Prints the itinerary and for each segment, the number of passengers.
// if the user is logged and has not an active booking it allows to click
// on a destination to set it as arrival/departure. If there is an active 
// booking it highlist the departure in red and the arrival in blue
function getItinerary(){
	$conn=connectDB();
	$result=array();
	$n_rows=0;
	$i=0;
	$myFrom="";
	$myTo="";
	$classD="";
	$classA="";
	
	try {
		// Disable autocommit
		mysqli_autocommit($conn, false);
		
		// If logged user see if it has an active booking, if so retrieve 
		// booking data to highlight them, if not add classes to make the 
		// addresses clickable
		if(isset($_SESSION['user'])){
			$booking=getUserBooking($conn, $_SESSION['user']);
			$myFrom=htmlentities($booking['from']);
			$myTo=htmlentities($booking['to']);
			if(!$booking){
				$classD="departure";
				$classA="arrival";
			}			
		}
		
		// Get all the addresses
		$result=getList($conn);
				
		// Fill the table 
		fillTable($conn, $result, $classD, $classA, $myFrom, $myTo);
		
		// All ok commit	
		mysqli_commit($conn);
		} catch (Exception $e) {
			// Rollback and give back the exception message
			mysqli_rollback($conn);
			mysqli_close($conn);
			redirect("error.php");
		}
		
		mysqli_close($conn); 
	
}

// Completes the table with the itinerary and the passengers.
// (each address except the first and the last ones is departure
// and arrivals)
function fillTable($conn, $result, $classD, $classA, $myFrom, $myTo){

	for($i=0; $i<count($result); $i++){				
		// ARRIVAL
		if($i!=0){
			$to=$result[$i][0];
			$people=getTotPeople($conn, $from, $to);
			if($people==null){
				$people="No passengers";
			}
			// If it's the arrival of the booking highlight it in blue
			if($to==$myTo){
				echo "<td style='color:red'>".htmlentities($result[$i][0])."</td><td>".htmlentities($people)."</td>";
			}else{
				echo "<td class='$classA'>".htmlentities($result[$i][0])."</td><td>".htmlentities($people)."</td>";
			}
			
			// If the user is logged get the bookings for the segment
			if(isset($_SESSION['user'])){				
				echo "<td>";
				getBookings($conn, $from, $to);
				echo "</td>";				
			}
			echo "</tr>";			
		}
		
		// DEPARTURE
		if($i!=count($result)-1){
			$from=$result[$i][0];
			// If it's the departure of the booking highlight it in blue
			if($from==$myFrom){
				echo "<tr><td style='color:red'>".htmlentities($from)."</td>";
			}else{
				echo "<tr><td class='$classD'>".htmlentities($from)."</td>";
			}	
		}
		
		
	}
	
}

// Returns all the addresses
function getList($conn){
	$result=array();
	$i=0;
	if ($res = mysqli_query($conn, "SELECT * FROM ITINERARY")){
		while ($row = mysqli_fetch_array($res)) {
			$result[$i++]=$row;
    	}		
		mysqli_free_result($res); 
	}else{
		throw new Exception("Exp retriving itnerary");
	}
	return $result;
}

// Prints the all itinerary
function printList(){
	$conn=connectDB();
	$list=getList($conn);
	mysqli_close($conn);
	$length=count($list);
	echo "<ul id='itinerary'>";
	for($i=0; $i<$length; $i++){
		echo "<li>".htmlentities($list[$i][0])."</li>";
		if($i!=$length-1){
			echo "<li>-></li>";
		}
	}
	echo "</ul>";
	
}

// Returns the bookings in the segment $from-$to
function getBookings($conn, $from, $to){
	$list=array();
	$i=0;
	$query = "SELECT username, nPeople FROM BOOKING WHERE departure<=? AND arrival>=?";
	
	if ($stmt = mysqli_prepare($conn, $query)) {
		
		mysqli_stmt_bind_param($stmt, "ss", $from, $to);
		if(!mysqli_stmt_execute($stmt)){
			throw new Exception("Exp getBookings");
		}
		mysqli_stmt_store_result($stmt);
		$rows=mysqli_stmt_num_rows($stmt);		
		mysqli_stmt_bind_result($stmt, $username, $people);
		while (mysqli_stmt_fetch($stmt)) {
			$list[$username]=$people;
    	}
    	mysqli_stmt_free_result($stmt);	
		mysqli_stmt_close($stmt);
		
		$counter=0;
		foreach ($list as $key=>$value){
			$passengers=($value==1)?"passenger":"passengers";
			echo "user ".htmlentities($key)." (".htmlentities($value)." ".$passengers.")";
			if($counter++!=$rows-1){
				echo ", ";
			}
		}
		if($counter==0){
			echo "empty";
		}
	}
	else {
		throw new Exception("Exp getBookings");;
	}
	
}

// Returns the number of passegers in the segmnent $from-$to
function getTotPeople($conn, $from, $to){
	$query = "SELECT sum(nPeople) 
			FROM BOOKING 
			WHERE departure<=? 
			AND arrival>=? 
			FOR UPDATE";
	if ($stmt = mysqli_prepare($conn, $query)) {
		mysqli_stmt_bind_param($stmt, "ss", $from, $to);
		if(!mysqli_stmt_execute($stmt)){
			throw new Exception("Exp getTotPeople");
		}
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $totPeople);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_free_result($stmt);
		mysqli_stmt_close($stmt);
		return $totPeople;
	}else {
		throw new Exception("Exp getTotPeople");
	}
	
}

// Opens and closes the connection and checks if the user has active bookings
function checkUserBooking($username){
	$conn=connectDB();
	$result=getUserBooking($conn, $username);
	mysqli_close($conn);
	return $result;
}

// Gets the reservation of the user, if any
function getUserBooking($conn, $username){
	$query = "SELECT departure, arrival FROM BOOKING WHERE username=?";
	if(!$stmt = mysqli_prepare($conn, $query)){
		throw new Exception("Exp getUserBooking");
	}
	mysqli_stmt_bind_param($stmt, "s", $username);
	if(!mysqli_stmt_execute($stmt)){
		throw new Exception("Exp getUserBooking");
	}
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $result['from'], $result['to']);
	mysqli_stmt_fetch($stmt);
	if(mysqli_stmt_num_rows($stmt)==0){
		return false;
	}
    mysqli_stmt_free_result($stmt);	
	mysqli_stmt_close($stmt);
	return $result;
}

?>
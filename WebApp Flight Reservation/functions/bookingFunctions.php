<?php
// BOOKING FUNCTIONS
// Booking function. It does a transaction in which first it checks if the booking can take place, so 
// if the username has not already a reservation and the booking doesn't exceed the shuttle capacity.
// Next, if the reservation can be done, it checks if the addresses are already in the itinerary, if 
// not it add them, and then it add the booking to the booking table. 
function book($username, $from, $to, $people){
	$conn=connectDB();	
	$list=array();
	try {
		// Disable autocommit
		mysqli_autocommit($conn, false);
				
		// Check if the user has already active bookings
		checkBookings($conn, $username);
		
		// Check if the number of people doesn't exceed the actual capacity
		$list=checkPeople($conn, $from, $to, $people);
		
		// All conditions are satisfied: save the booking
		
		// There are new addresses, add them
		if($list!=0){
			addAddresses($conn, $from, $to, $list);
		}
			
		// Add the booking in the booking table
		addBooking($conn, $username, $from, $to, $people);
		
		// All ok commit	
		mysqli_commit($conn);
		} catch (Exception $e) {
			// Rollback and give back the exception message
			mysqli_rollback($conn);
			mysqli_close($conn);
			return $e->getMessage();
		}
		return "Your booking has been done successfully";
		mysqli_close($conn); 
}

// It checks if the user has already active bookings
function checkBookings($conn, $username){
	$query = "SELECT * FROM BOOKING WHERE username=? FOR UPDATE";
	if(!$stmt = mysqli_prepare($conn, $query)){
		throw new Exception("Error in the booking process, try again");
	}
	mysqli_stmt_bind_param($stmt, "s", $username);
	if(!mysqli_stmt_execute($stmt)){
		throw new Exception("Error in the booking process, try again");
	}
	mysqli_stmt_store_result($stmt);
	if(mysqli_stmt_num_rows($stmt)!=0){
		throw new Exception("You have already an active booking, delete it to make a new one");
	}
    mysqli_stmt_free_result($stmt);	
	mysqli_stmt_close($stmt);
}

// It retrives the maximum number of passenger in the path between $from and $to
// and then it checks if with the new passengers the maximum capacity will be reached
function checkPeople($conn, $from, $to, $people){
		global $capacity;
		$max=0;
		$prelist=array();
		
		// Get all the addresses between $from and $to
		$list=getAddressesList($conn, $from, $to);	
		$prelist=$list;
		
		// If the departure is a new address
		if(!in_array($from, $list)){	
			array_unshift($list, $from);
		}
		
		// If the arrival is a new address
		if(!in_array($to, $list)){
			$list[count($list)]=$to;
		}
		
		// Retrive the number of passenger for each segment and save the maximum
		for($i=0; $i<count($list)-1;$i++){
			$current=getTotPeople($conn, $list[$i], $list[$i+1]);
			$max=($current>$max)?$current:$max;
		}
		
		// If the booking would exceed the booking capacities
		if($max+$people>$capacity){
			throw new Exception("Error: Your booking exceeds the shuttle capacity");
		}
		
		// If no new addresses
		if($list==$prelist){
			$prelist=0;
		}
		
		return $prelist;
		
}

// Returns the list of addresses between $from and $to
function getAddressesList($conn, $from, $to){
	$list=array();
	$i=0;
	$query = "SELECT * FROM ITINERARY WHERE name>=? AND name<=? FOR UPDATE";
	if(!$stmt = mysqli_prepare($conn, $query)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_bind_param($stmt, "ss", $from, $to);
	if(!mysqli_stmt_execute($stmt)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $name);
	
	while (mysqli_stmt_fetch($stmt)) {
		$list[$i++]=$name;
    }
    mysqli_stmt_free_result($stmt);	
	mysqli_stmt_close($stmt);
	
	return $list;
}

// If the $from and $to address are not in the itinerary add them
function addAddresses($conn, $from, $to, $list){
		// If the departure is a new address
		if(!in_array($from, $list)){	
			addAddress($conn, $from);
		}
		
		// If the arrival is a new address
		if(!in_array($to, $list)){
			addAddress($conn, $to);
		}	
}


// Add $addr to the itinerary
function addAddress($conn, $addr){
		$query = "INSERT INTO ITINERARY(name) VALUES (?)";
		if(!$stmt = mysqli_prepare($conn, $query)){
			throw new Exception("Error in the booking process, try again");
		}
		mysqli_stmt_bind_param($stmt, "s", $addr);
		if(!mysqli_stmt_execute($stmt)){
			throw new Exception("Error in the booking process, try again");
		}
		mysqli_stmt_store_result($stmt);
		if(mysqli_stmt_affected_rows($stmt)!=1){
			throw new Exception("Error in inserting the new address, try again");
		}
    	mysqli_stmt_free_result($stmt);	
		mysqli_stmt_close($stmt);		
	
}

// Add the booking into the booking table
function addBooking($conn, $username, $from, $to, $people){
		$query = "INSERT INTO BOOKING(username, departure, arrival, nPeople) VALUES (?,?,?,?)";
		if(!$stmt = mysqli_prepare($conn, $query)){
			throw new Exception("Error in the booking process, try again");
		}
		mysqli_stmt_bind_param($stmt, "sssi", $username, $from, $to, $people);
		if(!mysqli_stmt_execute($stmt)){
			throw new Exception("Error in the booking process, try again");
		}
		mysqli_stmt_store_result($stmt);
		if(mysqli_stmt_affected_rows($stmt)!=1){
			throw new Exception("Error in the booking process, try again");
		}
    	mysqli_stmt_free_result($stmt);	
		mysqli_stmt_close($stmt);
	
}

// DELETE BOOKING FUNCTIONS
// Deletes a booking. First it retrieves the booking detailles then it deletes the row.
// Next it uses the details to see if some addresses in the itinerary are not useful any more 
function deleteBooking($username){
	$conn=connectDB();	
	try {
		// Disable autocommit
		mysqli_autocommit($conn, false);
				
		// Get departure and arrival addresses
		$result=getFromAndTo($conn, $username);
		
		// Delete the row in the booking table
		deleteBookingRow($conn, $username);
		
		// Check if some adresses have to be removed from the itinerary
		checkItinerary($conn, $result['from'], $result['to']);
		
		// All ok commit	
		mysqli_commit($conn);
		} catch (Exception $e) {
			// Rollback and return the exception message
			mysqli_rollback($conn);
			mysqli_close($conn);
			return $e->getMessage();
		}
	return "You have successfully deleted your booking"; 
	mysqli_close($conn);
}

// Gets the departure and the arrival addresses of the reservation
function getFromAndTo($conn, $username){
	$query = "SELECT departure, arrival FROM BOOKING WHERE username=? FOR UPDATE";
	if(!$stmt = mysqli_prepare($conn, $query)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_bind_param($stmt, "s", $username);
	if(!mysqli_stmt_execute($stmt)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $result['from'], $result['to']);
	mysqli_stmt_fetch($stmt);
    mysqli_stmt_free_result($stmt);	
	mysqli_stmt_close($stmt);
	return $result;
}

// Deletes the row in booking table
function deleteBookingRow($conn, $username){
	$query = "DELETE FROM BOOKING WHERE username=?";
	if(!$stmt = mysqli_prepare($conn, $query)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_bind_param($stmt, "s", $username);
	if(!mysqli_stmt_execute($stmt)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_store_result($stmt);
	if(mysqli_stmt_affected_rows($stmt)!=1){
		throw new Exception("Error in deleting the record, try again");
	}
    mysqli_stmt_free_result($stmt);	
	mysqli_stmt_close($stmt);	
}

// Checks if some addresses can be deleted from the itinerary
function checkItinerary($conn, $from, $to){
	
	// Get the list of addresses between $from and $to
	$list=getAddressesList($conn, $from, $to);

	// Check if the address is still useful 
	for($i=0; $i<count($list);$i++){
		checkAddress($conn, $list[$i]);
	}
	
}

// If an address is not used neither as departure, nor as arrival, deletes it
function checkAddress($conn, $name){
	$query = "SELECT * FROM BOOKING WHERE departure=? OR arrival=?";
	if(!$stmt = mysqli_prepare($conn, $query)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_bind_param($stmt, "ss", $name, $name);
	if(!mysqli_stmt_execute($stmt)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_store_result($stmt);
	$rows=mysqli_stmt_num_rows($stmt);
	if($rows==0){
		deleteAddress($conn, $name);
	}
    mysqli_stmt_free_result($stmt);	
	mysqli_stmt_close($stmt);
}

// Deletes an address from the itinerary
function deleteAddress($conn, $name){
	$query = "DELETE FROM ITINERARY WHERE name=?";
	if(!$stmt = mysqli_prepare($conn, $query)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_bind_param($stmt, "s", $name);
	if(!mysqli_stmt_execute($stmt)){
		throw new Exception("Error in deleting the record, try again");
	}
	mysqli_stmt_store_result($stmt);
	if(mysqli_stmt_affected_rows($stmt)!=1){
		throw new Exception("Error in deleting the record, try again");
	}
    mysqli_stmt_free_result($stmt);	
	mysqli_stmt_close($stmt);
}

?>
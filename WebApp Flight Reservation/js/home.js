$(document).ready(function(){
	$booking=true; // To decide if the modal is called for deleting/booking confirmation
	
	// FUNCTIONS
	// Checks if both destination and arrival have been chosen and if the path is valid
	// If so it opens the modal and ask confirmation for the booking
	function checkForBooking(){		
		if($("#from").val() && $("#to").val()){			
			if($("#from").val().toUpperCase()<$("#to").val().toUpperCase()){
				$("#bookButton").removeAttr("disabled");
				$booking=true;
				return 0;				
			}else{
				$("#bookButton").attr("disabled","disabled");
				return 2;				
			}			
		}else{
			return 1;
		}
	}
	
	// ACTIONS
	// When clicking on a departure address, puts the value in the form and checks if the 
	// booking can go on
	$(".departure").click(function (){
		$(".departure").removeAttr("id");
		$(".departure").css({'background-color':'white'});
		$(this).attr("id", "myDep");
		$(this).css({'background-color':'red'});
		$("#from").val($("#myDep").text());
		$("#errorMsg").text("");
		if(checkForBooking()==2){
			$("#errorMsg").text("The departure address has to be before the arrival one");
		}
	});
	
	// When clicking on an arrival address, puts the value in the form and checks if the 
	// booking can go on 
	$(".arrival").click(function (){
		$(".arrival").removeAttr("id");
		$(".arrival").css({'background-color':'white'});
		$(this).attr("id", "myArr");	
		$(this).css({'background-color':'red'});
		$("#to").val($("#myArr").text());
		$("#errorMsg").text("");
		if(checkForBooking()==2){
			$("#errorMsg").text("The departure address has to be before the arrival one");
		}
	});
	
	// When clicking the booking button it checks if the booking can go on, if 
	// not prints an error message
	$("#bookButton").click(function(){
		$("#errorMsg").text("");
		switch(checkForBooking()){
		case 0:
				$("#modalTitle").text("Booking confirmation");
				$("#modalText").text("Are you sure to book from "+$("#from").val()+" to "+$("#to").val()+" for "+$("#people").val()+" people?");
				$("#homeModal").css({'display':'block'});
				break;
		case 1:
				$("#errorMsg").text("Insert a valid departure and a valid arrival address");
				break;
		case 2:
				$("#errorMsg").text("The departure address has to be before the arrival one");
				break;
				
		
		}
		
	});
	
	// When clicking on delete button, it opens the modal to ask confirmation for the action
	$("#delete").click(function(){
		$booking=false;
		$("#modalTitle").text("Delete confirmation");
		$("#modalText").text("Are you sure to delete your booking?");
		$("#homeModal").css({'display':'block'});
	});
	
	// When changing the "from" input checks if you can go on with the booking
	$("#from").change(function(){
		$("#errorMsg").text("");
		if(checkForBooking()==2){
			$("#errorMsg").text("The departure address has to be before the arrival one");
		}
	});
	
	// When changing the "to" input checks if you can go on with the booking
	$("#to").change(function(){
		$("#errorMsg").text("");
		if(checkForBooking()==2){
			$("#errorMsg").text("The departure address has to be before the arrival one");
		}
	});
	
	// MODAL
	// When clicking on X the modal disappeares
	$("#close").click(function(){
		$("#homeModal").css({'display':'none'});
	});
	
	// When clicking on No the modal disappeares
	$("#cancel").click(function(){
		$("#homeModal").css({'display':'none'});		
	});
	
	// Depending if the modal has been opened for booking or deleting, when clicking Yes
	// it continues the execution
	$("#confirm").click(function(){
		if($booking){
			$("#bookForm").submit();
		}else{
			window.location.href = "delete.php";
		}
	});
	
	// Clicking outside the modal, it is closed
	$(window).click(function(event){
		if (event.target.id =="homeModal") {
	    	$("#homeModal").css({'display':'none'});
	    }
	});
	
});
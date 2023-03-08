<?php 

?>

<!DOCTYPE html>
<html>
	<head>
		<?php
      require_once('universal.inc');
      include_once('database/dbEvents.php');

      // check if user has reached this page
      // with an event ID
      if (isset($_GET["id"])) {
        $id = $_GET["id"];
        echo '<title>'.$_GET["id"].' - Event</title>';
      } else {
        header('Location: calendar.php');
        die();
      }
    ?>
	</head>
	
	<body>
		<?php
      require_once('header.php');

      // grab event data using fetch_event_by_id() in dbEvents.php
      $event_info = fetch_event_by_id($id);
      if ($event_info == NULL) {
        // TODO: Need to create error page for no event found
        header('Location: calendar.php');
        die();
      }
      // print_r($event_info);
      $event_name = $event_info[1];
      $event_date = $event_info[3];
      $event_startTime = $event_info[4];
      $event_endTime = $event_info[5];
      $event_location = $event_info[7];
      $event_description = $event_info[6];
      
		  echo '<h1>'.$id.' - Event: '.$event_name.'</h1>';
    ?>

    <?php
      /* TODO: will figure out another way to center
               later
      */
		  echo '<p><center>Event Date(s): '.$event_date.'</center></p>';
		  echo '<p><center>Event Start Time: '.$event_startTime.'</center></p>';
		  echo '<p><center>Event End Time: '.$event_endTime.'</center></p>';
		  echo '<p><center>Event Location: '.$event_location.'</center></p>';
		  echo '<p><center>Event Description: '.$event_description.'</center></p>';
    ?>
    <br>
	
		<body>
			<h2>
        <center>List of Volunteers for Event</center>
      </h2>
      <!-- TODO: will figure out another way to center
                 later -->
        <li>
            <ul><center>&nbsp;Joe</center></ul>
            <ul><center>&nbsp;Alice</center></ul>
            <ul><center>&nbsp;Bob</center></ul>
            <ul><center>&nbsp;Michelle</center></ul>
       </li>
		</body>
    <br>
		<body>
			<h2>
        <center>Access/Insert Training Materials</center>
      </h2>	
      <center>
        To be added
      </center>
		</body>
		
    <!-- temporary breaks to separate headers -->
    <br><br>

		<body>
			<h2>
        <center>Post-Event Media</center>
      </h2>	
		</body>
		
		<center>		
		<a href="calendar.php"> 
				<button class = "block" style="width:200px;"><b>Go back to Calendar!</b></button>
		</a>
		</center>
		
		<br>
			
		<center>
		<a href="eventRegister.php"> 
				<button class = "block" style="width:200px;"><b>Register for Event!</b></button>
		</a>
		</center>
		
		<br>
			
		<center>
		<a href="eventRegister.php"> 
				<button class = "block" style="width:200px;"><b>Register for Event! For Admin Use Only</b></button>
		</a>
		</center>
		
	</body>
</html>

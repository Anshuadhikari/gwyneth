<?php 
/**
 * @version April 6, 2023
 * @author Alip Yalikun
 */


  session_cache_expire(30);
  session_start();
  ini_set("display_errors",1);
  error_reporting(E_ALL);
  $loggedIn = false;
  $accessLevel = 0;
  $userID = null;
  if (isset($_SESSION['_id'])) {
      $loggedIn = true;
      // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
      $accessLevel = $_SESSION['access_level'];
      $userID = $_SESSION['_id'];
  }

  require_once('include/input-validation.php');
  require_once('database/dbPersons.php');
  require_once('database/dbEvents.php');

  $get = sanitize($_GET);
  $indivID = @$get['indivID'];
  $type = $get['report_type'];
  $dateFrom = $_GET['date_from'];
  $dateTo = $_GET['date_to'];
  $lastFrom = $_GET['lname_start'];
  $lastTo = $_GET['lname_end'];
  // Is user authorized to view this page?
  if ($accessLevel < 2) {
      header('Location: index.php');
      die();
  }
  function getBetweenDates($startDate, $endDate){
      $rangArray = [];
          
      $startDate = strtotime($startDate);
      $endDate = strtotime($endDate);
           
      for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400)) {
        $date = date('Y-m-d', $currentDate);
        $rangArray[] = $date;
      }

      return $rangArray;
    }


?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Gwyneth's Gift VMS | Report Result</title>
        <style>
            table {
                margin-top: 1rem;
                margin-left: auto;
		margin-right: auto;
		border-collapse: collapse;
                width: 80%;
            }
            td {
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
            }
            th {
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
		font-weight: bold;
            }
          
            tr:nth-child(even) {
                background-color: #2f4159;
                color: white;
		
            }
            .theB{
                width: auto;
                font-size: 15px;
            }
	    .center_a {
                margin-top: 0;
		margin-bottom: 3rem;
                margin-left:auto;
                margin-right:auto;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .8rem;
            }
            .center_b {
                margin-top: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
		gap: .8rem;
            }
	    .intro {
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 0 0;
            }
	    @media only screen and (min-width: 1024px) {
                .intro{
                    width: 80%;
                }
                main.report {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
            }
    </style>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Report Result</h1>
        <main class="report">
	   <div class="intro">
        <div>
            <label>Reports Type:</label>
            <span>
                <?php echo '&nbsp&nbsp&nbsp'; 
                if($type == "top_perform"){
                    echo "Top Performers"; 
                }elseif($type == "general_volunteer_report"){
                    echo "General Volunteer Report";
                }elseif($type == "total_vol_hours"){
                    echo "Total Volunteer Hours";
                }elseif($type == "indiv_vol_hours"){
                    echo "Individual Volunteer Hours";
                }
                ?> 
            </span>
        </div>
        <div>

		<?php if ($type == "indiv_vol_hours"): ?>
			<label>Name: </label>
		<?php echo '&nbsp&nbsp&nbsp';
			$con=connect();
             		$query = "SELECT dbPersons.first_name, dbPersons.last_name FROM dbPersons WHERE id= '$indivID' ";
             		$result = mysqli_query($con,$query);
			$theName = mysqli_fetch_assoc($result);	
			echo $theName['first_name'], " " , $theName['last_name'] ?>
		<?php else: ?>    
	    		<label>Last Name Range:</label>
            <span>
                	<?php echo '&nbsp&nbsp&nbsp';
			      if($lastFrom == NULL && $lastTo == NULL): ?>
                        	<?php echo "All last names"; ?>
                    	<?php else: ?>
                        	<?php echo $lastFrom, " to " , $lastTo; ?>
			<?php endif ?>
                 <?php endif ?>
            </span>
	</div>


	<div>
             <label>Date Range:</label>
             <span>
                 <?php echo '&nbsp&nbsp&nbsp';
                 // if date from is provided but not date to, assume admin wants all dates from gi    ven date to current
                     if(isset($dateFrom) && !isset($dateTo)){
                         echo $dateFrom, " to Current";
                 // if date from is not provided but date to is, assume admin wants all dates prio    r to the date given
                     }elseif(!isset($dateFrom) && isset($dateTo)){
                         echo "Every date through ", $dateTo;
                 // if date from and date to is not provided assume admin wants all dates
                     }elseif($dateFrom == NULL && $dateTo ==NULL){
                         echo "All dates";
                     }else{
                         echo $dateFrom ," to ", $dateTo;
                     }
                 ?>
             </span>
         </div>

	<div>
            <label>Total Volunteer Hours: </label>
            <span>
                <?php echo '&nbsp&nbsp&nbsp';
			echo "need to calc hrs"; ?>
            </span>
        </div>
        <!--- <h3 style="font-weight: bold">Result: <h3> -->
	</div>
    </main>
	
	<div class="center_a">
                <a href="http://localhost/gwyneth/report.php">
                <button class = "theB">New Report</button>
                </a>
                <a href="http://localhost/gwyneth/index.php">
                <button class = "theB">Home Page</button>
                </a>
	</div>

        <?php 
        // view General volunteer report with all date range and all name range
        if($type == "general_volunteer_report" && $dateFrom == NULL && $dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		<th>Skills</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT * FROM dbPersons WHERE type LIKE '%" . $type1 . "%' ";
            $result = mysqli_query($con,$query);
            while($row = mysqli_fetch_assoc($result)){
                echo"<tr>
                <td>" . $row['first_name'] . "</td>
                <td>" . $row['last_name'] . "</td>
                <td>" . $row['phone1'] . "</td>
                <td>" . $row['email'] . "</td>
		<td>" . $row['specialties'] . "</td>
                <td>" . get_hours_volunteered_by($row['id']) . "</td>
                </tr>";    
            }
        }
        // date range and name range for general volunteer report 
        if($type == "general_volunteer_report" && !$dateFrom == NULL && !$dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		<th>Skills</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbPersons.phone1,dbPersons.email, dbPersons.specialties
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id";
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $nameRange = range($lastFrom,$lastTo);
                $bothRange = array_merge($dd,$nameRange);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($bothRange as $both){
                        if(in_array($both,$dateRange) && in_array($row['last_name'][0],$nameRange)){
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td>" . $row['phone1'] . "</td>
                            <td>" . $row['email'] . "</td>
			    <td>" . $row['specialties'] . "</td>
                            <td>" . get_hours_volunteered_by($row['id']) . "</td>
                            </tr>";
                        }
                    }
                }
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }
         //only name range for general volunteer report 
        if($type == "general_volunteer_report" && $dateFrom == NULL && $dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		<th>Skills</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT * FROM dbPersons WHERE type LIKE '%" . $type1 . "%' ";
            $result = mysqli_query($con,$query);
            $nameRange = range($lastFrom,$lastTo);
            while($row = mysqli_fetch_assoc($result)){
                foreach ($nameRange as $a){
                    if($row['last_name'][0] == $a){
                        echo"<tr>
                        <td>" . $row['first_name'] . "</td>
                        <td>" . $row['last_name'] . "</td>
                        <td>" . $row['phone1'] . "</td>
                        <td>" . $row['email'] . "</td>
			<td>" . $row['specialties'] . "</td>
                        <td>" . get_hours_volunteered_by($row['id']) . "</td>
                        </tr>";
                    }
                } 
            }
        }
        //only date range for general volunteer report 
        if($type == "general_volunteer_report" && !$dateFrom == NULL && !$dateTo ==NULL && $lastFrom == NULL  && $lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email Address</th>
		<th>Skills</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbPersons.phone1,dbPersons.email, dbPersons.specialties
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id";
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($dd as $date){
                        if(in_array($date,$dateRange)){
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td>" . $row['phone1'] . "</td>
                            <td>" . $row['email'] . "</td>
			    <td>" . $row['specialties'] . "</td>
                            <td>" . get_hours_volunteered_by($row['id']) . "</td>
                            </tr>";
                        }
                    }
                }
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }

        // view Top performers report with all date range and all name range
        if($type == "top_perform" && $dateFrom == NULL && $dateTo ==NULL && $lastFrom == NULL && $lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, dbEvents.startTime, dbEvents.endTime,
            (dbEvents.endTime - dbEvents.startTime) AS DURATION
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
            ORDER BY DURATION DESC";
            $result = mysqli_query($con,$query);
            while($row = mysqli_fetch_assoc($result)){
                echo"<tr>
                <td>" . $row['first_name'] . "</td>
                <td>" . $row['last_name'] . "</td>
                <td>" . (int)$row['endTime'] - (int)$row['startTime'] . "</td>
                </tr>";
            }
        }
        // date range and name range for top performer report
        if($type == "top_perform" && !$dateFrom == NULL && !$dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name, dbEvents.startTime, dbEvents.endTime,
            (dbEvents.endTime - dbEvents.startTime) AS DURATION
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
            ORDER BY DURATION DESC";
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $nameRange = range($lastFrom,$lastTo);
                $bothRange = array_merge($dd,$nameRange);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($bothRange as $both){
                        if(in_array($both,$dateRange) && in_array($row['last_name'][0],$nameRange)){
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td>" . (int)$row['endTime'] - (int)$row['startTime'] . "</td>
                            </tr>";
                        }
                    }
                }
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }
         //only name range for top performer report 
        if($type == "top_perform" && $dateFrom == NULL && $dateTo ==NULL && !$lastFrom == NULL  && !$lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbEvents.startTime, dbEvents.endTime,
            (dbEvents.endTime - dbEvents.startTime) AS DURATION
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
            ORDER BY DURATION DESC";
            $result = mysqli_query($con,$query);
            $nameRange = range($lastFrom,$lastTo);
            while($row = mysqli_fetch_assoc($result)){
                foreach ($nameRange as $a){
                    if($row['last_name'][0] == $a){
                        echo"<tr>
                        <td>" . $row['first_name'] . "</td>
                        <td>" . $row['last_name'] . "</td>
                        <td>" . (int)$row['endTime'] - (int)$row['startTime'] . "</td>
                        </tr>";
                    }
                } 
            }
        }
        //only date range for top performer report
        if($type == "top_perform" && !$dateFrom == NULL && !$dateTo ==NULL && $lastFrom == NULL  && $lastTo == NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbEvents.startTime, dbEvents.endTime,
            (dbEvents.endTime - dbEvents.startTime) AS DURATION
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
            ORDER BY DURATION DESC";
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($dd as $date){
                        if(in_array($date,$dateRange)){
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td>" . (int)$row['endTime'] - (int)$row['startTime'] . "</td>
                            </tr>";
                        }
                    }
                }
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }
        // view indiv_vol_hours report with all date range and all name range
        if($type == "indiv_vol_hours" && $dateFrom == NULL && $dateTo ==NULL){
            echo"
            <table>
            <tr>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbEvents.name, dbEvents.location,dbEvents.date,dbEvents.startTime,dbEvents.endTime,
            (dbEvents.endTime - dbEvents.startTime) AS DURATION
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
            WHERE dbPersons.id ='$indivID'
	    ORDER BY dbEvents.date desc";
            $result = mysqli_query($con,$query);
            while($row = mysqli_fetch_assoc($result)){
                echo"<tr>
                <td>" . $row['name'] . "</td>
                <td>" . $row['location'] . "</td>
                <td>" . $row['date'] . "</td>
                <td>" . $hours = (int)$row['endTime'] - (int)$row['startTime'] . "</td>
                </tr>";
		        @$hours+=(int)$hours;
            }
		echo"
		<tr>
		<td><label>Total Hours</label></td>
		<td><label>". $hours ."</label></td>
		</tr>";
        }
        // date range for indiv_vol_hours report
        if($type == "indiv_vol_hours" && !$dateFrom == NULL && !$dateTo ==NULL){
            echo"
            <table>
            <tr>
                <th>Event</th>
                <th>Event Location</th>
                <th>Event Date</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbEvents.name, dbEvents.location,dbEvents.date,dbEvents.startTime,dbEvents.endTime,
            (dbEvents.endTime - dbEvents.startTime) AS DURATION
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
	    WHERE dbPersons.id='$indivID'
	    ORDER BY dbEvents.date desc";            
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result)){
		    foreach ($dd as $date) {
                        if(in_array($date, $dateRange)){
                            echo"<tr>
                            <td>" . $row['name'] . "</td>
                            <td>" . $row['location'] . "</td>
                            <td>" . $row['date'] . "</td>
                            <td>" . (int)$row['endTime'] - (int)$row['startTime'] . "</td>
                            </tr>";
                        }
                    }
		}
              } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }
        //only starting date range for indiv_vol_hours report
        if($type == "indiv_vol_hours" && !$dateFrom == NULL && $dateTo ==NULL){
            echo"
            <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Event</th>
                <th>Event Location</th>
                <th>Volunteer Hours</th>
            </tr>
            <tbody>";
            $con=connect();
            $type1 = "volunteer";
            $query = "SELECT dbPersons.id,dbPersons.first_name,dbPersons.last_name,dbPersons.phone1,dbPersons.email,
            dbEvents.name, dbEvents.location,dbEvents.startTime,dbEvents.endTime,
            (dbEvents.endTime - dbEvents.startTime) AS DURATION
            FROM dbPersons JOIN dbEventVolunteers ON dbPersons.id = dbEventVolunteers.userID
            JOIN dbEvents ON dbEventVolunteers.eventID = dbEvents.id
            GROUP BY dbPersons.first_name, dbPersons.last_name";
            $result = mysqli_query($con,$query);
            try {
                // Code that might throw an exception or error goes here
                $dd = getBetweenDates($dateFrom, $dateTo);
                $dateRange = @fetch_events_in_date_range_as_array($dateFrom, $dateTo)[0];
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($dd as $date){
                        if(in_array($date,$dateRange)){
                            echo"<tr>
                            <td>" . $row['first_name'] . "</td>
                            <td>" . $row['last_name'] . "</td>
                            <td>" . $row['name'] . "</td>
                            <td>" . $row['location'] . "</td>
                            <td>" . (int)$row['endTime'] - (int)$row['startTime'] . "</td>
                            </tr>";
                        }
                    }
                }
            } catch (TypeError $e) {
                // Code to handle the exception or error goes here
                echo "No Results found!"; 
            }
        }
        ?>
        </tbody>
        </table>
        <div class="center_b">
	    <a href="http://localhost/gwyneth/report.php">
            <button class = "theB">New Report</button>
            </a>
	    <a href="http://localhost/gwyneth/index.php">
            <button class = "theB">Home Page</button>
        </a></div>
        </main>
    </body>
</html>

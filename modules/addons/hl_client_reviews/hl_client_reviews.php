<?php if (!defined("WHMCS")) { die("This file cannot be accessed directly");}
use WHMCS\Database\Capsule;
/**
 * @author Mohannad Otaibi <mohannad.otaibi@gmail.com>
 * @company Hardlayers.net.sa 
 * @link https://github.com/hardlayers/WHMCS-ClientReviews
 * @date June 2017
 */
 
// Plugin Initial Configurations
function hl_client_reviews_config() {
    return array(
		"name" => "HL Client Reviews",
		"description" => "Client Reviews for WHMCS by Hardlayers (http://www.hl.net.sa/)",
		"version" => "1.0",
		'language' => 'arabic',
		"author" => "Hardlayers"
	);
}

// Activate function to create database table
function hl_client_reviews_activate() {

    # Create Custom DB Table
    $query = "CREATE TABLE IF NOT EXISTS `mod_hl_client_reviews` (
			  `id` int(10) NOT NULL auto_increment,
			  `userid` int(10) NOT NULL,
			  `review` longtext NOT NULL,
			  `active` tinyint(1) NOT NULL default '0',
			  `show_last_name` tinyint(1) NOT NULL default '0',
			  `show_email` tinyint(1) NOT NULL default '0',
			  `timestamp` timestamp NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM;";
	$result = full_query($query);

    # Return Result
    return array('status'=>'success','description'=>'Installed Successfully');
}

// Deactivate function to drop database table
// Actually, this is commented out, we don't want to delete reviews on deactivate
function hl_client_reviews_deactivate() {
    # Remove Custom DB Table
    // $query = "DROP TABLE `mod_hl_client_reviews`";
	// $result = full_query($query);

    # Return Result
    return array('status'=>'success','description'=>'deactivated successflly');
}

// This does all the magic at admin area
function hl_client_reviews_output($vars) {

    $modulelink = $vars['modulelink'];
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$review_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	
	#Start Processing the action if isset
		$message = null; //Set default value for status message
		
		if(isset($action)){
			if(isset($review_id)){
				switch($action){
					case "approve":
						Capsule::table('mod_hl_client_reviews')
								->where('id',$review_id)
								->update(['active' => 1]);
						break;
					case "unapprove":
						Capsule::table('mod_hl_client_reviews')
								->where('id',$review_id)
								->update(['active' => 0]);
						break;
					case "delete":
						Capsule::table('mod_hl_client_reviews')
								->where('id',$review_id)
								->delete();
						break;
					default: break;
				}
				$message = $action;
			}
		}
	
	#Get Reviews Count
		$reviews = Capsule::table('mod_hl_client_reviews')->orderBy('id', 'desc')->get();
		$reviews_count = count($reviews);
	#Get Pending Reviews Count
		$pending_reviews = Capsule::table('mod_hl_client_reviews')->where('active','0')->get();
		$pending_reviews_count = count($pending_reviews);
		
	###################################
	#	Display Everything
	########################
	#Header - Start Printing the Header
		print_header($reviews_count,$pending_reviews_count);	
	#Messages - Print status messages (success/fail/deleted/done)
		print_messages($message,$review_id);
	#Table - Print table
		print_table($reviews,$modulelink);
	#Footer - Print the footer
		print_footer();
}

// This does all the magic at clients area 
function hl_client_reviews_clientarea($vars){
	
	# Initial Values
		$show_email = true;
		$show_last_name = true;
		$message = null;
		$message_type = null;
		$review_message = null;
		$modulelink = $vars['modulelink'];
		$client_id = $_SESSION['uid'];
	
	# Capture Request (if any)
		if (isset($_POST['update'])) {
			$action = "update";
			$review_message = mysql_real_escape_string($_POST['review']);
			$show_email = mysql_real_escape_string($_POST['show_email']);
			$show_last_name = mysql_real_escape_string($_POST['show_last_name']);
		}
		else if (isset($_POST['add'])) {
			$action = "add";
			$review_message = mysql_real_escape_string($_POST['review']);
			$show_email = mysql_real_escape_string($_POST['show_email']);
			$show_last_name = mysql_real_escape_string($_POST['show_last_name']);
		}
		else if(isset($_POST['delete'])){ 
			$action = "delete";
		}

	# Start Processing
	if(isset($action)){
		switch($action){
			case "add":
				Capsule::table('mod_hl_client_reviews')->insert(
					[
					'userid' => $client_id, 
					'review' => $review_message,
					'active' => 0,
					'show_email' => $show_email,
					'show_last_name' => $show_last_name,
					'timestamp' => time(),
					]
				);
				$message = '<strong>ممتاز!</strong> لقد تم إستلام رأيك وسيتم تفعيله بأقرب وقت ممكن.';
				$message_type = 'success';
				break;
			case "update":
				Capsule::table('mod_hl_client_reviews')
				->where('userid',$client_id)
				->update(
					[
					'userid' => $client_id, 
					'review' => $review_message,
					'active' => 0,
					'show_email' => $show_email,
					'show_last_name' => $show_last_name,
					'timestamp' => time(),
					]
				);
				$message = '<strong>تم!</strong> لقد تم تحديث رأيك كما يظهر بالنموذج التالي.';
				$message_type = 'warning';
				break;
			case "delete":
				Capsule::table('mod_hl_client_reviews')
					->where('userid',$client_id)
					->delete();
				$message = '<strong>تم!</strong> لقد تم مسح رأيك بناء على طلبك.';
				$message_type = 'danger';
				break;
		}
	}

	# Get Client Reviews
		$review = Capsule::table('mod_hl_client_reviews')->where('userid',$client_id)->first();
		$review_status = "";
		if(!empty($review)){
			$review_status = Capsule::table('mod_hl_client_reviews')->where('userid',$client_id)->value('active');
			$review_message = $review->review;
			$show_email = $review->show_email;
			$show_last_name = $review->show_last_name;
		}
		
	# if message was not set earlier and the status is pending approval
	if($review_status == "0"){
		if(!isset($message)){
			$message = '<strong>بالإنتظار!</strong> لم يتم تفعيل الرأي بعد, سيتم تفيله في أقرب وقت ممكن.';
			$message_type = 'warning';
		}
	}
	
	# Now Return Everything
    return array(
        'pagetitle' => 'رأي العميل',
        'breadcrumb' => array('index.php?m=hl_client_reviews'=>'رأي العميل'),
        'templatefile' => 'clientreviewspage',
        'requirelogin' => true, # accepts true/false
        'forcessl' => false, # accepts true/false
        'vars' => array(
            'modulelink' => $modulelink,
			'review' => stripcslashes($review_message),
			'show_last_name' => $show_last_name,
			'show_email' => $show_email,
			'message' => $message,
			'message_type' => $message_type,
        ),
    );
}

// Utility Functions
function print_header($reviews,$pending_reviews){
	$pending_reviews_string = '';
	$reviews_string = ' Reviews';
	if($reviews == 1){
		$reviews_string =" Review";
	}
	if($pending_reviews>0){
		$pending_reviews_string = ' <small>('.$pending_reviews.' pending)</small>';
	}
	echo '<div class="hl_client_reviews_admin">
	<div class="">
		<div class="">
			<div class="">
				<div class="">
					<h2>'.$reviews.$reviews_string.$pending_reviews_string.'</h2>
					<hr />
				</div>
			</div>
		</div>';
}

function print_messages($message = null,$review_id = null){
	if(isset($message)){
		switch($message){
			case "approve":
				$css = 'success'; //danger - warning ..etc.
				$message = '<strong>Success!</strong> Review #'. $review_id .' Approved Successfully.';
				break;
			case "unapprove":
				$css = 'warning'; //danger - warning ..etc.
				$message = '<strong>Success!</strong> Review #'. $review_id .' Status Set to Unapproved.';
				break;
			case "delete":
				$css = 'danger'; //danger - warning ..etc.
				$message = '<strong>Done!</strong> Review '. $review_id .' deleted Successfully.';
				break;
		}
		
		echo '<div class="row">
			<div class="col-md-12">
				<div class="alert alert-'.$css.' role="alert">
					'.$message.'
				</div>
			</div>
		</div>';
	}
}

function print_table($reviews,$modulelink){
	echo '<div class="row">
			<div class="col-md-12">
				<table class="table">
					<thead>
						<tr>
							<th class="col-md-1">#</th>
							<th class="col-md-2">Client Name</th>
							<th class="col-md-2">Date Added</th>
							<th class="col-md-5">Review</th>
							<th class="col-md-2">Action</th>
						</tr>
					</thead>
					<tbody>';
					
	if(count($reviews)==0){
		echo '<tr><th colspan=5>There are no reviews at the moment!</th></tr>';
	}
	else{
		foreach($reviews as $review){
			$css = $review->active ? "" : ' class="warning"';
			$client = Capsule::table('tblclients')->where('id',$review->userid)->first();
			
			echo '<tr'.$css.'>
					<th scope="row">'.$review->id.'</th>
					<td>' . $client->firstname . ' ' . $client->lastname . '</td>
					<td>' . date("d/m/Y", $review->timestamp). '</td>
					<td>' . nl2br(stripcslashes($review->review)) . '</td>
					<td>';
			
			if($review->active == 0){
				echo '<a class="btn btn-default btn-success btn-xs" href="'.$modulelink.'&action=approve&id='.$review->id.'" role="button">Approve</a>';
			}else{
				echo '<a class="btn btn-default btn-default btn-xs" href="'.$modulelink.'&action=unapprove&id='.$review->id.'" role="button">Unpprove</a>';
			}
			echo '
						<!-- <a class="btn btn-default btn-xs" href="#" role="button">Edit</a> -->
						<a class="btn btn-default btn-danger btn-xs" href="'.$modulelink.'&action=delete&id='.$review->id.'" role="button">Delete</a>
					</td>
				</tr>'; 
		}
	}
	
	
	echo '</tbody>
				</table>
			</div>
		</div>';
}

function print_footer(){
	echo '<div class="row">
			<div class="col-md-12">
				<hr />
				<p class="pull-right">Developed by <a href="http://www.hl.net.sa">Hardlayers</a> 2017</p>
			</div>
		</div>
	</div>
  </div>';
}

?>
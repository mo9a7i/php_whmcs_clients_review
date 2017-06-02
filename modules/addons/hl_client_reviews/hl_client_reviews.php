<?php 
function hl_client_reviews_config() {
    $configarray = array(
    "name" => "HL Client Reviews",
    "description" => "Client Reviews for WHMCS by Hardlayers (http://www.hl.net.sa/)",
    "version" => "1.0",
    "author" => "Hardlayers");
    return $configarray;
}

function hl_client_reviews_activate() {

    # Create Custom DB Table
    $query = "CREATE TABLE IF NOT EXISTS `mod_hl_client_reivews` (
			  `id` int(10) NOT NULL auto_increment,
			  `userid` int(10) NOT NULL,
			  `review` longtext NOT NULL,
			  `active` tinyint(1) NOT NULL default '0',
			  `show_last_name` tinyint(1) NOT NULL default '0',
			  `show_email` tinyint(1) NOT NULL default '0',
			  `timestamp` longtext NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM;";
	$result = full_query($query);

    # Return Result
    return array('status'=>'success','description'=>'Installed Successfully');
    //return array('status'=>'error','description'=>'You can use the error status return to indicate there was a problem activating the module');
    //return array('status'=>'info','description'=>'You can use the info status return to display a message to the user');

}

function hl_client_reviews_deactivate() {

    # Remove Custom DB Table
    $query = "DROP TABLE `mod_hl_client_reivews`";
	$result = full_query($query);

    # Return Result
    return array('status'=>'success','description'=>'deactivated successflly');

}

function hl_client_reviews_output($vars) {

    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $option1 = $vars['option1'];
    $option2 = $vars['option2'];
    $option3 = $vars['option3'];
    $option4 = $vars['option4'];
    $option5 = $vars['option5'];
    $option6 = $vars['option6'];
    $LANG = $vars['_lang'];

    echo '<p>The date & time are currently ' . date("Y-m-d H:i:s") . '</p>';

}

function hl_client_reviews_clientarea($vars) {
 
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $option1 = $vars['option1'];
    $option2 = $vars['option2'];
    $option3 = $vars['option3'];
    $option4 = $vars['option4'];
    $option5 = $vars['option5'];
    $option6 = $vars['option6'];
    $LANG = $vars['_lang'];
 
    return array(
        'pagetitle' => 'آراء العملاء',
        'breadcrumb' => array('index.php?m=hl_client_reviews'=>'آراء العملاء'),
        'templatefile' => 'clientreviewspage',
        'requirelogin' => true, # accepts true/false
        'forcessl' => false, # accepts true/false
        'vars' => array(
            'testvar' => 'demo',
            'anothervar' => 'value',
            'sample' => 'test',
        ),
    );
 
}




?>
<?php
include_once("class.emma.php");
$email = strtolower(trim($_GET['email']));

////////////////////////////////////
//send the first time order email
$emma = new Emma();

$emma -> SignupPost	= "";	//the signup post id from your account
$emma -> AccountID	= "";	//the numeric account id of your account
$emma -> Username	= "";	//provided by emma suppport (not your account username)
$emma -> Password	= "";	//provided by emma suppport (not your account username)

$emma -> GroupID	= "";	// add them to this group id?
//$emma -> noConfirm();				//uncomment to suppress signup confirmation email
//$emma -> flushGroups();			//uncomment to flush user from all existing groups (if any)
//$emma -> deleteMember();			//uncomment to delete this member

$emma -> AddField("emma_member_email",$email);	
$emma -> AddField("emma_member_name_first",$first_name);
$emma -> AddField("emma_member_name_last",$first_name);
$emma -> AddField("emma_member_wildcard_{id}",$custom_field); 	//custom fields can be stacked also
$emma -> AddDateField("emma_member_wildcard_{id}",date('Y'),date('m'),date('d')); /* format yyyy,mm,dd date fields can be setup too*/

//$emma -> friendlyReturn();			//uncomment for friendly message in place of error code

$emma->Go(); 
echo "Resp: ".$emma->Resp." <br>";

?>
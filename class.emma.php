<?php
////////////////////////////////////////////////////
// PHP API class using cURL
// built for myemma.com
//
// Version 1.00, Created 01/21/2007
//
// Class for making API calls to EMMA API
// Uses the cURL server component
//
// Author: Jim Carter <jimcarter@me.com>
////////////////////////////////////////////////////

/**
 * emma - PHP transport class
 * @author Jim Carter
 */

//error_reporting(0); 

// Create new service for PHP Remoting as Class.
class emma {


    /////////////////////////////////////////////////
    // CONFIG VARIABLES
    /////////////////////////////////////////////////

    /**
     * Signup Post. Account Specific.
     * @public
     * @type int
     */
    var $SignupPost          = 0;

    /**
     * Account ID. Account Specific.
     * @public
     * @type int
     */
    var $AccountID           = 0;

    /**
     * Username. Account Specific.
     * @public
     * @type string
     */
    var $Username           = "";

    /**
     * Password. Account Specific.
     * @public
     * @type string
     */
    var $Password           = "";

    
	
	/////////////////////////////////////////////////
    // PUBLIC VARIABLES
    /////////////////////////////////////////////////


	/**
     * GroupID. Which Group you wish to join on submission.
     * @public
     * @type int
     */
    var $GroupID           = 0;

	/**
     * New Member email.The member's email address will be updated to this value. The failure code is -3.
     * @public
     * @type string
     */
    var $new_member_email    = "";



    /////////////////////////////////////////////////
    // PRIVATE VARIABLES
    /////////////////////////////////////////////////

    /**
     *  Holds all Field values.
     *  @type array
     */
    var $fields              = array();

    /**
     *  Holds Date Field values.
     *  @type array
     */
    var $fieldsDate          = array();



    /////////////////////////////////////////////////
    // OPTIONAL
    /////////////////////////////////////////////////

    /**
     * When this variable is passed and is set to '1' during an update call, the member will be removed from all groups to which they belonged.
	 * @public
     * @returns void
     */
    function flushGroups() {
        $this->flush_groups = 1;
    }

    /**
     * Delete Member.
     * Success Returns 4.
     * Failure Returns -5.
     * @public
     * @returns 4 or -5
     */
    function deleteMember() {
        $this->delete_member = 1;
    }

    /**
     * By default, all remote signups send a confirmation email. If this variable is passed, the confirmation email will not be sent.
	 * @public
     * @returns void
     */
    function noConfirm() {
        $this->no_confirm = 1;
    }


	
	/////////////////////////////////////////////////
    // RETURN METHODS
    /////////////////////////////////////////////////

    /**
     * By default, the response code will be sent back, when function called, a friendly message will be returned
	 * @public
     * @returns void
     */
    function friendlyReturn() {
        $this->friendlyReturn = 1;
    }

	
	/////////////////////////////////////////////////
    // PROCESS
    /////////////////////////////////////////////////

    /**
     * Adds a field to the post.  Returns void.
     * @public
     * @returns void
     */
    function AddField($field, $fieldVal) {
        $cur = count($this->fields);
        $this->fields[$cur][0] = trim($field);
        $this->fields[$cur][1] = trim($fieldVal);
    }

    /**
     * Adds a date field to the post.  Returns void.
     * @public
     * @returns void
     */
    function AddDateField($field, $fieldValYYYY, $fieldValMM, $fieldValDD) {
        $cur = count($this->fieldsDate);
        $this->fieldsDate[$cur][0] = trim($field);
        $this->fieldsDate[$cur][1] = trim($fieldValYYYY);
        $this->fieldsDate[$cur][2] = trim($fieldValMM);
        $this->fieldsDate[$cur][3] = trim($fieldValDD);
    }

    /**
     * Creates post string. If theres is an error then the function
     * returns false.  Use the ErrorInfo variable to view description of the error.  Returns bool.
     * @public
     * @returns bool
     */
    function Go() {

        if((count($this->fields) + count($this->fieldsDate)) < 1)
        {
            $this->response_handler("You must provide at least one field");
            return false;
        }

        // Create Post string
		$post  = "signup_post=".		$this->SignupPost;
		$post .= "&emma_account_id=".	$this->AccountID;
		$post .= "&username=".			$this->Username;
		$post .= "&password=".			$this->Password;

		
		// Append with Group
		if(!empty($this->GroupID))
			$post .= "&group".sprintf("[%s]=1", $this->GroupID);

		// Append with new_member_email
		if(!empty($this->new_member_email))
			$post .= "&new_member_email".sprintf("=%s", $this->new_member_email);

		// Append with Variable Methods
		if(!empty($this->flush_groups))
			$post .= "&flush_groups=1";

		if(!empty($this->delete_member))
			$post .= "&delete_member=1";

		if(!empty($this->no_confirm))
			$post .= "&no_confirm=1";


		// Append with Fields
		for($i = 0; $i < count($this->fields); $i++) {
            $post .= sprintf("&%s=", $this->fields[$i][0]);
            $post .= $this->fields[$i][1];
		}

		// Append with Dates
		for($i = 0; $i < count($this->fieldsDate); $i++) {
            $post .= sprintf("&%s=", $this->fieldsDate[$i][0]);
            $post .= $this->fieldsDate[$i][1];
            $post .= "-".$this->fieldsDate[$i][2];
            $post .= "-".$this->fieldsDate[$i][3];
		}


		// make the call
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://app.e2ma.net/app/view:RemoteSignup");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec ($ch);

		if($this->friendlyReturn) {
	        return $this->response_handler($response,1);
		}else{
	        return $this->response_handler($response,0);
		}

    }
    


    /////////////////////////////////////////////////
    // MISCELLANEOUS
    /////////////////////////////////////////////////

    /**
     * Adds the error message to the error container.
     * Returns value.
     * @private
     * @returns value
     */
    function response_handler($response,$friendly) {

		switch ($response) {
			case "1":
				$msg = "New Member Added";
				break;

			case "2":
				$msg = "Member Updated";
				break;
			
			case "3":
				$msg = "Member Already Exists";
				break;
			
			case "4":
				$msg = "Member Deleted";
				break;
			
			case "-1":
				$msg = "Authentication Problem";
				break;
			
			case "-2":
				$msg = "Failure Adding Member";
				break;
			
			case "-3":
				$msg = "Failure Updating Member";
				break;
			
			case "-5":
				$msg = "Failure Deleting Member";
				break;
			case "-6":
				$msg = "Invalid email address";
				break;
		}
		
		if($friendly == 0)
			$this->Resp = $response;

		if($friendly == 1)
			$this->Resp = $msg;
    
	}

}


?>
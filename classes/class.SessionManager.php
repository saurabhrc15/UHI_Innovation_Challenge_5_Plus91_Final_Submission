<?php
session_start();

/*!
 * @class SessionManager
 * This class manages the user session.
 */

class SessionManager {
    public $iUserID;
    public $sUserMobileNo;
    public $sUserName;


    //constructor
    function __construct(){

        // Start session
        session_start();

        $this->iUserID=$_SESSION['iUserID'];
        $this->sUserMobileNo=$_SESSION['sUserMobileNo'];
        $this->sUserName=$_SESSION['sUserName'];

        // Session close
        session_write_close();
    }

    public function fSetSessionData($aSessionData){

        // Start session
        session_start();

        // Set session data
        $_SESSION['iUserID'] = $_POST['user_id'];
        $_SESSION['sUserName'] = $_POST['user_name'];
        $_SESSION['sUserMobileNo'] = $_POST['mobile_number'];

        // Set data
        $this->iUserID=$_SESSION['iUserID'];
        $this->sUserMobileNo=$_SESSION['sUserMobileNo'];
        $this->sUserName=$_SESSION['sUserName'];

        // Session close
        session_write_close();
    }
}
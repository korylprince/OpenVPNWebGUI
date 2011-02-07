<?php
  //Uses options noSessionTime,sessionTime, sessionLibrary, sessionLogout, sessionID, passwordSalt, and hashFunction.
  function session_auth($username, $password, $options)
  {
      //Use other session library if specified.
      if (isset($options['sessionLibrary'])) {
          require_once($options['sessionLibrary']);
      }
      else {
        session_start();
      }
      //Logout
      if (isset($options['sessionLogout'])) {
          session_logout();
          return array('Login' => 'Restricted', 'flags' => array('logout'), 'errorCode' => 1);
      }
      //Sets default sessionTime if not already set.
      if (!isset($options['sessionTime'])) {
          $options['sessionTime'] = (10 * 60);
      }
      //Checks for noSessionTime. If exists eliminate it.
      if (isset($options['noSessionTime']) && $options['noSessionTime'] != 0) {
          unset($options['sessionTime']);
      }
      //Check if options exist
      if (!isset($options['hashFunction'])) {
          $options['hashFunction'] = 'md5';
      }
      if (!isset($options['passwordSalt'])) {
          $options['passwordSalt'] = '';
      }
      $hash = call_user_func($options['hashFunction'], uniqid() . $options['passwordSalt']);
      //If sessionID is sent we need to check if it matches.
      if (isset($options['sessionID'])) {
          if (!isset($_SESSION['ID'])) {
              $_SESSION['ID'] = $hash;
          }
          //Check if it matches the real ID
          if ($_SESSION['ID'] == $options['sessionID']) {
              //If sessionTime exists
              if (isset($options['sessionTime'])) {
                  //If session time variable exists check if time window is still open
                  if (isset($_SESSION['TIME'])) {
                      if (time() - $_SESSION['TIME'] < $options['sessionTime']) {
                          $data = array('username' => $_SESSION['username'], 'password' => $_SESSION['password'], 'timeLeft' => $options['sessionTime'] - (time() - $_SESSION['TIME']));
                          return array('Login' => 'True', 'data' => $data);
                      }
                      //Over Time limit
                      else {
                          session_logout();
                          return array('Login' => 'Restricted', 'flags' => array('timeLimit'), 'errorCode' => 2);
                      }
                  } else {
                      //If session time variable isn't set. This should never happen.
                      session_logout();
                      return array('Login' => 'Fail', 'errorCode' => 3);
                  }
              } else {
                  $data = array('username' => $_SESSION['username'], 'password' => $_SESSION['password']);
                  return array('Login' => 'True', 'data' => $data);
              }
          }
          //If sessionID doesn't match
          else {
              session_logout();
              return array('Login' => 'Restricted', 'errorCode' => 4);
          }
      }
      //If SessionID is not send then create one.
      $_SESSION['username'] = $username;
      $_SESSION['password'] = $password;
      $_SESSION['ID'] = $hash;
      $_SESSION['TIME'] = time();
      return array('Login' => 'None', 'data' => array('sessionID' => $_SESSION['ID']));
  }
  //Function that destroys all session data.
  function session_logout()
  {
      session_destroy();
      $_SESSION = array();
  }
?>

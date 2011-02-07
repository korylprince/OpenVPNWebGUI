<?php
  function authenticate($username, $password, $types, $options)
  {
      //Make sure that session is done first.
      if (in_array('session', $types)) {
          $types = array_unique(array_merge(array('session'), $types));
      }
      //Set the default auths location if not set
      if (!isset($options['auths_location'])) {
          $options['auths_location'] = './';
      }
      //If no data is returned, it is set to null; Needs to be outside loop
      $returnData = array();
      //If no flags are returned, it is set to null; Needs to be outside loop
      $returnFlags = array();
      //Loop for each authentication
      for ($i = 0; $i < count($types); $i++) {
          //if library exists include it. Otherwise return error.
          if (!file_exists($options['auths_location'] . 'auth_' . $types[$i] . '.php')) {
              return array('Login' => 'Error', 'errorCode' => 1, 'errorString' => 'Authentication Library does not exist!');
          }
          include($options['auths_location'] . 'auth_' . $types[$i] . '.php');
          //Call the library function
          $authReturnArray = call_user_func($types[$i] . '_auth', $username, $password, $options);
          //If username or password was stored it is now given back.
          if (isset($authReturnArray['data']['username'])) {
              $username = $authReturnArray['data']['username'];
              unset($authReturnArray['data']['username']);
          }
          if (isset($authReturnArray['data']['password'])) {
              $password = $authReturnArray['data']['password'];
              unset($authReturnArray['data']['password']);
          }
          //set returnErrorCode in case it isn't set.
          $returnErrorCode = null;
          if (isset($authReturnArray['errorCode'])) {
              $returnErrorCode = $types[$i] . '_' . $authReturnArray['errorCode'];
          }
          if (isset($authReturnArray['data'])) {
              $returnData = array_merge($returnData, $authReturnArray['data']);
          }
          if (isset($authReturnArray['flags'])) {
              $returnFlags = array_merge($returnFlags, $authReturnArray['flags']);
          }
          switch ($authReturnArray['Login']) {
              case 'Fail':
                  //Library encounters some error
                  return array('Login' => 'Fail', 'errorCode' => $returnErrorCode, 'flags' => $returnFlags);
              case 'Server':
                  //Library encounters some error with a server
                  return array('Login' => 'Server', 'errorCode' => $returnErrorCode, 'flags' => $returnFlags);
              case 'Restricted':
                  //User is not allowed to log in
                  return array('Login' => 'Restricted', 'errorCode' => $returnErrorCode, 'flags' => $returnFlags);
              case 'True':
                  //Library successfully authenticates user
                  $LoginPass = true;
                  break;
              case 'None':
                  //Library was not able to authenticate or restrict user
                  break;
              default:
                  //Library did not return right
                  return array('Login' => 'Error', 'errorCode' => 2, 'errorString' => 'Auth Library ' . $types[$i] . ' did not return correctly!');
          }
      }
      if (isset($LoginPass)) {
          return array('Login' => 'True', 'data' => $returnData, 'flags' => $returnFlags);
      } else {
          return array('Login' => 'Restricted', 'errorCode' => 3, 'errorString' => 'Unknown Username or bad Password!');
      }
  }
?>

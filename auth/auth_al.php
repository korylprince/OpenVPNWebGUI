<?php
  //Uses options al_location
  //al.list should contain a json encoded array of allowed names: ["User1", "User2"]
  function al_auth($username, $password, $options)
  {
      //Check for require al_location 
      if (!isset($options['al_location'])) {
          return array('Login' => 'Fail', 'errorCode' => 1);
      }
      $json = file_get_contents($options['al_location']);
      //Check that file exists and has data
      if ($json == false) {
          return array('Login' => 'Fail', 'errorCode' => 2);
      }
      $list = json_decode($json);
      //Check if contents is really json
      if ($list == null) {
          return array('Login' => 'Fail', 'errorCode' => 3);
      }
      //check if name is in the list
      if (in_array($username, $list)) {
          return array('Login' => 'None');
      } else {
          return array('Login' => 'Restricted', 'errorCode' => 4);
      }
  }
?>

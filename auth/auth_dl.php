<?php
  //Uses options dl_location
  //dl.list should contain a json encoded array of disallowed names: ["User3", "User4"]
  function dl_auth($username, $password, $options)
  {
      //Check for require dl_location 
      if (!isset($options['dl_location'])) {
          return array('Login' => 'Fail', 'errorCode' => 1);
      }
      $json = file_get_contents($options['dl_location']);
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
      if (!in_array($username, $list)) {
          return array('Login' => 'None');
      } else {
          return array('Login' => 'Restricted', 'errorCode' => 4);
      }
  }
?>

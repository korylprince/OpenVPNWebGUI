<?php
  //checks if username is "administrator" and returns a flag if so.
  function admincheck_auth($username, $password, $options)
  {
      if ($username == "administrator") {
          return array('Login' => 'None', 'flags' => array('admin'));
      } else {
          return array('Login' => 'None');
      }
  }
?>

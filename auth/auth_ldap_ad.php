<?php
  //Uses options ldap_ad_server, ldap_ad_port, ldap_ad_domain, ldap_data, ldap_ad_allowed_groups,ldap_ad_disallowed_groups
  //Requires php5-ldap
  function ldap_ad_auth($username, $password, $options)
  {
      //Password cannot be blank or will return fake authentication in some cases
      if ($password == '') {
          return array('Login' => 'None');
      }
      //Check if server is specified
      if (!isset($options['ldap_ad_server'])) {
          return array('Login' => 'Fail', 'errorCode' => 1);
      }
      //Check if domain is specified
      if (!isset($options['ldap_ad_domain'])) {
          return array('Login' => 'Fail', 'errorCode' => 2);
      }
      //Check if port is specified
      if (!isset($options['ldap_ad_port'])) {
          $options['ldap_ad_port'] = 389;
      }
      //Check if Server is up
      $check = @fsockopen($options['ldap_ad_server'], $options['ldap_ad_port'], $errno, $errstr, 2);
      if (!$check) {
          return array('Login' => 'Server', 'errorCode' => 3);
      }
      //Start connection with AD options
      $ad_connection = ldap_connect($options['ldap_ad_server'], $options['ldap_ad_port']);
      ldap_set_option($ad_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_set_option($ad_connection, LDAP_OPT_REFERRALS, 0);
      if (!$ad_connection) {
          return array('Login' => 'Server', 'errorCode' => 4);
      }
      //Try authenticating
      if (@ldap_bind($ad_connection, $username . '@' . $options['ldap_ad_domain'], $password)) {
          //If data is needed, get it
          if (isset($options['ldap_data']) || isset($options['ldap_ad_allowed_groups']) || isset($options['ldap_ad_disallowed_groups'])) {
              $filter = 'userPrincipalName=' . $username . '@' . $options['ldap_ad_domain'];
              $dc = explode('.', $options['ldap_ad_domain']);
              //convert example.com to dc=example,dc=com
              $searchLocation = 'dc=' . $dc[0] . ',dc=' . $dc[1];
              $attributes = ldap_search($ad_connection, $searchLocation, $filter);
              $userData = ldap_get_entries($ad_connection, $attributes);
              //Set data in case we don't get any
              $data = null;
              if (isset($options['ldap_ad_data'])) {
                  //Loop throught results to get all the values
                  for ($i = 0; $i < count($options['ldap_ad_data']); $i++) {
                      $dataValue = null;
                      for ($k = 0; $k < $userData[0][$options['ldap_ad_data'][$i]]['count']; $k++) {
                          $dataValue[$k] = $userData[0][$options['ldap_ad_data'][$i]][$k];
                      }
                      $data[$options['ldap_ad_data'][$i]] = $dataValue;
                  }
              }
              //If an entry in the allowed groups agrees with one of the users groups then authenticate.
              if (isset($options['ldap_ad_allowed_groups'])) {
                  if (array_intersect($userData[0]['memberof'], $options['ldap_ad_allowed_groups'])) {
                  } else {
                      return array('Login' => 'Restricted', 'errorCode' => 5);
                  }
              }
              //If an entry in the disallowed groups agrees with one of the users groups then don't authenticate.
              if (isset($options['ldap_ad_disallowed_groups'])) {
                  if (array_intersect($userData[0]['memberof'], $options['ldap_ad_disallowed_groups'])) {
                      return array('Login' => 'Restricted', 'errorCode' => 6);
                  } else {
                  }
              }
              return array('Login' => 'True', 'data' => $data);
          }
          //If no data was needed
          else {
              return array('Login' => 'True');
          }
      }
      //If first authentication failed
      else {
          return array('Login' => 'None');
      }
  }
?>

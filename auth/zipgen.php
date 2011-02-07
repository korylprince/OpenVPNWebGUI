<?php
#Copyright Kory Prince 2011
/* Get data from POST */
if(!isset($_POST['jsondata']['data']['sessionID'])) {
    $returnArray = array('login'=>'Error', 'errorCode'=>1);
    echo json_encode($returnArray);
}
else {
    $options['sessionID'] = $_POST['jsondata']['data']['sessionID'];
}
require_once('options.php');
include('authlib.php');
$login = authenticate(null,null,$types,$options);
//echo json_encode($login);return 0;//debug
if($login['Login'] != 'True'){
    $returnArray = array('login'=>'Error', 'errorCode'=>2);
    echo json_encode($returnArray);
    return 0;
}
if(isset($login['flags'][0])){
        if(in_array('admin',$login['flags']) && isset($_POST['jsondata']['username'])){$username = $_POST['jsondata']['username']; $admin = true;}
    }
    if(isset($login['data']['samaccountname'][0])){$username = $login['data']['samaccountname'][0];}
    if(!isset($username)){
        $returnArray = array('login'=>'Error', 'errorCode'=>3);
        echo json_encode($returnArray);
        return 0;
    }
    $os = $_POST['jsondata']['os']; 
    $iflinux = $ifmac = $ifwindows7 = ';';
    $ifwindows = '';
    if ($os == 'Linux'){$iflinux='';}
    if ($os == 'Mac'){$ifmac='';}
    if ($os == 'Windows'){$ifwindows=';'; $ifwindows7 = '';}
    if (file_exists('/etc/openvpn/easy-rsa/keys/'.$username.'.crt')) {
        mkdir($_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username, 0700);
        shell_exec('rm '.$_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/*');
        shell_exec('rm '.$_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'.zip');
        copy('/etc/openvpn/easy-rsa/keys/'.$username.'.key', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/'.$username.'.key');
        copy('/etc/openvpn/easy-rsa/keys/'.$username.'.crt', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/'.$username.'.crt');
        copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/keys/ta.key', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/ta.key');
        copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/keys/ca.crt', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/ca.crt');
        if ($os == 'Mac'){
            copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/clients/tunnelblick.dmg', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/tunnelblick.dmg');
            copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/readmes/macReadme', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/macReadme');
        }
        if ($os == 'Windows'){
            copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/clients/openvpngui.exe', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/openvpngui.exe');
            copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/readmes/WindowsReadme.txt', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/WindowsReadme.txt');
        }
        if ($os == 'Linux'){
            copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/readmes/LinuxReadme', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/LinuxReadme');
        }
    }
    else {
        shell_exec('sh /etc/openvpn/easy-rsa/pkitool '.$username);
        mkdir($_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username, 0700);
        shell_exec('rm '.$_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/*');
        shell_exec('rm '.$_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'.zip');
        copy('/etc/openvpn/easy-rsa/keys/'.$username.'.key', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/'.$username.'.key');
        copy('/etc/openvpn/easy-rsa/keys/'.$username.'.crt', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/'.$username.'.crt');
        copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/keys/ta.key', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/ta.key');
        copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/keys/ca.crt', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/ca.crt');
        if ($os == 'Mac'){
            copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/clients/tunnelblick.dmg', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/tunnelblick.dmg');
            copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/readmes/macReadme', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/macReadme');
        }
        if ($os == 'Windows'){copy($_SERVER['DOCUMENT_ROOT'].'/vpn/files/clients/openvpngui.exe', $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/openvpngui.exe');}
    }

    
$config = <<< EOT
##############################################
# Bullard ISD OpenVPN Configuration File     #
# for connecting to multi-client server.     #
# On Windows rename this file so it has an   #
# .ovpn extension                            #
##############################################

client

#Needed on Linux
${iflinux}up /etc/openvpn/update-resolv-conf
${iflinux}down /etc/openvpn/update-resolv-conf

#Needed on Windows
${ifwindows7}route-method exe
${ifwindows7}route-delay 2

#Comment out on Windows
${ifwindows}remote-cert-tls server

dev tap

proto udp

remote 72.53.182.16 1194

resolv-retry infinite

nobind

keepalive 10 120

persist-key
persist-tun

comp-lzo

verb 3

ca ca.crt
cert $username.crt
key $username.key
tls-auth ta.key 1
EOT;

if ($os != 'Windows'){
        $fh = fopen($_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/bullardisd.conf', 'wb');
        fwrite($fh, $config);
        fclose($fh);
    }
    else {
        $config = iconv('UTF-8', 'ASCII', $config);
        $config = str_replace('\n','\r\n',$config);
        $fh = fopen($_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'/bullardisd.ovpn', 'wb');
        fwrite($fh, $config);
        fclose($fh);
    }
    shell_exec('cd '.$_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/; zip -r '.$_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'.zip ./'.$username.'/');
    if (isset($admin)) {
        $arr = array ('login'=>'Admin');
        echo json_encode($arr);
        return 0;
    }
    $arr = array ('login'=>'Yes');
        echo json_encode($arr);
        return 0;
?>

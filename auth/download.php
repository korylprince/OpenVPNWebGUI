<?#Copyright Kory Prince 2011
/* Get data from POST */
if(!isset($_GET['sessionID'])) {
    $returnArray = array('login'=>'Error', 'errorCode'=>1);
    echo json_encode($returnArray);
}
else {
    $options['sessionID'] = $_GET['sessionID'];
}
require_once('options.php');
include('authlib.php');
$login = authenticate(null,null,$types,$options);
//echo json_encode($login);return 0;//debug
if(isset($login['flags'])){
        if(in_array('admin',$login['flags']) && isset($_GET['username'])){$username = $_GET['username'];}
    }
    if(isset($login['data']['samaccountname'][0])){$username = $login['data']['samaccountname'][0];}
    if(!isset($username)){
        $returnArray = array('login'=>'Error', 'errorCode'=>2);
        echo json_encode($returnArray);
        return 0;
    }
if($login['Login'] != "True"){
    $returnArray = array('login'=>'Error', 'errorCode'=>3);
    echo json_encode($returnArray);
    return 0;
}
if(!isset($_GET['os'])){
    $returnArray = array('login'=>'Error', 'errorCode'=>4);
    echo json_encode($returnArray);
    return 0;
}
else {$os = $_GET['os'];}
if(!in_array($os,array("Windows","Mac","Linux"))) {
    $returnArray = array('login'=>'Error', 'errorCode'=>5);
    echo json_encode($returnArray);
    return 0;
}
    $path = $_SERVER['DOCUMENT_ROOT'].'/vpn/files/tmp/'.$username.'.zip';    
        if ($fd = fopen ($path, "r")) {
        $fsize = filesize($path);
        $path_parts = pathinfo($path);
        if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
        header("Cache-control: no-cache");
        header("Content-type: application/zip");
        }
        else {
            header("Cache-Control: private");
            header("Pragma: cache");
            header("Expires: 0");
            header("Content-type: application/force-download");
        }
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"".$username.$os.".zip"."\"");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($path));
        header("Cache-control: private");
        while(!feof($fd)) {
            $buffer = fread($fd, 2048);
            echo $buffer;
        }
    }
    fclose ($fd);
    exit;
?>

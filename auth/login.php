<?#Copyright Kory Prince 2011
/* Get data from POST */
$postVariable = 'jsondata';
if (!isset($_POST[$postVariable])){dieWithError('postVarible does not exist!',1);}
$postData = $_POST[$postVariable];
if (!isset($postData['username'])){dieWithError('username does not exist!',2);}
if (!isset($postData['password'])){dieWithError('password does not exist!',3);}
$username = $postData['username'];
$password = $postData['password'];
require_once('options.php');
include('authlib.php');
$login = authenticate($username,$password,$types,$options);
//echo json_encode($login);return True;//debug
$return['login'] = $login['Login'];
if(isset($login['data'])){$return['data'] = $login['data'];}
if(isset($login['flags'])){$return['flags'] = $login['flags'];}
if($login['Login']=="Server" && isset($login['errorCode'])){$return['errorCode'] = $login['errorCode'];}
echo json_encode($return);
return 0;

function dieWithError($string, $errorCode) {
    $returnArray = array('login'=>'Error', 'errorCode'=>$errorCode);
    echo json_encode($returnArray);
    die($string);
}
?>

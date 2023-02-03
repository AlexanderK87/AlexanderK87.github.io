
<?
session_start();
$db = mysqli_connect('localhost', 'user', 'password', 'name_db');
if (!$db) {die("Connection failed: " . mysqli_connect_error());}
mysqli_set_charset($db,"utf8");

$command = $_POST['command'];
if (strpos($command, ';') != false) 
{$commands = explode(';', $command);
 if($commands[count($commands)-1] == '') {unset($commands[count($commands)-1]);} 
}
else {$commands[0] = $command;}


$_SESSION['command'] = $command;
//$_SESSION['commands'] = $commands;
$array_gifts = ['A','B','C','D','E'];

function checkCode () {
session_start();    
global $commands;
foreach($commands as $val) {
    if (corr($val)) {$flag = true;}
    else {$flag = false; break;}
}
$_SESSION['flag'] = $flag;
return $flag;    
}

function corr ($comm) {
global $array_gifts;
$divider = explode(':', $comm);  
if (checkUser($divider[0])) {
    if ( (ctype_digit($divider[1])) && (!isset($divider[2])) ) {
        return true;}
    else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (!isset($divider[3])) ) {    
        return true;}
    else if ( ( in_array($divider[1], $array_gifts) ) && (!isset($divider[2])) ) {
        return true;}
    else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (in_array($divider[3], $array_gifts)) ) {    
        return true;}
    else {$_SESSION['err']='Ошибка в коде пользователя "'.$divider[0].'"'; return false;}
}

else {$_SESSION['err']='Такого пользователя "'.$divider[0].'" в базе нет';
     return false;
}

}

if (checkCode()) {
foreach($commands as $val) {
handler($val);}
header("Location: https://mathexpert.uz/temp/admin.php");    
}

else {header("Location: https://mathexpert.uz/temp/admin.php");}

function handler ($comm) {
global $array_gifts;
$divider = explode(':', $comm); 

if (checkUser($divider[0])) {
    
    if ( (ctype_digit($divider[1])) && (!isset($divider[2])) ) {
        $login = $divider[0]; $money = $divider[1];
        updateBD($login, $money, 0, '');
    }
    
    else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (!isset($divider[3])) ) {
        $login = $divider[0]; $money = $divider[1]; $balli = $divider[2];
        updateBD($login, $money, $balli, '');
    }
    
    else if ( ( in_array($divider[1], $array_gifts) ) && (!isset($divider[2])) ) {
        $login = $divider[0]; $gift = $divider[1];
        updateBD($login, 0, 0, $gift);
    }
    
    else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (in_array($divider[3], $array_gifts)) ) {
        $login = $divider[0]; $money = $divider[1]; $balli = $divider[2]; $gift = $divider[3];
        updateBD($login, $money, $balli, $gift);
    }
    
    else {$_SESSION['err']='Ошибка в коде команды';
     header("Location: https://mathexpert.uz/temp/admin.php");}
    
    //header("Location: https://mathexpert.uz/temp/admin.php");
}

else {$_SESSION['err']='Такого пользователя в базе нет';
     header("Location: https://mathexpert.uz/temp/admin.php");
}   
    
}

function updateBD ($login, $money, $balli, $gift) {
$db = mysqli_connect('localhost', 'user', 'password', 'name_db');
if (!$db) {die("Connection failed: " . mysqli_connect_error());}
mysqli_set_charset($db,"utf8");
$res = mysqli_query($db,"SELECT * FROM `migrations` WHERE `login` = '$login';");
$user = mysqli_fetch_assoc($res);
$user_money = $user['balance_money']+$money;
$user_balli = $user['balance_balli']+$balli;
$balance_gifts = json_decode($user['balance_gifts'], true);
if ($balance_gifts == '') { if ($gift != '') {$balance_gifts = []; $balance_gifts[0] = $gift;}}
else { if ($gift != '') {$balance_gifts[]=$gift; }}
if ($balance_gifts != '') {$gifts = json_encode($balance_gifts);}
else {$gifts='';}
$sql1 = mysqli_query($db, "UPDATE `migrations` SET `balance_money` = '$user_money', `balance_balli` = '$user_balli',
`balance_gifts` = '$gifts' WHERE `$migrations`.`login` = '$login';");
$res2 = mysqli_query($db,"SELECT * FROM `migrations` WHERE `login` = 'base';");
$base = mysqli_fetch_assoc($res2);
$base_money = $base['balance_money']-$money;
if ($gift != '') {$base_gifts = $base['balance_gifts']-1;}
else {$base_gifts = $base['balance_gifts'];}
$sql2 = mysqli_query($db, "UPDATE `migrations` SET `balance_money` = '$base_money',
`balance_gifts` = '$base_gifts' WHERE `$migrations`.`login` = 'base';");

if ( ($sql1) && ($sql2)) {$_SESSION['msg']='Команда успешно выполнена';}
else {$_SESSION['err']='Ошибка при вставки данных в базу';}

header("Location: https://mathexpert.uz/temp/admin.php");
}


function checkUser ($login) {
$db = mysqli_connect('localhost', 'user', 'password', 'name_db');
if (!$db) {die("Connection failed: " . mysqli_connect_error());}
mysqli_set_charset($db,"utf8");
$res = mysqli_query($db,"SELECT * FROM `migrations` WHERE `login` = '$login';");
$user = mysqli_fetch_assoc($res);
$_SESSION['res'] = $res;
$_SESSION['user_login'] = $login;
$_SESSION['user'] = $user;
if ( ($user) && ($user['login'] != 'admin') && ($user['login'] != 'base') ) {return true;}
else {return false;}
}


?>
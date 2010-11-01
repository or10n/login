<?php

  $login = $_POST['login'];	
  $pass = $_POST['pass'];
	$host = $_SERVER['REMOTE_ADDR'];

  if($login == '' || $pass == ''){
    //echo "<font colot=red>Need enter login and password!</font>";
		$error = 'empty';
		
	} else {

  require_once('db.inc.php');
  require_once('functions.inc.php');

	mysql_connect($dbhost,$dbuser,$dbpass) or die ("can't connect to database");
  mysql_select_db($dbname);
 
  
  $error = loaderror($host);
	if($error > 5) {
    //echo 'Hackinig attempt!';
		$error='hack';
	} else {
	if($error > 0)
	  sleep(5);
	if($error > 2)
	  sleep(5);

  $prot_login = protect($login); 

  $sql = "SELECT pass,status FROM hna_users WHERE login='$prot_login' LIMIT 1";
   
	$res = mysql_query($sql);
  
  if(!$res){
		echo 'ERROR!';
		exit;
  }

	$data = mysql_fetch_array($res);
  
	if($pass !== $data['pass']){
    //echo 'Error in login or pass';
		saveerror($host);
		$error = 'pass';
	} else {

  if($data['status'] == 1){
	  $error = 'ban';
	}

	if($data['status'] == 2){
    $error = 'arch';
	}

  if($data['status'] == 0 || $data['status'] == 3){
    //echo 'Congratulations!';
		$error='allgood';
    exec("/srv/www/login/scripts/login.sh $host $login");
    mysql_query("DELETE FROM hna_security WHERE host='$host'");
  }

  }
  } 
  }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru"> 
<head> 
  <title>Страница управления аккаунтом</title> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    <link rel='stylesheet' id='login-css'  href='css/login.css' type='text/css' media='all' /> 
    <link rel='stylesheet' id='colors-fresh-css'  href='css/colors-fresh.css' type='text/css' media='all' /> 
</head> 
<body> 
<?php

if($error=='ban')
	echo '<div id="error"><br>Ваша учетная запись залокирована, пожалуйста обратитесь к администратору сети</div>';

if($error=='arch')
	echo '<div id="error"><br>Ваша учетная запись помещена в архив, пожалуйста обратитесь к администратору</div>';

if($error=='pass')
	echo '<div id="error"><br>Неверный логин или пароль.<br>После 6 неправльных попыток IP-адрес будет заблокрован на сутки</div>';

if($error=='empty')
	echo '<div id="error"><br>Необходимо ввести логин и пароль</div>';

if($error=='hack')
	echo '<div id="error"><br>Обнаружена попытка взлома.<br>Ваш IP-адрес заблокирован на сутки</div>';

if($error=='allgood')
	echo '<div id="info"><br>Поздравляем!<br>Доступ к ресурсам сети открыт.</div>';


?>
</body>

<?php

require_once('db.inc.php');

function protect($str)
{
  $str = strip_tags($str);
  $str = htmlspecialchars($str);
  $str = stripslashes($str);												     
  $str = addslashes($str); 

  return $str;
}

function loaderror($host)
{
//  mysql_connect($dbhost,$dbuser,$dbpass) or die('mysql can\'t connect');
//	mysql_select_db($dbname);
  mysql_query("DELETE FROM hna_security WHERE date < NOW() - INTERVAL 1 DAY"); // удаляем записи старше 1 дня
  $res = mysql_query("SELECT COUNT(*) AS count FROM hna_security WHERE host='$host'");

  $data = mysql_fetch_assoc($res);

	return $data['count'];
 
//  mysql_close();
}

function saveerror($host)
{
//  mysql_connect($dbhost,$dbuser,$dbpass) or die('mysql can\'t connect');
//	mysql_select_db($dbname);

  mysql_query("INSERT INTO hna_security (action,host) VALUES ('1','$host')");
 
//  mysql_close();
}
?>

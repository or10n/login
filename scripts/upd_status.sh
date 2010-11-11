#!/bin/bash

. /srv/www/login/scripts/config.inc

#Month (format: XX)
MONTH=`date +%m | sed s/^0//g` 
#Month (format: Месяц)
BMONTH=`date +%B`

#BANLIST from hna_pays
BANID=`mysql --batch -u$DBUSER -p$DBPASS $DBNAME -e "SELECT hna_pays.user_id FROM hna_users,hna_pays WHERE (hna_pays.$MONTH='0' OR hna_pays.connect=0) AND (hna_users.note=NULL OR hna_users.note='') AND hna_usres.status=0;" | grep -v user_id`
#UNBANLIST from hna_pays
UNBANID=`mysql --batch -u$DBUSER -p$DBPASS $DBNAME -e "SELECT hna_pays.user_id FROM hna_pays,hna_users WHERE hna_pays.$MONTH=1 AND (hna_users.note=NULL OR hna_users.note='') AND hna_users.connect=1 AND hna_users.status=1;" | grep -v user_id`
#BAN Cycle
for ID in $BANID;
	do
	#ban people
	mysql --batch -u$DBUSER -p$DBPASS $DBNAME -e "UPDATE hna_users SET pass=CONCAT(pass,'%'), status=1 WHERE user_id=$ID";
	#logging
	mysql --batch -u$DBUSER -p$DBPASS $DBNAME -e "INSERT INTO hna_log_users (user_id,admin_id,time,action,message) VALUES ($ID,1,NOW(),4,\"Автобан\");";
	done;
#UNBAN Cycle
for ID in $UNBANID;
	do
	#unban people
	mysql --batch -u$DBUSER -p$DBPASS $DBNAME -e "UPDATE hna_users SET pass=TRIM(TRAILING '%' FROM pass), status=0 WHERE user_id=$ID"
	#logging
	mysql --batch -u$DBUSER -p$DBPASS $DBNAME -e "INSERT INTO hna_log_users (user_id,admin_id,time,action,message) VALUES ($ID,1,NOW(),4,\"Авторазбан\");";
  done;


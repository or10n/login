#!/bin/bash

MAC=`sudo /usr/sbin/arp -a | grep "($1)" | awk '{ print $4 }'`


# дабавляем новые IP/MAC для введенного логина в DB
BANS=$( mysql -u user --password="" hnabase -e "SELECT lastip FROM hna_users WHERE status=1;" | grep -v lastip )

# удаляем из ipset'a забаненых пользователей
for IP in $BANS; do
  if [ $IP != NULL ]; then
	  sudo /usr/local/sbin/ipset -D ipmacs $IP
		mysql -u user --password="" hnabase -e "UPDATE hna_users SET lastip=\"\",lastmac=\"\",endlease=\"0000-00-00\";"
	  #echo $IP"\n"
	fi	
done

#echo $MAC $2 $1;

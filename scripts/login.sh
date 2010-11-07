#! /bin/bash

#подключаем конфигурационный файл
. /srv/www/login/scripts/config.inc

MAC=`sudo /usr/sbin/arp -a | grep "($1)" | awk '{ print $4 }'`

# получаем значение последнего IP для введенного логина
LASTIP=`mysql --batch -u $DBUSER --password="$DBPASS" $DBNAME -e "SELECT lastip FROM hna_users WHERE login='$2';" | grep -v lastip`

# удаляем старый lastip из ipset'a для введенного логина
sudo /usr/local/sbin/ipset -D ipmacs $LASTIP

# дабавляем новые IP/MAC для введенного логина в DB
mysql -u $DBUSER --password="$DBPASS" $DBNAME -e "UPDATE hna_users SET  lastip=\"$1\",lastmac=\"$MAC\",endlease=NOW()+INTERVAL 3 DAY WHERE login=\"$2\";"

# добавляем новые IP/MAC для введенного логина в ipaet
sudo /usr/local/sbin/ipset -A ipmacs $1,$MAC

#echo $MAC $2 $1;

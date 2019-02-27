#/bin/bash
dbhost=192.168.0.40
dbpass=alexaner2013
dbuser=localalex

mysql -h192.168.0.40 -palexander2013 -ulocalalex worldsru -e "call copy_worlds_to_utf8"

mysqldump -h192.168.0.40 -ulocalalex -palexander2013 worldsru_utf8 history hotels hotels_pic > base/worldsru_1.dump
gzip -f base/worldsru_1.dump
mysqldump -h192.168.0.40 -ulocalalex -palexander2013 worldsru_utf8 cities Anekdot > base/worldsru_2.dump
gzip -f base/worldsru_2.dump
mysqldump -h192.168.0.40 -ulocalalex -palexander2013 worldsru_utf8 --ignore-table={worldsru_utf8.Anekdot,worldsru_utf8.hotels_pic,worldsru_utf8.history,worldsru_utf8.cities} > base/worldsru_3.dump 
gzip -f base/worldsru_3.dump

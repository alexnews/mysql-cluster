#/bin/bash
gunzip base/worldsru_1.dump.gz
mysql -h127.0.0.1 -P3312 -uroot -pTest000000 test < base/worldsru_1.dump
gunzip -f base/worldsru_2.dump.gz
mysql -h127.0.0.1 -P3312 -uroot -pTest000000 test < base/worldsru_2.dump
gunzip -f base/worldsru_3.dump.gz
mysql -h127.0.0.1 -P3312 -uroot -pTest000000 test < base/worldsru_3.dump
gzip base/worldsru_3.dump
gzip base/worldsru_1.dump
gzip base/worldsru_2.dump

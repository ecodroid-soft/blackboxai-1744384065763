#!/bin/bash
pkill -9 mysqld
rm -rf /var/run/mysqld/* /var/lib/mysql/*
mkdir -p /var/run/mysqld /var/lib/mysql
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql
mysqld --initialize-insecure --user=mysql
mysqld --user=mysql --bind-address=127.0.0.1 --port=3306 &
sleep 5
mysql -h 127.0.0.1 -P 3306 -u root -e "CREATE DATABASE IF NOT EXISTS pofinfraa_db;"
mysql -h 127.0.0.1 -P 3306 -u root pofinfraa_db < database/schema.sql

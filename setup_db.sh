#!/bin/bash

# Kill any existing MySQL process
pkill -9 mysqld

# Clean and create MySQL directories
rm -rf /var/run/mysqld/* /var/lib/mysql/*
mkdir -p /var/run/mysqld /var/lib/mysql
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql

# Initialize MySQL
mysqld --initialize-insecure --user=mysql

# Start MySQL server
mysqld --user=mysql --bind-address=127.0.0.1 --port=3306 &

# Wait for MySQL to start
sleep 5

# Create database and import schema
mysql -h 127.0.0.1 -P 3306 -u root << 'EOF'
DROP DATABASE IF EXISTS pofinfraa_db;
CREATE DATABASE pofinfraa_db;
USE pofinfraa_db;
source database/schema.sql;
EOF

# Create upload directories
mkdir -p uploads/sliders uploads/projects
chmod -R 777 uploads

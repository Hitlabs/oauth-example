CREATE DATABASE IF NOT EXISTS pronto_oauth_local;
CREATE USER IF NOT EXISTS 'pronto'@'%' IDENTIFIED WITH mysql_native_password BY 'pronto';
GRANT ALL PRIVILEGES ON *.* TO 'pronto'@'%';

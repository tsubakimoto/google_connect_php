<?php

/*
 
create database m_dotinstall_google_connect_php;
grant all on m_dotinstall_google_connect_php.* to matsumura@localhost;
 
use m_dotinstall_google_connect_php
 
create table users (
id int not null auto_increment primary key,
google_user_id varchar(30) unique,
google_email varchar(255),
google_name varchar(255),
google_picture varchar(255),
google_access_token varchar(255),
created datetime,
modified datetime
);
 
*/

define('DSN', 'mysql:host=localhost;dbname=m_dotinstall_google_connect_php');
define('DB_USER', 'matsumura');
define('DB_PASSWORD', 'matsumura');

define('CLIENT_ID', '241748568054-kcpv77op52gf184a41a9hnd1nhuf6lhj.apps.googleusercontent.com');
define('CLIENT_SECRET', 'wmBaFtIvT8vA9i-vKQz1tND-');

define('SITE_URL', 'http://ma.snm.dip.jp/php/google_connect_php/');

error_reporting(E_ALL & ~E_NOTICE);

session_set_cookie_params(0, '/php/google_connect_php/');

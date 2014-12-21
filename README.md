# LibreSSL Documentation

(c) 2005-2015 by CAcert Inc.  
License: GNU-GPLv2

## System Requirements:

 * Linux/POSIX
 * apache2 (apache2)
 * PHP 5.3 (libapache2-mod-php5)
 * mySQL 5.5 (mysql-server-5.5 php5-mysql)
 * FPDF - PDF generation library (php-fpdf php5-recode)
 * GetText PECL module for PHP (php-gettext gettext)
 * OpenSSL - X.509 toolkit (openssl)
 * openssl-vulnkey including blacklists for all common key sizes (openssl-blacklist)
 * GnuPG - OpenPGP toolkit (gnupg)
 * whois - whois client (whois)
 * CommModule - CAcert Communication Module

## Installation
### tools setup

<install a fresh debian minimal container>
$ cat /etc/debian_version
7.6
$ cat /etc/apt/sources.list
deb http://cdn.debian.net/debian wheezy main

apt-get install wget git make gettext
git clone https://github.com/CAcertOrg/cacert-devel

### fetching dependencies

```
apt-get install whois gnupg openssl openssl-blacklist
apt-get install apache2 libapache2-mod-php5
apt-get install mysql-server-5.5 php5-mysql
```

mysql root pw "*secret*"

$ php --version
PHP 5.4.4-14+deb7u14 (cli) (built: Aug 21 2014 08:36:44)
Copyright (c) 1997-2012 The PHP Group
Zend Engine v2.4.0, Copyright (c) 1998-2012 Zend Technologies

### configuring
" Setup apache vhost "

a2enmod ssl

In Apache vhost: "AllowOverride All"
replace path in /home/cacert/cacert-devel/www/.htaccess to match your setup


####/etc/php5/conf.d/20-cacert.ini
```
cat > /etc/php5/conf.d/20-cacert.ini << EOF
;
; Additional settings for CAcert webdb application
;
safe_mode_allowed_env_vars = LC_ALL,LANG,LANGUAGE,PHP_
disable_functions = passthru
expose_php = Off
memory_limit = 18M
display_errors = Off
log_errors = On
error_log = /var/log/apache2/phperrors.log
sendmail_path = "/usr/sbin/sendmail -t -i -freturns@cacert.org"
session.use_only_cookies = On
session.cookie_secure = On
error_reporting = E_ALL
EOF
```

### database setup

wget https://www.cacert.org/sqldump.php -O /home/cacert/dbdump.sql

mysql -u root "-p*secret*"

```
CREATE USER cacert IDENTIFIED BY '*secret*';
CREATE DATABASE cacert CHARACTER SET latin1;
GRANT ALL ON cacert.* TO cacert;
```

mysql -u root "-p*secret*" cacert < /home/cacert/dbdump.sql

cp /home/cacert/cacert-devel/includes/mysql.php.sample /home/cacert/cacert-devel/includes/mysql.php
replace "username", "password", "database"

replace path in line 43 of /home/cacert/cacert-devel/includes/general.php
$_SESSION['_config']['filepath'] = "/home/cacert/cacert-devel";  

### install fpdf

apt-get install php-fpdf php5-recode

replace path in Line 21 of /home/cacert/cacert-devel/www/cap.php
require_once('/usr/share/fpdf/fpdf.php');

### downloading locales
cd locale
make

### system locales.... 
"LibreSSL" needs various system locales activated and generated
Which charset is needed is not clear. Propably it's not UTF-8 but something else.
Make sure they are activated in /etc/locale.gen and regenerate your locales "locale-gen" (and restart apache).
ar_JO, bg_BG, cs_CZ, da_DK, de_DE, el_GR, en_US, es_ES, fa_IR, fi_FI, fr_FR, he_IL, hr_HR, hu_HU, id_ID, is_IS, it_IT, ja_JP, ka_GE, 
ko_KR, lv_LV, nb_NO, nl_NL, pl_PL, pt_PT, pt_BR, ro_RO, ru_RU, sl_SI, sv_SE, th_TH, tr_TR, uk_UA, zh_CN, zh_TW
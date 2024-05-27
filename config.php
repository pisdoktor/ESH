<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'DDH123**?');
define('DB', 'esh');
define('DB_PREFIX', 'esh_');


define('_ISO', 'charset=UTF-8');
define('ABSPATH', dirname(__FILE__));
define('SITEURL', 'http://localhost/esh102');

define('SITEHEAD', 'Evde Sağlık Hizmetleri Sistemi');
define('META_DESC', 'Evde Sağlık Hizmetleri Sistemi');
define('META_KEYS', '');

define('ADMINTEMPLATE', 'standart');
define('SITETEMPLATE', 'standart');

define('OFFSET', 0);
define('DEBUGMODE', 0);
define('SECRETWORD', 'esh');
define('ERROR_REPORT', 1);

define('SESSION_TYPE', 2);
define('USER_ACTIVATION', 0);

define('MAILER', 'sendmail'); //sendmail or smtp
define('SENDMAIL', '/usr/sbin/sendmail');
define('MAILFROM', 'ESH Sistemi');
define('MAILFROMNAME', 'admin@eshsistem.com');
define('smtpauth', '');
define('smtpuser', 'no-reply@eshsistem.com');
define('smtppass', 'son20er35');
define('smtphost', 'smtpout.secureserver.net');

define('GZIPCOMP', 0);
define('STATS', 1);
define('COUNTSTATS', 1);
define('FILEPERMS', '');
define('DIRPERMS', '');

define('SEF', 0); //henüz hazır değil
define('compactTopicPagesEnable', 1);
define('compactTopicPagesContiguous', 20);
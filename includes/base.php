<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

ini_set('magic_quotes_runtime', 0);

setlocale(LC_ALL, 'tr_TR.UTF-8');

if ( ERROR_REPORT === 0 || ERROR_REPORT === '0' ) {
	error_reporting( 0 );
} else if (ERROR_REPORT > 0) {
	error_reporting( E_ALL );
}

require_once( dirname( __FILE__ ) . '/version.php' );
require_once( dirname( __FILE__ ) . '/database.php' );
require_once( dirname( __FILE__ ) . '/phpmailer/class.phpmailer.php' );

//Veritabanı tablolarını içeri alalım
$tables = readDirectory(dirname(__FILE__).'/tables/');
foreach ($tables as $table) {
	require_once(dirname(__FILE__).'/tables/'.$table);
}

//sınıfları içeri alalım
$classes = readDirectory(dirname(__FILE__).'/classes/');
foreach ($classes as $class) {
	require_once(dirname(__FILE__).'/classes/'.$class);
}

$dbase = new DB( DB_HOST, DB_USER, DB_PASS, DB, DB_PREFIX );
/**
* @desc SET sql mode utf-8
*/
$dbase->setQuery("SET NAMES utf8");
$dbase->query();

if ($dbase->getErrorNum()) {
	$systemError = $dbase->getErrorNum();
	$systemErrorMsg = $dbase->getErrorMsg();
	include ABSPATH . '/closed.php';
	exit();
}
$dbase->debug( DEBUGMODE );

// platform neurtral url handling
if ( isset( $_SERVER['REQUEST_URI'] ) ) {
	$request_uri = $_SERVER['REQUEST_URI'];
} else {
	$request_uri = $_SERVER['SCRIPT_NAME'];
	// Append the query string if it exists and isn't null
	if ( isset( $_SERVER['QUERY_STRING'] ) && !empty( $_SERVER['QUERY_STRING'] ) ) {
		$request_uri .= '?' . $_SERVER['QUERY_STRING'];
	}
}
$_SERVER['REQUEST_URI'] = $request_uri;

// current server time
$now = date( 'Y-m-d H:i', time() );

DEFINE( '_CURRENT_SERVER_TIME', $now );
DEFINE( '_CURRENT_SERVER_TIME_FORMAT', '%Y-%m-%d %H:%M:%S' );

// Non http/https URL Schemes
$url_schemes = 'data:, file:, ftp:, gopher:, imap:, ldap:, mailto:, news:, nntp:, telnet:, javascript:, irc:, mms:';
DEFINE( '_URL_SCHEMES', $url_schemes );

// disable strict mode in MySQL 5 
/**
if (!defined( '_SET_SQLMODE' )) {
	
	define( '_SET_SQLMODE', 1 );

	// if running mysql 5, set sql-mode to mysql40 - thereby circumventing strict mode problems
	if ( strpos( $dbase->getVersion(), '5' ) === 0 ) {
		$query = "SET sql_mode = 'MYSQL40'";
		$dbase->setQuery( $query );
		$dbase->query();
	}
}  
**/
/**
 * Utility function to return a value from a named array or a specified default
 * @param array A named array
 * @param string The key to search for
 * @param mixed The default value to give if no key found
 * @param int An options mask: _NOTRIM prevents trim, _ALLOWHTML allows safe html, _ALLOWRAW allows raw input
 */
define( "_NOTRIM", 0x0001 );
define( "_ALLOWHTML", 0x0002 );
define( "_ALLOWRAW", 0x0004 );
function getParam( &$arr, $name, $def=null, $mask=0 ) {
	static $noHtmlFilter 	= null;
	static $safeHtmlFilter 	= null;

	$return = null;
	if (isset( $arr[$name] )) {
		$return = $arr[$name];

		if (is_string( $return )) {
			// trim data
			if (!($mask&_NOTRIM)) {
				$return = trim( $return );
			}

			if ($mask&_ALLOWRAW) {
				// do nothing
			} else if ($mask&_ALLOWHTML) {
				// do nothing - compatibility mode
			} else {
				// send to inputfilter
				if (is_null( $noHtmlFilter )) {
					$noHtmlFilter = new InputFilter( /* $tags, $attr, $tag_method, $attr_method, $xss_auto */ );
				}
				$return = $noHtmlFilter->process( $return );

				if (!empty($return) && is_numeric($def)) {
				// if value is defined and default value is numeric set variable type to integer
					$return = intval($return);
				}
			}

			// account for magic quotes setting
			if (!get_magic_quotes_gpc()) {
				$return = addslashes( $return );
			}
		}

		return $return;
	} else {
		return $def;
	}
}

/**
 * Strip slashes from strings or arrays of strings
 * @param mixed The input string or array
 * @return mixed String or array stripped of slashes
 */
function mosStripslashes( &$value ) {
	$ret = '';
	if (is_string( $value )) {
		$ret = stripslashes( $value );
	} else {
		if (is_array( $value )) {
			$ret = array();
			foreach ($value as $key => $val) {
				$ret[$key] = mosStripslashes( $val );
			}
		} else {
			$ret = $value;
		}
	}
	return $ret;
}

/**
* Copy the named array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
*/
function BindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
	if (!is_array( $array ) || !is_object( $obj )) {
		return (false);
	}

	$ignore = ' ' . $ignore . ' ';
	foreach (get_object_vars($obj) as $k => $v) {
		if( substr( $k, 0, 1 ) != '_' ) {			// internal attributes of an object are ignored
			if (strpos( $ignore, ' ' . $k . ' ') === false) {
				if ($prefix) {
					$ak = $prefix . $k;
				} else {
					$ak = $k;
				}
				if (isset($array[$ak])) {
					$obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? mosStripslashes( $array[$ak] ) : $array[$ak];
				}
			}
		}
	}

	return true;
}

/**
* Utility function to read the files in a directory
* @param string The file system path
* @param string A filter for the names
* @param boolean Recurse search into sub-directories
* @param boolean True if to prepend the full path to the file name
*/
function readDirectory( $path, $filter='.', $recurse=false, $fullpath=false  ) {
	$arr = array();
	if (!@is_dir( $path )) {
		return $arr;
	}
	$handle = opendir( $path );

	while ($file = readdir($handle)) {
		$dir = PathName( $path.'/'.$file, false );
		$isDir = is_dir( $dir );
		if (($file != ".") && ($file != "..")) {
			if (preg_match( "/$filter/", $file )) {
				if ($fullpath) {
					$arr[] = trim( PathName( $path.'/'.$file, false ) );
				} else {
					$arr[] = trim( $file );
				}
			}
			if ($recurse && $isDir) {
				$arr2 = readDirectory( $dir, $filter, $recurse, $fullpath );
				$arr = array_merge( $arr, $arr2 );
			}
		}
	}
	closedir($handle);
	asort($arr);
	return $arr;
}

/**
* Utility function redirect the browser location to another url
*
* Can optionally provide a message.
* @param string The file system path
* @param string A filter for the names
*/
function Redirect( $url, $msg='' ) {

   global $mainframe;

	// specific filters
	$iFilter = new InputFilter();
	$url = $iFilter->process( $url );
	if (!empty($msg)) {
		$msg = $iFilter->process( $msg );
	}

	// Strip out any line breaks and throw away the rest
	$url = preg_split("/[\r\n]/", $url);
	$url = $url[0];

	if ($iFilter->badAttributeValue( array( 'href', $url ))) {
		$url = SITEURL;
	}

	if (trim( $msg )) {
		if (strpos( $url, '?' )) {
			$url .= '&mosmsg=' . urlencode( $msg );
		} else {
			$url .= '?mosmsg=' . urlencode( $msg );
		}
	}
	

	if (headers_sent()) {
		echo "<script>document.location.href='$url';</script>\n";
	} else {
		@ob_end_clean(); // clear output buffer
		header( 'HTTP/1.1 301 Moved Permanently' );
		header( "Location: ". $url );
	}
	exit();
}

function treeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 ) {

	if (@$children[$id] && $level <= $maxlevel) {
		foreach ($children[$id] as $v) {
			$id = $v->id;

			if ( $type ) {
				$pre 	= '<sup>L</sup>&nbsp;';
				$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			} else {
				$pre 	= '- ';
				$spacer = '&nbsp;&nbsp;';
			}

			if ( $v->parent == 0 ) {
				$txt 	= $v->name;
			} else {
				$txt 	= $pre . $v->name;
			}
			$pt = $v->parent;
			$list[$id] = $v;
			$list[$id]->treename = "$indent$txt";
			$list[$id]->children = count( @$children[$id] );

			$list = treeRecurse( $id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type );
		}
	}
	return $list;
}

/**
* Function to strip additional / or \ in a path name
* @param string The path
* @param boolean Add trailing slash
*/
function PathName($p_path,$p_addtrailingslash = true) {
	$retval = "";

	$isWin = (substr(PHP_OS, 0, 3) == 'WIN');

	if ($isWin)	{
		$retval = str_replace( '/', '\\', $p_path );
		if ($p_addtrailingslash) {
			if (substr( $retval, -1 ) != '\\') {
				$retval .= '\\';
			}
		}

		// Check if UNC path
		$unc = substr($retval,0,2) == '\\\\' ? 1 : 0;

		// Remove double \\
		$retval = str_replace( '\\\\', '\\', $retval );

		// If UNC path, we have to add one \ in front or everything breaks!
		if ( $unc == 1 ) {
			$retval = '\\'.$retval;
		}
	} else {
		$retval = str_replace( '\\', '/', $p_path );
		if ($p_addtrailingslash) {
			if (substr( $retval, -1 ) != '/') {
				$retval .= '/';
			}
		}

		// Check if UNC path
		$unc = substr($retval,0,2) == '//' ? 1 : 0;

		// Remove double //
		$retval = str_replace('//','/',$retval);

		// If UNC path, we have to add one / in front or everything breaks!
		if ( $unc == 1 ) {
			$retval = '/'.$retval;
		}
	}

	return $retval;
}

function ObjectToArray($p_obj) {
	$retarray = null;
	if(is_object($p_obj))
	{
		$retarray = array();
		foreach (get_object_vars($p_obj) as $k => $v)
		{
			if(is_object($v))
			$retarray[$k] = ObjectToArray($v);
			else
			$retarray[$k] = $v;
		}
	}
	return $retarray;
}

function ErrorAlert( $text, $action='window.history.go(-1);', $mode=1 ) {
	$text = nl2br( $text );
	$text = addslashes( $text );
	$text = strip_tags( $text );

	switch ( $mode ) {
		case 2:
			echo "<script>$action</script> \n";
			break;

		case 1:
		default:
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; "._ISO."\" />";
			echo "<script>alert('$text'); $action</script> \n";
			//echo '<noscript>';
			//mosRedirect( @$_SERVER['HTTP_REFERER'], $text );
			//echo '</noscript>';
			break;
	}

	exit;
}

/**
* Makes a variable safe to display in forms
*
* Object parameters that are non-string, array, object or start with underscore
* will be converted
* @param object An object to be parsed
* @param int The optional quote style for the htmlspecialchars function
* @param string|array An optional single field name or array of field names not
*					 to be parsed (eg, for a textarea)
*/
function MakeHtmlSafe( &$mixed, $quote_style=ENT_QUOTES, $exclude_keys='' ) {
	if (is_object( $mixed )) {
		foreach (get_object_vars( $mixed ) as $k => $v) {
			if (is_array( $v ) || is_object( $v ) || $v == NULL || substr( $k, 1, 1 ) == '_' ) {
				continue;
			}
			if (is_string( $exclude_keys ) && $k == $exclude_keys) {
				continue;
			} else if (is_array( $exclude_keys ) && in_array( $k, $exclude_keys )) {
				continue;
			}
			$mixed->$k = htmlspecialchars( $v, $quote_style );
		}
	}
}
/**
* Returns formated date according to current local and adds time offset
* @param string date in datetime format
* @param string format optional format for strftime
* @param offset time offset if different than global one
* @returns formated date
*/
function FormatDate( $date, $format="", $offset=NULL ){
	
	if ( $format == '' ) {
		// %Y-%m-%d %H:%M:%S
		$format = '%d-%m-%Y %H:%M:%S';
	}
	if ( is_null($offset) ) {
		$offset = OFFSET;
	}
	if ( $date && preg_match( "/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs ) ) {
		$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		$date = $date > -1 ? strftime( $format, $date + ($offset*60*60) ) : '-';
	}
	return $date;
}

/**
* Returns current date according to current local and time offset
* @param string format optional format for strftime
* @returns current date
*/
function CurrentDate( $format="" ) {
	if ($format=="") {
		$format = '%d-%m-%Y %H:%M:%S';
	}
	$date = strftime( $format, time() + (OFFSET*3600) );
	return $date;
}

function CreateGUID(){
	srand((double)microtime()*1000000);
	$r = rand();
	$u = uniqid(getmypid() . $r . (double)microtime()*1000000,1);
	$m = md5 ($u);
	return($m);
}

function mosCompressID( $ID ){
	return(Base64_encode(pack("H*",$ID)));
}

function mosExpandID( $ID ) {
	return ( implode(unpack("H*",Base64_decode($ID)), '') );
}

/**
* Function to create a mail object for futher use (uses phpMailer)
* @param string From e-mail address
* @param string From name
* @param string E-mail subject
* @param string Message body
* @return object Mail object
*/
function CreateMail( $from='', $fromname='', $subject, $body ) {
	
	$mail = new PHPMailer();

	$mail->PluginDir = ABSPATH .'/includes/phpmailer/';
	$mail->SetLanguage( 'tr', ABSPATH . '/includes/phpmailer/language/' );
	$mail->CharSet 	= substr_replace(_ISO, '', 0, 8);
	$mail->isSendmail();
	$mail->From 	= $from ? $from : MAILFROM;
	$mail->FromName = $fromname ? $fromname : MAILFROMNAME;

	// Add smtp values if needed
	if ( MAILER == 'smtp' ) {
		$mail->SMTPAuth = smtpauth;
		$mail->Username = smtpuser;
		$mail->Password = smtppass;
		$mail->Host 	= smtphost;
	} else

	// Set sendmail path
	if ( MAILER == 'sendmail' ) {
		if (SENDMAIL)
			$mail->Sendmail = SENDMAIL;
	} // if

	$mail->Subject 	= $subject;
	$mail->Body 	= $body;

	return $mail;
}

/**
* Mail function (uses phpMailer)
* @param string From e-mail address
* @param string From name
* @param string/array Recipient e-mail address(es)
* @param string E-mail subject
* @param string Message body
* @param boolean false = plain text, true = HTML
* @param string/array CC e-mail address(es)
* @param string/array BCC e-mail address(es)
* @param string/array Attachment file name(s)
* @param string/array ReplyTo e-mail address(es)
* @param string/array ReplyTo name(s)
* @return boolean
*/
function mosMail( $from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
	global $debug;

	// Allow empty $from and $fromname settings (backwards compatibility)
	if ($from == '') {
		$from = MAILFROM;
	}
	if ($fromname == '') {
		$fromname = MAILFROMNAME;
	}

	// Filter from, fromname and subject
	if (!IsValidEmail( $from ) || !IsValidName( $fromname ) || !IsValidName( $subject )) {
		return false;
	}

	$mail = CreateMail( $from, $fromname, $subject, $body );

	// activate HTML formatted emails
	if ( $mode ) {
		$mail->IsHTML(true);
	}

	if (is_array( $recipient )) {
		foreach ($recipient as $to) {
			if (!IsValidEmail( $to )) {
				return false;
			}
			$mail->AddAddress( $to );
		}
	} else {
		if (!IsValidEmail( $recipient )) {
			return false;
		}
		$mail->AddAddress( $recipient );
	}
	if (isset( $cc )) {
		if (is_array( $cc )) {
			foreach ($cc as $to) {
				if (!IsValidEmail( $to )) {
					return false;
				}
				$mail->AddCC($to);
			}
		} else {
			if (!IsValidEmail( $cc )) {
				return false;
			}
			$mail->AddCC($cc);
		}
	}
	if (isset( $bcc )) {
		if (is_array( $bcc )) {
			foreach ($bcc as $to) {
				if (!IsValidEmail( $to )) {
					return false;
				}
				$mail->AddBCC( $to );
			}
		} else {
			if (!IsValidEmail( $bcc )) {
				return false;
			}
			$mail->AddBCC( $bcc );
		}
	}
	if ($attachment) {
		if (is_array( $attachment )) {
			foreach ($attachment as $fname) {
				$mail->AddAttachment( $fname );
			}
		} else {
			$mail->AddAttachment($attachment);
		}
	}
	//Important for being able to use mosMail without spoofing...
	if ($replyto) {
		if (is_array( $replyto )) {
			reset( $replytoname );
			foreach ($replyto as $to) {
				$toname = ((list( $key, $value ) = each( $replytoname )) ? $value : '');
				if (!IsValidEmail( $to ) || !IsValidName( $toname )) {
					return false;
				}
				$mail->AddReplyTo( $to, $toname );
			}
		} else {
			if (!IsValidEmail( $replyto ) || !IsValidName( $replytoname )) {
				return false;
			}
			$mail->AddReplyTo($replyto, $replytoname);
		}
	}

	$mailssend = $mail->Send();

	if( DEBUGMODE ) {
		//$mosDebug->message( "Mails send: $mailssend");
	}
	if( $mail->error_count > 0 ) {
		//$mosDebug->message( "The mail message $fromname <$from> about $subject to $recipient <b>failed</b><br /><pre>$body</pre>", false );
		//$mosDebug->message( "Mailer Error: " . $mail->ErrorInfo . "" );
	}
	return $mailssend;
} // mosMail

/**
 * Checks if a given string is a valid email address
 *
 * @param	string	$email	String to check for a valid email address
 * @return	boolean
 */
function IsValidEmail( $email ) {
	$valid = preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email );

	return $valid;
}

/**
 * Checks if a given string is a valid (from-)name or subject for an email
 *
 * @since		1.0.11
 * @deprecated	1.5
 * @param		string		$string		String to check for validity
 * @return		boolean
 */
function IsValidName( $string ) {
	/*
	 * The following regular expression blocks all strings containing any low control characters:
	 * 0x00-0x1F, 0x7F
	 * These should be control characters in almost all used charsets.
	 * The high control chars in ISO-8859-n (0x80-0x9F) are unused (e.g. http://en.wikipedia.org/wiki/ISO_8859-1)
	 * Since they are valid UTF-8 bytes (e.g. used as the second byte of a two byte char),
	 * they must not be filtered.
	 */
	$invalid = preg_match( '/[\x00-\x1F\x7F]/', $string );
	if ($invalid) {
		return false;
	} else {
		return true;
	}
}

/**
 * Initialise GZIP
 */
function initGzip() {
	global $do_gzip_compress;

	$do_gzip_compress = FALSE;
	if (GZIPCOMP == 1) {
		$phpver 	= phpversion();
		$useragent 	= getParam( $_SERVER, 'HTTP_USER_AGENT', '' );
		$canZip 	= getParam( $_SERVER, 'HTTP_ACCEPT_ENCODING', '' );

		$gzip_check 	= 0;
		$zlib_check 	= 0;
		$gz_check		= 0;
		$zlibO_check	= 0;
		$sid_check		= 0;
		if ( strpos( $canZip, 'gzip' ) !== false) {
			$gzip_check = 1;
		}
		if ( extension_loaded( 'zlib' ) ) {
			$zlib_check = 1;
		}
		if ( function_exists('ob_gzhandler') ) {
			$gz_check = 1;
		}
		if ( ini_get('zlib.output_compression') ) {
			$zlibO_check = 1;
		}
		if ( ini_get('session.use_trans_sid') ) {
			$sid_check = 1;
		}

		if ( $phpver >= '4.0.4pl1' && ( strpos($useragent,'compatible') !== false || strpos($useragent,'Gecko')	!== false ) ) {
			// Check for gzip header or northon internet securities or session.use_trans_sid
			if ( ( $gzip_check || isset( $_SERVER['---------------']) ) && $zlib_check && $gz_check && !$zlibO_check && !$sid_check ) {
				// You cannot specify additional output handlers if
				// zlib.output_compression is activated here
				ob_start( 'ob_gzhandler' );
				return;
			}
		} else if ( $phpver > '4.0' ) {
			if ( $gzip_check ) {
				if ( $zlib_check ) {
					$do_gzip_compress = TRUE;
					ob_start();
					ob_implicit_flush(0);

					header( 'Content-Encoding: gzip' );
					return;
				}
			}
		}
	}
	ob_start();
}

/**
* Perform GZIP
*/
function doGzip() {
	global $do_gzip_compress;
	if ( $do_gzip_compress ) {
		/**
		*Borrowed from php.net!
		*/
		$gzip_contents = ob_get_contents();
		ob_end_clean();

		$gzip_size = strlen($gzip_contents);
		$gzip_crc = crc32($gzip_contents);

		$gzip_contents = gzcompress($gzip_contents, 9);
		$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		echo $gzip_contents;
		echo pack('V', $gzip_crc);
		echo pack('V', $gzip_size);
	} else {
		ob_end_flush();
	}
}

/**
* Random password generator
* @return password
*/
function MakePassword($length=8) {
	$salt 		= "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
	$makepass	= '';
	mt_srand(10000000*(double)microtime());
	for ($i = 0; $i < $length; $i++)
		$makepass .= $salt[mt_rand(0,58)];
	return $makepass;
}

if (!function_exists('html_entity_decode')) {
	/**
	* html_entity_decode function for backward compatability in PHP
	* @param string
	* @param string
	*/
	function html_entity_decode ($string, $opt = ENT_COMPAT) {

		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);

		if ($opt & 1) { // Translating single quotes
			// Add single quote to translation table;
			// doesn't appear to be there by default
			$trans_tbl["&apos;"] = "'";
		}

		if (!($opt & 2)) { // Not translating double quotes
			// Remove double quote from translation table
			unset($trans_tbl["&quot;"]);
		}

		return strtr ($string, $trans_tbl);
	}
}

/**
* Sorts an Array of objects
*/
function SortArrayObjects_cmp( &$a, &$b ) {
	global $csort_cmp;

	if ( $a->$csort_cmp['key'] > $b->$csort_cmp['key'] ) {
		return $csort_cmp['direction'];
	}

	if ( $a->$csort_cmp['key'] < $b->$csort_cmp['key'] ) {
		return -1 * $csort_cmp['direction'];
	}

	return 0;
}

/**
* Sorts an Array of objects
* sort_direction [1 = Ascending] [-1 = Descending]
*/
function SortArrayObjects( &$a, $k, $sort_direction=1 ) {
	global $csort_cmp;

	$csort_cmp = array(
		'key'		  => $k,
		'direction'	=> $sort_direction
	);

	usort( $a, 'SortArrayObjects_cmp' );

	unset( $csort_cmp );
}

/**
* Displays a not authorised message
*
* If the user is not logged in then an addition message is displayed.
*/
function NotAuth() {
	global $my;

	echo 'Yetkiniz yok!';
	if ($my->id < 1) {
		echo "<br />" . 'Önce giriş yapın!';
	}
}

/**
* Replaces &amp; with & for xhtml compliance
*
* Needed to handle unicode conflicts due to unicode conflicts
*/
function ampReplace( $text ) {
	$text = str_replace( '&&', '*--*', $text );
	$text = str_replace( '&#', '*-*', $text );
	$text = str_replace( '&amp;', '&', $text );
	$text = preg_replace( '|&(?![\w]+;)|', '&amp;', $text );
	$text = str_replace( '*-*', '&#', $text );
	$text = str_replace( '*--*', '&&', $text );

	return $text;
}
/**
* Chmods files and directories recursively to given permissions. Available from 1.0.0 up.
* @param path The starting file or directory (no trailing slash)
* @param filemode Integer value to chmod files. NULL = dont chmod files.
* @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
* @return TRUE=all succeeded FALSE=one or more chmods failed
*/
function ChmodRecursive($path, $filemode=NULL, $dirmode=NULL) {
	$ret = TRUE;
	if (is_dir($path)) {
		$dh = opendir($path);
		while ($file = readdir($dh)) {
			if ($file != '.' && $file != '..') {
				$fullpath = $path.'/'.$file;
				if (is_dir($fullpath)) {
					if (!ChmodRecursive($fullpath, $filemode, $dirmode))
						$ret = FALSE;
				} else {
					if (isset($filemode))
						if (!@chmod($fullpath, $filemode))
							$ret = FALSE;
				} // if
			} // if
		} // while
		closedir($dh);
		if (isset($dirmode))
			if (!@chmod($path, $dirmode))
				$ret = FALSE;
	} else {
		if (isset($filemode))
			$ret = @chmod($path, $filemode);
	} // if
	return $ret;
} // ChmodRecursive

/**
* Chmods files and directories recursively to mos global permissions. Available from 1.0.0 up.
* @param path The starting file or directory (no trailing slash)
* @param filemode Integer value to chmod files. NULL = dont chmod files.
* @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
* @return TRUE=all succeeded FALSE=one or more chmods failed
*/
function mosChmod($path) {
	$filemode = NULL;
	if (FILEPERMS != '')
		$filemode = octdec(FILEPERMS);
	$dirmode = NULL;
	if (DIRPERMS != '')
		$dirmode = octdec(DIRPERMS);
	if (isset($filemode) || isset($dirmode))
		return ChmodRecursive($path, $filemode, $dirmode);
	return TRUE;
} // mosChmod

/**
 * Function to convert array to integer values
 * @param array
 * @param int A default value to assign if $array is not an array
 * @return array
 */
function ArrayToInts( &$array, $default=null ) {
	if (is_array( $array )) {
		foreach( $array as $key => $value ) {
			$array[$key] = (int) $value;
		}
	} else {
		if (is_null( $default )) {
			$array = array();
			return array(); // Kept for backwards compatibility
		} else {
			$array = array( (int) $default );
			return array( $default ); // Kept for backwards compatibility
		}
	}
}

/**
* Arraydan stringe: soner ekledi
* 
* @param mixed $array
* @param mixed $default
*/
function ArrayToStrings( &$array, $default=null ) {
	if (is_array( $array )) {
		foreach( $array as $key => $value ) {
			$array[$key] = (string) $value;
		}
	} else {
		if (is_null( $default )) {
			$array = array();
			return array(); // Kept for backwards compatibility
		} else {
			$array = array( (string) $default );
			return array( $default ); // Kept for backwards compatibility
		}
	}
}

/*
* Function to handle an array of integers
* Added 1.0.11
*/
function GetArrayInts( $name, $type=NULL ) {
	if ( $type == NULL ) {
		$type = $_POST;
	}

	$array = getParam( $type, $name, array(0) );

	ArrayToInts( $array );

	if (!is_array( $array )) {
		$array = array(0);
	}

	return $array;
}

/**
 * Provides a secure hash based on a seed
 * @param string Seed string
 * @return string
 */
function mosHash( $seed ) {
	return md5( SECRETWORD . md5( $seed ) );
}

/**
 * Format a backtrace error
 * @since 1.0.5
 */
function mosBackTrace() {
	if (function_exists( 'debug_backtrace' )) {
		echo '<div align="left">';
		foreach( debug_backtrace() as $back) {
			if (@$back['file']) {
				echo '<br />' . str_replace( ABSPATH, '', $back['file'] ) . ':' . $back['line'];
			}
		}
		echo '</div>';
	}
}

function spoofCheck( $header=NULL, $alt=NULL , $method = 'post') {
	switch(strtolower($method)) {
		case "get":
			$validate 	= getParam( $_GET, spoofValue($alt), 0 );
			break;
		case "request":
			$validate 	= getParam( $_REQUEST, spoofValue($alt), 0 );
			break;
		case "post":
		default:
			$validate 	= getParam( $_POST, spoofValue($alt), 0 );
			break;
	}

	// probably a spoofing attack
	if (!$validate) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( 'Yetkiniz yok' );
		return;
	}

	// First, make sure the form was posted from a browser.
	// For basic web-forms, we don't care about anything
	// other than requests from a browser:
	if (!isset( $_SERVER['HTTP_USER_AGENT'] )) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( 'Yetkiniz yok' );
		return;
	}

	// Make sure the form was indeed POST'ed:
	//  (requires your html form to use: action="post")
	if (!$_SERVER['REQUEST_METHOD'] == 'POST' ) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( 'Yetkiniz yok' );
		return;
	}

	if ($header) {
	// Attempt to defend against header injections:
		$badStrings = array(
			'Content-Type:',
			'MIME-Version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:'
		);

		// Loop through each POST'ed value and test if it contains
		// one of the $badStrings:
		_spoofCheck( $_POST, $badStrings );
	}
}

function _spoofCheck( $array, $badStrings ) {
	// Loop through each $array value and test if it contains
	// one of the $badStrings
	foreach( $array as $v ) {
		if (is_array( $v )) {
			_spoofCheck( $v, $badStrings );
		} else {
			foreach ( $badStrings as $v2 ) {
				if ( stripos( $v, $v2 ) !== false ) {
					header( 'HTTP/1.0 403 Forbidden' );
					mosErrorAlert( 'Yetkiniz yok' );
					exit(); // mosErrorAlert dies anyway, double check just to make sure
				}
			}
		}
	}
}

/**
 * Method to determine a hash for anti-spoofing variable names
 *
 * @return	string	Hashed var name
 * @static
 */
function spoofValue($alt=NULL) {
	global $mainframe, $my, $db;

	if ($alt) {
		if ( $alt == 1 ) {
			$random		= date( 'Ymd' );
		} else {
			$random		= $alt . date( 'Ymd' );
		}
	} else {
		$random		= date( 'dmY' );
	}
	// the prefix ensures that the hash is non-numeric
	// otherwise it will be intercepted by globals.php
	$validate 	= 'j' . mosHash( $db . $random . $my->id );

	return $validate;
}

/**
 * A simple helper function to salt and hash a clear-text password.
 *
 * @since	1.0.13
 * @param	string	$password	A plain-text password
 * @return	string	An md5 hashed password with salt
 */
function josHashPassword($password) {
	// Salt and hash the password
	$salt	= MakePassword(16);
	$crypt	= md5($password.$salt);
	$hash	= $crypt.':'.$salt;

	return $hash;
}

/**
* @desc Forum sisteminden aldığım fonksiyonlar
* sayfalandırma ve tarih çevirme içeriyor
*/
function timeformat($logTime, $show_today = true, $datetime=false) {
        
    $txt['days'] = array('Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi');
    $txt['days_short'] = array('Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt');
    // Months must start with 1 => 'January'. (or translated, of course.)
    $txt['months'] = array(1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');
    $txt['months_short'] = array(1 => 'Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara');

    if ($datetime == true) {
        $logTime = strtotime($logTime);
    }
    // Offset the time.
    //$time = $logTime + (OFFSET * 3600);

    // We can't have a negative date (on Windows, at least.)
    if ($time < 0)
        $time = 0;

    // Today and Yesterday?
    if (todayMod >= 1 && $show_today === true) {
        // Get the current time.
        $nowtime = forumtime();

        $then = @getdate($time);
        $now = @getdate($nowtime);

        /*
        // Try to make something of a time format string...
        $s = strpos(timeformat, '%S') === false ? '' : ':%S';
        
        if (strpos(timeformat, '%H') === false && strpos(timeformat, '%T') === false) {
            $today_fmt = '%I:%M' . $s . ' %p';
        } else {
            $today_fmt = '%H:%M' . $s;
        }
        */

        // Same day of the year, same year.... Today!
        if ($then['yday'] == $now['yday'] && $then['year'] == $now['year']) {
            return '<b>Bugün</b> ' . timeformat($logTime, $today_fmt);
        }

        // Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
        if (todayMod == '2' && (($then['yday'] == $now['yday'] - 1 && $then['year'] == $now['year']) || ($now['yday'] == 0 && $then['year'] == $now['year'] - 1) && $then['mon'] == 12 && $then['mday'] == 31)) {
            return '<b>Dün</b> ' . timeformat($logTime, $today_fmt);
        }
    }

    $str = !is_bool($show_today) ? $show_today : timeformat;

    if (setlocale(LC_ALL, 'tr_TR.UTF-8')) {
        foreach (array('%a', '%A', '%b', '%B') as $token)
            if (strpos($str, $token) !== false)
                $str = str_replace($token, ucwords(strftime($token, $time)), $str);
    } else {
        // Do-it-yourself time localization.  Fun.
        foreach (array('%a' => 'days_short', '%A' => 'days', '%b' => 'months_short', '%B' => 'months') as $token => $text_label)
            if (strpos($str, $token) !== false)
                $str = str_replace($token, $txt[$text_label][(int) strftime($token === '%a' || $token === '%A' ? '%w' : '%m', $time)], $str);
        if (strpos($str, '%p'))
            $str = str_replace('%p', (strftime('%H', $time) < 12 ? 'am' : 'pm'), $str);
    }

    // Format any other characters..
    return strftime($str, $time);
}

function forumtime($timestamp = null) {
    
    if ($timestamp === null) {
        $timestamp = time();
    } elseif ($timestamp == 0) {
        return 0;
    }

    return $timestamp + (OFFSET * 3600);
}

function constructPageIndex($base_url, $total, $limitstart, $limit=10, $flexible_start = false) {
    // Save whether $limitstart was less than 0 or not.
    $limitstart_invalid = $limitstart < 0;

    // Make sure $limitstart is a proper variable - not less than 0.
    if ($limitstart_invalid)
        $limitstart = 0;
    // Not greater than the upper bound.
    elseif ($limitstart >= $total)
        $limitstart = max(0, (int) $total - (((int) $total % (int) $limit) == 0 ? $limit : ((int) $total % (int) $limit)));
    // And it has to be a multiple of $limit!
    else
        $limitstart = max(0, (int) $limitstart - ((int) $limitstart % (int) $limit));

    $base_link = '<a class="navPages" href="'.($flexible_start ? $base_url : strtr($base_url, array('%' => '%%')) . '&limitstart=%d&limit='.$limit).'">%s</a> ';

    // Compact pages is off or on?
    if (!compactTopicPagesEnable) {
        // Show the left arrow.
        $pageindex = $limitstart == 0 ? ' ' : sprintf($base_link, $limitstart - $limit, '&#171;');

        // Show all the pages.
        $display_page = 1;
        for ($counter = 0; $counter < $total; $counter += $limit)
            $pageindex .= $limitstart == $counter && !$limitstart_invalid ? '<b>' . $display_page++ . '</b> ' : sprintf($base_link, $counter, $display_page++);

        // Show the right arrow.
        $display_page = ($limitstart + $limit) > $total ? $total : ($limitstart + $limit);
        if ($limitstart != $counter - $total && !$limitstart_invalid)
            $pageindex .= $display_page > $counter - $limit ? ' ' : sprintf($base_link, $display_page, '&#187;');
    } else {
        // If they didn't enter an odd value, pretend they did.
        $PageContiguous = (int) (compactTopicPagesContiguous - (compactTopicPagesContiguous % 2)) / 2;

        // Show the first page. (>1< ... 6 7 [8] 9 10 ... 15)
        if ($limitstart > $limit * $PageContiguous)
            $pageindex = sprintf($base_link, 0, '1');
        else
            $pageindex = '';

        // Show the ... after the first page.  (1 >...< 6 7 [8] 9 10 ... 15)
        if ($limitstart > $limit * ($PageContiguous + 1))
            $pageindex .= '<b> ... </b>';

        // Show the pages before the current one. (1 ... >6 7< [8] 9 10 ... 15)
        for ($nCont = $PageContiguous; $nCont >= 1; $nCont--)
            if ($limitstart >= $limit * $nCont)
            {
                $tmpStart = $limitstart - $limit * $nCont;
                $pageindex.= sprintf($base_link, $tmpStart, $tmpStart / $limit + 1);
            }

        // Show the current page. (1 ... 6 7 >[8]< 9 10 ... 15)
        if (!$limitstart_invalid)
            $pageindex .= '[<b>' . ($limitstart / $limit + 1) . '</b>] ';
        else
            $pageindex .= sprintf($base_link, $limitstart, $limitstart / $limit + 1);

        // Show the pages after the current one... (1 ... 6 7 [8] >9 10< ... 15)
        $tmpMaxPages = (int) (($total - 1) / $limit) * $limit;
        for ($nCont = 1; $nCont <= $PageContiguous; $nCont++)
            if ($limitstart + $limit * $nCont <= $tmpMaxPages)
            {
                $tmpStart = $limitstart + $limit * $nCont;
                $pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $limit + 1);
            }

        // Show the '...' part near the end. (1 ... 6 7 [8] 9 10 >...< 15)
        if ($limitstart + $limit * ($PageContiguous + 1) < $tmpMaxPages)
            $pageindex .= '<b> ... </b>';

        // Show the last number in the list. (1 ... 6 7 [8] 9 10 ... >15<)
        if ($limitstart + $limit * $PageContiguous < $tmpMaxPages)
            $pageindex .= sprintf($base_link, $tmpMaxPages, $tmpMaxPages / $limit + 1);
    }

    return $pageindex;
}
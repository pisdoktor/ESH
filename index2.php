<?php
//  ;)
define('ERISIM', 1);

require( dirname( __FILE__ ) . '/global.php' );
require( dirname( __FILE__ ) . '/config.php' );

require_once( dirname( __FILE__ ) . '/includes/base.php' );
require_once(dirname( __FILE__ ) . '/includes/functions.php');

$option = strval(strtolower(getParam($_REQUEST, 'option')));
$bolum = strval(strtolower(getParam($_REQUEST, 'bolum')));
$task = strval(strtolower(getParam($_REQUEST, 'task')));

$mainframe = new mainFrame( $dbase, $option );
$mainframe->initSession();

$my = $mainframe->getUser();

ob_start();

ob_end_clean();

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

initGzip();

if ($my->id) {
	//sistem yöneticisi ise 
	if ($my->access_type == 'admin') {    
		require_once(ABSPATH.'/admin/includes/functions.php');    
			
		loadAdminModule();
			
	} else {        
		loadSiteModule();
	}
}


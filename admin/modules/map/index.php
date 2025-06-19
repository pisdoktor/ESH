<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 



include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
	getMap();
	break;
}

function getMap() {
    global $dbase, $mainframe;
    
    $dbase->setQuery("SELECT id, isim, soyisim, coords FROM #__hastalar WHERE pasif=0 AND coords > 0");
    $rows = $dbase->loadObjectList();
    
    
    geoMapHTML::getMap($rows);
}

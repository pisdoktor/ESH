<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');  

$limit = intval(getParam($_REQUEST, 'limit', 15));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$isim = strval(getParam($_REQUEST, 'isim'));
$soyisim = strval(getParam($_REQUEST, 'soyisim'));
$mahalle = getParam($_REQUEST, 'mahalle');

switch($task) {
    default:
    case 'list':
    getirHastalar($isim, $soyisim, $mahalle);
    break;

}

function getirHastalar($isim, $soyisim, $mahalle) {
    global $dbase, $limit, $limitstart;
    
    $alfabe = array('A','B','C','Ç','D','E','F','G','H','I','İ','J','K','L','M','N','O','Ö','P','R','S','Ş','T','U','Ü','V','Y','Z');
    
    $list[] = mosHTML::makeOption('', 'Tümü');
    
    foreach ($alfabe as $alp) {
    $list[] = mosHTML::makeOption($alp, $alp);
    }

    $where[] = "h.pasif='0' ";

    if ($isim) {
    $where[] = "h.isim LIKE '" . $dbase->getEscaped( trim( $isim ) ) . "%'";
    }
    
    if ($soyisim) {
    $where[] = "h.soyisim LIKE '" . $dbase->getEscaped( trim( $soyisim ) ) . "%'";
    }
    
    if ($mahalle) {
        $dizi = implode(',', $mahalle);
        
        $where[] = "h.mahalle IN (".$dizi.")";
    }

    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
    $pageNav = new pageNav( $total, $limitstart, $limit);
    
    $lists['isim'] = mosHTML::selectList($list, 'isim', '', 'value', 'text', $isim);
    $lists['soyisim'] = mosHTML::selectList($list, 'soyisim', '', 'value', 'text', $soyisim);
    
    $query = "SELECT h.*, m.mahalle, ilc.ilce, COUNT(iz.id) AS izlemsayisi FROM #__hastalar AS h "
    . "\n CROSS JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n CROSS JOIN #__ilce AS ilc ON ilc.id=h.ilce "
    . "\n CROSS JOIN #__izlemler AS iz ON iz.hastatckimlik=h.tckimlik "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . "\n GROUP BY h.id " 
    . "\n ORDER BY h.isim ASC, h.soyisim ASC";
    
    $dbase->setQuery($query, $limitstart, $limit);
    
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {

        $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
        $row->sonizlemtarihi = $dbase->loadResult();
    }

    $dbase->setQuery("SELECT m.*, i.ilce FROM #__mahalle AS m "
     . "\n CROSS JOIN #__ilce AS i ON i.id=m.ilceid ORDER BY i.id ASC, m.mahalle ASC");
    
    $mahalleler = $dbase->loadObjectList();
    
    $data = array();
    
    foreach ($mahalleler as $mah){
        $mlist[] = mosHTML::makeOption($mah->id, $mah->ilce.' - '.$mah->mahalle);
        
        if (!isset($data[$mah->ilceid][$mah->id])) {
        $data[$mah->ilce][$mah->id] = $mah->id;
        }
    }
    $lists['mahalle'] = mosHTML::checkboxList($mlist, 'mahalle', '', 'value', 'text', $mahalle); 
            
    Dosyalama::getirHastalar($rows, $lists, $pageNav, $isim, $soyisim, $mahalle, $data);

}

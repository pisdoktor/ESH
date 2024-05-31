<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');  

$limit = intval(getParam($_REQUEST, 'limit', 20));
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
    
    $alfabe = array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,R,S,T,U,V,Y,Z);
    
    $list[] = mosHTML::makeOption('', 'Başlangıç');
    
    foreach ($alfabe as $alp) {
    $list[] = mosHTML::makeOption($alp, $alp);
    }
    
    
    $where[] = " pasif='0' ";
    
    
    
    if ($isim) {
    $where[] = "isim LIKE '" . $dbase->getEscaped( trim( $isim ) ) . "%'";
    }
    
    if ($soyisim) {
    $where[] = "soyisim LIKE '" . $dbase->getEscaped( trim( $soyisim ) ) . "%'";
    }
    
    
    /*
    if ($mahalle) {
        $dizi = implode(',', $mahalle);
        
        $where[] = "mahalle IN (".$dizi.")";
    
    }
    */
    $query = "SELECT COUNT(id) FROM #__hastalar "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
    $pageNav = new pageNav( $total, $limitstart, $limit);
    
    $lists['isim'] = mosHTML::selectList($list, 'isim', '', 'value', 'text', $isim);
    $lists['soyisim'] = mosHTML::selectList($list, 'soyisim', '', 'value', 'text', $soyisim);
    
    $query = "SELECT h.isim, h.soyisim, m.mahalle FROM #__hastalar AS h "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" ) 
    . "\n ORDER BY isim ASC, soyisim ASC";
    
    $dbase->setQuery($query, $limitstart, $limit);
    
    $rows = $dbase->loadObjectList();
    
    /*
    $dbase->setQuery("SELECT * FROM #__mahalle ORDER BY mahalle ASC");
    
    $mahalleler = $dbase->loadObjectList();
    
    foreach ($mahalleler as $mahalle){
        $mlist[] = mosHTML::makeOption($mahalle->id, $mahalle->mahalle);
    }
    $lists['mahalle'] = mosHTML::selectList($mlist, 'mahalle[]', 'multiple', 'value', 'text', $mahalle); 
     */
    Dosyalama::getirHastalar($rows, $lists, $pageNav, $isim, $soyisim);

}

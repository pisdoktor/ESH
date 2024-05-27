<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');
 
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));
$today = date('d.m.Y');
$first = date("01.m.Y", strtotime($today));
$last = date("t.m.Y", strtotime($today));
$baslangictarih = getParam($_REQUEST, 'baslangictarih', $first);
$bitistarih = getParam($_REQUEST, 'bitistarih', $last);

$ordering = getParam($_REQUEST, 'ordering');   

switch($task) {
    default:
    case 'list':
    getHastaList($baslangictarih, $bitistarih, $ordering);
    break;
}

function getHastaList($baslangictarih, $bitistarih, $ordering) {
    global $dbase, $limit, $limitstart;
    
    $where = array(); 
    if ($baslangictarih) {
    
        $cbaslangictarih = tarihCevir($baslangictarih);    
        
        $where[] = "h.pasiftarihi>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih); 
                                       
        $where[] = "h.pasiftarihi<='".$cbitistarih."'";
    }
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.pasiftarihi DESC";
     }

     //Pasif olanları alalım
     $where[] = "h.pasif=1";
    
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h"
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     $query = "SELECT h.*, m.mahalle AS mahalleadi FROM #__hastalar AS h "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle"
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY h.id "
     . $orderingfilter    
     //. "\n ORDER BY h.pasiftarihi DESC"
     
     ;
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    HastaList::getHastaList($rows, $pageNav, $baslangictarih, $bitistarih, $ordering);
}
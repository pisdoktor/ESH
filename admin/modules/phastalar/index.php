<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');
 
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));
$today = date('d.m.Y');
$first = date("01.m.Y", strtotime($today));
$last = date("t.m.Y", strtotime($today));
$search = strval(getParam($_REQUEST, 'search'));
$baslangictarih = getParam($_REQUEST, 'baslangictarih');
$bitistarih = getParam($_REQUEST, 'bitistarih');
$ordering = getParam($_REQUEST, 'ordering'); 
$secim = intval(getParam($_REQUEST, 'secim'));  

switch($task) {
    default:
    case 'list':
    getHastaList($search, $baslangictarih, $bitistarih, $ordering, $secim);
    break;
}

function getHastaList($search, $baslangictarih, $bitistarih, $ordering, $secim) {
    global $dbase, $limit, $limitstart;
    
    $where = array();
    if ($search) {
         $search = mosStripslashes($search);
         if (is_numeric($search)) {
         $where[] = "h.tckimlik = ". $dbase->getEscaped( $search );
         } else {
         $where[] = "(h.isim LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%' OR h.soyisim LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%')";
         } 
     }
      
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
     
     if ($secim) {
        $where[] = 'pasifnedeni='.$secim;
     }

     //Pasif olanları alalım
     $where[] = "h.pasif=1";
    
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h"
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     //sebebine göre filtreleme
     $pasif = array(
  '1' => 'İyileşme',
  '2' => 'Vefat',
  '3' => 'İkamet Değişikliği',
  '4' => 'Tedaviyi Reddetme',
  '5' => 'Tedaviye Yanıt Alamama',
  '6' => 'Sonlandırmanın Talep Edilmesi',
  '7' => 'Tedaviye Personel Gerekmemesi',
  '8' => 'ESH Takibine Uygun Olmaması'
  );
  
    $s[] = mosHTML::makeOption('0', 'Seçim Yapın');
    foreach ($pasif as $v=>$k) {
    $s[] = mosHTML::makeOption($v, $k);
    }
    
    $pasifneden = mosHTML::selectList($s, 'secim', 'id="secim"', 'value', 'text', $secim);
     
     $query = "SELECT h.*, m.mahalle AS mahalleadi, i.ilce AS ilceadi FROM #__hastalar AS h "
     . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY h.id "
     . $orderingfilter    
     //. "\n ORDER BY h.pasiftarihi DESC"
     
     ;
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
        $row->izlemsayisi = $dbase->loadResult();
        $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
        $row->sonizlemtarihi = $dbase->loadResult();
    }
    
    HastaList::getHastaList($rows, $pageNav, $search, $baslangictarih, $bitistarih, $ordering, $pasifneden, $secim);
}
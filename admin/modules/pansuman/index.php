<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );


include(dirname(__FILE__). '/html.php');

$id = intval(getParam($_REQUEST, 'id')); 

$today = date('w');
$day = intval(getParam($_REQUEST, 'day', $today));

switch($task) {
    default:
    case 'list':
    getHastaList($day);
    break;
    
    case 'add':
    hastaEkle($id);
    break;
    
    case 'delete':
    hastaCikar($id);
    break;
    
    case 'save':
    hastaKaydet();
    break;
}

function hastaKaydet() {
    global $dbase;
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
     if ($row->pansuman) {
    $row->pgunleri = implode(',', $row->pgunleri);
    } else {
    $row->pgunleri = '';
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$row->id);


}

function hastaCikar($id) {
    global $dbase;
    
    $row = new Hasta($dbase);
    
    $row->load($id);
    
    if (!$row->id) {
        Redirect('index.php?option=admin&bolum=hastalar', 'Böyle bir hasta yok!!!'); 
    }
    
    $row->pansuman = 0;
    $row->pgunleri = '';
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect("index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=".$row->tckimlik);
}

function hastaEkle($id) {
    global $dbase;
    
    $row = new Hasta($dbase);
    
    $row->load($id);
    
    //pansuman listesi işlemleri
    $daylist[] = mosHTML::makeOption('1', 'Pazartesi');
    $daylist[] = mosHTML::makeOption('2', 'Salı');
    $daylist[] = mosHTML::makeOption('3', 'Çarşamba');
    $daylist[] = mosHTML::makeOption('4', 'Perşembe');
    $daylist[] = mosHTML::makeOption('5', 'Cuma');
    $daylist[] = mosHTML::makeOption('6', 'Cumartesi');
    $daylist[] = mosHTML::makeOption('0', 'Pazar');
    
    $row->pgunleri = explode(',', $row->pgunleri);
   
    
    $lists['days'] = mosHTML::checkboxList($daylist, 'pgunleri', '', 'value', 'text', $row->pgunleri);
    
    PansumanHTML::hastaEkle($row, $lists);
    
}

function getHastaList($day) {
    global $dbase;
    
    $query = "SELECT h.*, m.mahalle, i.ilce FROM #__hastalar AS h "
     . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . "\n WHERE h.pasif=0 AND h.pansuman=1 AND FIND_IN_SET(".$day.", h.pgunleri) "
     . "\n ORDER BY h.mahalle ASC, h.isim ASC";
     $dbase->setQuery($query);
     $rows = $dbase->loadObjectList();
     
     foreach ($rows as $row) {
     $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
     $row->sonizlemtarihi = $dbase->loadResult();
     
     $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
     $row->izlemsayisi = $dbase->loadResult();
     }

    PansumanHTML::getHastaList($day, $rows);
}



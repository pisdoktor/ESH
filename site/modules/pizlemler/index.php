<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');

$id = intval(getParam($_REQUEST, 'id')); 
$cid = intval(getParam($_REQUEST, 'cid'));
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));
$tc = getParam($_REQUEST, 'tc');
$today = date('d.m.Y');
$first = date("01.m.Y", strtotime($today));
$last = date("t.m.Y", strtotime($today));
$baslangictarih = getParam($_REQUEST, 'baslangictarih', $today);
$bitistarih = getParam($_REQUEST, 'bitistarih', $today);
$ordering = getParam($_REQUEST, 'ordering');
$secim = intval(getParam($_REQUEST, 'secim'));   

switch($task) {
    case 'list':
    getIzlemList($baslangictarih, $bitistarih, $ordering, $secim);
    break;
    
    case 'edit':
    editIzlem($id);
    break;
    
    case 'new':
    editIzlem(0);
    break;
    
    case 'save':
    saveIzlem();
    break;
    
    case 'delete':
    deleteIzlem($id);
    break;
    
    case 'control':
    controlTC($tc);
    break;
    
    default:
    case 'takvim':
    getTakvim();
    break;
}

function getTakvim() {
    global $dbase;
    
    $query = "SELECT i.*, h.isim, h.soyisim, isl.islemadi, m.mahalle, isl.id AS islem FROM #__izlemler AS i "
     . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
     . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilacak "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . "\n WHERE planli=1 AND h.pasif=0 "
     . "\n GROUP BY i.id ";
     $dbase->setQuery($query);
     $rows = $dbase->loadObjectList();
     
     $data = array();
     
     $colors = array('#00DF50', '#CACACA', '#DADADA', '#8a9290', '#6z8078', '#FAFAFA', 
     '#0A2F51', '#137177', '#1D9A6C', '#39A96B', '#74C67A', '#A5AF2A',
     '#FBBACC', '#609504', '#EB942E', '#811A70', '#A093CF', '#BA4383',
     '#EA5E0B', '#FAA6DA', '#7a8189', '#BFE1B0');
     
     foreach ($rows as $row) {
         $data[] = "{
         title:'".$row->isim." ".$row->soyisim." (".$row->islemadi.")',
         start:'".date('Y-m-d', $row->planlanantarih)."',
         url: 'index.php?option=site&bolum=pizlemler&task=edit&id=".$row->id."',
         backgroundColor: '".$colors[$row->islem]."'
         }";
     }    
IzlemList::getTakvim($data); 
}

function deleteIzlem($id) {
    global $dbase;
    
    $del = new Izlem($dbase);
    $del->load($id);
    
    $dbase->setQuery("UPDATE #__izlemler SET planli='0', planlanantarih='0', yapilacak='0' WHERE id=".$del->id);
    $dbase->query();
    
    Redirect("index.php?option=site&bolum=izlemler&task=izlemgetir&tc=".$del->hastatckimlik, "Planlanan izlem silindi");
    
}

function controlTC($tc) {
    global $dbase;
    
    $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$tc." AND pasif=0");
    $dbase->loadObject($row);
    
    if (!$row) {
        echo "Böyle bir hasta kayıtlı değil";
    } else {
        echo $row->isim.' '.$row->soyisim;
    }
   
}

function getIzlemList($baslangictarih, $bitistarih, $ordering, $secim) {
    global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "i.planlanantarih>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "i.planlanantarih<='".$cbitistarih."'";
    }
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY i.planlanantarih ASC, h.isim ASC, h.soyisim ASC, h.mahalle ASC";
     }
    
    $where[] = "i.planli=1 ";
    $where[] = "h.pasif=0 ";
    
    if ($secim) {
        $where[] = "FIND_IN_SET(".$secim.", i.yapilacak)";
    }


    $query = "SELECT COUNT(i.id) FROM #__izlemler AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    ;
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     $query = "SELECT i.*, h.isim, h.soyisim, isl.islemadi, m.mahalle, ilc.ilce FROM #__izlemler AS i "
     . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
     . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilacak "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . "\n LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY i.id "                                                               
     . $orderingfilter
     ;
    
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    //işlemler select listesi
    $dbase->setQuery("SELECT * FROM #__islem ORDER BY id");
    $islemler = $dbase->loadObjectList();
    
    $data[] = mosHTML::makeOption('0', 'Bir İşlem Seçin'); 
    foreach ($islemler as $islem) {
    $data[] = mosHTML::makeOption($islem->id, $islem->islemadi);
    }
    
    $list['islem'] = mosHTML::selectList($data, 'secim', '', 'value', 'text', $secim);
    
    IzlemList::getIzlemList($rows, $pageNav, $baslangictarih, $bitistarih, $ordering, $list, $secim);
}

function saveIzlem() {
         global $dbase, $limitstart, $limit, $cid;
    
    $row = new Izlem( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        ErrorAlert($row->getError());
    }
    
    
    $row->izlemiyapan = implode(',', $row->izlemiyapan);
    $row->yapilan = implode(',', $row->yapilan);    
    $row->izlemtarihi = tarihCevir($row->izlemtarihi);
    
    if ($row->yapildimi) {
        $row->neden = '';
    }
    
    if (!$row->planli) {
        $row->planlanantarih = '';
        $row->yapilacak = '';    
    } else {
        
        $row->planlanantarih = tarihCevir($row->planlanantarih);
        $row->yapilacak = implode(',', $row->yapilacak);
        
        $dbase->setQuery("SELECT id FROM #__izlemler WHERE hastatckimlik=".$row->hastatckimlik." AND planlanantarih=".$row->planlanantarih." AND yapilacak=".$row->yapilacak);
        $varmi = $dbase->loadResult();
        
        if ($varmi) {
        ErrorAlert('Bu izlem daha önceden planlanmış!!!');
        }
        
        if ($row->planlanantarih == '') {
        ErrorAlert('Planlanan tarih girilmemiş');
        }
        
        if ($row->yapilacak == '') {
        ErrorAlert('Yapılacak işlem(ler) seçilmemiş');
        }
    } 
        
    if ($row->izlemiyapan == '') {
        ErrorAlert('İzlemi yapan(lar) seçilmemiş');
    }
    
    if ($row->yapilan == '') {
        ErrorAlert('Yapılan işlem(ler) seçilmemiş');
    }
    
    if ($row->izlemtarihi == '') {
        ErrorAlert('İzlem tarihi seçilmemiş');
    } 
    
    if (!$row->store()) {
        ErrorAlert($row->getError());
    }
    
    $dbase->setQuery("UPDATE #__izlemler SET planli='0', planlanantarih='', yapilacak='' WHERE id=".$cid);
    $upd = $dbase->query();
    
    $link = "index.php?option=site&bolum=izlemler";
    
    Redirect($link, 'İzlem Bilgileri kaydedildi');
    
}

function editIzlem($id) {
    global $dbase, $limitstart, $limit;
    
    $row = new Izlem($dbase);
    
    $row->load($id);
  
   
    if ($row->id) {
     $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$row->hastatckimlik);
     $dbase->loadObject($hasta);   
    }
    
    //işlem seçme kutusu yapalım
    $dbase->setQuery("SELECT * FROM #__islem ORDER BY islemadi ASC");
    $islemler = $dbase->loadObjectList();
    
    $islemtype = array();
    foreach ($islemler as $islem) {
        $islemtype[] = mosHTML::makeOption($islem->id, $islem->islemadi);
    }
    
    $row->yapilan = explode(',', $row->yapilan);
    $row->yapilacak = explode(',', $row->yapilacak);
    
    $lists['isplanlanan'] = mosHTML::checkboxList($islemtype, 'yapilacak', '', 'value','text');
    $lists['isyapilacak'] = mosHTML::checkboxList($islemtype, 'yapilan', '', 'value','text', $row->yapilacak);
    
    // PERSONEL SEÇME KUTULARI YAPALIM
    $dbase->setQuery("SELECT id, name FROM #__users WHERE activated = '1' ORDER BY name ASC");
    $personel = $dbase->loadObjectList();
    
    $islemiyapan = array();
    foreach ($personel as $per) {
        $islemiyapan[] = mosHTML::makeOption($per->id, $per->name);   
    }
    
    $lists['perlist'] = mosHTML::checkboxList($islemiyapan, 'izlemiyapan', '', 'value', 'text');
    
    $nedenler = array(
    '1' => 'Hastanede yatıyor',
    '2' => 'Vefat etmiş',
    '3' => 'Adresi değişmiş',
    '4' => '112 ye teslim edildi',
    '5' => 'Hizmet reddedildi',
    '6' => 'Evde kendisi/yakını yok',
    '7' => 'Gerekli evrak yok' 
    );
    
    foreach ($nedenler as $v=>$k) {
        $yneden[] = mosHTML::makeOption($v, $k);
    }
    
    $lists['yneden'] = mosHTML::radioList($yneden, 'neden', '', 'value', 'text');
    
    IzlemList::editIzlem($row, $limit, $limitstart, $hasta, $lists);

}
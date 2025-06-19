<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

$id = intval(getParam($_REQUEST, 'id'));
$today = date('d.m.Y');
$first = date("01.m.Y", strtotime($today));
$last = date("t.m.Y", strtotime($today));

$month = intval(getParam($_REQUEST, 'month', date('m')));
$year = intval(getParam($_REQUEST, 'year', date('Y'))); 

$baslangictarih = getParam($_REQUEST, 'baslangictarih', $first);
$bitistarih = getParam($_REQUEST, 'bitistarih', $last);

$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$secim = strval(getParam($_REQUEST, 'secim', ''));

$ordering = getParam($_REQUEST, 'ordering');

$ilce = intval(getParam($_REQUEST, 'ilce'));
$mahalle = intval(getParam($_REQUEST, 'mahalle'));
$sokak = intval(getParam($_REQUEST, 'sokak'));
$kapino = intval(getParam($_REQUEST, 'kapino'));
$ozellik = getParam($_REQUEST, 'ozellik');

include(dirname(__FILE__). '/html.php');

switch($task) {
    default:
    case 'temel':
    temelStats($baslangictarih, $bitistarih, $ordering);
    break;
    
    case 'hmahalle':
    hMahalle($secim);
    break;
    
    case 'kayityili':
    hKayityili();
    break;
    
    case 'kayitayi':
    hKayitayi();
    break;
    
    case 'yasgruplari':
    hYasGruplari();
    break;
    
    case 'hastalik':
    hastalikStats();
    break;
    
    case 'special':
    specialStats();
    break;
    
    case 'specialgetir':
    specialGetir($secim, $limitstart, $limit, $ordering);
    break;
    
    case 'dogumgunu':
    dogumGunuGetir();
    break;
    
    case 'izlenmeyen':
    Izlenmeyenler($secim, $ordering, $limitstart, $limit);
    break;
    
    case 'adres':
    adresHastaFiltre($ilce, $mahalle, $sokak, $kapino, $ozellik, $ordering);
    break;
    
    case 'ilce':
    getirIlce();
    break;
    
    case 'mahalle':
    getirMahalle($id);
    break;
    
    case 'sokak':
    getirSokak($id);
    break;
    
    case 'kapino':
    getirKapino($id);
    break;
    
    case 'islem':
    islemGetir($baslangictarih, $bitistarih);
    break;
    
    case 'personel':
    personelGetir($baslangictarih, $bitistarih);
    break;
    
    case 'hgirilmeyen':
    hastalikGirilmemiş($ordering, $secim, $ozellik);
    break;
    
    case 'sondadegisim':
    sondaDegisimTakip($baslangictarih, $bitistarih, $ordering);
    break;
    
    case 'mamarapor':
    mamaRaporuGetir($baslangictarih, $bitistarih, $ordering);
    break;
    
    case 'bezrapor':
    bezRaporuGetir($baslangictarih, $bitistarih);
    break;
    
    case 'ilacrapor':
    ilacRaporuGetir($baslangictarih, $bitistarih);
    break;
    
    case 'hastagetir':
    hastaGetir($id, $limitstart, $limit, $ordering);
    break;
    
    case 'izlemolmayan':
    IzlemiOlmayan($limit, $limitstart);
    break;
}

function sondaDegisimTakip($baslangictarih, $bitistarih, $ordering) {
     global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) {
    
        $birayoncesibaslangic = strtotime('-1 month', tarihCevir($baslangictarih));  
        
        $cbaslangictarih = $birayoncesibaslangic;
        
       $where[] = "h.sondatarihi>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $birayoncesibitis = strtotime('-1 month', tarihCevir($bitistarih));
        
        $cbitistarih = $birayoncesibitis;
                                       
        $where[] = "h.sondatarihi<='".$cbitistarih."'";
    }
    
    $where[] = "h.sonda='1' AND h.pasif='0'";
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.sondatarihi ASC";
     }
    
    $query = "SELECT COUNT(*) FROM #__hastalar AS h "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );
    $dbase->setQuery($query);
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h "
    . "\n LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . $orderingfilter
    ;
    
    $dbase->setQuery($query2, $limitstart, $limit);
    
    
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
        $row->toplamizlem = $dbase->loadResult();
    }
    
    StatsHTML::sondaDegisimTakip($rows, $baslangictarih, $bitistarih, $pageNav, $ordering);
}

function mamaRaporuGetir($baslangictarih, $bitistarih, $ordering) {
    global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "h.mamaraporbitis>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "h.mamaraporbitis<='".$cbitistarih."'";
    }
    
    $where[] = "h.mama='1'";
    $where[] = "h.pasif='0'";
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.mamaraporbitis ASC";
     }
 
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h" 
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );
    $dbase->setQuery($query);
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    $query2 = "SELECT h.*, i.ilce, m.mahalle FROM #__hastalar AS h "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . $orderingfilter
    ;
    $dbase->setQuery($query2, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
        $row->toplamizlem = $dbase->loadResult();
    }
    
    StatsHTML::mamaRaporuGetir($rows, $baslangictarih, $bitistarih, $pageNav, $ordering);
}

function bezRaporuGetir($baslangictarih, $bitistarih) {
    global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "h.bezraporbitis>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "h.bezraporbitis<='".$cbitistarih."'";
    }
    
    $where[] = "h.bezrapor='1'";
    $where[] = "h.pasif='0'";
 
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h" 
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );
    $dbase->setQuery($query);
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    $query2 = "SELECT h.*, i.ilce, m.mahalle FROM #__hastalar AS h "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . "\n ORDER BY bezraporbitis ASC"
    ;
    $dbase->setQuery($query2);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
        $row->toplamizlem = $dbase->loadResult();
    }
    
    StatsHTML::bezRaporuGetir($rows, $baslangictarih, $bitistarih, $pageNav);
}

function ilacRaporuGetir($baslangictarih, $bitistarih) {
    global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "r.bitistarihi>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "r.bitistarihi<='".$cbitistarih."'";
    }
    
    $where[] = "h.pasif='0'";
    
    $query1 = "SELECT COUNT(*) FROM #__hastailacrapor as r "
    . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=r.hastatckimlik " 
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    ;
    $dbase->setQuery($query1);
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    
    $query2 = "SELECT h.*, r.bitistarihi, hh.hastalikadi, r.brans, i.ilce, m.mahalle, r.raporyeri FROM #__hastailacrapor as r "
    . "\n CROSS JOIN #__hastalar AS h ON h.tckimlik=r.hastatckimlik "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__hastaliklar AS hh ON hh.id=r.hastalikid "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . "\n ORDER BY r.bitistarihi ASC"
    ;
    $dbase->setQuery($query2, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $dbase->setQuery("SELECT bransadi FROM #__branslar WHERE id IN (".$row->brans.")");
        
        $row->branslar = $dbase->loadResultArray();
        
        $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
        $row->toplamizlem = $dbase->loadResult();
    
    }
    
    StatsHTML::ilacRaporGetir($rows, $baslangictarih, $bitistarih, $pageNav); 
}

function IzlemiOlmayan($limit, $limitstart) {
    global $dbase;
    
    $dbase->setQuery("SELECT h.id, h.tckimlik, COUNT(i.id) AS izlemsayisi FROM #__hastalar AS h "
    . "\n INNER JOIN #__izlemler AS i ON i.hastatckimlik=h.tckimlik "
    . "\n GROUP BY h.id " 
    );
    $hastalar = $dbase->loadObjectList();
    
    foreach ($hastalar as $hasta) {
    $data[] = $hasta->tckimlik;
    }
    
    $lists = implode(',', $data);
    
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE tckimlik NOT IN (".$lists.")");
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    $query = "SELECT h.id, h.isim, h.soyisim, h.cinsiyet, h.tckimlik, m.mahalle, i.ilce FROM #__hastalar AS h "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    ."\n WHERE h.tckimlik NOT IN (".$lists.") ORDER BY h.isim ASC, h.soyisim ASC";
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    StatsHTML::IzlemiOlmayan($rows, $total, $pageNav);
}

function hastaGetir($id, $limitstart, $limit, $ordering) {
    global $dbase;
    
    if (!$id) {
        echo "Hastalık seçilmemiş";
        NotAuth();
        break;
    }
    
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC";
     }
    
    $dbase->setQuery("SELECT COUNT(h.id) FROM #__hastalar AS h"
    . "\n WHERE FIND_IN_SET(".$id.", h.hastaliklar) AND h.pasif=0");
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    $query = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h"
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce " 
    . "\n WHERE FIND_IN_SET(".$id.", h.hastaliklar) AND h.pasif=0 "
    . $orderingfilter
    ;
    
    $dbase->setQuery($query, $limitstart, $limit);
    
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
    $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
    $row->toplamizlem = $dbase->loadResult();
    
    $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
    $row->sonizlem = $dbase->loadResult();
    }
    
    $dbase->setQuery("SELECT id, hastalikadi FROM #__hastaliklar WHERE id=".$id);
    $dbase->loadObject($hastalik);
    
    StatsHTML::hastaGetir($rows, $hastalik, $pageNav, $total, $ordering);

}

function hastalikGirilmemiş($ordering, $secim, $ozellik) {
    global $dbase, $limit, $limitstart;
    

$where = array();

$filtre[] = mosHTML::makeOption('', 'Eksik Bilgi Seçin');
$filtre[] = mosHTML::makeOption('h.boy', 'Boy');
$filtre[] = mosHTML::makeOption('h.kilo', 'Kilo');
$filtre[] = mosHTML::makeOption('h.anneAdi', 'Anne Adı');
$filtre[] = mosHTML::makeOption('h.babaAdi', 'Baba Adı');
$filtre[] = mosHTML::makeOption('h.kapino', 'Kapı No');
$filtre[] = mosHTML::makeOption('h.sokak', 'Cadde/Sokak Adı');
$filtre[] = mosHTML::makeOption('h.mahalle', 'Mahalle Adı');
$filtre[] = mosHTML::makeOption('h.ilce', 'İlçe Adı');
$filtre[] = mosHTML::makeOption('h.hastaliklar', 'Hastalıkları');
$filtre[] = mosHTML::makeOption('h.coords', 'Adres Koordinat');
$filtre[] = mosHTML::makeOption('h.ceptel1', 'Telefon Bilgisi');
//$filtre[] = mosHTML::makeOption('h.bagimlilik', 'Bağımlılık Durumu');

$lists['filtre'] = mosHTML::selectList($filtre, 'secim', '', 'value', 'text', $secim);

if ($ozellik == '1') {
$where2 = " AND h.pasif='1'";
} else if ($ozellik == '0') {
$where2 = " AND h.pasif='0'";
} else {
$where2 = '';
}

$oz = array();
$oz[] = mosHTML::makeOption('', 'Tüm Hastalar'); 
$oz[] = mosHTML::makeOption('0', 'Aktif Hastalar');
$oz[] = mosHTML::makeOption('1', 'Pasif Hastalar');
$lists['ozellik'] = mosHTML::selectList($oz, 'ozellik', '', 'value', 'text', $ozellik);

if ($secim) {
$where[] = $secim."=''";
} else {
$where[] = "h.cinsiyet=''";
$where[] = "h.bagimlilik='0' OR h.bagimlilik=''";
$where[] = "h.hastaliklar='0' OR h.hastaliklar='' OR h.hastaliklar=NULL";
$where[] = "h.kapino='' OR h.sokak='' OR h.mahalle='' OR h.ilce=''";
$where[] = "h.coords='' OR h.coords<0";
$where[] = "h.anneAdi='' OR h.babaAdi=''";
$where[] = "h.boy='' OR h.kilo=''";
}

    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC";
     }
     
     $query = "SELECT COUNT(h.id) FROM #__hastalar AS h "
     . ( count( $where ) ? "\n WHERE (" . implode( ' OR ', $where ).")" : "" )
     . $where2
     ;

$dbase->setQuery($query);
$total = $dbase->loadResult();

$pageNav = new pageNav( $total, $limitstart, $limit); 

$query = "SELECT h.*, i.ilce, m.mahalle, s.sokakadi, k.kapino FROM #__hastalar AS h "
. "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
. "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
. "\n LEFT JOIN #__sokak AS s ON s.id=h.sokak "
. "\n LEFT JOIN #__kapino AS k ON k.id=h.kapino "
. ( count( $where ) ? "\n WHERE (" . implode( ' OR ', $where ).")" : "" )
. $where2 
. $orderingfilter
;

$dbase->setQuery($query, $limitstart, $limit);

$rows = $dbase->loadObjectList();
                                     
foreach ($rows as $row) {
    $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
    $row->toplamizlem = $dbase->loadResult();
}



StatsHTML::hastalikGirilmemis($rows, $total, $pageNav, $ordering, $secim, $lists, $ozellik); 
}

function personelGetir($baslangictarih, $bitistarih) {
    global $dbase;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "i.izlemtarihi>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "i.izlemtarihi<='".$cbitistarih."'";
    }
    
    //yeni ekleme 11.10.2024 13:14
    /**
    * @iki tarih arasında "izlemiyapan" tüm personelleri alalım 
    */
    $query = "SELECT izlemiyapan FROM #__izlemler AS i "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    ;
    $dbase->setQuery($query);
    
    $list = $dbase->loadResultArray();
    
    $list = implode(',', $list);
    
    $list = explode(',', $list);
    
    $veri = array_count_values($list);
    
    //işlemleri alalım
    $dbase->setQuery("SELECT id, name FROM #__users ORDER BY name ASC");
    $personeller = $dbase->loadObjectList();
    
    $data = array();
    
    foreach ($personeller as $personel) {
        if (!isset($data[$personel->id])) {
        $data[$personel->id]['personeladi'] = $personel->name;
        $data[$personel->id]['islemsayisi'] = $veri[$personel->id] ? $veri[$personel->id]: '0';
        }
    }
    //yeni ekleme
    
    StatsHTML::personelGetir($data, $baslangictarih, $bitistarih); 
    

}

function islemGetir($baslangictarih, $bitistarih) {
    global $dbase;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "i.izlemtarihi>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "i.izlemtarihi<='".$cbitistarih."'";
    }
    
    //yeni ekleme 11.10.2024 13:14
    /**
    * @iki tarih arasında "yapılan" tüm işlemleri alalım 
    */
    $query = "SELECT yapilan FROM #__izlemler AS i "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )

    ;
    $dbase->setQuery($query);
    
    $list = $dbase->loadResultArray();
    
    $list = implode(',', $list);
    
    $list = explode(',', $list);
    
    $veri = array_count_values($list);
    
    //işlemleri alalım
    $dbase->setQuery("SELECT * FROM #__islem ORDER BY islemadi ASC");
    $islemler = $dbase->loadObjectList();
    
    $data = array();
    
    foreach ($islemler as $islem) {
        if (!isset($data[$islem->id])) {
        $data[$islem->id]['islemadi'] = $islem->islemadi;
        $data[$islem->id]['islemsayisi'] = $veri[$islem->id] ? $veri[$islem->id]: '0';
        }
    }
    //yeni ekleme
    
    StatsHTML::islemGetir($data, $baslangictarih, $bitistarih);
}

function adresHastaFiltre($ilce, $mahalle, $sokak, $kapino, $ozellik, $ordering) {
    global $dbase, $limitstart, $limit;
    
    $where[] = "h.pasif='0' ";  // aktif hastalar
    
    if ($ilce) {
         $where[] = "h.ilce='".$ilce."'";
    }
    
    if ($mahalle) {
         $where[] = "h.mahalle='".$mahalle."'";
    }
    
    if ($sokak) {
         $where[] = "h.sokak='".$sokak."'";
    }
    
    if ($kapino) {
         $where[] = "h.kapino='".$kapino."'";
    }
    
    if ($ozellik) {
        $where[] = "h.".$ozellik."=1";
    }
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim, h.soyisim, h.ilce ASC, h.mahalle ASC, h.sokak ASC, h.kapino ASC";
     } 
    
     $query = "SELECT COUNT(h.id) FROM #__hastalar AS h"
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
    $pageNav = new pageNav( $total, $limitstart, $limit);
    
    $query = "SELECT h.*, ilc.ilce AS ilceadi, m.mahalle AS mahalleadi, s.sokakadi, k.kapino FROM #__hastalar AS h "
     . "\n LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . "\n LEFT JOIN #__sokak AS s ON s.id=h.sokak "
     . "\n LEFT JOIN #__kapino AS k ON k.id=h.kapino "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY h.id " 
     . $orderingfilter
     ; //"\n ORDER BY h.kayityili DESC, h.kayitay DESC, h.id ASC, h.isim ASC, h.soyisim ASC";
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
        $row->sonizlemtarihi = $dbase->loadResult();
        
        $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
        $row->toplamizlem = $dbase->loadResult();
    }
    
        //adres oluştur
     //adresler için ilçeleri alalım
    $query = "SELECT i.*, COUNT(h.id) AS hastasayisi FROM #__ilce AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.ilce=i.id "
    . "\n WHERE h.pasif=0 "
    . "\n GROUP BY i.id "
    . "\n ORDER BY i.ilce ASC";
    $dbase->setQuery($query);
    
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $dkapino[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');
    
    foreach ($adres['ilce'] as $ai) {
    $dilce[] = mosHTML::makeOption($ai->id, $ai->ilce." [".$ai->hastasayisi."]");
    }
    
    if ($ilce) {
        $dbase->setQuery("SELECT m.*, COUNT(h.id) as sayi FROM #__mahalle AS m "
        . "\n LEFT JOIN #__hastalar AS h ON h.mahalle=m.id "
        . "\n WHERE h.pasif=0 AND m.ilceid=".$ilce
        . "\n GROUP BY m.id "
        . "\n ORDER BY m.mahalle ASC"
        );
        $mahalleler = $dbase->loadObjectList();
        
        foreach ($mahalleler as $m) {
        $dmahalle[] = mosHTML::makeOption($m->id, $m->mahalle." [".$m->sayi."]");
        }
    }
    
    if ($mahalle) {
        $dbase->setQuery("SELECT s.*, COUNT(h.id) as sayi FROM #__sokak AS s "
        . "\n LEFT JOIN #__hastalar AS h ON h.sokak=s.id "
        . "\n WHERE h.pasif=0 AND s.mahalleid=".$mahalle
        . "\n GROUP BY s.id "
        . "\n ORDER BY s.sokakadi ASC "
        );
        $sokaklar = $dbase->loadObjectList();
        
        foreach ($sokaklar as $s) {
        $dsokak[] = mosHTML::makeOption($s->id, $s->sokakadi." [".$s->sayi."]");
        }
    }
    
    if ($sokak) {
        $dbase->setQuery("SELECT k.*, COUNT(h.id) as sayi FROM #__kapino AS k "
        . "\n LEFT JOIN #__hastalar AS h ON h.kapino=k.id "
        . "\n WHERE h.pasif=0 AND k.sokakid=".$sokak
        . "\n GROUP BY k.id "
        . "\n ORDER BY k.kapino ASC "
        );
        $kapinolar = $dbase->loadObjectList();
        
        foreach ($kapinolar as $k) {
        $dkapino[] = mosHTML::makeOption($k->id, $k->kapino." [".$k->sayi."]");
        }
    }


    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce"', 'value', 'text', $ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle"', 'value', 'text', $mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak"', 'value', 'text', $sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino"', 'value', 'text', $kapino);
    
    $oz = array();
    $oz[] = mosHTML::makeOption('', 'Hasta Özelliği Seçin');
    $oz[] = mosHTML::makeOption('gecici', 'Geçici Kayıtlı Hastalar');
    $oz[] = mosHTML::makeOption('ng', 'NG Takılı Hastalar');
    $oz[] = mosHTML::makeOption('peg', 'PEGli Hastalar');
    $oz[] = mosHTML::makeOption('port', 'PORTlu Hastalar');
    $oz[] = mosHTML::makeOption('o2bagimli', 'O2 Bağımlı Hastalar');
    $oz[] = mosHTML::makeOption('ventilator', 'Ventilatör Takılı Hastalar');
    $oz[] = mosHTML::makeOption('kolostomi', 'Kolostomili Hastalar');
    $oz[] = mosHTML::makeOption('sonda', 'Sondalı Hastalar');
    $oz[] = mosHTML::makeOption('mama', 'Mama Kullanan Hastalar');
    $oz[] = mosHTML::makeOption('yatak', 'Hasta Yatağı Olan Hastalar');
    $lists['ozellik'] = mosHTML::selectList($oz, 'ozellik', 'id="ozellik"', 'value', 'text', $ozellik);
    

    StatsHTML::adresHastaFiltre($rows, $lists, $ilce, $mahalle, $sokak, $kapino, $ozellik, $ordering, $pageNav); 
}

function getirIlce() {
    global $dbase;
    
    $query = "SELECT i.*, COUNT(h.id) AS hastasayisi FROM #__ilce AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.ilce=i.id WHERE h.pasif=0 GROUP BY i.id "
    . "\n ORDER BY i.ilce ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $data = array();
    foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->ilce.' ['.$row->hastasayisi.']"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
    echo $html;

}

function getirMahalle($id) {
    global $dbase;
    
    $query = "SELECT m.*, COUNT(h.id) AS hastasayisi FROM #__mahalle AS m "
    . "\n LEFT JOIN #__hastalar AS h ON h.mahalle=m.id "
    . "\n WHERE m.ilceid='".$id."' AND h.pasif=0"
    . "\n GROUP BY m.id "
    . "\n ORDER BY m.mahalle ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->mahalle." [".$row->hastasayisi."]</option>";
    }
        
    echo $html;
}

function getirSokak($id) {
    global $dbase;
    
    $query = "SELECT s.*, COUNT(h.id) AS hastasayisi FROM #__sokak AS s "
    . "\n LEFT JOIN #__hastalar AS h ON h.sokak=s.id "
    . "\n WHERE s.mahalleid='".$id."' AND h.pasif=0 " 
    . "\n GROUP BY s.id "
    . "\n ORDER BY s.sokakadi ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
   $html = '';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->sokakadi." [".$row->hastasayisi."]</option>";
    }
        
    echo $html;
}

function getirKapino($id) {
    global $dbase;
    
    $query = "SELECT k.*, COUNT(h.id) AS hastasayisi FROM #__kapino AS k "
    . "\n LEFT JOIN #__hastalar AS h ON h.kapino=k.id "
    . "\n WHERE k.sokakid='".$id."' AND h.pasif=0 "
    . "\n GROUP BY k.id "
    . "\n ORDER BY k.kapino ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->kapino." [".$row->hastasayisi."]</option>";
    }
        
    echo $html;
}

function specialGetir($secim, $limitstart, $limit, $ordering) {
       global $dbase;
       
       if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
         } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC";
         }
     
    switch($secim) {
        case 'ng':
        $title = "Nazogastrik Takılı Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.ng=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.ng=1 ".$orderingfilter;
        break;
        
        case 'peg':
        $title = "PEG Takılı Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.peg=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.peg=1 ".$orderingfilter; 
        break;
        
        case 'port':
        $title = "PORT Takılı Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.port=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.port=1 ".$orderingfilter;
        break;
        
        case 'o2bagimli':
        $title = "Oksijen Bağımlı Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.o2bagimli=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.o2bagimli=1 ".$orderingfilter; 
        break;
        
        case 'ventilator':
        $title = "Ventilatör Takılı Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.ventilator=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.ventilator=1 ".$orderingfilter; 
        break;
        
        case 'gecici':
        $title = "Geçici Kayıtlı Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.gecici=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.gecici=1 ".$orderingfilter;
        break;
        
        case 'kolostomi':
        $title = "Kolostomili Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.kolostomi=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.kolostomi=1 ".$orderingfilter;
        break;
        
        case 'sonda':
        $title = "Sonda Takılı Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.sonda=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.sonda=1 ".$orderingfilter;
        break;
        
        case 'mama':
        $title = "Mama Kullanan Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.mama=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.mama=1 ".$orderingfilter;
        break;
        
        case 'yatak':
        $title = "Hasta Yatağı Olan Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.yatak=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.yatak=1 ".$orderingfilter;
        break;
        
        case 'bez':
        $title = "Alt Bezi Kullanan Hastalar";
        $query1 = "SELECT COUNT(h.id) FROM #__hastalar AS h WHERE h.pasif='0' AND h.bez=1";
        $query2 = "SELECT h.*, m.mahalle, ilc.ilce FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce WHERE h.pasif='0' AND h.bez=1 ".$orderingfilter;
        break;
        }
        
        $dbase->setQuery($query1);
        $total = $dbase->loadResult();
        
        $pageNav = new pageNav($total, $limitstart, $limit);
        
        $dbase->setQuery($query2, $limitstart, $limit);
        $rows = $dbase->loadObjectList();
        
        
        foreach ($rows as $row) {
            $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik='".$row->tckimlik."' ORDER BY izlemtarihi DESC LIMIT 1");
            $row->sonizlem = $dbase->loadResult() ? $dbase->loadResult() : 'Yok';
            
            $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
            $row->toplamizlem = $dbase->loadResult();
        }
        
        StatsHTML::specialGetir($rows, $title, $secim, $pageNav, $ordering);

}

function Izlenmeyenler($secim, $ordering, $limitstart, $limit) {
    global $dbase;
    
    switch($secim) {
        default:
        case '1':
        $tarih = date('d.m.Y', strtotime('-1 month'));
        break;
        
        case '2':
        $tarih = date('d.m.Y', strtotime('-2 month'));
        break;
        
        case '3':
        $tarih = date('d.m.Y', strtotime('-3 month'));
        break;
        
        case '4':
        $tarih = date('d.m.Y', strtotime('-6 month'));
        break;
        
        case '5':
        $tarih = date('d.m.Y', strtotime('-12 month'));
        break;
        
        case '6':
        $tarih = date('d.m.Y', strtotime('-24 month'));
        break;
        
        case '7':
        $tarih = date('d.m.Y', strtotime('-36 month'));
        break;
        
        case '8':
        $tarih = date('d.m.Y', strtotime('-48 month'));
        break;
        
        case '9':
        $tarih = date('d.m.Y', strtotime('-60 month'));
        break;
        
                
    }
    
    $tarihgetir = tarihCevir($tarih);
   
    /*
    * Son X sürede izlem girilen aktif hastaları getirelim
    */
    
    $query = "SELECT h.tckimlik FROM #__izlemler AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . "\n WHERE h.pasif=0 AND i.izlemtarihi >= '".$tarihgetir."' "
    . "\n GROUP BY i.hastatckimlik "
    . "\n ORDER BY i.izlemtarihi DESC"
    ;
    $dbase->setQuery($query);
    
    
    $lists = $dbase->loadResultArray();
    
    $lists = implode(',', $lists);
    /*
    * Son x sürede izlem girilen hastaları aktif tüm hastalardan çıkaralım
    */
    $dbase->setQuery("SELECT COUNT(*) FROM #__hastalar WHERE pasif=0 AND tckimlik NOT IN (".$lists.")");
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC, h.mahalle ASC, h.kayityili ASC, h.kayitay ASC";
     }
    
    $dbase->setQuery("SELECT h.*, m.mahalle, i.ilce FROM #__hastalar AS h"
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "\n WHERE h.pasif=0 AND h.tckimlik NOT IN (".$lists.")"
    . "\n GROUP BY h.tckimlik "
    . $orderingfilter, $limitstart, $limit);
    $hastalar = $dbase->loadObjectList();
    
    $i = 0;
    foreach ($hastalar as $hasta) {
        $rows[$i]['id'] = $hasta->id;
        $rows[$i]['isim'] = $hasta->isim." ".$hasta->soyisim;
        $rows[$i]['tckimlik'] = $hasta->tckimlik;
        $rows[$i]['cinsiyet'] = $hasta->cinsiyet;
        $rows[$i]['dogumtarihi'] = $hasta->dogumtarihi;
        $rows[$i]['ilce'] = $hasta->ilce;
        $rows[$i]['mahalle'] = $hasta->mahalle;
        $rows[$i]['kayityili'] = $hasta->kayityili;
        $rows[$i]['kayitay'] = $hasta->kayitay; 
        $rows[$i]['cinsiyet'] = $hasta->cinsiyet;
        $rows[$i]['gecici'] = $hasta->gecici;
        $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE hastatckimlik=".$hasta->tckimlik);
        $rows[$i]['izlemsayisi'] = $dbase->loadResult();
        $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$hasta->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
        $rows[$i]['sonizlem'] = $dbase->loadResult();
        
        $i++;
    }
    
    /*
    * Seçim aralığı için listeleme yapalım
    */
    $slist[] = mosHTML::makeOption('1', '1 Ay'); 
    $slist[] = mosHTML::makeOption('2', '2 Ay');
    $slist[] = mosHTML::makeOption('3', '3 Ay');
    $slist[] = mosHTML::makeOption('4', '6 Ay');
    $slist[] = mosHTML::makeOption('5', '1 Yıl');
    $slist[] = mosHTML::makeOption('6', '2 Yıl');
    $slist[] = mosHTML::makeOption('7', '3 Yıl');
    $slist[] = mosHTML::makeOption('8', '4 Yıl'); 
    $slist[] = mosHTML::makeOption('9', '5 Yıl'); 
    
    $secimlist = mosHTML::selectList($slist, 'secim', '', 'value', 'text', $secim);
    
    StatsHTML::Izlenmeyenler($rows, $secimlist, $secim, $ordering, $pageNav);
    
}

function hastalikStats() {
    global $dbase;
    
    //hastalıkları alalım
    $dbase->setQuery("SELECT * FROM #__hastalikcat ORDER BY id ASC");
    $hcats = $dbase->loadObjectList();
    
    $hastaliklar = array();
    foreach ($hcats as $hcat) {
        
        $hastaliklar[$hcat->id]['id'] = $hcat->id;
        $hastaliklar[$hcat->id]['name'] = $hcat->name;
        
        $dbase->setQuery("SELECT id, icd, hastalikadi FROM #__hastaliklar WHERE cat='".$hcat->id."' ORDER BY hastalikadi ASC");
        $hastaliklar[$hcat->id]['hast'] = $dbase->loadObjectList();
    }
    
    $dbase->setQuery("SELECT hastaliklar FROM #__hastalar WHERE pasif=0 AND hastaliklar>0");
    $liste = $dbase->loadResultArray();

    
    $data = array();
    foreach ($liste as $li) {
        if ($li) {
          $data1 = explode(',', $li);
          
          $data = array_merge($data1, $data); 
        }
    }

    $veri = array_count_values($data);

    
    //toplam hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0'");
    $totalh = $dbase->loadResult();

StatsHTML::hastalikStats($hastaliklar, $totalh, $veri);    
}

function specialStats() {
    global $dbase;
    
    $s = array();
    //ngli hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND ng=1");
    $s['ng'] = $dbase->loadResult();
    //pegli hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND peg=1");
    $s['peg'] = $dbase->loadResult();
    //portlu hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND port=1");
    $s['port'] = $dbase->loadResult();
    //oksijen bağımlı hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND o2bagimli=1");
    $s['o2bagimli'] = $dbase->loadResult();
    //ventilatör bağımlı hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND ventilator=1");
    $s['ventilator'] = $dbase->loadResult();
    //geçici kayıtlı hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND gecici=1");
    $s['gecici'] = $dbase->loadResult();
     //kolostomili hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND kolostomi=1");
    $s['kolostomi'] = $dbase->loadResult();
    //sonda takılı hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND sonda=1");
    $s['sonda'] = $dbase->loadResult();
    //bez kullanan hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND bez=1");
    $s['bez'] = $dbase->loadResult();
    //mama kullanan hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND mama=1");
    $s['mama'] = $dbase->loadResult();
    //yatağı olan hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND yatak=1");
    $s['yatak'] = $dbase->loadResult();
    //toplam hasta sayısı
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0'");
    $s['total'] = $dbase->loadResult();
    
    
StatsHTML::specialStats($s);
}

function temelStats($baslangictarih, $bitistarih, $ordering) {
    global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "i.izlemtarihi>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "i.izlemtarihi<='".$cbitistarih."'";
    }
    
    $where[] = 'i.yapildimi=1';
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC";
     }
    
    $query = "SELECT i.hastatckimlik, h.isim, h.soyisim, h.cinsiyet, COUNT(i.id) AS izlemsayisi FROM #__izlemler AS i "
    . "LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . "LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . " GROUP BY h.tckimlik "
    //. $orderingfilter
    ;
    $dbase->setQuery($query);
    
    $bows = $dbase->loadObjectList();
    
        
      $toplamizlem = 0;
      $toplamhasta = 0;
      
      foreach ($bows as $bow) {
         $toplamizlem = $toplamizlem + $bow->izlemsayisi;
         ++$toplamhasta;
      }
      
    $total = count($bows);
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    
    $query = "SELECT i.hastatckimlik, h.id, h.isim, h.soyisim, h.cinsiyet, h.pasif, m.mahalle, ilc.ilce, h.kayityili, h.kayitay, h.bagimlilik, COUNT(i.id) AS izlemsayisi FROM #__izlemler AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . " GROUP BY h.tckimlik "
    . $orderingfilter 
    ;
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
    $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE hastatckimlik=".$row->hastatckimlik);
    $row->toplamizlem = $dbase->loadResult();
    
    $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->hastatckimlik. " ORDER BY izlemtarihi DESC LIMIT 1");
    $row->sonizlem = $dbase->loadResult();
    }
    
    StatsHTML::temelStats($rows, $toplamizlem, $toplamhasta, $pageNav, $baslangictarih, $bitistarih, $ordering);
}

function hMahalle($secim) {
    global $dbase;
    
    $where = '';
    if ($secim) {
    $where = " AND h.ilce=".$secim;
    }
    
    $dbase->setQuery("SELECT COUNT(h.id) as sayi, m.*, i.ilce FROM #__hastalar AS h "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS i ON i.id=m.ilceid "
    . "\n WHERE h.pasif='0' ".$where
    . "\n GROUP BY m.id "
    . "\n ORDER BY i.id ASC, m.mahalle ASC" );
    
    $rows = $dbase->loadObjectList();
    
    $total = 0;
    foreach($rows as $row) {
        $total = $total + $row->sayi;
    }
    
    $dbase->setQuery("SELECT * FROM #__ilce");
    $obs = $dbase->loadObjectList();
    
    $ilcelist = array();
    $ilcelist[] = mosHTML::makeOption('', 'Tüm İlçeler');
    foreach ($obs as $ob) {
    $ilcelist[] = mosHTML::makeOption($ob->id, $ob->ilce);
    }
    
    $ilceler = mosHTML::selectList($ilcelist, 'secim', '', 'value', 'text', $secim);

    StatsHTML::hMahalle($rows, $total, $ilceler);
}

function hKayityili() {
    global $dbase;
    
    $dbase->setQuery("SELECT COUNT(id) as sayi, kayityili FROM #__hastalar WHERE pasif='0' AND cinsiyet='E' GROUP BY kayityili ORDER BY kayityili ASC");
    $rows['erkek'] = $dbase->loadObjectList();
    
    $dbase->setQuery("SELECT COUNT(id) as sayi, kayityili FROM #__hastalar WHERE pasif='0' AND cinsiyet='K' GROUP BY kayityili ORDER BY kayityili ASC");
    $rows['kadin'] = $dbase->loadObjectList();
    
    StatsHTML::hKayityili($rows);
    
}

function hKayitayi() {
    global $dbase;
    
    $dbase->setQuery("SELECT COUNT(id) as sayi, kayitay FROM #__hastalar WHERE pasif='0' AND cinsiyet='E' GROUP BY kayitay ORDER BY kayitay ASC");
    $rows['erkek'] = $dbase->loadObjectList();
    
    $dbase->setQuery("SELECT COUNT(id) as sayi, kayitay FROM #__hastalar WHERE pasif='0' AND cinsiyet='K' GROUP BY kayitay ORDER BY kayitay ASC");
    $rows['kadin'] = $dbase->loadObjectList();
    
    StatsHTML::hKayitayi($rows);
    
}
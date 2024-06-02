<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

$id = intval(getParam($_REQUEST, 'id'));
$today = date('d.m.Y');
$first = date("01.m.Y", strtotime($today));
$last = date("t.m.Y", strtotime($today));

$baslangictarih = getParam($_REQUEST, 'baslangictarih', $first);
$bitistarih = getParam($_REQUEST, 'bitistarih', $last);

$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$secim = getParam($_REQUEST, 'secim');

$ordering = getParam($_REQUEST, 'ordering');

$ilce = intval(getParam($_REQUEST, 'ilce'));
$mahalle = intval(getParam($_REQUEST, 'mahalle'));
$sokak = intval(getParam($_REQUEST, 'sokak'));
$kapino = intval(getParam($_REQUEST, 'kapino'));

include(dirname(__FILE__). '/html.php');

switch($task) {
    default:
    case 'temel':
    temelStats($baslangictarih, $bitistarih, $ordering);
    break;
    
    case 'hmahalle':
    hMahalle();
    break;
    
    case 'kayityili':
    hKayityili();
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
    specialGetir($secim);
    break;
    
    case 'dogumgunu':
    dogumGunuGetir();
    break;
    
    case 'izlenmeyen':
    Izlenmeyenler($secim, $ordering, $limitstart, $limit);
    break;
    
    case 'adres':
    adresHastaFiltre($ilce, $mahalle, $sokak, $kapino, $ordering);
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
    hastalikGirilmemiş();
    break;
}

function hastalikGirilmemiş() {
    global $dbase;
    
$hastaliklar = array(
'parkinson' => 'Parkinson',
'alzheimer' => 'Alzheimer',
'demans' => 'Demans',
'senilite' => 'Senilite(Yaşlılık)',
'epilepsi' => 'Epilepsi',
'hidrosefali' => 'Hidrosefali',
'ensefalopati' => 'Ensefalopati',
'svo' => 'SVO',
'sspe' => 'SSPE',
'miyelit' => 'Miyelit',
'als' => 'ALS',
'motornoron' => 'Motor Nöron Hastalığı',
'multiplskleroz' => 'Multipl Skleroz',
'noropatikagri' => 'Nöropatik Ağrı',
'kuadripleji' => 'Kuadripleji',
'hemipleji' => 'Hemipleji',
'parapleji' => 'Parapleji',
'tetrapleji' => 'Tetrapleji',
'mentalretardasyon' => 'Mental Retardasyon',
'poliosekel' => 'Polio Sekeli',
'depresyon' => 'Depresyon',
'psikoz' => 'Psikoz',
'bipolar' => 'Bipolar Bozukluk',
'sizofren' => 'Şizofreni',
'serebralpalsi' => 'Serebral Palsi',
'kasatrofisi' => 'Kas Atrofisi',
'ataksi' => 'Ataksi',
'muskulerdistrofi' => 'Müsküler Distrofi',
'spastikveflask' => 'Spastik ve Flask Tip Özürlü',
'aritmi' => 'Aritmi',
'ht' => 'Hipertansiyon',
'kalpkapak' => 'Kalp Kapak Hastalıkları',
'kalpyet' => 'Kalp Yetmezliği',
'koronerarter' => 'Koroner Arter Hastalığı',
'venozdol' => 'Venöz Dolaşım Bozukluğu',
'dm' => 'Diyabet',
'hiperlipidemi' => 'Hiperlipidemi',
'hipertiroidi' => 'Hipertiroidi',
'hipotiroidi' => 'Hipotiroidi',
'obezite' => 'Obezite',
'diyaliz' => 'Diyaliz Hastası',
'bobrekyet' => 'Böbrek Yetmezliği',
'siroz' => 'Karaciğer Sirozu',
'anemi' => 'Anemi',
'itp' => 'ITP',
'ciltkanser' => 'Cilt Kanseri',
'memekanser' => 'Meme Kanseri',
'farinkskanser' => 'Farinks Kanseri',
'trakeakanser' => 'Trakea Kanseri',
'akckanseri' => 'Akciğer Kanseri',
'ozofaguskanser' => 'Özofagus Kanseri',
'midekanser' => 'Mide Kanseri',
'barsakkanser' => 'Bağırsak Kanseri',
'karacigerkanser' => 'Karaciğer Kanseri',
'pankreaskanser' => 'Pankreas Kanseri',
'safrakanser' => 'Safra Kesesi Kanseri',
'prostatkanser' => 'Prostat Kanseri',
'tiroidkanser' => 'Tiroid Kanseri',
'hipofizkanser' => 'Hipofiz Kanseri',
'bobrekkanser' => 'Böbrek Kanseri',
'overkanser' => 'Over Kanseri',
'uteruskanser' => 'Uterus Kanseri',
'kemikkanser' => 'Kemik Kanseri',
'beyinkanser' => 'Beyin Kanseri',
'multiplmiyelom' => 'Multipl Miyelom',
'koah' => 'KOAH',
'astim' => 'Astım',
'tuberkuloz' => 'Tüberküloz',
'kistikfibroz' => 'Kistik Fibrozis',
'dekubitulseri' => 'Dekübitus Ülseri',
'diskopati' => 'Diskopati',
'extremiteamp' => 'Ekstremitelerde Ampütasyon',
'kalcafraktur' => 'Kalça Fraktürü',
'operekirik' => 'Opere Edilmiş veya mobilizasyon kısıtlayıcı kırıklar',
'osteoporoz' => 'Osteoporoz',
'omurgafraktur' => 'Omurga Fraktürü',
'ebullosa' => 'Epidermolizis Büllosa',
'zona' => 'Zona',
'fil' => 'Fil Hastalığı',
'sle' => 'Sistemik Lupus Eritomatozus',
'bph' => 'Benign Prostat Hipertrofisi'
);

$where = array();
foreach ($hastaliklar as $v=>$k) {
    $where[] = $v."=0";
}

$where[] = "pasif=0 ";
$where[] = "bagimlilik=0 OR bagimlilik=NULL ";

$query = "SELECT h.*, m.mahalle FROM #__hastalar AS h "
. "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
. ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
. "\n ORDER BY isim ASC"
;

$dbase->setQuery($query);

$rows = $dbase->loadObjectList();

StatsHTML::hastalikGirilmemis($rows); 
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
    
    $dbase->setQuery("SELECT * FROM #__users WHERE activated=1 ORDER BY name ASC");
    $personeller = $dbase->loadObjectList();
    
    $data = array();
    
    $i = 0;
    foreach ($personeller as $personel) {
    
    $data[$i]['personeladi'] = $personel->name;
        
    $query = "SELECT COUNT(i.id) AS izlemsayisi FROM #__izlemler AS i "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . "\n AND i.izlemiyapan IN (".$personel->id.")"
    . "\n ORDER BY i.izlemtarihi ASC ";
    
    $dbase->setQuery($query);
    
    $data[$i]['toplam'] = $dbase->loadResult();
    $i++;
    }
    
    
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
    
    $dbase->setQuery("SELECT * FROM #__islem");
    $islemler = $dbase->loadObjectList();
    
    $data = array();
    
    $i = 0;
    foreach ($islemler as $islem) {
    
    $data[$i]['islemadi'] = $islem->islemadi;
        
    $query = "SELECT COUNT(i.id) AS izlemsayisi FROM #__izlemler AS i "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . "\n AND i.yapilan IN (".$islem->id.")"
    . "\n ORDER BY i.izlemtarihi ASC ";
    
    $dbase->setQuery($query);
    
    $data[$i]['toplam'] = $dbase->loadResult();
    $i++;
    }
    
    StatsHTML::islemGetir($data, $baslangictarih, $bitistarih);
}

function adresHastaFiltre($ilce, $mahalle, $sokak, $kapino, $ordering) {
    global $dbase, $limitstart, $limit;
    
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
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim, h.soyisim, h.ilce ASC, h.mahalle ASC, h.sokak ASC, h.kapino ASC";
     } 
    
    $where[] = "h.pasif='0' ";  // aktif hastalar
    
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
    $dilce[] = mosHTML::makeOption($ai->id, $ai->ilce."(".$ai->hastasayisi.")");
    }
    
    if ($ilce) {
        $dbase->setQuery("SELECT m.*, COUNT(h.id) as sayi FROM #__mahalle AS m "
        . "\n LEFT JOIN #__hastalar AS h ON h.mahalle=m.id "
        . "\n WHERE h.pasif=0 AND m.ilceid=".$ilce
        . "\n GROUP BY m.id "
        );
        $mahalleler = $dbase->loadObjectList();
        
        foreach ($mahalleler as $m) {
        $dmahalle[] = mosHTML::makeOption($m->id, $m->mahalle."(".$m->sayi.")");
        }
    }
    
    if ($mahalle) {
        $dbase->setQuery("SELECT s.*, COUNT(h.id) as sayi FROM #__sokak AS s "
        . "\n LEFT JOIN #__hastalar AS h ON h.sokak=s.id "
        . "\n WHERE h.pasif=0 AND s.mahalleid=".$mahalle
        . "\n GROUP BY s.id "
        );
        $sokaklar = $dbase->loadObjectList();
        
        foreach ($sokaklar as $s) {
        $dsokak[] = mosHTML::makeOption($s->id, $s->sokakadi."(".$s->sayi.")");
        }
    }


    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce"', 'value', 'text', $ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle"', 'value', 'text', $mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak"', 'value', 'text', $sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino"', 'value', 'text', $kapino);

    StatsHTML::adresHastaFiltre($rows, $lists, $ilce, $mahalle, $sokak, $kapino, $ordering, $pageNav); 
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
        $data[] = '"'.$row->id.'":"'.$row->ilce.'('.$row->hastasayisi.')"';
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
    
    $data = array();
    foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->mahalle.'('.$row->hastasayisi.')"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
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
    
     foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->sokakadi.'('.$row->hastasayisi.')"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
    echo $html;
}

function getirKapino($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__kapino WHERE sokakid='".$id."' ORDER BY kapino ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->kapino.'"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
    echo $html;
}

function specialGetir($secim) {
       global $dbase;
       
    switch($secim) {
        case 'ng':
        $title = "Nazogastrik Takılı Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.ng=1 ORDER BY h.id ASC");
        break;
        
        case 'peg':
        $title = "PEG Takılı Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.peg=1 ORDER BY h.id ASC"); 
        break;
        
        case 'port':
        $title = "PORT Takılı Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.port=1 ORDER BY h.id ASC");
        break;
        
        case 'o2bagimli':
        $title = "Oksijen Bağımlı Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.o2bagimli=1 ORDER BY h.id ASC"); 
        break;
        
        case 'ventilator':
        $title = "Ventilatör Takılı Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.ventilator=1 ORDER BY h.id ASC"); 
        break;
        
        default:
        case 'gecici':
        $title = "Geçici Kayıtlı Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.gecici=1 ORDER BY h.id ASC");
        break;
        
        case 'kolostomi':
        $title = "Kolostomili Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.kolostomi=1 ORDER BY h.id ASC");
        break;
        
        case 'sonda':
        $title = "Sonda Takılı Hastalar";
        $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif='0' AND h.sonda=1 ORDER BY h.sondatarihi ASC");
        break;
        }
        
        $rows = $dbase->loadObjectList();
        
        StatsHTML::specialGetir($rows, $title, $secim);

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
    
    $dbase->setQuery("SELECT h.*, m.mahalle FROM #__hastalar AS h"
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n WHERE h.pasif=0 AND h.tckimlik NOT IN (".$lists.")"
    . "\n GROUP BY h.tckimlik "
    . $orderingfilter, $limitstart, $limit);
    $hastalar = $dbase->loadObjectList();
    
    $i = 0;
    foreach ($hastalar as $hasta) {
        $rows[$i]['id'] = $hasta->id;
        $rows[$i]['isim'] = $hasta->isim." ".$hasta->soyisim;
        $rows[$i]['tckimlik'] = $hasta->tckimlik;
        $rows[$i]['dogumtarihi'] = $hasta->dogumtarihi;
        $rows[$i]['mahalle'] = $hasta->mahalle;
        $rows[$i]['kayityili'] = $hasta->kayityili;
        $rows[$i]['kayitay'] = $hasta->kayitay; 
        $rows[$i]['cinsiyet'] = $hasta->cinsiyet;
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
    $slist[] = mosHTML::makeOption('5', '12 Ay');
    
    $secimlist = mosHTML::selectList($slist, 'secim', '', 'value', 'text', $secim);
    
    StatsHTML::Izlenmeyenler($rows, $secimlist, $secim, $ordering, $pageNav);
    
}



function hastalikStats() {
    global $dbase;
    
    //toplam aktif hasta sayısı alalım
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0'");
    $thasta = $dbase->loadResult();
    
    include(ABSPATH. '/site/modules/hastalar/hastaliklar.php');

foreach ($tab as $s=>$i) {
foreach ($i as $k=>$v) {
$dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE ".$k."=1");
$total[$k] = $dbase->loadResult();
}
}
StatsHTML::hastalikStats($tab, $total, $thasta);    
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
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND sonda=1");
    $s['sonda'] = $dbase->loadResult();
    //sonda takılı hasta sayısı
    
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
    
    
    $query = "SELECT i.hastatckimlik, h.id, h.isim, h.soyisim, h.cinsiyet, m.mahalle, h.kayityili, h.kayitay, COUNT(i.id) AS izlemsayisi FROM #__izlemler AS i "
    . "LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . "LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . "GROUP BY h.tckimlik "
    . $orderingfilter 
    //. "ORDER BY h.id, h.isim DESC " 
    ;
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    
        
    StatsHTML::temelStats($rows, $toplamizlem, $toplamhasta, $pageNav, $baslangictarih, $bitistarih, $ordering);
}

function hMahalle() {
    global $dbase;
    
    $dbase->setQuery("SELECT COUNT(h.id) as sayi, m.*, i.ilce FROM #__hastalar AS h "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS i ON i.id=m.ilceid "
    . "\n WHERE h.pasif='0' "
    . "\n GROUP BY m.id "
    . "\n ORDER BY i.ilce ASC, m.mahalle ASC" );
    
    $rows = $dbase->loadObjectList();
    
    $total = 0;
    foreach($rows as $row) {
        $total = $total + $row->sayi;
    }

    StatsHTML::hMahalle($rows, $total);
}

function hKayityili() {
    global $dbase;
    
    $dbase->setQuery("SELECT COUNT(id) as sayi, kayityili FROM #__hastalar WHERE pasif='0' AND cinsiyet='E' GROUP BY kayityili ORDER BY kayityili ASC");
    $rows['erkek'] = $dbase->loadObjectList();
    
    $dbase->setQuery("SELECT COUNT(id) as sayi, kayityili FROM #__hastalar WHERE pasif='0' AND cinsiyet='K' GROUP BY kayityili ORDER BY kayityili ASC");
    $rows['kadin'] = $dbase->loadObjectList();
    
    StatsHTML::hKayityili($rows);
    
}


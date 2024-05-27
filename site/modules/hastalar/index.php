<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');

$id = intval(getParam($_REQUEST, 'id')); 
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$tc = getParam($_REQUEST, 'tc');

$search = strval(getParam($_REQUEST, 'search'));
$ilce = intval(getParam($_REQUEST, 'ilce'));
$mahalle = intval(getParam($_REQUEST, 'mahalle'));
$sokak = intval(getParam($_REQUEST, 'sokak'));
$kapino = intval(getParam($_REQUEST, 'kapino'));
$kayityili = intval(getParam($_REQUEST, 'kayityili'));
$kayitay = getParam($_REQUEST, 'kayitay');
$cinsiyet = getParam($_REQUEST, 'cinsiyet');
$bagimlilik = getParam($_REQUEST, 'bagimlilik');

$ordering = getParam($_REQUEST, 'ordering');

switch($task) {
    default:
    case 'list':
    getHastaList($search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ordering);
    break;
    
    case 'show':
    hastaGoster($id);
    break;
    
    case 'edit':
    editHasta($id);
    break;
    
    case 'new':
    editHasta(0);
    break;
    
    case 'save':
    saveHasta();
    break;
    
    case 'cancel':
    cancelHasta();
    break;
    
    case 'control':
    controlTC($tc);
    break;
    
    /*
    case 'ilce':
    hastaIlce($id);
    break;
    */
    case 'mahalle':
    hastaMahalle($id);
    break;
    
    case 'sokak':
    hastaSokak($id);
    break;
    
    case 'kapino':
    hastaKapino($id);
    break;
}
/*
function hastaIlce($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__ilce ORDER BY id ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $data = array();
    foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->ilce.'"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
    echo $html;

}
*/
function hastaMahalle($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__mahalle WHERE ilceid='".$id."' ORDER BY mahalle ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $data = array();
    foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->mahalle.'"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
    echo $html;
}

function hastaSokak($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__sokak WHERE mahalleid='".$id."' ORDER BY sokakadi ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
     foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->sokakadi.'"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
    echo $html;
}

function hastaKapino($id) {
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

function controlTC($tc) {
    global $dbase;
    
    $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$tc);
    $dbase->loadObject($row);
    
    if ($row) {
        echo "Bu kimlik numarasında <strong>".$row->isim." ".$row->soyisim."</strong> adında bir hasta kaydı var.";
    } else {
        echo '';
    }   
}


function hastaGoster($id) {
    global $dbase;
    
    $id = intval($id);
    $hasta = new Hasta($dbase);
    $hasta->load($id);
    
    if (!$hasta->id) {
        Redirect('index.php?option=site&bolum=hastalar', 'Böyle bir hasta yok!!!');
    }
    
    $query = "SELECT h.*, COUNT(iz.id) AS toplamizlem, i.ilce AS ilceadi, m.mahalle AS mahalleadi, s.sokakadi AS sokakadi, k.kapino AS kapino "
    . "\n FROM #__hastalar AS h "
    . "\n LEFT JOIN #__izlemler AS iz ON iz.hastatckimlik=h.tckimlik "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__sokak AS s ON s.id=h.sokak "
    . "\n LEFT JOIN #__kapino AS k ON k.id=h.kapino "
    . "\n WHERE h.id=".$hasta->id
    . "\n GROUP BY h.id"
    ;
    $dbase->setQuery($query);
    $dbase->loadObject($row);
    
    HastaList::hastaGoster($row); 
}

function getHastaList($search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ordering) {
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
     
     if ($kayityili) {
         $where[] = "h.kayityili='".$kayityili."'";  
     }
     
     if ($kayitay) {
         $where[] = "h.kayitay='".$kayitay."'";  
     }
     
     if ($cinsiyet) {
         $where[] = "h.cinsiyet='".$cinsiyet."'";  
     }
     
     if ($bagimlilik) {
         $where[] = "h.bagimlilik='".$bagimlilik."'";  
     }
     
     if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC, h.kayityili DESC";
     } 
     
         $where[] = "h.pasif='0'";  // aktif hastalar
   
    
    
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     
     $query = "SELECT h.*, m.mahalle FROM #__hastalar AS h "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY h.id " 
     . $orderingfilter
     ;

    $dbase->setQuery($query, $limitstart, $limit);
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
        $rows[$i]['sonizlemtarihi'] = $dbase->loadResult();
        $i++;
    }
    
    //adres oluştur
     //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $dkapino[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');
    
    foreach ($adres['ilce'] as $ai) {
    $dilce[] = mosHTML::makeOption($ai->id, $ai->ilce);
    }
    
    if ($ilce) {
    $dbase->setQuery("SELECT * FROM #__mahalle WHERE ilceid=".$ilce." ORDER BY mahalle ASC");
    $adres['mahalle'] = $dbase->loadObjectList();
    
    foreach ($adres['mahalle'] as $mah) {
    $dmahalle[] = mosHTML::makeOption($mah->id, $mah->mahalle);
    }
    }
    
    if ($mahalle) {
    $dbase->setQuery("SELECT * FROM #__sokak WHERE mahalleid=".$mahalle." ORDER BY sokakadi ASC");
    $adres['sokak'] = $dbase->loadObjectList();
    
    foreach ($adres['sokak'] as $sk) {
    $dsokak[] = mosHTML::makeOption($sk->id, $sk->sokakadi);
    }
    }
    
    if ($sokak) {
    $dbase->setQuery("SELECT * FROM #__kapino WHERE sokakid=".$sokak." ORDER BY kapino ASC");
    $adres['kapino'] = $dbase->loadObjectList();
    
    foreach ($adres['kapino'] as $kp) {
    $dkapino[] = mosHTML::makeOption($kp->id, $kp->kapino);
    }
    }
    
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce"', 'value', 'text', $ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle"', 'value', 'text', $mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak"', 'value', 'text', $sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino"', 'value', 'text', $kapino);
    
     $start = '2007';
     $end = date('Y');
        
     $lists['kayityili'] = mosHTML::integerSelectList($start, $end, '1', 'kayityili', 'id="kayityili"', $kayityili, 1);
     
      $c = array();
      $c[] = mosHTML::makeOption('', 'Cinsiyet Seçin');
      $c[] = mosHTML::makeOption('E', 'Erkek');
      $c[] = mosHTML::makeOption('K', 'Kadın');
        
      $lists['cinsiyet'] = mosHTML::selectList($c, 'cinsiyet', 'id="cinsiyet"', 'value', 'text', $cinsiyet);
      
      $t = array();
        $t[] = mosHTML::makeOption('', 'Bağımlılık Durumu Seçin' );
        $t[] = mosHTML::makeOption('2', 'Tam Bağımlı Hasta');
        $t[] = mosHTML::makeOption('1', 'Yarı Bağımlı Hasta');
        $t[] = mosHTML::makeOption('3', 'Bağımsız Hasta');
        
        $lists['bagimlilik'] = mosHTML::selectList($t, 'bagimlilik', 'id="bagimlilik"', 'value', 'text', $bagimlilik);
    
   HastaList::getHastaList($rows, $pageNav, $search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ordering, $lists);
}

function saveHasta() {
         global $dbase, $search, $limitstart, $limit;
    
    $row = new Hasta( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $isNew     = !$row->id;
    
    if ($row->pasiftarihi) {
    $row->pasiftarihi = tarihCevir($row->pasiftarihi);
    }
    
    if ($row->sondatarihi) {
    $row->sondatarihi = tarihCevir($row->sondatarihi);
    }
    
    $tarih = explode('.',$row->dogumtarihi);
    $tarih = mktime(0,0,0,$tarih[1],$tarih[0],$tarih[2]);
         
    $row->dogumtarihi = strftime("%Y.%m.%d", $tarih);
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if ($isNew) {
    $link = "index.php?option=site&bolum=izlemler&task=hedit&tc=".$row->tckimlik;
    } else {
    $link = "index.php?option=site&bolum=hastalar&task=show&id=".$row->id;
    }
    
    Redirect($link);
    
}

function editHasta($id) {
    global $dbase, $limit, $limitstart;
    
    $row = new Hasta($dbase);
    $row->load($id);
    
    if ($row->id) {
     $tarih = explode('.',$row->dogumtarihi);
     $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
     $row->dogumtarihi = strftime("%d.%m.%Y", $tarih);   
    }
    
    //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dbase->setQuery("SELECT * FROM #__mahalle ORDER BY mahalle ASC");
    $adres['mahalle'] = $dbase->loadObjectList();
    
    $dbase->setQuery("SELECT * FROM #__sokak ORDER BY sokakadi ASC");
    $adres['sokak'] = $dbase->loadObjectList();
    
    $dbase->setQuery("SELECT * FROM #__kapino ORDER BY kapino ASC");
    $adres['kapino'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $dkapino[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');
    
    foreach ($adres['ilce'] as $ilce) {
    $dilce[] = mosHTML::makeOption($ilce->id, $ilce->ilce);
    }
    
    foreach ($adres['mahalle'] as $mahalle) {
    $dmahalle[] = mosHTML::makeOption($mahalle->id, $mahalle->mahalle);
    }
    
    foreach ($adres['sokak'] as $sokak) {
    $dsokak[] = mosHTML::makeOption($sokak->id, $sokak->sokakadi);
    }
    
    foreach ($adres['kapino'] as $kapino) {
    $dkapino[] = mosHTML::makeOption($kapino->id, $kapino->kapino);
    }
    
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce" required', 'value', 'text', $row->ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle" required disabled="disabled"', 'value', 'text', $row->mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak" required disabled="disabled"', 'value', 'text', $row->sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino" required disabled="disabled"', 'value', 'text', $row->kapino);

    HastaList::editHasta($row, $lists, $limitstart, $limit);
}

function cancelHasta() {
    global $dbase, $limitstart, $limit;
    
    $row = new Hasta( $dbase );
    $row->bind( $_POST );
    
    //$link = "index.php?option=site&bolum=hastalar&task=show&id=".$row->id;
    $link = "index.php?option=site&bolum=hastalar&limit=".$limit."&limitstart=".$limitstart;

    Redirect( $link );
}


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
$pasif = getParam($_REQUEST, 'pasif');

$ordering = getParam($_REQUEST, 'ordering');



switch($task) {
    default:
    case 'list':
    getHastaList($search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $pasif, $ordering);
    break;
    
    case 'savesokak':
    ekleSokak();
    break;
    
    case 'savekapino':
    ekleKapino();
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

function ekleSokak() {
    global $dbase;
    $uid = intval(getParam($_REQUEST, 'uid'));
    
    $row = new Sokak($dbase);
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
        Redirect('index.php?option=admin&bolum=hastalar&task=edit&id='.$uid);
}

function ekleKapino() {
    global $dbase;
    $uid = intval(getParam($_REQUEST, 'uid'));
    $row = new KapiNo( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if ( !$row->check( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    Redirect('index.php?option=admin&bolum=hastalar&task=edit&id='.$uid);
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
        Redirect('index.php?option=admin&bolum=hastalar', 'Böyle bir hasta yok!!!');
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
    
    //hastalıklarını çekelim
    $dbase->setQuery("SELECT hastalikadi FROM #__hastaliklar WHERE id IN (".$row->hastaliklar.")");
    $hastaliklar = $dbase->loadResultArray();
    
    HastaList::hastaGoster($row, $hastaliklar); 
}

function getHastaList($search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $pasif, $ordering) {
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
     
     if ($pasif) {
         $where[] = "h.pasif='".$pasif."'";  
     }
     
     if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC, h.kayityili DESC";
     } 
     
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     
     $query = "SELECT h.*, m.mahalle, i.ilce FROM #__hastalar AS h "
     . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
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
        $rows[$i]['pasif'] = $hasta->pasif;
        $rows[$i]['ilce'] = $hasta->ilce;
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
        
        $pa = array();
      $pa[] = mosHTML::makeOption('', 'Tümü');
      $pa[] = mosHTML::makeOption('1', 'Pasif');
        
      $lists['pasif'] = mosHTML::selectList($pa, 'pasif', 'id="pasif"', 'value', 'text', $pasif);
        
    
   HastaList::getHastaList($rows, $pageNav, $search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ordering, $lists);
}

function saveHasta() {
         global $dbase;
    
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
    
    $row->hastaliklar = implode(',', $row->hastaliklar); 
    
    $tarih = explode('.',$row->dogumtarihi);
    $tarih = mktime(0,0,0,$tarih[1],$tarih[0],$tarih[2]);
         
    $row->dogumtarihi = strftime("%Y.%m.%d", $tarih);
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $link = "index.php?option=admin&bolum=hastalar&task=show&id=".$row->id;
    
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
     
     $row->hastaliklar = explode(',', $row->hastaliklar);   
    }
    
    //hastanın hastalıklarını alalım
    $dbase->setQuery("SELECT * FROM #__hastalikcat");
    
    $hcats = $dbase->loadObjectList();
    
    $hlists = array();
    foreach ($hcats as $hcat) {
        $dbase->setQuery("SELECT * FROM #__hastaliklar WHERE cat='".$hcat->id."'");
        $hlist = $dbase->loadObjectList();
        
        foreach ($hlist as $h) {
            $lists['hastalik'][$hcat->id][] = mosHTML::makeOption($h->id, $h->hastalikadi);
        }
    }
    
    //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $dkapino[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');
    
    foreach ($adres['ilce'] as $ilce) {
    $dilce[] = mosHTML::makeOption($ilce->id, $ilce->ilce);
    }
    
    if ($row->ilce) {
        $dbase->setQuery("SELECT * FROM #__mahalle "
        . "\n WHERE ilceid=".$row->ilce
        . "\n ORDER BY mahalle ASC");
        $adres['mahalle'] = $dbase->loadObjectList();
        
        foreach ($adres['mahalle'] as $m) {
        $dmahalle[] = mosHTML::makeOption($m->id, $m->mahalle);
        }
    }
    
    if ($row->mahalle) {
        $dbase->setQuery("SELECT * FROM #__sokak "
        . "\n WHERE mahalleid=".$row->mahalle
        . "\n ORDER BY sokakadi ASC "
        );
        $adres['sokak'] = $dbase->loadObjectList();
        
        foreach ($adres['sokak'] as $s) {
        $dsokak[] = mosHTML::makeOption($s->id, $s->sokakadi);
        }
    }
    
    if ($row->sokak) {
        $dbase->setQuery("SELECT * FROM #__kapino "
        . "\n WHERE sokakid=".$row->sokak
        . "\n ORDER BY kapino ASC "
        );
        $adres['kapino'] = $dbase->loadObjectList();
        
        foreach ($adres['kapino'] as $k) {
        $dkapino[] = mosHTML::makeOption($k->id, $k->kapino);
        }
    }
    
    
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce" required', 'value', 'text', $row->ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle" required', 'value', 'text', $row->mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak"', 'value', 'text', $row->sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino"', 'value', 'text', $row->kapino);
    
    $lists['ilceid'] = mosHTML::selectList($dilce, 'ilceid', 'id="ilce" required', 'value', 'text', $row->ilce);
    $lists['mahalleid'] = mosHTML::selectList($dmahalle, 'mahalleid', 'id="mahalle" required', 'value', 'text', $row->mahalle);
    $lists['sokakid'] = mosHTML::selectList($dsokak, 'sokakid', 'id="sokak" required', 'value', 'text', $row->sokak);
    
    HastaList::editHasta($row, $lists, $limitstart, $limit, $hcats);
}

function cancelHasta() {
    global $dbase, $limitstart, $limit;
    
    $row = new Hasta( $dbase );
    $row->bind( $_POST );
    
    //$link = "index.php?option=site&bolum=hastalar&task=show&id=".$row->id;
    $link = "index.php?option=admin&bolum=hastalar&limit=".$limit."&limitstart=".$limitstart;

    Redirect( $link );
}


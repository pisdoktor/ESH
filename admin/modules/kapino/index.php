<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );
 
include(dirname(__FILE__). '/html.php');  

$id = intval(getParam($_REQUEST, 'id'));

$search = strval(getParam($_REQUEST, 'search'));
$ilce = intval(getParam($_REQUEST, 'ilce'));
$mahalle = intval(getParam($_REQUEST, 'mahalle'));
$sokak = intval(getParam($_REQUEST, 'sokak'));



$limit = intval(getParam($_REQUEST, 'limit', 20));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$ordering = getParam($_REQUEST, 'ordering');

switch($task) {
    
	default:
    case 'list':
	getKapinoList($search, $ilce, $mahalle, $sokak, $ordering);
	break;
	
	case 'add':
	editKapino(0);
	break;
	
	case 'editx':
	editKapino($id);
	break;
	
	case 'save':
	saveKapino();
	break;
	
	case 'delete':
	delKapino($id);
	break;
    
    case 'mahalle':
    getMahalle($id);
    break;
    
    case 'sokak':
    getSokak($id);
    break;
}

function getSokak($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__sokak WHERE mahalleid='".$id."' ORDER BY sokakadi ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';                                         
    $html .= '<option value="0">Bir Sokak Seçin</option>';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->sokakadi."</option>";
    }
    echo $html;
}

function getMahalle($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__mahalle WHERE ilceid='".$id."' ORDER BY mahalle ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';
    $html .= '<option value="0">Bir Mahalle Seçin</option>';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->mahalle."</option>";
    }
    echo $html;
}

function getKapinoList($search, $ilce, $mahalle, $sokak, $ordering) {
     global $dbase, $limit, $limitstart;
     
     $where = array();
     if ($search) {
         $search = mosStripslashes($search);
         $where[] = "k.kapino LIKE '" . $dbase->getEscaped( trim( $search ) ) . "'";
     }
     
     if ($ilce) {
         $where[] = "ic.id='".$ilce."'";
     }
     
     if ($mahalle) {
         $where[] = "m.id='".$mahalle."'";
     }
     
     if ($sokak) {
         $where[] = "s.id='".$sokak."'";
     }
         
     if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY ic.ilce ASC, m.mahalle ASC, s.sokakadi ASC, k.kapino ASC";
     }
     

     $dbase->setQuery("SELECT COUNT(k.id) FROM #__kapino AS k "
     . "\n LEFT JOIN #__sokak AS s ON s.id=k.sokakid "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=s.mahalleid "
     . "\n LEFT JOIN #__ilce AS ic ON ic.id=m.ilceid "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" ) 
     . $orderingfilter
     );
     $total = $dbase->loadResult();
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     $query = "SELECT k.*, s.sokakadi AS sokakadi, m.mahalle AS mahalleadi, ic.ilce AS ilceadi FROM #__kapino AS k "
     . "\n LEFT JOIN #__sokak AS s ON s.id=k.sokakid "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=s.mahalleid "
     . "\n LEFT JOIN #__ilce AS ic ON ic.id=m.ilceid "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" ) 
     . $orderingfilter
     ;
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    

    
    //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    foreach ($adres['ilce'] as $ilc) {
    $dilce[] = mosHTML::makeOption($ilc->id, $ilc->ilce);
    }
    
    if ($ilce) {
    $dbase->setQuery("SELECT * FROM #__mahalle "
    . ($ilce ? 'WHERE ilceid='.$ilce:'')
    . "\n ORDER BY mahalle ASC");
    $adres['mahalle'] = $dbase->loadObjectList();
    
    $dmahalle[] = mosHTML::makeOption("", "Bir Mahalle Seçin"); 
    foreach ($adres['mahalle'] as $mahall) {
    $dmahalle[] = mosHTML::makeOption($mahall->id, $mahall->mahalle);
    }
    
    }
    
    if ($mahalle) {
    $dbase->setQuery("SELECT * FROM #__sokak "
    . ($mahalle ? 'WHERE mahalleid='.$mahalle:'')
    . "\n ORDER BY sokakadi ASC");
    $adres['sokak'] = $dbase->loadObjectList();
    
    $dsokak[] = mosHTML::makeOption("", "Bir Sokak Seçin");
    foreach ($adres['sokak'] as $soka) {
    $dsokak[] = mosHTML::makeOption($soka->id, $soka->sokakadi);
    }
    }
    
    if (!$mahalle || !$sokak) {
    $dmahalle[] = mosHTML::makeOption("", "Bir Mahalle Seçin");
    $dsokak[] = mosHTML::makeOption("", "Bir Sokak Seçin");
    }
    
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce"', 'value', 'text', $ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle"', 'value', 'text', $mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak"', 'value', 'text', $sokak);
   
    
    KapinoHTML::getKapinoList($rows, $search, $ilce, $mahalle, $sokak, $ordering, $lists, $pageNav);
}

function delKapino($id) {
	global $dbase;

	$dbase->setQuery("DELETE FROM #__kapino WHERE id=".$id);
    $dbase->query();
	
	Redirect( 'index.php?option=admin&bolum=kapino', 'Seçilen kapı no silindi' );
}

function saveKapino() {
	 global $dbase;
	
	$row = new KapiNo( $dbase );
	
	if ( !$row->bind( $_POST ) ) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
    
    /*
    $dbase->setQuery("SELECT id FROM #__kapino WHERE ilceid=".$row->ilceid." AND mahalleid=".$row->mahalleid." AND sokakid=".$row->sokakid." AND kapino=".$row->kapino);
    $var = $dbase->loadResult();
    
    if ($var) {
        ErrorAlert('O kapı numarası zaten eklenmiş');
    }
    */
    if ( !$row->check( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect('index.php?option=admin&bolum=kapino', 'Kapı No kaydedildi');
	
}


function editKapino($cid) {
	global $dbase;
	
	$row = new KapiNo($dbase);
	$row->load($cid);
    
    if ($row->id) {
        //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    foreach ($adres['ilce'] as $ilc) {
    $dilce[] = mosHTML::makeOption($ilc->id, $ilc->ilce);
    }
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilceid', 'id="ilce"', 'value', 'text', $row->ilceid); 
    
   
    $dbase->setQuery("SELECT * FROM #__mahalle "
    . ($row->ilceid ? "WHERE ilceid=".$row->ilceid:"")
    . " ORDER BY mahalle ASC");
    $adres['mahalle'] = $dbase->loadObjectList();
    
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    foreach ($adres['mahalle'] as $mahall) {
    $dmahalle[] = mosHTML::makeOption($mahall->id, $mahall->mahalle);
    }
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalleid', 'id="mahalle"', 'value', 'text', $row->mahalleid);
    
    $dbase->setQuery("SELECT * FROM #__sokak "
    . ($row->mahalleid ? "WHERE mahalleid=".$row->mahalleid:"")
    . " ORDER BY sokakadi ASC");
    $adres['sokak'] = $dbase->loadObjectList();

    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');

    foreach ($adres['sokak'] as $soka) {
    $dsokak[] = mosHTML::makeOption($soka->id, $soka->sokakadi);
    }
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokakid', 'id="sokak"', 'value', 'text', $row->sokakid);
    
    } else {
        //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    foreach ($adres['ilce'] as $ilc) {
    $dilce[] = mosHTML::makeOption($ilc->id, $ilc->ilce);
    }
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilceid', 'id="ilce"', 'value', 'text', $row->ilceid);
    
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalleid', 'id="mahalle"', 'value', 'text');
    
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokakid', 'id="sokak"', 'value', 'text');
    }
    
    
    
    
	KapinoHTML::editKapino($row, $lists);
}
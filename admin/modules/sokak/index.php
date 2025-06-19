<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

$cid = getParam($_REQUEST, 'cid');
$id = intval(getParam($_REQUEST, 'id'));

$search = strval(getParam($_REQUEST, 'search'));
$ilce = intval(getParam($_REQUEST, 'ilce'));
$mahalle = intval(getParam($_REQUEST, 'mahalle')); 

$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$ordering = getParam($_REQUEST, 'ordering'); 

include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
	getSokakList($search, $ilce, $mahalle, $ordering);
	break;
	
	case 'add':
	editSokak(0);
	break;
	
	case 'edit':
	editSokak(intval(($cid[0])));
	break;
	
	case 'editx':
	editSokak($id);
	break;
	
	case 'save':
	saveSokak();
	break;
	
	case 'cancel':
	cancelSokak();
	break;
	
	case 'delete':
	delSokak($cid);
	break;
    
    case 'mahalle':
    getMahalle($id);
    break;
}
function getMahalle($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__mahalle WHERE ilceid='".$id."' ORDER BY mahalle ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';
    $html .= "<option value=''>Bir Mahalle Seçin</option>";  
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->mahalle."</option>";
    }
    echo $html;
}
function getSokakList($search, $ilce, $mahalle, $ordering) {
     global $dbase, $limit, $limitstart;
     
     $where = array();
     if ($search) {
         $search = mosStripslashes($search);
         $where[] = "s.sokakadi LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%'";
     }
     
     $il = new Ilce($dbase);
     
     if ($ilce) {
         $where[] = "ic.id='".$ilce."'";
     }
     
     $mah = new Mahalle($dbase); 
     
     if ($mahalle) {
         $where[] = "m.id='".$mahalle."'";
     }
         
     if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY ic.id ASC, m.id ASC, s.sokakadi ASC";
     }
     
     $dbase->setQuery("SELECT COUNT(s.id) FROM #__sokak AS s "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=s.mahalleid "
     . "\n LEFT JOIN #__ilce AS ic ON ic.id=m.ilceid "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     );
     $total = $dbase->loadResult();
     
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     $query = "SELECT s.*, m.mahalle AS mahalleadi, ic.ilce AS ilceadi FROM #__sokak AS s "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=s.mahalleid "
     . "\n LEFT JOIN #__ilce AS ic ON ic.id=m.ilceid "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" ) 
     . $orderingfilter
     ;
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    SokakHTML::getSokakList($rows, $search, $ilce, $mahalle, $il, $mah, $ordering, $pageNav);
}

function delSokak(&$cid) {
	global $dbase;

	$total = count( $cid );
	if ( $total < 1) {
		echo "<script> alert('Silmek için listeden bir sokak seçin'); window.history.go(-1);</script>\n";
		exit;
	}

	ArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "DELETE FROM #__sokak"
	. "\n WHERE ( $cids )"
	;
	$dbase->setQuery( $query );
	if ( !$dbase->query() ) {
		echo "<script> alert('".$dbase->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect( 'index.php?option=admin&bolum=sokak', 'Seçili sokak(lar) silindi' );
}

function saveSokak() {
	 global $dbase, $ilce, $mahalle;
	
	$row = new Sokak( $dbase );
	
	if ( !$row->bind( $_POST ) ) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
    
    /*
    $dbase->setQuery("SELECT id FROM #__sokak WHERE ilceid=".$row->ilceid." AND mahalleid=".$row->mahalleid." AND sokakadi=".$row->sokakadi);
    $var = $dbase->loadResult();
   
    if ($var) {
        ErrorAlert('O sokak/cadde zaten eklenmiş');
    }
	 */
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect('index.php?option=admin&bolum=sokak&ilce='.$row->ilce.'&mahalle='.$row->mahalle, 'Sokak kaydedildi');
	
}

function cancelSokak() {
	global $dbase;
	
	$row = new Sokak( $dbase );
	$row->bind( $_POST );

	Redirect( 'index.php?option=admin&bolum=sokak');
}

function editSokak($cid) {
	global $dbase, $ilce, $mahalle;
	
	$row = new Sokak($dbase);
	$row->load($cid);
    
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $obs = $dbase->loadObjectList();
    
    $ilcelist = array();
    $ilcelist[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    
	foreach ($obs as $ob) {
    $ilcelist[] = mosHTML::makeOption($ob->id, $ob->ilce);
    }
    
    $ilceler = mosHTML::selectList($ilcelist, 'ilceid', 'id="ilce" required', 'value', 'text', $row->ilceid);
    
    if ($row->id) {
    
    $dbase->setQuery("SELECT * FROM #__mahalle WHERE ilceid=".$row->ilceid." ORDER BY mahalle ASC");
    $mobs = $dbase->loadObjectList();

    
    $mahallelist = array();
    
    $mahallelist[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    foreach ($mobs as $mob) {
    $mahallelist[] = mosHTML::makeOption($mob->id, $mob->mahalle);
    }
    
    $mahalleler = mosHTML::selectList($mahallelist, 'mahalleid', 'id="mahalle" required', 'value', 'text', $row->mahalleid);
    
    } else {
        
        $mahallelist[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
        $mahalleler = mosHTML::selectList($mahallelist, 'mahalle', 'id="mahalle" required', 'value', 'text');
    }
	SokakHTML::editSokak($row, $ilceler, $mahalleler);
}
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

$cid = getParam($_REQUEST, 'cid');
$id = intval(getParam($_REQUEST, 'id'));

$search = strval(getParam($_REQUEST, 'search'));
$ilce = intval(getParam($_REQUEST, 'ilce'));

$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$ordering = getParam($_REQUEST, 'ordering'); 

include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
    case 'list':
	getMahalleList($search, $ilce, $ordering);
	break;
	
	case 'add':
	editMahalle(0);
	break;
	
	case 'edit':
	editMahalle(intval(($cid[0])));
	break;
	
	case 'editx':
	editMahalle($id);
	break;
	
	case 'save':
	saveMahalle();
	break;
	
	case 'cancel':
	cancelMahalle();
	break;
	
	case 'delete':
	delMahalle($cid);
	break;
}

function delMahalle(&$cid) {
	global $dbase;

	$total = count( $cid );
	if ( $total < 1) {
		echo "<script> alert('Silmek için listeden bir duyuru seçin'); window.history.go(-1);</script>\n";
		exit;
	}

	ArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "DELETE FROM #__mahalle"
	. "\n WHERE ( $cids )"
	;
	$dbase->setQuery( $query );
	if ( !$dbase->query() ) {
		echo "<script> alert('".$dbase->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect( 'index.php?option=admin&bolum=mahalle', 'Seçili mahalle(ler) silindi' );
}

function saveMahalle() {
	 global $dbase;
	
	$row = new Mahalle( $dbase );
	
	if ( !$row->bind( $_POST ) ) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect('index.php?option=admin&bolum=mahalle', 'Mahalle kaydedildi');
	
}

function cancelMahalle() {
	global $dbase;
	
	$row = new Mahalle( $dbase );
	$row->bind( $_POST );

	Redirect( 'index.php?option=admin&bolum=mahalle');
}

function getMahalleList($search, $ilce, $ordering) {
	 global $dbase, $limit, $limitstart;
     
     $where = array();
     if ($search) {
         $search = mosStripslashes($search);
         $where[] = "m.mahalle LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%'";
     }
     
     $il = new Ilce($dbase);
     
     if ($ilce) {
         $where[] = "m.ilceid='".$ilce."'";
     }
         
     if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY m.ilceid ASC, m.mahalle ASC";
     }
	 
	 $dbase->setQuery("SELECT COUNT(m.id) FROM #__mahalle AS m "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )  
     );
	 $total = $dbase->loadResult();
     
	 
	 $pageNav = new pageNav( $total, $limitstart, $limit);
     
	 $query = "SELECT m.id, m.mahalle, ic.ilce as ilceadi FROM #__mahalle AS m "
     . "\n LEFT JOIN #__ilce AS ic ON ic.id=m.ilceid "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . $orderingfilter   
     //. "\n ORDER BY ic.id ASC, m.mahalle ASC"
     ;
	
	$dbase->setQuery($query, $limitstart, $limit);
	$rows = $dbase->loadObjectList();
	
	MahalleHTML::getMahalleList($rows, $search, $ilce, $il, $pageNav);
}

function editMahalle($cid) {
	global $dbase;
	
	$row = new Mahalle($dbase);
	$row->load($cid);
    
    $dbase->setQuery("SELECT * FROM #__ilce");
    $obs = $dbase->loadObjectList();
    
    $ilcelist = array();
    $ilcelist[] = mosHTML::makeOption('', 'İlçe Seçin');
	foreach ($obs as $ob) {
    $ilcelist[] = mosHTML::makeOption($ob->id, $ob->ilce);
    }
    
    $ilceler = mosHTML::selectList($ilcelist, 'ilceid', 'requreid', 'value', 'text', $row->ilceid);
	MahalleHTML::editMahalle($row, $ilceler);
}
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

$cid = getParam($_REQUEST, 'cid');
$id = intval(getParam($_REQUEST, 'id'));
$limit = intval(getParam($_REQUEST, 'limit', 20));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
	getBransList();
	break;
	
	case 'add':
	editBrans(0);
	break;
	
	case 'edit':
	editBrans(intval(($cid[0])));
	break;
	
	case 'editx':
	editBrans($id);
	break;
	
	case 'save':
	saveBrans();
	break;
	
	case 'cancel':
	cancelBrans();
	break;
	
	case 'delete':
	delBrans($cid);
	break;
}

function delBrans(&$cid) {
	global $dbase;

	$total = count( $cid );
	if ( $total < 1) {
		echo "<script> alert('Silmek için listeden bir branş seçin'); window.history.go(-1);</script>\n";
		exit;
	}

	ArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "DELETE FROM #__branslar"
	. "\n WHERE ( $cids )"
	;
	$dbase->setQuery( $query );
	if ( !$dbase->query() ) {
		echo "<script> alert('".$dbase->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect( 'index.php?option=admin&bolum=branslar', 'Seçili brans(lar) silindi' );
}

function saveBrans() {
	 global $dbase;
	
	$row = new Branslar( $dbase );
	
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
	
	Redirect('index.php?option=admin&bolum=branslar', 'Branş kaydedildi');
	
}

function cancelBrans() {
	global $dbase;
	
	$row = new Branslar( $dbase );
	$row->bind( $_POST );

	Redirect( 'index.php?option=admin&bolum=branslar');
}

function getBransList() {
	 global $dbase, $limit, $limitstart;
	 
	 $dbase->setQuery("SELECT COUNT(*) FROM #__branslar");
	 $total = $dbase->loadResult();
	 
	 $pageNav = new pageNav( $total, $limitstart, $limit);
	 $query = "SELECT * FROM #__branslar";
	
	$dbase->setQuery($query, $limitstart, $limit);
	$rows = $dbase->loadObjectList();
	
	BransHTML::getBransList($rows, $pageNav);
}

function editBrans($cid) {
	global $dbase;
	
	$row = new Branslar($dbase);
	$row->load($cid);
	
	BransHTML::editBrans($row);
}
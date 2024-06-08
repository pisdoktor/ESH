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
	getIlceList();
	break;
	
	case 'add':
	editIlce(0);
	break;
	
	case 'edit':
	editIlce(intval(($cid[0])));
	break;
	
	case 'editx':
	editIlce($id);
	break;
	
	case 'save':
	saveIlce();
	break;
	
	case 'cancel':
	cancelIlce();
	break;
	
	case 'delete':
	delIlce($cid);
	break;
}

function delIlce(&$cid) {
	global $dbase;

	$total = count( $cid );
	if ( $total < 1) {
		echo "<script> alert('Silmek için listeden bir kategori seçin'); window.history.go(-1);</script>\n";
		exit;
	}

	ArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "DELETE FROM #__hastalikcat"
	. "\n WHERE ( $cids )"
	;
	$dbase->setQuery( $query );
	if ( !$dbase->query() ) {
		echo "<script> alert('".$dbase->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect( 'index.php?option=admin&bolum=hastaliklarcat', 'Seçili kategori(ler) silindi' );
}

function saveIlce() {
	 global $dbase;
	
	$row = new HastalikCAT( $dbase );
	
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
	
	Redirect('index.php?option=admin&bolum=hastaliklarcat', 'Kategori kaydedildi');
	
}

function cancelIlce() {
	global $dbase;
	
	$row = new HastalikCAT( $dbase );
	$row->bind( $_POST );

	Redirect( 'index.php?option=admin&bolum=hastaliklarcat');
}

function getIlceList() {
	 global $dbase, $limit, $limitstart;
	 
	 $dbase->setQuery("SELECT COUNT(*) FROM #__hastalikcat");
	 $total = $dbase->loadResult();
	 
	 $pageNav = new pageNav( $total, $limitstart, $limit);
	 $query = "SELECT * FROM #__hastalikcat";
	
	$dbase->setQuery($query, $limitstart, $limit);
	$rows = $dbase->loadObjectList();
	
	IlceHTML::getIlceList($rows, $pageNav);
}

function editIlce($cid) {
	global $dbase;
	
	$row = new HastalikCAT($dbase);
	$row->load($cid);
	
	IlceHTML::editIlce($row);
}
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

$cid = getParam($_REQUEST, 'cid');
$id = intval(getParam($_REQUEST, 'id'));

$search = strval(getParam($_REQUEST, 'search'));
$cat = intval(getParam($_REQUEST, 'cat'));

$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$ordering = getParam($_REQUEST, 'ordering'); 

include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
    case 'list':
	getHastalikList($search, $cat, $ordering);
	break;
	
	case 'add':
	editHastalik(0);
	break;
	
	case 'edit':
	editHastalik(intval(($cid[0])));
	break;
	
	case 'editx':
	editHastalik($id);
	break;
	
	case 'save':
	saveHastalik();
	break;
	
	case 'cancel':
	cancelHastalik();
	break;
	
	case 'delete':
	delHastalik($cid);
	break;
}

function delHastalik(&$cid) {
	global $dbase;

	$total = count( $cid );
	if ( $total < 1) {
		echo "<script> alert('Silmek için listeden bir hastalık seçin'); window.history.go(-1);</script>\n";
		exit;
	}

	ArrayToInts( $cid );
	$cids = 'id=' . implode( ' OR id=', $cid );
	$query = "DELETE FROM #__hastaliklar"
	. "\n WHERE ( $cids )"
	;
	$dbase->setQuery( $query );
	if ( !$dbase->query() ) {
		echo "<script> alert('".$dbase->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect( 'index.php?option=admin&bolum=hastaliklar', 'Seçili hastalık(lar) silindi' );
}

function saveHastalik() {
	 global $dbase;
	
	$row = new Hastalik( $dbase );
	
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
	
	Redirect('index.php?option=admin&bolum=hastaliklar', 'Hastalık kaydedildi');
	
}

function cancelMahalle() {
	global $dbase;
	
	$row = new Hastalik( $dbase );
	$row->bind( $_POST );

	Redirect( 'index.php?option=admin&bolum=hastaliklar');
}

function getHastalikList($search, $cat, $ordering) {
	 global $dbase, $limit, $limitstart;
     
     $where = array();
     if ($search) {
         $search = mosStripslashes($search);
         $where[] = "h.hastalikadi LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%'";
     }
     
     if ($cat) {
         $where[] = "h.cat='".$cat."'";
     }
         
     if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.cat ASC, h.id ASC";
     }
	 
	 $dbase->setQuery("SELECT COUNT(h.id) FROM #__hastaliklar AS h "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )  
     );
	 $total = $dbase->loadResult();
     
	 
	 $pageNav = new pageNav( $total, $limitstart, $limit);
     
	 $query = "SELECT h.id, h.hastalikadi, c.name as kategoriadi, h.icd FROM #__hastaliklar AS h "
     . "\n LEFT JOIN #__hastalikcat AS c ON c.id=h.cat "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . $orderingfilter   
     //. "\n ORDER BY ic.id ASC, m.mahalle ASC"
     ;
	
	$dbase->setQuery($query, $limitstart, $limit);
	$rows = $dbase->loadObjectList();
    
    //kategorileri alalım
    $dbase->setQuery("SELECT * FROM #__hastalikcat");
    $cats = $dbase->loadObjectList();
    
    $catopt[] = mosHTML::makeOption('', 'Bir Kategori Seçin');
    foreach ($cats as $c) {
        $catopt[] = mosHTML::makeOption($c->id, $c->name);
    }
    
    $link = "index.php?option=admin&bolum=hastaliklar&amp;cat=' + this.options[selectedIndex].value + '";
    $list['cat'] = mosHTML::selectList($catopt, 'cat', ' onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $cat);
    
	
	HastalikHTML::getHastalikList($rows, $search, $cat, $list, $pageNav, $ordering);
}

function editHastalik($cid) {
	global $dbase;
	
	$row = new Hastalik($dbase);
	$row->load($cid);
    
    $dbase->setQuery("SELECT * FROM #__hastalikcat");
    $obs = $dbase->loadObjectList();
    
    $catlist = array();
    $catlist[] = mosHTML::makeOption('', 'Kategori Seçin');
	foreach ($obs as $ob) {
    $catlist[] = mosHTML::makeOption($ob->id, $ob->name);
    }
    
    $kategoriler = mosHTML::selectList($catlist, 'cat', 'requreid', 'value', 'text', $row->cat);
    
	HastalikHTML::editHastalik($row, $kategoriler);
}
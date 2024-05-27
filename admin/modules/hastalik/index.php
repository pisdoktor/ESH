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
    getHastalikList();
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
        echo "<script> alert('Silmek için listeden bir duyuru seçin'); window.history.go(-1);</script>\n";
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
    
    Redirect( 'index.php?option=admin&bolum=hastalik', 'Seçili hastalık(lar) silindi' );
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
    
    Redirect('index.php?option=admin&bolum=hastalik', 'Hastalık kaydedildi');
    
}

function cancelHastalik() {
    global $dbase;
    
    $row = new Hastalik( $dbase );
    $row->bind( $_POST );

    Redirect( 'index.php?option=admin&bolum=hastalik');
}

function getHastalikList() {
     global $dbase, $limit, $limitstart;
     
     $dbase->setQuery("SELECT COUNT(*) FROM #__hastaliklar");
     $total = $dbase->loadResult();
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     $query = "SELECT * FROM #__hastaliklar";
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    HastalikHTML::getHastalikList($rows, $pageNav);
}

function editHastalik($cid) {
    global $dbase;
    
    $row = new Hastalik($dbase);
    $row->load($cid);
    
    $dbase->setQuery("SELECT id, parent, hastalikadi as name FROM #__hastaliklar");
    $rows = $dbase->loadObjectList();
    
    $list = mosHTML::treeSelectList($rows, '', '', 'value', '', 'value', 'text', $row->parentid);
    
    HastalikHTML::editHastalik($row, $list);
}
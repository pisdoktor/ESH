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
    getIslemList();
    break;
    
    case 'add':
    editIslem(0);
    break;
    
    case 'edit':
    editIslem(intval(($cid[0])));
    break;
    
    case 'editx':
    editIslem($id);
    break;
    
    case 'save':
    saveIslem();
    break;
    
    case 'cancel':
    cancelIslem();
    break;
    
    case 'delete':
    delIslem($cid);
    break;
}

function delIslem(&$cid) {
    global $dbase;

    $total = count( $cid );
    if ( $total < 1) {
        echo "<script> alert('Silmek için listeden bir duyuru seçin'); window.history.go(-1);</script>\n";
        exit;
    }

    ArrayToInts( $cid );
    $cids = 'id=' . implode( ' OR id=', $cid );
    $query = "DELETE FROM #__islem"
    . "\n WHERE ( $cids )"
    ;
    $dbase->setQuery( $query );
    if ( !$dbase->query() ) {
        echo "<script> alert('".$dbase->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect( 'index.php?option=admin&bolum=islem', 'Seçili İşlem(ler) silindi' );
}

function saveIslem() {
     global $dbase;
    
    $row = new Islem( $dbase );
    
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
    
    Redirect('index.php?option=admin&bolum=islem', 'İşlem kaydedildi');
    
}

function cancelIslem() {
    global $dbase;
    
    $row = new Islem( $dbase );
    $row->bind( $_POST );

    Redirect( 'index.php?option=admin&bolum=islem');
}

function getIslemList() {
     global $dbase, $limit, $limitstart;
     
     $dbase->setQuery("SELECT COUNT(*) FROM #__islem");
     $total = $dbase->loadResult();
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     $query = "SELECT * FROM #__islem";
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    IslemHTML::getIslemList($rows, $pageNav);
}

function editIslem($cid) {
    global $dbase;
    
    $row = new Islem($dbase);
    $row->load($cid);
    
    IslemHTML::editIslem($row);
}
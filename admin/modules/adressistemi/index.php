<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');   

    global $dbase;
    
    $query = "SELECT i.*, m.id AS mid, m.mahalle, s.id AS sid, s.sokakadi, k.kapino FROM #__ilce AS i "
    . "\n LEFT JOIN #__mahalle AS m ON m.ilceid=i.id "
    . "\n LEFT JOIN #__sokak AS s ON s.mahalleid=m.id "
    . "\n LEFT JOIN #__kapino AS k ON k.sokakid=s.id "
    ;
    
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $root = array();
    $i = 0;
    foreach ($rows as $row) {
        if (!isset($root[$row->id])) {
        $root[$row->id]['id'] = $row->id;
        $root[$row->id]['name'] = $row->ilce;
        $root[$row->id]['mahalle'][] = array();
        }
        
        if (!isset($root[$row->id]['mahalle'][$row->mid])) {
        $root[$row->id]['mahalle'][$row->mid]['id'] = $row->mid;
        $root[$row->id]['mahalle'][$row->mid]['mahalle'] = $row->mahalle;
        $root[$row->id]['mahalle'][$row->mid]['sokak'][] = array();
        }
        
        if (!isset($root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid])) {
            $root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid]['id'] = $row->sid;
            $root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid]['sokak'] = $row->sokakadi;
        }
     }
     
     foreach ($root as $root) {
         
         
         echo $root['id'];
         echo "-";
         echo $root['name'];
         echo "<br/>";
          
         foreach ($root['mahalle'] as $mahalle) {
             if (!empty($mahalle)) {
         echo "<sup>L</sup>&nbsp;&nbsp;&nbsp;".$mahalle['id'];
         echo "-";
         echo $mahalle['mahalle'];
         echo "<br/>";              

             }
             
         foreach ($mahalle['sokak'] as $sokak) {
             if (!empty($sokak)) {
         echo "<sup>L</sup>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sokak['id'];
         echo "-";
         echo $sokak['sokak'];
         echo "<br/>";
             }
         }
         
         }
     
     }

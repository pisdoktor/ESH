<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');   

    global $dbase;
    
    if (defined(max_exe_time)) {
    ini_set('max_execution_time', max_exe_time); 
    } else {
    ini_set('max_execution_time', 720);
    }
    
    $query = "SELECT i.*, m.id AS mid, m.mahalle, s.id AS sid, s.sokakadi, k.id AS kid, k.kapino FROM #__ilce AS i "
    . "\n CROSS JOIN #__mahalle AS m ON m.ilceid=i.id "
    . "\n CROSS JOIN #__sokak AS s ON s.mahalleid=m.id "
    . "\n CROSS JOIN #__kapino AS k ON k.sokakid=s.id "
    . "\n ORDER BY i.id ASC, m.mahalle ASC, s.sokakadi ASC, k.kapino ASC "
    ;
    
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
        
    $root = array();

    
    $i = 0;
    foreach ($rows as $row) {
        if (!isset($root[$row->id]) && $row->id) {
        $root[$row->id]['id'] = $row->id;
        $root[$row->id]['ilce'] = $row->ilce;
        }
        
        if ($row->mid > 0 && !isset($root[$row->id]['mahalle'][$row->mid])) {
        $root[$row->id]['mahalle'][$row->mid]['id'] = $row->mid;
        $root[$row->id]['mahalle'][$row->mid]['mahalle'] = $row->mahalle;
        }
        
        if ($row->sid > 0 && !isset($root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid])) {
            $root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid]['id'] = $row->sid;
            $root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid]['sokak'] = $row->sokakadi;
        }
        
        if ($row->kid > 0 && !isset($root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid]['kapi'][$row->kid])) {
            $root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid]['kapi'][$row->kid]['id'] = $row->kid;
            $root[$row->id]['mahalle'][$row->mid]['sokak'][$row->sid]['kapi'][$row->kid]['no'] = $row->kapino;
        }
     }
     
     //var_dump($root);
    ?>
    <script type="text/javascript">
    $(function(){  // on page load
      // Create the tree inside the <div id="tree"> element.
      $("#tree").fancytree();
      // Note: Loading and initialization may be asynchronous, so the nodes may not be accessible yet.
    });
  </script>
    <?php

     echo '<div id="tree">';
     echo '<ul id="treeData" class="ilce">';
     foreach ($root as $root) {
         echo '<li class="ilce">';
         echo $root['id'];
         echo "-";
         echo $root['ilce']." (".count($root['mahalle'])." Mahalle)";
         
         //ksort($root['mahalle']);
         
            echo "\n <ul class='mahalle'>"; 
            foreach ($root['mahalle'] as $mahalle) {
                $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE mahalle=".$mahalle['id']." AND pasif=0");
                $tm = $dbase->loadResult();
                echo "\n <li  class='mahalle'>";
                echo $mahalle['id']."-".$mahalle['mahalle']." (".count($mahalle['sokak'])." Sokak)  (".$tm." Hasta)";
             
               // ksort($mahalle['sokak']);
         
                    echo "\n\n <ul class='sokak'>\n";    
                    foreach ($mahalle['sokak'] as $sokak) {
                        $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE sokak=".$sokak['id']." AND pasif=0");
                        $ts = $dbase->loadResult();
                        echo "\n <li  class='sokak'>"; 
                        echo $sokak['sokak']. " (".count($sokak['kapi'])." Kapı) (".$ts." Hasta)";
                        
                        if ($sokak['kapi']) {
                       // ksort($sokak['kapi']);
                 
                            echo "\n <ul class='kapino'>";
                                                        
                            foreach ($sokak['kapi'] as $kapi) {
                                $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE kapino=".$kapi['id']." AND pasif=0");
                                $th = $dbase->loadResult();
                                echo "\n <li class='kapino'>"; 
                                echo $kapi['no']."(".$th." Hasta)";
                                echo '</li>';
                            }
                           
                            echo "\n</ul>"; //kapino
                        }   
                        echo "\n</li>\n"; // sokak
                    }
                    echo "\n</ul>"; //sokak 
         
                echo "\n</li>"; // mahalle
            }
            echo "\n</ul>";   // mahalle
         
         echo "\n</li>";  //ilçe
     }
     echo "\n</ul>";  //ilçe

     echo "</div>";
     
     
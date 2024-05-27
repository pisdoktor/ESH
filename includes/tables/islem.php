<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Islem extends DBTable {
    
    var $id     = null;
    
    var $islemadi   = null;
    
    function Islem( &$db ) {
        $this->DBTable( '#__islem', 'id', $db );
    }
    
    function yapilanIslem($islem) {
        global $dbase;

        $dbase->setQuery("SELECT islemadi FROM #__islem WHERE id IN (".$islem.")");
        $rows = $dbase->loadResultArray();
    
        return implode(', ', $rows);
              
    }
    
    function getirIslemler() {
         global $dbase;
         
         $dbase->setQuery("SELECT * FROM #__islem");
         $rows = $dbase->loadObjectList();
         
         $op = array();
         foreach($rows as $row) {
             $op[] = mosHTML::makeOption($row->id, $row->islemadi);
         }
         
         return mosHTML::selectList($op, 'islemid', '', 'value', 'key', $islemid);
    }
}
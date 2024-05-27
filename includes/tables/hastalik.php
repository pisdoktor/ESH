<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Hastalik extends DBTable {
    
    var $id     = null;
    
    var $parentid   = null;
    
    var $hastalikadi = null;
    
    function Hastalik( &$db ) {
        $this->DBTable( '#__hastaliklar', 'id', $db );
    }
    
    function treeSelectList() {
        global $dbase;
        
        $dbase->setQuery("SELECT * FROM #__hastaliklar WHERE parent=0");
        $rows = $db->loadObjectList();
        
        return mosHTML::treeSelectList($rows, 'parent', '', 'value', 'text', $this->parent);
        
    }
}
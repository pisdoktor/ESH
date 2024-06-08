<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Hastalik extends DBTable {
    
    var $id     = null;
    
    var $cat = null;
    
    var $hastalikadi = null;
    
    function Hastalik( &$db ) {
        $this->DBTable( '#__hastaliklar', 'id', $db );
    }
}

class HastalikCAT extends DBTable {
    
    var $id = null;
    
    var $name = null;
    
    function HastalikCAT(&$db) {
        $this->DBTable('#__hastalikcat', 'id', $db);
    }

}
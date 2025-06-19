<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Branslar extends DBTable {
    
    var $id     = null;
    
    var $bransadi = null;
    
    function Branslar( &$db ) {
        $this->DBTable( '#__branslar', 'id', $db );
    }
}
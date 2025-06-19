<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class HastaIlacRapor extends DBTable {
    
    var $id     = null;
    
    var $hastatckimlik   = null;
    
    var $hastalikid = null;
    
    var $rapor = 0;
    
    var $bitistarihi = null;
    
    var $brans = null;
    
    var $raporyeri = null;


    function HastaIlacRapor( &$db ) {
        $this->DBTable( '#__hastailacrapor', 'id', $db );
    }
    
}
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Izlem extends DBTable {
    
    var $id     = null;
    
    var $hastatckimlik   = null;
    
    var $izlemtarihi = null;
    
    var $yapilan = null;
    
    var $planli = null;
    
    var $planlanantarih = null;
    
    var $yapilacak = null;
    
    var $izlemiyapan = null;
    
    function Izlem( &$db ) {
        $this->DBTable( '#__izlemler', 'id', $db );
    }
    
    function IzlemiYapanlar($izlemiyapan) {
        global $dbase;

        
        $dbase->setQuery("SELECT name FROM #__users WHERE id IN (".$izlemiyapan.")");
        $rows = $dbase->loadResultArray();
    
        return implode(', ', $rows);
              
    }
    
    
}
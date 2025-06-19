<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Izlem extends DBTable {
    
    var $id     = null;
    
    var $hastatckimlik   = null;
    
    var $izlemtarihi = null;
    
    var $yapilan = null;
    
    var $yapildimi = null;
    
    var $neden = null;
    
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
    
        return implode(',', $rows);
              
    }
    
    function nedenYapilmadi($why) {
    
        $neden = array(
        '0'=> 'Sebep girilmemiş',
        '1'=> 'Hastanede yatıyor', 
        '2'=>'Vefat etmiş',
        '3'=> 'Adresi değişmiş',
        '4'=> '112 ye teslim edildi',
        '5'=> 'Hizmet reddedildi/İptal edildi',
        '6'=> 'Evde kendisi/yakını yok',
        '7'=>'Gerekli evrak yok'
        );
        
        return $neden[$why];
    
    }
    
    
}
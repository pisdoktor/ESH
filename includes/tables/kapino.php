<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class KapiNo extends DBTable {
    
    var $id     = null;
    
    var $ilceid = null;
    
    var $mahalleid = null;
    
    var $sokakid = null;
    
    var $kapino   = null;
    
    function KapiNo( &$db ) {
        $this->DBTable( '#__kapino', 'id', $db );
    }
    
    function getKapiNo($link, $kapino) {
        
        $query = "SELECT * FROM #__kapino ORDER BY kapino ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        $s[] = mosHTML::makeOption('', 'Bir Kapı Numarası Seçin');    

        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->kapino);
        }
        // build the html select list 
        $link = $link ."&amp;kapino=' + this.options[selectedIndex].value + '";
        return mosHTML::selectList( $s, 'kapino', 'onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $kapino );    
    }
}
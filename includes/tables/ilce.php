<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Ilce extends DBTable {
    
    var $id     = null;
    
    var $ilce   = null;
    
    function Ilce( &$db ) {
        $this->DBTable( '#__ilce', 'id', $db );
    }
    
    function getIlce($link, $ilce) {
        
        $query = "SELECT * FROM #__ilce ORDER BY ilce ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        $s[] = mosHTML::makeOption('', 'Bir İlçe Seçin');    

        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->ilce);
        }
        // build the html select list 
        $link = $link ."&amp;ilce=' + this.options[selectedIndex].value + '";
        return mosHTML::selectList( $s, 'ilce', 'id="ilce" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $ilce );    
    }
}

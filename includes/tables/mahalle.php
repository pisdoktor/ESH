<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Mahalle extends DBTable {
    
    var $id     = null;
    
    var $ilceid = null;
    
    var $mahalle   = null;
    
    var $bolge = null;
    
    var $gun = null;
    
    function Mahalle( &$db ) {
        $this->DBTable( '#__mahalle', 'id', $db );
    }
    
    function getMahalle($link, $selected) {
        
        $query = "SELECT m.* FROM #__mahalle AS m ORDER BY mahalle ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        $s[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');    

        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->mahalle);
        }
        // build the html select list 
        $link = $link ."&amp;mahalle=' + this.options[selectedIndex].value + '";
        return mosHTML::selectList( $s, 'mahalle', 'id="mahalle" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $selected );    
    }
}
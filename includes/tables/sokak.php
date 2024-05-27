<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Sokak extends DBTable {
    
    var $id     = null;
    
    var $ilceid = null;
    
    var $mahalleid = null;
    
    var $sokakadi   = null;
    
    function Sokak( &$db ) {
        $this->DBTable( '#__sokak', 'id', $db );
    }
    
    function getSokak($link, $sokak) {
        
        $query = "SELECT * FROM #__sokak ORDER BY sokakadi ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        $s[] = mosHTML::makeOption('', 'Bir Sokak Seçin');    

        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->sokakadi);
        }
        // build the html select list 
        $link = $link ."&amp;sokak=' + this.options[selectedIndex].value + '";
        return mosHTML::selectList( $s, 'sokak', 'id="sokak" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $sokak );    
    }
}
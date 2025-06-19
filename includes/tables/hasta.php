<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Hasta extends DBTable {
    
    var $id     = null;
    
    var $tckimlik   = null;
    
    var $isim = null;
    
    var $soyisim = null;
    
    var $anneAdi = null;
    
    var $babaAdi = null;
    
    var $dogumtarihi = null;
    
    var $cinsiyet = null;
    
    var $boy = null;
    
    var $kilo = null;
    
    var $kayityili = null;
    
    var $kayitay = null; 

    var $ceptel1 = null;
    
    var $ceptel2 = null;
     
    var $ilce = null;
    
    var $mahalle = null;
    
    var $sokak = null;
    
    var $kapino = null;
    
    var $coords = null;
    

    var $bagimlilik = null;
    
    /*
    * Barthel İndeksi  
    */
    var $barbeslenme = null;
    
    var $barbanyo = null;
    
    var $barbakim = null;
    
    var $bargiyinme = null;
    
    var $barbarsak = null;
    
    var $barmesane = null;
    
    var $bartuvalet = null;
    
    var $bartransfer = null;
    
    var $barmobilite = null;
    
    var $barmerdiven = null;
    
    /*
    * Barthel İndeksi 
    */
    
    var $pasif = null;
    
    var $pasiftarihi = null;
    
    var $pasifnedeni = null;
    
    var $gecici = null;
    
    var $ng = null;
    
    var $peg = null;
    
    var $port = null;
    
    var $o2bagimli = null;
    
    var $ventilator = null;
    
    var $kolostomi = null;
    
    var $sonda = null;
    
    var $sondatarihi = null;
    
    var $pansuman = null;
    
    var $pgunleri = null;
    
    var $mama = null;
    
    var $mamacesit = null;
    
    var $mamaraporbitis = null;
    
    var $mamaraporyeri = null;
    
    var $bez = null;
    
    var $bezrapor = null;
    
    var $bezraporbitis = null;
    
    var $yatak = null;
    
    var $hastaliklar = null;
    
    var $notes = null;
    
    function Hasta( &$db ) {
        $this->DBTable( '#__hastalar', 'id', $db );
    }
    
    function hastaIlce($required=1) {
    
    $query = "SELECT * FROM #__ilce ORDER BY ilce ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        if ($required) {
        $s[] = mosHTML::makeOption('', 'Bir İlçe Seçin');    
        } else {
        $s[] = mosHTML::makeOption('0', 'Bir İlçe Seçin');    
        }
        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->ilce);
        }
        
        return mosHTML::selectList($s, 'ilce', 'id="ilce"', 'value', 'text', $this->ilce);
    }
    
    function hastaMahalle($required=1) {
    
    $query = "SELECT * FROM #__mahalle WHERE ilceid=".$this->ilce." ORDER BY mahalle ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        if ($required) {
        $s[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');    
        } else {
        $s[] = mosHTML::makeOption('0', 'Bir Mahalle Seçin');    
        }
        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->mahalle);
        }
        
        return mosHTML::selectList($s, 'mahalle', 'id="mahalle" disabled="disabled"', 'value', 'text', $this->mahalle);
    }
    
    function hastaSokak($required=1) {
    
    $query = "SELECT * FROM #__sokak WHERE mahalleid=".$this->mahalle." ORDER BY sokak ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        if ($required) {
        $s[] = mosHTML::makeOption('', 'Bir Sokak Seçin');    
        } else {
        $s[] = mosHTML::makeOption('0', 'Bir Sokak Seçin');    
        }
        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->sokakadi);
        }
        
        return mosHTML::selectList($s, 'sokak', 'id="sokak" disabled="disabled"', 'value', 'text', $this->sokak);
    }
                      
    function hastaKapiNo($required=1) {
    
    $query = "SELECT * FROM #__kapino WHERE sokakid=".$this->sokak." ORDER BY kapino ASC";
        $this->_db->setQuery($query);
        
        $lists = $this->_db->loadObjectList();
        
        $s = array();
        if ($required) {
        $s[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');    
        } else {
        $s[] = mosHTML::makeOption('0', 'Bir Kapı No Seçin');    
        }
        
        foreach($lists as $list) {
            $s[] = mosHTML::makeOption($list->id, $list->kapino);
        }
        
        return mosHTML::selectList($s, 'kapino', 'id="kapino" disabled="disabled"', 'value', 'text', $this->kapino);
    }
    
    function hastaCinsiyet($required=1) {
        
        $c = array();
        $c[] = mosHTML::makeOption('E', 'Erkek');
        $c[] = mosHTML::makeOption('K', 'Kadın');
        
        return mosHTML::radioList($c, 'cinsiyet', 'id="cinsiyet" class="radio-inline" required', 'value', 'text', $this->cinsiyet);
    }
    
    function hastaKayitYili($required=1) {
        
        $start = '2007';
        $end = date('Y');
        
        $kayityili = $this->kayityili ? $this->kayityili : date('Y');
        
        return mosHTML::integerSelectList($start, $end, '1', 'kayityili', 'id="kayityili" required', $kayityili, $required);
    }
    
    function getKayityili($link, $kayityili, $required=1) {
        
        $start     = '2007';
        $end     = date('Y');
        $inc     = 1;
        $s     = array();

        if ($required) {
        $s[] = mosHTML::makeOption('', 'Bir Yıl Seçin');    
        } else {
        $s[] = mosHTML::makeOption('0', 'Bir Yıl Seçin');    
        }
        
        
        for ($i=$end; $i >= $start; $i-=$inc) {
            $s[] = mosHTML::makeOption( $i, $i );
        }
          
        // build the html select list 
        $link = $link ."&amp;kayityili=' + this.options[selectedIndex].value + '";
        return mosHTML::selectList( $s, 'kayityili', 'onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $kayityili );    
    }
    
    function hastaPasif($required=1) {
        
        $c = array();
        $c[] = mosHTML::makeOption('1', 'Evet'); 
        $c[] = mosHTML::makeOption('0', 'Hayır');
        
        
        
        return mosHTML::radioList($c, 'pasif', 'id="pasif" class="radio-inline" '.$required ? 'required' : ''.'', 'value', 'text', $this->pasif);
    }
    
  
    function hastaPasifNedeni($required=0) {
        
        $c = array();
        $c[] = mosHTML::makeOption('1', 'İyileşme');
        $c[] = mosHTML::makeOption('2', 'Vefat');
        $c[] = mosHTML::makeOption('3', 'İkamet Değişikliği');
        $c[] = mosHTML::makeOption('4', 'Tedaviyi Reddetme');
        $c[] = mosHTML::makeOption('5', 'Tedaviye Yanıt Alamama');
        $c[] = mosHTML::makeOption('6', 'Sonlandırmanın Talep Edilmesi');
        $c[] = mosHTML::makeOption('7', 'Tedaviye Personel Gerekmemesi');
        $c[] = mosHTML::makeOption('8', 'ESH Takibine Uygun Olmaması');
        
        
        return mosHTML::radioList($c, 'pasifnedeni', 'id="pasifnedeni" class="radio-inline"', 'value', 'text', $this->pasifnedeni);
    }
    
    function hastaGecici($required=1) {
        
        $c = array();
        $c[] = mosHTML::makeOption('1', 'Evet');
        $c[] = mosHTML::makeOption('0', 'Hayır');
        
        
        
        return mosHTML::radioList($c, 'gecici', 'id="gecici" class="radio-inline" '.$required ? 'required' : ''.'', 'value', 'text', $this->gecici);
    }
    
    function hastaMama($required=1) {
        
        $c = array();
        $c[] = mosHTML::makeOption('0', 'Bilinmiyor');
        $c[] = mosHTML::makeOption('1', 'Abbot');
        $c[] = mosHTML::makeOption('2', 'Nestle');
        $c[] = mosHTML::makeOption('3', 'Nutricia');
        
        
        return mosHTML::selectList($c, 'mamacesit', 'id="mamacesit" required', 'value', 'text', $this->mamacesit);
    }
    
    function hastaBagimlilik($required=1) {
        
        $t = array();
        $t[] = mosHTML::makeOption('', 'Bağımlılık Durumu Seçin' );
        $t[] = mosHTML::makeOption('2', 'Tam Bağımlı Hasta');
        $t[] = mosHTML::makeOption('1', 'Yarı Bağımlı Hasta');
        $t[] = mosHTML::makeOption('3', 'Bağımsız Hasta');
        
        return mosHTML::selectList($t, 'bagimlilik', 'id="bagimlilik" required', 'value', 'text', $this->bagimlilik);
        
    }
}

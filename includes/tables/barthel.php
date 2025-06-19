<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Barthel extends DBTable {
    
    var $hid     = null;
    
    var $beslenme   = null;
    
    var $banyo = null;
    
    var $bakim = null;
    
    var $giyinme = null;
    
    var $barsak = null;
    
    var $mesane = null;
    
    var $tuvalet = null; 

    var $transfer = null;
    
    var $mobilite = null;
    
    var $merdiven = null;
    
    function Barthel( &$db ) {
        $this->DBTable( '#__barthel', 'hid', $db );
    }
    
    function barthelHesapla() {
    
        $total = $this->beslenme + $this->banyo + $this->bakim + $this->giyinme + $this->barsak + $this->mesane + $this->tuvalet + $this->transfer + $this->mobilite + $this->merdiven;
        /*
        * 0-20 TAM BAĞIMLI
        * 21-61 İLERİD ERECEDE BAĞIMLI
        * 62-90 ORTA DERECEDE BAĞIMLI
        * 91-99 HAFİF BAĞIMLI
        * 100 BAĞIMSIZ
        */
        
        if ($total < 21) {
        return 'Tam Bağımlı ('.$total.')';
        } else if ($total >= 21 || $total < 62 ) {
        return 'İleri Derecede Bağımlı ('.$total.')';
        } else if ($total >= 62 || $total < 91) {
        return 'Orta Derecede Bağımlı ('.$total.')';
        } else if ($total >= 91 || $total < 100) {
        return 'Hafif Derecede Bağımlı ('.$total.')';
        } else {
        return 'Bağımsız ('.$total.')';
        }
}

function barthelBeslenme($barbeslenme) {
    
        $c = array();
        $c[] = mosHTML::makeOption('0', 'Tam Bağımlı');
        $c[] = mosHTML::makeOption('5', 'Kısmi yardım [kesme,sürme vs], diyet modifikasyonu gerekli');
        $c[] = mosHTML::makeOption('10', 'Bağımsız yemek yer ve aletleri kullanır');
        
        return mosHTML::radioList($c, 'barbeslenme', 'id="barbeslenme" class="radio-inline"', 'value', 'text', $barbeslenme);
}

function barthelBanyo($barbanyo) {
    
        $c = array();
        $c[] = mosHTML::makeOption('0', 'Yardım gerekir');
        $c[] = mosHTML::makeOption('5', 'Tek başına yıkanabilir, duş alabilmesi yeterlidir');
        
        return mosHTML::radioList($c, 'barbanyo', 'id="barbanyo" class="radio-inline"', 'value', 'text', $barbanyo);
}

function barthelBakim($barbakim) {
    
        $c = array();
        $c[] = mosHTML::makeOption('0', 'Yardım gerekir');
        $c[] = mosHTML::makeOption('5', 'El yüz temizliği diş fırçalama, traş gibi işleri kendi başına tamamlayabilir');
        
        return mosHTML::radioList($c, 'barbakim', 'id="barbakim" class="radio-inline"', 'value', 'text', $barbakim);
}

function barthelGiyinme($bargiyinme) {
    
        $c = array();                       
        $c[] = mosHTML::makeOption('0', 'Tam bağımlıdır');
        $c[] = mosHTML::makeOption('5', 'Yardım gerekir ama en az yarısını kendisi tamamlar');
        $c[] = mosHTML::makeOption('10', 'Bağımsız giyinebilir. Düğme açma kapama, sürgüleme, ayakkabı bağlama vs yapar');
        
        return mosHTML::radioList($c, 'bargiyinme', 'id="bargiyinme" class="radio-inline"', 'value', 'text', $bargiyinme);
}

function barthelBarsak($barbarsak) {
    
        $c = array();                       
        $c[] = mosHTML::makeOption('0', 'İnkontinans, lavman gerekliliği');
        $c[] = mosHTML::makeOption('5', 'Arasıra kaçırır');
        $c[] = mosHTML::makeOption('10', 'Kontinan');
        
        return mosHTML::radioList($c, 'barbarsak', 'id="barbarsak" class="radio-inline"', 'value', 'text', $barbarsak);
}

function barthelMesane($barmesane) {
    
        $c = array();                       
        $c[] = mosHTML::makeOption('0', 'İnkontinans, lavman gerekliliği');
        $c[] = mosHTML::makeOption('5', 'Arasıra kaçırır');
        $c[] = mosHTML::makeOption('10', 'Kontinan [gece dahil]');
        
        return mosHTML::radioList($c, 'barmesane', 'id="barmesane" class="radio-inline"', 'value', 'text', $barmesane);
}

function barthelTuvalet($bartuvalet) {
    
        $c = array();                       
        $c[] = mosHTML::makeOption('0', 'Kullanamaz. Tam bağımlı');
        $c[] = mosHTML::makeOption('5', 'Yardımsız yapamaz ama kendi birşeyler [elbise çıkarma, tuvalet kağıdı alma] yapabilir');
        $c[] = mosHTML::makeOption('10', 'Kendisi tamamlar [silme dahil]');
        
        return mosHTML::radioList($c, 'bartuvalet', 'id="bartuvalet" class="radio-inline"', 'value', 'text', $bartuvalet);
}

function barthelTransfer($bartransfer) {
    
        $c = array();                       
        $c[] = mosHTML::makeOption('0', 'Tam bağımlı. Oturma dengesi yoktur');
        $c[] = mosHTML::makeOption('5', 'Tek başına oturur. Ama sandalyeye geçiş için MAJOR yardım gerekir');
        $c[] = mosHTML::makeOption('10', 'Geçiş esnasında MİNÖR yardım alır [fiziksel, sözel]');
        $c[] = mosHTML::makeOption('15', 'Tam bağımsız');
        
        return mosHTML::radioList($c, 'bartransfer', 'id="bartransfer" class="radio-inline"', 'value', 'text', $bartransfer);
}

function barthelMobilite($barmobilite) {
    
        $c = array();                       
        $c[] = mosHTML::makeOption('0', 'İmmobil. Tekerlekli sandalyede oturur ama kulllanamaz');
        $c[] = mosHTML::makeOption('5', 'Yardımla da olsa yürüyemez, ama tekerlekli sandalye kullabilir');
        $c[] = mosHTML::makeOption('10', 'Yardımla yürür. Yürüyebilme için MİNÖR yardım alır [fiziksel, sözel]');
        $c[] = mosHTML::makeOption('15', 'Yardımsız en az 45 metre yürür. Baston veya yürüteç gibi cihaz kullanabilir');
        
        return mosHTML::radioList($c, 'barmobilite', 'id="barmobilite" class="radio-inline"', 'value', 'text', $barmobilite);
}

function barthelMerdiven($barmerdiven) {
    
        $c = array();                       
        $c[] = mosHTML::makeOption('0', 'Yapamaz');
        $c[] = mosHTML::makeOption('5', 'Yardımsız yapamaz');
        $c[] = mosHTML::makeOption('10', 'Kendi başına inip çıkar. Trabzan, baston vs kullanabilir');
        
        return mosHTML::radioList($c, 'barmerdiven', 'id="barmerdiven" class="radio-inline"', 'value', 'text', $barmerdiven);
}
    
}


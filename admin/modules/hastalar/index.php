<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');

$id = intval(getParam($_REQUEST, 'id')); 
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));
$tc = getParam($_REQUEST, 'tc');
$search = strval(getParam($_REQUEST, 'search'));
$ilce = intval(getParam($_REQUEST, 'ilce'));
$mahalle = intval(getParam($_REQUEST, 'mahalle'));
$sokak = intval(getParam($_REQUEST, 'sokak'));
$kapino = intval(getParam($_REQUEST, 'kapino'));
$kayityili = intval(getParam($_REQUEST, 'kayityili'));
$kayitay = getParam($_REQUEST, 'kayitay');
$cinsiyet = getParam($_REQUEST, 'cinsiyet');
$bagimlilik = getParam($_REQUEST, 'bagimlilik');
$ozellik = getParam($_REQUEST, 'ozellik');
$pasif = intval(getParam($_REQUEST, 'pasif'));
$secim = intval(getParam($_REQUEST, 'secim'));  
$ordering = getParam($_REQUEST, 'ordering');

$baslangictarih = getParam($_REQUEST, 'baslangictarih');
$bitistarih = getParam($_REQUEST, 'bitistarih');

switch($task) {
    default:
    case 'list':
    getHastaList($baslangictarih, $bitistarih, $search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ozellik, $pasif, $ordering);
    break;
    
    case 'savesokak':
    ekleSokak();
    break;
    
    case 'savekapino':
    ekleKapino();
    break;
    
    case 'show':
    hastaGoster($id);
    break;
    
    case 'edit':
    editHasta($id);
    break;
    
    case 'new':
    editHasta(0);
    break;
    
    case 'save':
    saveHasta();
    break;
    
    case 'cancel':
    cancelHasta();
    break;
    
    case 'control':
    controlTC($tc);
    break;
    
    /*
    case 'ilce':
    hastaIlce($id);
    break;
    */
    case 'mahalle':
    hastaMahalle($id);
    break;
    
    case 'sokak':
    hastaSokak($id);
    break;
    
    case 'kapino':
    hastaKapino($id);
    break;
    
    case 'savebarthel':
    barthelGiris();
    break;
    
    case 'savesonda':
    sondaTarihiGir();
    break;
    
    case 'saveadres':
    adresDegistir();
    break;
    
    case 'savepasif':
    pasifDegistir();
    break;
    
    case 'saveceptel':
    ceptelDegistir();
    break;
    
    case 'savepansuman':
    pansumanDegistir();
    break;
    
    case 'savemama':
    mamaKaydet();
    break;
    
    case 'savebez':
    bezKaydet();
    break;
    
    case 'savecoords':
    coordsDegistir();
    break;
    
    case 'savetemel':
    saveTemel();
    break;
    
    case 'change':
    ozellikDegistir($id, $ozellik, $secim);
    break;
    
    case 'ek3hazirla':
    EkHazirla();
    break;
 
    case 'notekle':
    NotEkle();
    break;
}

function NotEkle() {
     global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $row->notes = mosHTML::cleanText($row->notes);
        
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);


}

function bezKaydet() {
    global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->bez) {
        $row->bezrapor = '';
        $row->bezraporbitis = '';
    } else {
        $row->bezraporbitis = tarihCevir($row->bezraporbitis);
    }
    
    if (!$row->bezrapor) {
        $row->bezraporbitis = '';
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function mamaKaydet() {
    global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->mama) {
        $row->mamacesit = '';
        $row->mamaraporbitis = '';
    } else {
        $row->mamaraporbitis = tarihCevir($row->mamaraporbitis);
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function EkHazirla() {
    global $dbase;
    
    $istekler = getParam($_REQUEST, 'istekler');
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $dbase->setQuery("SELECT h.*, i.ilce AS ilceadi, m.mahalle AS mahalleadi, s.sokakadi, k.kapino FROM #__hastalar AS h"
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce"
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle"
    . "\n LEFT JOIN #__sokak AS s ON s.id=h.sokak"
    . "\n LEFT JOIN #__kapino AS k ON k.id=h.kapino"
    . "\n WHERE h.id=".$id);
    $dbase->loadObject($hasta);
    
    //dogum tarihi düzelt
    $tarih = explode('.',$hasta->dogumtarihi);
    $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
    $hasta->dtarihi = strftime("%d.%m.%Y", $tarih);
        
    //hastalıklarını çekelim
    $dbase->setQuery("SELECT hastalikadi FROM #__hastaliklar WHERE id IN (".$hasta->hastaliklar.")");
    $hastaliklar = $dbase->loadResultArray();
    
    $isteklist = array(
        'mamacikart'=>'Mama Raporu Çıkarma',
        'mamayenile'=>'Mama Raporu Yenileme', 
        'ilaccikart'=>'İlaç Raporu Çıkarma',
        'ilacyenile'=>'İlaç Raporu Yenileme',
        'ilacyazdir'=>'İlaç Yazdırma',
        'tibbimalzeme' => 'Tıbbi Malzeme/Cihaz Raporu Yenileme',
        'bezcikart'=>'Bez Raporu Çıkarma',
        'bezyenile'=>'Bez Raporu Yenileme',
        'tahlil'=>'Tahlil Sonucu Değerlendirme'
    );
    
    $is = array();
    foreach ($istekler as $istek) {
        $is[] = $isteklist[$istek];
    }
?>
<script>

var dd = {
    content: [
        {
            text: [
            'T.C. SAĞLIK BAKANLIĞI\n',
            'DENİZLİ DEVLET HASTANESİ\n',
            'EVDE SAĞLIK HİZMETLERİ POLİKLİNİĞİ\n',
            'BAŞVURU FORMU / EK-3\n\n' 
            ],
            style: 'header',
            alignment: 'center',
            bold: true
        },
        {
            style: 'tableExample',
            table: {
                headerRows: 1,
                body: [
                    [{text: 'KİMLİK BİLGİLERİ:', style: 'tableHeader', bold:true}, {text: '', style: 'tableHeader'}, {text: '', style: 'tableHeader'}, {text: '', style: 'tableHeader'}],
                    [{text: 'Hasta TC Kimlik:', bold: true}, '<?php echo $hasta->tckimlik;?>', {text: 'İLÇE:', bold:true}, '<?php echo $hasta->ilceadi;?>'],
                    [{text: 'Hastanın Adı:', bold:true}, '<?php echo $hasta->isim;?>', {text: 'MAHALLE:', bold:true}, '<?php echo $hasta->mahalleadi;?>'],
                    [{text: 'Hastanın Soyadı:', bold:true}, '<?php echo $hasta->soyisim;?>', {text: 'CADDE/SOKAK:', bold:true}, '<?php echo $hasta->sokakadi;?>'],
                    [{text: 'Doğum Tarihi:', bold:true}, '<?php echo $hasta->dtarihi;?> (<?php echo yas_bul($hasta->dogumtarihi);?> yaş)', {text: 'KAPI NO:', bold:true}, '<?php echo $hasta->kapino;?>'],
                    [{text: 'Anne Adı:', bold:true}, '<?php echo $hasta->anneAdi;?>', {text: 'TELEFON:', bold:true}, '<?php echo $hasta->ceptel1;?>'],
                    [{text: 'Baba Adı:', bold:true}, '<?php echo $hasta->babaAdi;?>', '', '<?php echo $hasta->ceptel2;?>'],
                    [{text: 'Boy:', bold:true}, '<?php echo $hasta->boy;?>', {text: 'Kilo:', bold:true}, '<?php echo $hasta->kilo;?>'],
                ]
            },
            layout: 'headerLineOnly'
        },
        {
        text: [
        {text: '\nGüvence Durumu / Sosyal Güvenlik Numarası:', bold: true},
        '..................\n\n'
        ]},
        {
        text: [
        {text: 'HASTALIKLARI:', bold: true}, ' <?php echo implode(', ', $hastaliklar);?>\n\n']
        },
        {
        text: [
        {text: 'HASTALIĞI HAKKINDA BİLGİ', bold: true}, '(Tanı/Tedavi):\n', 
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n\n',
        {text: 'BAŞVURU AMACI :', bold: true}, ' <?php echo implode(',', $is);?>\n\n',
        ]
        },
        {
        text: [{text: 'SÜREKLİ KULLANDIĞI İLAÇ/TIBBİ CİHAZ/ORTEZ/PROTEZ:', bold:true},'...............................................................\n',
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n',
        'DİŞ TEDAVİSİ İHTİYACI (var ise) :...........................................................................................................\n\n',
        'Yukarıda açık kimliği, adres ve hastalık bilgileri olan şahsın evde sağlık hizmetine ihtiyacı vardır.\nTarafınızdan değerlendirilmesi arz olunur.\n\n\n'
        ], alignment: 'justify'
        },
        {text: ['<?php echo date ("d.m.Y");?>'], alignment: 'right'
        },
        {text: ['.....................\n'], alignment: 'right'
        },
        {text: 'Müracaatı yapanın yakınlık derecesi:..............................\n\n'
        },
        {text: [
        {text: 'DEĞERLENDİRME SONUCU:\n', bold: true},
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n',
        '..................................................................................................................................................................\n',
        {text: ['Değerlendiren Tabip\n',
         'Kaşe/İmza\n'], alignment: 'left'
        }
        ]
        },
        { text: ['ONAY\n',
        'Kurum/Kuruluş Amiri\n',
        'Kaşe/imza/mühür\n',], alignment: 'center'
        }
        
        ]
};

</script>
<div class="panel panel-<?php echo $hasta->pasif ? 'warning':'info';?>">
    
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-9"><h4><i class="fa-solid fa-file-lines"></i> Hasta EK-3 Formu:  <?php echo $hasta->isim." ".$hasta->soyisim;?> <sub>(<?php echo $hasta->anneAdi ? $hasta->anneAdi:'';?>/<?php echo $hasta->babaAdi ? $hasta->babaAdi:'';?>)</sub> <?php echo $hasta->pasif ? '('.$pasifmi[$hasta->pasifnedeni].')' : '' ;?></div>
    <div class="col-xs-3" align="right"><h4><?php echo $hasta->pasif ? '<i class="fa-solid fa-triangle-exclamation"></i> DOSYA KAPALI ('.tarihCevir($hasta->pasiftarihi, 1).')':'';?></h4></div>
    </div>
    </div>
    
    <div class="panel-body">
    <iframe id="iframeContainer" width="100%" height="600px"></iframe>
<a href="" onclick="javascript:pdfMake.createPdf(dd).print();" class="btn btn-warning">Yazdır</a>
<a href="" onclick="javascript:pdfMake.createPdf(dd).download('<?php echo $hasta->isim."-".$hasta->soyisim;?>-<?php echo date ("d.m.Y");?>-EK3.pdf');" class="btn btn-info">İndir</a>
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
</div>

</div>
<script>
var doc = pdfMake.createPdf(dd);

var f = document.getElementById('iframeContainer');

var callback = function(url) { f.setAttribute('src',url); }

doc.getDataUrl(callback, doc);

</script>
<?php
}

function saveTemel() {
        global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $row->isim = trim($row->isim);
    $row->soyisim = trim($row->soyisim);
    $row->anneAdi = trim($row->anneAdi);
    $row->babaAdi = trim($row->babaAdi);
    
    if ($row->dogumtarihi) {
    $tarih = explode('.',$row->dogumtarihi);
    $tarih = mktime(0,0,0,$tarih[1],$tarih[0],$tarih[2]);
    $row->dogumtarihi = strftime("%Y.%m.%d", $tarih);
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function coordsDegistir() {
     global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function adresDegistir() {
             global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function pasifDegistir() {
         global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if ($row->pasif) {
        $row->pasiftarihi = tarihCevir($row->pasiftarihi);
        $row->pansuman = '0';
        $row->pgunleri = '';
    } else {
        $row->pasiftarihi = '';
        $row->pasifnedeni = '';
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function ceptelDegistir() {
     global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function pansumanDegistir() {
        global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
     if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
     if ($row->pansuman) {
    $row->pgunleri = implode(',', $row->pgunleri);
    } else {
    $row->pgunleri = '';
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);

}

function ozellikDegistir($id, $ozellik, $secim) {
    global $dbase;
    
    $dbase->setQuery("UPDATE #__hastalar SET ".$ozellik."=".$secim." WHERE id=".$id);
    $dbase->query();
    
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);
}

function sondaTarihiGir() {
    global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->sonda) {
        $row->sondatarihi = '';
    }
    
    if ($row->sondatarihi) {
        $row->sondatarihi = tarihCevir($row->sondatarihi);
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);
}

function barthelGiris() {
    global $dbase;
    
    $id = intval(getParam($_REQUEST, 'id'));
    
    $row = new Hasta($dbase);
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=hastalar&task=show&id='.$id);
}

function ekleSokak() {
    global $dbase;
    $uid = intval(getParam($_REQUEST, 'uid'));
    
    $row = new Sokak($dbase);
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
        Redirect('index.php?option=admin&bolum=hastalar&task=edit&id='.$uid);
}

function ekleKapino() {
    global $dbase;
    $uid = intval(getParam($_REQUEST, 'uid'));
    $row = new KapiNo( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if ( !$row->check( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    Redirect('index.php?option=admin&bolum=hastalar&task=edit&id='.$uid);
}
/*
function hastaIlce($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__ilce ORDER BY id ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $data = array();
    foreach ($rows as $row) {
        $data[] = '"'.$row->id.'":"'.$row->ilce.'"';
    }
    
    $html = "{";
    $html.= implode(',',$data);
    $html.= "}";
    
    echo $html;

}
*/
function hastaMahalle($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__mahalle WHERE ilceid='".$id."' ORDER BY mahalle ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->mahalle."</option>";
    }
    echo $html;
}

function hastaSokak($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__sokak WHERE mahalleid='".$id."' ORDER BY sokakadi ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->sokakadi."</option>";
    }
    echo $html;
}

function hastaKapino($id) {
    global $dbase;
    
    $query = "SELECT * FROM #__kapino WHERE sokakid='".$id."' ORDER BY kapino ASC";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $html = '';
    foreach ($rows as $row) {
        $html .= "<option value=".$row->id.">".$row->kapino."</option>";
    }
    echo $html;
}

function controlTC($tc) {
    global $dbase;
    
    $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$tc);
    $dbase->loadObject($row);
    
    if ($row) {
        echo "Bu kimlik numarasında <strong>".$row->isim." ".$row->soyisim."</strong> adında bir hasta kaydı var.";
    } else {
        echo '';
    }   
}

function tcKimlikKontrol($tcKimlik=null) {
        // Girilen ifade sayı değilse 
        if(!ctype_digit($tcKimlik))
            return false;
        
        // Boşlukları ve soldaki sıfırı temizle
        $tcKimlik=trim($tcKimlik);
        $tcKimlik=trim($tcKimlik,"0");
        
        if(strlen($tcKimlik)!=11)
            return false;
        
        // TC Kimlik Format Kontrolü : 1-3-5-7-9. haneler toplamından, 2-4-6-8. haneleri çıkar
        // Elde edilen sayıyı 10'a böl, 
        // Kalan sayı TC Kimlik Numarasının 10. karakterini verecek
        $tekBasamaklar=0;
        $ciftBasamaklar=0;
        
        for($i=0; $i<=8; $i+=2)
            $tekBasamaklar+=$tcKimlik[$i];
        
        for($i=1; $i<=7; $i+=2)
            $ciftBasamaklar+=$tcKimlik[$i];
        
        if( ((7*$tekBasamaklar)-$ciftBasamaklar)%10!=$tcKimlik[9] )
            return false;
        
        // Format Kontrolü -2 : 1-10. haneler toplamının 10'a bölümünden kalan, 11. haneyi verecek
        $toplam=0;
        for($i=0; $i<=9; $i++)
            $toplam+=$tcKimlik[$i];
        
        if($toplam%10!=$tcKimlik[10])
            return false;
        else
            return true;
}

function hastaGoster($id) {
    global $dbase;
    
    $id = intval($id);
    $hasta = new Hasta($dbase);
    $hasta->load($id);
    
    if (!$hasta->id) {
        Redirect('index.php?option=admin&bolum=hastalar', 'Böyle bir hasta yok!!!');
    }
    
    $query = "SELECT h.*, COUNT(iz.id) AS toplamizlem, i.ilce AS ilceadi, m.mahalle AS mahalleadi, s.sokakadi AS sokakadi, k.id AS kapinoid, k.kapino AS kapino "
    . "\n FROM #__hastalar AS h "
    . "\n CROSS JOIN #__izlemler AS iz ON iz.hastatckimlik=h.tckimlik "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__sokak AS s ON s.id=h.sokak "
    . "\n LEFT JOIN #__kapino AS k ON k.id=h.kapino "
    . "\n WHERE iz.izlemtarihi>0 AND h.id=".$hasta->id
    . "\n GROUP BY h.id"
    ;
    $dbase->setQuery($query);
    $dbase->loadObject($row);
    
    //hastalıklarını çekelim
    $dbase->setQuery("SELECT id, hastalikadi FROM #__hastaliklar WHERE id IN (".$row->hastaliklar.")");
    $hastaliklara = $dbase->loadObjectList();
    
    foreach ($hastaliklara as $hast) {
        $dbase->setQuery("SELECT rapor FROM #__hastailacrapor WHERE hastatckimlik=".$hasta->tckimlik." AND hastalikid=".$hast->id);
        $varmi = $dbase->loadResult();
            
        if ($varmi) {
            $hastaliklar[] = $hast->hastalikadi.'<sup><strong>(R)</strong></sup>';
        } else {
            $hastaliklar[] = $hast->hastalikadi;
        }
    }
    
    //son izlem tarihini alalım
    $dbase->setQuery("SELECT i.* FROM #__izlemler AS i "
    . "\n WHERE i.hastatckimlik=".$row->tckimlik  
    . "\n ORDER BY i.izlemtarihi DESC LIMIT 1");
    $dbase->loadObject($sonizlem);
    
    $is = new Islem($dbase);
    
    $sonizlem->yapilan = $is->yapilanIslem($sonizlem->yapilan);
    
     //son izlemi yapanlar
    $yap = new Izlem($dbase);
    
    $sonizlem->yapanlar = $yap->IzlemiYapanlar($sonizlem->izlemiyapan);
    
    
    //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $dkapino[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');
    
    foreach ($adres['ilce'] as $ilce) {
    $dilce[] = mosHTML::makeOption($ilce->id, $ilce->ilce);
    }
    
    if ($row->ilce) {
        $dbase->setQuery("SELECT * FROM #__mahalle "
        . "\n WHERE ilceid=".$row->ilce
        . "\n ORDER BY mahalle ASC");
        $adres['mahalle'] = $dbase->loadObjectList();
        
        foreach ($adres['mahalle'] as $m) {
        $dmahalle[] = mosHTML::makeOption($m->id, $m->mahalle);
        }
    }
    
    if ($row->mahalle) {
        $dbase->setQuery("SELECT * FROM #__sokak "
        . "\n WHERE mahalleid=".$row->mahalle
        . "\n ORDER BY sokakadi ASC "
        );
        $adres['sokak'] = $dbase->loadObjectList();
        
        foreach ($adres['sokak'] as $s) {
        $dsokak[] = mosHTML::makeOption($s->id, $s->sokakadi);
        }
    }
    
    if ($row->sokak) {
        $dbase->setQuery("SELECT * FROM #__kapino "
        . "\n WHERE sokakid=".$row->sokak
        . "\n ORDER BY kapino ASC "
        );
        $adres['kapino'] = $dbase->loadObjectList();
        
        foreach ($adres['kapino'] as $k) {
        $dkapino[] = mosHTML::makeOption($k->id, $k->kapino);
        }
    }
    
    
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce" required', 'value', 'text', $row->ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle" required', 'value', 'text', $row->mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak" required', 'value', 'text', $row->sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino"', 'value', 'text', $row->kapinoid);

    //pansuman listesi işlemleri
    $daylist[] = mosHTML::makeOption('1', 'Pzt');
    $daylist[] = mosHTML::makeOption('2', 'Sal');
    $daylist[] = mosHTML::makeOption('3', 'Çar');
    $daylist[] = mosHTML::makeOption('4', 'Per');
    $daylist[] = mosHTML::makeOption('5', 'Cum');
    $daylist[] = mosHTML::makeOption('6', 'Cmt');
    $daylist[] = mosHTML::makeOption('0', 'Paz');
    
    $row->pgunleri = explode(',', $row->pgunleri);
   
    
    $lists['days'] = mosHTML::checkboxList($daylist, 'pgunleri', '', 'value', 'text', $row->pgunleri);
    
    $lists['pasif'] = mosHTML::yesnoRadioList('pasif', 'id="pasif" class="radio-inline" required', $hasta->pasif, 'Evet', 'Hayır');
    
     $c = array();
        $c[] = mosHTML::makeOption('1', 'İyileşme');
        $c[] = mosHTML::makeOption('2', 'Vefat');
        $c[] = mosHTML::makeOption('3', 'İkamet Değişikliği');
        $c[] = mosHTML::makeOption('4', 'Tedaviyi Reddetme');
        $c[] = mosHTML::makeOption('5', 'Tedaviye Yanıt Alamama');
        $c[] = mosHTML::makeOption('6', 'Sonlandırmanın Talep Edilmesi');
        $c[] = mosHTML::makeOption('7', 'Tedaviye Personel Gerekmemesi');
        $c[] = mosHTML::makeOption('8', 'ESH Takibine Uygun Olmaması');
        
        
    $lists['pasifnedeni'] = mosHTML::radioList($c, 'pasifnedeni', 'id="pasifnedeni" class="radio-inline"', 'value', 'text', $hasta->pasifnedeni);

    //ek 3 istekleri
    $istek = array();
        
        $istekler = array(
        'mamacikart'=>'Mama Raporu Çıkarma',
        'mamayenile'=>'Mama Raporu Yenileme', 
        'ilaccikart'=>'İlaç Raporu Çıkarma',
        'ilacyenile'=>'İlaç Raporu Yenileme',
        'ilacyazdir'=>'İlaç Yazdırma',
        'tibbimalzeme' => 'Tıbbi Malzeme/Cihaz Raporu Yenileme',
        'bezcikart'=>'Bez Raporu Çıkarma',
        'bezyenile'=>'Bez Raporu Yenileme',
        'tahlil'=>'Tahlil Sonucu Değerlendirme'
        );
        
        foreach ($istekler as $v=>$k) {
        $istek[] = mosHTML::makeOption($v, $k);
        }
        
        $lists['ek3istek'] = mosHTML::checkboxList($istek, 'istekler', '', 'value', 'text');
        
        if ($hasta->mamacesit == 0 || $hasta->mamacesit == "") {
        $row->mamacesit = "Bilinmiyor";
        } else if ($hasta->mamacesit == '1') {
        $row->mamacesit = "Abbot";
        } else if ($hasta->mamacesit == '2') {
        $row->mamacesit = "Nestle";
        } else if ($hasta->mamacesit == '3') {
        $row->mamacesit = "Nutricia";
        }
        
        
                //mama çeşidi
    $c = array();
    $c[] = mosHTML::makeOption('0', 'Bilinmiyor');
    $c[] = mosHTML::makeOption('1', 'Abbot');
    $c[] = mosHTML::makeOption('2', 'Nestle');
    $c[] = mosHTML::makeOption('3', 'Nutricia');
        
    $lists['mamacesit'] = mosHTML::selectList($c, 'mamacesit', '', 'value', 'text', $hasta->mamacesit);
    
    //ölmüş mü kontrol edelim
    /*
    if (olumBildirimi($hasta)) {
        
        $dbase->setQuery("UPDATE #__hastalar SET pasif='-1' WHERE tckimlik=".$hasta->tckimlik);
        $dbase->query();
    }
    */
    
   
    HastaList::hastaGoster($row, $hastaliklar, $sonizlem, $lists); 
}

function getHastaList($baslangictarih, $bitistarih, $search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ozellik, $pasif, $ordering) {
    global $dbase, $limit, $limitstart;
    
    $where = array();
     if ($search) {
         $search = mosStripslashes($search);
         if (is_numeric($search)) {
         $where[] = "h.tckimlik = ". $dbase->getEscaped( $search );
         } else {
         $where[] = "(h.isim LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%' OR h.soyisim LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%')";
         } 
     }
     
     if ($baslangictarih) {
         $baslangictime = strtotime('-'.$baslangictarih.' year');
         $baslangictime = date('Y.m.d', $baslangictime);
         
        $where[] = "h.dogumtarihi<='".$baslangictime."'";
     }
     
     if ($bitistarih) {
         $bitistime = strtotime('-'.$bitistarih.' year');
         $bitistime = date('Y.m.d', $bitistime);
         
        $where[] = "h.dogumtarihi>='".$bitistime."'";
     }
     
     if ($ilce) {
         $where[] = "h.ilce='".$ilce."'";
     }
     
     if ($mahalle) {
         $where[] = "h.mahalle='".$mahalle."'";
     }
     
     if ($sokak) {
         $where[] = "h.sokak='".$sokak."'";
     }
     if ($kapino) {
         $where[] = "h.kapino='".$kapino."'";
     }
     
     if ($kayityili) {
         $where[] = "h.kayityili='".$kayityili."'";  
     }
     
     if ($kayitay) {
         $where[] = "h.kayitay='".$kayitay."'";  
     }
     
     if ($cinsiyet) {
         $where[] = "h.cinsiyet='".$cinsiyet."'";  
     }
     
     if ($bagimlilik) {
         $where[] = "h.bagimlilik='".$bagimlilik."'";  
     }
     
     if ($ozellik) {
        $where[] = "h.".$ozellik."=1";
    }
    
    if ($pasif == 1) {
        $where[] = "h.pasif='1'";
    } else if ($pasif == 2) {
        $where[] = "h.pasif='0'";
    } else if ($pasif == '-1') {
        $where[] = "h.pasif='-1'";
    }
    
     
     if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY h.isim ASC, h.soyisim ASC";
     }  
    
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     ;
     
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     
     $query = "SELECT h.*, m.mahalle, i.ilce, COUNT(iz.id) AS izlemsayisi FROM #__hastalar AS h "
     . "\n CROSS JOIN #__izlemler AS iz ON iz.hastatckimlik=h.tckimlik "
     . "\n CROSS JOIN #__ilce AS i ON i.id=h.ilce "
     . "\n CROSS JOIN #__mahalle AS m ON m.id=h.mahalle "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY h.id " 
     . $orderingfilter
     ;

    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
         $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
         $row->sonizlemtarihi = $dbase->loadResult();
         
         $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE planli=1 AND planlanantarih>0 AND hastatckimlik=".$row->tckimlik);
         $row->totalplanli = $dbase->loadResult();    
    }
    
    //adres oluştur
     //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $dkapino[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');
    
    foreach ($adres['ilce'] as $ai) {
    $dilce[] = mosHTML::makeOption($ai->id, $ai->ilce);
    }
    
    if ($ilce) {
    $dbase->setQuery("SELECT * FROM #__mahalle WHERE ilceid=".$ilce." ORDER BY mahalle ASC");
    $adres['mahalle'] = $dbase->loadObjectList();
    
    foreach ($adres['mahalle'] as $mah) {
    $dmahalle[] = mosHTML::makeOption($mah->id, $mah->mahalle);
    }
    }
    
    if ($mahalle) {
    $dbase->setQuery("SELECT * FROM #__sokak WHERE mahalleid=".$mahalle." ORDER BY sokakadi ASC");
    $adres['sokak'] = $dbase->loadObjectList();
    
    foreach ($adres['sokak'] as $sk) {
    $dsokak[] = mosHTML::makeOption($sk->id, $sk->sokakadi);
    }
    }
    
    if ($sokak) {
    $dbase->setQuery("SELECT * FROM #__kapino WHERE sokakid=".$sokak." ORDER BY kapino ASC");
    $adres['kapino'] = $dbase->loadObjectList();
    
    foreach ($adres['kapino'] as $kp) {
    $dkapino[] = mosHTML::makeOption($kp->id, $kp->kapino);
    }
    }
    
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce"', 'value', 'text', $ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle"', 'value', 'text', $mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak"', 'value', 'text', $sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino"', 'value', 'text', $kapino);
    
     $start = '2007';
     $end = date('Y');
        
     $lists['kayityili'] = mosHTML::integerSelectList($start, $end, '1', 'kayityili', 'id="kayityili"', $kayityili, 1);
     
      $c = array();
      $c[] = mosHTML::makeOption('', 'Cinsiyet Seçin');
      $c[] = mosHTML::makeOption('E', 'Erkek');
      $c[] = mosHTML::makeOption('K', 'Kadın');
        
      $lists['cinsiyet'] = mosHTML::selectList($c, 'cinsiyet', 'id="cinsiyet"', 'value', 'text', $cinsiyet);
      
      $t = array();
        $t[] = mosHTML::makeOption('', 'Bağımlılık Durumu Seçin' );
        $t[] = mosHTML::makeOption('2', 'Tam Bağımlı Hasta');
        $t[] = mosHTML::makeOption('1', 'Yarı Bağımlı Hasta');
        $t[] = mosHTML::makeOption('3', 'Bağımsız Hasta');
        
        $lists['bagimlilik'] = mosHTML::selectList($t, 'bagimlilik', 'id="bagimlilik"', 'value', 'text', $bagimlilik);
        
        
   $oz = array();
    $oz[] = mosHTML::makeOption('', 'Hasta Özelliği Seçin');
    $oz[] = mosHTML::makeOption('gecici', 'Geçici Kayıtlı');
    $oz[] = mosHTML::makeOption('ng', 'Nazogastrik Takılı');
    $oz[] = mosHTML::makeOption('peg', 'PEGli Hastalar');
    $oz[] = mosHTML::makeOption('port', 'PORTlu Hastalar');
    $oz[] = mosHTML::makeOption('o2bagimli', 'O2 Bağımlı Hastalar');
    $oz[] = mosHTML::makeOption('ventilator', 'Ventilatör Takılı');
    $oz[] = mosHTML::makeOption('kolostomi', 'Kolostomili Hastalar');
    $oz[] = mosHTML::makeOption('sonda', 'Sondalı Hastalar');
    $oz[] = mosHTML::makeOption('bez', 'Alt Bezi Kullananlar');
    $oz[] = mosHTML::makeOption('mama', 'Mama Kullananlar');
    $oz[] = mosHTML::makeOption('yatak', 'Hasta Yatağı Olanlar');
    $oz[] = mosHTML::makeOption('pansuman', 'Pansuman Takibindekiler');
    $lists['ozellik'] = mosHTML::selectList($oz, 'ozellik', 'id="ozellik"', 'value', 'text', $ozellik);
    
    $pas = array();
    $pas[] = mosHTML::makeOption('', 'Tüm Hastalar');
    $pas[] = mosHTML::makeOption('1', 'Pasif Hastalar');
    $pas[] = mosHTML::makeOption('2', 'Aktif Hastalar');
    $pas[] = mosHTML::makeOption('-1', 'Muhtemel Vefatlar');
    $lists['pasif'] = mosHTML::selectList($pas, 'pasif', 'id="pasif"', 'value', 'text', $pasif);
    
   HastaList::getHastaList($baslangictarih, $bitistarih, $rows, $pageNav, $search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ozellik, $pasif, $ordering, $lists);
}

function saveHasta() {
         global $dbase;
    
    $row = new Hasta( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $row->isim = trim($row->isim);
    $row->soyisim = trim($row->soyisim);
    $row->anneAdi = trim($row->anneAdi);
    $row->babaAdi = trim($row->babaAdi);
    
    $isNew     = !$row->id;
    
    if ($row->pasiftarihi) {
    $row->pasiftarihi = tarihCevir($row->pasiftarihi);
    $row->pansuman = '0';
    $row->pgunleri = '';
    }
    
    if ($row->sondatarihi) {
    $row->sondatarihi = tarihCevir($row->sondatarihi);
    }
    
    if ($row->bezraporbitis) {
    $row->bezraporbitis = tarihCevir($row->bezraporbitis);
    }
    
    if (!$row->bez) {
       $row->bezrapor = 0;
       $row->bezraporbitis = '';
    }
    
    
    if ($row->mamaraporbitis) {
    $row->mamaraporbitis = tarihCevir($row->mamaraporbitis);
    }
    
    if (!$row->mama) {
       $row->mamacesit = '';
       $row->mamaraporbitis = '';
       $row->mamaraporyeri = '';
    }
    
    if ($row->hastaliklar) {
    $row->hastaliklar = implode(',', $row->hastaliklar);
    }
    
    if ($row->dogumtarihi) {
    $tarih = explode('.',$row->dogumtarihi);
    $tarih = mktime(0,0,0,$tarih[1],$tarih[0],$tarih[2]);
         
    $row->dogumtarihi = strftime("%Y.%m.%d", $tarih);
    }
    
    if ($row->pansuman) {
    $row->pgunleri = implode(',', $row->pgunleri);
    } else {
    $row->pgunleri = '';
    }
    
    if (!$isNew) {
        $dbase->setQuery("SELECT tckimlik FROM #__hastalar WHERE id=".$row->id);
        $oldtc = $dbase->loadResult();
        
        if ($oldtc != $row->tckimlik) {
            $dbase->setQuery("UPDATE #__izlemler SET hastatckimlik=".$row->tckimlik." WHERE hastatckimlik=".$oldtc);
            $dbase->query();
        }
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if ($isNew) {
    $link = "index.php?option=admin&bolum=izlemler&task=hedit&tc=".$row->tckimlik;
    } else {
    $link = "index.php?option=admin&bolum=hastalar&task=show&id=".$row->id;
    }
    
    Redirect($link);
    
}

function editHasta($id) {
    global $dbase, $limit, $limitstart;
    
    $row = new Hasta($dbase);
    $row->load($id);
    
    if ($row->id) {
     $tarih = explode('.',$row->dogumtarihi);
     $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
     $row->dogumtarihi = strftime("%d.%m.%Y", $tarih);
     
     $row->hastaliklar = explode(',', $row->hastaliklar);   
    }
    
    /**
    * @desc sık kullanılan hastalıkları alıp bir selectlist yapalım
    */
    if (!$row->hastaliklar) { 
    $dbase->setQuery("SELECT hastaliklar FROM #__hastalar WHERE pasif=0 AND hastaliklar>0");
    $liste = $dbase->loadResultArray();
    } else {
    $liste = $row->hastaliklar;
    }

    
    $data = array();
    foreach ($liste as $li) {
        if ($li) {
          $data1 = explode(',', $li);
          
          $data = array_merge($data1, $data); 
        }
    }

    //hastalıkların kullanım sayılarını alalım
    $veri = array_count_values($data);
    //en çok kullanılandan en az kullanılana göre sıralayalım
    arsort($veri);
    //sık kullanılan 10 hastalığı seçelim
    $veri = array_slice($veri, 0, 10, true);
     
    
    
    $sikdata = array();   
    
    foreach ($veri as $v=>$k) {
        
        $dbase->setQuery("SELECT * FROM #__hastaliklar WHERE id='".$v."' ORDER BY hastalikadi ASC");
        $dbase->loadObject($opt);
        
        $sikopt[] = mosHTML::makeOption($opt->id, $opt->icd.' '.$opt->hastalikadi); 
        
        $sikdata[] = $v;
    }
    
    $lists['hastalik']['sik'] = mosHTML::checkboxList($sikopt, 'hastaliklar', '', 'value', 'text', $row->hastaliklar); 

    $sikdata = implode(',', $sikdata);
    
     
    //diğer hastalıkları alalım
    $dbase->setQuery("SELECT * FROM #__hastalikcat ORDER BY name ASC");
    $hcats = $dbase->loadObjectList();

    foreach ($hcats as $hcat) {
        $dbase->setQuery("SELECT * FROM #__hastaliklar WHERE cat='".$hcat->id."'"
        . ($sikdata ? " AND id NOT IN (".$sikdata.")":"")
        . "\n ORDER BY hastalikadi ASC"
        );
        $hlist = $dbase->loadAssocList();
        
        foreach ($hlist as $h) { 
           
                 $option['hastalik'][$hcat->id][] = mosHTML::makeOption($h['id'], $h['icd'].' '.$h['hastalikadi']);
                 
        }
        
        $lists['hastalik'][$hcat->id] = mosHTML::checkboxList($option['hastalik'][$hcat->id], 'hastaliklar', '', 'value', 'text', $row->hastaliklar); 
    }
    
     
    //adresler için ilçeleri alalım
    $dbase->setQuery("SELECT * FROM #__ilce ORDER BY ilce ASC");
    $adres['ilce'] = $dbase->loadObjectList();
    
    $dilce[] = mosHTML::makeOption('', 'Bir İlçe Seçin');
    $dmahalle[] = mosHTML::makeOption('', 'Bir Mahalle Seçin');
    $dsokak[] = mosHTML::makeOption('', 'Bir Sokak Seçin');
    $dkapino[] = mosHTML::makeOption('', 'Bir Kapı No Seçin');
    
    foreach ($adres['ilce'] as $ilce) {
    $dilce[] = mosHTML::makeOption($ilce->id, $ilce->ilce);
    }
    
    if ($row->ilce) {
        $dbase->setQuery("SELECT * FROM #__mahalle "
        . "\n WHERE ilceid=".$row->ilce
        . "\n ORDER BY mahalle ASC");
        $adres['mahalle'] = $dbase->loadObjectList();
        
        foreach ($adres['mahalle'] as $m) {
        $dmahalle[] = mosHTML::makeOption($m->id, $m->mahalle);
        }
    }
    
    if ($row->mahalle) {
        $dbase->setQuery("SELECT * FROM #__sokak "
        . "\n WHERE mahalleid=".$row->mahalle
        . "\n ORDER BY sokakadi ASC "
        );
        $adres['sokak'] = $dbase->loadObjectList();
        
        foreach ($adres['sokak'] as $s) {
        $dsokak[] = mosHTML::makeOption($s->id, $s->sokakadi);
        }
    }
    
    if ($row->sokak) {
        $dbase->setQuery("SELECT * FROM #__kapino "
        . "\n WHERE sokakid=".$row->sokak
        . "\n ORDER BY kapino ASC "
        );
        $adres['kapino'] = $dbase->loadObjectList();
        
        foreach ($adres['kapino'] as $k) {
        $dkapino[] = mosHTML::makeOption($k->id, $k->kapino);
        }
    }
    
    
    $lists['ilce'] = mosHTML::selectList($dilce, 'ilce', 'id="ilce" required', 'value', 'text', $row->ilce);
    $lists['mahalle'] = mosHTML::selectList($dmahalle, 'mahalle', 'id="mahalle" required', 'value', 'text', $row->mahalle);
    $lists['sokak'] = mosHTML::selectList($dsokak, 'sokak', 'id="sokak"', 'value', 'text', $row->sokak);
    $lists['kapino'] = mosHTML::selectList($dkapino, 'kapino', 'id="kapino"', 'value', 'text', $row->kapino);
    
    $lists['ilceid'] = mosHTML::selectList($dilce, 'ilceid', 'id="ilce" required readonly', 'value', 'text', $row->ilce);
    $lists['mahalleid'] = mosHTML::selectList($dmahalle, 'mahalleid', 'id="mahalle" required readonly', 'value', 'text', $row->mahalle);
    $lists['sokakid'] = mosHTML::selectList($dsokak, 'sokakid', 'id="sokak" required readonly', 'value', 'text', $row->sokak);
    
    HastaList::editHasta($row, $lists, $limitstart, $limit, $hcats);
}

function cancelHasta() {
    global $dbase, $limitstart, $limit;
    
    $row = new Hasta( $dbase );
    $row->bind( $_POST );
    
    $link = "index.php?option=admin&bolum=hastalar&limit=".$limit."&limitstart=".$limitstart;

    Redirect( $link );
}
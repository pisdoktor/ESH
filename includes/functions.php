<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );  

/**
* Site menüsü
*/
function siteMenu() {
    global $my, $dbase;
    ?>
<div id="cssmenu"> 
<ul>
<?php
// üyelere özel menü
if (!$my->id) {
?>
</ul> 
</div>  
<?php        
} else {
?>

<li><a href="<?php echo SITEURL;?>"><span>Anasayfa</span></a></li>
<li><a href="index.php?option=site&bolum=profil&task=my"><span>Profilim</span></a></li>

<li class="has-sub"><a href="#"><span>Hasta İşlemleri</span></a>
<ul>
<li><a href="index.php?option=site&bolum=hastalar">Aktif Hasta Listesi</a></li>
<li><a href="index.php?option=site&bolum=phastalar">Pasif Hasta Listesi</a></li>
<li><div class="divider"></li> 
<li><a href="index.php?option=site&bolum=hastalar&task=new">Yeni Hasta Kayıt</a></li>
</ul>
</li>

<li class="has-sub"><a href="#"><span>İzlem İşlemleri</span></a>
<ul>
<li><a href="index.php?option=site&bolum=izlemler">Aktif İzlem Listesi</a></li>
<li><a href="index.php?option=site&bolum=pizlemler">Planlanan İzlem Listesi</a></li>
<li><a href="index.php?option=site&bolum=stats&task=izlenmeyen">İzlem Girilmeyen Hastalar</a></li>
</ul>
</li>

<li class="has-sub"><a href="#"><span>Genel İstatistik</span></a>
<ul>

<li><a href="index.php?option=site&bolum=stats&task=temel">İzleme Göre Hastalar</a></li>
<!-- mahalleye göre istatistikler -->
<li><a href="index.php?option=site&bolum=stats&task=hmahalle">Mahalleye Göre Hastalar</a></li>
<!-- kayıt yılına göre istatistikler -->
<li><a href="index.php?option=site&bolum=stats&task=kayityili">Kayıt Yılına Göre Hastalar</a></li>
<!-- kayıt yılına göre istatistikler -->
<li><a href="index.php?option=site&bolum=stats&task=yasgruplari">Yaşına Göre Hastalar</a></li>
<!-- hastalıklara göre istatistikler -->
<li><a href="index.php?option=site&bolum=stats&task=hastalik">Hastalıklara Göre Hastalar</a></li>
<!-- özelliği olan hasta istatistikler -->
<li><a href="index.php?option=site&bolum=stats&task=special">Özelliğine Göre Hastalar</a></li>

<li><a href="index.php?option=site&bolum=stats&task=dogumgunu">Bugün Doğan Hastalar</a></li>

<li><a href="index.php?option=site&bolum=stats&task=adres">Adrese Göre Hastalar</a></li> 

<li><a href="index.php?option=site&bolum=stats&task=islem">Tarihe Göre İşlemler</a></li> 

<li><a href="index.php?option=site&bolum=stats&task=personel">Tarihe Göre Personeller</a></li>

<li><a href="index.php?option=site&bolum=stats&task=hgirilmeyen">Bilgileri Eksik Hastalar</a></li> 
</ul>
</li>

<?php 
if ($my->id == 1) {
?>
<li><a href="index.php?option=admin"><span>Yönetim Paneli</span></a></li>
<?php    
}
?>
<li><a href="index.php?option=logout"><span>Çıkış Yap</span></a></li>    
</ul>
</div>
<div id="messages"></div>
<?php
}
}
/**
* Admin için siteden yönetim paneline geçiş için fonksiyon
*/
function convertAdmin() {
    global $mainframe, $dbase, $my;
    
    if ($my->id == 1) {
    $session = new Session($dbase);
    $session->load($mainframe->_session->session);

    $session->access_type = 'admin';
    $session->update();
    
    Redirect('index.php');
    } else {
        NotAuth();
    }    
}
/**
* Site modüllerini yükleyen fonksiyon
*/
function loadSiteModule() {
    global $option, $bolum, $task;
    global $id, $cid;
    global $limit, $limitstart;
    global $mainframe, $my, $mosmsg;
    
    switch($option) {
    default:
    MainPage();
    break;
    
    case 'site':
    if ($bolum) {
    include_once(ABSPATH. '/site/modules/'.$bolum.'/index.php');
    } else {
        Redirect('index.php');
    }
    break;
    
    case 'admin':
    convertAdmin();
    break;
    }
    
}

/**
* Ana sayfa menüsü
*/
function MainPage() {
    ?>
    <div class="row">
    <div class="col-sm-3">
    <?php echo izlemSikligi();?>
    <?php echo loadYasGrup();?> 
    <?php echo UserPanel();?>
    
    </div>
    <div class="col-sm-9">
    <?php echo GenelStats();?>  
    <?php echo loadCikarilmaNedeni();?>
    </div>
    </div>
    <?php
}

function izlemSikligi() {
    global $dbase;
    
    $today = date('d.m.Y');
    $first = date("01.m.Y", strtotime($today));
    $last = date("t.m.Y", strtotime($today));

     $where = array();
     $where[] = "i.izlemtarihi>='".tarihCevir($first)."'";
     $where[] = "i.izlemtarihi<='".tarihCevir($last)."'";
 
     
     $query = "SELECT COUNT(i.id) AS izlemsayisi FROM #__izlemler AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . "\n GROUP BY h.tckimlik ";
    
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
       
        
      $toplamizlem = 0;
      $toplamhasta = 0;
      
      foreach ($rows as $row) {
         $toplamizlem = $toplamizlem + $row->izlemsayisi;
         ++$toplamhasta;
      }
     
    ?>
    <div class="panel panel-success">
  <div class="panel-heading">Bu Ayın İzlem Sıklığı</div>
  <table class="table table-rounded">
  <tr>
  <th>T. HASTA</th>
  <th>T. İZLEM</th>
  <th>İZLEM SIKLIĞI</th>
  </tr>
  <tr>
  <td><h4><span class="label label-success"><?php echo $toplamhasta;?></span></h4></td>
  <td><h4><span class="label label-info"><?php echo $toplamizlem;?></span></h4></td>
  <td><h4><span class="label label-warning"><?php echo round($toplamizlem/$toplamhasta, 2);?></span></h4></td>
  </tr>
  </table>
   
   
    
  </div>
    <?php

}

/**
* Aylık hastaların takipten çıkarılma nedeni 
*/
function loadCikarilmaNedeni() {
    global $dbase;
    
    $today = date('d.m.Y');  
    $first = tarihCevir(date("01.m.Y", strtotime($today)));
    $last = tarihCevir(date("t.m.Y", strtotime($today)));
    
    $dbase->setQuery("SELECT pasifnedeni, COUNT(pasifnedeni) AS sayi FROM #__hastalar WHERE pasif=1 AND pasiftarihi>='".$first."' AND pasiftarihi<='".$last."' GROUP BY pasifnedeni");
    $rows = $dbase->loadObjectList();
    
    $neden = array(
    '0' => 'İyileşme',
    '1' => 'Vefat',
    '2' => 'İkamet Değişikliği',
    '3' => 'Tedaviyi Reddetme',
    '4' => 'Tedaviye Yanıt Alamama',
    '5' => 'Sonlandırmanın Talep Edilmesi',
    '6' => 'Tedaviye Personel Gerekmemesi',
    '7' => 'ESH Takibine Uygun Olmaması'
    );
    ?>
<div class="panel panel-danger">
  <div class="panel-heading">Bu Ay Takipten Çıkarılma Nedenleri</div>
  
<table class="table">
          <thead class="thead-dark"> 
          <tr>
      <th scope="col">Takipten Çıkarılma Nedeni</th>
      <th scope="col">Hasta Sayısı</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($rows as $row) { ?>
    <tr>
    <td><?php echo $neden[$row->pasifnedeni];?></td>
    <td><?php echo $row->sayi;?></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
    <?php    
}
/**
* Aylık yaş gruplarına göre izlem yapılan hastalar
*/
function loadYasGrup() {
    global $dbase;
    
    $today = date('d.m.Y');  
    $first = tarihCevir(date("01.m.Y", strtotime($today)));
    $last = tarihCevir(date("t.m.Y", strtotime($today)));
    
    $query = "SELECT h.dogumtarihi FROM #__izlemler AS i "
    . "LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . "WHERE i.izlemtarihi>='".$first."' AND i.izlemtarihi<='".$last."' "
    . "GROUP BY h.tckimlik ORDER BY h.id, h.isim DESC" 
    ;
    $dbase->setQuery($query);
    $tarihler = $dbase->loadResultArray();
    
    $yasaralik = array();
    
    //0-1 ay
    $yasaralik[01] = 0; 
    // 2 ay-2 yaş
    $yasaralik[22] = 0;
    //3-18 yaş
    $yasaralik[318] = 0;
    //19-45 yaş
    $yasaralik[1945] = 0;
    //46-65 yaş
    $yasaralik[4665] = 0;
    //66-85 yaş
    $yasaralik[6685] = 0;
    // 86 ve üzeri
    $yasaralik[86] = 0;
    
    $yasaralik['toplam'] = 0;
    
    foreach ($tarihler as $dtarih) {
        
        $yas = yas_bul($dtarih);
        
        if ($yas < 1) {
            ++$yasaralik[01];
        }
        
        if ($yas > 1 && $yas < 3) {
           ++$yasaralik[22];
        }
        
        if ($yas > 2 && $yas < 19) {
           ++$yasaralik[318];
        }
        
        if ($yas > 18 && $yas < 46) {
           ++$yasaralik[1945]; 
        }
        
        if ($yas > 45 && $yas < 66) {
          ++$yasaralik[4665];  
        }
        if ($yas > 65 && $yas < 86) {
          ++$yasaralik[6685];  
        }
        
        if ($yas > 85) {
          ++$yasaralik[86];
        }
    }
    
    $yasaralik['toplam'] = $yasaralik[01] + $yasaralik[22] + $yasaralik[318] + $yasaralik[1945] + $yasaralik[4665] + $yasaralik[6685] + $yasaralik[86]; 
   
    
    
    
?>

<div class="panel panel-danger">
  <div class="panel-heading">Bu Ay İzlem Yapılan Hasta Yaş Grupları</div>

<table class="table">
          <thead class="thead-dark"> 

          <tr>
      <th scope="col">Yaş Aralığı</th>
      <th scope="col">Hasta Sayısı</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <th>0-1 Aylık</th>
    <td><?php echo $yasaralik[01];?></td>
    </tr>
     <tr>
    <th>2 Ay -2 Yaş</th>
    <td><?php echo $yasaralik[22];?></td>
    </tr>
     <tr>
    <th>3-18 Yaş</th>
    <td><?php echo $yasaralik[318];?></td>
    </tr>
    <tr>
    <th>19-45 Yaş</th>
    <td><?php echo $yasaralik[1945];?></td>
    </tr>
    <tr>
    <th>46-65 Yaş</th>
    <td><?php echo $yasaralik[4665];?></td>
    </tr>
    <tr>
    <th>66-85 Yaş</th>
    <td><?php echo $yasaralik[6685];?></td>
    </tr>
    <tr>
    <th>86 Yaş ve Üzeri</th>
    <td><?php echo $yasaralik[86];?></td>
    </tr>
    <tr>
    <th>TOPLAM:</th>
    <th><?php echo $yasaralik['toplam'];?></th>
    </tr>
    </tbody>
    </table>
</div>
<?php
}
/**
* Kullanıcı paneli: kullanıcı hakkında kısa bilgi
*/
function UserPanel() {
    global $my;
    
    $lastvisit = ($my->lastvisit == '0000-00-00 00:00:00') ? 'İlk Defa Giriş Yaptınız' : FormatDate($my->lastvisit);
    

    echo '<div class="panel panel-primary">';
    echo '<div class="panel-heading">Hoşgeldiniz '.$my->name.'</div>';
    echo '<div class="panel-body">';
    echo '<span><strong><u>Siteye Son Gelişiniz:</u></strong><br />'. $lastvisit.'</span><br />';
    echo '</div>';
    echo '</div>';
}

function GenelStats() {
    global $dbase;
    
    $today = date('d.m.Y');  
    $first = tarihCevir(date("01.m.Y", strtotime($today)));
    $last = tarihCevir(date("t.m.Y", strtotime($today)));
    
    /*
    * toplam ulaşılan hasta sayısı / aylık çalışma için
    */
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar");
    $totalul = $dbase->loadResult();
    /*
    * toplam kayıtlı hasta sayısı / aylık çalışma için
    */
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND cinsiyet='E'");
    $totaler = $dbase->loadResult() ? $dbase->loadResult() : '0';
    
    $dbase->setQuery("SELECT COUNT(id) FROM #__hastalar WHERE pasif='0' AND cinsiyet='K'");
    $totalka = $dbase->loadResult() ? $dbase->loadResult() : '0';
    /*
    * toplam çıkarılan hasta / aylık çalışma için
    */
    $dbase->setQuery("SELECT COUNT(cinsiyet) FROM #__hastalar WHERE pasif=1 AND cinsiyet='E' AND (pasiftarihi>='".$first."' AND pasiftarihi<='".$last."')");
    $totalper = $dbase->loadResult() ? $dbase->loadResult() : '0';
    
    $dbase->setQuery("SELECT COUNT(cinsiyet) FROM #__hastalar WHERE pasif=1 AND cinsiyet='K' AND (pasiftarihi>='".$first."' AND pasiftarihi<='".$last."')");
    $totalpka = $dbase->loadResult() ? $dbase->loadResult() : '0';
    /*
    * son ay takibe başlanan hasta / aylık çalışma için
    */
    $thisyear = date('Y');
    $thismonth = date('m');
    $dbase->setQuery("SELECT COUNT(cinsiyet) FROM #__hastalar WHERE cinsiyet='E' AND (pasif=0 AND kayityili='".$thisyear."' AND kayitay='".$thismonth."')");
    $sonayerkek = $dbase->loadResult() ? $dbase->loadResult() : '0';
    
    $dbase->setQuery("SELECT COUNT(cinsiyet) FROM #__hastalar WHERE cinsiyet='K' AND (pasif=0 AND kayityili='".$thisyear."' AND kayitay='".$thismonth."')");
    $sonaykadin = $dbase->loadResult() ? $dbase->loadResult() : '0';
    
    $totalta = $sonayerkek + $sonaykadin;
    
    //Tam bağımlı hasta sayısı
    /*
    *   $t[] = mosHTML::makeOption('2', 'Tam Bağımlı Hasta');
        $t[] = mosHTML::makeOption('1', 'Yarı Bağımlı Hasta');
        $t[] = mosHTML::makeOption('0', 'Bağımsız Hasta');
    */
    $dbase->setQuery("SELECT COUNT(*) FROM #__hastalar WHERE bagimlilik='2' AND pasif='0'");
    $toplambagimli = $dbase->loadResult();
    ?>
    <div class="panel panel-default">
  <div class="panel-heading">Genel İstatistikler</div>
 <table class="table table-striped">
 <thead>
 </thead>
 <tbody>
 
 <tr>
 <th>Toplam Ulaşılan Hasta Sayısı:(1 Mart 2024 ten bu yana)</th>
 <th><?php echo $totalul;?></th>
 </tr>

 <tr>
 <th>Toplam Aktif Kayıtlı Hasta Sayısı:</th>
 <th><a href="index.php?option=site&bolum=hastalar"><?php echo ($totaler+$totalka);?></a> ( <span>ERKEK <span class="label label-primary"><?php echo $totaler;?></span></span>  <span>KADIN <span class="label label-danger"><?php echo $totalka;?></span></span> )</th>
 </tr>
 
  <tr>
 <th>Bu Ay Takibe Başlanan Yeni Hasta Sayısı:</th>
 <th><a href="index.php?option=site&bolum=hastalar&kayityili=<?php echo date('Y');?>&kayitay=<?php echo date('m');?>"><?php echo $totalta;?></a> ( <span>ERKEK <span class="label label-primary"><?php echo $sonayerkek;?></span></span>  <span>KADIN <span class="label label-danger"><?php echo $sonaykadin;?></span></span> )</th>
 </tr>
 
 <tr>
 <th>Bu Ay Takipten Çıkarılan Hasta Sayısı:</th>
 <th><a href="index.php?option=site&bolum=phastalar"><?php echo ($totalper+$totalpka);?></a> ( <span>ERKEK <span class="label label-primary"><?php echo $totalper;?></span></span>  <span>KADIN <span class="label label-danger"><?php echo $totalpka;?></span></span> )</th>
 </tr>
      
 <tr>
 <th>Tam Bağımlı Hasta Sayısı:</th>
 <th><a href="index.php?option=site&bolum=hastalar&bagimlilik=2"><?php echo $toplambagimli;?></a></th>
 </tr>
 
 </tbody>
 </table>
 </div>
     <?php
}

function formButton($value, $onclick, $uyari=0) {
    $html = "";
    $html.= '<input type="button" name="button"';
    $html.= ' value="'.$value.'"';
    if ($uyari==1) {
    $html.= 'onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert(\'Lütfen listeden bir seçim yapın\'); } else {submitbutton(\''.$onclick.'\');}"';
    } elseif ($uyari==2) {
    $html.= 'onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert(\'Lütfen listeden bir seçim yapın\'); } else if (confirm(\'İşlemi onaylıyor musunuz?\')){ submitbutton(\''.$onclick.'\');}"';    
    } else {
    $html.= ' onclick="javascript:submitbutton(\''.$onclick.'\');"';
    }
    $html.= ' class="btn btn-default" />';
    
    return $html;
}
   
    function yas_bul($dogum_tarihi) {

//Unixin çıkışından(1 Ocak 1970 00:00:00 GMT) 
       //bu yana geçen zamanı saniye cinsinden aldık.
       $simdikiTarih = time();
      
       //"/" karakterine göre girilen tarihi
       // parse ettik
       $tarih = explode('.',$dogum_tarihi);
      
       //Kullanıcıdan aldığımız tarihi
       //saniye cinsinden ifade ettik
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
      
       //Saniye cinsinden iki sayıyı çıkardık
       $yas = $simdikiTarih - $tarih;
      
       //Saniye cinsinden olan sayıyı gün olarak ifade ettik
       $yas = floor($yas/(60*60*24));
      
       //Gün cinsinden olan sayıyı yıl olarak ifade ettik
       $yas = floor($yas/365);
      
       return $yas;
}

function tarihCevir($tarih, $dt=0) {
    
    if ($dt) {
     return date('d.m.Y', $tarih);   
    } else {
    return strtotime($tarih);
}
}
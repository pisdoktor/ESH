<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

function convertPage($secim) {
    global $mainframe, $dbase, $my;
    
    if ($my->isadmin) {
    $session = new Session($dbase);
    $session->load($mainframe->_session->session);

    $session->access_type = $secim;
    $session->update();
    
    Redirect('index.php');
    } else {
        NotAuth();
    }    
}  

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

<li><a href="<?php echo SITEURL;?>"><i class="fa-solid fa-bars-progress"></i> <span>Anasayfa</span></a></li>
<!--
<li><a href="index.php?option=site&bolum=calendar"><i class="fa-solid fa-calendar-days"></i> İşlem Takvimi</a></li>
<li><a href="index.php?option=site&bolum=profil&task=my"><span>Profilim</span></a></li>
-->
<li class="has-sub"><a href="#"><i class="fa-solid fa-people-roof"></i> <span>Hasta İşlemleri</span></a>
<ul>
<li><a href="index.php?option=site&bolum=hastalar"><i class="fa-solid fa-person-cane"></i> <span>Aktif Hasta Listesi</span></a></li>
<li><a href="index.php?option=site&bolum=phastalar"><i class="fa-solid fa-bed-pulse"></i> <span>Pasif Hasta Listesi</span></a></li> 
<li><a href="index.php?option=site&bolum=hastalar&task=new"><i class="fa-solid fa-square-plus"></i> <span>Yeni Hasta Kayıt</span></a></li>
</ul>
</li>


<li><a href="index.php?option=site&bolum=izlemler"><i class="fa-solid fa-file-medical"></i> <span>Aktif İzlemler</span></a></li>


<!--
<li><a href="index.php?option=site&bolum=pansuman"><i class="fa-solid fa-eye"></i> Pansuman Listesi</a></li>
-->

<?php 
if ($my->isadmin) {
?>
<li><a href="index.php?option=admin"><i class="fa-solid fa-chart-line"></i> <span>Yönetim Paneli</span></a></li>
<?php    
}
?>
<li><a href="index.php?option=logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Çıkış Yap</span></a></li>    
</ul>
</div>
<div id="messages"></div>
<?php
}
}

function adminMenu() {
    ?>
<div id="cssmenu">
<ul>
<li><a href="<?php echo SITEURL;?>"><i class="fa-solid fa-bars-progress"></i> <span>Anasayfa</span></a></li>

<li class="has-sub"><a href="#"><i class="fa-solid fa-people-roof"></i> <span>Hasta İşlemleri</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=hastalar"><i class="fa-solid fa-person-cane"></i> <span>Tüm Hasta Listesi</span></a></li>
<li><a href="index.php?option=admin&bolum=hastalar&task=new"><i class="fa-solid fa-square-plus"></i> <span>Yeni Hasta Kayıt</span></a></li>
<li><a href="index.php?option=admin&bolum=stats&task=hgirilmeyen"><i class="fa-solid fa-circle-half-stroke"></i> <span>Bilgileri Eksik Hastalar</span></a></li> 
<li><a href="index.php?option=admin&bolum=stats&task=izlenmeyen"><i class="fa-solid fa-hospital-user"></i> <span>İzlenmeyen Hastalar</span></a></li>
<li><a href="index.php?option=admin&bolum=stats&task=izlemolmayan"><i class="fa-solid fa-file-medical"></i> <span>Hiç İzlem Girilmemişler</span></a></li>
<li class="has-sub"><a href="#"><i class="fa-solid fa-clock"></i> <span>Takip Listeleri</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=stats&task=sondadegisim"><i class="fa-solid fa-vial"></i> <span>Sonda Değişimi Takip</span></a></li>
<li><a href="index.php?option=admin&bolum=stats&task=mamarapor"><i class="fa-solid fa-bowl-food"></i> <span>Mama Raporu Takip</span></a></li>
<li><a href="index.php?option=admin&bolum=stats&task=bezrapor"><i class="fa-solid fa-boxes-packing"></i> <span>Bez Raporu Takip</span></a></li>
<li><a href="index.php?option=admin&bolum=stats&task=ilacrapor"><i class="fa-solid fa-pills"></i> <span>İlaç Raporu Takip</span></a></li>
<li></li>
</ul>
</li>
<li  class="has-sub"><a href="index.php?option=admin&bolum=olumbildirim"><i class="fa-solid fa-dice-d20"></i> <span>Ölen Hasta Tarama</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=olumbildirim&task=olenler"><i class="fa-solid fa-triangle-exclamation"></i> <span>Muhtemel Ölenler</span></a></li>
</ul>
</li>
</ul>
</li>

<li class="has-sub"><a href="#"><i class="fa-solid fa-book-medical"></i> <span>İzlem İşlemleri</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=izlemler"><i class="fa-solid fa-file-medical"></i> <span>Yapılan İzlemler</span></a></li>
<li><a href="index.php?option=admin&bolum=yizlemler"><i class="fa-solid fa-file-medical"></i> <span>Yapılmayan İzlemler</span></a></li>
<li><a href="index.php?option=admin&bolum=pizlemler"><i class="fa-solid fa-receipt"></i> <span>Planlı İzlemler</span></a></li>
</ul>
</li>

<li><a href="index.php?option=admin&bolum=pansuman"><i class="fa-solid fa-eye"></i> <span>Pansuman Listesi</span></a></li>


<li class="has-sub"><a href="#"><i class="fa-solid fa-chart-pie"></i> <span>İstatistikler</span></a>
<ul>

<li><a href="index.php?option=admin&bolum=stats&task=temel"><i class="fa-solid fa-chart-gantt"></i> <span>Aylık İzlem Sıklığı</span></a></li>
<!-- mahalleye göre istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=hmahalle"><i class="fa-solid fa-chart-column"></i> <span>Mahalle İstatistiği</span></a></li>
<!-- kayıt yılına göre istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=kayityili"><i class="fa-solid fa-chart-bar"></i> <span>Kayıt Yılı İstatistiği</span></a></li>
<!-- kayıt ayına göre istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=kayitayi"><i class="fa-solid fa-chart-bar"></i> <span>Kayıt Ayı İstatistiği</span></a></li>
<!-- hastalıklara göre istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=hastalik"><i class="fa-solid fa-chart-area"></i> <span>Hastalık İstatistiği</span></a></li>
<!-- özelliği olan hasta istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=special"><i class="fa-solid fa-ranking-star"></i> <span>Özellik İstatistiği</span></a></li>

<li><a href="index.php?option=admin&bolum=stats&task=adres"><i class="fa-solid fa-globe"></i> <span>Adres İstatistiği</span></a></li>

<li><a href="harita.php" target="_blank"><i class="fa-solid fa-earth-americas"></i> <span>Hasta Adres Haritası</span></a></li>    

<li><a href="index.php?option=admin&bolum=stats&task=islem"><i class="fa-solid fa-file-invoice"></i> <span>Tarih Bazlı İşlemler</span></a></li> 

<li><a href="index.php?option=admin&bolum=stats&task=personel"><i class="fa-solid fa-square-poll-vertical"></i> <span>Tarih Bazlı Personeller</span></a></li>



</ul>
</li>



<li class="has-sub"><a href="#"><i class="fa-solid fa-clipboard-user"></i> <span>Genel İşlemler</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=user"><i class="fa-solid fa-users"></i> <span>Kullanıcı Yönetimi</span></a></li>

<li><a href="index.php?option=admin&bolum=islem"><i class="fa-solid fa-book-journal-whills"></i> <span>İşlem Yönetimi</span></a></li> 

<li class="has-sub"><a href="#"><i class="fa-solid fa-bacterium"></i> <span>Hastalık İşlemleri</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=hastaliklarcat"><i class="fa-solid fa-bacterium"></i> <span>Hastalık Kategorileri</span></a></li>
<li><a href="index.php?option=admin&bolum=hastaliklar"><i class="fa-solid fa-bacterium"></i> <span>Hastalıklar Listesi</span></a></li>
<li><a href="index.php?option=admin&bolum=branslar"><i class="fa-solid fa-file-invoice"></i> <span>Branşlar Listesi</span></a></li> 
</ul>
</li>


<li class="has-sub"><a href="#"><i class="fa-solid fa-address-book"></i> <span>Adres Yönetimi</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=ilce"><i class="fa-solid fa-sign-hanging"></i> <span>İlçe Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=mahalle"><i class="fa-solid fa-house"></i> <span>Mahalle Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=sokak"><i class="fa-solid fa-road"></i> <span>Sokak Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=kapino"><i class="fa-solid fa-door-open"></i> <span>Kapı No Yönetimi</span></a></li>
</ul>
</li>


<li><a href="index.php?option=admin&bolum=db"><i class="fa-solid fa-database"></i> <span>Veritabanı Yönetimi</span></a></li>

<li><a href="index.php?option=admin&bolum=ayarlar"><i class="fa-solid fa-gears"></i> <span>Yapılandırma</span></a></li>


<li><a href="index.php?option=admin&bolum=dosyalama"><i class="fa-regular fa-folder-open"></i> <span>Dosyalama Sistemi</span></a></li>
<li><a href="index.php?option=admin&bolum=planlama"><i class="fa-regular fa-folder-open"></i> <span>Bölge Planlama</span></a></li>


</ul>
</li>


<li><a href="index.php?option=site"><i class="fa-solid fa-clipboard-user"></i> <span>Siteye Geç</span></a></li>
<li><a href="index.php?option=logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Çıkış Yap</span></a></li>    
</ul>
</div>
<?php
}

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
    convertPage('admin');
    break;
    }
}

function loadAdminModule() {
    global $option, $bolum, $task;
    global $id, $cid;
    global $limit, $limitstart;
    global $mainframe, $my, $mosmsg;
    
    switch($option) {
    default:
    AdminPanelMenu();
    break;
    
    case 'admin':
    if ($bolum) {
    include_once(ABSPATH. '/admin/modules/'.$bolum.'/index.php');
    } else {
        Redirect('index.php');
    }
    break;
    
    case 'site':
    convertPage('site');
    break;
}
}

function MainPage() {
    
include(ABSPATH.'/site/modules/takvim/index.php');

}

function AdminPanelMenu() {
    ?>
    <div class="panel panel-default">
    <div class="panel-heading"><h4><i class="fa-solid fa-chart-line"></i> Yönetim Paneli</h4></div>
    <div class="panel-body">
    <div class="col-sm-4">
    <?php izlemSikligi();?>
    <?php GenelStats();?>
    <?php loadCikarilmaNedeni();?> 
    </div>                
    
    <div class="col-sm-8">
    <?php hYasGruplari();?>
    <?php dogumGunuGetir();?>
    
    </div>
    </div>
    </div>
    <?php
}

/*İstatistikler*/
function dogumGunuGetir() {
    global $dbase, $my;
    
    $buay = date('m');
    $bugun = date('d');
    
    $query = "SELECT h.id, h.isim, h.soyisim, h.cinsiyet, h.tckimlik, h.dogumtarihi, m.mahalle, ilc.ilce FROM #__hastalar AS h "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce "
    . "\n WHERE h.pasif=0";
    $dbase->setQuery($query);
    
    $rows = $dbase->loadObjectList();
    
    $list = array();
    
    foreach ($rows as $row) {
     $m = explode('.', $row->dogumtarihi);
     $row->dogumgun = $m[2];
     $row->dogumay = $m[1];
     
     if ($row->dogumay == $buay && $row->dogumgun == $bugun) {
         $list[$row->id]['id'] = $row->id;
         $list[$row->id]['isim'] = $row->isim." ".$row->soyisim;
         $list[$row->id]['gun'] = $row->dogumgun;
         $list[$row->id]['tc'] = $row->tckimlik;
         $list[$row->id]['mahalle'] = $row->mahalle;
         $list[$row->id]['ilce'] = $row->ilce;
         $list[$row->id]['cinsiyet'] = $row->cinsiyet;
         
         $tarih = explode('.',$row->dogumtarihi);
         $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
         $list[$row->id]['dtarihi'] = strftime("%d.%m.%Y", $tarih);
         $list[$row->id]['dogumtarihi'] = $row->dogumtarihi; 
     }
     }
?>
<div class="panel panel-default">
        <div class="panel-heading"><h4><i class="fa-solid fa-cake-candles"></i> Bugün Doğum Günü Olan Hastalar</h4></div>
              
    <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Hasta Adı</th>
      <th scope="col">Hasta TC Kimlik</th>
      <th scope="col">Mahalle</th> 
      <th scope="col">Doğum Tarihi</th> 
      <th scope="col">Yaşı</th> 
    </tr>
  </thead>
  <tbody>

  <?php   

   foreach($list as $v=>$k) {
      ?>
      
       <tr>
       <th><a href="index.php?option=<?php echo $my->access_type;?>&bolum=hastalar&task=show&id=<?php echo $k['id'];?>"><span style="color:<?php echo $k['cinsiyet'] == 'E' ? 'blue':'#f5070f';?>"><?php echo $k['isim'];?></span></a></th>
      <td><?php echo $k['tc'];?></td>
      <td><?php echo $k['mahalle'];?> <span class="label label-success"><?php echo $k['ilce'];?></span></td>
      <td><?php echo $k['dtarihi'];?></td>
       <td><?php echo yas_bul($k['dogumtarihi']);?></td> 
    </tr>
 <?php 

  }?>
    </tbody>
</table>
   </div>
<?php    
}

function hYasGruplari() {
    global $dbase;
    
    $today = date('Y.m.d');
    
    $aralik[01] = date('Y.m.d', strtotime('-1 month'));
    
    $aralik[02] = date('Y.m.d', strtotime('-2 months'));
    
    $aralik[22] = date('Y.m.d', strtotime('-2 years'));
    
    $aralik[318] = date('Y.m.d', strtotime('-18 years'));
    
    $aralik[1945] = date('Y.m.d', strtotime('-45 years'));
    
    $aralik[4665] = date('Y.m.d', strtotime('-65 years'));
     
    $aralik[85] = date('Y.m.d', strtotime('-85 years'));
    
    //yaşlarına göre gruplandıralım
    $dbase->setQuery("SELECT dogumtarihi FROM #__hastalar WHERE pasif='0' AND cinsiyet='E'");
    $tarihler['E'] = $dbase->loadResultArray();
    
    $dbase->setQuery("SELECT dogumtarihi FROM #__hastalar WHERE pasif='0' AND cinsiyet='K'");
    $tarihler['K'] = $dbase->loadResultArray();
    
    $yasaralik = array();
    
    //0-1 ay
    $yasaralik[01]['K'] = 0; 
    $yasaralik[01]['E'] = 0;
    // 2 ay-2 yaş
    $yasaralik[22]['K'] = 0;
    $yasaralik[22]['E'] = 0;
    //3-18 yaş
    $yasaralik[318]['K'] = 0;
    $yasaralik[318]['E'] = 0;
    //19-45 yaş
    $yasaralik[1945]['K'] = 0;
    $yasaralik[1945]['E'] = 0;
    //46-65 yaş
    $yasaralik[4665]['K'] = 0;
    $yasaralik[4665]['E'] = 0;
    //66-85 yaş
    $yasaralik[6685]['K'] = 0;
    $yasaralik[6685]['E'] = 0;
    // 86 ve üzeri
    $yasaralik[86]['K'] = 0;
    $yasaralik[86]['E'] = 0;
    
    $yasaralik['toplam'] = array();
    
    foreach ($tarihler['E'] as $dtarih) {
        
        //$yas = yas_bul($dtarih);
        
        //0 ve 1 aylık hastalar
        if ($dtarih <= $today && $dtarih >= $aralik[01]) {
            ++$yasaralik[01]['E'];
        }
        
        //2 ay ve 2 yaş arası
        if ($dtarih <= $aralik[02] && $dtarih >= $aralik[22]) {
           ++$yasaralik[22]['E'];
        }
        
        //3 yaş 18 yaş arası
        if ($dtarih < $aralik[22] && $dtarih >= $aralik[318]) {
           ++$yasaralik[318]['E'];
        }
        
        //19 yaş 45 yaş arası
        if ($dtarih < $aralik[318] && $dtarih >= $aralik[1945]) {
           ++$yasaralik[1945]['E']; 
        }
        
        //46 yaş 65 yaş arası
        if ($dtarih < $aralik[1945] && $dtarih >= $aralik[4665]) {
          ++$yasaralik[4665]['E'];  
        }
        
        //66 yaş ve 85 yaş arası
        if ($dtarih < $aralik[4665] && $dtarih >= $aralik[85]) {
          ++$yasaralik[6685]['E'];  
        }
        
        //86 yaş ve üzeri
        if ($dtarih < $aralik[85]) {
          ++$yasaralik[86]['E'];
        }
    }
    
    foreach ($tarihler['K'] as $dtarih) {
        
        //$yas = yas_bul($dtarih);
        
       //0 ve 1 aylık hastalar
        if ($dtarih <= $today && $dtarih >= $aralik[01]) {
            ++$yasaralik[01]['K'];
        }
        
        //2 ay ve 2 yaş arası
        if ($dtarih <= $aralik[02] && $dtarih >= $aralik[22]) {
           ++$yasaralik[22]['K'];
        }
        
        //3 yaş 18 yaş arası
        if ($dtarih < $aralik[22] && $dtarih >= $aralik[318]) {
           ++$yasaralik[318]['K'];
        }
        
        //19 yaş 45 yaş arası
        if ($dtarih < $aralik[318] && $dtarih >= $aralik[1945]) {
           ++$yasaralik[1945]['K']; 
        }
        
        //46 yaş 65 yaş arası
        if ($dtarih < $aralik[1945] && $dtarih >= $aralik[4665]) {
          ++$yasaralik[4665]['K'];  
        }
        
        //66 yaş ve 85 yaş arası
        if ($dtarih < $aralik[4665] && $dtarih >= $aralik[85]) {
          ++$yasaralik[6685]['K'];  
        }
        
        //86 yaş ve üzeri
        if ($dtarih < $aralik[85]) {
          ++$yasaralik[86]['K'];
        }
    }
    
    $yasaralik['toplam']['E'] = $yasaralik[01]['E'] + $yasaralik[22]['E'] + $yasaralik[318]['E'] + $yasaralik[1945]['E'] + $yasaralik[4665]['E'] + $yasaralik[6685]['E'] + $yasaralik[86]['E']; 
    $yasaralik['toplam']['K'] = $yasaralik[01]['K'] + $yasaralik[22]['K'] + $yasaralik[318]['K'] + $yasaralik[1945]['K'] + $yasaralik[4665]['K'] + $yasaralik[6685]['K'] + $yasaralik[86]['K']; 
    
     ?>
         <div class="panel panel-default">
        <div class="panel-heading"><i class="fa-solid fa-chart-simple"></i> Yaş Gruplarına Göre Aktif Hasta Sayıları</div>
                 <div>                         
  <canvas id="myChart"></canvas>
</div>

<script src="<?php echo SITEURL;?>/admin/modules/stats/chart.js"></script>

<script>

  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data:{
      labels: ['0-1 Aylık', '2 Ay-2 Yaş', '3-18 Yaş', '19-45 Yaş', '46-65 Yaş', '66-85 Yaş', '85 Yaş Üzeri'],
      datasets: [{
        label: 'Kadın Sayısı',
        data: [<?php echo $yasaralik[01]['K'].','.$yasaralik[22]['K'].','.$yasaralik[318]['K'].','.$yasaralik[1945]['K'].','.$yasaralik[4665]['K'].','.$yasaralik[6685]['K'].','.$yasaralik[86]['K'];?>],
        backgroundColor: 'pink',
        borderWidth: 1,
        stack: '1'
      },
      {
      label: 'Erkek Sayısı',
        data: [<?php echo $yasaralik[01]['E'].','.$yasaralik[22]['E'].','.$yasaralik[318]['E'].','.$yasaralik[1945]['E'].','.$yasaralik[4665]['E'].','.$yasaralik[6685]['E'].','.$yasaralik[86]['E'];?>],
        backgroundColor: 'blue',
        borderWidth: 1,
        stack: '1'
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
         <table class="table table-striped">
          <thead class="thead-dark"> 
          <tr>
      <th scope="col">Yaş Aralığı</th>
      <th scope="col">Kadın Hasta Sayısı</th>
      <th scope="col">Erkek Hasta Sayısı</th>
      <th scope="col">Toplam Hasta Sayısı</th>
    </tr>
    </thead>
    
    <tbody>
    <tr>
    <th>0-1 Aylık</th>
    <td><?php echo $yasaralik[01]['K'];?></td>
    <td><?php echo $yasaralik[01]['E'];?></td>
    <td><?php echo $yasaralik[01]['K']+$yasaralik[01]['E'];?></td>
    </tr>
    
     <tr>
    <th>2 Ay-2 Yaş</th>
    <td><?php echo $yasaralik[22]['K'];?></td>
    <td><?php echo $yasaralik[22]['E'];?></td>
    <td><?php echo $yasaralik[22]['K']+$yasaralik[22]['E'];?></td> 
    </tr>
    
     <tr>
    <th>3-18 Yaş</th>
    <td><?php echo $yasaralik[318]['K'];?></td>
    <td><?php echo $yasaralik[318]['E'];?></td>
    <td><?php echo $yasaralik[318]['K']+$yasaralik[318]['E'];?></td> 
    </tr>
    
    <tr>
    <th>19-45 Yaş</th>
    <td><?php echo $yasaralik[1945]['K'];?></td>
    <td><?php echo $yasaralik[1945]['E'];?></td>
    <td><?php echo $yasaralik[1945]['K']+$yasaralik[1945]['E'];?></td>
    </tr>
    
    <tr>
    <th>46-65 Yaş</th>
    <td><?php echo $yasaralik[4665]['K'];?></td>
    <td><?php echo $yasaralik[4665]['E'];?></td>
    <td><?php echo $yasaralik[4665]['K']+$yasaralik[4665]['E'];?></td>
    </tr>
    
    <tr>
    <th>66-85 Yaş</th>
    <td><?php echo $yasaralik[6685]['K'];?></td>
    <td><?php echo $yasaralik[6685]['E'];?></td>
    <td><?php echo $yasaralik[6685]['K']+$yasaralik[6685]['E'];?></td>
    </tr>
    
    <tr>
    <th>86 Yaş ve Üzeri</th>
    <td><?php echo $yasaralik[86]['K'];?></td>
    <td><?php echo $yasaralik[86]['E'];?></td>
    <td><?php echo $yasaralik[86]['K']+$yasaralik[86]['E'];?></td>
    </tr>
    </tbody> 
    <tfoot>
    <tr>
    <th>TOPLAM</th>
    <th><?php echo $yasaralik['toplam']['K'];?></th>
    <th><?php echo $yasaralik['toplam']['E'];?></th>
    <th><?php echo $yasaralik['toplam']['K']+$yasaralik['toplam']['E'];?></th>
    </tr>
    </tfoot> 
    
    </table>
        
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
     $where[] = 'i.yapildimi=1';
 
     
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
  <div class="panel-heading"><i class="fa-solid fa-list"></i> Bu Ayki İzlem Sıklığı</div>
  <table class="table table-rounded">
  <tr>
  <th>TOPLAM HASTA</th>
  <th>TOPLAM İZLEM</th>
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
    '1' => 'İyileşme',
    '2' => 'Vefat',
    '3' => 'İkamet Değişikliği',
    '4' => 'Tedaviyi Reddetme',
    '5' => 'Tedaviye Yanıt Alamama',
    '6' => 'Sonlandırmanın Talep Edilmesi',
    '7' => 'Tedaviye Personel Gerekmemesi',
    '8' => 'ESH Takibine Uygun Olmaması'
    );
    ?>
<div class="panel panel-danger">
  <div class="panel-heading"><i class="fa-solid fa-share-from-square"></i> Bu Ay Takipten Çıkarılma Nedenleri</div>
  
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
        
        if ($yas <= 1) {
            ++$yasaralik[01];
        }
        
        if ($yas > 1 && $yas < 3) {
           ++$yasaralik[22];
        }
        
        if ($yas > 2 && $yas <= 18) {
           ++$yasaralik[318];
        }
        
        if ($yas > 18 && $yas <= 45) {
           ++$yasaralik[1945]; 
        }
        
        if ($yas > 45 && $yas <= 65) {
          ++$yasaralik[4665];  
        }
        if ($yas > 65 && $yas <= 85) {
          ++$yasaralik[6685];  
        }
        
        if ($yas > 85) {
          ++$yasaralik[86];
        }
    }
    
    $yasaralik['toplam'] = $yasaralik[01] + $yasaralik[22] + $yasaralik[318] + $yasaralik[1945] + $yasaralik[4665] + $yasaralik[6685] + $yasaralik[86]; 
   
    
    
    
?>

<div class="panel panel-danger">
  <div class="panel-heading"><i class="fa-solid fa-users"></i> Bu Ay İzlenen Yaş Grupları</div>

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
    echo '<div class="panel-heading"><i class="fa-solid fa-user"></i> Hoşgeldiniz '.$my->name.'</div>';
    echo '<div class="panel-body">';
    echo '<span><strong><u>Siteye Son Gelişiniz:</u></strong><br />'. $lastvisit.'</span><br />';
    echo '</div>';
    echo '</div>';
}
/**
* Genel istatistikler
*/
function GenelStats() {
    global $dbase, $my;
    
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
  <div class="panel-heading"><i class="fa-solid fa-chart-simple"></i> Genel İstatistikler</div>
 <table class="table table-striped">
 <thead>
 </thead>
 <tbody>
 
 <tr>
 <th colspan="3">Toplam Ulaşılan Hasta Sayısı:</th>
 </tr>
 <tr>
 <td colspan="3"><?php echo $totalul;?></td>
 </tr>

 <tr>
 <th colspan="3">Aktif Kayıtlı Hasta Sayısı:</th>
 </tr>
 <tr>
 <td>TOPLAM <span class="label label-warning"><a href="index.php?option=<?php echo $my->access_type;?>&bolum=hastalar&pasif=2"><?php echo ($totaler+$totalka);?></a></span></td>
 <td>ERKEK <span class="label label-primary"><?php echo $totaler;?></span></td>
 <td>KADIN <span class="label label-danger"><?php echo $totalka;?></span></td>
 </tr>
 
  <tr>
 <th colspan="3">Bu Ay Takibe Başlananlar:</th>
 </tr>
 <tr>
 <td>TOPLAM <span class="label label-warning"><a href="index.php?option=<?php echo $my->access_type;?>&bolum=hastalar&kayityili=<?php echo date('Y');?>&kayitay=<?php echo date('m');?>"><?php echo $totalta;?></a></span></td>
 <td>ERKEK <span class="label label-primary"><a href="index.php?option=<?php echo $my->access_type;?>&bolum=hastalar&kayityili=<?php echo date('Y');?>&kayitay=<?php echo date('m');?>&cinsiyet=E"><?php echo $sonayerkek;?></a></span></td>
 <td>KADIN <span class="label label-danger"><a href="index.php?option=<?php echo $my->access_type;?>&bolum=hastalar&kayityili=<?php echo date('Y');?>&kayitay=<?php echo date('m');?>&cinsiyet=K"><?php echo $sonaykadin;?></a></span></td>
 </tr>
 
 <tr>
 <th colspan="3">Bu Ay Takipten Çıkarılanlar:</th>
 </tr>
 <tr>
 <td>TOPLAM <span class="label label-warning"><a href="index.php?option=<?php echo $my->access_type;?>&bolum=phastalar&baslangictarih=<?php echo tarihCevir($first,1);?>&bitistarih=<?php echo tarihCevir($last,1);?>"><?php echo ($totalper+$totalpka);?></a></span></td>
 <td>ERKEK <span class="label label-primary"><?php echo $totalper;?></span></td>
 <td>KADIN <span class="label label-danger"><?php echo $totalpka;?></span></td>
 </tr>
      
 <tr>
 <th colspan="3">Tam Bağımlı Hasta Sayısı:</th>
 </tr>
 <tr>
 <td colspan="3"><a href="index.php?option=<?php echo $my->access_type;?>&bolum=hastalar&bagimlilik=2&pasif=2"><?php echo $toplambagimli;?></a></td>
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

function bkiHesapla($kilo, $boy) {
/**
18, 5 kg/m² ‘nin altındaki sonuçlar: Zayıf
18, 5 kg/m² ile 24, 9 kg/m² arasındaki sonuçlar: Sağlıklı
25 kg/m² ile 29, 9 kg/m² arasındaki sonuçlar: Şişman
30 kg/m² ile 39, 9 kg/m² arasındaki sonuçlar: Obez
40 kg/m² üzerindeki sonuçlar: Aşırı obez (Morbid obez)
**/
if (!$boy || !$kilo) {
    $bki = 0;
} else {
    $bki = round($kilo/($boy*$boy), 1);
}
    
    $text = '';
    
    if ($bki <= '18.5') {
      $text = 'Zayıf';
    } else if ($bki > '18.5' && $bki <= '24.9') {
        $text = 'Sağlıklı';
    } else if ($bki > '24.9' && $bki <= '29.9') {
        $text = 'Şişman';
    } else if ($bki > '29.9' && $bki <= '39.9') {
        $text = 'Obez';
    } else if ($bki >= '39.9') {
        $text = 'Aşırı Obez';
    }

    return $bki." (".$text.")";
}

function tarihCevir($tarih, $dt=0) {
    
    if ($dt) {
     return date('d.m.Y', $tarih);   
    } else {
    return strtotime($tarih);
}
}

function pasifNedeni($secim) {
    
    $pasifmi = array('1' => 'İyileşme', '2' => 'Vefat', '3' => 'İkamet Değişikliği (GÖÇ)', '4' => 'Tedaviyi Reddetme','5' => 'Tedaviye Yanıt Alamama','6' => 'Sonlandırmanın Talep Edilmesi','7' => 'Tedaviye Personel Gerekmemesi','8' => 'ESH Takibine Uygun Olmaması');
    
    return $pasifmi[$secim];
}

function aySecimi($secim) {
    
    $aylar = array('01' => 'Ocak', '02' => 'Şubat', '03' => 'Mart', '04' => 'Nisan', '05' => 'Mayıs', '06' => 'Haziran', '07' => 'Temmuz', '08' => 'Ağustos', '09' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık');
    
    return $aylar[$secim];
}

function bagimlilikDurumu($secim) {
    $bagimli = array('3' => 'Bağımsız', '1' => 'Yarı Bağımlı', '2' => 'Tam Bağımlı');
    
    return $bagimli[$secim];
}

function olumBildirimi($row) {
    
    //2,5 yıl önceki tarih 
    $tarih = date('d.m.Y', strtotime('-30 month'));
    $tarihgetir = tarihCevir($tarih);
    
    $link = "http://mezarlik.denizli.bel.tr/sorgu.ashx?islem=definListesiGetir";
    $link.= "&ad=".trim($row->isim);
    $link.= "&soyad=".trim($row->soyisim);
    $link.= "&anneAd=".trim($row->anneAdi);
    $link.= "&babaAd=".trim($row->babaAdi);
    
    $doc = new DOMDocument();
    $doc->loadHTMLFile($link);
    $html = $doc->saveHTML();
    /**
    * @desc json_decode fonksiyonu ile veri parse ettirebilir miyiz?
    * denenecek!!!
    */
    
    $olumTimestamp = '';
    
    if (strlen($html) > 3) {
    
    $html = preg_split('/[{*}]/', $html);
    
    $tarih = explode(',', $html[1]);
    $olumTarihi = explode(':', $tarih[8]);
    
    $olumTarihi = preg_split('/["*"]/', $olumTarihi[1]);
    
    $olumTimestamp = strtotime($olumTarihi[1]);

    } 
    
    return ($olumTimestamp > $tarihgetir) ? $olumTarihi[1]:''; 
}

function getMonthlyTakvim() {
    global $dbase;
    
    $query = "SELECT i.*, h.isim, h.soyisim, isl.islemadi, m.mahalle, isl.id AS islem FROM #__izlemler AS i "
     . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
     . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilacak "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . "\n WHERE planli=1 AND h.pasif=0 "
     . "\n GROUP BY i.id ";
     $dbase->setQuery($query);
     $rows = $dbase->loadObjectList();
     
     $data = array();
     
     $colors = array('#00DF50', '#CACACA', '#DADADA', '#8a9290', '#6z8078', '#FAFAFA', 
     '#0A2F51', '#137177', '#1D9A6C', '#39A96B', '#74C67A', '#A5AF2A',
     '#FBBACC', '#609504', '#EB942E', '#811A70', '#A093CF', '#BA4383',
     '#EA5E0B', '#FAA6DA', '#7a8189', '#BFE1B0');
     
     foreach ($rows as $row) {
         $data[] = "{
         title:'".$row->isim." ".$row->soyisim." (".$row->islemadi.") [".$row->hastatckimlik."] [".$row->mahalle."]',
         start:'".date('Y-m-d', $row->planlanantarih)."',
         url: 'index.php?option=site&bolum=pizlemler&task=edit&id=".$row->id."',
         backgroundColor: '".$colors[$row->islem]."'
         }";
     }
     ?>
   <script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        
      headerToolbar: {
        left: 'prev,next,today',
        center: 'title',
        right: 'listDay,listWeek,dayGridMonth'
      },

      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        dayGridMonth: { buttonText: 'Aylık Liste' },
        listWeek: { buttonText: 'Haftalık Liste' },
        listDay: { buttonText: 'Günlük Liste' }
      },

      initialView: 'listDay',
      initialDate: '<?php echo date('Y-m-d');?>',
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      dayMaxEvents: true, // allow "more" link when too many events
      events: [<?php echo implode(',', $data);?>]
    });
    calendar.setOption('locale', 'tr');
    calendar.render();
  });
  
</script>

<div class="panel panel-default">
    <div class="panel-heading">
     <div class="row">
        <div class="col-xs-10"><h4><i class="fa-solid fa-calendar-days"></i> Planlanan İzlemler</h4></div>
        <div class="col-xs-2" align="right"><a href="index.php?option=site&bolum=pizlemler&task=list" class="btn btn-sm btn-warning">Listeyi Göster</a></div>
    </div>
    </div>
    <div class="panel-body">
    <div id='calendar'></div>
    </div>
</div>
   <?php
   }
/**
* @desc Barthel Fonksiyonları
*/
function barthelHesapla($id) {
    global $dbase;
    
    $dbase->setQuery("SELECT barbakim, barbanyo, barbarsak, barbeslenme, bargiyinme, barmerdiven, barmesane, barmobilite, bartransfer, bartuvalet FROM #__hastalar WHERE id=".$id);
    $dbase->loadObject($row);
        
        $total = $row->barbakim + $row->barbanyo + $row->barbarsak + $row->barbeslenme + $row->bargiyinme + $row->barmerdiven + $row->barmesane + $row->barmobilite + $row->bartransfer + $row->bartuvalet;
        /*
        * 0-20 TAM BAĞIMLI
        * 21-61 İLERİ DERECEDE BAĞIMLI
        * 62-90 ORTA DERECEDE BAĞIMLI
        * 91-99 HAFİF BAĞIMLI
        * 100 BAĞIMSIZ
        */
        
        if ($total < 21) {
        return 'Tam Bağımlı ('.$total.')';
        } else if ($total >= 21 && $total < 62 ) {
        return 'İleri Derecede ('.$total.')';
        } else if ($total >= 62 && $total < 91) {
        return 'Orta Derecede ('.$total.')';
        } else if ($total >= 91 && $total < 100) {
        return 'Hafif Derecede ('.$total.')';
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
      
       //Saniye cinsinden olan sayıyı yıl olarak ifade ettik
       $yas = floor($yas/(60*60*24*365));
    /*
    $parca = explode('.', $dogum_tarihi);
    $gun = $parca[2];
    $ay = $parca[1];
    $yil = $parca[0];

    $yas = date('Y') - $yil;

    if (date('m') < $ay) {
        $yas--;
    }
    
    else if (date('d') < $gun) {
        $yas--;
    }           
    */
    return $yas;
}

/**
* @desc
* 
* olumBildirimi($hasta) ? '<span style="color:red"><i class="fa-solid fa-skull"></i> Ölmüş Olabilir <i class="fa-solid fa-exclamation"></i></span>':'';
*  
*/ 
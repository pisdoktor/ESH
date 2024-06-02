<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );  

function adminMenu() {
	?>
<div id="cssmenu">
<ul>
<li><a href="<?php echo SITEURL;?>"><i class="fa-solid fa-bars-progress"></i> <span>Anasayfa</span></a></li>

<li class="has-sub"><a href="#"><i class="fa-solid fa-clipboard-user"></i> <span>Temel İşlemler</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=user"><i class="fa-solid fa-users"></i> <span>Kullanıcı Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=islem"><i class="fa-solid fa-book-journal-whills"></i> <span>İşlem Yönetimi</span></a></li> 
<li><a href="index.php?option=admin&bolum=db"><i class="fa-solid fa-database"></i> <span>Veritabanı Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=ayarlar"><i class="fa-solid fa-gears"></i> <span>Yapılandırma</span></a></li>
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
<li class="has-sub"><a href="#"><i class="fa-solid fa-chart-pie"></i> <span>İstatistikler</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=stats&task=izlenmeyen"><i class="fa-solid fa-hospital-user"></i> <span>İzlenmeyen Hastalar</span></a></li>

<li><a href="index.php?option=admin&bolum=stats&task=temel"><i class="fa-solid fa-chart-gantt"></i> <span>Aylık İzlem Sıklığı</span></a></li>
<!-- mahalleye göre istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=hmahalle"><i class="fa-solid fa-chart-column"></i> <span>Mahalle İstatistiği</span></a></li>
<!-- kayıt yılına göre istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=kayityili"><i class="fa-solid fa-chart-bar"></i> <span>Kayıt Yılı İstatistiği</span></a></li>
<!-- hastalıklara göre istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=hastalik"><i class="fa-solid fa-chart-area"></i> <span>Hastalık İstatistiği</span></a></li>
<!-- özelliği olan hasta istatistikler -->
<li><a href="index.php?option=admin&bolum=stats&task=special"><i class="fa-solid fa-ranking-star"></i> <span>Özellik İstatistiği</span></a></li>

<li><a href="index.php?option=admin&bolum=stats&task=adres"><i class="fa-solid fa-globe"></i> <span>Adres İstatistiği</span></a></li> 

<li><a href="index.php?option=admin&bolum=stats&task=islem"><i class="fa-solid fa-file-invoice"></i> <span>Tarih Bazlı İşlemler</span></a></li> 

<li><a href="index.php?option=admin&bolum=stats&task=personel"><i class="fa-solid fa-square-poll-vertical"></i> <span>Tarih Bazlı Personeller</span></a></li>

<li><a href="index.php?option=admin&bolum=stats&task=hgirilmeyen"><i class="fa-solid fa-circle-half-stroke"></i> <span>Bilgileri Eksik Hastalar</span></a></li> 

<li><a href="index.php?option=admin&bolum=dosyalama"><i class="fa-regular fa-folder-open"></i> <span>Dosyalama Sistemi</span></a></li>
</ul>
</li>

<li><a href="index.php?option=site"><i class="fa-solid fa-clipboard-user"></i> <span>Siteye Geçiş Yap</span></a></li>
<li><a href="index.php?option=logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Çıkış Yap</span></a></li>    
</ul>
</div>
<?php
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
	convertSite();
	break;
}
}

function convertSite() {
	global $mainframe, $dbase, $my;
	
	if ($my->isadmin) {
	$session = new Session($dbase);
	$session->load($mainframe->_session->session);

	$session->access_type = 'site';
	$session->update();
	
	Redirect('index.php');
	} else {
		NotAuth();
	}    
}

function AdminPanelMenu() {
	?>
	<div class="panel panel-default">
	<div class="panel-heading"><h4><i class="fa-solid fa-chart-line"></i> Yönetim Paneli</h4></div>
	<div class="panel-body">
    <div class="col-sm-6">
    <?php hYasGruplari();?> 
    </div>
    <div class="col-sm-6">
    <?php dogumGunuGetir();?>
    </div>
    </div>
    </div>
	<?php
}

function dogumGunuGetir() {
    global $dbase;
    
    $buay = date('m');
    $bugun = date('d');
    
    $query = "SELECT h.id, h.isim, h.soyisim, h.tckimlik, h.dogumtarihi, m.mahalle FROM #__hastalar AS h "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle WHERE h.pasif=0";
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
       <th><?php echo $k['isim'];?> <?php echo $k['soyisim'];?></th>
      <td><?php echo $k['tc'];?></td>
      <td><?php echo $k['mahalle'];?></td>
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
        
        $yas = yas_bul($dtarih);
        
        if ($yas < 1) {
            ++$yasaralik[01]['E'];
        }
        
        if ($yas > 1 && $yas < 3) {
           ++$yasaralik[22]['E'];
        }
        
        if ($yas > 2 && $yas < 19) {
           ++$yasaralik[318]['E'];
        }
        
        if ($yas > 18 && $yas < 46) {
           ++$yasaralik[1945]['E']; 
        }
        
        if ($yas > 45 && $yas < 66) {
          ++$yasaralik[4665]['E'];  
        }
        if ($yas > 65 && $yas < 86) {
          ++$yasaralik[6685]['E'];  
        }
        
        if ($yas > 85) {
          ++$yasaralik[86]['E'];
        }
    }
    
    foreach ($tarihler['K'] as $dtarih) {
        
        $yas = yas_bul($dtarih);
        
        if ($yas < 1) {
            ++$yasaralik[01]['K'];
        }
        
        if ($yas > 1 && $yas < 3) {
           ++$yasaralik[22]['K'];
        }
        
        if ($yas > 2 && $yas < 19) {
           ++$yasaralik[318]['K'];
        }
        
        if ($yas > 18 && $yas < 46) {
           ++$yasaralik[1945]['K']; 
        }
        
        if ($yas > 45 && $yas < 66) {
          ++$yasaralik[4665]['K'];  
        }
        if ($yas > 65 && $yas < 86) {
          ++$yasaralik[6685]['K'];  
        }
        
        if ($yas > 85) {
          ++$yasaralik[86]['K'];
        }
    }
    
    $yasaralik['toplam']['E'] = $yasaralik[01]['E'] + $yasaralik[22]['E'] + $yasaralik[318]['E'] + $yasaralik[1945]['E'] + $yasaralik[4665]['E'] + $yasaralik[6685]['E'] + $yasaralik[86]['E']; 
    $yasaralik['toplam']['K'] = $yasaralik[01]['K'] + $yasaralik[22]['K'] + $yasaralik[318]['K'] + $yasaralik[1945]['K'] + $yasaralik[4665]['K'] + $yasaralik[6685]['K'] + $yasaralik[86]['K']; 
    
     ?>
         <div class="panel panel-default">
        <div class="panel-heading"><h4><i class="fa-solid fa-chart-simple"></i> Yaş Gruplarına Göre Hasta Sayıları</h4></div>
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


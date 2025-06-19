<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

$limit = intval(getParam($_REQUEST, 'limit', 20));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));
$tc = getParam($_REQUEST, 'tc');
$search = strval(getParam($_REQUEST, 'search'));

switch ($task) {
    
    default:
    Listele($search);
    break;
    
    case 'olenler':
    Olenler();
    break;
    
    case 'tara':
    Tarama();
    break;
}

function Tarama() {
    global $dbase, $limit, $limitstart;
    
    if (defined(max_exe_time)) {
    ini_set('max_execution_time', max_exe_time); 
    } else {
    ini_set('max_execution_time', 720);
    }
    
    //var_dump(ini_get('max_execution_time'));
    
    $dbase->setQuery("SELECT COUNT(*) FROM #__hastalar WHERE pasif='0'");
    
    $total = $dbase->loadResult();
    
    $total_pages = ceil( $total / $limit );
    
    $end_page = ($total_pages-1) * $limit;
    
    $i = 0;
    
    while($total_pages >= 1) {
        
    $query = "SELECT h.* FROM #__hastalar AS h "
    . "\n WHERE h.pasif='0' "
    . "\n ORDER BY h.isim ASC, h.soyisim ASC";
    
    $dbase->setQuery($query, $end_page, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        if (olumBildirimi($row)) {
            $pasiftarihi = tarihCevir(olumBildirimi($row));
        
            $dbase->setQuery("UPDATE #__hastalar SET pasif='-1', pasiftarihi='".$pasiftarihi."', pasifnedeni='2' WHERE tckimlik=".$row->tckimlik);
            $dbase->query();
        $i++;
        }
    } //foreach
    
    $end_page = $end_page - $limit;
    
    $total_pages--;
    
    } //while

    Redirect("index.php?option=admin&bolum=olumbildirim&task=olenler", 'Toplam '.$i.' hasta tespit edildi');

}


function Olenler() {
    global $dbase, $limit, $limitstart;
    
    if (defined(max_exe_time)) {
    ini_set('max_execution_time', max_exe_time); 
    } else {
    ini_set('max_execution_time', 720);
    }
    
    //var_dump(ini_get('max_execution_time'));
    
    $sayfa = 'index.php?option=admin&bolum=olumbildirim&task=olenler';
    
    $dbase->setQuery("SELECT COUNT(*) FROM #__hastalar WHERE pasif='-1'");
    
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav( $total, $limitstart, $limit); 
    
    $query = "SELECT h.*, il.ilce, m.mahalle FROM #__hastalar AS h "
    . "\n LEFT JOIN #__ilce AS il ON il.id=h.ilce "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n WHERE h.pasif='-1' "
    . "\n ORDER BY h.pasiftarihi ASC, h.isim ASC, h.soyisim ASC";
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    ?>
    <div class="panel panel-primary">
<div class="panel-heading">
<div class="row">
    <div class="col-xs-2"><h4><i class="fa-solid fa-triangle-exclamation"></i> Ölen Hasta Listesi</h4></div>
    <div class="col-xs-7">
    </div>
    <div class="col-xs-2">
    <a href="index.php?option=admin&bolum=olumbildirim&task=tara" class="btn btn-danger" id="tarama">Taramayı Başlat</a>
    </div>
    <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($sayfa);?></div>
</div>
</div>
    <table class="table table-hover">
    <thead>
    <tr>
    <th>#</th>
    <th>Hasta Adı</th>
    <th>TC Kimlik</th>
    <th>Anne Adı</th>
    <th>Baba Adı</th>
    <th>Mahalle</th>
    <th>Doğum Tarihi</th>
    <th>Yaşı</th>
    <th>Son İzlem Tarihi</th>
    <th>Ölüm Tarihi</th>
    </tr>
    </thead>
    <?php
foreach ($rows as $row) {
  
    
    $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
    $sonizlem= $dbase->loadResult();
    
    $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
    $row->toplamizlem= $dbase->loadResult();
         
    $tarih = explode('.',$row->dogumtarihi);
    $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
    $row->dtarihi = strftime("%d.%m.%Y", $tarih);
    ?>
    <tr>
    <th><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span></th>
    <th><div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span></a>  <?php echo $row->gecici ? '<sub>(G)</sub>':'';?>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
</th>
    <td><?php echo $row->tckimlik;?></td>
    <td><?php echo $row->anneAdi;?></td>
    <td><?php echo $row->babaAdi;?></td>
    <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
    <td><?php echo $row->dtarihi;?></td>
    <td><?php echo yas_bul($row->dogumtarihi);?></td>
    <td><?php echo tarihCevir($sonizlem, 1);?></td>
    <td><?php echo tarihCevir($row->pasiftarihi, 1);?></td>
    </tr>
    
    <?php
}
    ?>
    </table>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($sayfa);
?>
</div>
<div class="pagenav_counter">
<?php echo $pageNav->writeLeafsCounter();?>
</div>
</div>
</div>

<script type="text/javascript"> 
 
    // unblock when ajax activity stops 
    $(document).ajaxStop($.unblockUI); 
 
    $(document).ready(function() { 
        $('#tarama').click(function() { 
            $.blockUI(); 
        }); 
    }); 
 
</script>
<?php

}

function Listele($search) {
    global $dbase, $limit, $limitstart;
    
    //2 yıl önceki tarih 
    $tarih = date('d.m.Y', strtotime('-24 month'));
    $tarihgetir = tarihCevir($tarih);
    
    $sayfa = 'index.php?option=admin&bolum=olumbildirim';
        if ($search) {
            $sayfa .= "&amp;search=".$search;
        }
    
    $where = array();
    
    $where[] = "h.pasif='0'";               
    $where[] = "h.anneAdi != '' AND h.babaAdi != ''";
    
     if ($search) {
         $search = mosStripslashes($search);
         if (is_numeric($search)) {
         $where[] = "h.tckimlik = ". $dbase->getEscaped( $search );
         } else {
         $where[] = "(h.isim LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%' OR h.soyisim LIKE '" . $dbase->getEscaped( trim( $search ) ) . "%')";
         } 
     }
    
    $query = "SELECT COUNT(h.id) FROM #__hastalar AS h"
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" );
    $dbase->setQuery($query);
    
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav( $total, $limitstart, $limit);
    
    $query = "SELECT h.*, i.ilce AS ilce, m.mahalle AS mahalle FROM #__hastalar AS h "
    . "LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . " ORDER BY h.isim ASC, h.soyisim ASC";
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    ?>
   <form action="index.php" method="GET" name="adminForm" role="form">
<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="olumbildirim" />  
    <div class="panel panel-primary">
<div class="panel-heading">
<div class="row">
    <div class="col-xs-2"><h4><i class="fa-solid fa-dice-d20"></i> Aktif Hasta Listesi</h4></div>
    <div class="col-xs-5">
    </div>
    <div class="col-xs-4">
    <div class="input-group">
        <input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control"  onChange="document.adminForm.submit();" placeholder="TC Kimlik Numarası yada Bir isim Yazın" autocomplete="off">
        <div class="input-group-btn">
        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </div>
    </div>
    </div>
    <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($sayfa);?></div>
</div>
</div>
    <table class="table table-hover">
    <thead>
    <tr>
    <th>#</th>
    <th>Hasta Adı</th>
    <th>TC Kimlik</th>
    <th>Anne Adı</th>
    <th>Baba Adı</th>
    <th>Mahalle</th>
    <th>Doğum Tarihi</th>
    <th>Yaşı</th>
    <th>Son İzlem Tarihi</th>
    <th>Ölüm Tarihi</th>
    </tr>
    </thead>
    <?php
foreach ($rows as $row) {
    
    if (olumBildirimi($row)) {
        
        /*
        $dbase->setQuery("SELECT tckimlik FROM #__olum WHERE tckimlik=".$row->tckimlik);
        $varmi = $dbase->loadResult();
        
        if (!$varmi) {
    
        $dbase->setQuery("INSERT INTO #__olum (tckimlik, olumtarihi) VALUES ('".$row->tckimlik."', '".olumBildirimi($row)."')");
        $dbase->query();
        
        }
        */
        $pasiftarihi = tarihCevir(olumBildirimi($row));
        
        $dbase->setQuery("UPDATE #__hastalar SET pasif='-1', pasiftarihi='".$pasiftarihi."', pasifnedeni='2' WHERE tckimlik=".$row->tckimlik);
        $dbase->query();
    }
    
    
    $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
    $sonizlem= $dbase->loadResult();
    
    $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE hastatckimlik=".$row->tckimlik);
    $row->toplamizlem= $dbase->loadResult();
         
    $tarih = explode('.',$row->dogumtarihi);
    $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
    $row->dtarihi = strftime("%d.%m.%Y", $tarih);
    ?>
    <tr class="<?php echo olumBildirimi($row) ? 'alert alert-warning':'';?>">
    <th><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span></th>
    <th><div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span></a>  <?php echo $row->gecici ? '<sub>(G)</sub>':'';?>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
</th>
    <td><?php echo $row->tckimlik;?></td>
    <td><?php echo $row->anneAdi;?></td>
    <td><?php echo $row->babaAdi;?></td>
    <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
    <td><?php echo $row->dtarihi;?></td>
    <td><?php echo yas_bul($row->dogumtarihi);?></td>
    <td><?php echo tarihCevir($sonizlem, 1);?></td>
    <td><?php echo olumBildirimi($row);?></td>
    </tr>
    
    <?php
}
    ?>
    </table>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($sayfa);
?>
</div>
<div class="pagenav_counter">
<?php echo $pageNav->writeLeafsCounter();?>
</div>
</div>
</div>
</form>
    <?php
}
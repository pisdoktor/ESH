<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class StatsHTML {
    
    function mamaRaporuGetir($rows, $baslangictarih, $bitistarih, $pageNav, $ordering) {
        $link = "index.php?option=admin&bolum=stats&task=mamarapor";
        if ($baslangictarih) {
        $link .= "&baslangictarih=".$baslangictarih;
        } 
        if ($bitistarih) {
        $link .= "&bitistarih=".$bitistarih;
        }
        
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
     ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="bezrapor" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-4"><h4><i class="fa-solid fa-bowl-food"></i> Hasta Mama Rapor Takibi</h4></div>
        <div class="col-xs-7">
        
         <div class="form-group row">      
      
    <div class="col-sm-12">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">arası</div>
    
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('mamarapor');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
    </div>
 
    </div>
    
    </div>
    
    </div> <!-- form-group row -->
        </div>

        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div> 

        </div>
    </div>
    
    <table class="table table-striped">
    <thead>
    <tr>
    <th><a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a></th>
    <th>Hasta Adı
    <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
    </th>
    <th>TC Kimlik Numarası</th>
    <th>Mahalle
    <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
    </th>
    <th>Doğum Tarihi</th>
    <th>Yaşı</th>
    <th>Rapor Bitiş Tarihi
    <span><a href="<?php echo $link;?>&ordering=h.mamaraporbitis-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mamaraporbitis-ASC">▼</a></span>
    </th>
    <th>Mama Markası
     <span><a href="<?php echo $link;?>&ordering=h.mamacesit-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mamacesit-ASC">▼</a></span>
    </th>
    <th>Rapor Yeri</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($rows as $row) {
        $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
       
        $mamacesit = array('Bilinmiyor', 'Abbot', 'Nestle', 'Nutricia');
        ?>
        <tr>
        <td><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span></td>
         <th>
         <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span> <?php echo $row->gecici ? '<sub>(G)</sub>':'';?></a>
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
         <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
            <td><?php echo $row->dtarihi;?></td>
         <td><?php echo yas_bul($row->dogumtarihi);?></td>
         <td><?php echo tarihCevir($row->mamaraporbitis, 1);?></td>
         <td><?php echo $mamacesit[$row->mamacesit];?></td>
         <td><?php echo $row->mamaraporyeri ? 'DDH':'Dış Merkez';?></td>
         </tr>
        <?php
    }
    ?>
    </tbody>
    </table>
    
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>

</div>  
    </div>
    </form>   
    <?php
    }
    
    function bezRaporuGetir($rows, $baslangictarih, $bitistarih, $pageNav) {
        $link = "index.php?option=admin&bolum=stats&task=bezrapor";
        if ($baslangictarih) {
        $link .= "&baslangictarih=".$baslangictarih;
        } 
        if ($bitistarih) {
        $link .= "&bitistarih=".$bitistarih;
        }
        
     ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="bezrapor" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-4"><h4><i class="fa-solid fa-boxes-packing"></i> Hasta Alt Bezi Rapor Takibi</h4></div>
        <div class="col-xs-7">
        
         <div class="form-group row">      
      
    <div class="col-sm-12">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">arası</div>
    
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('bezrapor');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
    </div>
 
    </div>
    
    </div>
    
    </div> <!-- form-group row -->
        </div>

        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div> 

        </div>
    </div>
    
    <table class="table table-striped">
    <thead>
    <tr>
    <th><a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a></th>
    <th>Hasta Adı</th>
    <th>TC Kimlik Numarası</th>
    <th>Mahalle</th>
    <th>Doğum Tarihi</th>
    <th>Yaşı</th>
    <th>Rapor Bitiş Tarihi</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($rows as $row) {
        $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
        ?>
        <tr>
        <td><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span></td>
         <th>
         <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span> <?php echo $row->gecici ? '<sub>(G)</sub>':'';?></a>
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
         <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
         <td><?php echo $row->dtarihi;?></td>
         <td><?php echo yas_bul($row->dogumtarihi);?></td>
         <td><?php echo tarihCevir($row->bezraporbitis, 1);?></td>
         </tr>
        <?php
    }
    ?>
    </tbody>
    </table>
    
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>

</div>  
    </div>
    </form>   
    <?php
    }
    
    function ilacRaporGetir($rows, $baslangictarih, $bitistarih, $pageNav) {
        $link = "index.php?option=admin&bolum=stats&task=ilacrapor";
        if ($baslangictarih) {
        $link .= "&baslangictarih=".$baslangictarih;
        } 
        if ($bitistarih) {
        $link .= "&bitistarih=".$bitistarih;
        }
        
     ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="ilacrapor" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-4"><h4><i class="fa-solid fa-pills"></i> Hasta İlaç Rapor Takibi</h4></div>
        <div class="col-xs-7">
        
         <div class="form-group row">      
      
    <div class="col-sm-12">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">arası</div>
    
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('ilacrapor');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
    </div>
 
    </div>
    
    </div>
    
    </div> <!-- form-group row -->
        </div>

        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div> 

        </div>
    </div>
    
    <table class="table table-striped">
    <thead>
    <tr>
   <th><a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a></th>
    <th>Hasta Adı</th>
    <th>TC Kimlik Numarası</th>
    <th>Mahalle</th>
    <th>Doğum Tarihi</th>
    <th>Yaşı</th>
    <th>Hastalık Adı</th>
    <th>Rapor Bitiş Tarihi</th>
    <th>İlgili Branş</th>
    <th>Rapor Yeri</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($rows as $row) {
        $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
        ?>
        <tr>
        <td><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span></td>
         <th>
         <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span> <?php echo $row->gecici ? '<sub>(G)</sub>':'';?></a>
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
         <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
         <td><?php echo $row->dtarihi;?></td>
         <td><?php echo yas_bul($row->dogumtarihi);?></td>
         <td><?php echo $row->hastalikadi;?></td>
         <td><?php echo tarihCevir($row->bitistarihi, 1);?></td>
         <td><?php echo implode(',', $row->branslar);?></td>
         <td><?php echo $row->raporyeri ? '<strong>DDH</strong>':'Dış Merkez';?></td>
         </tr>
        <?php
    }
    ?>
    </tbody>
    </table>
    
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>

</div>  
    </div>
    </form>   
    <?php
    }
    
    function IzlemiOlmayan($rows, $total, $pageNav) {
        $link = 'index.php?option=admin&bolum=stats&task=izlemolmayan'; 
    ?>
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-9"><h4><i class="fa-solid fa-file-medical"></i> Hiç İzlem Girilmemiş Hastalar</h4></div>
        <div class="col-xs-2" align="right"><h5>Toplam <?php echo $total;?> hasta</h5></div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
        </div>
    </div>
    <table class="table table-striped table-hover">
    <thead>
    <tr>
    <th>Hasta Adı</th>
    <th>TC Kimlik</th>
    <th>Mahalle</th>
    </tr>
    </thead>
    <tbody> 
    <?php 
    foreach ($rows as $row) {
     ?>
     <tr>
     <th scope="row">
     <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span></a>
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
     <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
     </tr>
    <?php
    }
    ?>
    </tbody>
    </table>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>
</div>
    </div>
    <?php
    }
    
    function hastaGetir($rows, $hastalik, $pageNav, $total, $ordering) {
        $link = "index.php?option=admin&bolum=stats&task=hastagetir&id=".$hastalik->id;
        
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
      ?>
        <div class="panel panel-default">
        <div class="panel-heading">
        <div class="row">
        <div class="col-xs-11"><h4><i class="fa-solid fa-ranking-star"></i> <?php echo $hastalik->hastalikadi;?> Tanısı Girilmiş Hastalar <span class="">(<?php echo $total;?> Hasta)</span></h4> </div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
        </div>
        
        <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
    <th scope="col">#</th>
      <th scope="col">Hasta Adı Soyadı
      <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
      </th>
      <th scope="col">TC Kimlik Numarası</th>
      <th scope="col">Mahalle
      <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
      </th> 
      <th scope="col">Kayıt Yılı
      <span><a href="<?php echo $link;?>&ordering=h.kayityili-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-ASC">▼</a></span>
      </th>
      <th scope="col">Doğum Tarihi
      <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-ASC">▼</a></span>
      </th> 
      <th scope="col">Yaşı</th>
      <th scope="col">Son İzlem Tarihi</th>
    </tr>
  </thead>
  
  <tbody>
   <?php 
     foreach ($rows as $row) {
         $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
         $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
      ?>
       <tr>
       <td scope="row"><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span>
       </td>
       <th scope="col">
      <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span></a> <?php echo $row->gecici ? '<sub>(G)</sub>':'';?>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
  </ul>
</div> 
      </th>
      <td scope="col"><?php echo $row->tckimlik;?></td>
      <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
      <td scope="col"><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td>
      <td><?php echo $row->dtarihi;?></td>
      <td><?php echo yas_bul($row->dogumtarihi);?></td>
      <td><?php echo $row->sonizlem ? tarihCevir($row->sonizlem, 1) : 'Yok';?></td> 
    </tr>
      <?php   
     }
   
   ?>
  </tbody>
  </table>
<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>

</div> 
        </div>
      
      <?php 
    
    }
    
    function sondaDegisimTakip($rows, $baslangictarih, $bitistarih, $pageNav, $ordering) {
        $link = "index.php?option=admin&bolum=stats&task=sondadegisim";
        
        if ($baslangictarih) {
        $link .= "&baslangictarih=".$baslangictarih;
        }
         
        if ($bitistarih) {
        $link .= "&bitistarih=".$bitistarih;
        }
        
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
     ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="sondadegisim" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-4"><h4><i class="fa-solid fa-vial"></i> Sonda Değişimi Takibi</h4></div>
        <div class="col-xs-7">
        
         <div class="form-group row">      
      
    <div class="col-sm-12">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" autocomplete="off" />
    
    <div class="input-group-addon">arası</div>
    
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('sondadegisim');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
    </div>
 
    </div>
    
    </div>
    
    </div> <!-- form-group row -->
        </div>

        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div> 

        </div>
    </div>
    
    <table class="table table-striped">
    <thead>
    <tr>
    <th><a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a></th>
    <th>Hasta Adı
      <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
    </th>
    <th>TC Kimlik Numarası</th>
    <th>Mahalle
      <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
    </th>
    <th>Doğum Tarihi</th>
    <th>Yaşı</th>
    <th>Sonda Tarihi
      <span><a href="<?php echo $link;?>&ordering=h.sondatarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.sondatarihi-ASC">▼</a></span>
    </th>
    <th>Sonda Değişim Tarihi</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($rows as $row) {
        $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
        ?>
        <tr>
        <td><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span></td>
         <th>
         <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span> <?php echo $row->gecici ? '<sub>(G)</sub>':'';?></a>
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
         <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
            <td><?php echo $row->dtarihi;?></td>
         <td><?php echo yas_bul($row->dogumtarihi);?></td>
         <td><?php echo tarihCevir($row->sondatarihi, 1);?></td>
         <td><?php echo tarihCevir(strtotime('+1 month', $row->sondatarihi), 1);?></td>
         </tr>
        <?php
    }
    ?>
    </tbody>
    </table>
    
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>

</div>  
    </div>
    </form>   
    <?php
    }
    
    function hastalikGirilmemis($rows, $total, $pageNav, $ordering, $secim, $lists, $ozellik) {
        
        $link = 'index.php?option=admin&bolum=stats&task=hgirilmeyen';
        if ($ordering) {
        $link.= '&ordering='.$ordering;
        }
        
        if ($secim) {
        $link .= '&secim='.$secim;
        }
        
        
        $link .= '&ozellik='.$ozellik;
    
       
        $bagim = array('1' => 'Yarı Bağımlı', '2' => 'Tam Bağımlı', '3' => 'Bağımsız');
        $aylar = array('' => '','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
        
        ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
    <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="hgirilmeyen" />
         <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-6"><h4><i class="fa-solid fa-circle-half-stroke"></i> Bilgileri Eksik Olan Hastalar</h4></div>
        <div class="col-sm-2">
        <?php echo $lists['ozellik'];?>
        </div>
        <div class="col-sm-3">
        <div class="input-group"> 
         <?php echo $lists['filtre'];?>
        <div class="input-group-btn"><button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button></div>
        </div>
        </div>
         <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
        </div>
    </div>
    <table class="table table-striped table-hover">
    <thead>
    <tr>
    <th>#</th>
    <th>Hasta Adı
    <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
    </th>
    <th>TC Kimlik
    <span><a href="<?php echo $link;?>&ordering=h.tckimlik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-ASC">▼</a></span>
    </th>
    <!--
    <th>Cinsiyet
    <span><a href="<?php echo $link;?>&ordering=h.cinsiyet-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.cinsiyet-ASC">▼</a></span>
    </th>
    -->
    <th>Mahalle
    <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
    </th>
    <th>Cadde/Sokak
        <span><a href="<?php echo $link;?>&ordering=h.sokak-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.sokak-ASC">▼</a></span>
    </th>
    <th>Kapı No
        <span><a href="<?php echo $link;?>&ordering=h.kapino-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kapino-ASC">▼</a></span>
    </th>
    <!--
    <th>Bağımlılık
        <span><a href="<?php echo $link;?>&ordering=h.bagimlilik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.bagimlilik-ASC">▼</a></span>
    </th>
    -->
    <th>Hastalık
        <span><a href="<?php echo $link;?>&ordering=h.hastaliklar-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.hastaliklar-ASC">▼</a></span>
    </th>
    <!--
    <th>Koordinat
        <span><a href="<?php echo $link;?>&ordering=h.coords-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.coords-ASC">▼</a></span>
    </th>
    -->
    <th>Anne Adı
        <span><a href="<?php echo $link;?>&ordering=h.anneAdi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.anneAdi-ASC">▼</a></span>
    </th>
    <th>Baba Adı
        <span><a href="<?php echo $link;?>&ordering=h.babaAdi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.babaAdi-ASC">▼</a></span>
    </th>
    <th>Boy
        <span><a href="<?php echo $link;?>&ordering=h.boy-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.boy-ASC">▼</a></span>
    </th>
    <th>Kilo
        <span><a href="<?php echo $link;?>&ordering=h.kilo-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kilo-ASC">▼</a></span>
    </th>
    <th>Cep Telefonu</th>
    </tr>
    </thead>
    <tbody>
     <?php 
    foreach ($rows as $row) {
        $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
     ?>
     <tr class="<?php echo $row->pasif ? "warning":"";?>">
     <th><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->toplamizlem;?></a></span></th>
     <th scope="row">
     <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span> <?php echo $row->gecici ? '<sub>(G)</sub>':'';?>
</a>
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
     <!--<td><?php echo $row->cinsiyet;?></td>-->
     <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
     <td><?php echo $row->sokakadi;?></td>
     <td><?php echo $row->kapino;?></td> 
     <!--<td><?php echo $bagim[$row->bagimlilik];?></td>-->
     <td><?php echo $row->hastaliklar;?></td>
     <!--<td><?php echo $row->coords;?></td>-->
     <td><?php echo $row->anneAdi;?></td>
     <td><?php echo $row->babaAdi;?></td>
     <td><?php echo $row->boy;?></td>
     <td><?php echo $row->kilo;?></td>
     <td><?php echo $row->ceptel1;?></td>
     </tr>
    <?php
    }
    ?>
    </tbody>
    </table>
    <div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>
</div>
    </div>
        <?php
    
    }
    
    function personelGetir($data, $baslangictarih, $bitistarih) {
        ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="personel" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-4"><h4><i class="fa-solid fa-square-poll-vertical"></i> İki Tarih Arası Personel İşlem Sayıları</h4></div>
        <div class="col-xs-7">
        
         <div class="form-group row">      
      
    <div class="col-sm-12">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" />
    
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" />
    
    <div class="input-group-addon">arası</div>
    
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('personel');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
    </div>
 
    </div>
    
    </div>
    
    </div> <!-- form-group row -->
        </div>
        <div class="col-xs-1" align="right"></div>
        </div>
    </div>
    
    <table class="table table-striped">
    <thead>
    <tr>
    <th>Personel Adı</th>
    <th>İşlem Sayısı</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($data as $data) {
        
        if ($data['islemsayisi']) {
        ?>
        <tr>
         <td><?php echo $data['personeladi'];?></td>
         <td><?php echo $data['islemsayisi'];?></td>
         </tr>
        <?php
        }
    }
    ?>
    </tbody>
    </table>
    
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script>  
    </div>
    </form>   
    <?php
    }
    
    function islemGetir($data, $baslangictarih, $bitistarih) {
        ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="islem" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-4"><h4><i class="fa-solid fa-file-invoice"></i> İki Tarih Arası Yapılan İşlemler</h4></div>
        <div class="col-xs-7">
        <div class="form-group row">      
      
    <div class="col-sm-12">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" />
    
    <div class="input-group-addon">ile</div>
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" />
    
    <div class="input-group-addon">arası</div>
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('islem');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
 </div>
    </div>
    
    </div>
    
    </div> <!-- form-group row -->
        </div>
        <div class="col-xs-1" align="right"></div>
        </div>
    </div>
    
    <table class="table table-striped">
    <thead>
    <tr>
    <th>İşlem Adı</th>
    <th>Yapılma Sayısı</th>
    </tr>
    </thead>
    <tbody>
    <?php  
    foreach ($data as $data) {
        if ($data['islemsayisi']) {
        ?>
         <tr>
         <td><?php echo $data['islemadi'];?></td>
         <td><?php echo $data['islemsayisi'];?></td>
         </tr>
        <?php
        }
    }
    ?>
    </tbody>
    </table>
    
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script>  
    </div>
    </form>   
    <?php
    }
    
    function adresHastaFiltre($rows, $lists, $ilce, $mahalle, $sokak, $kapino,  $ozellik, $ordering, $pageNav) {
        $link = 'index.php?option=admin&bolum=stats&task=adres';
        if ($ilce) {
            $link .= "&amp;ilce=".$ilce;
        }
        if ($mahalle) {
            $link .= "&amp;mahalle=".$mahalle;
        }
        if ($sokak) {
            $link .= "&amp;sokak=".$sokak;
        }
        if ($kapino) {
            $link .= "&amp;kapino=".$kapino;
        }
        if ($ozellik) {
            $link .= "&amp;ozellik=".$ozellik;
        }
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
        
    ?>
<form action="index.php" method="GET" name="adminForm" role="form">
<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="adres" /> 

<div class="col-sm-3">

<div class="panel panel-default" id="leftside"> 
<div class="panel-heading"><h4><i class="fa-solid fa-magnifying-glass"></i> Filtreleme Seçenekleri</h4></div>
        
<div class="panel-body">

<div class="form-group">
<label for="ilce">İlçe</label>
<?php echo $lists['ilce'];?>
</div>

<div class="form-group">
<label for="mahalle">Mahalle</label>
<?php echo $lists['mahalle'];?>
</div>

<div class="form-group">
<label for="sokak">Sokak</label>
<?php echo $lists['sokak'];?>
</div>

<div class="form-group">
<label for="kapino">Kapı No</label>
<?php echo $lists['kapino'];?>
</div>

<div class="form-group">
<label for="özellik">Özelliğine Göre Hastalar</label>
<?php echo $lists['ozellik'];?>
</div>

<div class="form-group">
<input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('adres');" class="btn btn-primary"  />
</div>
 
</div> <!-- panel body -->
<script>                                  
$(document).ready(function(){
    
        $("#ilce").on("change", function(){

            $("#mahalle").html("<option value=''>Bir Mahalle Seçin</option>");
            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>"); 
            console.log($(this).val()); 
            
            ajaxFunc("mahalle", $(this).val(), "#mahalle");

        });

        $("#mahalle").on("change", function(){

            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("sokak", $(this).val(), "#sokak");

        });
        
        $("#sokak").on("change", function(){

            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("kapino", $(this).val(), "#kapino");

        });
        
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=admin&bolum=stats",
                type: "POST",
                data: {task:task, id:id},
                success: function(sonuc){
                    console.log(sonuc);
                    $(name).append(sonuc);
                }});
        }

    });
</script> 

</div> <!-- panel left-->

</div> <!-- col-sm -->


<div class="col-sm-9">    
    
<div class="panel panel-default" id="mainside">
<div class="panel-heading">
<div class="row">
    <div class="col-xs-11"><h4><i class="fa-solid fa-globe"></i> Adrese Göre Hastalar</h4></div>
    <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
</div>
</div>
    <table class="table table-striped">
    <thead class="thead-dark">
    <tr>
    <th><a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a></th>
    <th scope="col">
      <div>
      Hasta Adı   
  <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col"><div>
      TC Kimlik Numarası
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col"> <div>
      İlçe
  <span><a href="<?php echo $link;?>&ordering=ilc.ilce-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=ilc.ilce-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col"> <div>
      Mahalle
  <span><a href="<?php echo $link;?>&ordering=m.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.mahalle-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col"> <div>
      Cadde/Sokak
  <span><a href="<?php echo $link;?>&ordering=s.sokakadi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=s.sokakadi-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col"> <div>
      Kapı No
  <span><a href="<?php echo $link;?>&ordering=k.kapino-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=k.kapino-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col">
      Son İzlem Tarihi
    </th>
    </tr>
  </thead>
  <tbody>

  <?php 
   foreach($rows as $row) {
       $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
       
       //son izlem tarihi
       $row->sonizlemtarihi = $row->sonizlemtarihi ? tarihCevir($row->sonizlemtarihi, 1) : 'Yok';
         
       $link2 = "index.php?option=admin&bolum=hastalar&task=edit&id=".$row->id;
       if ($pageNav->limit) {
           $link2 .= "&limit=".$pageNav->limit;
       }
       if ($pageNav->limitstart) {
           $link2 .= "&limitstart=".$pageNav->limitstart;
       }
       $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
      ?>
      
      <tr>
      <th><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'default':'warning';?>"><?php echo $row->toplamizlem;?></span></th>
      <th scope="row">
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span></a>
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
      <td><?php echo $row->ilceadi;?> </td>
      <td><?php echo $row->mahalleadi;?> </td>
      <td><?php echo $row->sokakadi;?></td>
      <td><?php echo $row->kapino;?></td>
      <td><?php echo $row->sonizlemtarihi;?></td>
      </tr>
 <?php 

  }?>
  
  
    </tbody>
</table>
<!--
<script>
    $(document).ready(function () {
        $("#btnExport").click(function () {
            let table = document.getElementsByTagName("table");
            console.log(table);
            debugger;
            TableToExcel.convert(table[0], {
                name: `Hastalar.xlsx`,
                sheet: {
                    name: 'Hastalar'
                }
            });
        });
    });
</script>
-->
<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>
</div>

</div> <!-- panel-default -->   

</div> <!-- col-sm -->
   
</form> 
<?php    
    }
    
    function specialGetir($rows, $title, $secim, $pageNav, $ordering) {
        $link = "index.php?option=admin&bolum=stats&task=specialgetir&secim=".$secim;
        
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        ?>
        <div class="panel panel-default">
        <div class="panel-heading">
        <div class="row">
        <div class="col-xs-11"><h4><i class="fa-solid fa-ranking-star"></i> Özellikli Durumlarına Göre Hastalar - <?php echo $title;?></h4></div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
        
        </div>
        
        <table class="table table-striped table-hover">
  <thead class="thead-dark">
    <tr>
    <th><a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a></th>
      <th scope="col">Hasta Adı Soyadı
      <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
      </th>
      <th scope="col">TC Kimlik Numarası</th>
      <th scope="col">Mahalle
      <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
      </th> 
      <th scope="col">Kayıt Yılı
      <span><a href="<?php echo $link;?>&ordering=h.kayityili-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-ASC">▼</a></span>
      </th>
      <?php if ($secim == sonda) { ?>
      <th scope="col">Sonda Değişim Tarihi
      <span><a href="<?php echo $link;?>&ordering=h.sondatarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.sondatarihi-ASC">▼</a></span>
      </th>
      <?php } ?>
      <?php if ($secim == mama) { ?>
      <th scope="col">Kullandığı Mama
      <span><a href="<?php echo $link;?>&ordering=h.mamacesit-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mamacesit-ASC">▼</a></span>
      </th>
      <th scope="col">Rapor Bitiş Tarihi
      <span><a href="<?php echo $link;?>&ordering=h.mamaraporbitis-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mamaraporbitis-ASC">▼</a></span>
      </th>
      <th scope="col">Rapor Yeri</th>
      <?php } ?>
      
      <?php if ($secim == bez) { ?>
      <th scope="col">Bez Raporu Var mı?</th>
      <th scope="col">Rapor Bitiş Tarihi
      <span><a href="<?php echo $link;?>&ordering=h.bezraporbitis-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.bezraporbitis-ASC">▼</a></span>
      </th>
      <?php } ?>
      
      <th scope="col">Doğum Tarihi
      <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-ASC">▼</a></span>
      </th> 
      <th scope="col">Yaşı</th>
      <th scope="col">Son İzlem Tarihi</th>  
    </tr>
  </thead>
  
  <tbody>
   <?php 
     foreach ($rows as $row) {
        $tarih = explode('.',$row->dogumtarihi);
        $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
        $row->dtarihi = strftime("%d.%m.%Y", $tarih);
        $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
       
       $mamacesidi = array('Bilinmiyor', 'Abbot', 'Nestle', 'Nutricia');
      ?>
       <tr>
       <td><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'default':'warning';?>"><?php echo $row->toplamizlem;?></span></td>
      <th scope="col">
      <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
  <li class="divider"></li> 
    <li><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
      </th>
      <td scope="col"><?php echo $row->tckimlik;?></td>
      <td scope="col"><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
      <td scope="col"><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td>
      <?php if ($secim == sonda) { ?>
      <th><?php echo tarihCevir($row->sondatarihi, 1);?></th>
      <?php } ?>
      <?php if ($secim == mama) { ?>
      <th scope="col"><?php echo $mamacesidi[$row->mamacesit];?></th>
      <th scope="col"><?php echo tarihCevir($row->mamaraporbitis, 1);?></th>
      <th scope="col"><?php echo $row->mamaraporyeri ? 'DDH':'Dış Merkez';?></th>
      <?php } ?>
       <?php if ($secim == bez) { ?>
      <td scope="col"><?php echo $row->bezrapor ? '<strong>Evet</strong>':'Hayır';?></td>
      <th scope="col"><?php echo $row->bezraporbitis ? tarihCevir($row->bezraporbitis, 1):'';?></th>
      <?php } ?>
      <td><?php echo $row->dtarihi;?></td>
      <td><?php echo yas_bul($row->dogumtarihi);?></td>
      <td><?php echo tarihCevir($row->sonizlem, 1);?></td>  
    </tr>
      <?php   
     }
   
   ?>
  </tbody>
  </table>
<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>

</div> 
        </div>
      
      <?php 
    }
    
    function Izlenmeyenler($rows, $secimlist, $secim, $ordering, $pageNav) {
        $link = 'index.php?option=admin&bolum=stats&task=izlenmeyen';
        if ($secim) {
            $link .= "&secim=".$secim;
        }
        
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
    ?>
<form action="index.php" method="GET" name="adminForm" role="form">
    
<div class="panel panel-default">
        <div class="panel-heading">
         <div class="row">
        <div class="col-xs-8"><h4><i class="fa-solid fa-hospital-user"></i> İzlem Girilmeyen Hastalar</h4></div>
        <div class="col-xs-3">
         <div class="form-group row">
        
        <div class="col-sm-12">
        <div class="input-group">
      <div class="input-group-addon">Son</div>
       <?php echo $secimlist;?> 
      <div class="input-group-addon">İzlemi Yapılmayan</div>
       <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('izlenmeyen');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
 </div>
      </div>
        </div>
        
        </div> <!-- form-group -->
        </div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
        </div>
 
    <table class="table table-striped table-hover">
  <thead class="thead-dark">
    <tr>
     <th scope="col">
      <div>
     <a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a> 
    </div>  
    </th>  
      <th scope="col">Hasta Adı
      <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
      </th>
      <th scope="col">TC Kimlik Numarası
      <span><a href="<?php echo $link;?>&ordering=h.tckimlik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-ASC">▼</a></span>
      </th>
      <th scope="col">Mahalle
      <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
      </th>
      <th scope="col">Kayıt Yılı
      <span><a href="<?php echo $link;?>&ordering=h.kayityili-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-ASC">▼</a></span>
      </th>
      <th scope="col"><div>
      D. Tarihi
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-ASC">▼</a></span>
    </div>  
    </th>
      <th>
      Yaşı
      </th>
      <th>
      Cinsiyet
      </th>
      <th>
      Son İzlem Tarihi
      </th>
    </tr>
  </thead>
  <tbody>

  <?php   

   foreach($rows as $row) {
       $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
       $tarih = explode('.',$row['dogumtarihi']);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row['dtarihi'] = strftime("%d.%m.%Y", $tarih);
      ?>
      
      <tr>
      <td scope="row"><span data-toggle="tooltip" title="<?php echo $row['izlemsayisi'];?> İzlem" class="label label-<?php echo $row['izlemsayisi'] ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row['tckimlik'];?>"><?php echo $row['izlemsayisi'];?></a></span></td>
       <th>
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row['cinsiyet'] == 'E' ? 'blue':'#f5070f';?>"><?php echo $row['isim'];?></span></a> <?php echo $row['gecici'] ? '<sub>(G)</sub>':'';?>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row['id'];?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row['id'];?>">Bilgileri Düzenle</a></li>
  <li class="divider"></li> 
    <li><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row['tckimlik'];?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=izlemler&task=hedit&tc=<?php echo $row['tckimlik'];?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
</th>
      <td><?php echo $row['tckimlik'];?></td>
      <td><?php echo $row['mahalle'];?> <span class="label label-success"><?php echo $row['ilce'];?></span></td>
      <td><?php echo $row['kayityili'];?> <?php echo $aylar[$row['kayitay']];?></td>
      <td><?php echo $row['dtarihi'];?></td>
      <td><?php echo yas_bul($row['dogumtarihi']);?></td>
      <td><?php echo $row['cinsiyet'] == "E" ? "Erkek":"Kadın";?></td> 
      <td><?php echo $row['sonizlem'] ? tarihCevir($row['sonizlem'], 1) : 'Yok';?></td> 
    </tr>
 <?php 

  }?>
    </tbody>
</table>



<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="izlenmeyen" />

<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>

</div>


   </div> <!-- panel-default -->  
   
   </form>    
<?php
}
        
    function specialStats($s) {
        ?>
        <div class="panel panel-default">
        <div class="panel-heading"><h4><i class="fa-solid fa-ranking-star"></i> Özellikli Durumlarına Göre Hastalar</h4></div>
        <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Özellikli Durum</th>
      <th scope="col">Hasta Sayısı</th>
      <th scope="col">Toplam Hastaya Oranı</th> 
    </tr>
  </thead>
  <tbody>
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=ventilator">Ventilatör Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['ventilator'];?>
  </td>
  <td>
  % <?php echo round(($s['ventilator']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=kolostomi">Kolostomi Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['kolostomi'];?>
  </td>
  <td>
  % <?php echo round(($s['kolostomi']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=o2bagimli">Oksijen Bağımlı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['o2bagimli'];?>
  </td>
  <td>
  % <?php echo round(($s['o2bagimli']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=ng">Nazogastrik Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['ng'];?>
  </td>
  <td>
  % <?php echo round(($s['ng']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=peg">PEG Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['peg'];?>
  </td>
  <td>
  % <?php echo round(($s['peg']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=port">Port Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['port'];?>
  </td>
  <td>
  % <?php echo round(($s['port']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=gecici">Geçici Takipli Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['gecici'];?>
  </td>
  <td>
  % <?php echo round(($s['gecici']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=sonda">Sonda Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['sonda'];?>
  </td>
  <td>
  % <?php echo round(($s['sonda']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=bez">Alt Bezi Kullanan Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['bez'];?>
  </td>
  <td>
  % <?php echo round(($s['bez']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=mama">Mama Kullanan Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['mama'];?>
  </td>
  <td>
  % <?php echo round(($s['mama']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=yatak">Hasta Yatağı Olan Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['yatak'];?>
  </td>
  <td>
  % <?php echo round(($s['yatak']*100)/$s['total'], 2);?>
  </td>
  </tr>
  
  </tbody>
  </table>
        </div>
        
      <?php  
    }
    
    function hastalikStats($hastaliklar, $totalh, $veri) {
        ?>
      <div class="panel panel-primary">
        <div class="panel-heading"><h4><i class="fa-solid fa-chart-area"></i> Hastalıklarına Göre Hasta Sayısı</h4></div>
        <?php
        foreach ($hastaliklar as $k=>$v) {?>
        <div class="panel panel-default">
        <div class="panel-heading"><h5><?php echo $v['name'];?></h5></div>
        <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col" width="15%">ICD Kodu</th>
      <th scope="col" width="35%">Hastalık</th>
      <th scope="col" width="25%">Hasta Sayısı</th> 
      <th scope="col" width="25%">Toplam Hastaya Oranı</th>
    </tr>
  </thead>
    <tbody>
        <?php
        foreach ($v['hast'] as $hast) {
            

        ?>
        <tr>
        <td width="15%">
        <?php echo $hast->icd;?>
        </td>
        <td width="35%">
        <?php echo $veri[$hast->id] ? '<a href="index.php?option=admin&bolum=stats&task=hastagetir&id='.$hast->id.'">'.$hast->hastalikadi.'</a>' : $hast->hastalikadi;?>
        </td>
        <td width="25%">
        <?php echo $veri[$hast->id] ? '<a href="index.php?option=admin&bolum=stats&task=hastagetir&id='.$hast->id.'">'.$veri[$hast->id].'</a>' : '0';?>
        </td>
        <td width="25%">% <?php echo round((100*$veri[$hast->id])/$totalh, 2);?></td>
        </tr>
        <?php
        }
        ?>
         </tbody>
         </table>
        </div>
        <?php
        }
        ?>
    </tbody>
</table>
        </div>
        
     <?php   
    }
    
    function temelStats($rows, $toplamizlem, $toplamhasta, $pageNav, $baslangictarih, $bitistarih, $ordering) {
    $link = 'index.php?option=admin&bolum=stats';

         $link .= "&baslangictarih=".$baslangictarih;


         $link .= "&bitistarih=".$bitistarih;

    
    if ($ordering) {
        $link .= "&ordering=".$ordering;
    }
    ?>
    <form action="index.php" method="GET" name="adminForm" role="form">
    <div class="panel panel-default">
    <div class="panel-heading">
     <div class="row">
        <div class="col-xs-4"><h4><i class="fa-solid fa-chart-gantt"></i> İki Tarih Arası İzlem Yapılan Hastalar</h4></div>
        <div class="col-xs-7">
        
         <div class="form-group row">      
      
    <div class="col-sm-12">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" />
    
    
    <div class="input-group-addon">ile</div>
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" />
    
    
    <div class="input-group-addon">arası</div>
    
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('temel');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
 </div>
     
    </div>
    
    
    </div>
    
    
    </div> <!-- form-group row-->
        
        </div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
    </div>
        
    <div class="panel-body">
 
     <div class="btn-group">
     <button type="button" class="btn btn-sm btn-warning">İZLEM SIKLIĞI <span class="badge badge-light"><?php echo round($toplamizlem/$toplamhasta, 2);?></span></button>
     <button type="button" class="btn btn-sm btn-info">TOPLAM HASTA <span class="badge badge-light"><?php echo $toplamhasta;?></span></button>
     <button type="button" class="btn btn-sm btn-success">TOPLAM İZLEM <span class="badge badge-light"><?php echo $toplamizlem;?></span></button>
    </div>      
  
  </div> <!-- panel-body-->
    <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
    <th><a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a></th>
      <th scope="col">Hasta Adı
      <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
  </th>
      <th scope="col">TC Kimlik Numarası
      <span><a href="<?php echo $link;?>&ordering=h.tckimlik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-ASC">▼</a></span>
  </th>
      <th scope="col">Mahalle
      <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
  </th>
      <th scope="col">Kayıt Yılı
       <span><a href="<?php echo $link;?>&ordering=h.kayityili-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-ASC">▼</a></span>
  </th>
      <th scope="col">Yapılan İzlem Sayısı
      <span><a href="<?php echo $link;?>&ordering=izlemsayisi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=izlemsayisi-ASC">▼</a></span>
  </th>
  <th scope="col">Bağımlılık Durumu
  <span><a href="<?php echo $link;?>&ordering=h.bagimlilik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.bagimlilik-ASC">▼</a></span>
      </th>
      <th>
      Son İzlem Tarihi
      </th> 
    </tr>
  </thead>
  <tbody>

  <?php   

   foreach($rows as $row) {
       $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
       $bagimlilik = array('0' => 'Seçilmemiş', '1' => 'Yarı Bağımlı', '2' => 'Tam Bağımlı', '3' => 'Bağımsız');
      ?>
      
       <tr class="<?php echo $row->pasif ? "warning":"";?>">
       <td><span data-toggle="tooltip" title="<?php echo $row->toplamizlem;?> İzlem" class="label label-<?php echo $row->toplamizlem ? 'default':'warning';?>"><?php echo $row->toplamizlem;?></span></td>
       <th>
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
   <li class="divider"></li> 
    <li><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->hastatckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=izlemler&task=hedit&tc=<?php echo $row->hastatckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div>
</th>
      <td><?php echo $row->hastatckimlik;?></td>
      <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
       <td><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td> 
      <td><?php echo $row->izlemsayisi;?></td>
      <td><?php echo $bagimlilik[$row->bagimlilik];?></td>
      <td><?php echo tarihCevir($row->sonizlem, 1);?></td>
    </tr>
 <?php 

  }?>
    </tbody>
</table>




<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="temel" />
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script> 
<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>
 </div>
   </div>      <!-- panel-default -->     
   
   </form>    
<?php    
    }
    
    function hMahalle($rows, $total, $ilceler) {
        ?>
       <div class="panel panel-default">
        <div class="panel-heading">
        
        <div class="row">
        <div class="col-xs-10"><h4><i class="fa-solid fa-chart-column"></i> Mahalleye Göre Hasta Sayıları</div>
        <div class="col-xs-2" align="right">
        <form action="index.php" method="get" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
        <input type="hidden" name="bolum" value="stats" />
        <input type="hidden" name="task" value="hmahalle" />
        <div class="input-group">
        <?php echo $ilceler;?>
        <div class="input-group-btn">
      <button class="btn btn-default" type="submit">
        <i class="glyphicon glyphicon-search"></i>
      </button>
      </div>
 </div>
        
        </form>
        </div>
        </div>
        
        </div>
        
        <div class="panel-body">
        
    <?php 
    $i = 0;
      foreach ($rows as $data) {
          $data->mahalle = $data->mahalle ? $data->mahalle : 'Boş';
          
          $d['text'][$i] = "'".$data->mahalle."'";
          $d['value'][$i] = $data->sayi;
          $i++;
      }
      ?>                          
  <canvas id="myChart"></canvas>
</div>
<script src="<?php echo SITEURL;?>/admin/modules/stats/chart.js"></script>
<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: { 
      labels: [<?php echo implode(',', $d['text']);?>],
      datasets: [{
        label: 'Kişi Sayısı',
        data: [<?php echo implode(',', $d['value']);?>],
         backgroundColor: '#ffaaff'
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
    <th scope="col">İlçe Adı</th>
      <th scope="col">Mahalle Adı</th>
      <th scope="col">Hasta Sayısı</th>
      <th scope="col">Toplam Hastaya Oranı</th>
    </tr>
    </thead>
    <tbody>
  <?php 

  foreach ($rows as $row) { 
   
  ?>
  <tr>
  <th><?php echo $row->ilce ? $row->ilce : 'İlçe Girilmemiş';?></th>
    <th><?php echo $row->mahalle ? $row->mahalle : 'Mahalle Girilmemiş';?></th>
    <td><?php echo $row->sayi;?></td>
    <td>% <?php echo round(($row->sayi*100)/$total, 2);?></td>
      </tr>
  <?php 
  } ?>
  
  <tr>
    <th>TOPLAM:</th>
    <th></th>
    <th><?php echo $total;?></th>
    <th></th> 
  </tr>
  </tbody>
  </table>
  
  </div>
  
  </div><!-- panel-default -->

        <?php
    }
    
    function hKayityili($rows) {
        ?>
       <div class="panel panel-default">
        <div class="panel-heading"><h4><i class="fa-solid fa-chart-bar"></i> Kayıt Yılına Göre Aktif Hasta Sayıları</h4></div> 
         <div>
    <?php 
    
       $data = array();
       
    foreach ($rows['erkek'] as $erkek) {
        if (!isset($data[$erkek->kayityili])) {
             $data[$erkek->kayityili] = $erkek->sayi;
         }
    }
    
    foreach ($rows['kadin'] as $kadin) {
        if (isset($data[$kadin->kayityili])) {
             $data[$kadin->kayityili] += $kadin->sayi;
         }
    }
    
     $i = 0; 
      foreach ($rows['erkek'] as $dataerkek) {
          $dataerkek->kayityili = $dataerkek->kayityili ? $dataerkek->kayityili : 'B';
          $d['text'][$i] = $dataerkek->kayityili;
          $de['value'][$i] = $dataerkek->sayi;
          $i++;
      }
      
      $x = 0;
      foreach ($rows['kadin'] as $datakadin) {
          $datakadin->kayityili = $datakadin->kayityili ? $datakadin->kayityili : 'B';
          $dk['value'][$x] = $datakadin->sayi;
          $x++;
      }
      ?>                          
  <canvas id="myChart"></canvas>
</div>

<script src="<?php echo SITEURL;?>/admin/modules/stats/chart.js"></script>

<script>

  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [<?php echo implode(',', $d['text']);?>],
      datasets: [{
        label: 'Kadın Sayısı',
        data: [<?php echo implode(',', $dk['value']);?>],
        backgroundColor: 'pink',
        borderWidth: 1,
        stack: '1'
      },
      {
      label: 'Erkek Sayısı',
        data: [<?php echo implode(',', $de['value']);?>],
        backgroundColor: 'blue',
        borderWidth: 1,
        stack: '1'
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          stacked: true
        }
      }
    }
  });
</script>

        <table class="table table-striped">
        <thead class="thead-dark"> 
        <tr>
      <th scope="col">Kayıt Yılı</th>
      <th scope="col">Hasta Sayısı</th>
      <th scope="col">Toplam Hastaya Oranı</th>
    </tr>
    </thead>
    <tbody> 
  <?php 
  $toran = 0;
  $total = 0;
  foreach ($data as $k=>$v) {
  $total += $v;
  $toran += $toran;
  }
  
  foreach ($data as $k=>$v) {
  ?>
  <tr>
  <th><?php echo $k;?></th>
  <td><?php echo $v;?></td>
  <td>% <?php echo round(($v*100)/$total, 2);?></td>
  </tr>
  <?php 
  } 
  ?>
   <tr>
      <th scope="col">TOPLAM:</th>
      <th scope="col"><?php echo $total;?></th>
      <th></th>
    </tr>
  </tbody>
  </table>
  
   </div>
   <?php
    }
    
    function hKayitayi($rows) {
             // kayıt ayını seç
        $aylar = array('' => 'Boş', '01' => 'Ocak', '02' => 'Şubat', '03' => 'Mart', '04' => 'Nisan', '05' => 'Mayıs', '06' => 'Haziran', '07' => 'Temmuz', '08' => 'Ağustos', '09' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık');
        ?>
       <div class="panel panel-default">
        <div class="panel-heading"><h4><i class="fa-solid fa-chart-bar"></i> Kayıt Ayına Göre Hasta Sayıları</h4></div> 
         <div>
    <?php 
    
       $data = array();
       
    foreach ($rows['erkek'] as $erkek) {
        if (!isset($data[$erkek->kayitay])) {
             $data[$erkek->kayitay] = $erkek->sayi;
         }
    }
    
    foreach ($rows['kadin'] as $kadin) {
        if (isset($data[$kadin->kayitay])) {
             $data[$kadin->kayitay] += $kadin->sayi;
         }
    }
    
     $i = 0; 
      foreach ($rows['erkek'] as $dataerkek) {
          $dataerkek->kayitay = $dataerkek->kayitay ? $dataerkek->kayitay : 'B';
          $d['text'][$i] = $dataerkek->kayitay ? $aylar[$dataerkek->kayitay] : 'Boş';
          $de['value'][$i] = $dataerkek->sayi;
          $i++;
      }
      
      $x = 0;
      foreach ($rows['kadin'] as $datakadin) {
          $datakadin->kayitay = $datakadin->kayitay ? $datakadin->kayitay : 'B';
          $dk['value'][$x] = $datakadin->sayi;
          $x++;
      }
      ?>                          
  <canvas id="myChart"></canvas>
</div>

<script src="<?php echo SITEURL;?>/admin/modules/stats/chart.js"></script>

<script>

  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ["<?php echo implode('","', $d['text']);?>"],
      datasets: [{
        label: 'Kadın Sayısı',
        data: [<?php echo implode(',', $dk['value']);?>],
        backgroundColor: 'pink',
        borderWidth: 1,
        stack: '1'
      },
      {
      label: 'Erkek Sayısı',
        data: [<?php echo implode(',', $de['value']);?>],
        backgroundColor: 'blue',
        borderWidth: 1,
        stack: '1'
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          stacked: true
        }
      }
    }
  });
</script>

        <table class="table table-striped">
        <thead class="thead-dark"> 
        <tr>
      <th scope="col">Kayıt Ayı</th>
      <th scope="col">Hasta Sayısı</th>
      <th scope="col">Toplam Hastaya Oranı</th>
    </tr>
    </thead>
    <tbody> 
  <?php 
  $toran = 0;
  $total = 0;
  
  foreach ($data as $k=>$v) {
  $total += $v;    
  $toran += $toran; 
  }
  foreach ($data as $k=>$v) {
  ?>
  <tr>
  <th><?php echo $aylar[$k];?></th>
  <td><?php echo $v;?></td>
  <td>% <?php echo round(($v*100)/$total, 2);?></td>
  </tr>
  <?php 
  } 
  ?>
   <tr>
      <th scope="col">TOPLAM:</th>
      <th scope="col"><?php echo $total;?></th>
      <th></th>
    </tr>
  </tbody>
  </table>
  
   </div>
   <?php
    }

}
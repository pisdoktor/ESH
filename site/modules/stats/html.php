<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class StatsHTML {
    
    static function hastalikGirilmemis($rows) {
        ?>
         <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-10"><h4>Bilgileri Eksik Olan Hastalar</h4></div>
        <div class="col-xs-2" align="right"><h5>Toplam <?php echo count($rows);?> hasta</h5></div>
        </div>
    </div>
    <table class="table table-striped">
    <thead>
    <tr>
    <th>Hasta Adı</th>
    <th>TC Kimlik</th>
    <th>Mahalle</th>
    <th>Doğum Tarihi</th>
    <th>Yaşı</th>
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
     <th scope="row">
     <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->isim;?> <?php echo $row->soyisim;?></a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
     </th>
     <td><?php echo $row->tckimlik;?></td>
     <td><?php echo $row->mahalle;?></td>
     <td><?php echo $row->dtarihi;?></td>
     <td><?php echo yas_bul($row->dogumtarihi);?></td>
     </tr>
    <?php
    }
    ?>
    </tbody>
    </table>
    </div>
        <?php
    
    }
    
        static function personelGetir($data, $baslangictarih, $bitistarih) {
        ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="site" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="personel" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-11"><h4>İki Tarih Arası Personel İşlem Sayıları</h4></div>
        <div class="col-xs-1" align="right"></div>
        </div>
    </div>
        
    <div class="panel-body">
 
     <div class="form-group row">      
      
    <div class="col-sm-6">
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" />
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" />
    <div class="input-group-addon">arası</div>
    </div>
    </div>

    <div class="col-sm-1">
    <input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('personel');" class="btn btn-primary"  />
    </div>
    
    </div> <!-- form-group row -->
    
    </div> <!-- panel body -->
    
    <table class="table table-striped">
    <thead>
    <tr>
    <th>Personel Adı</th>
    <th>İşlem Sayısı</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($data as $d) {
        ?>
         <tr>
         <td><?php echo $d['personeladi'];?></td>
         <td><?php echo $d['toplam'];?></td>
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
    </div>
    </form>   
    <?php
    }
    
    static function islemGetir($data, $baslangictarih, $bitistarih) {
        ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="site" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="islem" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-11"><h4>İki Tarih Arası Yapılan İşlemler</h4></div>
        <div class="col-xs-1" align="right"></div>
        </div>
    </div>
        
    <div class="panel-body">
 
     <div class="form-group row">      
      
    <div class="col-sm-6">
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" />
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" />
    <div class="input-group-addon">arası</div>
    </div>
    </div>

    <div class="col-sm-1">
    <input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('islem');" class="btn btn-primary"  />
    </div>
    
    </div> <!-- form-group row -->
    
    </div> <!-- panel body -->
    
    <table class="table table-striped">
    <thead>
    <tr>
    <th>İşlem Adı</th>
    <th>Yapılma Sayısı</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($data as $d) {
        ?>
         <tr>
         <td><?php echo $d['islemadi'];?></td>
         <td><?php echo $d['toplam'];?></td>
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
    </div>
    </form>   
    <?php
    }
    
    function adresHastaFiltre($rows, $lists, $ilce, $mahalle, $sokak, $kapino, $ordering, $pageNav) {
        $link = 'index.php?option=site&bolum=stats&task=adres';
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
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
        
    ?>
<form action="index.php" method="GET" name="adminForm" role="form">
<input type="hidden" name="option" value="site" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="adres" /> 

<div class="row">
<div class="col-sm-3">

<div class="panel panel-default" id="leftside"> 
<div class="panel-heading"><h4>Filtreleme Seçenekleri</h4></div>
        
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
                url: "index2.php?option=site&bolum=stats",
                type: "GET",
                data: {task:task, id:id},
                success: function(sonuc){
                    $.each(JSON.parse(sonuc), function(key, value){
                        console.log(sonuc);
                        $(name).append("<option value="+key+">"+value+"</option>");
                    });
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
    <div class="col-xs-11"><h4>Adrese Göre Hastalar</h4></div>
    <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
</div>
</div>
    <table class="table table-striped">
    <thead class="thead-dark">
    <tr>
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
  <span><a href="<?php echo $link;?>&ordering=h.ilce-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.ilce-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col"> <div>
      Mahalle
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col"> <div>
      Cadde/Sokak
  <span><a href="<?php echo $link;?>&ordering=h.sokak-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.sokak-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col"> <div>
      Kapı No
  <span><a href="<?php echo $link;?>&ordering=h.kapino-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kapino-ASC">▼</a></span>
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
         
       $link2 = "index.php?option=site&bolum=hastalar&task=edit&id=".$row->id;
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
      <th scope="row">
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->isim;?> <?php echo $row->soyisim;?>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
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

</div>
   
</form> 
<?php    
    }
    
    function specialGetir($rows, $title, $secim) {
        ?>
        <div class="panel panel-default">
        <div class="panel-heading"><h4>Özellikli Durumlarına Göre Hastalar - <?php echo $title;?></h4></div>
        
        <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Hasta Adı Soyadı</th>
      <th scope="col">TC Kimlik Numarası</th>
      <th scope="col">Mahalle</th> 
      <th scope="col">Kayıt Yılı</th>
      <?php if ($secim == sonda) { ?>
      <th scope="col">Sonda Değişim Tarihi</th>
      <?php } ?>
      <th scope="col">Doğum Tarihi</th> 
      <th scope="col">Yaşı</th>  
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
      <th scope="col">
      <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->isim;?> <?php echo $row->soyisim;?>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
      </th>
      <td scope="col"><?php echo $row->tckimlik;?></td>
      <td scope="col"><?php echo $row->mahalle;?></td>
      <td scope="col"><?php echo $row->kayityili;?></td>
      <?php if ($secim == sonda) { ?>
      <th scope="col"><?php echo tarihCevir($row->sondatarihi, 1);?></th>
      <?php } ?>
      <td><?php echo $row->dtarihi;?></td>
      <td><?php echo yas_bul($row->dogumtarihi);?></td>  
    </tr>
      <?php   
     }
   
   ?>
  </tbody>
  </table>
  <div class="panel-footer">
<div class="form-group row">
<div class="col-sm-7">
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
</div>
</div>
</div> 
        </div>
      
      <?php 
    }
    
    function Izlenmeyenler($rows, $secimlist, $secim, $ordering, $pageNav) {
        $link = 'index.php?option=site&bolum=stats&task=izlenmeyen';
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
        <div class="col-xs-11"><h4>İzlem Girilmeyen Hastalar</h4></div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
        </div>
        <div class="panel-body">
        
        <div class="form-group row">
        
        <div class="col-sm-3">
        <div class="input-group">
      <div class="input-group-addon">Son</div>
       <?php echo $secimlist;?> 
      <div class="input-group-addon">İzlemi Yapılmayanlar</div>
      </div>
        </div>
        
        <div class="col-sm-2">
        <input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('izlenmeyen');" class="btn btn-primary"  />
        </div>
        
        </div> <!-- form-group -->
        </div> <!-- panel-body -->  
        
  
    <table class="table table-striped">
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
      
      ?>
      
      <tr>
      <td scope="row"><span data-toggle="tooltip" title="<?php echo $row['izlemsayisi'];?> İzlem" class="label label-warning"><?php echo $row['izlemsayisi'];?></span></td>
       <th>
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row['isim'];?>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row['id'];?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row['id'];?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row['tckimlik'];?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row['tckimlik'];?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
</th>
      <td><?php echo $row['tckimlik'];?></td>
      <td><?php echo $row['mahalle'];?></td>
      <td><?php echo $row['kayityili'];?> <?php echo $aylar[$row['kayitay']];?></td> 
      <td><?php echo $row['sonizlem'] ? tarihCevir($row['sonizlem'], 1) : 'Yok';?></td>
    </tr>
 <?php 

  }?>
    </tbody>
</table>



<input type="hidden" name="option" value="site" />
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
    
    function dogumGunuGetir($list) {
    ?>    
<div class="panel panel-default">
        <div class="panel-heading"><h4>Bugün Doğum Günü Olan Hastalar</h4></div>
              
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
       <th>
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $k['isim'];?> <?php echo $k['soyisim'];?>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $k['id'];?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $k['id'];?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $k['tc'];?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $k['tc'];?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
       </th>
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
    
    function specialStats($s) {
        ?>
        <div class="panel panel-default">
        <div class="panel-heading"><h4>Özellikli Durumlarına Göre Hastalar</h4></div>
        <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Özellikli Durum Adı</th>
      <th scope="col">Hasta Sayısı</th> 
    </tr>
  </thead>
  <tbody>
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=ventilator">Ventilatör Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['ventilator'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=kolostomi">Kolostomi Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['kolostomi'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=o2bagimli">Oksijen Bağımlı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['o2bagimli'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=ng">Nazogastrik Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['ng'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=peg">PEG Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['peg'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=port">Port Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['port'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=gecici">Geçici Takipli Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['gecici'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=site&bolum=stats&task=specialgetir&secim=sonda">Sonda Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['sonda'];?>
  </td>
  </tr>
  
  </tbody>
  </table>
        </div>
        
      <?php  
    }
    
    function hastalikStats($tab, $total, $thasta) {
        ?>
      <div class="panel panel-default">
        <div class="panel-heading"><h4>Hastalıklarına Göre Hastalar</h4></div>
        <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Hastalık</th>
      <th scope="col">Hasta Sayısı</th> 
      <th scope="col">Toplam Hastaya Oranı</th>
    </tr>
  </thead>
  <tbody>
        <?php
         foreach ($tab as $k=>$v) {
             foreach ($v as $i=>$s) {
             ?>
          <tr>
          <th><?php echo $s;?></th>
          <td><?php echo $total[$i];?></td>
          <td><?php echo round(($total[$i]*100)/$thasta, 2);?> %</td>
          </tr>   
         <?php    
             }
         }
        ?>
    </tbody>
</table>
        </div>
        
     <?php   
    }
    
    function temelStats($rows, $toplamizlem, $toplamhasta, $pageNav, $baslangictarih, $bitistarih, $ordering) {
    $link = 'index.php?option=site&bolum=stats';

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
        <div class="col-xs-11"><h4>İki Tarih Arası İzlem Yapılan Hastalar</h4></div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
    </div>
        
    <div class="panel-body">
 
     <div class="form-group row">      
      
    <div class="col-sm-6">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" />
    
    
    <div class="input-group-addon">ile</div>
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" />
    
    
    <div class="input-group-addon">arası</div>
     
    </div>
    
    
    </div>

    <div class="col-sm-1">
    <input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('list');" class="btn btn-primary"  />
    </div>
    
    <div class="col-sm-5">
    <div class="btn-group">
     <button type="button" class="btn btn-sm btn-warning">İZLEM SIKLIĞI <span class="badge badge-light"><?php echo round($toplamizlem/$toplamhasta, 2);?></span></button>
     <button type="button" class="btn btn-sm btn-info">TOPLAM HASTA <span class="badge badge-light"><?php echo $toplamhasta;?></span></button>
     <button type="button" class="btn btn-sm btn-success">TOPLAM İZLEM <span class="badge badge-light"><?php echo $toplamizlem;?></span></button>
    </div>
    </div>
    
    </div> <!-- form-group row-->      
  
  </div> <!-- panel-body-->
    <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
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
    </tr>
  </thead>
  <tbody>

  <?php   

   foreach($rows as $row) {
       $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
      
      ?>
      
       <tr class="<?php echo $row->cinsiyet == "E" ? "success":"warning";?>">
       <th>
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->isim;?> <?php echo $row->soyisim;?>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->hastatckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row->hastatckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div>
</th>
      <td><?php echo $row->hastatckimlik;?></td>
      <td><?php echo $row->mahalle;?></td>
       <td><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td> 
      <td><?php echo $row->izlemsayisi;?></td>
    </tr>
 <?php 

  }?>
    </tbody>
</table>




<input type="hidden" name="option" value="site" />
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
    
    function hYasGruplari($yasaralik) {
        ?>
         <div class="panel panel-default">
        <div class="panel-heading"><h4>Yaş Gruplarına Göre Hasta Sayıları</h4></div>
                 <div>                         
  <canvas id="myChart"></canvas>
</div>

<script src="<?php echo SITEURL;?>/site/modules/stats/chart.js"></script>

<script>

  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['0-1 Aylık', '2 Ay-2 Yaş', '3-18 Yaş', '19-45 Yaş', '46-65 Yaş', '66-85 Yaş', '85 Yaş Üzeri'],
      datasets: [{
        label: 'Kişi Sayısı',
        data: [<?php echo $yasaralik[01].','.$yasaralik[22].','.$yasaralik[318].','.$yasaralik[1945].','.$yasaralik[4665].','.$yasaralik[6685].','.$yasaralik[86];?>
        ],
        borderWidth: 1
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
      <th scope="col">Hasta Sayısı</th>
      <th scope="col">Genele Oranı</th> 
    </tr>
    </thead>
    
    <tbody>
    <tr>
    <th>0-1 Aylık</th>
    <td><?php echo $yasaralik[01];?></td>
    <td><div class="progress-bar" style="width:<?php echo round(($yasaralik[01]*100)/$yasaralik['toplam'], 2);?>%"><?php echo round(($yasaralik[01]*100)/$yasaralik['toplam'], 2);?>%</div></td>
    </tr>
    
     <tr>
    <th>2 Ay-2 Yaş</th>
    <td><?php echo $yasaralik[22];?></td>
    <td><div class="progress-bar" style="width:<?php echo round(($yasaralik[22]*100)/$yasaralik['toplam'], 2);?>%"><?php echo round(($yasaralik[22]*100)/$yasaralik['toplam'], 2);?>%</div></td>
    </tr>
    
     <tr>
    <th>3-18 Yaş</th>
    <td><?php echo $yasaralik[318];?></td>
    <td><div class="progress-bar" style="width:<?php echo round(($yasaralik[318]*100)/$yasaralik['toplam'], 2);?>%"><?php echo round(($yasaralik[318]*100)/$yasaralik['toplam'], 2);?>%</div></td>
    </tr>
    
    <tr>
    <th>19-45 Yaş</th>
    <td><?php echo $yasaralik[1945];?></td>
    <td><div class="progress-bar" style="width:<?php echo round(($yasaralik[1945]*100)/$yasaralik['toplam'], 2);?>%"><?php echo round(($yasaralik[1945]*100)/$yasaralik['toplam'], 2);?>%</div></td>
    </tr>
    
    <tr>
    <th>46-65 Yaş</th>
    <td><?php echo $yasaralik[4665];?></td>
    <td><div class="progress-bar" style="width:<?php echo round(($yasaralik[4665]*100)/$yasaralik['toplam'], 2);?>%"><?php echo round(($yasaralik[4665]*100)/$yasaralik['toplam'], 2);?>%</div></td>
    </tr>
    
    <tr>
    <th>66-85 Yaş</th>
    <td><?php echo $yasaralik[6685];?></td>
    <td><div class="progress-bar" style="width:<?php echo round(($yasaralik[6685]*100)/$yasaralik['toplam'], 2);?>%"><?php echo round(($yasaralik[6685]*100)/$yasaralik['toplam'], 2);?>%</div></td>
    </tr>
    
    <tr>
    <th>86 Yaş ve Üzeri</th>
    <td><?php echo $yasaralik[86];?></td>
    <td><div class="progress-bar" style="width:<?php echo round(($yasaralik[86]*100)/$yasaralik['toplam'], 2);?>%"><?php echo round(($yasaralik[86]*100)/$yasaralik['toplam'], 2);?>%</div></td>
    </tr>
    
    <tr>
    <th>TOPLAM:</th>
    <th><?php echo $yasaralik['toplam'];?></th>
    <th>100%</th>
    </tr>
    
    </tbody>
    </table>
        
        </div> 
     
        <?php
    }
    
    function hMahalle($rows, $total) {
        ?>
       <div class="panel panel-default">
        <div class="panel-heading"><h4>Mahalleye Göre Hasta Sayıları</h4></div>
         <div>
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
<script src="<?php echo SITEURL;?>/site/modules/stats/chart.js"></script>
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
      </tr>
  <?php 
  } ?>
  
  <tr>
    <th>TOPLAM:</th>
    <th></th>
    <th><?php echo $total;?></th>
  </tr>
  </tbody>
  </table>
  
  </div><!-- panel-default -->

        <?php
    }
    
     function hKayityili($rows) {
        ?>
       <div class="panel panel-default">
        <div class="panel-heading"><h4>Kayıt Yılına Göre Hasta Sayıları</h4></div> 
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

<script src="<?php echo SITEURL;?>/site/modules/stats/chart.js"></script>

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
      <th scope="col">Toplam Hasta Sayısı</th>
    </tr>
    </thead>
    <tbody> 
  <?php 
  $toran = 0;
  $total = 0;
  foreach ($data as $k=>$v) {
  $total += $v;
  ?>
  <tr>
  <th><?php echo $k;?></th>
  <td><?php echo $v;?></td>
  </tr>
  <?php 
  $toran = $toran + $oran;
  } ?>
   <tr>
      <th scope="col">TOPLAM:</th>
      <th scope="col"><?php echo $total;?></th>
    </tr>
  </tbody>
  </table>
  
   </div>
   <?php
    }
    
}
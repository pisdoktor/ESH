<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class StatsHTML {
    
    function hastalikGirilmemis($rows) {
        ?>
         <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-10"><h4><i class="fa-solid fa-circle-half-stroke"></i> Bilgileri Eksik Olan Hastalar</h4></div>
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
     <a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></a>
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
    
function personelGetir($data, $baslangictarih, $bitistarih) {
        ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="personel" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-11"><h4><i class="fa-solid fa-square-poll-vertical"></i> İki Tarih Arası Personel İşlem Sayıları</h4></div>
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
    
function islemGetir($data, $baslangictarih, $bitistarih) {
        ?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="islem" />
    <div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
        <div class="col-xs-11"><h4><i class="fa-solid fa-file-invoice"></i> İki Tarih Arası Yapılan İşlemler</h4></div>
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
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
        
    ?>
<form action="index.php" method="GET" name="adminForm" role="form">
<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="stats" />
<input type="hidden" name="task" value="adres" /> 

<div class="row">
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
    <div class="col-xs-11"><h4><i class="fa-solid fa-globe"></i> Adrese Göre Hastalar</h4></div>
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
      <th scope="row">
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->isim;?> <?php echo $row->soyisim;?>
</a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
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
        <div class="panel-heading"><h4><i class="fa-solid fa-ranking-star"></i> Özellikli Durumlarına Göre Hastalar - <?php echo $title;?></h4></div>
        
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
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
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
        <div class="col-xs-11"><h4><i class="fa-solid fa-hospital-user"></i> İzlem Girilmeyen Hastalar</h4></div>
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
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row['id'];?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row['id'];?>">Bilgileri Düzenle</a></li>
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
      <th scope="col">Özellikli Durum Adı</th>
      <th scope="col">Hasta Sayısı</th> 
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
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=kolostomi">Kolostomi Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['kolostomi'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=o2bagimli">Oksijen Bağımlı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['o2bagimli'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=ng">Nazogastrik Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['ng'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=peg">PEG Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['peg'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=port">Port Takılı Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['port'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=gecici">Geçici Takipli Hasta Sayısı</a>
  </th>
  <td>
  <?php echo $s['gecici'];?>
  </td>
  </tr>
  
  <tr>
  <th>
  <a href="index.php?option=admin&bolum=stats&task=specialgetir&secim=sonda">Sonda Takılı Hasta Sayısı</a>
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
        <div class="panel-heading"><h4><i class="fa-solid fa-chart-area"></i> Hastalıklarına Göre Hastalar</h4></div>
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
        <div class="col-xs-11"><h4><i class="fa-solid fa-chart-gantt"></i> İki Tarih Arası İzlem Yapılan Hastalar</h4></div>
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
  <li><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=admin&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
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
    
    function hMahalle($rows, $total) {
        ?>
       <div class="panel panel-default">
        <div class="panel-heading"><h4><i class="fa-solid fa-chart-column"></i> Mahalleye Göre Hasta Sayıları</h4></div>
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
        <div class="panel-heading"><h4><i class="fa-solid fa-chart-bar"></i> Kayıt Yılına Göre Hasta Sayıları</h4></div> 
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
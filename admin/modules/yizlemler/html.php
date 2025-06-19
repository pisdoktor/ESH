<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class IzlemList {
    
static function IzlemGetir($hasta, $rows, $pageNav, $lists) {
        $link = 'index.php?option=admin&bolum=izlemler&task=izlemgetir&tc='.$hasta->tckimlik;
        
    ?>
    <form action="index.php" method="GET" name="adminForm" role="form"> 
<div class="panel panel-default">
        <div class="panel-heading">
        <div class="row">
    <div class="col-xs-11"><h4><i class="fa-solid fa-stethoscope"></i> İzlem Listesi: <a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $hasta->id;?>"><?php echo $hasta->isim.' '.$hasta->soyisim;?></a> <?php echo $hasta->pasif ? '<i class="fa-solid fa-triangle-exclamation"></i> DOSYA KAPALI ('.tarihCevir($hasta->pasiftarihi, 1).')':'';?></h4></div>
    <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>

    </div>
    </div>
    
        <div class="panel-body">
        
        <div align="left" style="float:left">
        <a href="index.php?option=admin&bolum=izlemler&task=hedit&tc=<?php echo $hasta->tckimlik;?>" class="btn btn-warning" />Yeni İzlem Gir</a>
        <?php if (!$hasta->pansuman) { ?>
        <a href="index.php?option=admin&bolum=pansuman&task=add&id=<?php echo $hasta->id;?>" class="btn btn-success" />Pansuman Takibine Al</a>
        <?php } else { ?>
        <a href="index.php?option=admin&bolum=pansuman&task=delete&id=<?php echo $hasta->id;?>" class="btn btn-info" />Pansuman Takibinden Çıkar</a>
        <?php } ?>
        <a href="#" data-toggle="modal" data-target="#sonda" class="btn btn-danger" />Sonda Tarihi Değiştir</a>
        <a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a> 
        </div>
    
        </div> <!-- panel-body -->
    
    <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">SIRA</th>
      <th scope="col">İzlem Tarihi</th>
      <th scope="col">Yapılan İşlem</th>
      <th scope="col">İzlemi Yapan</th>
    </tr>
  </thead>
  <tbody>

  <?php 
    $iz = new Izlem($dbase);
  $is = new Islem($dbase);
  
  $i = 0;
   foreach($rows as $row) {
      ?>
       <tr>
       <td><a href="index.php?option=admin&bolum=izlemler&task=edit&id=<?php echo $row->id;?>"><?php echo $pageNav->rowNumber( $i );?></a></td>
      <th>
      <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo tarihCevir($row->izlemtarihi, 1);?></a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=izlemler&task=edit&id=<?php echo $row->id;?>">Düzenle</a></li>
  <li><a href="index.php?option=admin&bolum=izlemler&task=delete&id=<?php echo $row->id;?>">Sil</a></li>
  </ul>
</div>
</th>
      <td><?php echo $is->yapilanIslem($row->yapilan);?></td> 
      <td><?php echo $iz->IzlemiYapanlar($row->izlemiyapan);?> </td>
    </tr>
 <?php 
  $i++;
  }?>
  
    </tbody>
</table>
   </div>


<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="hastalar" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />

</form>
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
<!-- sonda bilgileri değiştirme -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>        
<!-- Modal -->
<div id="sonda" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Sonda Bilgileri: <?php echo $hasta->isim.' '.$hasta->soyisim;?></h4>
      </div>
        
        <table class="table table-striped">
        <tr>
        <td><label>Sonda Takılı:</label></td>
        <td><?php echo mosHTML::yesnoRadioList('sonda', '', $hasta->sonda);?></td>
        </tr>
        <tr id="sondacheck" style="display:none">  <!-- sondacheck --> 
        <td><label for="sondatarihi">Sonda Takılma Tarihi:</label></td>
        <td class='input-group date' id='sondatarihi1' data-date-format="dd.mm.yyyy">
        <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="sondatarihi" name="sondatarihi" value="<?php echo $hasta->sondatarihi ? tarihCevir($hasta->sondatarihi, 1) : '';?>" autocomplete="off" />
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </td>
        </tr> <!-- sondacheck -->
        </table>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
      
    </div> <!-- modal content -->

  </div>
</div> <!-- modal -->
<input type="hidden" name="option" value="admin">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savesonda">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>"> 
</form>

<script>                                  
$(document).ready(function(){
    
$('#sondatarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    
    if($("#sonda1").is(':checked')){
            $("#sondacheck").show();
        }else{
            $("#sondacheck").hide();
        }
        
     $("input[name=sonda]").change(function(){

        if($("#sonda1").is(':checked')){
            $("#sondacheck").show();
        }else{
            $("#sondacheck").hide();
        }
    });    
});
</script>
<!-- sonda bilgileri değiştirme -->
<?php    
        
    }
    
static function heditIzlem($row, $limit, $limitstart, $isyapilan, $isyapilacak, $hasta, $perlist) {
    ?>
    <form action="index.php" method="post">
    
    <div class="panel panel-primary">
    
    <div class="panel-heading"><h4><i class="fa-solid fa-hospital-user"></i> İzlem Ekle</h4></div>
    
    <div class="panel-body">
        
<div class="form-group row">
<div class="col-sm-3"><label for="hastatckimlik">Hastanın TC Kimlik No:</label></div>
<div class="col-sm-3"><input maxlength="11" type="text" id="hastatckimlik" name="hastatckimlik" class="form-control" value="<?php echo $hasta->tckimlik;?>" readonly required></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="hastaisim">Hastanın Adı:</label></div>
<div class="col-sm-3"><div class="form-control" id="sonuc"><?php echo $hasta ? $hasta->isim.' '.$hasta->soyisim : '';?></div></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="izlemtarihi">İzlem Tarihi:</label></div>  
<div class="col-sm-3 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="izlemtarihi" name="izlemtarihi" value="<?php echo date('d.m.Y');?>" autocomplete="off" required />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>

</div>  
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">İşlemi Yapan:</label></div>
<div class="col-sm-9"><?php echo $perlist;?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">Yapılan İşlem:</label></div>
<div class="col-sm-9"><?php echo $isyapilan;?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="planli">Planlı İşlem Var mı?</label></div>
<div class="col-sm-9"><?php echo mosHTML::yesnoRadioList('planli', '', 0);?></div>
</div>

<div id="planli"  style="display:none">
<div class="form-group row">
<div class="col-sm-3"><label for="izlemtarihi">Planlanan İzlem Tarihi:</label></div>  
<div class="col-sm-3 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="planlanantarih" name="planlanantarih" value="" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>

</div>  
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">Yapılacak İşlem:</label></div>
<div class="col-sm-9"><?php echo $isyapilacak;?></div>
</div>
</div>
    
<div class="form-group row">
<div class="col-sm-4">
<button type="submit" id="save" class="btn btn-primary">Kaydet</button>
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
</div>
</div>

    </div>
  
    </div>

    <input type="hidden" name="option" value="admin" />
    <input type="hidden" name="bolum" value="izlemler" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="id" value="" />
    <input type="hidden" name="limit" value="<?php echo $limit;?>" />
    <input type="hidden" name="limitstart" value="<?php echo $limitstart;?>" />
    <script type="text/javascript">
    $(document).ready(function(){
        
    $('#hastatckimlik').keyup(function(){
    var val = $('#hastatckimlik').val(); 
    var uzunluk = val.length;
    
    if (uzunluk==11) {
            $.ajax({
                url:'index2.php?&option=admin&bolum=izlemler&task=control&tc='+val,
                type:'GET',
                success:function(result){
                    $('#sonuc').html(result);
                }  
            });
    } else {
                    $('#sonuc').empty();
    }    
});

    $("input[name=planli]").change(function(){

        if($("#planli1").is(':checked')){
            $("#planli").show();
        }else{
            $("#planli").hide();
        }
    });
    
    if($("#planli1").is(':checked')){
            $("#planli").show();
    }
  }); 
  
    $('#planlanantarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    }); 

    $('#izlemtarihi').datepicker({
    todayHighlight: true,
        autoclose: true,
        language: "tr"

    }); 
</script>
    </form>
    <?php
    }
    
static function editIzlem($row, $limit, $limitstart, $isyapilan, $isyapilacak, $hasta, $perlist) {
    ?>
    <form action="index.php" method="post">
    
    <div class="panel panel-default">
    
    <div class="panel-heading"><h4><i class="fa-solid fa-hospital-user"></i> İzlem <?php echo $row->id ? 'Düzenle' : 'Ekle';?></h4></div>
    
    <div class="panel-body">
    
<div class="form-group row">
<div class="col-sm-3"><label for="hastatckimlik">Hastanın TC Kimlik No:</label></div>
<div class="col-sm-4"><input maxlength="11" type="text" id="hastatckimlik" name="hastatckimlik" class="form-control" value="<?php echo $row->hastatckimlik;?>" readonly required></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="hastaisim">Hastanın Adı:</label></div>
<div class="col-sm-4"><div class="form-control" id="sonuc"><?php echo $hasta ? $hasta->isim.' '.$hasta->soyisim : '';?></div></div>
</div>

<div class="form-group row">

<div class="col-sm-3"><label for="izlemtarihi">İzlem Tarihi:</label></div>  
<div class="col-sm-4 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="izlemtarihi" name="izlemtarihi" value="<?php echo $row->izlemtarihi ? tarihCevir($row->izlemtarihi, 1) : date('d.m.Y');?>" autocomplete="off" required />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
 <script type="text/javascript">
    $('#izlemtarihi').datepicker({
    todayHighlight: true,
        autoclose: true,
        language: "tr"

    }); 
</script>
</div>
  
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">İşlemi Yapan:</label></div>
<div class="col-sm-9"><?php echo $perlist;?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">Yapılan İşlem:</label></div>
<div class="col-sm-9"><?php echo $isyapilan;?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="planli">Planlı İşlem Var mı?</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('planli', '', $row->planli);?></div>
</div>

<div id="planli"  style="display:none">

<div class="form-group row">
<div class="col-sm-3"><label for="izlemtarihi">Planlanan İzlem Tarihi:</label></div>  
<div class="col-sm-4 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="planlanantarih" name="planlanantarih" value="<?php echo $row->planlanantarih ? tarihCevir($row->planlanantarih, 1) : '';?>" autocomplete="off" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
 <script type="text/javascript">
    $('#planlanantarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
    }); 
</script>
</div>  
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">Yapılacak İşlem:</label></div>
<div class="col-sm-9"><?php echo $isyapilacak;?></div>
</div>

</div> <!-- planli -->
    
<div class="form-group row">
<div class="col-sm-4">
<button type="submit" id="save" class="btn btn-primary">Kaydet</button>
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a></div>
</div>


    </div>
  
    </div>

    <input type="hidden" name="option" value="admin" />
    <input type="hidden" name="bolum" value="izlemler" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="id" value="<?php echo $row->id;?>" />
    <input type="hidden" name="limit" value="<?php echo $limit;?>" />
    <input type="hidden" name="limitstart" value="<?php echo $limitstart;?>" />
    <script type="text/javascript">
    $(document).ready(function(){
            
    $('#hastatckimlik').keyup(function(){
    var val = $('#hastatckimlik').val(); 
    var uzunluk = val.length;
    
    if (uzunluk==11) {
            $.ajax({
                url:'index2.php?&option=admin&bolum=izlemler&task=control&tc='+val,
                type:'GET',
                success:function(result){
                    $('#sonuc').html(result);
                }  
            });
    } else {
                    $('#sonuc').empty();
    }    
});

    $("input[name=planli]").change(function(){

        if($("#planli1").is(':checked')){
            $("#planli").show();
        }else{
            $("#planli").hide();
        }
    });
    if($("#planli1").is(':checked')){
            $("#planli").show();
    }
  }); 
</script>
    </form>
    <?php
    }
    
static function getIzlemList($rows, $pageNav, $baslangictarih, $bitistarih, $ordering, $list, $secim, $search) {
        $link = 'index.php?option=admin&bolum=yizlemler';

            $link .= '&baslangictarih='.$baslangictarih;
            $link .= '&bitistarih='.$bitistarih;   
 
        
            if ($ordering) {
                $link .= "&ordering=".$ordering;
            }
            if ($secim) {
                $link .= "&secim=".$secim;
            }
    ?>
    <form action="index.php" method="GET" name="adminForm" role="form">
    <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="yizlemler" />
<input type="hidden" name="task" value="" />
    <div class="panel panel-warning">
    <div class="panel-heading">
     <div class="row">
        <div class="col-xs-3"><h4><i class="fa-solid fa-file-medical"></i> Tüm Yapılmayan İzlemler</h4></div>
        <div class="col-xs-8">
        <div class="form-group row">      
      
    <div class="col-sm-5">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" autocomplete="off" />
    
    
    <div class="input-group-addon">ile</div>
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" autocomplete="off" />
    
    
    <div class="input-group-addon">arası</div>
     
    </div>
    
    
    </div>
    
    <div class="col-sm-3">
    <div class="input-group">
<input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control"  onChange="document.adminForm.submit();" placeholder="TC Kimlik yada Bir isim Yazın" autocomplete="off">
<div class="input-group-btn"><button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button></div>
</div>
    </div>
    
     <div class="col-sm-2">
    <?php echo $list['islem'];?>
    </div>

    <div class="col-sm-1">
    <input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('list');" class="btn btn-primary"  />
    </div>
    
    </div> <!-- form-group row-->
        </div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
    </div>
    
    <table class="table table-hover">
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
      <th scope="col">İzlem Tarihi
       <span><a href="<?php echo $link;?>&ordering=i.izlemtarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=i.izlemtarihi-ASC">▼</a></span>
  </th>
      <th scope="col">Yapılmayan İşlem
      <span><a href="<?php echo $link;?>&ordering=i.yapilan-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=i.yapilan-ASC">▼</a></span>
      </th>
      <th scope="col">İzlemi Yapan</th>
      <th scope="col">Planlı İzlem
            <span><a href="<?php echo $link;?>&ordering=i.planli-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=i.planli-ASC">▼</a></span>
      </th>
    </tr>
    </thead>
    <tbody>
    <?php 
  $iz = new Izlem($dbase);
  $is = new Islem($dbase);
  
   foreach($rows as $row) {
      ?>     
   <tr class="<?php echo $row->pasif ? 'warning':'default';?>">
      <th scope="row">
      <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span> <?php echo $row->gecici ? '<sub>(G)</sub>':'';?>
</a>
  <ul class="dropdown-menu">
    <li><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->hastatckimlik;?>">İzlemlerini Göster</a></li>
    <li class="divider"></li>    
    <li><a href="index.php?option=admin&bolum=izlemler&task=edit&id=<?php echo $row->id;?>">İzlemi Düzenle</a></li>
    <li><a href="index.php?option=admin&bolum=izlemler&task=delete&id=<?php echo $row->id;?>">İzlemi Sil</a></li>
  </ul>
</div> 
      </th>
      <td>
      <a href="index.php?option=admin&bolum=izlemler&task=edit&id=<?php echo $row->id;?>"><?php echo $row->hastatckimlik;?></a>
      </td>
      <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
      <td><?php echo tarihCevir($row->izlemtarihi, 1);?></td>
      <td><?php echo $is->yapilanIslem($row->yapilan);?></td> 
      <td><?php echo $iz->IzlemiYapanlar($row->izlemiyapan);?> </td>
      <td><?php echo $row->planli ? 'Evet': 'Hayır';?></td>
   </tr>
 <?php 

  }?>
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

   </div> <!-- panel-default -->   
</form>    
<?php    
    }

}
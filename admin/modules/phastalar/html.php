<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class HastaList {
    
    function getHastaList($rows, $pageNav, $search, $baslangictarih, $bitistarih, $ordering, $pasifneden, $secim) {
        $link = 'index.php?option=admin&bolum=phastalar';
        if ($search) {
            $link .= "&amp;search=".$search;
        }
            $link .= "&amp;baslangictarih=".$baslangictarih;
            $link .= "&amp;bitistarih=".$bitistarih;     
        
         if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
        if ($secim) {
            $link .= "&secim=".$secim;
        }
    ?>
    <form action="index.php" method="GET" name="adminForm" role="form"> 
    <div class="panel panel-warning">
    <div class="panel-heading">
    <div class="row">
        <div class="col-xs-2"><h4><i class="fa-solid fa-bed-pulse"></i> Pasif Hasta Listesi</h4></div>
        <div class="col-xs-9">
        <div class="form-group row">      
      
    <div class="col-sm-5">
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" autocomplete="off" />
    <div class="input-group-addon">ile</div>
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" autocomplete="off" />
    <div class="input-group-addon">arası</div>
    </div>
    </div>
    
    <div class="col-sm-2">
    <?php echo $pasifneden;?>
    </div>
    
    <div class="col-sm-3">
    <input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control"  onChange="document.adminForm.submit();" placeholder="TC Kimlik yada Bir isim Yazın" autocomplete="off">
    </div>
    


    <div class="col-sm-2">
    <input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('list');" class="btn btn-warning"  />
    </div>
    
    </div> <!-- form-group row-->
        </div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
    </div>
    
    <table class="table table-hover">
    <thead class="thead-light">
    <tr>
    <th scope="col">#</th>
    
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
      <th scope="col">Pasif Tarihi
        <span><a href="<?php echo $link;?>&ordering=h.pasiftarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.pasiftarihi-ASC">▼</a></span>
  </th>
      <th scope="col">Pasif Nedeni
        <span><a href="<?php echo $link;?>&ordering=h.pasifnedeni-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.pasifnedeni-ASC">▼</a></span>
  </th>
  <th scope="col">Son İzlem Tarihi</th> 
    </tr>
    </thead>
    <tbody>
    <?php
    $pasif = array(
  '1' => 'İyileşme',
  '2' => 'Vefat',
  '3' => 'İkamet Değişikliği',
  '4' => 'Tedaviyi Reddetme',
  '5' => 'Tedaviye Yanıt Alamama',
  '6' => 'Sonlandırmanın Talep Edilmesi',
  '7' => 'Tedaviye Personel Gerekmemesi',
  '8' => 'ESH Takibine Uygun Olmaması'
  ); 
    foreach($rows as $row) {
        $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
       $row->sonizlemtarihi = $row->sonizlemtarihi ? tarihCevir($row->sonizlemtarihi, 1):'Yok'; 
      ?>
   <tr>
   <td><span data-toggle="tooltip" title="<?php echo $row->izlemsayisi;?> İzlem" class="label label-<?php echo $row->izlemsayisi ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->izlemsayisi;?></a></span></td>
      <th scope="row"><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></a></th>
      <td><?php echo $row->tckimlik;?></td>
      <td><?php echo $row->mahalleadi;?> <span class="label label-success"><?php echo $row->ilceadi;?></span></td>
      <td><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td>
      <td><?php echo tarihCevir($row->pasiftarihi, 1);?></td>
      <th scope="row"><?php echo $pasif[$row->pasifnedeni];?></th>
      <td><?php echo $row->sonizlemtarihi;?></td>
   </tr>
 <?php 
    }?>
    </tbody>
    </table>
   


<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="phastalar" />
<input type="hidden" name="task" value="" />
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
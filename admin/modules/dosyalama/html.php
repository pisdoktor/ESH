<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );


class Dosyalama {
    
    static function getirHastalar($rows, $lists, $pageNav, $isim, $soyisim, $mahalle, $data) {
        $link = 'index.php?option=admin&bolum=dosyalama';
        
        if ($isim) {
            $link .= '&isim='.$isim;
        }
        
        if ($soyisim) {
            $link .= '&soyisim='.$soyisim;
        }
        
        if ($mahalle) {
        if (is_array($mahalle)) {
            foreach ($mahalle as $mah) {
                $link .= '&mahalle[]='.$mah;
            }
        } else {
            $link .= 'mahalle[]='.$mahalle;
        }
        }
    ?>
    <form action="index.php" method="GET" name="adminForm" role="form">
    <input type="hidden" name="option" value="admin" />
    <input type="hidden" name="bolum" value="dosyalama" />
    <input type="hidden" name="task" value="" />

    <div class="col-sm-3">
    <div class="panel panel-default"> <!-- sol panel başlangıç -->
    <div class="panel-heading">
    <h4><i class="fa-solid fa-magnifying-glass"></i> Mahalleler</h4>
    </div>
    <div class="panel-body">
    
    <div class="form-group row">
    <div class="col-sm-11">
    
    <div class="form-group">
    <label class="checkbox-inline" for="checkAll">
<input type="checkbox" name="" id="checkAll" value="" class=""  />
<strong>TÜMÜNÜ İŞARETLE</strong>
</label>
<script>
    $('#checkAll').click(function () {    
     $('input:checkbox').prop('checked', this.checked);    
 });
</script>  
 <?php
   foreach ($data as $k=>$v) {
   ?>
   <label class="checkbox-inline" for="<?php echo $k;?>">
<input type="checkbox" name="" id="<?php echo $k;?>" value=""  />
<strong><?php echo $k;?></strong>
</label>
<script>
    $('#<?php echo $k;?>').click(function () {
   <?php
   
   foreach ($v as $i=>$k) {
   ?>
   $('#mahalle<?php echo $i;?>').prop('checked', this.checked);    
   <?php
   }
   ?>
    });  
   </script>
   <?php
   }
 ?>
    </div>
 
    <?php echo $lists['mahalle'];?></div>
    </div>
    
    </div>
    
    
    </div><!-- sol panel bitiş -->
    
    </div>
    <div class="col-sm-9">
     
    <div class="panel panel-success"><!-- sağ panel başlangıç -->
    <div class="panel-heading">
    <div class="row">
        <div class="col-xs-7"><h4><i class="fa-regular fa-folder-open"></i> Seçilen Hastalar</h4></div>
        
        <div class="col-xs-4" align="right">
        
         <div class="input-group">      
      
    <div class="input-group-addon">İsim</div>
    <div class="input-group"><?php echo $lists['isim'];?></div>
    
    <div class="input-group-addon">Soyisim</div>
    <div class="input-group"><?php echo $lists['soyisim'];?></div>
   
    <div class="input-group-btn">
      <button class="btn btn-default" type="button" onclick="javascript:submitbutton('list');">
        <i class="glyphicon glyphicon-search"></i>
      </button>
    </div>
    
    </div> <!-- input-group -->
        
    </div> <!-- col-xs-->
        
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
    </div>
    
    <table class="table table-striped" id="datatablesSimple">
    <thead>
    <tr>
    <th>Sıra No</th>
    <th>#</th>
    <th>İsim</th>
    <th>Soyisim</th>
    <th>TC Kimlik No</th>
    <th>Kayıt Tarihi</th>
    <th>Mahalle</th>
    <th>Son İzlem Tarihi</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
    $i = 0;
     foreach ($rows as $row) {
         $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
         $row->sonizlemtarihi = $row->sonizlemtarihi ? tarihCevir($row->sonizlemtarihi, 1):'Yok';
     ?>
     <tr>
     <td><?php echo $pageNav->rowNumber( $i );?></td>
     <td><span data-toggle="tooltip" title="<?php echo $row->izlemsayisi;?> İzlem" class="label label-<?php echo $row->izlemsayisi ? 'info':'warning';?>"><a href="index.php?option=admin&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->izlemsayisi;?></a></span></td>
     <td><?php echo $row->isim;?></td>
     <td><?php echo $row->soyisim;?></td>
     <td><?php echo $row->tckimlik;?></td>
     <td><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td>
     <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
     <td><?php echo $row->sonizlemtarihi;?></td>
     <?php
     $i++;
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
<div class="pagenav_leafscounter">
<?php echo $pageNav->writeLeafsCounter();?>
</div>
</div>
</div>
    </div><!-- sağ panel bitiş -->
    
    </div>
    

    </form>
    <?php
    }


}

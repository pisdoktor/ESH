<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );


class Dosyalama {
    
    static function getirHastalar($rows, $lists, $pageNav, $isim, $soyisim) {
        $link = 'index.php?option=site&bolum=dosyalama';
        
        if ($isim) {
            $link .= '&isim='.$isim;
        }
        
        if ($soyisim) {
            $link .= '&soyisim='.$soyisim;
        }
    ?>
    <form action="index.php" method="GET" name="adminForm" role="form">
    <input type="hidden" name="option" value="site" />
    <input type="hidden" name="bolum" value="dosyalama" />
    <input type="hidden" name="task" value="" /> 
    <div class="panel panel-success">
    <div class="panel-heading">
    <div class="row">
        <div class="col-xs-11"><h4>Dosyalama İçin Sıralama</h4></div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
    </div>
    
    <div class="panel-body">
    
    <div class="form-group row">
    <div class="col-sm-2"><label for="isim">Hastanın Adı:</label></div>
    <div class="col-sm-1"><?php echo $lists['isim'];?></div>
    <div class="col-sm-2"><label for="soyisim">Hastanın Soyadı:</label></div>
    <div class="col-sm-1"><?php echo $lists['soyisim'];?></div>
    
    
    <div class="col-sm-3">
    <input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('list');" class="btn btn-primary"  />
    </div>
    </div>
    
    </div>
    <div class="form-group row">
    <div class="col-sm-11"><?php echo $lists['mahalle'];?></div>
    </div>
    <table class="table table-striped" id="datatablesSimple">
    <thead>
    <tr>
    <th>Sıra No</th>
    <th>İsim</th>
    <th>Soyisim</th>
    <th>Mahalle</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
    $i = 0;
     foreach ($rows as $row) {
     ?>
     <tr>
     <td><?php echo $pageNav->rowNumber( $i );?></td>
     <td><?php echo $row->isim;?></td>
     <td><?php echo $row->soyisim;?></td>
     <td><?php echo $row->mahalle;?></td>
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
</div>
</div>
    </div>
    </form>
    <?php
    }


}

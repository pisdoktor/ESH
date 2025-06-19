<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

$footer = new Version();  
?>
<div align="center" class="footer">

<div>
<small>
<?php echo $footer->getShortVersion();?>
</small>
</div>

<div>
<small>
<?php echo $footer->getCopy();?>
</small>
</div>


<small>
<a href="#" data-toggle="modal" data-target="#hakkinda">Hakkında</a>
</small>

<!-- Modal -->
<div id="hakkinda" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $footer->PRODUCT;?></h4>
      </div>
        <table class="table table-striped">
  <tr>
    <td><strong>Paket Sürümü:</strong></td>
    <td><?php echo $footer->RELEASE;?></td>
  </tr>
  <tr>
    <td><strong>Geliştirme Seviyesi:</strong></td>
    <td><?php echo $footer->DEV_LEVEL;?></td>
  </tr>
    <tr>
    <td><strong>Paket Durumu:</strong></td>
    <td><?php echo $footer->DEV_STATUS;?></td>
  </tr>
    <tr>
    <td><strong>Paket Kod Adı:</strong></td>
    <td><?php echo $footer->CODENAME;?></td>
  </tr>
    <tr>
    <td><strong>Kodlama Başlangıç Tarihi:</strong></td>
    <td><?php echo $footer->RELDATE;?></td>
  </tr>
    <tr>
    <td><strong>Kodlamacı:</strong></td>
    <td>Soner Ekici</td>
  </tr>
</table>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
      
    </div> <!-- modal content -->

  </div>
</div> <!-- modal -->
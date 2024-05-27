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

<div>
<a href="#" id="opener"><small>Hakkında</small></a>
</div>

</div>

<div style="display:none;" id="dialog" title="Hakkında">
<table width="100%" class="table-striped">
  <tr>
	<td width="50%"><strong>Paket Adı:</strong></td>
	<td><?php echo $footer->PRODUCT;?></td>
  </tr>
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
	<td><strong>Kodlama Tarihi:</strong></td>
	<td><?php echo $footer->RELDATE;?></td>
  </tr>
	<tr>
	<td><strong>Kodlamacı:</strong></td>
	<td>Soner Ekici</td>
  </tr>
</table>
</div>
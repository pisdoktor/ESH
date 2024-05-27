<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class IlceHTML {
	static function editIlce($row) {
		?>
		<div class="panel panel-default">
	<div class="panel-heading"><h4>Yönetim Paneli - İlçe <?php echo $row->id ? 'Düzenle' : 'Ekle';?></h4>
	</div>
	<div class="panel-body">

		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.ilce.value == ""){
				alert( "İlçe Adını boş bırakmışsınız" );
			}  else {
		submitform( pressbutton );
			}
		}
		//-->
		</script> 
<form action="index.php" method="post" name="adminForm" role="form">

<div class="row">
<div class="col-sm-3">
<label for="mahalle">İlçe Adı:</label></div>
<div class="col-sm-9"><input type="text" id="ilce" name="ilce" class="form-control text-uppercase" value="<?php echo $row->ilce;?>" required></div>
</div>

<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="ilce" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $row->id;?>" />
</form>
<br />

<div>
<input type="button" name="button" value="Kaydet" onclick="javascript:submitbutton('save');" class="btn btn-primary"  />
<input type="button" name="button" value="İptal" onclick="javascript:submitbutton('cancel');" class="btn btn-warning" />
</div>

</div>
</div>
<?php
}
	
	static function getIlceList($rows, $pageNav) {
		?>
        <form action="index.php" method="post" name="adminForm" role="form">
		<div class="panel panel-default">
	<div class="panel-heading"><h4>Yönetim Paneli - İlçeler</h4></div>
	<div class="panel-body">
    
<div class="form-group">
<div class="btn-group">
<input type="button" name="button" value="Yeni İlçe Ekle" onclick="javascript:submitbutton('add');" class="btn btn-primary" />
<input type="button" name="button" value="Seçileni Düzenle" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else {submitbutton('edit');}" class="btn btn-default" /> 
<input type="button" name="button" value="Seçileni Sil" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else if (confirm('Bu mahalleleri silmek istediğinize emin misiniz?')){ submitbutton('delete');}" class="btn btn-warning" /> 
</div>
</div>

</div>

<table class="table table-striped">
<thead>
<tr>
<th>SIRA</th>
<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
<th>İlçe ID</th>
<th>İlçenin Adı</th>
</tr>
</thead>
<tbody>
<?php
$t = 0;
for($i=0; $i<count($rows);$i++) {
$row = $rows[$i];

$checked = mosHTML::idBox( $i, $row->id );
?>
<tr>
<td><?php echo $pageNav->rowNumber( $i ); ?></td>
<td><?php echo $checked;?></td>
<td><?php echo $row->id;?></td>
<td><a href="index.php?option=admin&bolum=ilce&task=editx&id=<?php echo $row->id;?>"><?php echo $row->ilce;?></a></td>
</tr>
<?php
$t = 1 - $t;
}
?>
</tbody>
</table>

<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="ilce" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />

  
</form>

<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
$link = 'index.php?option=admin&bolum=ilce';
echo $pageNav->writePagesLinks($link);?>
</div>
</div>
</div>

</div>  
<?php		
	}
}

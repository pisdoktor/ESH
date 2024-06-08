<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class HastalikHTML {
	static function editHastalik($row, $kategoriler) {
		?>
		<div class="panel panel-default">
	<div class="panel-heading"><h4><i class="fa-solid fa-bacterium"></i> Yönetim Paneli - Hastalık <?php echo $row->id ? 'Düzenle' : 'Ekle';?></h4>
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
			if (form.hastalikadi.value == ""){
				alert( "Hastalık Adını boş bırakmışsınız" );
			}  else if (form.cat.value == "") {
                alert("Kategori seçimi boş bırakılamaz");
            } else  {
		submitform( pressbutton );
			}
		}
		//-->
		</script> 
<form action="index.php" method="post" name="adminForm" role="form">

<div class="row">
<div class="col-sm-3">
<label for="mahalle">Kategori Adı:</label></div>
<div class="col-sm-9"><?php echo $kategoriler;?></div>
</div>

<div class="row">
<div class="col-sm-3">
<label for="mahalle">Hastalık Adı:</label></div>
<div class="col-sm-9"><input type="text" id="hastalikadi" name="hastalikadi" class="form-control" value="<?php echo $row->hastalikadi;?>" required></div>
</div>

<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="hastaliklar" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $row->id;?>" />
</form>
<br />

<div>
<input type="button" name="button" value="Kaydet" onclick="javascript:submitbutton('save');" class="btn btn-primary"  />
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>  
</div>

</div>
</div>
<?php
}
	
	static function getHastalikList($rows, $search, $cat, $list, $pageNav) {
        $link = 'index.php?option=admin&bolum=hastaliklar';
        if ($search) {
            $link .= "&amp;search=".$search;
        }
        if ($cat) {
            $link .= "&amp;cat=".$cat;
        }
        if ($ordering) {
            $link .= "&amp;ordering=".$ordering;
        }
		?>
        <form action="index.php" method="GET" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="hastaliklar" />
<input type="hidden" name="task" value="list" />
<input type="hidden" name="boxchecked" value="0" />

		<div class="panel panel-default">
	<div class="panel-heading"><h4><i class="fa-solid fa-bacterium"></i> Yönetim Paneli - Hastalıklar</h4></div>
	<div class="panel-body">
	
<div class="form-group">

<div class="col-sm-4">
<div class="btn-group">
<input type="button" name="button" value="Yeni Hastalık Ekle" onclick="javascript:submitbutton('add');" class="btn btn-primary" />
<input type="button" name="button" value="Seçileni Düzenle" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else {submitbutton('edit');}" class="btn btn-default" /> 
<input type="button" name="button" value="Seçileni Sil" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else if (confirm('Bu mahalleleri silmek istediğinize emin misiniz?')){ submitbutton('delete');}" class="btn btn-warning" /> 
</div>
</div>

<div class="col-sm-4">
 <div class="input-group">
    <input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control"  onChange="document.adminForm.submit();" placeholder="Hastalık Adı Yazın">
    <div class="input-group-btn">
      <button class="btn btn-default" type="submit">
        <i class="glyphicon glyphicon-search"></i>
      </button>
 </div>
 </div>
</div>

  <div class="col-sm-3">    
    <?php echo $list['cat'];?>
 </div>
 
  <div class="col-sm-1">    
    <?php echo $pageNav->getLimitBox($link);?>
 </div>

</div> <!-- form group -->

</div> <!-- panel body -->

<table class="table table-striped">
<thead>
<tr>
<th>SIRA</th>
<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
<th>Hastalık ID
 <span><a href="<?php echo $link;?>&ordering=m.id-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.id-ASC">▼</a></span>
</th>
<th>Kategori Adı
 <span><a href="<?php echo $link;?>&ordering=ic.id-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=ic.id-ASC">▼</a></span>
</th>
<th>Hastalık Adı
 <span><a href="<?php echo $link;?>&ordering=m.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.mahalle-ASC">▼</a></span>
</th>
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
<td><?php echo $row->kategoriadi;?></td>
<td><a href="index.php?option=admin&bolum=hastaliklar&task=editx&id=<?php echo $row->id;?>"><?php echo $row->hastalikadi;?></a></td>
</tr>
<?php
$t = 1 - $t;
}
?>
</tbody>
</table>
  
</form>

<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);?>
</div>
</div>
</div>

</div>  
<?php
	}
}

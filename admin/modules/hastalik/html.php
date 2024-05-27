<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class HastalikHTML {
    static function editHastalik($row, $list) {
        ?>
        <div class="panel panel-default">
    <div class="panel-heading"><h4>Yönetim Paneli - Hastalık <?php echo $row->id ? 'Düzenle' : 'Ekle';?></h4>
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
            }  else {
        submitform( pressbutton );
            }
        }
        //-->
        </script> 
<form action="index.php" method="post" name="adminForm" role="form">

<div class="row">
<div class="col-sm-3">
<label for="hastalikadi">Ana Dizini:</label></div>
<div class="col-sm-9"><?php echo $list;?></div>
</div>

<div class="row">
<div class="col-sm-3">
<label for="hastalikadi">Hastalık Adı:</label></div>
<div class="col-sm-9"><input type="text" id="hastalikadi" name="hastalikadi" class="form-control" value="<?php echo $row->hastalik;?>" required></div>
</div>

<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="hastalik" />
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
    
    static function getHastalikList($rows, $pageNav) {
        ?>
        <div class="panel panel-default">
    <div class="panel-heading"><h4>Yönetim Paneli - Hastalıklıar</h4></div>
    <div class="panel-body">
    
<form action="index.php" method="post" name="adminForm" role="form">

<div class="form-group">
<div class="btn-group">
<input type="button" name="button" value="Yeni Hastalık Ekle" onclick="javascript:submitbutton('add');" class="btn btn-primary" />
<input type="button" name="button" value="Seçileni Düzenle" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else {submitbutton('edit');}" class="btn btn-default" /> 
<input type="button" name="button" value="Seçileni Sil" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else if (confirm('Bu mahalleleri silmek istediğinize emin misiniz?')){ submitbutton('delete');}" class="btn btn-warning" /> 
</div>
</div>

<div class="row">
<div class="col-sm-1">
<strong>SIRA</strong>
</div>
<div class="col-sm-1">
<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
</div>
<div class="col-sm-3">
<strong>Hastalık ID</strong>
</div>
<div class="col-sm-7">
<strong>Hastalık Adı</strong> 
</div>

</div>

<?php
$t = 0;
for($i=0; $i<count($rows);$i++) {
$row = $rows[$i];

$checked = mosHTML::idBox( $i, $row->id );
?>

<div class="row" id="detail<?php echo $row->id;?>">

<div class="col-sm-1">
<?php echo $pageNav->rowNumber( $i ); ?>
</div>
<div class="col-sm-1">
<?php echo $checked;?>
</div>
<div class="col-sm-3">
<?php echo $row->id;?>
</div>
<div class="col-sm-7">
<a href="index.php?option=admin&bolum=hastalik&task=editx&id=<?php echo $row->id;?>"><?php echo $row->hastalikadi;?></a>
</div>
</div>

<?php
$t = 1 - $t;
}
?>
<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="hastalik" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<br />
<div class="btn-group">
<input type="button" name="button" value="Yeni Hastalık Ekle" onclick="javascript:submitbutton('add');" class="btn btn-primary" />
<input type="button" name="button" value="Seçileni Düzenle" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else {submitbutton('edit');}" class="btn btn-default" /> 
<input type="button" name="button" value="Seçileni Sil" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else if (confirm('Bu mahalleleri silmek istediğinize emin misiniz?')){ submitbutton('delete');}" class="btn btn-warning" /> 
</div>
</form>

<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
$link = 'index.php?option=admin&bolum=hastalik';
echo $pageNav->writePagesLinks($link);?>
</div>
</div>


</div>
</div>

<?php
        
    }
}

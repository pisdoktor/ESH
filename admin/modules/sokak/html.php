<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class SokakHTML {
	static function editSokak($row, $ilceler, $mahalleler) {
		?>
		<div class="panel panel-default">
	<div class="panel-heading"><h4>Yönetim Paneli - Sokak <?php echo $row->id ? 'Düzenle' : 'Ekle';?></h4>
	</div>
	<div class="panel-body">
<form action="index.php" method="post" name="adminForm" role="form">

<div class="form-group row">
<div class="col-sm-3"><label for="ilce">İlçe:</label></div>
<div class="col-sm-4"><?php echo $ilceler;?></div>
</div>

<div class="form-group row"> 
<div class="col-sm-3"><label for="mahalle">Mahalle:</label></div>
<div class="col-sm-4"><?php echo $mahalleler;?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="mahalle">Sokak Adı:</label></div>
<div class="col-sm-4"><input type="text" id="sokakadi" name="sokakadi" class="form-control" value="<?php echo $row->sokakadi;?>" required>
</div>
</div>

<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="sokak" />
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

        <script>                                  
$(document).ready(function(){
    
        $("#ilce").on("change", function(){

            $("#mahalle").attr("disabled", false).html("<option value=''>Bir Mahalle Seçin</option>");
            console.log($(this).val()); 
            
            ajaxFunc("mahalle", $(this).val(), "#mahalle");

        });
        
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=admin&bolum=sokak",
                type: "POST",
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
<?php
}
	
	static function getSokakList($rows, $search, $ilce, $mahalle, $il, $mah, $ordering, $pageNav) {
        $link = 'index.php?option=admin&bolum=sokak';
        if ($search) {
            $link .= "&amp;search=".$search;
        }
        if ($ilce) {
            $link .= "&amp;ilce=".$ilce;
        }
        if ($mahalle) {
            $link .= "&amp;mahalle=".$mahalle;
        }
        if ($ordering) {
            $link .= "&amp;ordering=".$ordering;
        }
		?>
        <form action="index.php" method="GET" name="adminForm" role="form">
		<div class="panel panel-default">
	<div class="panel-heading"><h4><i class="fa-solid fa-road"></i> Yönetim Paneli - Sokaklar</h4></div>
	<div class="panel-body">
    
<div class="form-group">

<div class="col-sm-4">
<div class="btn-group">
<input type="button" name="button" value="Yeni Sokak Ekle" onclick="javascript:submitbutton('add');" class="btn btn-primary" />
<input type="button" name="button" value="Seçileni Düzenle" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else {submitbutton('edit');}" class="btn btn-default" /> 
<input type="button" name="button" value="Seçileni Sil" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else if (confirm('Bu sokakları silmek istediğinize emin misiniz?')){ submitbutton('delete');}" class="btn btn-warning" /> 
</div>
</div>

<div class="col-sm-3">
 <div class="input-group">
    <input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control"  onChange="document.adminForm.submit();" placeholder="Sokak Adı Yazın">
    <div class="input-group-btn">
      <button class="btn btn-default" type="submit">
        <i class="glyphicon glyphicon-search"></i>
      </button>
 </div>
 </div>
</div>

  <div class="col-sm-2">    
    <?php echo $il->getIlce($link, $ilce);?>
 </div>
 
   <div class="col-sm-2">    
    <?php echo $mah->getMahalle($link, $mahalle);?>
 </div>
 
  <div class="col-sm-1">    
    <?php echo $pageNav->getLimitBox($link);?>
 </div>
 
</div>  <!-- form group -->

</div>

<table class="table table-striped">
<thead>
<tr>
<th>SIRA</th>
<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
<th>Sokak ID
 <span><a href="<?php echo $link;?>&ordering=s.id-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=s.id-ASC">▼</a></span>
</th>
<th>Sokak Adı
 <span><a href="<?php echo $link;?>&ordering=s.sokakadi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=s.sokakadi-ASC">▼</a></span>
</th>
<th>İlçe Adı
 <span><a href="<?php echo $link;?>&ordering=ic.ilce-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=ic.ilce-ASC">▼</a></span>
</th> 
<th>Mahalle Adı
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
<td><a href="index.php?option=admin&bolum=sokak&task=editx&id=<?php echo $row->id;?>"><?php echo $row->sokakadi;?></a></td>
<td><?php echo $row->ilceadi;?></td>
<td><?php echo $row->mahalleadi;?></td>

</tr>
<?php
$t = 1 - $t;
}
?>
</tbody>
</table>

<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="sokak" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
 
</form>
<script>                                  
$(document).ready(function(){
    
    if ($("#ilce").val() > 0) {
        
        $("#mahalle").find('option').remove();
        
        $("#mahalle").append("<option value=''>Bir Mahalle Seçin</option>");
    
        ajaxFunc("mahalle", "<?php echo $ilce;?>", "#mahalle");
    
    }
        
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=admin&bolum=sokak",
                type: "POST",
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

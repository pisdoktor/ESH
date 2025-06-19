<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class KapinoHTML {
	static function editKapino($row, $lists) {
		?>
		<div class="panel panel-default">
	<div class="panel-heading"><h4>Yönetim Paneli - Kapı No <?php echo $row->id ? 'Düzenle' : 'Ekle';?></h4>
	</div>
	<div class="panel-body">
<form action="index.php" method="post" name="adminForm" role="form">

<div class="form-group row">
<div class="col-sm-3">
<label for="ilce">İlçe:</label></div>
<div class="col-sm-9"><?php echo $lists['ilce'];?></div>
</div>
<div class="form-group row">
<div class="col-sm-3">
<label for="mahalle">Mahalle:</label></div>
<div class="col-sm-9"><?php echo $lists['mahalle'];?></div>
</div>
<div class="form-group row">
<div class="col-sm-3">
<label for="sokak">Cadde/Sokak Adı:</label></div>
<div class="col-sm-9"><?php echo $lists['sokak'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-3">
<label for="kapino">Kapı Numarası:</label></div>
<div class="col-sm-9"><input type="text" id="kapino" name="kapino" class="form-control" value="<?php echo $row->kapino;?>" required></div>
</div>

<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="kapino" />
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

            $("#mahalle").html("<option value=''>Bir Mahalle Seçin</option>");
            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>"); 
            console.log($(this).val()); 
            
            ajaxFunc("mahalle", $(this).val(), "#mahalle");

        });

        $("#mahalle").on("change", function(){

            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("sokak", $(this).val(), "#sokak");

        });
        
        
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=admin&bolum=kapino",
                type: "POST",
                data: {task:task, id:id},
                success: function(sonuc){
                    console.log(sonuc);
                    $(name).html(sonuc);
                }});
        }
    });
</script> 
<?php
}
	
static function getKapinoList($rows, $search, $ilce, $mahalle, $sokak, $ordering, $lists, $pageNav) {
        $link = 'index.php?option=admin&bolum=kapino';
        if ($search) {
            $link .= "&amp;search=".$search;
        }
        if ($ilce) {
            $link .= "&amp;ilce=".$ilce;
        }
        if ($mahalle) {
            $link .= "&amp;mahalle=".$mahalle;
        }
        if ($sokak) {
            $link .= "&amp;sokak=".$sokak;
        }
        if ($ordering) {
            $link .= "&amp;ordering=".$ordering;
        }
		?>
        
<form action="index.php" method="GET" name="adminForm" role="form">  
<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="kapino" />
<input type="hidden" name="task" value="list" />


<script>                                  
$(document).ready(function(){
    
        $("#ilce").on("change", function(){

            $("#mahalle").html("<option value=''>Bir Mahalle Seçin</option>");
            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>"); 
            console.log($(this).val()); 
            
            ajaxFunc("mahalle", $(this).val(), "#mahalle");

        });

        $("#mahalle").on("change", function(){

            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("sokak", $(this).val(), "#sokak");

        });
        
     
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=admin&bolum=kapino",
                type: "POST",
                data: {task:task, id:id},
                success: function(sonuc){
                    console.log(sonuc);
                    $(name).html(sonuc);
                }});
        }
    });
</script> 

<div class="panel panel-default">
<div class="panel-heading"><h4><i class="fa-solid fa-door-open"></i> Yönetim Paneli - Kapı Numaraları</h4></div>
<div class="panel-body">
	
<div class="form-group">
<div class="col-sm-1"> 
<a href="index.php?option=admin&bolum=kapino&task=add" class="btn btn-default">Yeni Ekle</a>
</div>

<div class="col-sm-10">
<div class="col-sm-4"><input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control" placeholder="Aramak istediğiniz kapı numarasını yazın"></div>
<div class="col-sm-2"><?php echo $lists['ilce'];?></div>
<div class="col-sm-2"><?php echo $lists['mahalle'];?></div>
<div class="col-sm-2"><?php echo $lists['sokak'];?></div>
<div class="col-sm-2"><input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('list');" class="btn btn-primary"  /></div>  
</div>
 
<div class="col-sm-1">    
<?php echo $pageNav->getLimitBox($link);?>
</div>
</div>

</div>

<table class="table table-striped">
<thead>
<tr>
<th>Kapı Numarası
<span><a href="<?php echo $link;?>&ordering=k.kapino-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=k.kapino-ASC">▼</a></span>
  </th>
  <th>Sokak Adı
 <span><a href="<?php echo $link;?>&ordering=s.sokakadi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=s.sokakadi-ASC">▼</a></span>
</th>
<th>Mahalle Adı
 <span><a href="<?php echo $link;?>&ordering=m.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.mahalle-ASC">▼</a></span>
</th>
<th>İlçe Adı
 <span><a href="<?php echo $link;?>&ordering=ic.ilce-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=ic.ilce-ASC">▼</a></span>
</th> 
</tr>
</thead>

<tbody>
<?php
foreach($rows as $row) {
?>
<tr>
<th>
<div class="dropdown">
<a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->kapino;?></a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=admin&bolum=kapino&task=editx&id=<?php echo $row->id;?>">Düzenle</a></li>
    <li><a href="index.php?option=admin&bolum=kapino&task=delete&id=<?php echo $row->id;?>">Sil</a></li>
  </ul>
</div> 
</th>
<td><?php echo $row->sokakadi;?></td> 
<td><?php echo $row->mahalleadi;?></td>
<td><?php echo $row->ilceadi;?></td>  
</tr>
<?php
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
</div> <!-- panel footer -->



<?php
		
	}
}

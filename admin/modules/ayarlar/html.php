<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class ConfigHTML {
	
	static function ConfigFile($data) {
		?>
        <form action="index.php" method="post" name="adminForm" role="form"> 
		<div class="panel panel-default">
	<div class="panel-heading"><h4><i class="fa-solid fa-gears"></i> Yönetim Paneli - Yapılandırma Dosyası</h4></div>
	<div class="panel-body">
		
		
		<div class="row">
		
		<div class="col-sm-2">
		<label for="data">Dosya İçeriği:</label>
		</div>
		<div class="col-sm-10">
		<textarea id="data" name="data" cols="80" rows="20" class="form-control"><?php echo $data;?></textarea>
		</div>
		
		</div>
		  
<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="ayarlar" />
<input type="hidden" name="task" value="" />

		
		<div class="form-group">
		<div class="btn-group">
<input type="button" name="button" value="Kaydet" onclick="javascript:submitbutton('save');" class="btn btn-primary"  />
<input type="button" name="button" value="İptal" onclick="javascript:submitbutton('cancel');" class="btn btn-warning" />
</div>
</div>

</div>
</div>

        </form> 
		<?php
		
	}

}

<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Profile {
	static function editImage($photo, $width, $height, $type, $minWidth, $minHeight) {
		
		if ($width < $minWidth) {
			$minWidth = $width;
		}
		if ($height < $minHeight) {
			$minHeight = $height;
		}
		
		?>
<script type="text/javascript">
  $(function(){
	  
	$('#target').Jcrop({
		boxWidth: 960,
		boxHeight: 450,
		setSelect: [ <?php echo $minWidth;?>, <?php echo $minHeight;?>, 0, 0 ],
		trueSize: [<?php echo $width;?>, <?php echo $height;?>],
	  onSelect: updateCoords,
	  onChange: updateCoords
	});

  });
  
  function checkCoords(){
	if (parseInt(jQuery('#w').val())>0) return true;
	alert('Lütfen bir alan seçin.');
	return false;
  };
  
  function updateCoords(c) {
	$('#x').val(c.x);
	$('#y').val(c.y);
	$('#w').val(c.w);
	$('#h').val(c.h);
  };
</script>
<div class="text-info"><h4>Resmin içerisindeki kutucuğu uygun şekilde sürükleyip "Resmi Kes ve Kaydet" butonuna basınız! Kutucuk içerisinde kalan alan profil resminiz olarak kullanılacaktır!</h4></div>
<div id="real-image" align="center">
<img id="target" src="<?php echo $photo['withaddr'];?>" alt="Düzenlenecek Profil Resmi">
</div>


<form action="index.php?option=site&bolum=profil&task=cropsave" method="post" onsubmit="return checkCoords();">
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />
	<input type="hidden" name="type" value="<?php echo $type;?>" />
<br />
	<input type="submit" value="Resmi Kes ve Kaydet" class="btn btn-primary" />
</form>
		<?php
	}
	
	static function editProfile($row) {
		?>
<form action="index.php?option=site&bolum=profil&task=save" method="post" id="adminForm" role="form">
        <div class="panel panel-default">
		<div class="panel-heading"><h4>Profil Düzenle</h4></div>
        
		<div class="panel-body">
        
        <div class="form-group row">
        <div class="col-sm-3"><label for="name">Adınız ve Soyadınız:</label></div>
        <div class="col-sm-4"><input type="text" id="name" name="name" class="form-control" value="<?php echo $row->name;?>" required></div>
        </div>
		
        <div class="form-group row">
        <div class="col-sm-3"><label for="username">Kullanıcı Adınız:</label></div>
        <div class="col-sm-4"><input type="text" id="username" name="username" class="form-control" value="<?php echo $row->username;?>" required></div>
        </div>
        
        <div class="form-group row">
        <div class="col-sm-3"><label for="tckimlikno">TC Kimlik Numaranız:</label></div>
        <div class="col-sm-4"><input type="text" id="tckimlikno" name="tckimlikno" class="form-control" value="<?php echo $row->tckimlikno;?>" required></div>
        </div>
        
        <div class="form-group row">
        <div class="col-sm-3">
        <button type="submit" class="btn btn-primary" />Profili Güncelle</button>
        </div>

</div>

</div>
</div>
</form>
		<?php
	}
	static function getProfile($row) {
	    $edit = 1;
		$image = $row->image ? SITEURL.'/images/profil/'.$row->image : SITEURL.'/images/profil/noimage.png';
		$editlink = $edit ? '<a class="btn btn-primary" href="index.php?option=site&bolum=profil&task=edit">Profili Düzenle</a>' : '';
		$passlink = $edit ? '<a class="btn btn-default" href="#" id="changepass">Parola Değiştir</a>' : '';
		$editimage = $edit ? '<a class="btn btn-warning" href="#" id="changeimg">Resim Ekle</a>' : '';
		$deleteimage = ($edit && $row->image) ? '<a class="btn btn-success" href="index.php?option=site&bolum=profil&task=deleteimage">Resmi Sil</a>' : ''; 
		$cropimage = ($edit && $row->image) ? '<a class="btn btn-default" href="index.php?option=site&bolum=profil&task=editimage">Resmi Düzenle</a>' : ''; 
	
		
		$head = $edit ? 'Profilim' : 'Profil : '.$row->name;
		?>
		<div class="panel panel-default">
        
		<div class="panel-heading"><h4><?php echo $head;?></h4></div>
        
		<div class="panel-body"> 
		
		<div class="row">
		<div class="col-sm-3">
        
		<div class="figure">
		<img src="<?php echo $image;?>" class="img-thumbnail" title="<?php echo $row->name;?>" alt="<?php echo $row->name;?>" width="200" height="200" />
		</div>
		        
        </div>

		<div class="col-sm-9">
        
		<div class="form-group">
		<div class="row">
		<div class="col-sm-4"><strong>Adı, Soyadı:</strong></div>
		<div class="col-sm-8"><?php echo $row->name;?></div>
		</div>
		</div>
		
		<div class="form-group">
		<div class="row">
		<div class="col-sm-4"><strong>Kullanıcı Adı:</strong></div>
		<div class="col-sm-8"><?php echo $row->username;?></div>
		</div>
		</div>
        
        <div class="form-group">
        <div class="row">
        <div class="col-sm-4"><strong>TC Kimlik Numarası:</strong></div>
        <div class="col-sm-8"><?php echo $row->tckimlikno;?></div>
        </div>
        </div>
		
		<div class="form-group">
		<div class="row">
		<div class="col-sm-4"><strong>Siteye Kayıt Tarihi:</strong></div>
		<div class="col-sm-8"><?php echo FormatDate($row->registerDate, '%d-%m-%Y %H:%M:%S');?></div>
		</div>
		</div>
		

		<div class="form-group">
		<div class="row">
		<div class="col-sm-4"><strong>Siteye Son Geliş Tarihi:</strong></div>
		<div class="col-sm-8"><?php echo FormatDate($row->lastvisit, '%d-%m-%Y %H:%M:%S');?></div>
		</div>
		</div>
        
        <div class="form-group">
        <div class="row">
        <div class="col-sm-12">
        <?php echo $editimage;?> <?php echo $cropimage;?> <?php echo $deleteimage;?> <?php echo $editlink;?> <?php echo $passlink;?></div>
        </div>
        </div>
        </div>
		
				
		</div>
		
		</div>
		</div>
		</div>
		
		
		<!-- Profil Resmi Değiştirme -->
		<div id="imagechange" style="display: none;" title="Profil Resmi Değiştir"> 
		<div class="text-info">* Resminizin uzantısı jpg, jpeg, gif, png olmak zorundadır.</div>
	   <div class="text-warning">* Resminizin boyutu 2 Mb geçemez!</div>
		<form action="index.php?option=site&bolum=profil&task=saveimage" method="post" enctype="multipart/form-data" role="form">
		<input type="file" name="image" id="image" class="btn btn-default" />
		<br />       
		<button type="submit" class="btn btn-primary">Profil Resmi Yap</button>
		</form>
		</div>
		<!-- Profil Resmi Değiştirme -->
		
		<!-- Parola Değiştirme -->
		<div id="passchange" style="display: none;" title="Parola Değiştir">
		<form action="index.php?option=site&bolum=profil&task=changepass" method="post" role="form">
		<label for="password">Yeni Parola:</label>
		<input type="password" name="password" id="password" class="form-control" required />
		<br />
		<label for="password2">Yeni Parola Tekrar:</label>
		<input type="password" name="password2" id="password2" class="form-control" required />
		<br />
		<button type="submit" class="btn btn-primary">Parolayı Değiştir</button>
		</form>
		</div>
		<!-- Parola Değiştirme -->
		<?php
	}
}

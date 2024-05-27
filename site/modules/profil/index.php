<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

$id = intval(getParam($_REQUEST, 'id')); 

include(dirname(__FILE__). '/html.php');

switch($task) {
	default:
	case 'my':
    case 'show':
	getProfile();
	break;
	
	case 'edit':
	editProfile();
	break;
	
	case 'save':
	saveProfile();
	break;
	
	case 'cancel':
	cancelProfile();
	break;
	
	case 'delete':
	delProfile();
	break;
	
	case 'changepass':
	changePass();
	break;
	
	case 'savepass':
	savePass();
	break;
	
	case 'saveimage':
	saveImage();
	break;
	
	case 'deleteimage':
	deleteImage();
	break;
	
	case 'editimage':
	editImage();
	break;
	
	case 'cropsave':
	CropandSave();
	break;
}

function CropandSave() {
	global $dbase, $my;

	$query = "SELECT image FROM #__users WHERE id=".$dbase->Quote($my->id);
	$dbase->setQuery($query);
	$image = $dbase->loadResult();
	
	if (!$image) {
		return;
	}
	
	$realimage = ABSPATH.'/images/profil/'.$image;
	
	if (!file_exists($realimage)) {
		echo 'Resim yerinde yok';
		return;
	}
	
	$type = getParam($_POST, 'type');
	
	$x = getParam($_POST, 'x');
	$y = getParam($_POST, 'y');
	$w = getParam($_POST, 'w');
	$h =getParam($_POST, 'h');
	
	$minWidth = 200;
	$minHeight = 200;
	
	switch($type) {
		case 'jpg':
		case 'jpeg':
		$img_r = imagecreatefromjpeg($realimage);
		break;
		
		case 'png':
		$img_r = imagecreatefrompng($realimage);
		break;
		
		case 'gif':
		$img_r = imagecreatefromgif($realimage);
		break;
	}
	
	$dst_r = ImageCreateTrueColor( $minWidth, $minHeight );
	
	imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $minWidth, $minHeight, $w, $h);
	
	switch($type) {
		case 'jpg':
		case 'jpeg':
		imagejpeg($dst_r,$realimage,100);
		break;
		
		case 'png':
		imagepng($dst_r,$realimage,100);
		break;
		
		case 'gif':
		imagegif($dst_r,$realimage,100);
		break;
	}
	
	Redirect('index.php?option=site&bolum=profil&task=my');
}

/**
* Profil resmi düzenleme
*/
function editImage() {
	global $dbase, $my;
	
	$query = "SELECT image FROM #__users WHERE id=".$dbase->Quote($my->id);
	$dbase->setQuery($query);
	$image = $dbase->loadResult();
	
	if (!$image) {
		return;
	} 
	
	$photo = array();
	$photo['name'] = $image;
	$photo['withpath'] = ABSPATH.'/images/profil/'.$image;
	$photo['withaddr'] = SITEURL.'/images/profil/'.$image;
	
	
	list($width, $height) = getimagesize($photo['withpath']);
	
	$type = pathinfo($photo['withpath']);
	$type = strtolower($type["extension"]);
	
	$minWidth = 200;
	$minHeight = 200;
	
	Profile::editImage($photo, $width, $height, $type, $minWidth, $minHeight);	
}
/**
* Profil resmi silme fonksiyonu
*/
function deleteImage() {
	global $dbase, $my;
	
	$query = "SELECT image FROM #__users WHERE id=".$dbase->Quote($my->id);
	$dbase->setQuery($query);
	
	$image = $dbase->loadResult();
	
	if ($image && @unlink(ABSPATH.'/images/profil/'.$image)) {
			$dbase->setQuery("UPDATE #__users SET image='' WHERE id=".$dbase->Quote($my->id));
			$dbase->query();
	} else {
		ErrorAlert('Resim yok');
	}
	
	Redirect('index.php?option=site&bolum=profil&task=my');
}
/**
* Profil parolası değiştirme fonksiyonu
*/
function changePass() {
	global $dbase, $my;
	
	$password = getParam($_POST, 'password');
	$password2 = getParam($_POST, 'password2');
	
	if ($password == $password2) {
		
		$row = new Users($dbase);
		$salt = MakePassword(16);
		$crypt = md5($password.$salt);
		
		$password = $crypt.':'.$salt;
		
		$query = "UPDATE #__users SET password=".$dbase->Quote($password)." WHERE id=".$dbase->Quote($my->id);
		$dbase->setQuery($query);
		$dbase->query();
			
		Redirect('index.php?option=site&bolum=profil&task=my', 'Parolanız değiştirildi');
		
	} else {
		Redirect('index.php?option=site&bolum=profil&task=my', 'Parolalar uyuşmuyor!');
	}
}
/**
* Profil resmi ekleme/değiştirme fonksiyonu
*/
function saveImage() {
	global $dbase, $my;
	
	$image = getParam($_FILES, 'image');
	
	if (!$image['name']) {
		Redirect('index.php?option=site&bolum=profil&task=my', 'Resim seçmemişsiniz');
	}
	
	$row = new Users($dbase);
	$row->load($my->id);
	
	//eğer varsa önce eski resmi silelim
	if ($row->image) {
		@unlink(ABSPATH.'/images/profil/'.$row->image);
	}
	
	//şimdi yeni resmi yükleyelim
	$dest = ABSPATH.'/images/profil/';
	$maxsize = '2048';
	$allow = array('png', 'gif', 'jpg', 'jpeg');
	$minWidth = 200;
	$minHeight = 200;
		
	$uzanti = pathinfo($image['name']);
	$uzanti = strtolower($uzanti["extension"]);
	
	$error = '';
		
	if (!in_array($uzanti, $allow)) {
		$error = addslashes( $image['name'].' için dosya türü uygun değil');
	}
		
	if ($image['size'] > $maxsize*1024) {
		$error = addslashes($image['name'].' için dosya boyutu istenilenden büyük!');
	}
	
	$word = str_replace(' ', '_', $row->username);
	$word = trim(strtolower($word));
						
	$imagename = $row->id.$word.$row->createCode(6).'.'.$uzanti;
	$targetfile= $dest.$imagename;
		
	if (!move_uploaded_file($image['tmp_name'], $targetfile)) {
		$error = addslashes( $image['name'].' yüklenemedi!');
	}
	
	$query = "UPDATE #__users SET image=".$dbase->Quote($imagename)
	. "\n WHERE id=".$dbase->Quote($row->id);
	$dbase->setQuery($query);
	$dbase->query();
	
	list($imgwidth, $imgheight) = getimagesize($targetfile); 
	
	if ($error) {
	Redirect('index.php?option=site&bolum=profil&task=my', $error);
	} else {
		if ($imgwidth > $minWidth || $imgheight > $minHeight) {
			Redirect('index.php?option=site&bolum=profil&task=editimage');
		} else {
			Redirect('index.php?option=site&bolum=profil&task=my');        
		}
	}
}
/**
* Profil güncellemesi için açılacak sayfa
*/
function editProfile() {
	global $dbase, $my;
	
	$row = new Users($dbase);
	$row->load($my->id);
	
	Profile::editProfile($row);
	
}
/**
* Profil güncelleme fonksiyonu
*/
function saveProfile() {
	global $dbase, $my;
	
	$row = new Users( $dbase );
	
	
	if ( !$row->bind( $_POST ) ) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	$row->id = $my->id;
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	Redirect('index.php?option=site&bolum=profil&task=my', 'Değişiklikler başarıyla kaydedildi');
}
/**
* Profil gösterimi
* @param mixed $id gösterim yapılacak kullanıcının id si
* Mesaj ve arkadaşlık isteği gönderimi konusunda daha iyi bir kodlama yapılmalı!
*/
function getProfile() {
	global $dbase, $my;
	
	$query = "SELECT u.* FROM #__users AS u"
    . "\n WHERE u.id=".$dbase->Quote($my->id);
    $dbase->setQuery($query);
    
    $dbase->loadObject($row);    
	
    Profile::getProfile($row);
}

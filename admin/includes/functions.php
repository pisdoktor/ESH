<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );  

function adminMenu() {
	?>
<div id="cssmenu">
<ul>
<li><a href="<?php echo SITEURL;?>"><span>Anasayfa</span></a></li>

<li class="has-sub"><a href="#"><span>Yönetim</span></a>
<ul>
<li><a href="index.php?option=admin&bolum=user"><span>Kullanıcı Yönetimi</span></a></li>
<li class="has-sub"><a href="#">Adres Yönetimi</a>
<ul>
<li><a href="index.php?option=admin&bolum=ilce"><span>İlçe Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=mahalle"><span>Mahalle Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=sokak"><span>Sokak Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=kapino"><span>Kapı No Yönetimi</span></a></li>
</ul>
</li>
<li><a href="index.php?option=admin&bolum=islem"><span>İşlem Yönetimi</span></a></li> 
<li><a href="index.php?option=admin&bolum=db"><span>Veritabanı Yönetimi</span></a></li>
<li><a href="index.php?option=admin&bolum=ayarlar"><span>Yapılandırma</span></a></li>
</ul>

</li>
<li><a href="index.php?option=admin&bolum=stats"><span>İstatistikler</span></a></li>
<li><a href="index.php?option=site"><span>Siteye Geçiş Yap</span></a></li>
<li><a href="index.php?option=logout"><span>Çıkış Yap</span></a></li>    
</ul>
</div>
<?php
}

function loadAdminModule() {
	global $option, $bolum, $task;
	global $id, $cid;
	global $limit, $limitstart;
	global $mainframe, $my, $mosmsg;
	
	switch($option) {
	default:
	AdminPanelMenu();
	break;
	
	case 'admin':
	if ($bolum) {
	include_once(ABSPATH. '/admin/modules/'.$bolum.'/index.php');
	} else {
		Redirect('index.php');
	}
	break;
	
	case 'site':
	convertSite();
	break;
}
}

function convertSite() {
	global $mainframe, $dbase, $my;
	
	if ($my->id == 1) {
	$session = new Session($dbase);
	$session->load($mainframe->_session->session);

	$session->access_type = 'site';
	$session->update();
	
	Redirect('index.php');
	} else {
		NotAuth();
	}    
}

function quickiconButton( $link, $image, $text ) {
?>
<div class="quickicon col-sm-3">
<span>
<a href="<?php echo $link; ?>">
<img src="<?php echo SITEURL;?>/admin/templates/<?php echo ADMINTEMPLATE;?>/images/<?php echo $image;?>" alt="<?php echo $text;?>" title="<?php echo $text;?>" border="0" /><br /><?php echo $text;?>
</a>
</span>
</div>
<?php
}

function AdminPanelMenu() {
	?>
	<div class="panel panel-default">
	<div class="panel-heading"><h4>Yönetim Paneli - Kısayollar</h4></div>
	<div class="panel-body">
	<div class="row">
	<?php 
    echo quickiconButton('index.php?option=admin&bolum=user', 'kullanici.png', 'Kullanıcı Yönetimi');   
	echo quickiconButton('index.php?option=admin&bolum=kapino', 'ilce.png', 'Kapı No Yönetimi');
	echo quickiconButton('index.php?option=admin&bolum=db', 'veri.png', 'Veritabanı Yönetimi');    
    echo quickiconButton('index.php?option=admin&bolum=stats', 'group.png', 'İstatistikler');
	echo quickiconButton('index.php?option=admin&bolum=ayarlar', 'config.png', 'Yapılandırma');
	echo quickiconButton('index.php?option=site', 'change.png', 'Siteye Geçiş Yap');
	?>
	</div>
	</div></div>
	<?php
}
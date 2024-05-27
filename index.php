<?php
//  ;)
define('ERISIM', 1);

require( dirname( __FILE__ ) . '/global.php' );
require( dirname( __FILE__ ) . '/config.php' );

require_once( dirname( __FILE__ ) . '/includes/base.php' );
require_once(dirname( __FILE__ ) . '/includes/functions.php');


$option = strval(strtolower(getParam($_REQUEST, 'option')));
$bolum = strval(strtolower(getParam($_REQUEST, 'bolum')));
$task = strval(strtolower(getParam($_REQUEST, 'task')));
$return = strval( getParam( $_REQUEST, 'return', NULL ) );
$code = strval(getParam($_REQUEST, 'code'));
$return = strval(getParam($_REQUEST, 'return')); 

$mosmsg = getParam($_REQUEST, 'mosmsg');

$mainframe = new mainFrame( $dbase, $option );
$mainframe->initSession();

$my = $mainframe->getUser();

switch($option) {

	case 'login':
	Login();
	break;    
	
	case 'logout':
	Logout();
	break;
	
	case 'cookiecheck':
	CookieCheck();
	break;
	
	//case 'reguser':
	//registerUser();
	//break;
	
	case 'forgot':
	resendPassword();
	break;
	
	case 'activate':
	activeUser($code);
	break;
}

function activeUser($code) {
	global $dbase;
	
	spoofCheck(NULL,1);
	
	$code = htmlspecialchars($code);
	$code = stripslashes($code);
	$code = trim($code);
	
	$query = "SELECT id FROM #__users WHERE activation=".$dbase->Quote($code);
	$dbase->setQuery($query);
	
	$exist = $dbase->loadResult();
	
	if ($exist) {
		$row = new Users($dbase);
		$row->activateUser($exist);
		Redirect('index.php', 'Kullanıcı başarıyla aktive edildi. Artık giriş yapabilirsiniz!');
	} else {
		Redirect('index.php', 'Kullanıcı yok yada daha önce aktive edilmiş!');
	}
}

function resendPassword() {
	global $dbase;
	
	spoofCheck(NULL,1);
	
	$mail = getParam($_REQUEST, 'email');
	
	$mail = htmlspecialchars($mail);
	$mail = stripslashes($mail);
	$mail = trim($mail);
	
	if (!$mail) {
		Redirect('index.php', 'E-posta adresi girmemişsiniz!');
	}
	
	if (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $mail) == false) {
		Redirect('index.php', 'Geçerli bir e-posta adresi girmelisiniz!');
	}
	
	$query = "SELECT id FROM #__users WHERE email=".$dbase->Quote($mail);
	$dbase->setQuery($query);
	
	$exist = $dbase->loadResult();
	
	if ($exist) {		
		$user = new Users($dbase);
		$user->load($exist);
		
		$passwd = MakePassword(8);
		$salt = MakePassword(16);
		$crypt = md5($passwd.$salt);
		
		$password = $crypt.':'.$salt;
		
	   $query    = 'UPDATE #__users SET password = '.$dbase->Quote($password)
	   . ' WHERE id = '.(int)$user->id;
	   $dbase->setQuery($query);
	   $dbase->query();
	   
	   $body = "İsteğiniz üzerine parolanız sıfırlandı.\n";
	   $body.= "----------------------------------------\n";
	   $body.= "Yeni Parolanız: ".$passwd."\n";
	   $body.= "----------------------------------------\n";
	   $body.= "Site: ".SITEURL."\n";
	   $body.= "Lütfen siteye giriş yaparak parolanızı tekrar değiştiriniz.\n";
	   
	   mosMail(MAILFROM, MAILFROMNAME, $mail, 'Yeni Parola', $body, 1, '', '', '', MAILFROM, MAILFROMNAME);
	   Redirect('index.php', 'Yeni parola verdiğiniz e-posta adresine gönderildi!');
	} else {
		Redirect('index.php', 'Verilen e-postaya ait bir kullanıcı yok!');
	}
}

function registerUser() {
	global $dbase;
	
	$row = new Users($dbase);
	
	spoofCheck(NULL,1);
	
	if ( !$row->bind( $_POST ) ) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	
	
	$salt = MakePassword(16);
	$crypt = md5($row->password.$salt);
		
	$row->password = $crypt.':'.$salt;
	
	$row->registerDate = date('Y-m-d H:i:s');
	
	if (USER_ACTIVATION) {
	
	$row->activated = 0;
	$row->activation = $row->createCode();
	
	$alink = '<a href="'.SITEURL.'/index.php?option=active&code='.$row->activation.'">Aktive Et</a>';
	
	$body = "Üyelik talebiniz başarıyla kaydedildi.\n";
	$body.= "----------------------------------------\n";
	$body.= "Aktivasyon Linki: ".$alink."\n";
	$body.= "Aktivasyon Kodu: ".$row->activation."\n";
	$body.= "----------------------------------------\n";
	$body.= "Site: ".SITEURL."\n";
	$body.= "Siteye giriş yapmak için yukarıdaki aktivasyon linkine tıklayınız.\n";
	   
	mosMail(MAILFROM, MAILFROMNAME, $row->email, 'Yeni Üyelik', $body, 1, '', '', '', MAILFROM, MAILFROMNAME);
	
	$msg = 'Kayıt talebiniz kaydedildi. E-posta adresinize aktivasyon linki gönderildi. Lütfen önce aktivasyonu gerçekleştiriniz!';
	
	} else {
		$row->activated = 1;
		$row->activation = '';
		$msg = 'Üyeliğiniz başarıyla gerçekleştirildi. Siteye giriş yapabilirsiniz!';
	}
	
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}		
	
	Redirect( 'index.php', $msg );

}

function Login() {
	global $mainframe, $return;
	
	$mainframe->login();
    
    
/*
if ( $return ) {
		if (isset( $_COOKIE[$mainframe->sessionCookieName()] )) {
			Redirect( $return );
		} else {
			Redirect( 'index.php?option=cookiecheck&return=' . urlencode( $return ) );
		}
	} else {
		if (isset( $_COOKIE[$mainframe->sessionCookieName()] )) {
			Redirect( 'index.php' );
		} else {
			Redirect( 'index.php?option=cookiecheck&return=' . urlencode( 'index.php' ) );
		}
	}
    */
    Redirect('index.php');
}

function Logout() {
	global $mainframe, $return;
	
	$mainframe->logout();
   
	if ( $return ) {
		Redirect( $return );
	} else {
		Redirect( 'index.php' );
	}
}

function CookieCheck() {
	global $mainframe, $return;
	if (isset( $_COOKIE[$mainframe->sessionCookieName()] )) {
		Redirect( $return );
	} else {
		ErrorAlert( 'Çerezler açık değil!' );
	}
}


ob_start();

ob_end_clean();

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

initGzip();

//ziyaretçi
if (!$my->id) {
	if ($option == 'register') {
		$reg = new Users($dbase);
		include_once(ABSPATH. '/site/templates/'.SITETEMPLATE.'/register.php');    
	} else {
		include_once(ABSPATH. '/site/templates/'.SITETEMPLATE.'/login.php');    
	}
}
//kayıtlı kullanıcı
else {
		
		//sistem yöneticisi ise 
    if ($my->access_type == 'admin') {    
        require_once(ABSPATH.'/admin/includes/functions.php');    
            
        include_once(ABSPATH.'/admin/templates/'.ADMINTEMPLATE.'/index.php');
            
    } else {        
        include_once(ABSPATH.'/site/templates/'.SITETEMPLATE.'/index.php');
    }
}

if (DEBUGMODE) {
    ?>

<div class="panel panel-default">    
    <div class="panel-heading"><h4>SQL HATA AYIKLAMA</h4></div>
    
    <div class="panel-body">
    <?php
	echo $dbase->_ticker . ' sorgu çalıştırıldı';
    ?>
    </div>
    <table class="table table-striped">
    <thead>
    <tr>
    <th>SIRA</th>
    <th>SQL SORGUSU</th>
    </tr>
    </thead>
    <tbody>
    <?php
	 foreach ($dbase->_log as $k=>$sql) {
         ?>
         <tr>
         <td><?php echo $k+1;?></td>
         <td><?php echo $sql;?></td>
         </tr>
         <?php
	}
    ?>
	</tbody>
    </table>
    <?php
}
	
doGzip();
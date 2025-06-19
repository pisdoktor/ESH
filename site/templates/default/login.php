<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 
$mainframe->addStyleSheet(SITEURL.'/includes/fontawesome/css/all.min.css');  
?>
<!DOCTYPE html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php $mainframe->showHead();?>
<link rel="alternate" href="<?php echo SITEURL;?>" hreflang="tr" />

</head>
<body>
<?php
$validate = spoofValue(1);
?>
<div id="container">

<div id="header">
<div id="logo clearfix">
<div class="row">
<div class="col-sm-6 text-left">
<img src="<?php echo SITEURL;?>/site/templates/<?php echo SITETEMPLATE;?>/images/evdesagliklogo.png" border="0" alt="<?php echo SITEHEAD;?>" title="<?php echo SITEHEAD;?>" />
</div>

<div class="col-sm-6 text-right">
<img src="<?php echo SITEURL;?>/site/templates/<?php echo SITETEMPLATE;?>/images/sblogo.png" border="0" alt="<?php echo SITEHEAD;?>" title="<?php echo SITEHEAD;?>" />
</div>
</div>
</div>

</div>

<div id="header-bar" class="clearfix">
<div class="header">
<?php siteMenu();?> 
</div>
</div>

<div id="content" class="clearfix">
<?php
	if ($mosmsg) {
	echo '<div id="message" title="Uyarı">'.$mosmsg.'</div>';
	}
?>
<div class="col-sm-4">

</div>

<div class="col-sm-4 center">
<div class="panel panel-default">
<div class="panel-heading"><i class="fa-solid fa-right-to-bracket"></i> ÜYE GİRİŞİ</div>
<div class="panel-body">
<form action="index.php" method="post" name="login" id="loginForm" role="form">

<div class="form-group">
<label class="sr-only" for="username">Kullanıcı Adı:</label>
<input name="username" id="username" type="text" class="form-control" placeholder="Kullanıcı adınızı yazın" required />
</div>

<div class="form-group">
<label class="sr-only" for="password">Parola:</label>
<input type="password" id="password" name="passwd" class="form-control" placeholder="Parolanızı yazın" required />
</div>

 <div class="form-group">
 <div class="checkbox">
 <label>
 <input type="checkbox" name="remember" id="remember" value="yes" /> Beni hatırla</label>
  </div>
  </div>

<div class="form-group">
<button type="submit" class="btn btn-primary">GİRİŞ YAP</button>
</div>   

<div class="form-group">
<a href="#" id="forgot">ŞİFREMİ UNUTTUM!</a>
</div>

<?php if (USER_ACTIVATION) { ?>
<div class="form-group">
<a href="#" id="activ">HESAP AKTİVASYONU</a>
</div>
<?php } ?>




<input type="hidden" name="option" value="login" />
<input type="hidden" name="op2" value="login" />
<input type="hidden" name="return" value="index.php" />
<input type="hidden" name="force_session" value="1" />
<input type="hidden" name="<?php echo $validate; ?>" value="1" />
</form>
</div>
</div>
</div>


<div class="col-sm-4">

</div>

</div><!-- content -->

<div id="forgotpass">
<form action="index.php" method="post" role="form">
<span class="help-block">* Şifrenizi sıfırlamak için lütfen kayıtlı e-posta adresinizi yazın.</span>

<div class="form-group">
<div class="row">
<div class="col-sm-5">
<label for="email">E-posta Adresiniz:</label>
</div>
<div class="col-sm-7">
<input type="text" name="email" id="email" class="form-control" required />
</div> 
</div>
</div>

<div class="form-group">
<div class="row">
<div class="col-sm-12">
<input type="submit" name="button" class="btn btn-info" value="PAROLAYI SIFIRLA" />
</div>
</div> 
</div>

<input type="hidden" name="option" value="forgot" />
<input type="hidden" name="<?php echo $validate; ?>" value="1" />
</form>
</div>

<?php if (USER_ACTIVATION) { ?>
<div id="activation">
<form action="index.php" method="post" role="form">
<span class="help-block">* E-posta adresinize gönderilen aktivasyon kodunu giriniz.</span>

<div class="form-group">
<div class="row">
<div class="col-sm-5">
<label for="code">Aktivasyon Kodu:</label>
</div>
<div class="col-sm-7">
<input type="text" name="code" id="code" class="form-control" required />
</div> 
</div>
</div>

<div class="form-group">
<div class="row">
<div class="col-sm-12">
<input type="submit" name="button" class="btn btn-warning" value="AKTİVE ET!" />
</div>
</div> 
</div>

<input type="hidden" name="option" value="activate" />
<input type="hidden" name="<?php echo $validate; ?>" value="1" />
</form>
</div>
<?php } ?>

<?php include(ABSPATH.'/includes/footer.php');?>

</div><!-- container -->
</body>
</html>
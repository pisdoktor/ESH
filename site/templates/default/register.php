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
<script type="text/javascript">
$(function(){
	$('input[name=password2]').on('keyup', function(){
		
		var pwd = $('input[name=password]').val();
		var confirm_pwd = $(this).val();
	
		$('span.error').hide();
		
		if( pwd != confirm_pwd ){
			$('span.error').show();
		}
	});	
});
</script>
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
</div></div>
</div>

<div id="header-bar" class="clearfix">
<div class="header">
<?php siteMenu();?> 
</div>
</div>

<div id="content" class="clearfix">
<form action="index.php?option=reguser" method="post" id="adminForm" role="form"> 
<div class="panel panel-default">
        <div class="panel-heading"><h4>Üye Kayıt Formu</h4></div>
        
        <div class="panel-body">
        
        <div class="form-group row">
        <div class="col-sm-3"><label for="name">Adınız ve Soyadınız:</label></div>
        <div class="col-sm-4"><input name="name" id="name" type="text" class="form-control" placeholder="Adınızı ve soyadınızı yazın" required /></div>
        </div>
        
        <div class="form-group row">
        <div class="col-sm-3"><label for="username">Kullanıcı Adınız:</label></div>
        <div class="col-sm-4"><input name="username" id="username" type="text" class="form-control" placeholder="Kullanıcı adınızı yazın" required /></div>
        </div>
        
        <div class="form-group row">
        <div class="col-sm-3"><label for="password">Parolanız:</label>  </div>
        <div class="col-sm-4"><input name="password" id="password" type="password" class="form-control" placeholder="Parolanızı yazın" required /></div>
        </div>
        
        <div class="form-group row">
        <div class="col-sm-3"><label for="password2">Parolanız Tekrar:</label>  </div>
        <div class="col-sm-4"><input name="password2" id="password2" type="password" class="form-control" placeholder="Parolanızı tekrar yazın" required /></div> 
        <div class="col-sm-5"><span class="error" style="display: none; background-color: red;"> * Parolalar uyuşmuyor!</span></div>  
        </div>
        
        <div class="form-group row">
        <div class="col-sm-3"><label for="email">E-posta Adresiniz:</label> </div>
        <div class="col-sm-4"><input name="email" id="email" type="text" class="form-control" placeholder="E-posta adresinizi yazın" required /></div> 
        </div>
        
        <div class="form-group row">
        <div class="col-sm-3"></div>
        <div class="col-sm-4"><button type="submit" class="btn btn-primary">SİTEYE KAYIT OL!</button></div>
        </div> 
        
        </div>

</div>

<input type="hidden" name="<?php echo $validate; ?>" value="1" />
</form>


</div><!-- content -->

<?php include(ABSPATH.'/includes/footer.php');?>

</div><!-- container -->
</body>
</html>
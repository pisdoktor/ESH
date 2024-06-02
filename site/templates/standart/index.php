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
	echo '<div id="message" title="Mesaj">'.$mosmsg.'</div>';
	}
?>
<?php loadSiteModule();?>
</div><!-- content -->

<?php include(ABSPATH.'/includes/footer.php');?>

</div><!-- container -->


<a href="#" id="scroll-top"></a>
</body>
</html>
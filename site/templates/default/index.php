<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

$mainframe->addStyleSheet(SITEURL.'/includes/fontawesome/css/all.min.css');  
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Evde Sağlık Hizmetleri Sistemi" />
<meta name="keywords" content="" />
<meta name="Generator" content="Soner Ekici" />
<meta name="robots" content="index, follow" />
<link rel="stylesheet" type="text/css" href="<?php echo SITEURL;?>/templates/default/fontawesome/css/all.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITEURL;?>/templates/default/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITEURL;?>/templates/default/bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITEURL;?>/templates/default/css/bootstrap-datepicker3.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITEURL;?>/templates/default/css/global.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITEURL;?>/templates/default/css/cssmenu.css" />
<script src="<?php echo SITEURL;?>/templates/default/jquery/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo SITEURL;?>/templates/default/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo SITEURL;?>/templates/default/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/js/bootstrap-datepicker.tr.min.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/js/validator.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/js/tableToExcel.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/js/global.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/js/cssmenu.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/calendar/index.global.js" type="text/javascript"></script>
<script src="http://localhost/esh102/includes/global/calendar/locales/tr.global.js" type="text/javascript"></script>
<link rel="alternate" href="<?php echo SITEURL;?>" hreflang="tr" />

</head>

<body>
<div id="container">

<div id="header">
<div id="logo clearfix">

<div class="row">

<div class="col-sm-5 text-left">
<img src="<?php echo SITEURL;?>/site/templates/<?php echo SITETEMPLATE;?>/images/evdesagliklogo.png" border="0" alt="<?php echo SITEHEAD;?>" title="<?php echo SITEHEAD;?>" />
</div>

<div class="col-sm-5 text-right">
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
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

$mainframe->addStyleSheet(SITEURL.'/includes/fontawesome/css/all.min.css'); 
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php $mainframe->showHead();?>
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
<?php adminMenu();?> 
</div>
</div>

<div id="content" class="clearfix">
<?php
    if ($mosmsg) {
    echo '<div id="message" title="Mesaj">'.$mosmsg.'</div>';
    }
?>
<?php loadAdminModule();?>
</div><!-- content -->

<?php include(ABSPATH.'/includes/footer.php');?>

</div><!-- container -->
</body>
</html>
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

$id = intval(getParam($_REQUEST, 'id')); 

$ilce = intval(getParam($_REQUEST, 'ilce'));
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));

$today = date('w');
$day = intval(getParam($_REQUEST, 'day', $today));

$ordering = getParam($_REQUEST, 'ordering');  

include(dirname(__FILE__). '/html.php');

/**
* @desc config.php üzerinden değiştirilecek ekip sayısına göre bölge sayısı oluşturulacak
* 
* define('ekipSayisi', 2);
* 
*/


switch($task) {

    default:
    case 'list':
    MahalleListesi($limit, $limitstart, $ordering, $ilce);
    break;
    
    case 'edit':
    MahalleBolgeAta($id);
    break;
    
    case 'save':
    MahalleBolgeKaydet();
    break;
    
    case 'table':
    tabloYap($day);
    break;
      
}

function tabloYap($day) {
    global $dbase;
    
    $dbase->setQuery("SELECT m.*, COUNT(h.id) AS hastasayisi, i.ilce FROM #__mahalle AS m "
    . "\n LEFT JOIN #__hastalar AS h ON h.mahalle=m.id "
    . "\n LEFT JOIN #__ilce AS i ON i.id=m.ilceid "
    . "\n WHERE h.pasif=0 AND m.bolge>0 AND FIND_IN_SET(".$day.", m.gun) "
    . "\n GROUP BY m.id "
    );
    
    $rows = $dbase->loadResultArray();

    
    $root = array();
    
    foreach ($rows as $row) {
        if (!isset($root[$row->id])) {
            $root[$row->id][$row->bolge]['id'] = $row->bolge;
            $root[$row->id][$row->bolge]['mahalle'] = $row->mahalle;
            $root[$row->id][$row->bolge]['hastasayisi'] = $row->hastasayisi;
            
        }
    
    }
                    
     ?>
        <div class="panel panel-default">
    
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-6">
    <h4><i class="fa-solid fa-eye"></i> Planlama Listesi</h4>
    
    </div>
    <div class="col-xs-6" align="right">
    <ul class="nav nav-pills">
  <li<?php echo $day == 1 ? ' class="active"':'';?>><a href="index.php?option=admin&bolum=planlama&task=table&day=1">Pazartesi</a></li>
  <li<?php echo $day == 2 ? ' class="active"':'';?>><a href="index.php?option=admin&bolum=planlama&task=table&day=2">Salı</a></li>
  <li<?php echo $day == 3 ? ' class="active"':'';?>><a href="index.php?option=admin&bolum=planlama&task=table&day=3">Çarşamba</a></li>
  <li<?php echo $day == 4 ? ' class="active"':'';?>><a href="index.php?option=admin&bolum=planlama&task=table&day=4">Perşembe</a></li>
  <li<?php echo $day == 5 ? ' class="active"':'';?>><a href="index.php?option=admin&bolum=planlama&task=table&day=5">Cuma</a></li>
  <li<?php echo $day == 6 ? ' class="active"':'';?>><a href="index.php?option=admin&bolum=planlama&task=table&day=6">Cumartesi</a></li>
  <li<?php echo $day == 0 ? ' class="active"':'';?>><a href="index.php?option=admin&bolum=planlama&task=table&day=0">Pazar</a></li>
</ul>
    </div>
    </div>
    </div>
    <?php

}


function MahalleListesi($limit, $limitstart, $ordering, $ilce) {
    global $dbase;
    
    $il = new Ilce($dbase);
    
    if ($ilce) {
         $where[] = "m.ilceid='".$ilce."'";
     }
    
    $dbase->setQuery("SELECT COUNT(*) FROM #__mahalle AS m"
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    );
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY m.ilceid ASC, m.mahalle ASC ";
     }
    
    $dbase->setQuery("SELECT m.*, i.ilce AS ilceadi FROM #__mahalle AS m "
    . "\n LEFT JOIN #__ilce AS i ON i.id=m.ilceid "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    . $orderingfilter  
    . "\n ", $limitstart, $limit);
    
    $rows = $dbase->loadObjectList();
    
    $link = "index.php?option=admin&bolum=planlama";
    
    if ($ilce) {
            $link .= "&amp;ilce=".$ilce;
        }
      ?>
      <form action="index.php" method="get" name="adminForm" role="form">
        <input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="planlama" />
<input type="hidden" name="task" value="list" />
<input type="hidden" name="boxchecked" value="0" />

        <div class="panel panel-default">
    <div class="panel-heading"><h4><i class="fa-solid fa-house"></i> Yönetim Paneli - Mahalle Bölge Planlama</h4></div>
    <div class="panel-body">
    
<div class="form-group">

<div class="col-sm-9">
<div class="btn-group">
<input type="button" name="button" value="Seçileni Düzenle" onclick="javascript:if (document.adminForm.boxchecked.value == 0){ alert('Lütfen listeden bir seçim yapın'); } else {submitbutton('edit');}" class="btn btn-default" /> 
</div>
</div>
 

  <div class="col-sm-2">    
    <?php echo $il->getIlce($link, $ilce);?>
  </div> 
  <div class="col-sm-1">
    <?php echo $pageNav->getLimitBox($link);?>
 </div>

</div> <!-- form group -->

</div> <!-- panel body -->

<table class="table table-striped">
<thead>
<tr>
<th>SIRA</th>
<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
<th>Mahalle ID
 <span><a href="<?php echo $link;?>&ordering=m.id-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.id-ASC">▼</a></span>
</th>
<th>Mahallenin Adı
 <span><a href="<?php echo $link;?>&ordering=m.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.mahalle-ASC">▼</a></span>
</th>
<th>Mahallenin Bölgesi
 <span><a href="<?php echo $link;?>&ordering=m.bolge-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.bolge-ASC">▼</a></span>
</th>
<th>Mahallenin Günü
 <span><a href="<?php echo $link;?>&ordering=m.gun-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.gun-ASC">▼</a></span>
</th>
<th>İlçe Adı
 <span><a href="<?php echo $link;?>&ordering=m.ilceid-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=m.ilceid-ASC">▼</a></span>
</th>
</tr>
</thead>
<tbody>
<?php
$t = 0;
for($i=0; $i<count($rows);$i++) {
$row = $rows[$i];

$gun = explode(',',$row->gun);

$gunler = array('Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi');

$checked = mosHTML::idBox( $i, $row->id );
?>
<tr>
<td><?php echo $pageNav->rowNumber( $i ); ?></td>
<td><?php echo $checked;?></td>
<td><?php echo $row->id;?></td>
<td><a href="index.php?option=admin&bolum=planlama&task=edit&id=<?php echo $row->id;?>"><?php echo $row->mahalle;?></a></td>
<td><?php echo $row->bolge;?>. Bölge</td>
<td><?php
foreach ($gun as $gun) {
    echo $gunler[$gun].' ';
}
?></td>
<td><?php echo $row->ilceadi;?></td>
</tr>
<?php
$t = 1 - $t;
}
?>
</tbody>
</table>
  
</form>

<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);?>
</div>
</div>
</div>

</div>  
      <?php
}

function MahalleBolgeAta($id) {
    global $dbase;
    
        global $dbase;
    
    $row = new Mahalle($dbase);
    $row->load($id);
    
    if (defined(bolgeSayisi)) {
        $bolgesayisi = bolgeSayisi;
    } else {
        $bolgesayisi = '7';
    }
    
  
    $bolgeler = array();
    $bolgeler[] = mosHTML::makeOption('', 'Bir Bölge Seçin');
    $i = 1;
    while($i <= $bolgesayisi) {
    $bolgeler[] = mosHTML::makeOption($i, $i.'. Bölge');
    $i++;
    }
    
    
    $lists['bolge'] = mosHTML::selectList($bolgeler, 'bolge', '', 'value', 'text', $row->bolge);
    
    
    //pansuman listesi işlemleri
    $daylist[] = mosHTML::makeOption('1', 'Pazartesi');
    $daylist[] = mosHTML::makeOption('2', 'Salı');
    $daylist[] = mosHTML::makeOption('3', 'Çarşamba');
    $daylist[] = mosHTML::makeOption('4', 'Perşembe');
    $daylist[] = mosHTML::makeOption('5', 'Cuma');
    $daylist[] = mosHTML::makeOption('6', 'Cumartesi');
    $daylist[] = mosHTML::makeOption('0', 'Pazar');
    
    $row->gun = explode(',',$row->gun);
   
    
    $lists['gun'] = mosHTML::checkboxList($daylist, 'gun', '', 'value', 'text', $row->gun);

    ?>
    <div class="panel panel-default">
    <div class="panel-heading"><h4>Yönetim Paneli - Mahalle <?php echo $row->id ? 'Düzenle' : 'Ekle';?></h4>
    </div>
    <div class="panel-body">

        <script language="javascript" type="text/javascript">
        <!--
        function submitbutton(pressbutton) {
            var form = document.adminForm;

            if (pressbutton == 'cancel') {
                submitform( pressbutton );
                return;
            }
            // do field validation
            if (form.mahalle.value == ""){
                alert( "Mahalle Adını boş bırakmışsınız" );
            }  else  {
        submitform( pressbutton );
            }
        }
        //-->
        </script> 
<form action="index.php" method="post" name="adminForm" role="form">

<div class="form-group row">
<div class="col-sm-3">
<label for="mahalle">Mahalle Adı:</label></div>
<div class="col-sm-9"><input type="text" id="mahalle" name="mahalle" class="form-control" value="<?php echo $row->mahalle;?>" required></div>
</div>


<div class="form-group row">
<div class="col-sm-3">
<label for="mahalle">Bölge Adı:</label></div>
<div class="col-sm-9"><?php echo $lists['bolge'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-3">
<label for="mahalle">Gidilecek Günler:</label></div>
<div class="col-sm-9"><?php echo $lists['gun'];?></div>
</div>



<input type="hidden" name="option" value="admin" />
<input type="hidden" name="bolum" value="planlama" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $row->id;?>" />
</form>
<br />

<div>
<input type="button" name="button" value="Kaydet" onclick="javascript:submitbutton('save');" class="btn btn-primary"  />
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>  
</div>

</div>
</div>
      <?php
}

function MahalleBolgeKaydet() {
    global $dbase;
    
    $row = new Mahalle( $dbase );
    
   
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if ($row->gun) {
    $row->gun = implode(',',$row->gun);
    }
    
    if (!$row->check()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    Redirect('index.php?option=admin&bolum=planlama', 'Mahalle bölgesi kaydedildi');


}
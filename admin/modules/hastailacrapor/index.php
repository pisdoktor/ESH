<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');

$id = strval(getParam($_REQUEST, 'id'));


switch($task) {
    default:
    getHastalikList($id);
    break;
    
    case 'save':
    saveRapor();
    break;
    
    case 'delete':
    deleteRapor($id);
    break;
}

function deleteRapor($id) {
    global $dbase;
    
    $dbase->setQuery("DELETE FROM #__hastailacrapor WHERE id=".$id);
    $dbase->query();

}

function saveRapor() {
    global $dbase;
    
    $row = new HastaIlacRapor( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    if ($row->rapor) {
    
    if ($row->brans) {
    $row->brans = implode(',', $row->brans);
    }
    
    $row->bitistarihi = tarihCevir($row->bitistarihi);
    
    } else {
    $row->brans = '';
    $row->bitistarihi = '';
    $row->raporyeri = ''; 
    }
    
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $dbase->setQuery("SELECT id FROM #__hastalar WHERE tckimlik=".$row->hastatckimlik);
    $hastaid = $dbase->loadResult();
    
    Redirect("index.php?option=admin&bolum=hastailacrapor&id=".$hastaid); 

}


function getHastalikList($id) {
    global $dbase;
    
    //hasta bilgilerini çekelim
    $row = new Hasta($dbase);
    $row->load($id);
    
    //hastanın hastalıklarını alalım
    $dbase->setQuery("SELECT hastaliklar FROM #__hastalar WHERE tckimlik=".$row->tckimlik);
    $hastaliklar = $dbase->loadResult();

    //hastalık bilgilerini çekelim
    $dbase->setQuery("SELECT id, hastalikadi FROM #__hastaliklar WHERE id IN (".$hastaliklar.")");
    $hastaliklar = $dbase->loadObjectList();
    
    //hasta ve hastalıklarına göre tablo oluşturalım ve rapor bilgilerini çekelim
     ?>
    <div class="panel panel-<?php echo $row->pasif ? 'warning':'info';?>">
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-9"><h4><i class="fa-solid fa-file-lines"></i> Hasta İlaç Rapor Bilgileri:  <span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>"><?php echo $row->isim." ".$row->soyisim;?></a></span> <sub>(<?php echo $row->anneAdi ? $row->anneAdi:'';?>/<?php echo $row->babaAdi ? $row->babaAdi:'';?>)</sub> <?php echo $row->pasif ? '('.pasifNedeni($row->pasifnedeni).')' : '' ;?></div>
    <div class="col-xs-3" align="right"><h4><?php echo $row->pasif ? '<i class="fa-solid fa-triangle-exclamation"></i> DOSYA KAPALI ('.tarihCevir($row->pasiftarihi, 1).')': '';?></h4></div>
    </div>
    </div>
    <table class="table table-striped table-hover">
    <thead>
    <tr>
    <th>Hastalık Adı</th>
    <th>Raporlu Mu?</th>
    <th>Rapor Bitiş Tarihi</th>
    <th>Raporu Yazan Branş</th>
    <th>Rapor DDH mı Yazılmış</th>
    <th>İşlem</th>
    </tr>
    </thead>
    <?php
    foreach ($hastaliklar as $hastalik) {
        $dbase->setQuery("SELECT * FROM #__hastailacrapor WHERE hastatckimlik='".$row->tckimlik."' AND hastalikid='".$hastalik->id."'");
        $dbase->loadObject($bilgi[$hastalik->id]);
        
        //branşları alalım
        $dbase->setQuery("SELECT * FROM #__branslar");
        $bslar = $dbase->loadObjectList();
        
        $b = array();
        
        foreach ($bslar as $bs) {
        $b[] = mosHTML::makeOption($bs->id, $bs->bransadi);
        }
        
        $branss = explode(',', $bilgi[$hastalik->id]->brans);
        
        $lists[$hastalik->id]['branslar'] = mosHTML::checkboxList($b, 'brans', '', 'value', 'text', $branss);
        
        if ($bilgi[$hastalik->id]->brans) {
        $dbase->setQuery("SELECT bransadi FROM #__branslar WHERE id IN (".$bilgi[$hastalik->id]->brans.")");
        $branslar[$hastalik->id] = $dbase->loadResultArray();
        }
     ?>
     <tr>
     <th><?php echo $hastalik->hastalikadi;?></th>
     <td><?php echo $bilgi[$hastalik->id]->rapor ? '<strong>Evet</strong>':'Hayır';?></td>
     <td><?php echo $bilgi[$hastalik->id]->bitistarihi ? tarihCevir($bilgi[$hastalik->id]->bitistarihi, 1): '';?></td>
     <td><?php echo implode(', ', $branslar[$hastalik->id]);;?></td>
     <td><?php echo $bilgi[$hastalik->id]->rapor ? ($bilgi[$hastalik->id]->raporyeri ? '<strong>DDH</strong>':'Dış Merkez') : '';?></td>
     <td><a href="#" data-toggle="modal" data-target="#<?php echo $hastalik->id;?>" />Düzenle</a></td>
     </tr>
     
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<div id="<?php echo $hastalik->id;?>" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Rapor Bilgileri: <?php echo $hastalik->hastalikadi;?></h4>
      </div>
      <div class="modal-body">
      
<div class="form-group row">
<div class="col-sm-6"><label>Raporlu mu?:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('rapor', '', $bilgi[$hastalik->id]->rapor);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="raporbitistarihi<?php echo $hastalik->id;?>">Rapor Bitiş Tarihi:</label></div>
<div class="col-sm-6 input-group date" id='raporbitistarihi<?php echo $hastalik->id;?>' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="raporbitistarihi<?php echo $hastalik->id;?>" name="bitistarihi" autocomplete="off" value="<?php echo $bilgi[$hastalik->id]->bitistarihi ? tarihCevir($bilgi[$hastalik->id]->bitistarihi, 1): '';?>" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="rapor">Raporu Yazan Branş:</label></div>
<div class="col-sm-6"><?php echo $lists[$hastalik->id]['branslar'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label>Rapor DDH mı yazılmış?:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('raporyeri', '', $bilgi[$hastalik->id]->raporyeri);?></div>
</div>

      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
      
    </div>
    <!-- modal content -->
  </div>
</div>  <!-- modal -->
<input type="hidden" name="option" value="admin">
<input type="hidden" name="bolum" value="hastailacrapor">
<input type="hidden" name="task" value="save">
<input type="hidden" name="id" value="<?php echo $bilgi[$hastalik->id]->id;?>">
<input type="hidden" name="hastalikid" value="<?php echo $hastalik->id;?>">
<input type="hidden" name="hastatckimlik" value="<?php echo $row->tckimlik;?>">
</form>
         <script>                                  

    $('#raporbitistarihi<?php echo $hastalik->id;?>').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
    });     

</script>
     <?php
    }
    ?>
    </table>
    </div>
    <?php
}

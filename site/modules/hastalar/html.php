<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class HastaList {
    
    function hastaGoster($hasta, $hastaliklar, $sonizlem, $lists) {
        //dogum tarihi düzelt
        $tarih = explode('.',$hasta->dogumtarihi);
        $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
        $hasta->dtarihi = strftime("%d.%m.%Y", $tarih);
        ?>
     <div class="panel panel-<?php echo $hasta->pasif ? 'warning':'info';?>">
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-9"><h4><i class="fa-solid fa-file-lines"></i> Hasta Bilgileri:  <span style="color:<?php echo $hasta->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $hasta->isim." ".$hasta->soyisim;?></span> <sub>(<?php echo $hasta->anneAdi ? $hasta->anneAdi:'';?>/<?php echo $hasta->babaAdi ? $hasta->babaAdi:'';?>)</sub> <?php echo $hasta->pasif ? '('.pasifNedeni($hasta->pasifnedeni).')' : '' ;?></div>
    <div class="col-xs-3" align="right"><h4><?php echo $hasta->pasif ? '<i class="fa-solid fa-triangle-exclamation"></i> DOSYA KAPALI ('.tarihCevir($hasta->pasiftarihi, 1).')': '';?></h4></div>
    </div>
    </div>
    
    <div class="panel-body">
                                                                                                                                  
    <div class="col-sm-7"> 
    <div class="form-group row">
<div class="col-sm-4"><label for="tckimlik">TC Kimlik Numarası:</label></div>
<div class="col-sm-8"><?php echo $hasta->tckimlik;?></div>
</div>

    <div class="form-group row">
<div class="col-sm-4"><label for="dogumtarihi">Doğum Tarihi:</label></div>
<div class="col-sm-8"><?php echo $hasta->dtarihi;?> <strong>(<?php echo yas_bul($hasta->dogumtarihi);?> Yaş)</strong></div>
</div>


    
 <div class="form-group row">
<div class="col-sm-4"><label for="adres">Adres Bilgisi:</label></div>
<div class="col-sm-8"><?php echo $hasta->mahalleadi;?> MAH. <?php echo $hasta->sokakadi;?> SK-CD NO: <?php echo $hasta->kapino;?> - <?php echo $hasta->ilceadi;?></div>
</div>

 <div class="form-group row">
<div class="col-sm-4"><label for="kayittarihi">Kayıt Tarihi:</label></div>
<div class="col-sm-8"><?php echo $hasta->kayityili;?> / <?php echo aySecimi($hasta->kayitay);?></div>
</div>

<div class="form-group row">
<div class="col-sm-4"><label for="dogumtarihi">Telefon Numarası 1:</label></div>
<div class="col-sm-8"><?php echo $hasta->ceptel1;?></div>
</div>

<div class="form-group row">
<div class="col-sm-4"><label for="dogumtarihi">Telefon Numarası 2:</label></div>
<div class="col-sm-8"><?php echo $hasta->ceptel2;?></div>
</div>

 <div class="form-group row">
<div class="col-sm-4"><label for="bagimlilik">Bağımlılık Durumu:</label></div>
<div class="col-sm-8"><?php echo bagimlilikDurumu($hasta->bagimlilik);?></div>
</div>

 <div class="form-group row">
<div class="col-sm-4"><label for="bki">Beden Kitle İndeksi:</label></div>
<div class="col-sm-8"><?php echo bkiHesapla($hasta->kilo, $hasta->boy);?></div>
</div>

 <div class="form-group row">
<div class="col-sm-4"><label for="bagimlilik">Barthel İndeksi:</label></div>
<div class="col-sm-8"><a href="#" data-toggle="modal" data-target="#barthel"><span class="label label-info"><?php echo barthelHesapla($hasta->id)?></span></a></div>
</div>

 <div class="form-group row">
<div class="col-sm-4"><label for="toplamizlem">Toplam İzlem Sayısı:</label></div>
<div class="col-sm-8"><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $hasta->tckimlik;?>"><span class="label label-warning"><?php echo $hasta->toplamizlem;?> İzlem</span></a></div>
</div>

<div class="form-group row">
<div class="col-sm-4"><label for="toplamizlem">Son İzlem Tarihi:</label></div>
<div class="col-sm-8"><?php echo tarihCevir($sonizlem->izlemtarihi, 1);?> - <?php echo $sonizlem->yapilan;?> (<?php echo $sonizlem->yapanlar;?>)</div>
</div>

 <div class="form-group row">
<div class="col-sm-4"><label for="hbilgileri">Hastalık Bilgileri:</label></div>
<div class="col-sm-8"><?php echo implode(', ', $hastaliklar);?></div>
</div>

<?php if ($hasta->pasif) {
?>
 <div class="form-group row">
<div class="col-sm-4"><label for="hbilgileri">Pasifleştirme Tarihi:</label></div>
<div class="col-sm-8"><?php echo tarihCevir($hasta->pasiftarihi, 1);?></div>
</div>

 <div class="form-group row">
<div class="col-sm-4"><label for="hbilgileri">Pasifleştirme Nedeni:</label></div>
<div class="col-sm-8"><?php echo pasifNedeni($hasta->pasifnedeni);?></div>
</div>

<?php
}
?>
</div> 

    <div class="col-sm-5">
<ul class="nav nav-pills">
  <li class="active"><a data-toggle="pill" href="#geneltab">Genel</a></li>
  <li><a data-toggle="pill" href="#sondatab">Sonda</a></li>
  <li><a data-toggle="pill" href="#mamatab">Mama</a></li>
  <li><a data-toggle="pill" href="#beztab">Alt Bezi</a></li>
  <li><a data-toggle="pill" href="#pansumantab">Pansuman</a></li>
  <li><a data-toggle="pill" href="#yataktab">Hasta Yatağı</a></li>
</ul>

<div class="tab-content">
  <div id="geneltab" class="tab-pane fade in active">
    <table class="table table-striped">
<tr>
<th class="col-xs-6">Geçici Takipli Hasta:</th>
<td class="col-xs-6"><?php echo $hasta->gecici ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=gecici&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=gecici&secim=1">Hayır</a>';?></td>
</tr>
<tr>
<th>Nazogastrik Takılı:</th>
<td><?php echo $hasta->ng ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=ng&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=ng&secim=1">Hayır</a>';?></td>
</tr>
<tr>
<th>PEG Takılı:</th>
<td><?php echo $hasta->peg ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=peg&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=peg&secim=1">Hayır</a>';?></td>
</tr>
<tr>
<th>Port Takılı:</th>
<td><?php echo $hasta->port ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=port&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=port&secim=1">Hayır</a>';?></td>
</tr>
<tr>
<th>Oksijen Bağımlı:</th>
<td><?php echo $hasta->o2bagimli ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=o2bagimli&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=o2bagimli&secim=1">Hayır</a>';?></td>
</tr>
<tr>
<th>Ventilatör Takılı:</th>
<td><?php echo $hasta->ventilator ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=ventilator&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=ventilator&secim=1">Hayır</a>';?></td>
</tr>
<tr>
<th>Kolostomi Takılı:</th>
<td><?php echo $hasta->kolostomi ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=kolostomi&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=kolostomi&secim=1">Hayır</a>';?></td>
</tr>
</table>
  </div>
  
  <div id="sondatab" class="tab-pane fade">
     <table class="table table-striped">
<tr>
<th>Sonda Takılı:</th>
<td><?php echo $hasta->sonda ? '<a href="#" data-toggle="modal" data-target="#sonda" /><strong>Evet</strong></a>': '<a href="#" data-toggle="modal" data-target="#sonda" />Hayır</a>';?></td>
</tr>
<?php if ($hasta->sonda) { ?>
<tr>
<th>Sonda Takılma Tarihi:</th>
<td><?php echo tarihCevir($hasta->sondatarihi, 1);?></td>
</tr>
<?php } ?>
</table>
  </div>
  
  <div id="mamatab" class="tab-pane fade">
    <table class="table table-striped">
<tr>
<th>Mama Kullanımı:</th>
<td><a href="#" data-toggle="modal" data-target="#mama" /><?php echo $hasta->mama ? '<strong>Evet</strong>': 'Hayır';?></a></td>
</tr>
<?php if ($hasta->mama) { ?>
<tr>
<th>Mama Markası:</th>
<td><?php echo $hasta->mamacesit;?></td>
</tr>
<tr>
<th>Mama Raporu Bitiş Tarihi:</th>
<td><?php echo tarihCevir($hasta->mamaraporbitis, 1);?></td>
</tr>
<tr>
<th>Rapor DDH mı yazılmış</th>
<td><?php echo $hasta->mamaraporyeri ? 'Evet':'Hayır';?></td>
</tr>
<?php } ?>
</table>
  </div>
  
  <div id="beztab" class="tab-pane fade">
    <table class="table table-striped">
<tr>
<th>Hasta Alt Bezi Kullanımı:</th>
<td><?php echo $hasta->bez ? '<a href="#" data-toggle="modal" data-target="#bez" /><strong>Evet</strong></a>': '<a href="#" data-toggle="modal" data-target="#bez" />Hayır</a>';?></td>
</tr>
<?php if ($hasta->bez) { ?>
<tr>
<th>Bez Raporu Var mı?:</th>
<td><?php echo $hasta->bezrapor ? 'Evet':'Hayır';?></td>
</tr>
<?php if ($hasta->bezrapor) { ?>
<tr>
<th>Bez Raporu Bitiş Tarihi:</th>
<td><?php echo tarihCevir($hasta->bezraporbitis, 1);?></td>
</tr>
<?php } ?>
<?php } ?>
</table>
  </div>
  
  <div id="pansumantab" class="tab-pane fade">
    <table class="table table-striped">
<tr>
<th>Pansuman Takibi</th>
<td><?php echo $hasta->pansuman ? '<a href="#" data-toggle="modal" data-target="#pansuman" /><strong>Evet</strong></a>':'<a href="#" data-toggle="modal" data-target="#pansuman" />Hayır</a>';?></td>
</tr>
<?php if ($hasta->pansuman) {?>
<tr>
<th>Pansuman Günleri</th>
<td><?php 
$gunler = array('Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi');
foreach ($hasta->pgunleri as $pgunleri) {
echo $gunler[$pgunleri].' ';
}
?></td>
</tr>
<?php } ?>
</table>
  </div>
  
  <div id="yataktab" class="tab-pane fade">
     <table class="table table-striped">
<tr>
<th>Hasta Yatağı Var mı?:</th>
<td><?php echo $hasta->yatak ? '<strong><a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=yatak&secim=0">Evet</a></strong>': '<a href="index.php?option=site&bolum=hastalar&task=change&id='.$hasta->id.'&ozellik=yatak&secim=1">Hayır</a>';?></td>
</tr>
</table>
  </div>
  
</div> <!-- tab content -->

<!-- hasta notları -->
<div class="panel panel-warning">
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-8"><h4><i class="fa-solid fa-file-lines"></i> Hasta Notları</h4></div>
    <div class="col-xs-4"><a href="#" data-toggle="modal" data-target="#changenotes" class="btn btn-info">Not Ekle</a></div>
    </div>
    </div>
    
    <div class="panel-body">
<?php echo $hasta->notes ? nl2br($hasta->notes) : 'Henüz not alınmamış';?>
</div>
</div>

<!-- hasta notları -->

    </div><!-- col-sm-5-->

    </div> <!-- panelbody-->  
    
    <div class="panel-footer">
    
    <div class="row">
    <div class="col-xs-6">
    <div class="form-group btn-group">
<a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $hasta->id;?>" class="btn btn-warning">Detaylı Düzenle</a>
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
<a href="#" data-toggle="modal" data-target="#ek3hazirla" class="btn btn-info">EK-3 Hazırla</a>
<a href="index.php?option=site&bolum=hastailacrapor&id=<?php echo $hasta->id;?>" class="btn btn-primary">Hastalık Rapor Bilgileri</a> 
</div>
    </div>
    <div class="col-xs-6">
   <div class="form-group btn-group">
<a href="#" data-toggle="modal" data-target="#changeadres" class="btn btn-info">Adres Değiştir</a>
<a href="#" data-toggle="modal" data-target="#changetelefon" class="btn btn-primary">Telefon Numarası Değiştir</a>
<a href="#" data-toggle="modal" data-target="#changekimlik" class="btn btn-default">Temel Bilgileri Değiştir</a>
<a href="#" data-toggle="modal" data-target="#hastapasif" class="btn btn-danger"><?php echo $hasta->pasif ? 'Dosyayı Aktifleştir':'Dosyayı Pasifleştir';?></a>
</div> 
    </div>
    </div>

    </div>
    </div> <!-- panel -->
 

    <!-- modal kutular başlangıç -->
        <!-- hasta not modal -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="notekle">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>">

<div id="changenotes" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Not Ekle: <?php echo $hasta->isim." ".$hasta->soyisim;?></h4>
      </div>
      <div class="modal-body">
      
      <textarea class="form-control" id="notes" name="notes" rows="5"><?php echo $hasta->notes;?></textarea>
      
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-info" id="clear">Tümünü Sil</button>
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
      </div>
    <!-- modal content -->
  </div>
</div>  <!-- modal -->
<script>
$(document).ready(function(){
  $("#clear").on("click", function(){
       $('#notes').val('');
});
});
</script>
</form>
<!-- hasta not modal -->

    <!-- ek 3 istem modal -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="ek3hazirla">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>">

<div id="ek3hazirla" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">EK-3 Hazırla: <?php echo $hasta->isim." ".$hasta->soyisim;?></h4>
      </div>
      <div class="modal-body">
      
      <?php echo $lists['ek3istek'];?>
      
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Hazırla</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
      </div>
    <!-- modal content -->
  </div>
</div>  <!-- modal -->
</form>
<!-- ek3 istem modal -->
    <!-- kimlik bilgisi değiştirme modal -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<div id="changekimlik" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Kimlik Bilgileri: <?php echo $hasta->isim." ".$hasta->soyisim;?></h4>
      </div>
      <div class="modal-body">
      
      <div class="form-group row">
<div class="col-sm-6"><label for="isim">Hastanın Adı:</label></div>
<div class="col-sm-6"><input id="isim" type="text" name="isim" class="form-control" value="<?php echo $hasta->isim;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="soyisim">Hastanın Soyadı:</label></div>
<div class="col-sm-6"><input type="text" id="soyisim" name="soyisim" class="form-control" value="<?php echo $hasta->soyisim;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">

<div class="col-sm-6"><label for="dogumtarihi">Doğum Tarihi:</label></div>  
<div class="col-sm-6 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" data-format="dd.dd.dddd" class="form-control bfh-phone" id="dogumtarihi" name="dogumtarihi" value="<?php echo $hasta->dtarihi;?>" autocomplete="off" required>
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>

</div>
      
<div class="form-group row">
<div class="col-sm-6"><label for="anneAdi">Anne Adı:</label></div>
<div class="col-sm-6"><input type="text" id="anneAdi" name="anneAdi" class="form-control" placeholder="Anne Adı" value="<?php echo $hasta->anneAdi;?>" autocomplete="off"></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="babaAdi">Baba Adı:</label></div>
<div class="col-sm-6"><input type="text" id="babaAdi" name="babaAdi" class="form-control" placeholder="Baba Adı" value="<?php echo $hasta->babaAdi;?>" autocomplete="off"></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="boy">Hastanın Boyu:</label></div>
<div class="col-sm-6"><input type="text" id="boy" name="boy" class="form-control bfh-phone" data-format="d.dd" placeholder="0.00" value="<?php echo $hasta->boy;?>" autocomplete="off"></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="kilo">Hastanın Kilosu:</label></div>
<div class="col-sm-6"><input type="text" id="kilo" name="kilo" class="form-control bfh-phone" data-format="ddd" placeholder="000" value="<?php echo $hasta->kilo;?>" autocomplete="off"></div>
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
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savetemel">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>">
</form>
<!-- kimlik bilgisi değiştirme modal -->
<!-- Adres değiştirme modal -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<div id="changeadres" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Adres Bilgileri: <?php echo $hasta->isim." ".$hasta->soyisim;?></h4>
      </div>
      <div class="modal-body">
      
      <div class="form-group row">
<div class="col-sm-6"><label for="ilceid">İlçe:</label></div>
<div class="col-sm-6"><?php echo $lists['ilce'];?></div>
</div>

<div class="form-group row"> 
<div class="col-sm-6"><label for="mahalleid">Mahalle:</label></div>
<div class="col-sm-6"><?php echo $lists['mahalle'];?></div>
</div>
                    
<div class="form-group row">                    
<div class="col-sm-6"><label for="sokakid">Cadde/Sokak:</label></div>
<div class="col-sm-6"><?php echo $lists['sokak'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="kapino">Kapı No:</label></div>
<div class="col-sm-6"><?php echo $lists['kapino'];?></div>
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
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="saveadres">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>">
</form>
<!-- Adres değiştirme modal -->

<!-- Telefon değiştirme modal -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<div id="changetelefon" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Telefon Bilgileri: <?php echo $hasta->isim." ".$hasta->soyisim;?></h4>
      </div>
      <div class="modal-body">
      
<div class="form-group row">
<div class="col-sm-6"><label for="telefon1">Telefon Numarası 1:</label></div>
<div class="col-sm-6"><input type="text" id="telefon1" name="ceptel1" class="form-control bfh-phone" data-format="d (ddd) ddd dd dd" placeholder="0 (000) 000 00 00" value="<?php echo $hasta->ceptel1;?>" autocomplete="off"></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="telefon2">Telefon Numarası 2:</label></div>
<div class="col-sm-6"><input type="text" id="telefon2" name="ceptel2" class="form-control bfh-phone" data-format="d (ddd) ddd dd dd" placeholder="0 (000) 000 00 00" value="<?php echo $hasta->ceptel2;?>" autocomplete="off"></div>
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
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="saveceptel">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>">
</form>
<!-- telefon değiştirme modal -->

<!-- barthel Modal -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<div id="barthel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Barthel Indeksi: <?php echo $hasta->isim." ".$hasta->soyisim;?></h4>
      </div>
        <table class="table table-bordered table-striped table-hover table-condensed table-responsive">
        <thead>
        <tr>
    <td width="20%"><strong>Aktivite</strong></td>
    <td><strong>Puanlama</strong></td>
        </tr>
        </thead>
        <tbody>
        <tr>
    <td width="20%"><strong>Beslenme</strong></td>
    <td><?php echo barthelBeslenme($hasta->barbeslenme);?></td>
        </tr>
  <tr>
    <td><strong>Banyo:</strong></td>
    <td><?php echo barthelBanyo($hasta->barbanyo);?></td>
  </tr>
    <tr>
    <td><strong>Kişisel Bakım:</strong></td>
    <td><?php echo barthelBakim($hasta->barbakim);?></td>
  </tr>
    <tr>
    <td><strong>Giyinme/Soyunma:</strong></td>
    <td><?php echo barthelGiyinme($hasta->bargiyinme);?></td>
  </tr>
    <tr>
    <td><strong>Barsak Bakımı:</strong></td>
    <td><?php echo barthelBarsak($hasta->barbarsak);?></td>
  </tr>
    <tr>
    <td><strong>Mesane Kontrolü:</strong></td>
    <td><?php echo barthelMesane($hasta->barmesane);?></td>
  </tr>
  <tr>
    <td><strong>Tuvalet Kullanımı:</strong></td>
    <td><?php echo barthelTuvalet($hasta->bartuvalet);?></td>
  </tr>
  <tr>
    <td><strong>Transfer:</strong></td>
    <td><?php echo barthelTransfer($hasta->bartransfer);?></td>
  </tr>
  <tr>
    <td><strong>Mobilite:</strong></td>
    <td><?php echo barthelMobilite($hasta->barmobilite);?></td>
  </tr>
  <tr>
    <td><strong>Merdiven:</strong></td>
    <td><?php echo barthelMerdiven($hasta->barmerdiven);?></td>
  </tr>
  </tbody>
</table>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div> <!-- modal content -->
  </div>
</div> <!-- modal -->
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savebarthel">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>"> 
</form>
<!-- barthel modal -->

<!-- bez değiştirme -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>        
<!-- Modal -->
<div id="bez" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Alt Bezi Bilgileri: <?php echo $hasta->isim.' '.$hasta->soyisim;?></h4>
      </div>
        <div class="modal-body">
        <div class="form-group row">
        <div class="col-sm-5"><label>Alt Bezi Kullanımı:</label></div>
        <div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('bez', '', $hasta->bez);?></div>
        </div>
        
<div id="bezcheck" style="display:none">

<div class="form-group row">
<div class="col-sm-5"><label>Bez Raporu Var mı?:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('bezrapor', '', $hasta->bezrapor);?></div>
</div>
 
<div id="bezraporcheck" style="display:none"> <!-- bezraporcheck -->      
<div class="form-group row">
<div class="col-sm-5"><label for="bezraporbitis">Bez Raporu Bitiş Tarihi:</label></div>

<div class='col-sm-6 input-group date' id='bezraporbitis1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bezraporbitis" name="bezraporbitis" value="<?php echo $hasta->bezraporbitis ? tarihCevir($hasta->bezraporbitis, 1) : '';?>" autocomplete="off" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>

</div><!-- bezraporcheck -->

</div><!-- bezcheck -->
</div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div> <!-- modal content -->
  </div>
</div> <!-- modal -->
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savebez">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>"> 
</form>
<!-- bez değiştirme -->

<!-- mama değiştirme -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>        
<!-- Modal -->
<div id="mama" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Mama Bilgileri: <?php echo $hasta->isim.' '.$hasta->soyisim;?></h4>
      </div>
        <div class="modal-body">
        <div class="form-group row">
        <div class="col-sm-5"><label>Mama Kullanımı:</label></div>
        <div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('mama', '', $hasta->mama);?></div>
        </div>
        
<div id="mamacheck" style="display:none">

<div class="form-group row">
<div class="col-sm-5"><label>Kullanılan Mama Markası:</label></div>
<div class="col-sm-6"><?php  echo $lists['mamacesit'];?></div>
</div>
      
<div class="form-group row">
<div class="col-sm-5"><label for="pasiftarihi">Mama Rapor Bitiş Tarihi:</label></div>

<div class='col-sm-6 input-group date' id='mamaraporbitis1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="mamaraporbitis" name="mamaraporbitis" value="<?php echo $hasta->mamaraporbitis ? tarihCevir($hasta->mamaraporbitis, 1) : '';?>" autocomplete="off" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>

<div class="form-group row">
<div class="col-sm-5"><label>Rapor DDH mı yazılmış?:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('mamaraporyeri', '', $hasta->mamaraporyeri);?></div>
</div>

</div><!-- mamacheck -->
</div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div> <!-- modal content -->
  </div>
</div> <!-- modal -->
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savemama">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>"> 
</form>
<!-- mama değiştirme -->

<!-- sonda değiştirme modal -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>        
<div id="sonda" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Sonda Bilgileri: <?php echo $hasta->isim.' '.$hasta->soyisim;?></h4>
      </div>
        <table class="table table-striped">
        <tr>
        <td><label>Sonda Takılı:</label></td>
        <td><?php echo mosHTML::yesnoRadioList('sonda', '', $hasta->sonda);?></td>
        </tr>
        <tr id="sondacheck" style="display:none">  <!-- sondacheck --> 
        <td><label for="sondatarihi">Sonda Takılma Tarihi:</label></td>
        <td class='input-group date' id='sondatarihi1' data-date-format="dd.mm.yyyy">
        <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="sondatarihi" name="sondatarihi" value="<?php echo $hasta->sondatarihi ? tarihCevir($hasta->sondatarihi, 1) : '';?>" autocomplete="off" />
        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
        </td>
        </tr> <!-- sondacheck -->
        </table>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div> <!-- modal content -->
  </div>
</div> <!-- modal -->
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savesonda">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>"> 
</form>
<!-- sonda değiştirme modal -->

<!-- pasif bilgileri değiştirme -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>        
<!-- Modal -->
<div id="hastapasif" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Hasta Bilgileri: <?php echo $hasta->isim.' '.$hasta->soyisim;?></h4>
      </div>
        <div class="modal-body">
        <div class="form-group row">
        <div class="col-sm-5"><label>Hasta Dosyası Pasif:</label></div>
        <div class="col-sm-6"><?php  echo $lists['pasif'];?></div>
        </div>
<div id="pasifcheck" style="display:none">
      
<div class="form-group row">
<div class="col-sm-5"><label for="pasiftarihi">Pasif Edilme Tarihi:</label></div>

<div class='col-sm-6 input-group date' id='pasiftarihi1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="pasiftarihi" name="pasiftarihi" value="<?php echo $hasta->pasiftarihi ? tarihCevir($hasta->pasiftarihi, 1) : '';?>" autocomplete="off" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>
<div class="form-group row">
<div class="col-sm-5"><label>Pasif Edilme Nedeni:</label></div>
<div class="col-sm-6"><?php  echo $lists['pasifnedeni'];?></div>
</div>
</div><!-- pasifcheck -->
</div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div> <!-- modal content -->
  </div>
</div> <!-- modal -->
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savepasif">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>"> 
</form>
<!-- pasif bilgileri değiştirme -->

<!-- pansuman listesi işlemleri -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>        
<!-- Modal -->
<div id="pansuman" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Pansuman Günleri: <?php echo $hasta->isim.' '.$hasta->soyisim;?></h4>
      </div>
        
        <table class="table table-striped">
        <tr>
        <td><label>Pansuman Takibinde:</label></td>
        <td><?php echo mosHTML::yesnoRadioList('pansuman', '', $hasta->pansuman);?></td>
        </tr>
        <tr id="pansumancheck" style="display:none">  <!-- pansumancheck --> 
        <td><label for="pansumangunleri">Pansuman Günleri:</label></td>
        <td><?php echo $lists['days'];?></td>
        </tr> <!-- pansumancheck -->
        </table>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
      
    </div> <!-- modal content -->

  </div>
</div> <!-- modal -->
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savepansuman">
<input type="hidden" name="id" value="<?php echo $hasta->id;?>"> 
</form>
<script>                                  
$(document).ready(function(){
    
    if($("#pansuman1").is(':checked')){
            $("#pansumancheck").show();
        }else{
            $("#pansumancheck").hide();
        }
        
     $("input[name=pansuman]").change(function(){

        if($("#pansuman1").is(':checked')){
            $("#pansumancheck").show();
        }else{
            $("#pansumancheck").hide();
        }
    });
    
        $("#ilce").on("change", function(){

            $("#mahalle").html("<option value=''>Bir Mahalle Seçin</option>");
            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val()); 
            
            ajaxFunc("mahalle", $(this).val(), "#mahalle");

        });

        $("#mahalle").on("change", function(){

            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("sokak", $(this).val(), "#sokak");

        });
        
        $("#sokak").on("change", function(){

            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("kapino", $(this).val(), "#kapino");

        });
        
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=site&bolum=hastalar",
                type: "POST",
                data: {task:task, id:id},
                success: function(sonuc){
                    console.log(sonuc);
                    $(name).append(sonuc);
                }});
        }

$('#dogumtarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr",
        orientation: "bottom"
    });
        
$('#sondatarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr",
        orientation: "bottom"
    });
    
        $('#pasiftarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr",
        orientation: "bottom"
    });
    
    $('#mamaraporbitis').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr",
        orientation: "bottom"
    });
    
     $('#bezraporbitis').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr",
        orientation: "bottom"
    });
    
    if($("#bez1").is(':checked')){
            $("#bezcheck").show();
        }else{
            $("#bezcheck").hide();
        }
        
     $("input[name=bez]").change(function(){

        if($("#bez1").is(':checked')){
            $("#bezcheck").show();
        }else{
            $("#bezcheck").hide();
        }
    });
    
    if($("#bezrapor1").is(':checked')){
            $("#bezraporcheck").show();
        }else{
            $("#bezraporcheck").hide();
        }
        
     $("input[name=bezrapor]").change(function(){

        if($("#bezrapor1").is(':checked')){
            $("#bezraporcheck").show();
        }else{
            $("#bezraporcheck").hide();
        }
    });
    
    if($("#mama1").is(':checked')){
            $("#mamacheck").show();
        }else{
            $("#mamacheck").hide();
        }
        
     $("input[name=mama]").change(function(){

        if($("#mama1").is(':checked')){
            $("#mamacheck").show();
        }else{
            $("#mamacheck").hide();
        }
    });
    
    if($("#sonda1").is(':checked')){
            $("#sondacheck").show();
        }else{
            $("#sondacheck").hide();
        }
        
     $("input[name=sonda]").change(function(){

        if($("#sonda1").is(':checked')){
            $("#sondacheck").show();
        }else{
            $("#sondacheck").hide();
        }
    });
    
        if($("#pasif1").is(':checked')){
            $("#pasifcheck").show();
        }else{
            $("#pasifcheck").hide();
        }
        
     $("input[name=pasif]").change(function(){

        if($("#pasif1").is(':checked')){
            $("#pasifcheck").show();
        }else{
            $("#pasifcheck").hide();
        }
    });    
});
</script>      
    <?php
    } 
    
    function editHasta($row, $lists, $limitstart, $limit, $hcats) {
    ?>
    <form action="index.php" method="post" data-toggle="validator" novalidate>
    <div class="panel panel-<?php echo $row->pasif ? 'warning':'primary';?>"><!-- main panel -->
    
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-9"><h4><i class="fa-solid fa-square-plus"></i> <?php echo $row->id ? 'Düzenle: '.$row->isim.' '.$row->soyisim : 'Yeni Hasta Kayıt';?></div>
    <div class="col-xs-3" align="right"><h4><?php echo $row->pasif ? '<i class="fa-solid fa-triangle-exclamation"></i> DOSYA KAPALI ('.tarihCevir($row->pasiftarihi, 1).')':'';?></h4></div>
    </div>
    </div>
    
    <div class="panel-body"><!-- main panel-body -->
    
     
    <div class="row"><!-- row 1 -->
    
    <div class="col-sm-6">
    
    <div class="panel panel-<?php echo $row->pasif ? 'warning':'primary';?>">
    <div class="panel-heading"><h5><i class="fa-regular fa-user"></i> Temel Bilgileri</h5></div>
    <div class="panel-body"><!-- temel bilgiler-->
    
<div class="form-group row">
<div class="col-sm-6"><label for="tckimlik">TC Kimlik Numarası:</label></div>
<div class="col-sm-6"><input type="text" data-minlength="11" maxlength="11" id="tckimlik" name="tckimlik" class="form-control  bfh-phone" data-format="ddddddddddd" value="<?php echo $row->tckimlik;?>" autocomplete="off" required>
<?php if (!$row->id) { ?>
<span id="sonuc"></span>
<?php } ?>
</div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="isim">Hastanın Adı:</label></div>
<div class="col-sm-6"><input id="isim" type="text" name="isim" class="form-control" value="<?php echo $row->isim;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="soyisim">Hastanın Soyadı:</label></div>
<div class="col-sm-6"><input type="text" id="soyisim" name="soyisim" class="form-control" value="<?php echo $row->soyisim;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="anneAdi">Anne Adı:</label></div>
<div class="col-sm-6"><input id="anneAdi" type="text" name="anneAdi" class="form-control" value="<?php echo $row->anneAdi;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="babaAdi">Baba Adı:</label></div>
<div class="col-sm-6"><input id="babaAdi" type="text" name="babaAdi" class="form-control" value="<?php echo $row->babaAdi;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">

<div class="col-sm-6"><label for="dogumtarihi">Doğum Tarihi:</label></div>  
<div class="col-sm-6 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" data-format="dd.dd.dddd" class="form-control bfh-phone" id="dogumtarihi" name="dogumtarihi" value="<?php echo $row->dogumtarihi;?>" autocomplete="off" required>
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>

</div>

<div class="form-group row">
<div class="col-sm-6"><label>Cinsiyeti:</label></div>
<div class="col-sm-6"><?php  echo $row->hastaCinsiyet(1); ?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="boy">Hastanın Boyu:</label></div>
<div class="col-sm-6"><input type="text" id="boy" name="boy" class="form-control bfh-phone" data-format="d.dd" placeholder="0.00" value="<?php echo $row->boy;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="kilo">Hastanın Kilosu:</label></div>
<div class="col-sm-6"><input type="text" id="kilo" name="kilo" class="form-control bfh-phone" data-format="ddd" placeholder="000" value="<?php echo $row->kilo;?>" autocomplete="off" required></div>
</div>


<div class="form-group row">
<div class="col-sm-6"><label for="kayityili">Kayıt Yılı:</label></div>
<div class="col-sm-6"><?php  echo $row->hastaKayitYili(1);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="kayitay">Kayıt Ayı:</label></div>
<div class="col-sm-6"><?php $kayitay = $row->kayitay ? $row->kayitay : date('m');  echo mosHTML::monthSelectList('kayitay', 'id="kayitay" required', $kayitay, 1);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="telefon1">Telefon Numarası 1:</label></div>
<div class="col-sm-6"><input type="text" id="telefon1" name="ceptel1" class="form-control bfh-phone" data-format="d (ddd) ddd dd dd" placeholder="0 (000) 000 00 00" value="<?php echo $row->ceptel1;?>" autocomplete="off" required></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="telefon2">Telefon Numarası 2:</label></div>
<div class="col-sm-6"><input type="text" id="telefon2" name="ceptel2" class="form-control bfh-phone" data-format="d (ddd) ddd dd dd" placeholder="0 (000) 000 00 00" value="<?php echo $row->ceptel2;?>" autocomplete="off"></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="ilce">İlçe:</label></div>
<div class="col-sm-6"><?php echo $lists['ilce'];?></div>
</div>

<div class="form-group row"> 
<div class="col-sm-6"><label for="mahalle">Mahalle:</label></div>
<div class="col-sm-6"><?php echo $lists['mahalle'];?></div>
</div>

<?php 
if ($row->id) { ?>                    
<div class="form-group row">                    
<div class="col-sm-5"><label for="sokak">Cadde/Sokak:</label></div>
<div class="col-sm-1"><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#sokakEkle">Ekle</button></div> 
<div class="col-sm-6"><?php echo $lists['sokak'];?></div>
</div>

<div class="form-group row">                    
<div class="col-sm-5"><label for="kapino">Kapı No:</label></div>
<div class="col-sm-1"><button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#kapinoEkle">Ekle</button></div> 
<div class="col-sm-6"><?php echo $lists['kapino'];?></div>
</div>
<?php } else { ?>
<div class="form-group row">                    
<div class="col-sm-6"><label for="sokak">Cadde/Sokak:</label></div>
<div class="col-sm-6"><?php echo $lists['sokak'];?></div>
</div>

<div class="form-group row">                    
<div class="col-sm-6"><label for="kapino">Kapı No:</label></div>
<div class="col-sm-6"><?php echo $lists['kapino'];?></div>
</div>
<?php } ?>
    </div><!-- body temel bilgiler -->
    
    </div><!-- panel-primary-->
    
    <div class="panel panel-<?php echo $row->pasif ? 'warning':'primary';?>">
    <div class="panel-heading"><h5><i class="fa-regular fa-user"></i> Özellikli Durumları</h5></div>
    <div class="panel-body"><!-- özellikli durumları-->
    
    <div class="form-group row">
<div class="col-sm-6"><label>Hastanın Bağımlılık Durumu:</label></div>
<div class="col-sm-6"><?php  echo $row->hastaBagimlilik($row->id);?></div>
</div>

    <div class="form-group row">
<div class="col-sm-6"><label>Nazogastrik Takılı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('ng', '', $row->ng);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label>PEG Takılı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('peg', '', $row->peg);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label>Port Takılı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('port', '', $row->port);?></div>
</div>
 
<div class="form-group row">
<div class="col-sm-6"><label>Oksijen Bağımlı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('o2bagimli', '', $row->o2bagimli);?></div>
</div>
 
<div class="form-group row">
<div class="col-sm-6"><label>Ventilatöre Bağlı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('ventilator', '', $row->ventilator);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label>Kolostomili:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('kolostomi', '', $row->kolostomi);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label>Hasta Geçici Takipli:</label></div>
<div class="col-sm-6"><?php  echo $row->hastaGecici($row->id);?></div>
</div>

    </div><!-- özellikli durumları body-->
    </div><!-- özellikli durumları panel-->
    
    </div><!-- col-sm-6-->
    
    <div class="col-sm-6">
    
    <div class="panel panel-<?php echo $row->pasif ? 'warning':'primary';?>">
    <div class="panel-heading"><h5><i class="fa-solid fa-bacterium"></i> Hastalıkları</h5></div>
    <div class="panel-body"><!-- ek özellikler-->
<div class="panel panel-primary">
<span class="side-tab" data-target="#tab0" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="heading0" data-toggle="collapse" data-parent="#accordion" href="#collapse0" aria-expanded="false" aria-controls="collapse0">
<h5 class="collapsed"><strong><?php echo $row->id ? 'Hastanın Hastalıkları':'Sık Kullanılanlar';?></strong></h5>
</div>
</span>
                    
<div id="collapse0" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading0">
<div class="panel-body">
<?php
echo $lists['hastalik']['sik'];
?>
</div>
</div>
</div>    
    
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">

<?php 
foreach ($hcats as $hcat) {
?>
<div class="panel panel-primary">
<span class="side-tab" data-target="#tab<?php echo $hcat->id;?>" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="heading<?php echo $hcat->id;?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $hcat->id;?>" aria-expanded="false" aria-controls="collapse<?php echo $hcat->id;?>">
<h5 class="collapsed"><?php echo $hcat->name;?></h5>
</div>
</span>
                    
<div id="collapse<?php echo $hcat->id;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $hcat->id;?>">
<div class="panel-body">
<?php
echo $lists['hastalik'][$hcat->id];
?>
</div>
</div>
</div>
<?php
}
?> 
</div> <!-- accordion -->
    </div><!-- body hastalıkları -->
    </div><!-- panel-success --> 
    
    <div class="panel panel-<?php echo $row->pasif ? 'warning':'primary';?>">
    <div class="panel-heading"><h5><i class="fa-solid fa-user"></i> Diğer Özellikler</h5></div>
    <div class="panel-body">
    
    <div class="form-group row">
<div class="col-sm-6"><label>Sonda Takılı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('sonda', '', $row->sonda);?></div>
</div>

<div id="sondacheck" style="display:none">
<div class="form-group row">
<div class="col-sm-6"><label for="sondatarihi">En Son Sonda Takılma Tarihi:</label></div>
<div class='col-sm-6 input-group date' id='sondatarihi1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="sondatarihi" name="sondatarihi" autocomplete="off" value="<?php echo $row->sondatarihi ? tarihCevir($row->sondatarihi, 1) : '';?>" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>

</div>

</div> <!-- sondacheck -->

<div class="form-group row">
<div class="col-sm-6"><label>Alt Bezi Kullanımı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('bez', '', $row->bez);?></div>
</div>

<div id="bezcheck" style="display:none">

<div class="form-group row">
<div class="col-sm-6"><label>Bez Raporu Var mı?:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('bezrapor', '', $row->bezrapor);?></div>
</div>
 
<div id="bezraporcheck" style="display:none"> <!-- bezraporcheck -->      
<div class="form-group row">
<div class="col-sm-6"><label for="bezraporbitis">Bez Raporu Bitiş Tarihi:</label></div>

<div class='col-sm-6 input-group date' id='bezraporbitis1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bezraporbitis" name="bezraporbitis" value="<?php echo $row->bezraporbitis ? tarihCevir($row->bezraporbitis, 1) : '';?>" autocomplete="off" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>

</div><!-- bezraporcheck -->

</div><!-- bezcheck -->



<div class="form-group row">
<div class="col-sm-6"><label>Mama Kullanımı:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('mama', '', $row->mama);?></div>
</div>

<div id="mamacheck" style="display:none">

<div class="form-group row">
<div class="col-sm-6"><label for="mamacesidi">Kullandığı Mama Markası:</label></div>
<div class="col-sm-6" id="mamacesidi">
<?php  echo $row->hastaMama($row->id);?>
</div>

</div>

<div class="form-group row">
<div class="col-sm-6"><label for="pasiftarihi">Mama Rapor Bitiş Tarihi:</label></div>

<div class='col-sm-6 input-group date' id='mamaraporbitis' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="mamaraporbitis" name="mamaraporbitis" autocomplete="off" value="<?php echo $row->mamaraporbitis ? tarihCevir($row->mamaraporbitis, 1) : '';?>" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>

</div>

<div class="form-group row">
<div class="col-sm-6"><label>Mama Raporu DDH mı yazmış:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('mamaraporyeri', '', $row->mamaraporyeri);?></div>
</div>

</div> <!-- mamacheck -->

<div class="form-group row">
<div class="col-sm-6"><label>Hasta Yatağı Var mı?:</label></div>
<div class="col-sm-6"><?php echo mosHTML::yesnoRadioList('yatak', '', $row->yatak);?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label>Hasta Dosyası Pasif:</label></div>
<div class="col-sm-6"><?php  echo $row->hastaPasif(1);?></div>
</div>

<div id="pasifcheck" style="display:none">

<div class="form-group row">
<div class="col-sm-6"><label for="pasiftarihi">Pasif Edilme Tarihi:</label></div>

<div class='col-sm-6 input-group date' id='pasiftarihi1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="pasiftarihi" name="pasiftarihi" autocomplete="off" value="<?php echo $row->pasiftarihi ? tarihCevir($row->pasiftarihi, 1) : '';?>" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>

</div>


<div class="form-group row">
<div class="col-sm-6"><label>Pasif Edilme Nedeni:</label></div>
<div class="col-sm-6"><?php  echo $row->hastaPasifNedeni();?></div>
</div>

</div><!-- pasifcheck -->
    
    </div>
    </div>
       
    </div><!-- col-sm-6 -->

    
    
    </div><!-- row 1-->

    
<div class="form-group row">
<div class="col-sm-7">
<button type="submit" id="save" class="btn btn-primary">Kaydet</button>
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
</div>
</div>
    
    </div><!-- main panel-body -->
    </div><!-- main panel --> 
    <input type="hidden" name="option" value="site" />
    <input type="hidden" name="bolum" value="hastalar" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="id" value="<?php echo $row->id;?>" /> 
    </form> 
        <script>                                  
$(document).ready(function(){
    
        $("#ilce").on("change", function(){

            $("#mahalle").html("<option value=''>Bir Mahalle Seçin</option>");
            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val()); 
            
            ajaxFunc("mahalle", $(this).val(), "#mahalle");

        });

        $("#mahalle").on("change", function(){

            $("#sokak").html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("sokak", $(this).val(), "#sokak");

        });
        
        $("#sokak").on("change", function(){

            $("#kapino").html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("kapino", $(this).val(), "#kapino");

        });
        
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=site&bolum=hastalar",
                type: "POST",
                data: {task:task, id:id},
                success: function(sonuc){
                    console.log(sonuc);
                    $(name).append(sonuc);
                }});
        }
    });

        $(document).ready(function(){        
  <?php if (!$row->id) { ?>        
    $('#tckimlik').keyup(function(){
    var val = $('#tckimlik').val(); 
    var uzunluk = val.length;
    
    if (uzunluk==11) {
            $.ajax({
                url:'index2.php?&option=site&bolum=hastalar&task=control&tc='+val,
                type:'GET',
                success:function(result){
                    $('#sonuc').html(result);
                }  
            });
    } else if (uzunluk < 11) {
        $('#sonuc').html("<strong>Kimlik numarası eksik yazılmış</strong>");   
    } else {
                    $('#sonuc').empty();
    }    
});
<?php } ?>  
    });

    $('#dogumtarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
    }); 
    
    $('#pasiftarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    
    $('#mamaraporbitis').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    
    $('#sondatarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    
    $('#bezraporbitis').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr",
        orientation: "bottom"
    });
    
    if($("#bez1").is(':checked')){
            $("#bezcheck").show();
        }else{
            $("#bezcheck").hide();
        }
        
     $("input[name=bez]").change(function(){

        if($("#bez1").is(':checked')){
            $("#bezcheck").show();
        }else{
            $("#bezcheck").hide();
        }
    });
    
    if($("#bezrapor1").is(':checked')){
            $("#bezraporcheck").show();
        }else{
            $("#bezraporcheck").hide();
        }
        
     $("input[name=bezrapor]").change(function(){

        if($("#bezrapor1").is(':checked')){
            $("#bezraporcheck").show();
        }else{
            $("#bezraporcheck").hide();
        }
    });
    
    $(document).ready(function () {
        
        if($("#pasif1").is(':checked')){
            $("#pasifcheck").show();
        }else{
            $("#pasifcheck").hide();
        }
        
     $("input[name=pasif]").change(function(){

        if($("#pasif1").is(':checked')){
            $("#pasifcheck").show();
        }else{
            $("#pasifcheck").hide();
        }
    });
    
    if($("#sonda1").is(':checked')){
            $("#sondacheck").show();
        }else{
            $("#sondacheck").hide();
        }
        
     $("input[name=sonda]").change(function(){

        if($("#sonda1").is(':checked')){
            $("#sondacheck").show();
        }else{
            $("#sondacheck").hide();
        }
    });
    
    if($("#mama1").is(':checked')){
            $("#mamacheck").show();
        }else{
            $("#mamacheck").hide();
        }
        
     $("input[name=mama]").change(function(){

        if($("#mama1").is(':checked')){
            $("#mamacheck").show();
        }else{
            $("#mamacheck").hide();
        }
    });
    });
    </script>
        
<!-- Sokak Ekle -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<div id="sokakEkle" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Yeni Sokak Ekle</h4>
      </div>
      <div class="modal-body">
<div class="form-group row">
<div class="col-sm-6"><label for="ilceid">İlçe:</label></div>
<div class="col-sm-6"><?php echo $lists['ilceid'];?></div>
</div>

<div class="form-group row"> 
<div class="col-sm-6"><label for="mahalleid">Mahalle:</label></div>
<div class="col-sm-6"><?php echo $lists['mahalleid'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="sokakadi">Cadde/Sokak:</label></div>
<div class="col-sm-6"><input type="text" id="sokakadi" name="sokakadi" class="form-control" value="" required></div>
</div>

    </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div>

  </div>
</div>
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savesokak">
<input type="hidden" name="uid" value="<?php echo $row->id;?>"> 
</form>
<!-- Sokak ekle-->  

<!-- Kapı No Ekle -->
<form action="index.php" data-toggle="validator" method="post" role="form" novalidate>
<div id="kapinoEkle" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Yeni Kapı No Ekle</h4>
      </div>
      <div class="modal-body">
      
      <div class="form-group row">
<div class="col-sm-6"><label for="ilceid">İlçe:</label></div>
<div class="col-sm-6"><?php echo $lists['ilceid'];?></div>
</div>

<div class="form-group row"> 
<div class="col-sm-6"><label for="mahalleid">Mahalle:</label></div>
<div class="col-sm-6"><?php echo $lists['mahalleid'];?></div>
</div>
                    
<div class="form-group row">                    
<div class="col-sm-6"><label for="sokakid">Cadde/Sokak:</label></div>
<div class="col-sm-6"><?php echo $lists['sokakid'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-6"><label for="kapino">Kapı No:</label></div>
<div class="col-sm-6"><input type="text" id="kapino" name="kapino" class="form-control" value="" required></div>
</div>
      
      
      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Kaydet</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div>

  </div>
</div>
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="hastalar">
<input type="hidden" name="task" value="savekapino">
<input type="hidden" name="uid" value="<?php echo $row->id;?>">
</form>
<!-- Kapı No ekle-->
<?php
}
    
    function getHastaList($rows, $pageNav, $search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ozellik, $ordering) {
        $link = 'index.php?option=site&bolum=hastalar';
        if ($search) {
            $link .= "&amp;search=".$search;
        }
        if ($ilce) {
            $link .= "&amp;ilce=".$ilce;
        }
        if ($mahalle) {
            $link .= "&amp;mahalle=".$mahalle;
        }
        if ($sokak) {
            $link .= "&amp;sokak=".$sokak;
        }
        if ($kapino) {
            $link .= "&amp;kapino=".$kapino;
        }
        if ($kayityili) {
            $link .= "&amp;kayityili=".$kayityili;
        }
        if ($kayitay) {
            $link .= "&amp;kayitay=".$kayitay;
        }
        if ($cinsiyet) {
            $link .= "&amp;cinsiyet=".$cinsiyet;
        }
        if ($bagimlilik) {
            $link .= "&amp;bagimlilik=".$bagimlilik;
        }
        if ($ozellik) {
            $link .= "&amp;ozellik=".$ozellik;
        }
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
        
    ?>
<form action="index.php" method="GET" name="adminForm" role="form">
<input type="hidden" name="option" value="site" />
<input type="hidden" name="bolum" value="hastalar" />
<input type="hidden" name="task" value="list" />  
    
<div class="panel panel-primary">
<div class="panel-heading">
<div class="row">
    <div class="col-xs-2"><h4><i class="fa-solid fa-person-cane"></i> Aktif Hasta Listesi</h4></div>
    <div class="col-xs-5">
    </div>
    <div class="col-xs-4">
    <div class="input-group">
        <input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control"  onChange="document.adminForm.submit();" placeholder="TC Kimlik Numarası yada Bir isim Yazın" autocomplete="off">
        <div class="input-group-btn">
        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </div>
    </div>
    </div>
    <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
</div>
</div>
    <table class="table table-hover" id="datatablesSimple">
    <thead class="thead-dark">
    <tr>
    <th scope="col">
      <div>
     <a href="#" data-toggle="tooltip" title="Toplam İzlem Sayısı">#</a> 
    </div>  
    </th>   
      <th scope="col">
      <div>
      Hasta Adı   
  <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col"><div>
      TC Kimlik
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col"> <div>
      Mahalle
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
    </div>  
    </th>
    <th scope="col">Anne Adı
    <span><a href="<?php echo $link;?>&ordering=h.anneAdi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.anneAdi-ASC">▼</a></span>
    </th>
      <th scope="col">Baba Adı
      <span><a href="<?php echo $link;?>&ordering=h.babaAdi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.babaAdi-ASC">▼</a></span>
      </th>
      <th scope="col"><div>
      Kayıt Yılı
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col"><div>
      D. Tarihi
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col">Yaşı</th> 
      <th scope="col">Cinsiyet
      <span><a href="<?php echo $link;?>&ordering=h.cinsiyet-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.cinsiyet-ASC">▼</a></span>
      </th>
      <th scope="col">Telefon</th>
      <th scope="col">Son İzlem</th>
      </tr>
  </thead>
  <tbody>

  <?php 
   foreach($rows as $row) {
       $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
       
       //son izlem tarihi
       $row->sonizlemtarihi = $row->sonizlemtarihi ? tarihCevir($row->sonizlemtarihi, 1):'Yok';
         
       $link2 = "index.php?option=site&bolum=hastalar&task=edit&id=".$row->id;
       if ($pageNav->limit) {
           $link2 .= "&limit=".$pageNav->limit;
       }
       if ($pageNav->limitstart) {
           $link2 .= "&limitstart=".$pageNav->limitstart;
       }
       $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
      ?>
      
      <tr class="">
      <td scope="row"><span data-toggle="tooltip" title="<?php echo $row->izlemsayisi;?> İzlem" class="label label-<?php echo $row->izlemsayisi ? 'info':'warning';?>"><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->izlemsayisi;?></a></span></td>
      <th scope="row">
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><span style="color:<?php echo $row->cinsiyet == 'E' ? 'blue':'#f5070f';?>"><?php echo $row->isim;?> <?php echo $row->soyisim;?></span></a>  <?php echo $row->gecici ? '<sub>(G)</sub>':'';?>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemlerini Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
      </th>
      <td><?php echo $row->tckimlik;?></td>
      <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
      <td><?php echo $row->anneAdi;?></td>
      <td><?php echo $row->babaAdi;?></td>
      <td><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td>
      <td><?php echo $row->dtarihi;?></td>
      <td><?php echo yas_bul($row->dogumtarihi);?></td> 
      <td><?php echo $row->cinsiyet == "E" ? "Erkek":"Kadın";?></td>
      <td><?php echo $row->ceptel1;?></td>
      <td><?php echo $row->sonizlemtarihi;?></td>
      
      </tr>
 <?php 

  }?>
  
    </tbody>
</table>
<!--
<script src="<?php echo SITEURL;?>/site/modules/hastalar/datatables-simple.js" type="text/javascript"></script>
<script>
new DataTable('#datatablesSimple', {
    fixedHeader: true,
    layout: {
        topStart: {
            buttons: [
                'copy', 'excel', 'pdf'
            ]
        }
    }
    
});
</script>
<script>
    $(document).ready(function () {
        $("#btnExport").click(function () {
            let table = document.getElementsByTagName("table");
            console.log(table);
            debugger;
            TableToExcel.convert(table[0], {
                name: `Hastalar.xlsx`,
                sheet: {
                    name: 'Hastalar'
                }
            });
        });
    });
</script>
-->
<div class="panel-footer">
<div align="center">
<div class="pagenav_counter">
<?php echo $pageNav->writePagesCounter();?>
</div>
<div class="pagenav_links">
<?php 
echo $pageNav->writePagesLinks($link);
?>
</div>
</div>
</div>

</div> <!-- panel-default -->      
</form>
<?php    
    }    

}
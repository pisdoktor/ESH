<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class HastaList {
    
    function hastaGoster($hasta) {
        //dogum tarihi düzelt
        $tarih = explode('.',$hasta->dogumtarihi);
        $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
        $hasta->dtarihi = strftime("%d.%m.%Y", $tarih);
         //bağımlılık durumu seç
        $bagimli = array('0' => 'Bağımsız', '1' => 'Yarı Bağımlı', '2' => 'Tam Bağımlı');
         // kayıt ayını seç
        $aylar = array('01' => 'Ocak', '02' => 'Şubat', '03' => 'Mart', '04' => 'Nisan', '05' => 'Mayıs', '06' => 'Haziran', '07' => 'Temmuz', '08' => 'Ağustos', '09' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık');
         // hastalıkları
        $hastaliklar = array(
'parkinson' => 'Parkinson',
'alzheimer' => 'Alzheimer',
'demans' => 'Demans',
'senilite' => 'Senilite(Yaşlılık)',
'epilepsi' => 'Epilepsi',
'hidrosefali' => 'Hidrosefali',
'ensefalopati' => 'Ensefalopati',
'svo' => 'SVO',
'sspe' => 'SSPE',
'miyelit' => 'Miyelit',
'als' => 'ALS',
'motornoron' => 'Motor Nöron Hastalığı',
'multiplskleroz' => 'Multipl Skleroz',
'noropatikagri' => 'Nöropatik Ağrı',
'kuadripleji' => 'Kuadripleji',
'hemipleji' => 'Hemipleji',
'parapleji' => 'Parapleji',
'tetrapleji' => 'Tetrapleji',
'mentalretardasyon' => 'Mental Retardasyon',
'poliosekel' => 'Polio Sekeli',
'depresyon' => 'Depresyon',
'psikoz' => 'Psikoz',
'bipolar' => 'Bipolar Bozukluk',
'sizofren' => 'Şizofreni',
'serebralpalsi' => 'Serebral Palsi',
'kasatrofisi' => 'Kas Atrofisi',
'ataksi' => 'Ataksi',
'muskulerdistrofi' => 'Müsküler Distrofi',
'spastikveflask' => 'Spastik ve Flask Tip Özürlü',
'aritmi' => 'Aritmi',
'ht' => 'Hipertansiyon',
'kalpkapak' => 'Kalp Kapak Hastalıkları',
'kalpyet' => 'Kalp Yetmezliği',
'koronerarter' => 'Koroner Arter Hastalığı',
'venozdol' => 'Venöz Dolaşım Bozukluğu',
'dm' => 'Diyabet',
'hiperlipidemi' => 'Hiperlipidemi',
'hipertiroidi' => 'Hipertiroidi',
'hipotiroidi' => 'Hipotiroidi',
'obezite' => 'Obezite',
'diyaliz' => 'Diyaliz Hastası',
'bobrekyet' => 'Böbrek Yetmezliği',
'siroz' => 'Karaciğer Sirozu',
'anemi' => 'Anemi',
'itp' => 'ITP',
'ciltkanser' => 'Cilt Kanseri',
'memekanser' => 'Meme Kanseri',
'farinkskanser' => 'Farinks Kanseri',
'trakeakanser' => 'Trakea Kanseri',
'akckanseri' => 'Akciğer Kanseri',
'ozofaguskanser' => 'Özofagus Kanseri',
'midekanser' => 'Mide Kanseri',
'barsakkanser' => 'Bağırsak Kanseri',
'karacigerkanser' => 'Karaciğer Kanseri',
'pankreaskanser' => 'Pankreas Kanseri',
'safrakanser' => 'Safra Kesesi Kanseri',
'prostatkanser' => 'Prostat Kanseri',
'tiroidkanser' => 'Tiroid Kanseri',
'hipofizkanser' => 'Hipofiz Kanseri',
'bobrekkanser' => 'Böbrek Kanseri',
'overkanser' => 'Over Kanseri',
'uteruskanser' => 'Uterus Kanseri',
'kemikkanser' => 'Kemik Kanseri',
'beyinkanser' => 'Beyin Kanseri',
'multiplmiyelom' => 'Multipl Miyelom',
'koah' => 'KOAH',
'astim' => 'Astım',
'tuberkuloz' => 'Tüberküloz',
'kistikfibroz' => 'Kistik Fibrozis',
'dekubitulseri' => 'Dekübitus Ülseri',
'diskopati' => 'Diskopati',
'extremiteamp' => 'Ekstremitelerde Ampütasyon',
'kalcafraktur' => 'Kalça Fraktürü',
'operekirik' => 'Opere Edilmiş veya mobilizasyon kısıtlayıcı kırıklar',
'osteoporoz' => 'Osteoporoz',
'omurgafraktur' => 'Omurga Fraktürü',
'ebullosa' => 'Epidermolizis Büllosa',
'zona' => 'Zona',
'fil' => 'Fil Hastalığı',
'sle' => 'Sistemik Lupus Eritomatozus',
'bph' => 'Benign Prostat Hipertrofisi'
);


$list = array();
foreach ($hastaliklar as $v=>$k) {
if ($hasta->$v == 1) {
$list[] = $k;
}
}
?>
     <div class="panel panel-default">
    
    <div class="panel-heading"><h4>Hasta Bilgileri:  <?php echo $hasta->isim." ".$hasta->soyisim;?></h4></div>
    
    <div class="panel-body">
    
    <div class="col-sm-6">
    <div class="form-group row">
<div class="col-sm-6"><label for="tckimlik">TC Kimlik Numarası:</label></div>
<div class="col-sm-6"><?php echo $hasta->tckimlik;?></div>
</div>

    <div class="form-group row">
<div class="col-sm-6"><label for="dogumtarihi">Doğum Tarihi:</label></div>
<div class="col-sm-6"><?php echo $hasta->dtarihi;?></div>
</div>
    
 <div class="form-group row">
<div class="col-sm-6"><label for="adres">Adres Bilgisi:</label></div>
<div class="col-sm-6"><?php echo $hasta->mahalleadi;?> / <?php echo $hasta->sokakadi;?> / <?php echo $hasta->kapino;?> / <?php echo $hasta->ilceadi;?></div>
</div>

 <div class="form-group row">
<div class="col-sm-6"><label for="kayittarihi">Kayıt Tarihi:</label></div>
<div class="col-sm-6"><?php echo $hasta->kayityili;?> / <?php echo $aylar[$hasta->kayitay];?></div>
</div>

 <div class="form-group row">
<div class="col-sm-6"><label for="bagimlilik">Bağımlılık Durumu:</label></div>
<div class="col-sm-6"><?php echo $bagimli[$hasta->bagimlilik];?></div>
</div>

 <div class="form-group row">
<div class="col-sm-6"><label for="toplamizlem">Toplam İzlem Sayısı:</label></div>
<div class="col-sm-6"><span class="label label-warning"><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $hasta->tckimlik;?>"><?php echo $hasta->toplamizlem;?> İzlem</a></span></div>
</div>

 <div class="form-group row">
<div class="col-sm-6"><label for="hbilgileri">Hastalık Bilgileri:</label></div>
<div class="col-sm-6"><?php echo implode(', ', $list);?></div>
</div>



<div class="form-group row">
<a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $hasta->id;?>" class="btn btn-warning">Bilgileri Düzenle</a>
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
</div>

    </div>
    <div class="col-sm-6">
    <table class="table table-striped">
    <thead>
    <tr>
    <th colspan="2">Hastanın Ek Özellikleri:</th>
    </tr>
    </thead>
    <tbody>
<tr>
<th>Geçici Takipli Hasta:</th>
<td><?php echo $hasta->gecici ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr>
<tr>
<th>Nazogastrik Takılı:</th>
<td><?php echo $hasta->ng ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr>
<tr>
<th>PEG Takılı:</th>
<td><?php echo $hasta->peg ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr>
<tr>
<th>Port Takılı:</th>
<td><?php echo $hasta->port ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr>
<tr>
<th>Oksijen Bağımlı:</th>
<td><?php echo $hasta->o2bagimli ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr>
<tr>
<th>Ventilatör Takılı:</th>
<td><?php echo $hasta->ventilator ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr>
<tr>
<th>Kolostomi Takılı:</th>
<td><?php echo $hasta->kolostomi ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr> 
<tr>
<th>Sonda Takılı:</th>
<td><?php echo $hasta->sonda ? '<strong>Evet</strong>': 'Hayır';?></td>
</tr>
<?php if ($hasta->sonda) { ?>
<tr>
<th>Sonda Takılma Tarihi:</th>
<td><?php echo tarihCevir($hasta->sondatarihi, 1);?></td>
</tr>
<?php } ?>
</tbody>
</table>
    </div>
    
    </div>
    </div> 
    <?php
    } 
    
    function editHasta($row, $lists, $limitstart, $limit) {
    ?>
    <form action="index.php" data-toggle="validator" method="post" name="adminForm" role="form" novalidate>
    
    <div class="panel panel-<?php echo $row->pasif ? 'danger':'default';?>">
    
    <div class="panel-heading"><h4><?php echo $row->id ? 'Düzenle : '.$row->isim.' '.$row->soyisim : 'Yeni Hasta Ekle';?></h4></div>
    
    <div class="panel-body">       
    
    <div class="form-group"> 
    <div class="panel with-nav-tabs">  <!-- panel navtabs -->
    
    <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1default" data-toggle="tab"><strong>Temel Bilgiler</strong></a></li>
    <li><a href="#tab2default" data-toggle="tab"><strong>Hastalık Bilgileri</strong></a></li> 
    <li><a href="#tab3default" data-toggle="tab"><strong>Hasta Özellikleri</strong></a></li>
    </ul>

    <div class="panel-body">  <!-- panel body -->
    <div class="tab-content">        <!-- tab content -->
                        
<div class="tab-pane fade in active" id="tab1default"> <!-- 1. Tab -->
                        
<div class="form-group row">
<div class="col-sm-3"><label for="tckimlik">TC Kimlik Numarası:</label></div>
<div class="col-sm-4"><input type="text" data-minlength="11" maxlength="11" id="tckimlik" name="tckimlik" class="form-control" value="<?php echo $row->tckimlik;?>" required>
<?php if (!$row->id) { ?>
<span id="sonuc"></span>
<?php } ?>
</div>
</div>
    
<div class="form-group row">
<div class="col-sm-3"><label for="isim">Hastanın Adı:</label></div>
<div class="col-sm-4"><input id="isim" type="text" name="isim" class="form-control" value="<?php echo $row->isim;?>" required></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="soyisim">Hastanın Soyadı:</label></div>
<div class="col-sm-4"><input type="text" id="soyisim" name="soyisim" class="form-control" value="<?php echo $row->soyisim;?>" required></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="dogumtarihi">Doğum Tarihi:</label></div>  
<div class="col-sm-4 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="dogumtarihi" name="dogumtarihi" value="<?php echo $row->dogumtarihi;?>" required>
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>

</div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label>Cinsiyeti:</label></div>
<div class="col-sm-4"><?php  echo $row->hastaCinsiyet(1); ?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="kayityili">Kayıt Yılı:</label></div>
<div class="col-sm-4"><?php  echo $row->hastaKayitYili(1);?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="kayitay">Kayıt Ayı:</label></div>
<div class="col-sm-4"><?php  echo mosHTML::monthSelectList('kayitay', 'id="kayitay" required', $row->kayitay, 1);?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="ilce">İlçe:</label></div>
<div class="col-sm-4"><?php echo $lists['ilce'];?></div>
</div>

<div class="form-group row"> 
<div class="col-sm-3"><label for="mahalle">Mahalle:</label></div>
<div class="col-sm-4"><?php echo $lists['mahalle'];?></div>
</div>
                    
 
<div class="form-group row">                    
<div class="col-sm-3"><label for="sokak">Cadde/Sokak:</label></div>
<div class="col-sm-4"><?php echo $lists['sokak'];?></div>
</div>

<div class="form-group row">                    
<div class="col-sm-3"><label for="kapino">Kapı No:</label></div>
<div class="col-sm-4"><?php echo $lists['kapino'];?></div>
</div>





<div class="form-group row">
<div class="col-sm-3"><label>Hasta Dosyası Pasif:</label></div>
<div class="col-sm-4"><?php  echo $row->hastaPasif(1);?></div>
</div>

<div id="pasifcheck" style="display:none">
<div class="form-group row">
<div class="col-sm-3"><label for="pasiftarihi">Pasif Edilme Tarihi:</label></div>
<div class='col-sm-4 input-group date' id='pasiftarihi1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="pasiftarihi" name="pasiftarihi" value="<?php echo $row->pasiftarihi ? tarihCevir($row->pasiftarihi, 1) : '';?>" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>


<div class="form-group row">
<div class="col-sm-3"><label>Pasif Edilme Nedeni:</label></div>
<div class="col-sm-9"><?php  echo $row->hastaPasifNedeni();?></div>
</div>
</div>

</div>  <!-- 1. Tab -->

<script>                                  
$(document).ready(function(){
    
        $("#ilce").on("change", function(){

            $("#mahalle").attr("disabled", false).html("<option value=''>Bir Mahalle Seçin</option>");
            $("#sokak").attr("disabled", true).html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").attr("disabled", true).html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val()); 
            
            ajaxFunc("mahalle", $(this).val(), "#mahalle");

        });

        $("#mahalle").on("change", function(){

            $("#sokak").attr("disabled", false).html("<option value=''>Bir Sokak Seçin</option>");
            $("#kapino").attr("disabled", true).html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("sokak", $(this).val(), "#sokak");

        });
        
        $("#sokak").on("change", function(){

            $("#kapino").attr("disabled", false).html("<option value=''>Bir Kapı No Seçin</option>");
            console.log($(this).val());
            
            ajaxFunc("kapino", $(this).val(), "#kapino");

        });
        
        function ajaxFunc(task, id, name ){
            $.ajax({
                url: "index2.php?option=site&bolum=hastalar",
                type: "POST",
                data: {task:task, id:id},
                success: function(sonuc){
                    $.each(JSON.parse(sonuc), function(key, value){
                    console.log(sonuc);
                        $(name).append("<option value="+key+">"+value+"</option>");
                    });
                }});
        }
    });
</script>
                        
                        
<div class="tab-pane fade" id="tab2default"> <!-- 2. tab -->

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

<div class="panel panel-default">
<span class="side-tab" data-target="#tab1" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
<h5 class="collapsed">Nörolojik ve Psikiyatrik Hastalıklar</h5>
</div>
</span>
                    
<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
<div class="panel-body">
<?php 
$tab1 = array(
'parkinson' => 'Parkinson',
'alzheimer' => 'Alzheimer',
'demans' => 'Demans',
'senilite' => 'Senilite(Yaşlılık)',
'epilepsi' => 'Epilepsi',
'hidrosefali' => 'Hidrosefali',
'ensefalopati' => 'Ensefalopati',
'svo' => 'SVO',
'sspe' => 'SSPE',
'miyelit' => 'Miyelit',
'als' => 'ALS',
'motornoron' => 'Motor Nöron Hastalığı',
'multiplskleroz' => 'Multipl Skleroz',
'noropatikagri' => 'Nöropatik Ağrı',
'kuadripleji' => 'Kuadripleji',
'hemipleji' => 'Hemipleji',
'parapleji' => 'Parapleji',
'tetrapleji' => 'Tetrapleji',
'mentalretardasyon' => 'Mental Retardasyon',
'poliosekel' => 'Polio Sekeli',
'depresyon' => 'Depresyon',
'psikoz' => 'Psikoz',
'bipolar' => 'Bipolar Bozukluk',
'sizofren' => 'Şizofreni',
'serebralpalsi' => 'Serebral Palsi'
);

foreach ($tab1 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div> 
 
<div class="panel panel-default">
<span class="side-tab" data-target="#tab2" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingTwo" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
<h5 class="collapsed">Kas Hastalıkları (Yatağa Bağımlı)</h5>
</div>
</span>

<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
<div class="panel-body">
<?php 
$tab2 = array(
'kasatrofisi' => 'Kas Atrofisi',
'ataksi' => 'Ataksi',
'muskulerdistrofi' => 'Müsküler Distrofi',
'spastikveflask' => 'Spastik ve Flask Tip Özürlü'
);

foreach ($tab2 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div>
 
<div class="panel panel-default">
<span class="side-tab" data-target="#tab3" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingThree" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
<h5 class="collapsed">Kardiyovasküler Hastalıklar</h5>
</div>
</span>

<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
<div class="panel-body">
<?php 
$tab3 = array(
'aritmi' => 'Aritmi',
'ht' => 'Hipertansiyon',
'kalpkapak' => 'Kalp Kapak Hastalıkları',
'kalpyet' => 'Kalp Yetmezliği',
'koronerarter' => 'Koroner Arter Hastalığı',
'venozdol' => 'Venöz Dolaşım Bozukluğu'
);

foreach ($tab3 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div>

<div class="panel panel-default">
<span class="side-tab" data-target="#tab4" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingFour" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
<h5 class="collapsed">Kronik ve Endokrin Hastalıklar</h5>
</div>
</span>

<div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
<div class="panel-body">
<?php 
$tab4 = array(
'dm' => 'Diyabet',
'hiperlipidemi' => 'Hiperlipidemi',
'hipertiroidi' => 'Hipertiroidi',
'hipotiroidi' => 'Hipotiroidi',
'obezite' => 'Obezite',
'diyaliz' => 'Diyaliz Hastası',
'bobrekyet' => 'Böbrek Yetmezliği',
'siroz' => 'Karaciğer Sirozu'
);

foreach ($tab4 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div> 

<div class="panel panel-default">
<span class="side-tab" data-target="#tab5" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingFive" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
<h5 class="collapsed">Hematolojik ve Onkolojik Hastalıklar</h5>
</div>
</span>

<div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
<div class="panel-body">
<?php 
$tab5 = array(
'anemi' => 'Anemi',
'itp' => 'ITP',
'ciltkanser' => 'Cilt Kanseri',
'memekanser' => 'Meme Kanseri',
'farinkskanser' => 'Farinks Kanseri',
'trakeakanser' => 'Trakea Kanseri',
'akckanseri' => 'Akciğer Kanseri',
'ozofaguskanser' => 'Özofagus Kanseri',
'midekanser' => 'Mide Kanseri',
'barsakkanser' => 'Bağırsak Kanseri',
'karacigerkanser' => 'Karaciğer Kanseri',
'pankreaskanser' => 'Pankreas Kanseri',
'safrakanser' => 'Safra Kesesi Kanseri',
'prostatkanser' => 'Prostat Kanseri',
'tiroidkanser' => 'Tiroid Kanseri',
'hipofizkanser' => 'Hipofiz Kanseri',
'bobrekkanser' => 'Böbrek Kanseri',
'overkanser' => 'Over Kanseri',
'uteruskanser' => 'Uterus Kanseri',
'kemikkanser' => 'Kemik Kanseri',
'beyinkanser' => 'Beyin Kanseri',
'multiplmiyelom' => 'Multipl Miyelom'
);

foreach ($tab5 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div>

<div class="panel panel-default">
<span class="side-tab" data-target="#tab6" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingSix" data-toggle="collapse" data-parent="#accordion" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
<h5 class="collapsed">Akciğer ve Solunum Sistemi Hastalıkları</h5>
</div>
</span>

<div id="collapseSix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
<div class="panel-body">
<?php 
$tab6 = array(
'koah' => 'KOAH',
'astim' => 'Astım',
'tuberkuloz' => 'Tüberküloz',
'kistikfibroz' => 'Kistik Fibrozis'
);

foreach ($tab6 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div>

<div class="panel panel-default">
<span class="side-tab" data-target="#tab7" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingSeven" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
<h5 class="collapsed">Ortopedi ve Travmatoloji Hastalıkları</h5>
</div>
</span>

<div id="collapseSeven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSeven">
<div class="panel-body">
<?php 
$tab7 = array(
'dekubitulseri' => 'Dekübitus Ülseri',
'diskopati' => 'Diskopati',
'extremiteamp' => 'Ekstremitelerde Ampütasyon',
'kalcafraktur' => 'Kalça Fraktürü',
'operekirik' => 'Opere Edilmiş veya mobilizasyon kısıtlayıcı kırıklar',
'osteoporoz' => 'Osteoporoz',
'omurgafraktur' => 'Omurga Fraktürü'
);

foreach ($tab7 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div>

<div class="panel panel-default">
<span class="side-tab" data-target="#tab8" data-toggle="tab" role="tab" aria-expanded="false">
<div class="panel-heading" role="tab" id="headingEight" data-toggle="collapse" data-parent="#accordion" href="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
<h5 class="collapsed">Diğer Hastalıklar</h5>
</div>
</span>

<div id="collapseEight" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingEight">
<div class="panel-body">
<?php 
$tab2 = array(
'ebullosa' => 'Epidermolizis Büllosa',
'zona' => 'Zona',
'fil' => 'Fil Hastalığı',
'sle' => 'Sistemik Lupus Eritomatozus',
'bph' => 'Benign Prostat Hipertrofisi'
);

foreach ($tab2 as $v=>$k) {
    
    $checked = $row->$v ? " checked=\"checked\"" : "";

    echo '<label class="checkbox-inline">';
    echo '<input type="checkbox" name="'.$v.'" id="'.$v.'" value="1"'.$checked.' />'.$k.'</label>';
}
?>
</div>
</div>
</div>   
 
</div>
</div>  <!-- 2. tab -->     

<div class="tab-pane fade" id="tab3default"> <!-- 3. Tab -->     
<div class="form-group row">
<div class="col-sm-3"><label>Hastanın Bağımlılık Durumu:</label></div>
<div class="col-sm-4"><?php  echo $row->hastaBagimlilik($row->id);?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label>Nazogastrik Takılı:</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('ng', '', $row->ng);?></div>
</div>
<div class="form-group row">
<div class="col-sm-3"><label>PEG Takılı:</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('peg', '', $row->peg);?></div>
</div>
<div class="form-group row">
<div class="col-sm-3"><label>Port Takılı:</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('port', '', $row->port);?></div>
</div>
<div class="form-group row">
<div class="col-sm-3"><label>Oksijen Bağımlı:</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('o2bagimli', '', $row->o2bagimli);?></div>
</div>
<div class="form-group row">
<div class="col-sm-3"><label>Ventilatöre Bağlı:</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('ventilator', '', $row->ventilator);?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label>Kolostomili:</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('kolostomi', '', $row->kolostomi);?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label>Hasta Geçici Takipli:</label></div>
<div class="col-sm-4"><?php  echo $row->hastaGecici($row->id);?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label>Sonda Takılı:</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('sonda', '', $row->sonda);?></div>
</div>

<div id="sondacheck" style="display:none">
<div class="form-group row">
<div class="col-sm-3"><label for="sondatarihi">En Son Sonda Takılma Tarihi:</label></div>
<div class='col-sm-4 input-group date' id='sondatarihi1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="sondatarihi" name="sondatarihi" value="<?php echo $row->sondatarihi ? tarihCevir($row->sondatarihi, 1) : '';?>" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>

</div> <!-- 3. tab -->

</div> <!-- tabcontent -->
</div> <!-- panel body -->
</div> <!-- panel navtabs -->     

</div>     
  
<div class="form-group row">
<div class="col-sm-7">
<input type="button" id="save" name="button" value="Kaydet" onclick="javascript:submitbutton('save');" class="btn btn-primary"  />
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
</div>
</div>

</div>   <!-- panel-body -->

</div>  <!-- panel default -->


    <input type="hidden" name="option" value="site" />
    <input type="hidden" name="bolum" value="hastalar" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="limitstart" value="<?php echo $limitstart;?>" />
    <input type="hidden" name="limit" value="<?php echo $limit;?>" />
    <input type="hidden" name="id" value="<?php echo $row->id;?>" />
    <script type="text/javascript">
    $(document).ready(function(){
        $("#save").on("click", function() {
            $(this).prop("disabled", true);
        });
        
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
</script>
    <script type="text/javascript">
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
    
    $('#sondatarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"
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
    });
    </script>
    </form>
    <?php
    }
    
    function getHastaList($rows, $pageNav, $search, $ilce, $mahalle, $sokak, $kapino, $kayityili, $kayitay, $cinsiyet, $bagimlilik, $ordering, $lists) {
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
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
        
    ?>
<form action="index.php" method="GET" name="adminForm" role="form">
<input type="hidden" name="option" value="site" />
<input type="hidden" name="bolum" value="hastalar" />
<input type="hidden" name="task" value="list" /> 

<div class="row">
<div class="col-sm-3">

<div class="panel panel-default" id="leftside"> 
<div class="panel-heading"><h4>Arama Seçenekleri</h4></div>
        
<div class="panel-body">

<div class="form-group">
<div class="input-group">
<input type="text" name="search" maxlength="11" value="<?php echo htmlspecialchars( $search );?>" class="form-control"  onChange="document.adminForm.submit();" placeholder="TC Kimlik yada Bir isim Yazın">
<div class="input-group-btn"><button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button></div>
</div>
</div>

<div class="form-group">
<label for="ilce">İlçe</label>
<?php echo $lists['ilce'];?>
</div>

<div class="form-group">
<label for="mahalle">Mahalle</label>
<?php echo $lists['mahalle'];?>
</div>

<div class="form-group">
<label for="sokak">Sokak</label>
<?php echo $lists['sokak'];?>
</div>

<div class="form-group">
<label for="kapino">Kapı No</label>
<?php echo $lists['kapino'];?>
</div>

<div class="form-group">
<label for="kayityili">Kayıt Yılı</label> 
<?php echo $lists['kayityili'];?>  
</div>

<div class="form-group">
<label for="kayitay">Kayıt Ayı:</label>
<?php  echo mosHTML::monthSelectList('kayitay', 'id="kayitay"', $kayitay, 1);?>
</div>

<div class="form-group">
<label for="cinsiyet">Hasta Cinsiyet</label> 
<?php echo $lists['cinsiyet'];?>  
</div>

<div class="form-group">
<label for="bagimlilik">Bağımlılık Durumu</label> 
<?php echo $lists['bagimlilik'];?>  
</div>

<div class="form-group">
<input type="button" name="button" value="Kayıtları Getir" onclick="javascript:submitbutton('list');" class="btn btn-primary"  />
</div>
 
</div> <!-- panel body -->
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
                type: "GET",
                data: {task:task, id:id},
                success: function(sonuc){
                    $.each(JSON.parse(sonuc), function(key, value){
                        console.log(sonuc);
                        $(name).append("<option value="+key+">"+value+"</option>");
                    });
                }});
        }
    });
</script> 

</div> <!-- panel left-->

</div> <!-- col-sm -->


<div class="col-sm-9">    
    
<div class="panel panel-primary" id="mainside">
<div class="panel-heading">
<div class="row">
    <div class="col-xs-11"><h4><?php echo $pasif ? 'Pasif' : 'Aktif';?> Hasta Listesi</h4></div>
    <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
</div>
</div>
    <table class="table table-striped">
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
      TC Kimlik Numarası
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
      <th scope="col"><div>
      Kayıt Yılı
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.kayityili-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col"><div>
      Doğum Tarihi
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.dogumtarihi-ASC">▼</a></span>
    </div>  
    </th>
      <th scope="col">Yaşı</th> 
      <th scope="col">Cinsiyet</th> 
      <th scope="col">Son İzlem Tarihi</th>
    </tr>
  </thead>
  <tbody>

  <?php 
   foreach($rows as $row) {
       $tarih = explode('.',$row['dogumtarihi']);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row['dtarihi'] = strftime("%d.%m.%Y", $tarih);
       
       //son izlem tarihi
       $row['sonizlemtarihi'] = $row['sonizlemtarihi'] ? tarihCevir($row['sonizlemtarihi'], 1):'Yok';
         
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
      
      <tr>
      <td scope="row"><span data-toggle="tooltip" title="<?php echo $row['izlemsayisi'];?> İzlem" class="label label-<?php echo $row['izlemsayisi'] ? 'default':'warning';?>"><?php echo $row['izlemsayisi'];?></span></td>
      <th scope="row">
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row['isim'];?></a>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row['id'];?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row['id'];?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row['tckimlik'];?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row['tckimlik'];?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
      </th>
      <td><?php echo $row['tckimlik'];?></td>
      <td><?php echo $row['mahalle'];?> </td>
      <td><?php echo $row['kayityili'];?> <?php echo $aylar[$row['kayitay']];?></td>
      <td><?php echo $row['dtarihi'];?></td>
      <td><?php echo yas_bul($row['dogumtarihi']);?></td> 
      <td><?php echo $row['cinsiyet'] == "E" ? "Erkek":"Kadın";?></td>
      <td><?php echo $row['sonizlemtarihi'];?></td>
      </tr>
 <?php 

  }?>
  
    </tbody>
</table>
<!--
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

</div> <!-- col-sm -->

</div>
   
</form> 
<?php    
    }    
}
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class PansumanHTML {
    
    function getHastaList($day, $rows, $total) {
        
        ?>
        <div class="panel panel-default">
    
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-6">
    <h4><i class="fa-solid fa-eye"></i> Pansuman Listesi</h4>
    
    </div>
    <div class="col-xs-6" align="right">
    <ul class="nav nav-pills">
  <li<?php echo $day == 1 ? ' class="active"':'';?>><a href="index.php?option=site&bolum=pansuman&day=1">Pazartesi</a></li>
  <li<?php echo $day == 2 ? ' class="active"':'';?>><a href="index.php?option=site&bolum=pansuman&day=2">Salı</a></li>
  <li<?php echo $day == 3 ? ' class="active"':'';?>><a href="index.php?option=site&bolum=pansuman&day=3">Çarşamba</a></li>
  <li<?php echo $day == 4 ? ' class="active"':'';?>><a href="index.php?option=site&bolum=pansuman&day=4">Perşembe</a></li>
  <li<?php echo $day == 5 ? ' class="active"':'';?>><a href="index.php?option=site&bolum=pansuman&day=5">Cuma</a></li>
  <li<?php echo $day == 6 ? ' class="active"':'';?>><a href="index.php?option=site&bolum=pansuman&day=6">Cumartesi</a></li>
  <li<?php echo $day == 0 ? ' class="active"':'';?>><a href="index.php?option=site&bolum=pansuman&day=0">Pazar</a></li>
</ul>
    </div> 
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
    </div>  
    </th>
      <th scope="col"><div>
      TC Kimlik
    </div>  
    </th>
    <th scope="col"> <div>
      Mahalle
    </div>  
    </th>
      <th scope="col"><div>
      Kayıt Yılı
    </div>  
    </th>
      <th scope="col"><div>
      D. Tarihi
   </div>  
    </th>
      <th scope="col">Yaşı</th> 
      <th scope="col">Cinsiyet</th>
      <th scope="col">Telefon 1</th>
      <th scope="col">Telefon 2</th>
      <th scope="col">Son İzlem</th> 
    </tr>
  </thead>
  <tbody>
    <?php     
    foreach ($rows as $row) {
      $tarih = explode('.',$row->dogumtarihi);
       $tarih = mktime(0,0,0,$tarih[1],$tarih[2],$tarih[0]);
         
       $row->dtarihi = strftime("%d.%m.%Y", $tarih);
       
       //son izlem tarihi
       $row->sonizlemtarihi = $row->sonizlemtarihi ? tarihCevir($row->sonizlemtarihi, 1):'Yok';
         
       $aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
      ?>
      
      <tr class="">
      <td scope="row"><span data-toggle="tooltip" title="<?php echo $row->izlemsayisi;?> İzlem" class="label label-<?php echo $row->izlemsayisi ? 'info':'warning';?>"><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>"><?php echo $row->izlemsayisi;?></a></span></td>
      <th scope="row">
       <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->isim.' '.$row->soyisim;?></a>  <?php echo $row->gecici ? '<sub>(G)</sub>':'';?>
  <ul class="dropdown-menu">
  <li><a href="index.php?option=site&bolum=hastalar&task=show&id=<?php echo $row->id;?>">Bilgileri Göster</a></li>
    <li><a href="index.php?option=site&bolum=hastalar&task=edit&id=<?php echo $row->id;?>">Bilgileri Düzenle</a></li>
     <li class="divider"></li> 
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->tckimlik;?>">İzlemleri Göster</a></li>
    <li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc=<?php echo $row->tckimlik;?>">Yeni İzlem Gir</a></li>
  </ul>
</div> 
      </th>
      <td><?php echo $row->tckimlik;?></td>
      <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td>
      <td><?php echo $row->kayityili;?> <?php echo $aylar[$row->kayitay];?></td>
      <td><?php echo $row->dtarihi;?></td>
      <td><?php echo yas_bul($row->dogumtarihi);?></td> 
      <td><?php echo $row->cinsiyet == "E" ? "Erkek":"Kadın";?></td>
      <td><?php echo $row->ceptel1;?></td>
      <td><?php echo $row->ceptel2;?></td>
      <td><?php echo $row->sonizlemtarihi;?></td>
      </tr>
    <?php
    }
    ?>
    </tbody>
    </table>

    
    <div class="panel-footer">
    
    </div>

</div>

<?php
    
    
    }
    
    function hastaEkle($row, $lists) {
        
        ?>
         <form action="index.php" data-toggle="validator" method="post" role="form" novalidate>     
        <div class="panel panel-default">
    
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-6"><h4><i class="fa-solid fa-eye"></i> Pansuman Günleri: <?php echo $row->isim.' '.$row->soyisim;?></h4></div>
     </div>
     </div> 
    
        <table class="table table-striped">
        <tr>   
        <td><label for="pansumangunleri">Pansuman Günleri:</label></td>
        <td><?php echo $lists['days'];?></td>
        </tr> 
        </table>
        
<input type="hidden" name="option" value="site">
<input type="hidden" name="bolum" value="pansuman">
<input type="hidden" name="task" value="save">
<input type="hidden" name="pansuman" value="1">
<input type="hidden" name="id" value="<?php echo $row->id;?>"> 

    
    <div class="panel-footer">
     <button type="submit" class="btn btn-primary">Kaydet</button> 
     <a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>  
    </div>
    
    </div>
</form>     
    <?php
    
    }

}
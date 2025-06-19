<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class IzlemList {
    
    function editIzlem($row, $limit, $limitstart, $hasta, $lists) {
    ?>
    <form action="index.php" method="post" name="adminForm" role="form" data-toggle="validator" novalidate>
    
    <div class="panel panel-default">
    
    <div class="panel-heading"><h4><i class="fa-solid fa-calendar-days"></i> Planlı İzlem Ekle</h4></div>
    
    <div class="panel-body">
    
<div class="form-group row">
<div class="col-sm-3"><label for="hastatckimlik">Hastanın TC Kimlik No:</label></div>
<div class="col-sm-4"><input maxlength="11" type="text" id="hastatckimlik" name="hastatckimlik" class="form-control" value="<?php echo $row->hastatckimlik;?>" readonly required></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="hastaisim">Hastanın Adı:</label></div>
<div class="col-sm-4"><div class="form-control" id="sonuc"><?php echo $hasta ? $hasta->isim.' '.$hasta->soyisim : '';?></div></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="izlemtarihi">İzlem Tarihi:</label></div>  
<div class="col-sm-4 date" id="datepicker">

<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="izlemtarihi" name="izlemtarihi" value="<?php echo tarihCevir($row->planlanantarih, 1);?>" required />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
 <script type="text/javascript">
    $('#izlemtarihi').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

    }); 
</script>
</div>  
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">İşlemi Yapan:</label></div>
<div class="col-sm-9"><?php echo $lists['perlist'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">Yapılan İşlem:</label></div>
<div class="col-sm-9"><?php echo $lists['isyapilacak'];?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="yapildimi">İşlem Yapıldı mı?</label></div>
<div class="col-sm-9"><?php echo mosHTML::yesnoRadioList('yapildimi', '', 1);?></div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="planli">Planlı İşlem Var mı?</label></div>
<div class="col-sm-4"><?php echo mosHTML::yesnoRadioList('planli', '', 0);?></div>
</div>

</div><!-- panel body -->
<div class="panel-footer">

<div id="yneden"  style="display:none">  <!-- yapilmama nedeni -->

<div class="form-group row">
<div class="col-sm-3"><label for="neden">Yapılmama Nedeni:</label></div>  
<?php echo $lists['yneden'];?>
</div>

</div>  <!-- yapilmama nedeni -->
    
    
    
    <div id="planli"  style="display:none">  <!-- planlii -->

<div class="form-group row">
<div class="col-sm-3"><label for="izlemtarihi">Planlanan İzlem Tarihi:</label></div>  
<div class="col-sm-3 date" id="datepicker">
<div class='input-group date' id='datepicker1' data-date-format="dd.mm.yyyy">
<input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="planlanantarih" name="planlanantarih" value="" autocomplete="off" />
<span class="input-group-addon">
<span class="glyphicon glyphicon-calendar"></span>
</span>
</div>
</div>
</div>

<div class="form-group row">
<div class="col-sm-3"><label for="islem">Yapılacak İşlem:</label></div>
<div class="col-sm-9"><?php echo $lists['isplanlanan'];?></div>
</div>

</div>  <!-- planlii -->
    
    
    <div class="form-group row">
<div class="col-sm-4">
<button type="submit" id="save" class="btn btn-primary">Kaydet</button>
<a href="javascript:history.go(-1);" class="btn btn-default">Geri Git</a>
</div>
</div>
    </div><!-- panel footer -->
  
    </div>

    <input type="hidden" name="option" value="site" />
    <input type="hidden" name="bolum" value="pizlemler" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="id" value="<?php echo $row->id;?>" />
    <input type="hidden" name="limit" value="<?php echo $limit;?>" />
    <input type="hidden" name="limitstart" value="<?php echo $limitstart;?>" />
    <script type="text/javascript">
    $(document).ready(function(){
    $('#hastatckimlik').keyup(function(){
    var val = $('#hastatckimlik').val(); 
    var uzunluk = val.length;
    
    if (uzunluk==11) {
            $.ajax({
                url:'index2.php?&option=site&bolum=pizlemler&task=control&tc='+val,
                type:'GET',
                success:function(result){
                    $('#sonuc').html(result);
                }  
            });
    } else {
                    $('#sonuc').empty();
    }    
});

    $("input[name=planli]").change(function(){

        if($("#planli1").is(':checked')){
            $("#planli").show();
            $('#planlanantarih').attr('required', true);
        }else{
            $("#planli").hide();
            $('#planlanantarih').removeAttr('required');
        }
    });
    
    if($("#planli1").is(':checked')){
            $("#planli").show();
            $('#planlanantarih').attr('required', true);
    } else {
            $("#planli").hide();
            $('#planlanantarih').removeAttr('required');
    }
    
    
    $("input[name=yapildimi]").change(function(){

        if($("#yapildimi0").is(':checked')){
            $("#yneden").show();
            $('#neden').attr('required', true);
        }else{
            $("#yneden").hide();
            $('#neden').removeAttr('required');
        }
    });
    
    if($("#yapi1dimi").is(':checked')){
            $("#yneden").hide();
            $('#neden').removeAttr('required');
    } else {
            $("#yneden").hide();
            $('#neden').attr('required', true);
            
    }
  }); 
  
    $('#planlanantarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        startDate: '+1d',
        autoclose: true,
        language: "tr",
        orientation: "bottom"
    }); 

    $('#izlemtarihi').datepicker({
    todayHighlight: true,
        autoclose: true,
        language: "tr",
        orientation: "bottom"

    });
</script>
    </form>
    <?php
    }
    
    function getIzlemList($rows, $pageNav, $baslangictarih, $bitistarih, $ordering, $list, $secim) {
        $link = 'index.php?option=site&bolum=pizlemler&task=list';

        $link .= "&baslangictarih=".$baslangictarih;
        
        $link .= "&bitistarih=".$bitistarih;
        
        
        if ($ordering) {
            $link .= "&ordering=".$ordering;
        }
        
        if ($secim) {
            $link .= "&secim=".$secim;
        }
    ?>
    <form action="index.php" method="GET" data-toggle="validator" novalidate>
    <input type="hidden" name="option" value="site" />
<input type="hidden" name="bolum" value="pizlemler" />
<input type="hidden" name="task" value="list" /> 
    <div class="panel panel-default">
    <div class="panel-heading">
     <div class="row">
        <div class="col-xs-2"><h4><i class="fa-solid fa-receipt"></i> Planlanan İzlemler</h4></div>
        <div class="col-xs-9">
         <div class="form-group row">      
      
    <div class="col-sm-6">
    
    <div class='input-group input-daterange' id='datepicker1' data-date-format="dd.mm.yyyy">
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="baslangictarih" name="baslangictarih" value="<?php echo $baslangictarih;?>" autocomplete="off" />
    
    
    <div class="input-group-addon">ile</div>
    
    <input data-date-format="dd.mm.yyyy" type='text' placeholder="GG.AA.YYYY" class="form-control" id="bitistarih" name="bitistarih" value="<?php echo $bitistarih;?>" autocomplete="off" />
    
    
    <div class="input-group-addon">arası</div>
     
    </div>
    
    
    </div>
    
    <div class="col-sm-3">
    <?php echo $list['islem'];?>
    </div>

    <div class="col-sm-2">
    <input type="submit" name="button" value="Kayıtları Getir" class="btn btn-primary"  />
    </div>
    
    </div> <!-- form-group row-->
        </div>
        <div class="col-xs-1" align="right"><?php echo $pageNav->getLimitBox($link);?></div>
    </div>
    </div>
  
    <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Hasta Adı
      <span><a href="<?php echo $link;?>&ordering=h.isim-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.isim-ASC">▼</a></span>
      </th>
      <th scope="col">TC Kimlik Numarası
      <span><a href="<?php echo $link;?>&ordering=h.tckimlik-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.tckimlik-ASC">▼</a></span>
      </th>
      <th scope="col">Mahalle
      <span><a href="<?php echo $link;?>&ordering=h.mahalle-DESC">▲</a></span>
  <span><a href="<?php echo $link;?>&ordering=h.mahalle-ASC">▼</a></span>
      </th>
      <th scope="col">Planlanan İzlem Tarihi</th>
      <th scope="col">Yapılacak İşlem</th>
    </tr>
  </thead>
  <tbody>

  <?php 
  $iz = new Izlem($dbase);
  $is = new Islem($dbase);
  
   foreach($rows as $row) {
       $edit = "index.php?option=site&bolum=pizlemler&task=edit&id=".$row->id;
      ?>
      
       <tr>
      <th scope="row">
      <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->isim;?> <?php echo $row->soyisim;?>
</a>
  <ul class="dropdown-menu">
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->hastatckimlik;?>">İzlemlerini Göster</a></li>
    <li class="divider"></li>    
    <li><a href="index.php?option=site&bolum=pizlemler&task=edit&id=<?php echo $row->id;?>">İzlemi Düzenle</a></li>
    <li><a href="index.php?option=site&bolum=pizlemler&task=delete&id=<?php echo $row->id;?>">İzlemi Sil</a></li>
  </ul>
</div>
      </th>
      <td>
      <div class="dropdown">
  <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $row->hastatckimlik;?>
</a>
  <ul class="dropdown-menu">
    <li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc=<?php echo $row->hastatckimlik;?>">İzlemlerini Göster</a></li>
    <li class="divider"></li>    
    <li><a href="index.php?option=site&bolum=pizlemler&task=edit&id=<?php echo $row->id;?>">İzlemi Düzenle</a></li>
    <li><a href="index.php?option=site&bolum=pizlemler&task=delete&id=<?php echo $row->id;?>">İzlemi Sil</a></li>
  </ul>
</div>
      
      </td> 
      <td><?php echo $row->mahalle;?> <span class="label label-success"><?php echo $row->ilce;?></span></td> 
      <td><?php echo tarihCevir($row->planlanantarih, 1);?></td>
      <td><?php echo $is->yapilanIslem($row->yapilacak);?></td>
    </tr>
 <?php 

  }?>
  
  </tbody>
</table>
<script type="text/javascript">
var userTarget = "";
var exit = false;
$('.input-daterange').datepicker({
  format: "dd.mm.yyyy",
        weekStart: 1,
        todayHighlight: true,
        autoclose: true,
        language: "tr"

});
$('.input-daterange').focusin(function(e) {
  userTarget = e.target.name;
});
$('.input-daterange').on('changeDate', function(e) {
  if (exit) return;
  if (e.target.name != userTarget) {
    exit = true;
    $(e.target).datepicker('clearDates');
  }
  exit = false;
});
    $('#baslangictarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    $('#bitistarih').datepicker({
        format: "dd.mm.yyyy",
        weekStart: 1,
                todayHighlight: true,
        autoclose: true,
        language: "tr"
    });
    </script> 

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
    
    function getTakvim($data) {
   ?>
   <script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        
      headerToolbar: {
        left: 'prev,next,today',
        center: 'title',
        right: 'dayGridMonth,listWeek,listDay'
      },

      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        listDay: { buttonText: 'Günlük Liste' },
        listWeek: { buttonText: 'Haftalık Liste' },
        dayGridMonth: { buttonText: 'Aylık Liste' },
      },

      initialView: 'dayGridMonth',
      initialDate: '<?php echo date('Y-m-d');?>',
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      dayMaxEvents: true, // allow "more" link when too many events
      events: [<?php echo implode(',', $data);?>]
    });
    calendar.setOption('locale', 'tr');
    calendar.render();
  });
  
</script>

<div class="panel panel-default">
    <div class="panel-heading">
     <div class="row">
        <div class="col-xs-10"><h4><i class="fa-solid fa-calendar-days"></i> Planlanan İzlemler (Takvim)</h4></div>
        <div class="col-xs-2" align="right"><a href="index.php?option=site&bolum=pizlemler&task=list" class="btn btn-sm btn-warning">Listeyi Göster</a></div>
    </div>
    </div>
    <div class="panel-body">
    <div id='calendar'></div>
    </div>
</div>
   <?php
   } 
    
}
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');

$id = intval(getParam($_REQUEST, 'id')); 
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));
$tc = getParam($_REQUEST, 'tc');
$today = date('d.m.Y');
$first = date("01.m.Y", strtotime($today));
$last = date("t.m.Y", strtotime($today));
$baslangictarih = getParam($_REQUEST, 'baslangictarih', $today);
$bitistarih = getParam($_REQUEST, 'bitistarih', $today);
$ordering = getParam($_REQUEST, 'ordering');
$secim = intval(getParam($_REQUEST, 'secim'));
$yap = intval(getParam($_REQUEST, 'yap', 1));  

switch($task) {
    default:
    case 'list':
    getIzlemList($baslangictarih, $bitistarih, $ordering, $secim, $yap);
    break;
    
    case 'izlemgetir':
    IzlemGetir($tc);
    break;
    
    case 'edit':
    editIzlem($id);
    break;
    
    case 'hedit':
    heditIzlem($tc);
    break;
    
    case 'new':
    editIzlem(0);
    break;
    
    case 'save':
    saveIzlem();
    break;
    
    case 'cancel':
    cancelIzlem();
    break;
    
    case 'delete':
    deleteIzlem($id);
    break;
    
    case 'control':
    controlTC($tc);
    break;
    
    case 'takvim':
    getTakvim();
    break;
}

function getTakvim() {
    global $dbase;
    
    $query = "SELECT i.*, h.isim, h.soyisim, isl.islemadi FROM #__izlemler AS i "
     . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
     . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilan "
     . "\n GROUP BY i.id ";
      
     $dbase->setQuery($query);
     $rows = $dbase->loadObjectList();
     
     $data = array();
     
     foreach ($rows as $row) {
         $data[] = "
         {
         title:'".$row->isim." ".$row->soyisim." (".$row->islemadi.")',
         start:'".date('Y-m-d', $row->izlemtarihi)."',
         url: 'index.php?option=site&bolum=izlemler&task=edit&id=".$row->id."'
         }";
     }    
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
        <div class="col-xs-10"><h4>Aktif İzlemler</h4></div>
        <div class="col-xs-2" align="right"></div>
    </div>
    </div>
    <div class="panel-body">
    <div id='calendar'></div>
    </div>
</div>
<?php 
}

function deleteIzlem($id) {
    global $dbase;
    
    $del = new Izlem($dbase);
    $del->load($id);
    
    if (!$del->id) {
    Redirect("index.php?option=site&bolum=izlemler&task=izlemgetir&tc=".$del->hastatckimlik, "Seçili izlem yok!!!");
    }
    
    $del->delete($id);
    
   Redirect("index.php?option=site&bolum=izlemler&task=izlemgetir&tc=".$del->hastatckimlik, "Seçilen izlem silindi"); 
}

function IzlemGetir($tc) {
    global $dbase, $limit, $limitstart;
    
    $dbase->setQuery("SELECT * FROM #__hastalar WHERE tckimlik=".$tc);
    $dbase->loadObject($hasta);
    
    $dbase->setQuery("SELECT COUNT(*) FROM #__izlemler WHERE izlemtarihi>0 AND hastatckimlik=".$tc);
    $total = $dbase->loadResult();
    
    $pageNav = new pageNav($total, $limitstart, $limit);
    
    $dbase->setQuery("SELECT * FROM #__izlemler WHERE izlemtarihi>0 AND hastatckimlik='".$tc."' ORDER BY izlemtarihi DESC", $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    //pansuman listesi işlemleri
    $daylist[] = mosHTML::makeOption('1', 'Pzt');
    $daylist[] = mosHTML::makeOption('2', 'Sal');
    $daylist[] = mosHTML::makeOption('3', 'Çar');
    $daylist[] = mosHTML::makeOption('4', 'Per');
    $daylist[] = mosHTML::makeOption('5', 'Cum');
    $daylist[] = mosHTML::makeOption('6', 'Cmt');
    $daylist[] = mosHTML::makeOption('0', 'Paz');
    
    $hasta->pgunleri = explode(',', $hasta->pgunleri);
   
    
    $lists['days'] = mosHTML::checkboxList($daylist, 'pgunleri', '', 'value', 'text', $hasta->pgunleri);
    
    IzlemList::IzlemGetir($hasta, $rows, $pageNav, $lists);
}

function heditIzlem($tc) {
    global $dbase, $limitstart, $limit;
                                                                      
     $dbase->setQuery("SELECT id, isim, soyisim, tckimlik FROM #__hastalar WHERE tckimlik=".$tc);
     $dbase->loadObject($hasta);
     
     $row = new Izlem($dbase);
     $row->load($hasta->id);
     
  
    //işlem seçme kutusu yapalım
    $dbase->setQuery("SELECT * FROM #__islem ORDER BY islemadi ASC");
    $islemler = $dbase->loadObjectList();
    
    $islemtype = array();
    foreach ($islemler as $islem) {
        $islemtype[] = mosHTML::makeOption($islem->id, $islem->islemadi);
    }
        
    $lists['isyapilan'] = mosHTML::checkboxList($islemtype, 'yapilan', '', 'value','text');
    $lists['isyapilacak'] = mosHTML::checkboxList($islemtype, 'yapilacak', '', 'value','text');
    
    // PERSONEL SEÇME KUTULARI YAPALIM
    $dbase->setQuery("SELECT id, name FROM #__users WHERE activated='1' ORDER BY name ASC");
    $personel = $dbase->loadObjectList();
    
    $islemiyapan = array();
    foreach ($personel as $per) {
        $islemiyapan[] = mosHTML::makeOption($per->id, $per->name);   
    }
    
    $row->izlemiyapan = explode(',', $row->izlemiyapan);
        
    $lists['perlist'] = mosHTML::checkboxList($islemiyapan, 'izlemiyapan', '', 'value', 'text');
    
    $nedenler = array(
    '1' => 'Hastanede yatıyor',
    '2' => 'Vefat etmiş',
    '3' => 'Adresi değişmiş',
    '4' => '112 ye teslim edildi',
    '5' => 'Hizmet reddedildi/İptal edildi',
    '6' => 'Evde kendisi/yakını yok',
    '7' => 'Gerekli evrak yok' 
    );
    
    foreach ($nedenler as $v=>$k) {
        $yneden[] = mosHTML::makeOption($v, $k);
    }
    
    $lists['yneden'] = mosHTML::radioList($yneden, 'neden', '', 'value', 'text');
    
    IzlemList::heditIzlem($row, $limit, $limitstart, $hasta, $lists);

}

function controlTC($tc) {
    global $dbase;
    
    if (!$tc) {
    echo "Hasta tc yazılmamış!";
    }
    
    $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$tc." AND pasif=0");
    $dbase->loadObject($row);
    
    if (!$row) {
        echo "Böyle bir hasta kayıtlı değil";
    } else {
        echo $row->isim.' '.$row->soyisim;
    }
   
}

function getIzlemList($baslangictarih, $bitistarih, $ordering, $secim, $yap) {
    global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "i.izlemtarihi>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "i.izlemtarihi<='".$cbitistarih."'";
    }
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY i.izlemtarihi DESC, h.isim ASC, h.soyisim ASC, i.planli DESC";
     }
     
     if ($secim) {
     
         $where[] = "FIND_IN_SET(".$secim.", i.yapilan)";
     
     }
     
     $where[] = "i.yapildimi = ".$yap;
    
    $query = "SELECT COUNT(i.id) FROM #__izlemler AS i"
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    ;
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     $query = "SELECT i.*, h.id AS hid, h.isim, h.soyisim, h.cinsiyet, h.gecici, isl.islemadi, il.ilce, m.mahalle, s.sokakadi FROM #__izlemler AS i "
     . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
     . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilan "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . "\n LEFT JOIN #__ilce AS il ON il.id=h.ilce "
     . "\n LEFT JOIN #__sokak AS s ON s.id=h.sokak "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY i.id "                                                               
     . $orderingfilter   
     ;
    
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    foreach ($rows as $row) {
        $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE izlemtarihi>0 AND hastatckimlik=".$row->hastatckimlik);
        $row->toplamizlem = $dbase->loadResult();
    }
    
    //işlemler select listesi
    $dbase->setQuery("SELECT * FROM #__islem ORDER BY islemadi ASC");
    $islemler = $dbase->loadObjectList();
    
    $data[] = mosHTML::makeOption('0', 'Bir İşlem Seçin'); 
    foreach ($islemler as $islem) {
    $data[] = mosHTML::makeOption($islem->id, $islem->islemadi);
    }
    
    $list['islem'] = mosHTML::selectList($data, 'secim', '', 'value', 'text', $secim);
    
    
    $yapi[] = mosHTML::makeOption("1", "Yapılan"); 
    $yapi[] = mosHTML::makeOption("0", "Yapılmayan");
    
    
    $list['yap'] = mosHTML::selectList($yapi, 'yap', '', 'value', 'text', $yap);
    
    IzlemList::getIzlemList($rows, $pageNav, $baslangictarih, $bitistarih, $ordering, $list, $secim);
}

function saveIzlem() {
         global $dbase, $limitstart, $limit;
    
    $row = new Izlem( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        ErrorAlert($row->getError());
    }
    
    $row->izlemiyapan = implode(',', $row->izlemiyapan);
    $row->yapilan = implode(',', $row->yapilan);
    $row->izlemtarihi = tarihCevir($row->izlemtarihi);
    
    if ($row->yapildimi) {
        $row->neden = '';
    }
    
    
    if (!$row->planli) {
        $row->planlanantarih = '';
        $row->yapilacak = '';    
    } else {
        
        $row->planlanantarih = tarihCevir($row->planlanantarih);
        $row->yapilacak = implode(',', $row->yapilacak);
        
        $dbase->setQuery("SELECT id FROM #__izlemler WHERE hastatckimlik=".$row->hastatckimlik." AND planlanantarih=".$row->planlanantarih." AND yapilacak=".$row->yapilacak);
        $varmi = $dbase->loadResult();
        
        if ($varmi) {
        ErrorAlert('Bu izlem daha önceden planlanmış!!!');
        }
        
        if ($row->planlanantarih == '') {
        ErrorAlert('Planlanan tarih girilmemiş');
        }
        
        if ($row->yapilacak == '') {
        ErrorAlert('Yapılacak işlem(ler) seçilmemiş');
        }
    }
    
    if ($row->izlemiyapan == '') {
        ErrorAlert('İzlemi yapan(lar) seçilmemiş');
    }
    
    if ($row->yapilan == '') {
        ErrorAlert('Yapılan işlem(ler) seçilmemiş');
    }
    
    if ($row->izlemtarihi == '') {
        ErrorAlert('İzlem tarihi seçilmemiş');
    } 
    
    if (!$row->store()) {
        ErrorAlert($row->getError());
    }
    
    $link = "index.php?option=site&bolum=izlemler&task=izlemgetir&tc=".$row->hastatckimlik;
    Redirect($link);
    
}

function editIzlem($id) {
    global $dbase, $limitstart, $limit;
    
    if (!$id) {
    Redirect("index.php?option=site&bolum=hastalar", "Hasta seçilmemiş!");
    }
    
    $row = new Izlem($dbase);
    
    $row->load($id);
    
     $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$row->hastatckimlik);
     $dbase->loadObject($hasta);   
    
    //işlem seçme kutusu yapalım
    $dbase->setQuery("SELECT * FROM #__islem ORDER BY islemadi ASC");
    $islemler = $dbase->loadObjectList();
    
    $islemtype = array();
    foreach ($islemler as $islem) {
        $islemtype[] = mosHTML::makeOption($islem->id, $islem->islemadi);
    }
    
    $row->yapilan = explode(',', $row->yapilan);
    $row->yapilacak = explode(',', $row->yapilacak);
    
    $lists['isyapilan'] = mosHTML::checkboxList($islemtype, 'yapilan', '', 'value','text', $row->yapilan);
    $lists['isyapilacak'] = mosHTML::checkboxList($islemtype, 'yapilacak', '', 'value','text', $row->yapilacak);
    
    // PERSONEL SEÇME KUTULARI YAPALIM
    $dbase->setQuery("SELECT id, name FROM #__users WHERE activated='1' ORDER BY name ASC");
    $personel = $dbase->loadObjectList();
    
    $islemiyapan = array();
    foreach ($personel as $per) {
        $islemiyapan[] = mosHTML::makeOption($per->id, $per->name);   
    }
    
    $row->izlemiyapan = explode(',', $row->izlemiyapan);
        
    $lists['perlist'] = mosHTML::checkboxList($islemiyapan, 'izlemiyapan', '', 'value', 'text', $row->izlemiyapan);
    
    $nedenler = array(
    '1' => 'Hastanede yatıyor',
    '2' => 'Vefat etmiş',
    '3' => 'Adresi değişmiş',
    '4' => '112 ye teslim edildi',
    '5' => 'Hizmet reddedildi/İptal edildi',
    '6' => 'Evde kendisi/yakını yok',
    '7' => 'Gerekli evrak yok' 
    );
    
    foreach ($nedenler as $v=>$k) {
        $yneden[] = mosHTML::makeOption($v, $k);
    }
    
    $lists['yneden'] = mosHTML::radioList($yneden, 'neden', '', 'value', 'text', $row->neden);
    
    IzlemList::editIzlem($row, $limit, $limitstart, $hasta, $lists);

}

function cancelIzlem() {
    global $dbase;
    
    $row = new Izlem( $dbase );
    $row->bind( $_POST );
    Redirect( 'index.php?option=site&bolum=izlemler');
}
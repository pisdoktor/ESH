<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

include(dirname(__FILE__). '/html.php');

$id = intval(getParam($_REQUEST, 'id')); 
$cid = intval(getParam($_REQUEST, 'cid'));
$limit = intval(getParam($_REQUEST, 'limit', 50));
$limitstart = intval(getParam($_REQUEST, 'limitstart', 0));
$tc = getParam($_REQUEST, 'tc');
$today = date('d.m.Y');
$first = date("01.m.Y", strtotime($today));
$last = date("t.m.Y", strtotime($today));
$baslangictarih = getParam($_REQUEST, 'baslangictarih', $today);
$bitistarih = getParam($_REQUEST, 'bitistarih', $today);

$ordering = getParam($_REQUEST, 'ordering');  

switch($task) {
    case 'list':
    getIzlemList($baslangictarih, $bitistarih, $ordering);
    break;
    
    case 'edit':
    editIzlem($id);
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
    
    default:
    case 'takvim':
    getTakvim();
    break;
}


function getTakvim() {
    global $dbase;
    
    $query = "SELECT i.*, h.isim, h.soyisim, isl.islemadi, m.mahalle FROM #__izlemler AS i "
     . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
     . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilacak "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . "\n WHERE planli=1 AND h.pasif=0 "
     . "\n GROUP BY i.id ";
     $dbase->setQuery($query);
     $rows = $dbase->loadObjectList();
     
     $data = array();
     
     foreach ($rows as $row) {
         $data[] = "{
         title:'".$row->isim." ".$row->soyisim." (".$row->islemadi.")',
         start:'".date('Y-m-d', $row->planlanantarih)."',
         url: 'index.php?option=site&bolum=pizlemler&task=edit&id=".$row->id."',
         backgroundColor: '#7a8189'
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
        <div class="col-xs-10"><h4>Planlanan İzlem Listesi</h4></div>
        <div class="col-xs-2" align="right"><a href="index.php?option=site&bolum=pizlemler&task=list" class="btn btn-sm btn-default">Listeyi Göster</a></div>
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
    
    $dbase->setQuery("UPDATE #__izlemler SET planli='0', planlanantarih='0', yapilacak='0' WHERE id=".$del->id);
    $dbase->query();
    
    Redirect("index.php?option=site&bolum=pizlemler", "Planlanan izlem silindi");
    
}

function controlTC($tc) {
    global $dbase;
    
    $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$tc." AND pasif=0");
    $dbase->loadObject($row);
    
    if (!$row) {
        echo "Böyle bir hasta kayıtlı değil";
    } else {
        echo $row->isim.' '.$row->soyisim;
    }
   
}

function getIzlemList($baslangictarih, $bitistarih, $ordering) {
    global $dbase, $limit, $limitstart;
    
    if ($baslangictarih) { 
        
        $cbaslangictarih = tarihCevir($baslangictarih);
        
       $where[] = "i.planlanantarih>='".$cbaslangictarih."'";
    }
    
    if ($bitistarih) {
        
        $cbitistarih = tarihCevir($bitistarih);
                                       
        $where[] = "i.planlanantarih<='".$cbitistarih."'";
    }
    
    if ($ordering) {
         $order = explode('-', $ordering);
         $orderingfilter = "ORDER BY ".$order[0]." ".$order[1];
     } else {
         $orderingfilter = "ORDER BY i.planlanantarih ASC, h.isim ASC, h.soyisim ASC, h.mahalle ASC";
     }
    
    $where[] = "i.planli=1 ";
    $where[] = "h.pasif=0 ";
    
    $islemtype = new Islem($dbase);


    $query = "SELECT COUNT(i.id) FROM #__izlemler AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
    ;
    $dbase->setQuery($query);
    $total = $dbase->loadResult(); 
     
     $pageNav = new pageNav( $total, $limitstart, $limit);
     
     $query = "SELECT i.*, h.isim, h.soyisim, isl.islemadi, m.mahalle FROM #__izlemler AS i "
     . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
     . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilacak "
     . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
     . ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "" )
     . "\n GROUP BY i.id "                                                               
     . $orderingfilter
     //. "\n ORDER BY i.planlanantarih ASC"
     ;
    
    
    $dbase->setQuery($query, $limitstart, $limit);
    $rows = $dbase->loadObjectList();
    
    IzlemList::getIzlemList($rows, $pageNav, $baslangictarih, $bitistarih);
}

function saveIzlem() {
         global $dbase, $limitstart, $limit, $cid;
    
    $row = new Izlem( $dbase );
    
    if ( !$row->bind( $_POST ) ) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $row->izlemiyapan = implode(',', $row->izlemiyapan);
    $row->yapilan = implode(',', $row->yapilan);
    $row->yapilacak = implode(',', $row->yapilacak);
    
    $row->izlemtarihi = tarihCevir($row->izlemtarihi);
    $row->planlanantarih = $row->planlanantarih ? tarihCevir($row->planlanantarih) : '';
    
    if (!$row->planli) {
        $row->planlanantarih = NULL;
        $row->yapilacak = NULL;    
    } 
        
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    
    $dbase->setQuery("UPDATE #__izlemler SET planli='0', planlanantarih='', yapilacak='' WHERE id=".$cid);
    $upd = $dbase->query();
    
    $link = "index.php?option=site&bolum=pizlemler";
    
    if ($limit) {
        $link .= "&limit".$limit;
    }
    
    if ($limitstart) {
        $link .= "&limitstart=".$limitstart;   
    }
    
    Redirect($link, 'İzlem Bilgileri kaydedildi');
    
}

function editIzlem($id) {
    global $dbase, $limitstart, $limit;
    
    $row = new Izlem($dbase);
    
    $row->load($id);
  
   
    if ($row->id) {
     $dbase->setQuery("SELECT isim, soyisim FROM #__hastalar WHERE tckimlik=".$row->hastatckimlik);
     $dbase->loadObject($hasta);   
    }
    
    //işlem seçme kutusu yapalım
    $dbase->setQuery("SELECT * FROM #__islem");
    $islemler = $dbase->loadObjectList();
    
    $islemtype = array();
    foreach ($islemler as $islem) {
        $islemtype[] = mosHTML::makeOption($islem->id, $islem->islemadi);
    }
    
    $row->yapilan = explode(',', $row->yapilan);
    $row->yapilacak = explode(',', $row->yapilacak);
    
    $isplanlanan = mosHTML::checkboxList($islemtype, 'yapilacak', '', 'value','text');
    $isyapilacak = mosHTML::checkboxList($islemtype, 'yapilan', '', 'value','text', $row->yapilacak);
    
    // PERSONEL SEÇME KUTULARI YAPALIM
    $dbase->setQuery("SELECT id, name FROM #__users ORDER BY name ASC");
    $personel = $dbase->loadObjectList();
    
    $islemiyapan = array();
    foreach ($personel as $per) {
        $islemiyapan[] = mosHTML::makeOption($per->id, $per->name);   
    }
    
    //$row->izlemiyapan = explode(',', $row->izlemiyapan);
        
    $perlist = mosHTML::checkboxList($islemiyapan, 'izlemiyapan', '', 'value', 'text');
    
    IzlemList::editIzlem($row, $limit, $limitstart, $isplanlanan, $isyapilacak, $hasta, $perlist);

}

function cancelIzlem() {
    global $dbase;
    
    $row = new Izlem( $dbase );
    $row->bind( $_POST );
    Redirect( 'index.php?option=site&bolum=izlemler');
}


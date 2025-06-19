<?php 
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

//include(dirname(__FILE__). '/html.php');

$year = intval(getParam($_REQUEST, 'year', date('Y')));
$month = intval(getParam($_REQUEST, 'month', date('m')));
$date = getParam($_REQUEST, 'date', date('Y-m-d'));
$task = getParam($_REQUEST, 'task');

switch($task) {
    
    default:
    case 'TakvimGetir': 
    getCalendar($year, $month); 
    break;
    
    case 'Islemler':
    getEvents($date);
    break;
} 
/* 
 * Generate event calendar in HTML format 
 */ 
function getCalendar($year, $month) {
    global $dbase;
    
    $dateMonth = $month ? $month : date('m');
    
    $dateYear =  $year ? $year : date('Y'); 
 
    $date = $dateYear.'-'.$dateMonth.'-01';
     
    $currentMonthFirstDay = date("N",strtotime($date));
     
    $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN,$dateMonth,$dateYear); 
    
    $totalDaysOfMonthDisplay = ($currentMonthFirstDay == 1)?($totalDaysOfMonth):($totalDaysOfMonth + ($currentMonthFirstDay - 1)); 
    
    $boxDisplay = ($totalDaysOfMonthDisplay <= 35)?35:42; 
     
    $prevMonth = date("m", strtotime('-1 month', strtotime($date))); 
    
    $prevYear = date("Y", strtotime('-1 month', strtotime($date))); 
    
    $totalDaysOfMonth_Prev = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear); 
?>
<div class="panel panel-primary"><!-- main panel -->
    
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-6"><h4><i class="fa-solid fa-calendar-days"></i> Planlı İzlem Takvimi</h4></div>
    <div class="col-xs-1" align="left"><a href="index.php?option=site&bolum=takvim&task=TakvimGetir&year=<?php echo date("Y",strtotime($date.' - 1 Month')); ?>&month=<?php echo date("m",strtotime($date.' - 1 Month')); ?>" class="title-bar__prev"></a> </div>
    <div class="col-xs-4" align="center"><h4><?php echo getMonthList($dateMonth); ?> <?php echo getYearList($dateYear); ?></h4></div>
    <div class="col-xs-1" align="right"><a href="index.php?option=site&bolum=takvim&task=TakvimGetir&year=<?php echo date("Y",strtotime($date.' + 1 Month')); ?>&month=<?php echo date("m",strtotime($date.' + 1 Month')); ?>" class="title-bar__next"></a> </div>
    </div>
    </div>
    
    <div class="panel-body">
 <div class="row">   
    <div class="col-sm-6"><!-- left side -->
    
        
    <div id="event_list">
    <?php echo getEvents(); ?>
    </div>
    
    
    </div><!-- left side -->
    
    <div class="col-sm-6"><!-- right side -->
    
    <div class="panel panel-success"><!-- left panel -->
    <div class="panel-heading"><h4><i class="fa-solid fa-calendar"></i> Takvim</h4></div>
    <div class="panel-body">
        <section class="calendar__days"> 
            <section class="calendar__top-bar"> 
                <span class="top-bar__days">Pazartesi</span> 
                <span class="top-bar__days">Salı</span> 
                <span class="top-bar__days">Çarşamba</span> 
                <span class="top-bar__days">Perşembe</span> 
                <span class="top-bar__days">Cuma</span> 
                <span class="top-bar__days">Cumartesi</span> 
                <span class="top-bar__days">Pazar</span> 
            </section> 
             <?php  
                $dayCount = 1; 
                $eventNum = 0; 
                 
                echo '<section class="calendar__week">'; 
                for($cb=1;$cb<=$boxDisplay;$cb++){ 
                    if(($cb >= $currentMonthFirstDay || $currentMonthFirstDay == 1) && $cb <= ($totalDaysOfMonthDisplay)){ 
                        // Current date 
                        $currentDate = $dateYear.'-'.$dateMonth.'-'.$dayCount; 
                         
                        // O güne ait planlı işlemlerin sayısı 
                        $dbase->setQuery("SELECT planlanantarih FROM #__izlemler WHERE planlanantarih = '".strtotime($currentDate)."' AND planli = 1"); 
                        $rows = $dbase->loadObjectList();
                        $eventNum = count($rows);
                        
                        // O güne ait planlı pansuman sayısı
                        $w = date('w', strtotime($currentDate));
                        $dbase->setQuery("SELECT COUNT(h.id) FROM #__hastalar AS h "
                        . "\n WHERE h.pansuman=1 AND FIND_IN_SET($w, h.pgunleri) AND h.pasif=0 ");
                        $panNum = $dbase->loadResult(); 
                        
                        // O gün yapılan ve sisteme girilen izlem sayısı
                        $dbase->setQuery("SELECT COUNT(id) FROM #__izlemler WHERE izlemtarihi=".strtotime($currentDate)." AND yapildimi=1");
                        $totali = $dbase->loadResult();
                         
                        // Define date cell color 
                        if(strtotime($currentDate) == strtotime(date("Y-m-d"))){ 
                            echo ' 
                                <div class="calendar__day today" onclick="javascript:getEvents(\''.$currentDate.'\');"> 
                                    <span class="calendar__date">'.$dayCount.'</span> 
                                    <span class="calendar__task calendar__task--today">'.$eventNum.' İzlem<br>'.$panNum.' Pansuman<br> Yapılan '.$totali.' İzlem</span> 
                                </div> 
                            '; 
                        }elseif($eventNum > 0){ 
                            echo ' 
                                <div class="calendar__day event" onclick="javascript:getEvents(\''.$currentDate.'\');"> 
                                    <span class="calendar__date">'.$dayCount.'</span> 
                                    <span class="calendar__task">'.$eventNum.' İzlem<br>'.$panNum.' Pansuman<br> Yapılan '.$totali.' İzlem</span> 
                                </div> 
                            '; 
                        }else{ 
                            echo ' 
                                <div class="calendar__day no-event" onclick="javascript:getEvents(\''.$currentDate.'\');"> 
                                    <span class="calendar__date">'.$dayCount.'</span> 
                                    <span class="calendar__task">'.$eventNum.' İzlem<br>'.$panNum.' Pansuman<br> Yapılan '.$totali.' İzlem</span> 
                                </div> 
                            '; 
                        } 
                        $dayCount++; 
                    }else{ 
                        if($cb < $currentMonthFirstDay){ 
                            $inactiveCalendarDay = ((($totalDaysOfMonth_Prev-$currentMonthFirstDay)+1)+$cb); 
                            $inactiveLabel = ''; 
                        }else{ 
                            $inactiveCalendarDay = ($cb-$totalDaysOfMonthDisplay); 
                            $inactiveLabel = ''; 
                        } 
                        echo ' 
                            <div class="calendar__day inactive"> 
                                <span class="calendar__date">'.$inactiveCalendarDay.'</span> 
                                <span class="calendar__task">'.$inactiveLabel.'</span> 
                            </div> 
                        '; 
                    } 
                    echo ($cb%7 == 0 && $cb != $boxDisplay)?'</section><section class="calendar__week">':''; 
                } 
                echo '</section>'; 
            ?> 
        </section> 
    </div>
    </div>
    
    </div><!-- right side -->
 </div>   
    
    </div>
</div> <!-- main panel --> 
    <script>  
        function getEvents(date){ 
            $.ajax({
                url:'index2.php?&option=site&bolum=takvim&task=Islemler&date='+date,
                type:'GET',
                success:function(result){
                    console.log(result);
                    $('#event_list').html(result);
                }  
            }); 
        } 
    </script> 
<?php 
}    
 
/* 
 * Generate months options list for select box 
 */ 
function getMonthList($selected = ''){

    $monthnames = array('1'=>'Ocak', '2'=>'Şubat','3'=>'Mart','4'=>'Nisan','5'=>'Mayıs','6'=>'Haziran','7'=>'Temmuz','8'=>'Ağustos','9'=>'Eylül','10'=>'Ekim','11'=>'Kasım','12'=>'Aralık'); 
    /*
    $options = ''; 
    for($i=1;$i<=12;$i++) 
    { 
        $value = ($i < 10)?'0'.$i:$i; 
        $selectedOpt = ($value == $selected) ? 'selected':''; 
        $options .= '<option value="'.$value.'" '.$selectedOpt.' >'.$monthnames[$i].'</option>'; 
    } 
    return $options;
    */
    return $monthnames[$selected]; 
} 
 
/* 
 * Generate years options list for select box 
 */ 
function getYearList($selected = ''){
/* 
    $yearInit = !empty($selected)?$selected:date("Y"); 
    $yearPrev = ($yearInit - 1); 
    $yearNext = ($yearInit + 1); 
    $options = ''; 
    for($i=$yearPrev;$i<=$yearNext;$i++){ 
        $selectedOpt = ($i == $selected)?'selected':''; 
        $options .= '<option value="'.$i.'" '.$selectedOpt.' >'.$i.'</option>'; 
    } 
    return $options; 
    */
    return $selected;
} 
 
/* 
 * Generate events list in HTML format 
 */ 
function getEvents($date=''){
    global $dbase;
     
    $date = $date ? $date : date("Y-m-d");
    
    $monthnames = array('01'=>'Ocak', '02'=>'Şubat','03'=>'Mart','04'=>'Nisan','05'=>'Mayıs','06'=>'Haziran','07'=>'Temmuz','08'=>'Ağustos','09'=>'Eylül','10'=>'Ekim','11'=>'Kasım','12'=>'Aralık'); 
    $daynames = array('0' => 'Pazar', '1' => 'Pazartesi', '2' => 'Salı', '3' => 'Çarşamba', '4' => 'Perşembe', '5' => 'Cuma', '6' => 'Cumartesi');
     
    $eventListHTML = '<div class="panel panel-info"><!-- left panel -->
    <div class="panel-heading"><h4><i class="fa-solid fa-calendar-check"></i> Planlı İşlemler: '.date('j', strtotime($date)).' '.$monthnames[date('m', strtotime($date))].' '.date('Y', strtotime($date)).', '.$daynames[date('w', strtotime($date))].'</h4></div>'; 
     
    // Planlanan izlemleri alalım
    $dbase->setQuery("SELECT i.*, h.id AS hastaid, h.isim, h.soyisim, h.gecici, h.cinsiyet, isl.islemadi, m.mahalle, ilc.ilce FROM #__izlemler AS i "
    . "\n LEFT JOIN #__hastalar AS h ON h.tckimlik=i.hastatckimlik "
    . "\n LEFT JOIN #__islem AS isl ON isl.id=i.yapilacak "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS ilc ON ilc.id=h.ilce "
    . "\n WHERE i.planlanantarih = '".strtotime($date)."' AND i.planli = 1"
    . "\n ORDER BY h.isim ASC, h.soyisim ASC, h.mahalle ASC "
    );
    $rows = $dbase->loadObjectList();
     
    if(count($rows) > 0){ 
         $eventListHTML .= '<table class="table table-striped table-hover"><tr>';
         $eventListHTML .= '<thead>';
            $eventListHTML .= '<th>Sıra</th>';
            $eventListHTML .= '<th>Hasta Adı Soyadı</th>';
            $eventListHTML .= '<th>TC Kimlik</th>';
            $eventListHTML .= '<th>Mahalle</th>'; 
            $eventListHTML .= '<th>Yapılacak İşlem</th>';
            $eventListHTML .= '</tr>';
            $eventListHTML .= '</thead>'; 
         
        $i = 1;
        foreach ($rows as $row) {

            $eventListHTML .= '<tr>'; 
            $eventListHTML .= '<td>'.$i.'</td>';
            $eventListHTML .= '<td><div class="dropdown">';
            $eventListHTML .= '<a class="dropdown-toggle" href="#" data-toggle="dropdown">'.$row->isim.' '.$row->soyisim.'</a>';
            $eventListHTML .= '<ul class="dropdown-menu">';
            $eventListHTML .= '<li><a href="index.php?option=site&bolum=hastalar&task=show&id='.$row->hastaid.'">Bilgileri Göster</a></li>';
            $eventListHTML .= '<li><a href="index.php?option=site&bolum=hastalar&task=edit&id='.$row->hastaid.'">Bilgileri Düzenle</a></li>';
            $eventListHTML .= '<li class="divider"></li>';
            $eventListHTML .= '<li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc='.$row->hastatckimlik.'">İzlemlerini Göster</a></li>';
            $eventListHTML .= '<li><a href="index.php?option=site&bolum=pizlemler&task=edit&id='.$row->id.'">Bu İzlemi Gir</a></li>';
            $eventListHTML .= '<li><a href="index.php?option=site&bolum=pizlemler&task=delete&id='.$row->id.'">Bu İzlemi Sil</a></li>';
            $eventListHTML .= '</ul>';
            $eventListHTML .= '</div></td>';
            $eventListHTML .= '<td>'.$row->hastatckimlik.'</td>';
            $eventListHTML .= '<td>'.$row->mahalle.' <span class="label label-success">'.$row->ilce.'</span></td>';
            $eventListHTML .= '<td>'.$row->islemadi.'</td>';
 
            $eventListHTML .= '</tr>';

        $i++;
        } 
         $eventListHTML .= '</table>';
        
    } else {
        $eventListHTML .= '<div class="panel-body">';
        $eventListHTML .= 'Bu gün için planlanmış izlem yok';
        $eventListHTML .= '</div>';
    }
     $eventListHTML .= '</div>';   
    echo $eventListHTML; 
    
        // Pansuman listesindeki hastaları alalım
    $today = date('w', strtotime($date));
    $dbase->setQuery("SELECT h.id, h.tckimlik, h.isim, h.soyisim, h.cinsiyet, h.gecici, m.mahalle, i.ilce FROM #__hastalar AS h "
    . "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
    . "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
    . "\n WHERE h.pansuman=1 AND FIND_IN_SET($today, h.pgunleri) AND h.pasif=0 "
    . "\n ORDER BY h.isim ASC, h.soyisim ASC, h.mahalle ASC "
    );
    $pows = $dbase->loadObjectList();
    
     $panListHTML = '<div class="panel panel-warning"><!-- left panel -->
    <div class="panel-heading"><h4><i class="fa-solid fa-calendar-check"></i> Planlı Pansumanlar: '.date('j', strtotime($date)).' '.$monthnames[date('m', strtotime($date))].' '.date('Y', strtotime($date)).', '.$daynames[date('w', strtotime($date))].'</h4></div>'; 
   
    
    if(count($pows) > 0) {

        $panListHTML .= '<table class="table table-striped table-hover"><tr>';
         $panListHTML .= '<thead>';
            $panListHTML .= '<th>Sıra</th>';
            $panListHTML .= '<th>Hasta Adı Soyadı</th>';
            $panListHTML .= '<th>TC Kimlik</th>';
            $panListHTML .= '<th>Mahalle</th>'; 
            $panListHTML .= '<th>Son İzlem Tarihi</th>';
            $panListHTML .= '</tr>';
            $panListHTML .= '</thead>'; 
        
        $k = 1;
        foreach($pows as $pow) {
            
            $dbase->setQuery("SELECT izlemtarihi FROM #__izlemler WHERE hastatckimlik=".$pow->tckimlik." ORDER BY izlemtarihi DESC LIMIT 1");
            $sonizlem = $dbase->loadResult();
            
             $panListHTML .= '<tr>'; 
            $panListHTML .= '<td>'.$k.'</td>';
            $panListHTML .= '<td><div class="dropdown">';                                                                            
            $panListHTML .= '<a class="dropdown-toggle" href="#" data-toggle="dropdown">'.$pow->isim.' '.$pow->soyisim.'</a>';
            $panListHTML .= '<ul class="dropdown-menu">';
            $panListHTML .= '<li><a href="index.php?option=site&bolum=hastalar&task=show&id='.$pow->id.'">Bilgileri Göster</a></li>';
            $panListHTML .= '<li><a href="index.php?option=site&bolum=hastalar&task=edit&id='.$pow->id.'">Bilgileri Düzenle</a></li>';
            $panListHTML .= '<li class="divider"></li>';
            $panListHTML .= '<li><a href="index.php?option=site&bolum=izlemler&task=izlemgetir&tc='.$pow->tckimlik.'">İzlemlerini Göster</a></li>';
            $panListHTML .= '<li><a href="index.php?option=site&bolum=izlemler&task=hedit&tc='.$pow->tckimlik.'">Pansumanı Gir</a></li>';
            $panListHTML .= '</ul>';
            $panListHTML .= '</div></td>';
            $panListHTML .= '<td>'.$pow->tckimlik.'</td>';
            $panListHTML .= '<td>'.$pow->mahalle.' <span class="label label-success">'.$pow->ilce.'</span></td>';
            $panListHTML .= '<td>'.tarihCevir($sonizlem, 1).'</td>';
            $panListHTML .= '</tr>';

        $k++;
        }
        $panListHTML .= '</table>';
    } else {
        $panListHTML .= '<div class="panel-body">';
        $panListHTML .= 'Bu gün için planlanmış pansuman yok';
        $panListHTML .= '</div>';
    }
    
     $panListHTML .= '</div>';   
    echo $panListHTML;
    
    
    
}
<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class geoMapHTML {
	
	static function getMap($rows) {
        ?>
        <div class="panel panel-<?php echo $hasta->pasif ? 'warning':'info';?>">
    
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-9"><h4><i class="fa-solid fa-earth-americas"></i> Hasta Adres Haritası</div>
    <div class="col-xs-3" align="right"><h4></h4></div>
    </div>
    </div>
    
    <div class="panel-body">
    
    <div id="map" class="map" style="height: 100vh; width: 1300px;">
    <script>
var map = L.map('map').setView([37.797348,29.076367], 11);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

<?php

foreach ($rows as $row) {
?>
var marker = L.marker([<?php echo $row->coords;?>], {alt: '<?php echo $row->isim.' '.$row->soyisim;?>'}).addTo(map).bindPopup('<a href="index.php?option=admin&bolum=hastalar&task=show&id=<?php echo $row->id;?>"><?php echo $row->isim.' '.$row->soyisim;?></a>');;
<?php

}
?>
</script>
    </div>
            
    </div class="panel-footer">
    
    </div>
        
        <?php
	}

}

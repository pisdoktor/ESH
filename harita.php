<?php
//  ;)
define('ERISIM', 1);

require( dirname( __FILE__ ) . '/global.php' );
require( dirname( __FILE__ ) . '/config.php' );

require_once( dirname( __FILE__ ) . '/includes/base.php' );
require_once(dirname( __FILE__ ) . '/includes/functions.php');

$dbase->setQuery("SELECT h.id, h.isim, h.soyisim, h.tckimlik, h.coords, h.kayityili, h.kayitay, (SELECT izlemtarihi FROM #__izlemler AS iz WHERE iz.hastatckimlik=h.tckimlik ORDER BY iz.izlemtarihi DESC LIMIT 1) as sonizlem FROM #__hastalar AS h WHERE h.pasif=0 AND h.coords>0");

$rows = $dbase->loadObjectList();

$aylar = array('' => 'Boş','01' => 'Ocak','02' => 'Şubat','03' => 'Mart','04' => 'Nisan','05' => 'Mayıs',
       '06' => 'Haziran','07' => 'Temmuz','08' => 'Ağustos','09' => 'Eylül','10' => 'Ekim','11' => 'Kasım','12' => 'Aralık');
      

foreach ($rows as $row) {
    
    $rcoords = explode(", ", $row->coords);
    $ncoords = $rcoords[1].",".$rcoords[0];
    
    $html = "<div>";
    $html.= "<span><b>".$row->isim." ".$row->soyisim."</b></span>";
    $html.= "</div>";
    
    $html.= "<div>";
    $html.= "<b>Kayıt Tarihi:</b> ".$aylar[$row->kayitay]." ".$row->kayityili.""; 
    $html.= "</div>";
    
    $html.= "<div>";
    $html.= "<b>Son İzlem Tarihi:</b> ".tarihCevir($row->sonizlem, 1).""; 
    $html.= "</div>";
    
    $data[] = "{
    coordinates: [".$ncoords."],
    properties: {
        id: ".$row->id.", 
        html: '".$html."'
        }
    }";

}

$list = implode(",", $data);
?>
<head>
    <meta http-equiv='X-UA-Compatible' content='IE=Edge' />
    <meta charset='UTF-8'>
    <title>ESH - Hasta Harita Ekranı</title>
    <link rel="stylesheet" type="text/css" href="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.0/maps/maps.css"/>
    <link rel='stylesheet' type='text/css' href='https://api.tomtom.com/maps-sdk-for-web/cdn/plugins/ZoomControls/2.1.6//ZoomControls.css'/>
    <script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.0/maps/maps-web.min.js"></script>
    <script src='https://api.tomtom.com/maps-sdk-for-web/cdn/plugins/ZoomControls/2.1.6//ZoomControls-web.js'></script>

</head>

  <body style="width: 100%; height: 100%; margin: 0; padding: 0;">

    <div id="map" style="width: 100%; height: 100%;"></div>

    <script>
     var map = tt.map({
        key: "6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB",
        container: "map",
        center: [29.085011, 37.780677],
        zoom: 10
     });
     
     var ttZoomControls = new tt.plugins.ZoomControls({
         className: 'margin-left-20'
     });
     
     map.addControl(new tt.FullscreenControl());
     map.addControl(ttZoomControls, 'top-right');
     
     var markersOnTheMap = {};
     var eventListenersAdded = false;
     
      var points = [<?php echo $list;?>];
      
      var geoJson = {
            type: 'FeatureCollection',
            features: points.map(function(point) {
                return {
                    type: 'Feature',
                    geometry: {
                        type: 'Point',
                        coordinates: point.coordinates
                    },
                    properties: point.properties
                };
            })
        };

        function refreshMarkers() {
            Object.keys(markersOnTheMap).forEach(function(id) {
                markersOnTheMap[id].remove();
                delete markersOnTheMap[id];
            });

            map.querySourceFeatures('point-source').forEach(function(feature) {
                if (feature.properties && !feature.properties.cluster) {
                    var id = parseInt(feature.properties.id, 10);
                    if (!markersOnTheMap[id]) {
                        var newMarker = new tt.Marker().setLngLat(feature.geometry.coordinates);
                        newMarker.addTo(map);
                        newMarker.setPopup(new tt.Popup({offset: 30}).setHTML(feature.properties.html));
                        markersOnTheMap[id] = newMarker;
                    }
                }
            });
        }
        map.on('load', function() {
            map.addSource('point-source', {
                type: 'geojson',
                data: geoJson,
                cluster: true,
                clusterMaxZoom: 15,
                clusterRadius: 50
            });
            
        map.addLayer({
                id: 'clusters',
                type: 'circle',
                source: 'point-source',
                filter: ['has', 'point_count'],
                paint: {
                    'circle-color': [
                        'step',
                        ['get', 'point_count'],
                        '#811A70',
                        5,
                        '#609504',
                        50,
                        '#74C67A',
                        100,
                        '#FAA6DA'
                    ],
                    'circle-radius': [
                        'step',
                        ['get', 'point_count'],
                        15,
                        4,
                        20,
                        7,
                        25
                    ],
                    'circle-stroke-width': 1,
                    'circle-stroke-color': 'black',
                    'circle-stroke-opacity': 0.5
                }
            });
            
            map.addLayer({
                id: 'cluster-count',
                type: 'symbol',
                source: 'point-source',
                filter: ['has', 'point_count'],
                layout: {
                    'text-field': '{point_count_abbreviated}',
                    'text-size': 14
                },
                paint: {
                    'text-color': 'white'
                }
            });

            map.on('data', function(e) {
                if (e.sourceId !== 'point-source' || !map.getSource('point-source').loaded()) {
                    return;
                }

                refreshMarkers();

                if (!eventListenersAdded) {
                    map.on('move', refreshMarkers);
                    map.on('moveend', refreshMarkers);
                    eventListenersAdded = true;
                }
            });
            
            map.on('click', 'clusters', function(e) {
                var features = map.queryRenderedFeatures(e.point, { layers: ['clusters'] });
                var clusterId = features[0].properties.cluster_id;
                map.getSource('point-source').getClusterExpansionZoom(clusterId, function(err, zoom) {
                    if (err) {
                        return;
                    }

                    map.easeTo({
                        center: features[0].geometry.coordinates,
                        zoom: zoom + 0.5
                    });
                });
            });

            map.on('mouseenter', 'clusters', function() {
                map.getCanvas().style.cursor = 'pointer';
            });

            map.on('mouseleave', 'clusters', function() {
                map.getCanvas().style.cursor = '';
            });
        });
     </script>

  </body>

</html>
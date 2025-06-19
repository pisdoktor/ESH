<?php
//  ;)
define('ERISIM', 1);
?>
<!DOCTYPE html>
<html class='use-all-space'>
<head>
    <meta http-equiv='X-UA-Compatible' content='IE=Edge' />
    <meta charset='UTF-8'>
    <title>Maps SDK for Web - Search with entry points</title>
    <meta name='viewport'
          content='width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no'/>
    <link rel='stylesheet' type='text/css' href='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/maps/maps.css'/>
    <link rel='stylesheet' type='text/css' href='./tomtom/assets/ui-library/index.css'/>
    <link rel='stylesheet' type='text/css' href='https://api.tomtom.com/maps-sdk-for-web/cdn/plugins/SearchBox/3.2.0//SearchBox.css'/>
    <link rel='stylesheet' type='text/css' href='./tomtom/assets/ui-library/icons-css/poi.css'/>
</head>
<body>
<style>
    .tt-popup {
        padding-top: 7px;
    }
    .tt-search-box-input {
        width: 400px;
    }
</style>
<div id='map' class='map'></div>
<script src='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/maps/maps-web.min.js'></script>
<script src='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/services/services-web.min.js'></script>
<script src='https://api.tomtom.com/maps-sdk-for-web/cdn/plugins/SearchBox/3.2.0//SearchBox-web.js'></script>
<script data-showable type='text/javascript' src='./tomtom/search/search-markers/search-marker.js'></script>
<script data-showable type='text/javascript' src='./tomtom/search/search-markers/entry-points.js'></script>
<script data-showable type='text/javascript' src='./tomtom/search/search-markers/search-markers-manager.js'></script>
<script data-showable type='text/javascript' src='./tomtom/mobile-or-tablet.js'></script>
<script>
    tt.setProductInfo('${productInfo.name}', '${productInfo.version}');
    var map = tt.map({
        key: '6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB',
        container: 'map',
        center: [29.085011, 37.780677],
        zoom: 10,
        dragPan: !isMobileOrTablet()
    });
    
    map.addControl(new tt.FullscreenControl());
    map.addControl(new tt.NavigationControl());

    var ttSearchBox = new tt.plugins.SearchBox(tt.services, {
        searchOptions: {
            key: '6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB',
            language: 'tr-TR',
            limit: 15
        },
        labels: {
            placeholder: 'Adres yazın',
            noResultsMessage: 'Sonuç bulunamadı'

        }
    });
    
    map.addControl(ttSearchBox, 'top-left');

    var searchMarkersManager;

    map.on('load', function() {
        searchMarkersManager = new SearchMarkersManager(map, { entryPoints: true, reverseGeocodeService: function(options) {
            options.key = searchApiKey;
            return tt.services.reverseGeocode(options);
        }});
    });

    function getBounds(data) {
        if (!data.viewport) {
            return;
        }
        var btmRight = [data.viewport.btmRightPoint.lng, data.viewport.btmRightPoint.lat];
        var topLeft = [data.viewport.topLeftPoint.lng, data.viewport.topLeftPoint.lat];
        return [btmRight, topLeft];
    }

    function fitToViewport(markerData) {
        if (!markerData || !markerData.length) {
            return;
        }

        var bounds = new tt.LngLatBounds();
        if (markerData instanceof Array) {
            markerData.forEach(function(marker) {
                bounds.extend(getBounds(marker));
            });
        } else {
            bounds.extend(getBounds(markerData));
        }
        map.fitBounds(bounds, { padding: 100, linear: true });
    }

    ttSearchBox.on('tomtom.searchbox.resultscleared', function() {
        searchMarkersManager.clear();
    });

    ttSearchBox.on('tomtom.searchbox.resultsfound', function(resp) {
        var results = resp.data.results.fuzzySearch.results;
        searchMarkersManager.draw(results);
        fitToViewport(results);
    });

    ttSearchBox.on('tomtom.searchbox.resultselected', function(resp) {
        searchMarkersManager.draw([resp.data.result]);
        searchMarkersManager.jumpToMarker(resp.data.result.id);
    });
</script>
</body>
</html>
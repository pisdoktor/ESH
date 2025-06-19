<?php
//  ;)
define('ERISIM', 1);

require( dirname( __FILE__ ) . '/global.php' );
require( dirname( __FILE__ ) . '/config.php' );

require_once( dirname( __FILE__ ) . '/includes/base.php' );
require_once(dirname( __FILE__ ) . '/includes/functions.php');

$uid = intval(getParam($_REQUEST, 'uid'));

$query = "SELECT i.ilce, m.mahalle, s.sokakadi, k.kapino FROM #__hastalar AS h "
. "\n LEFT JOIN #__ilce AS i ON i.id=h.ilce "
. "\n LEFT JOIN #__mahalle AS m ON m.id=h.mahalle "
. "\n LEFT JOIN #__sokak AS s ON s.id=h.sokak "
. "\n LEFT JOIN #__kapino AS k ON k.id=h.kapino "
. "\n WHERE h.id=".$uid
;

$dbase->setQuery($query);

$dbase->loadObject($data);


$adres = $data ? $data->mahalle.', '.$data->sokakadi.'., '.$data->kapino.', '.$data->ilce.', DENİZLİ':'';

?>

<html class='use-all-space'>
<head>
    <meta http-equiv='X-UA-Compatible' content='IE=Edge' />
    <meta charset='UTF-8'>
    <title>ESH - Konum Bulma Ekranı</title>
    <meta name='viewport' content='width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no' />
    <link rel='stylesheet' type='text/css' href='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/maps/maps.css'>
    <link rel='stylesheet' type='text/css' href='./tomtom/assets/ui-library/index.css'/>
    <link rel='stylesheet' type='text/css' href='./tomtom/assets/ui-library/icons-css/poi.css'/>
</head>
<body>
    <div class='map-view'>
        <form class='tt-side-panel js-form'>
            <header class='tt-side-panel__header'>
                <div class='tt-input-icon'>
                    <input class='tt-input' name='query' placeholder='Adres yazın' value="<?php echo $adres;?>">
                    <span class='tt-icon -search'></span>
                </div>
            </header>
            <div class='tt-tabs js-tabs'>
                <div class='tt-tabs__tabslist' role='tablist'>
                    <button role='tab' aria-selected='true' aria-controls='options' class='tt-tabs__tab'
                        type='button'>Parametreler</button>
                    <button role='tab' aria-selected='false' aria-controls='results' class='tt-tabs__tab'
                        type='button'>Sonuçlar</button>
                </div>
                
                <div role='tabpanel' class='tt-tabs__panel' id='options'>
                    <div class='tt-params-box'>
                        <header class='tt-params-box__header'>
                            Geokod Parametreleri
                        </header>
                        <div class='tt-params-box__content'> 
                            <label class='tt-form-label'>
                                Dil
                                <select class='js-language-select tt-select'></select>
                            </label> 
                            <label class='tt-form-label js-slider'>
                                Limit (<span class='js-counter'>10</span>)
                                <input class='tt-slider' name='limit' type='range' min='1' max='100' value='10'>
                            </label>
                            
                            <div class='tt-spacing-top-24 js-bias-container'>
                                <div class='tt-controls-group'>
                                    <div class='tt-controls-group__title'>
                                        Geobias
                                    </div>
                                    <div class='tt-controls-group__toggle'>
                                        <input id='toggle1' class='tt-toggle js-bias-toggle' type='checkbox' checked='false'>
                                        <label for='toggle1' class='tt-label'></label>
                                    </div>

                                    <div class='js-bias-controls'>
                                        <label class='tt-form-label js-slider'>
                                            Radius (<span class='js-counter'>0</span>m)
                                            <input class='tt-slider' name='radius' type='range' min='0' max='10000'
                                                value='0'>
                                        </label>
                                        <label class='tt-form-label'>
                                            Latitude
                                            <input class='tt-input' name='latitude' placeholder='e.g. 37.9717162'>
                                        </label>
                                        <label class='tt-form-label'>
                                            Longitude
                                            <input class='tt-input' name='longitude' placeholder='e.g. 23.7263126'>
                                        </label>
                                    </div>
                                </div>
                            </div>
                             
                            <div class='tt-spacing-top-24'>
                                <input type='submit' class='tt-button -primary tt-spacing-top-24' name='submit'
                                    value='Gönder'>
                            </div>
                        </div>
                    </div>
                </div>
                <div role='tabpanel' class='tt-tabs__panel' hidden='hidden' id='results'>
                    <div class='js-results' hidden='hidden'></div>
                    <div class='js-results-loader' hidden='hidden'>
                        <div class='loader-center'><span class='loader'></span></div>
                    </div>
                    <div class='tt-tabs__placeholder js-results-placeholder'>
                        SONUÇ YOK
                    </div>
                </div>
            </div>
        </form>
        <div id='map' class='full-map'></div>
    </div>
    <script type='text/javascript' src='./tomtom/search/search-results-parser.js'></script>
    <script type='text/javascript' src='./tomtom/search/dom-helpers.js'></script>
    <script type='text/javascript' src='./tomtom/formatters.js'></script>
    <script type='text/javascript' src='./tomtom/search/tabs.js'></script>
    <script type='text/javascript' src='./tomtom/search/results-manager.js'></script>
    <script type='text/javascript' src='./tomtom/search/languages.js'></script>
    <script type='text/javascript' src='./tomtom/info-hint.js'></script>
    <script type='text/javascript' src='./tomtom/search/slider.js'></script>
    <script type='text/javascript' src='./tomtom/search/side-panel.js'></script>
    <script type='text/javascript' src='./tomtom/tail.select.min.js'></script>
    <script type='text/javascript' src='./tomtom/tail-selector.js'></script>
    <script src='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/maps/maps-web.min.js'></script>
    <script src='https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.25.1/services/services-web.min.js'></script>
    <script type='text/javascript' src='./tomtom/search/search-markers/search-marker.js'></script>
    <script type='text/javascript' src='./tomtom/search/search-markers/search-markers-manager.js'></script>
    <script type='text/javascript' src='./tomtom/mobile-or-tablet.js'></script>
    <script>
        tt.setProductInfo('<your-product-name>', '<your-product-version>');
        var map = tt.map({
            key: '6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB',
            container: 'map',
            center: [29.085011, 37.780677],
            zoom: 10,
            dragPan: !isMobileOrTablet()
        });
        
        map.addControl(new tt.FullscreenControl({ container: document.querySelector('body') }));
        map.addControl(new tt.NavigationControl());

        new SidePanel('.tt-side-panel', map);
        var searchMarkersManager = new SearchMarkersManager(map);
        var tabs = new Tabs('.js-tabs');
        Array.prototype.slice.call(document.querySelectorAll('.js-slider'))
            .forEach(function(slider) {
                new Slider(slider);
            });

        var languageSelector = new TailSelector(searchLanguages, '.js-language-select', 'tr-TR');

        function Geocode() {
            this.domHelpers = DomHelpers;
            this.searchResultsParser = SearchResultsParser;
            this.formatters = Formatters;
            this.resultsManager = new ResultsManager();
            this.errorHint = new InfoHint('error', 'bottom-center', 3000)
                .addTo(document.getElementById('map'));

            this.elements = {
                language: languageSelector.getElement(),
                biasContainer: document.querySelector('.js-bias-container'),
                biasPlaceholder: document.querySelector('.js-bias-placeholder'),
                biasControls: document.querySelector('.js-bias-controls'),
                geobiasToggle: document.querySelector('.js-bias-toggle'),
                form: document.querySelector('.js-form')
            };

            Array.prototype.slice.call(document.querySelectorAll('input'))
                .forEach(function(input) {
                    this.elements[input.name] = input;
                }.bind(this));

            this.state = {
                query: this.elements.query.value,
                language: 'tr-TR',
                limit: this.elements.limit.value,
                radius: this.elements.radius.value,
                isGeobiasActive: true,
                markers: {}
            };

            this.updateInputValue = this.updateInputValue.bind(this);
            this.updateSelectValue = this.updateSelectValue.bind(this);

            this.bindEvents();
        }

        Geocode.prototype.bindEvents = function() {
            this.elements.language.on('change', this.updateSelectValue.bind(this, 'language'));
            this.elements.query.addEventListener('change', this.updateInputValue.bind(this, 'query'));
            this.elements.latitude.addEventListener('change', this.updateInputValue.bind(this, 'latitude'));
            this.elements.longitude.addEventListener('change', this.updateInputValue.bind(this, 'longitude'));
            this.elements.limit.addEventListener('change', this.updateInputValue.bind(this, 'limit'));
            this.elements.radius.addEventListener('change', this.updateInputValue.bind(this, 'radius'));
            this.elements.geobiasToggle.addEventListener('click', this.toggleGeoBias.bind(this));
            this.elements.submit.addEventListener('click', this.handleSubmit.bind(this));
            this.elements.form.addEventListener('submit', this.handleSubmit.bind(this));

            map.on('load', this.updateBiasPosition.bind(this));
            map.on('moveend', this.updateBiasPosition.bind(this));
        };

        Geocode.prototype.updateBiasPosition = function() {
            var lat = Formatters.roundLatLng(map.getCenter().lat);
            var lng = Formatters.roundLatLng(map.getCenter().lng);
            this.elements.latitude.value = lat;
            this.elements.longitude.value = lng;
            this.state.latitude = lat;
            this.state.longitude = lng;
        };

        Geocode.prototype.updateInputValue = function(property, event) {
            this.state[property] = event.target.value;

            if (property === 'minFuzzyLevel' || property === 'maxFuzzyLevel') {
                this.validateMinMaxFuzzy();
            }
        };

        Geocode.prototype.updateSelectValue = function(property, selected) {
            var selectedKey = selected.key;
            this.state[property] = selectedKey;
        };

        Geocode.prototype.toggleGeoBias = function() {
            var isGeobiasActive = !this.state.isGeobiasActive;
            this.state.isGeobiasActive = isGeobiasActive;

            Array.prototype.slice.call(this.elements.biasControls.querySelectorAll('label, input'))
                .forEach(function(label) {
                    if (isGeobiasActive) {
                        label.removeAttribute('disabled');
                    } else {
                        label.setAttribute('disabled', 'true');
                    }
                });
        };

        Geocode.prototype.handleSubmit = function(event) {
            event.preventDefault();

            var callParameters = {
                key: '6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB',
                query: this.state.query,
                language: this.state.language,
                limit: this.state.limit
            };

            this.resultsManager.loading();
            searchMarkersManager.clear();
            if (this.state.query) {
                tabs.clickTab(document.querySelector('[aria-controls="results"]'));
            } else {
                this.resultsManager.resultsNotFound();
            }

            if (this.state.isGeobiasActive) {
                callParameters.radius = this.state.radius;
                callParameters.center = [this.state.longitude, this.state.latitude];
            }

            tt.services.geocode(callParameters)

                .then(this.handleResponse.bind(this))
                .catch(this.handleError.bind(this));
        };

        Geocode.prototype.handleResponse = function(response) {
            var resultsDocumentFragment = document.createDocumentFragment();
            if (response.results && response.results.length > 0) {
                this.resultsManager.success();

                Array.prototype.slice.call(response.results)
                    .forEach(function(result) {
                        var distance = this.searchResultsParser.getResultDistance(result);
                        var addressLines = this.searchResultsParser.getAddressLines(result);
                        var searchResult = this.domHelpers.createSearchResult(
                            addressLines[0],
                            addressLines[1],
                            distance ? this.formatters.formatAsMetricDistance(distance) : ''
                        );

                        var resultItem = this.domHelpers.createResultItem();
                        resultItem.appendChild(searchResult);
                        resultItem.setAttribute('data-id', result.id);
                        resultItem.addEventListener('click', this.handleSearchResultItemClick.bind(this));

                        resultsDocumentFragment.appendChild(resultItem);
                    }, this);

                searchMarkersManager.draw(response.results);

                map.fitBounds(searchMarkersManager.getMarkersBounds(), { padding: 50 });

                var resultList = this.domHelpers.createResultList();
                resultList.appendChild(resultsDocumentFragment);
                this.resultsManager.append(resultList);
            } else {
                this.resultsManager.resultsNotFound();
                this.errorHint.setMessage('Verilen parametrelerde sonuç yok');
            }
        };

        Geocode.prototype.handleError = function(error) {
            this.errorHint.setMessage(error.message);
        };

        Geocode.prototype.selectResultItem = function(resultItem) {
            Array.prototype.slice.call(document.querySelectorAll('.tt-results-list__item'))
                .forEach(function(item) {
                    item.classList.remove('-selected');
                });
            resultItem.classList.add('-selected');
        };

        Geocode.prototype.handleSearchResultItemClick = function(event) {
            var id = event.currentTarget.getAttribute('data-id');
            searchMarkersManager.openPopup(id);
            searchMarkersManager.jumpToMarker(id);
        };

        new Geocode();
        
        /**Reverse code*/
        function ReverseGeocode() {
            this.errorHint = new InfoHint('error', 'bottom-center', 3000)
                .addTo(document.getElementById('map'));

            this.loadingHint = new InfoHint('info', 'bottom-center', 3000)
                .addTo(document.getElementById('map'));

            this.isRequesting = false;
            this.clickPosition = null;
            this.marker = null;

            map.on('click', this.handleMapClick.bind(this));
        }

        ReverseGeocode.prototype.handleResponse = function(response) {
            this.errorHint.hide();

            var result = response.addresses[0];
            var popupAddress = document.createElement('strong');

            if (result && result.address.freeformAddress) {
                popupAddress.innerText = result.address.freeformAddress;
                popupAddress.innerText+= '\n'+Formatters.roundLatLng(result.position.lat)+', '+Formatters.roundLatLng(result.position.lng);
            } else {
                popupAddress.innerText = 'Adres yok';
            }

            var popupContent = document.createElement('div');
            popupContent.appendChild(popupAddress);

            this.setPopup(this.clickPosition);
            this.popup.setDOMContent(popupContent);
            this.loadingHint.hide();
        };

        ReverseGeocode.prototype.setPopup = function(lnglat) {
            this.popup = new tt.Popup({ offset: [0, -30] })
                .setLngLat(new tt.LngLat(lnglat[0], lnglat[1]));

            this.marker = new tt.Marker()
                .setLngLat(new tt.LngLat(lnglat[0], lnglat[1]));

            this.marker.getElement().classList.add('marker');
            this.marker.addTo(map);
            this.marker.setPopup(this.popup);
            this.marker.togglePopup();
        };

        ReverseGeocode.prototype.removePopup = function() {
            if (this.marker && this.popup) {
                this.marker.remove();
                this.marker = null;

                this.popup.remove();
                this.popup = null;
            }
        };

        ReverseGeocode.prototype.handleMapClick = function(event) {
            if (DomHelpers.checkIfElementOrItsParentsHaveClass(event.originalEvent.target, 'marker')) {
                return;
            }

            this.loadingHint.setMessage('Yükleniyor');
            this.clickPosition = event.lngLat.toArray();
            map.panTo(this.clickPosition);

            this.errorHint.hide();

            this.removePopup();

            if (this.isRequesting) {
                return;
            }

            this.isRequesting = true;

            tt.services.reverseGeocode({
                key: '6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB',
                position: this.clickPosition,
                language: 'tr-TR'
            })

                .then(this.handleResponse.bind(this))
                .catch(function(error) {
                    this.loadingHint.hide();
                    this.errorHint.setMessage(error.data ? error.data.errorText : error);
                }.bind(this))
                .finally(function() {
                    this.isRequesting = false;
                }.bind(this));
        };

        new ReverseGeocode();
    </script>
</body>
</html>
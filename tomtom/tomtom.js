tt.setProductInfo('<your-product-name>', '<your-product-version>');
var map = tt.map({
    key: '6Nw0E48SHVaoOxvAWxFVRYArWkTsUMeB',
    container: 'map',
    center: [29.085011, 37.780677],
    zoom: 11,
    dragPan: !isMobileOrTablet()
});
map.addControl(new tt.FullscreenControl({container: document.querySelector('body')}));
map.addControl(new tt.NavigationControl());
var roundLatLng = Formatters.roundLatLng;
/*
var welcomePopup = new tt.Popup({ className: 'tt-popup' })
    .setHTML('Click anywhere on the map to get position.')
    .setLngLat(new tt.LngLat(4.3705, 50.8515))
    .addTo(map);    
*/
var roadUseOptions = [
    'LocalStreet',
    'LimitedAccess',
    'Arterial',
    'Terminal',
    'Ramp',
    'Rotary'
];
new SidePanel('.tt-side-panel', map);
var radiusSlider = new Slider(document.querySelector('.js-radius-slider'));
var languageSelector = new TailSelector(searchLanguages, '.js-language-select', 'en-US');
var roadUseSelector = new TailSelector(
    roadUseOptions,
    '.js-road-use-select',
    undefined, {
        multiple: true,
        placeholder: 'Add options from the list',
        multiShowCount: false,
        multiContainer: '.js-road-use-container',
        deselect: true
    }
);
var headingDirectionGroup = new ButtonsGroup(document.getElementById('heading-dir'));
function ReverseGeocode() {
    this.errorHint = new InfoHint('error', 'bottom-center', 3000)
        .addTo(document.getElementById('map'));
    this.loadingHint = new InfoHint('info', 'bottom-center', 3000)
        .addTo(document.getElementById('map'));
    this.bindElements();
    this.state = {
        searchLanguage: 'en-US',
        radius: this.elements.radius.value,
        heading: null,
        position: [50.8515, 4.3705],
        isRequesting: false,
        preferredNumber: null,
        roadUse: null,
        returnRoadUse: false,
        speedLimit: false,
        matchType: false,
        allowNewLines: false,
        currentMarker: null
    };
    this.bindEvents();
}
ReverseGeocode.prototype.bindElements = function() {
    this.elements = {
        radius: radiusSlider,
        languageSelector: languageSelector.getElement(),
        headingSelector: headingDirectionGroup,
        preferredNumber: document.querySelector('.js-house-number'),
        roadUse: roadUseSelector.getElement()
    };
    Array.prototype.slice.call(document.querySelectorAll('input'))
        .forEach(function(input) {
            this.elements[input.name] = input;
        }.bind(this));
};
ReverseGeocode.prototype.handleHeadingChange = function(button) {
    var chosenHeading = parseInt(button.getAttribute('direction'), 10);
    if (chosenHeading === this.state.heading) {
        headingDirectionGroup.unselect();
        this.state.heading = null;
    } else {
        this.state.heading = chosenHeading;
    }
    this.handleRequest();
};
ReverseGeocode.prototype.bindEvents = function() {
    this.elements.radius.addEventListener('change', this.updateInputValue.bind(this, 'radius'));
    this.elements.preferredNumber.addEventListener('change', this.updateInputValue.bind(this, 'preferredNumber'));
    this.elements.headingSelector.onSelect(this.handleHeadingChange.bind(this));
    this.elements.roadUse.on('change', this.handleRequest.bind(this));
    this.elements.languageSelector.on('change', function(selected) {
        this.state.searchLanguage = selected.key;
        this.handleRequest();
    }.bind(this));
    Array.prototype.slice.call(document.querySelectorAll('.tt-checkbox'))
        .forEach(function(checkbox) {
            checkbox.addEventListener('change', function(event) {
                this.state[event.target.id] = event.target.checked;
                this.handleRequest();
            }.bind(this));
        }.bind(this));
    map.on('click', this.handleMapClick.bind(this));
};
ReverseGeocode.prototype.updateInputValue = function(property, event) {
    this.state[property] = event.target.value;
    this.handleRequest();
};
ReverseGeocode.prototype.getReverseGeocodeService = function() {
    return tt.services.reverseGeocode({
        key: '<your-tomtom-maps-API-key>',
        position: this.state.position,
        language: this.state.searchLanguage,
        radius: this.state.radius,
        heading: this.state.heading,
        roadUse: this.state.roadUse,
        allowFreeformNewline: this.state.allowNewLines,
        returnMatchType: this.state.matchType,
        returnRoadUse: this.state.returnRoadUse,
        returnSpeedLimit: this.state.speedLimit,
        number: this.state.preferredNumber
    });
};
ReverseGeocode.prototype.requestData = function() {
    if (this.state.isRequesting) {
        return;
    }
    this.state.isRequesting = true;
    this.getReverseGeocodeService()
        .then(this.handleResponse.bind(this))
        .catch(this.handleError.bind(this))
        .finally(function() {
            this.state.isRequesting = false;
        }.bind(this));
};
ReverseGeocode.prototype.handleError = function(error) {
    this.errorHint.setMessage(error.data ? error.data.errorText : error);
};
ReverseGeocode.prototype.handleResponse = function(response) {
    this.errorHint.hide();
    this.loadingHint.hide();
    var result = response.addresses[0];
    var popupAddress = document.createElement('strong');
    if (result && result.address.freeformAddress) {
        popupAddress.innerText = result.address.freeformAddress;
    } else {
        popupAddress.innerText = 'No address';
    }
    var popupContent = document.createElement('div');
    popupContent.appendChild(popupAddress);
    if (popupAddress.innerText !== 'No address') {
        var details = document.createElement('details');
        details.setAttribute('class', 'tt-details tt-spacing-top-8');
        var parametersTable = document.createElement('div');
        parametersTable.setAttribute('class', 'popup-table');
        Object.keys(result.address).forEach(function(key) {
            if (typeof result.address[key] !== 'object') {
                parametersTable.appendChild(this.createParameterRow(key, result.address[key]));
            }
        }.bind(this));
        if (result.matchType) {
            parametersTable.appendChild(this.createParameterRow('matchType', result.matchType));
        }
        details.appendChild(parametersTable);
        popupContent.appendChild(details);
    }
    var clickPosition = new tt.LngLat(this.state.position[0], this.state.position[1]);
    this.state.currentMarker = new tt.Marker()
        .setLngLat(result && result.position || clickPosition)
        .addTo(map)
        .setPopup(new tt.Popup({ offset: [0, -30] })
            .setDOMContent(popupContent)
            .setMaxWidth('none'));
    this.state.currentMarker.getElement().classList.add('marker');
    this.state.currentMarker.getPopup().addTo(map);
    if (popupAddress.innerText !== 'No address') {
        var ne = result.address.boundingBox.northEast;
        var sw = result.address.boundingBox.southWest;
        var bounds = new tt.LngLatBounds(sw, ne);
        map.fitBounds(bounds, { duration: 1000, padding: 100 });
    } else {
        map.flyTo({ center: clickPosition, duration: 1000 });
    }
};
ReverseGeocode.prototype.createParameterRow = function(key, value) {
    var tableRow = document.createElement('tr');
    var parameterKey = document.createElement('td');
    parameterKey.innerHTML = '<b>' + key + '</b>';
    var parameterValue = document.createElement('td');
    parameterValue.innerText = value;
    tableRow.appendChild(parameterKey);
    tableRow.appendChild(parameterValue);
    return tableRow;
};
ReverseGeocode.prototype.handleMapClick = function(event) {
    if (DomHelpers.checkIfElementOrItsParentsHaveClass(event.originalEvent.target, 'marker')) {
        return;
    }
    this.loadingHint.setMessage('Loading');
    var pos = event.lngLat.toArray();
    this.state.position = pos;
    this.elements.longitude.value = roundLatLng(pos[0]);
    this.elements.latitude.value = roundLatLng(pos[1]);
    this.handleRequest();
};
ReverseGeocode.prototype.handleRequest = function() {
    welcomePopup.remove();
    this.state.roadUse = Array.apply(null, document.querySelectorAll('div[class=select-handle]'))
        .map(function(item) {
            return item.innerHTML;
        });
    if (this.state.roadUse.length === 0) {
        this.state.roadUse = null;
    }
    if (this.state.currentMarker) {
        this.state.currentMarker.remove();
    }
    this.requestData();
};
new ReverseGeocode();
<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('google-map');

global $hide_place_fields;

$default_city           = golo_get_option('default_city', '');
$map_default_position   = golo_get_option('map_default_position', '');
$map_type               = golo_get_option('map_type', 'google_map');
$map_zoom_level         = golo_get_option('map_zoom_level', '15');
$type_single_place      = golo_get_option('type_single_place', 'type-1' );

$map_marker_icon_url    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';

$google_map_style = $openstreetmap_style = $mapbox_style = '';

if( $map_type == 'google_map' ) {
    $google_map_style       = golo_get_option('googlemap_style', '');
    $googlemap_type       = golo_get_option('googlemap_type', 'roadmap');
} else if( $map_type == 'openstreetmap' ) {
    $openstreetmap_style       = golo_get_option('openstreetmap_style', 'streets-v11');
    $openstreetmap_api_key      = Golo_Helper::golo_get_option('openstreetmap_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
} else {
    $mapbox_style       = golo_get_option('mapbox_style', 'streets-v11');
    $googlemap_api_key      = Golo_Helper::golo_get_option('mapbox_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
}

$lat = 59.325;
$lng = 18.070;

if( $map_default_position ) {
    if( $map_default_position['location'] ) {
        list( $lat, $lng )  = !empty($map_default_position['location']) ? explode( ',', $map_default_position['location'] ) : array('', '');
    }
}
?>

<div class="place-fields-wrap">
    <div class="place-fields place-city">
        <div class="form-group row">
            <?php if (!in_array('city_town', $hide_place_fields)) : ?>
            <div class="col-sm-4">
                <div class="form-group form-select golo-loading-ajax-wrap">
                    <label class="place-fields-title" for="city"><?php esc_html_e('City / Town*', 'golo-framework'); ?></label>
                    <select name="place_city" id="city" class="golo-place-city-ajax form-control nice-select wide">
                        <option value=""><?php esc_html_e('Select City / Town', 'golo-framework'); ?></option>
                        <?php golo_get_taxonomy_slug('place-city', $default_city); ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!in_array('new_place_city', $hide_place_fields)) : ?>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="place-fields-title" for="zip"><?php esc_html_e('Create New City', 'golo-framework'); ?></label>
                    <input type="text" id="search-city" class="form-control" name="new_place_city" placeholder="<?php esc_attr_e('Search City', 'golo-framework'); ?>" autofill="off" autocomplete="off">
                    <input type="hidden" class="form-control" name="custom_place_city">
                    <input type="hidden" class="form-control" name="custom_place_city_location">
                </div>
            </div>
            <?php endif; ?>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="place-fields-title"><?php esc_html_e('Time Zone', 'golo-framework'); ?></label>
                    <select name="place_timezone" id="place_timezone" class="form-control nice-select wide">
                        <?php echo wp_timezone_choice( get_wp_timezone() ); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!in_array('address', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields place-address">
        <div class="form-group">
            <label class="place-fields-title" for="search-location"><?php echo esc_html__('Place Address*', 'golo-framework'); ?></label>
            <div class="input-area">
                <input type="text" id="search-location" class="form-control" name="place_map_address" placeholder="<?php esc_attr_e('Full Address', 'golo-framework'); ?>" autocomplete="off">
                <a class="my-location" href="#"><i class='fas fa-location'></i></a>
            </div>
            <input type="hidden" class="form-control place-map-location" name="place_map_location"/>
            <div id="geocoder" class="geocoder"></div>
        </div>
    </div>
</div>

<div class="place-fields-wrap place-fields-map">
    <div class="place-fields-title">
        <h3><?php esc_html_e('Set Location on Map', 'golo-framework'); ?></h3>
        <div class="control-marker">
            <a href="#" id="unlock-pin">
                <i class="la la-unlock medium"></i>
                <span><?php esc_html_e('Unlock Pin Location', 'golo-framework'); ?></span>
            </a>
            <a href="#" id="lock-pin">                    
                <i class="la la-lock medium"></i>
                <span><?php esc_html_e('Lock Pin Location', 'golo-framework'); ?></span>
            </a>
        </div>
    </div>
    <div class="place-fields place-map">
        <?php if( $map_type == 'google_map' ){ ?>
            <div class="map_canvas maptype" id="map" data-maptype="<?php echo $map_type; ?>" style="height: 300px"></div>
        <?php } else if( $map_type == 'openstreetmap' ) { ?>
            <div id="openstreetmap_location" class="maptype" data-maptype="<?php echo $map_type; ?>" style="height: 300px;width: 100%;" data-key="<?php if( $openstreetmap_api_key ) { echo $openstreetmap_api_key; } ?>"></div>
        <?php } else { ?>
            <div id="mapbox_location" class="maptype" data-maptype="<?php echo $map_type; ?>" style="height: 300px;width: 100%;" data-key="<?php if( $googlemap_api_key ) { echo $googlemap_api_key; } ?>"></div>
        <?php } ?>
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        
        var maptype = jQuery( '.maptype' ).data( 'maptype' );
        
        if( maptype == 'google_map' ){
            
            var styles, google_map_style;
            var bounds = new google.maps.LatLngBounds();
            var silver = [
                {
                    elementType: 'geometry',
                    stylers: [{color: '#f5f5f5'}]
                },
                {
                    elementType: 'labels.icon',
                    stylers: [{visibility: 'off'}]
                },
                {
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#616161'}]
                },
                {
                    elementType: 'labels.text.stroke',
                    stylers: [{color: '#f5f5f5'}]
                },
                {
                    featureType: 'administrative.land_parcel',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#bdbdbd'}]
                },
                {
                    featureType: 'poi',
                    elementType: 'geometry',
                    stylers: [{color: '#eeeeee'}]
                },
                {
                    featureType: 'poi',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#757575'}]
                },
                {
                    featureType: 'poi.park',
                    elementType: 'geometry',
                    stylers: [{color: '#e5e5e5'}]
                },
                {
                    featureType: 'poi.park',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#9e9e9e'}]
                },
                {
                    featureType: 'road',
                    elementType: 'geometry',
                    stylers: [{color: '#ffffff'}]
                },
                {
                    featureType: 'road.arterial',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#757575'}]
                },
                {
                    featureType: 'road.highway',
                    elementType: 'geometry',
                    stylers: [{color: '#dadada'}]
                },
                {
                    featureType: 'road.highway',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#616161'}]
                },
                {
                    featureType: 'road.local',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#9e9e9e'}]
                },
                {
                    featureType: 'transit.line',
                    elementType: 'geometry',
                    stylers: [{color: '#e5e5e5'}]
                },
                {
                    featureType: 'transit.station',
                    elementType: 'geometry',
                    stylers: [{color: '#eeeeee'}]
                },
                {
                    featureType: 'water',
                    elementType: 'geometry',
                    stylers: [{color: '#9dcaef'}]
                },
                {
                    featureType: 'water',
                    elementType: 'labels.text.fill',
                    stylers: [{color: '#9e9e9e'}]
                }
            ];
    
            styles = silver;
    
            google_map_style = <?php echo json_encode($google_map_style); ?>;
    
            if ( google_map_style ) {
                styles = JSON.parse(google_map_style);
            }
    
            <?php if(!empty($lat) && !empty($lng)) : ?>
                var lat = parseFloat('<?php echo esc_attr($lat) ?>'), lng = parseFloat('<?php echo esc_attr($lng) ?>');
            <?php endif; ?>
    
            var marker;
            var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
            var isDraggable = w > 1024;
            var mapOptions = {
                zoom: 14,
                center: {lat: lat, lng: lng},
                mapTypeId: <?php echo "'" . $googlemap_type . "'"; ?>,
                draggable: isDraggable,
                styles: styles,
                mapTypeControl: false,
                streetViewControl : false,
                rotateControl: false,
                zoomControl: true,
                fullscreenControl: true,
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
    
            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: {lat: lat, lng: lng},
            });
    
            var geocoder = new google.maps.Geocoder;
    
            var infowindow = new google.maps.InfoWindow({
                maxWidth: 370,
            });
    
            initAutocomplete();
            controlMarker();
            // golo_my_location(map);
    
            function controlMarker() {
                // This event listener will call addMarker() when the map is clicked.
                map.addListener('click', function(event) {
                    if( jQuery('body .lock-marker').length == 0 ) {
                        marker.setPosition(event.latLng);
                        geocodeLatLng(geocoder, map, infowindow, event.latLng);
                        jQuery('#submit_place_form').find('input[name="place_map_location"]').val(event.latLng.lat() + ',' + event.latLng.lng());
                    }
                });
    
                google.maps.event.addListener(marker, 'dragend', function(event) { 
                    geocodeLatLng(geocoder, map, infowindow, event.latLng);
                    jQuery('#submit_place_form').find('input[name="place_map_location"]').val(event.latLng.lat() + ',' + event.latLng.lng());
                });
    
                google.maps.event.addListener(marker, 'click', function(event) { 
                    infowindow.open(map,marker);
                    jQuery('#submit_place_form').find('input[name="place_map_location"]').val(event.latLng.lat() + ',' + event.latLng.lng());
                });
            }
            
            function geocodeLatLng(geocoder, map, infowindow, latlng) {
                var findResult = function(results){
                    var result =  _.find(results, function(obj){
                        return obj.types[0] == 'locality' && obj.types[1] == "political";
                    });
                    if( !result ) {
                        var result =  _.find(results, function(obj){
                            return obj.types[0] == 'administrative_area_level_1' && obj.types[1] == "political";
                        });
                    }
                    return result ? result.short_name : null;
                };
    
                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            marker.setPosition(latlng);
                            var scale                = Math.pow(2, map.getZoom()),
                                offsety              = ( (50 / scale) || 0 ),
                                projection           = map.getProjection(),
                                markerPosition       = marker.getPosition(),
                                markerScreenPosition = projection.fromLatLngToPoint(markerPosition),
                                pointHalfScreenAbove = new google.maps.Point(markerScreenPosition.x, markerScreenPosition.y - offsety),
                                aboveMarkerLatLng    = projection.fromPointToLatLng(pointHalfScreenAbove);
                            map.panTo(aboveMarkerLatLng);
    
                            infowindow.close();
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, marker);
    
                            var city = findResult(results[0].address_components);
    
                            document.getElementById('search-location').value = results[0].formatted_address;
                        } else {
                            window.alert('No results found');
                        }
                    } else {
                        window.alert('Geocoder failed due to: ' + status);
                    }
                });
            }
    
            function golo_my_location(map) {
    
                var my_location = {};
                var my_lat = '';
                var my_lng = '';
    
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
    
                        my_lat = position.coords.latitude;
                        my_lng = position.coords.longitude;
    
                        my_location = { lat: parseFloat(my_lat),lng: parseFloat(my_lng) };
    
                    }, function() {
                        handleLocationError(true, infowindow, map.getCenter());
                    });
                } else {
                    // Browser doesn't support Geolocation
                    handleLocationError(false, infowindow, map.getCenter());
                }
    
                function CenterControl(controlDiv, map) {
    
                    // Set CSS for the control border.
                    const controlUI = document.createElement("div");
                    controlUI.style.backgroundColor = "#fff";
                    controlUI.style.border = "2px solid #fff";
                    controlUI.style.borderRadius = "3px";
                    controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
                    controlUI.style.cursor = "pointer";
                    controlUI.style.width = "40px";
                    controlUI.style.height = "40px";
                    controlUI.style.margin = "10px";
                    controlUI.style.textAlign = "center";
                    controlUI.title = "My location";
                    controlDiv.appendChild(controlUI);
    
                    // Set CSS for the control interior.
                    const controlText = document.createElement("div");
                    controlText.style.fontSize = "18px";
                    controlText.style.lineHeight = "37px";
                    controlText.style.paddingLeft = "5px";
                    controlText.style.paddingRight = "5px";
                    controlText.innerHTML = "<i class='fas fa-location'></i>";
                    controlUI.appendChild(controlText);
    
                    // Setup the click event listeners: simply set the map to Chicago.
                    controlUI.addEventListener('click', () => {
                        map.panTo(my_location);
                        geocodeLatLng(geocoder, map, infowindow, my_location);
                        jQuery('#submit_place_form').find('input[name="place_map_location"]').val(parseFloat(my_lat) + ',' + parseFloat(my_lng));
                    });
    
                    jQuery('.my-location').on('click', function(e) {
                        e.preventDefault();
                        map.panTo(my_location);
                        geocodeLatLng(geocoder, map, infowindow, my_location);
                        jQuery('#submit_place_form').find('input[name="place_map_location"]').val(parseFloat(my_lat) + ',' + parseFloat(my_lng));
                    });
                }
    
                const centerControlDiv = document.createElement("div");
                CenterControl(centerControlDiv, map);
    
                centerControlDiv.index = 1;
                map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(centerControlDiv);
    
                function handleLocationError(browserHasGeolocation, infowindow, pos) {
                    infowindow.setPosition(pos);
                    infowindow.setContent(browserHasGeolocation ? 'Error: The Geolocation service failed.' : 'Error: Your browser doesn\'t support geolocation.');
                    infowindow.open(map);
                }
    
            };
    
            jQuery('body').on('click', '.control-marker', function(e){
                e.preventDefault();
                jQuery(this).toggleClass('lock-marker');
                jQuery('.place-map').toggleClass('lock-marker');
                if( jQuery(this).hasClass('lock-marker') ) {
                    jQuery('#search-location').prop('disabled', true);
                }else{
                    jQuery('#search-location').prop('disabled', false);
                }
            });
    
            function setMapOnAll(map) {
                marker.setMap(map);
            }
    
            function clearMarkers() {
                setMapOnAll(null);
            }
            
            function showMarkers() {
                setMapOnAll(map);
            }
    
            function initAutocomplete() {
    
                // Create the search box and link it to the UI element.
                var input = document.getElementById('search-location');
                var autocomplete = new google.maps.places.Autocomplete(input);
    
                var options = {
                    types: ['(cities)'],
                };
    
                var input_city = document.getElementById('search-city');
                var autocomplete_city = new google.maps.places.Autocomplete(input_city, options);
    
                autocomplete.bindTo('bounds', map);
    
                autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);
    
                var findResult = function(results){
                    var result =  _.find(results, function(obj){
                        return obj.types[0] == 'locality' && obj.types[1] == "political";
                    });
                    if( !result ) {
                        var result =  _.find(results, function(obj){
                            return obj.types[0] == 'administrative_area_level_1' && obj.types[1] == "political";
                        });
                    }
                    return result ? result.short_name : null;
                };
    
                autocomplete_city.addListener('place_changed', function() {
                    var place = autocomplete_city.getPlace();
                    var city = findResult(place.address_components);
    
                    if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }
    
                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }
    
                    jQuery('#submit_place_form').find('input[name="custom_place_city"]').val(city);
                    jQuery('#submit_place_form').find('input[name="custom_place_city_location"]').val(place.geometry.location.lat() + ',' + place.geometry.location.lng());
                    jQuery('#submit_place_form').find('input[name="new_place_city"]').val(address);
                });
    
                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }
    
                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(12);  // Why 17? Because it looks good.
                    }
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);
    
                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }
    
                    jQuery('#submit_place_form').find('input[name="place_map_location"]').val(place.geometry.location.lat() + ',' + place.geometry.location.lng());
    
                    infowindow.setContent(address);
                    infowindow.open(map, marker);
                });
            }
            
        
        } else if( maptype == 'openstreetmap' ) {

            <?php if(!empty($lat) && !empty($lng)) : ?>
                var lat = parseFloat('<?php echo esc_attr($lat) ?>'), lng = parseFloat('<?php echo esc_attr($lng) ?>');
            <?php endif; ?>

            <?php if(!empty($map_zoom_level)) : ?>
                var zoom = <?php echo esc_attr($map_zoom_level) ?>;
            <?php else : ?>
                var zoom = 4;
            <?php endif; ?>
            
            var osm_api = jQuery( '#openstreetmap_location' ).data( 'key' );
            
            var map_location = new L.map('openstreetmap_location');

            map_location.setView([lat, lng], zoom);
            
            var titleLayer_id = 'mapbox/<?php echo $openstreetmap_style; ?>';
                
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + osm_api, {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                id: titleLayer_id,
                tileSize: 512,
                zoomOffset: -1,
                accessToken: osm_api
            }).addTo(map_location);
            
            var osm_marker = new L.marker([lat, lng], {draggable:'true'});
            
            map_location.addLayer(osm_marker);
            
            var searchControl = L.esri.Geocoding.geosearch().addTo(map_location);

            var results = L.layerGroup().addTo(map_location);
            
            searchControl.on('results', function (data) {
                results.clearLayers();
                for (var i = data.results.length - 1; i >= 0; i--) {
                  results.addLayer(osm_marker.setLatLng(new L.LatLng(data.results[i].latlng['lat'], data.results[i].latlng['lng']),{draggable:'true'}));
                    jQuery('#submit_place_form').find('input[name="place_map_location"]').val(data.results[i].latlng['lat'] + ',' + data.results[i].latlng['lng']);
                    jQuery('#submit_place_form').find('input[name="custom_place_city"]').val(data.results[i].properties['City']);
                    jQuery('#submit_place_form').find('input[name="custom_place_city_location"]').val(data.results[i].properties['DisplayY'] + ',' + data.results[i].properties['DisplayX']);
                    jQuery('#submit_place_form').find('input[name="new_place_city"]').val(data.results[i].properties['City']);
                    jQuery('#submit_place_form').find('input[name="place_map_address"]').val(data.results[i].text);
                }
            });
            
            osm_marker.on('dragend', function(event) {
                var latlng = event.target.getLatLng();
                osm_marker.setLatLng(new L.LatLng(latlng.lat, latlng.lng),{draggable:'true'});
                jQuery('#submit_place_form').find('input[name="place_map_location"]').val(latlng.lat + ',' + latlng.lng);
            });
            
            map_location.on('click', function(e){

                osm_marker.setLatLng(new L.LatLng(e.latlng.lat, e.latlng.lng),{draggable:'true'}).addTo(map_location);
                
                jQuery('#submit_place_form').find('input[name="place_map_location"]').val(e.latlng.lat + ',' + e.latlng.lng);
            });
            
        } else {
        
            var mapbox_api = jQuery( '#mapbox_location' ).data( 'key' );
                mapboxgl.accessToken = mapbox_api;
                
            var map_location = new mapboxgl.Map({
                container: 'mapbox_location',
                style: 'mapbox://styles/mapbox/<?php echo $mapbox_style; ?>',
                zoom: <?php echo $map_zoom_level; ?>,
                center: [<?php echo $lng; ?>, <?php echo $lat; ?>],
            });
            
            var geocoder = new MapboxGeocoder({
                accessToken: mapboxgl.accessToken,
                mapboxgl: mapboxgl
            });
            
            
            document.getElementById('geocoder').appendChild(
                geocoder.onAdd(map_location)
            );
            
            jQuery( '#search-location' ).each( function() {
                var val = jQuery( this ).attr( 'placeholder' );
                jQuery( this ).attr( 'placeholder', '' );
                jQuery( '.mapboxgl-ctrl-geocoder--input' ).attr( 'placeholder', val );
                jQuery( '.mapboxgl-ctrl-geocoder--input' ).attr( 'autocomplete', 'off' );
            });
            
            jQuery( '.mapboxgl-ctrl-geocoder--input' ).change( function() {
                var val = jQuery( this ).val();
                if( val != '' ){
                    jQuery( '#search-location-error' ).hide();
                    jQuery( 'input[name="place_map_address"]' ).val( val );
                }
            });
            
            var marker_location = new mapboxgl.Marker({
                draggable: true
            })
            .setLngLat([<?php echo $lng; ?>, <?php echo $lat; ?>])
            .addTo(map_location);
            
            controlMarker();
            // golo_my_location(map_location);
    
            function controlMarker() {
                // This event listener will call addMarker() when the map is clicked.
                map_location.on('click', function(event) {
                    if( jQuery('body .lock-marker').length == 0 ) {
                        var lngLat = marker_location.getLngLat();
                        marker_location.setLngLat(event.lngLat).addTo(map_location);
                        jQuery('#submit_place_form').find('input[name="place_map_location"]').val(lngLat.lat + ',' + lngLat.lng);
                    }
                });
                
                function onDragEnd() {
                    var lngLat = marker_location.getLngLat();
                    marker_location.setLngLat([lngLat.lng, lngLat.lat]);
                    jQuery('#submit_place_form').find('input[name="place_map_location"]').val(lngLat.lat + ',' + lngLat.lng);
                }
                 
                marker_location.on('dragend', onDragEnd);
            }
    
            function golo_my_location(map) {
    
                var my_location = {};
                var my_lat = '';
                var my_lng = '';
    
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
    
                        my_lat = position.coords.latitude;
                        my_lng = position.coords.longitude;
    
                        my_location = { lat: parseFloat(my_lat),lng: parseFloat(my_lng) };
    
                    }, function() {
                        handleLocationError(true, infowindow, map.getCenter());
                    });
                } else {
                    // Browser doesn't support Geolocation
                    handleLocationError(false, infowindow, map.getCenter());
                }
    
                function CenterControl(controlDiv, map) {
    
                    // Set CSS for the control border.
                    const controlUI = document.createElement("div");
                    controlUI.style.backgroundColor = "#fff";
                    controlUI.style.border = "2px solid #fff";
                    controlUI.style.borderRadius = "3px";
                    controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
                    controlUI.style.cursor = "pointer";
                    controlUI.style.width = "40px";
                    controlUI.style.height = "40px";
                    controlUI.style.margin = "10px";
                    controlUI.style.textAlign = "center";
                    controlUI.title = "My location";
                    controlDiv.appendChild(controlUI);
    
                    // Set CSS for the control interior.
                    const controlText = document.createElement("div");
                    controlText.style.fontSize = "18px";
                    controlText.style.lineHeight = "37px";
                    controlText.style.paddingLeft = "5px";
                    controlText.style.paddingRight = "5px";
                    controlText.innerHTML = "<i class='fas fa-location'></i>";
                    controlUI.appendChild(controlText);
    
                    // Setup the click event listeners: simply set the map to Chicago.
                    controlUI.addEventListener('click', () => {
                        jQuery('#submit_place_form').find('input[name="place_map_location"]').val(parseFloat(my_lat) + ',' + parseFloat(my_lng));
                    });
    
                    jQuery('.my-location').on('click', function(e) {
                        e.preventDefault();
                        
                        marker_location.setLngLat([parseFloat(my_lng), parseFloat(my_lat)]).addTo(map_location);
                            
                        map_location.flyTo({
                            center: [
                            parseFloat(my_lng),
                            parseFloat(my_lat)
                            ],
                            essential: true
                        });
                        
                        jQuery('#submit_place_form').find('input[name="place_map_location"]').val(parseFloat(my_lat) + ',' + parseFloat(my_lng));
                    });
                }
    
                const centerControlDiv = document.createElement("div");
                CenterControl(centerControlDiv, map);
    
                centerControlDiv.index = 1;
    
            };
    
            jQuery('body').on('click', '.control-marker', function(e){
                e.preventDefault();
                jQuery(this).toggleClass('lock-marker');
                jQuery('.place-map').toggleClass('lock-marker');
                if( jQuery(this).hasClass('lock-marker') ) {
                    jQuery('#search-location').prop('disabled', true);
                }else{
                    jQuery('#search-location').prop('disabled', false);
                }
            });

            geocoder.on('result', function(ev) {
                
                jQuery('#submit_place_form').find('input[name="place_map_location"]').val(ev.result.geometry['coordinates'][1] + ',' + ev.result.geometry['coordinates'][0]);
                jQuery( '.mapboxgl-marker:last-child' ).remove();

                if (ev.result.context) {
                    console.log(ev.result);
                    ev.result.context.map(function (idx, ele) {

                        if( idx['id'].split('.').shift() === 'region' ){
                            console.log(idx);
                            jQuery('#submit_place_form').find('input[name="custom_place_city"]').val(idx['text']);
                            jQuery('#submit_place_form').find('input[name="custom_place_city_location"]').val(ev.result.geometry['coordinates'][1] + ',' + ev.result.geometry['coordinates'][0]);
                            jQuery('#submit_place_form').find('input[name="new_place_city"]').val(idx['text']);
                        }
                    });
                }
            });
            
        }
    });
</script>
<?php endif; ?>
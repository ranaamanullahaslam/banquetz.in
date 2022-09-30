<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$place_id    = get_the_ID();
$place_title = get_the_title();

$place_meta_data = get_post_custom( $place_id );
$place_booking_type = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type'][0] : '';

if( $place_booking_type == 'info' ) {
    return;
}

wp_enqueue_script('google-map');

global $wpdb;

$title = $icon_url = $map_style = $price = $price_short = $price_unit = $price_prefix = $price_postfix = $img_src = $place_address = $link = $map_address = '';

$map_zoom_level         = golo_get_option('map_zoom_level', '15');
$type_single_place      = golo_get_option('type_single_place', 'type-1' );
$map_type               = golo_get_option('map_type', 'google_map');
$googlemap_type         = 'roadmap';
$openstreetmap_style = $mapbox_style = 'streets-v11';
if( $map_type == 'google_map' ){
    $google_map_style       = golo_get_option('googlemap_style', '');
    $googlemap_type       = golo_get_option('googlemap_type', 'roadmap');
} else if( $map_type == 'openstreetmap' ) {
    $openstreetmap_style           = golo_get_option('openstreetmap_style', 'streets-v11');
    $openstreetmap_api_key      = Golo_Helper::golo_get_option('openstreetmap_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
} else {
    $mapbox_style       = golo_get_option('mapbox_style', 'streets-v11');
    $googlemap_api_key      = Golo_Helper::golo_get_option('mapbox_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
}

$price_short    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short'][0] : '';
$price_range    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range'][0] : '';
$place_address  = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
$place_location = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', true);

$primary_term               = get_primary_taxonomy_id($place_id, 'place-categories');

$icon_marker = get_term_meta( $primary_term, 'place_categories_icon_marker', true );
if( !empty($icon_marker['url']) ) {
    $icon_url = $icon_marker['url'];
} else {
    $icon_url    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
}

if (empty($primary_term)) {

    $place_categories = get_the_terms( $place_id, 'place-categories');
    if( $place_categories ) {
        foreach ($place_categories as $cate) {
            $cate_id     = $cate->term_id;
            $icon_marker = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
            if( !empty($icon_marker['url']) ) {
                $icon_url = $icon_marker['url'];
                break;
            } else {
                $icon_url    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';
            }
        }
    }

}

$price = '';
$currency_sign = golo_get_option('currency_sign', '$');
$low_price     = golo_get_option('low_price', '$');
$medium_price  = golo_get_option('medium_price', '$$');
$high_price    = golo_get_option('high_price', '$$$');

if( $price_range && $price_range != 0 ){

    if( $price_range == 1 ){
        $price = esc_html__('Free', 'golo-framework');
    }
    if( $price_range == 2 ){
        $price = $low_price;
    }
    if( $price_range == 3 ){
        $price = $medium_price;
    }
    if( $price_range == 4 ){
        $price = $high_price;
    }
}

if( $price_short ){
    $price = golo_get_format_money( $price_short );
}

if (!empty($place_location['location'])) {
    list($lat, $lng) = explode(',', $place_location['location']);
}
if( $map_type == 'google_map' ){
    if( $place_location && $place_location['address'] )
    {
        $google_map_address_url = "http://maps.google.com/?q=" . $place_location['address'];
        $map_address = $place_location['address'];
    }
    else
    {
        $google_map_address_url = "http://maps.google.com/?q=" . $place_address;
        $map_address = $place_address;
    }
}
if( empty($map_address) ) {
    return;
}

// Rating
$rating = $total_reviews = $total_stars = 0;
$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 )";
$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id  AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
$get_comments   = $wpdb->get_results($comments_query);
if (!is_null($get_comments)) {
    foreach ($get_comments as $comment) {
        if ($comment->comment_approved == 1) {
            if( !empty($comment->meta_value) ){
                $total_reviews++;
            }
            if( $comment->meta_value > 0 ){
                $total_stars += $comment->meta_value;
            }
        }
    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }
}

$city_slug  = '';
$place_city = get_the_terms( $place_id, 'place-city');
if( $place_city ) {
    $city_slug = $place_city[0]->slug;
}

?>

<div class="place-map place-area">
    <div class="entry-heading">
        <h3 class="entry-title"><?php esc_html_e('Maps', 'golo-framework'); ?></h3>
    </div>
    
    <div class="entry-detail">
        <?php if( $map_type == 'google_map' ){ ?>
            <div id="golo-place-map" class="golo-place-map maptype" data-maptype="<?php echo $map_type; ?>" style="height: 300px;width: 100%;"></div>
        <?php } else if( $map_type == 'openstreetmap' ) { ?>
            <div id="openstreetmap_map" class="maptype" data-maptype="<?php echo $map_type; ?>" style="height: 300px;width: 100%;" data-key="<?php if( $openstreetmap_api_key ) { echo $openstreetmap_api_key; } ?>"></div>
        <?php } else { ?>
            <div id="mapbox_map" class="maptype" data-maptype="<?php echo $map_type; ?>" style="height: 300px;width: 100%;" data-key="<?php if( $googlemap_api_key ) { echo $googlemap_api_key; } ?>"></div>
        <?php } ?>
        
    </div>
</div>

<?php
    ob_start();?>
        <div class="golo-marker">
            <div class="inner-marker">
                <?php 
                    $attach_id      = get_post_thumbnail_id($place_id);
                    $thumb_src      = golo_image_resize_id( $attach_id, 120, 150, true);
                    $thumb_full_src = wp_get_attachment_image_src( $attach_id, 'full');
                    $no_image_src   = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
                    if (!$thumb_src) {
                        $thumb_src = $no_image_src;
                    }
                ?>
                <?php if( $attach_id ): ?>
                <div class="entry-thumbnail">
                    <a href="<?php echo get_permalink($place_id); ?>">
                        <img src="<?php echo esc_url($thumb_src) ?>" alt="<?php echo get_the_title($place_id); ?>" title="<?php echo get_the_title($place_id); ?>">
                    </a>
                </div>
                <?php endif; ?>
                <div class="entry-detail">
                    <div class="entry-head">
                        <?php if( $place_categories ) : ?>
                        <div class="place-cate list-item">
                            <?php 
                            foreach ($place_categories as $cate) {
                                $cate_link = get_term_link($cate, 'place-categories');
                                ?>
                                    <a href="<?php echo esc_url($cate_link); ?>?city=<?php echo esc_attr($city_slug); ?>"><?php echo esc_html($cate->name); ?></a>
                                <?php
                            }
                            ?>
                        </div>
                        <?php endif; ?>

                        <?php if( !empty($place_title) ): ?>
                        <div class="place-title">
                            <h3 class="entry-title"><a href="<?php echo get_permalink($place_id); ?>"><?php echo get_the_title($place_id); ?></a></h3>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="entry-bottom">
                        <div class="place-preview">
                        <?php if( !empty($my_review) ) { ?>
                            <div class="place-rating">
                                <span><?php echo esc_html($rating); ?></span>
                                <i class="la la-star"></i>
                            </div>
                            <?php } ?>
                            <span class="count-reviews">
                                <?php printf(_n('(%s review)', '(%s Reviews)', $total_reviews, 'golo-framework'), $total_reviews); ?>
                            </span>
                        </div>
                        <?php if( !empty($price) ) : ?>
                        <div class="place-price">
                            <span>                     
                                <?php echo esc_html($price); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    $html_content = ob_get_clean();
    $html_content = str_replace(PHP_EOL, ' ', $html_content);
    $html_content = preg_replace('/[\r\n]+/', "\n", $html_content);
    $html_content = preg_replace('/[ \t]+/', ' ', $html_content);
    $html_content = preg_replace('/\s+/', ' ', $html_content);
?>

<script>

    jQuery(document).ready(function () {
        
        var maptype = jQuery( '.maptype' ).data( 'maptype' );
        
        if( maptype == 'google_map' ){
            var element = document.getElementById('golo-place-map');
            if ( element != null ) {
                var styles, google_map_style;
                var bounds = new google.maps.LatLngBounds();
                var silver = [
                    {
                        "featureType": "landscape",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "stylers": [
                            {
                                "hue": "#00aaff"
                            },
                            {
                                "saturation": -100
                            },
                            {
                                "gamma": 2.15
                            },
                            {
                                "lightness": 12
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "lightness": 24
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "lightness": 57
                            }
                        ]
                    }
                ];
        
                styles = silver;
        
                <?php if(!empty($google_map_style)): ?>

                    google_map_style = <?php echo json_encode($google_map_style); ?>;

                <?php else : ?>
    
                    google_map_style = '';

                <?php endif; ?>
        
                if ( google_map_style ) {
                    styles = JSON.parse(google_map_style);
                }
        
                <?php if(!empty($lat) && !empty($lng)): ?>
                var lat = '<?php echo esc_attr($lat) ?>', lng = '<?php echo esc_attr($lng) ?>';
                var marker;
                var position = new google.maps.LatLng(lat, lng);
                var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
                var isDraggable = w > 1024;
                var mapOptions = {
                    mapTypeId: <?php echo "'" . $googlemap_type . "'"; ?>,
                    center: position,
                    draggable: true,
                    scrollwheel: true,
                    styles: styles,
                    mapTypeControl: false,
                    streetViewControl : true,
                    rotateControl: false,
                    zoomControl: true,
                    fullscreenControl: true,
                };
                var map = new google.maps.Map(document.getElementById("golo-place-map"), mapOptions);
                bounds.extend(position);
        
                marker_size = new google.maps.Size(40, 40);
                var marker_icon = {
                    url: '<?php echo esc_url($icon_url) ?>',
                    size: marker_size,
                    scaledSize: new google.maps.Size(40, 40),
                };
        
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    icon: marker_icon,
                    title: '<?php echo esc_html($title) ?>',
                    animation: google.maps.Animation.DROP
                });
                
                var infowindow = new google.maps.InfoWindow({
                    maxWidth: 370,
                });
        
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
                        });
        
                        jQuery('.my-location').on('click', function(e) {
                            e.preventDefault();
                            map.panTo(my_location);
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
                // golo_my_location(map);
        
                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent('<?php echo $html_content; ?>');
                    infowindow.open(map, this);
                });
        
                map.fitBounds(bounds);
                var boundsListener = google.maps.event.addListener((map), 'idle', function (event) {
                    this.setZoom(<?php echo esc_js($map_zoom_level); ?>);
                    google.maps.event.removeListener(boundsListener);
                });
                <?php else: ?>
                document.getElementById('golo-place-map').style.height = 'auto';
                <?php endif; ?>
            }
        
        } else if( maptype == 'openstreetmap' ) {
            
            var element = document.getElementById('openstreetmap_map');
            if ( element != null ) {
                var osm_api = jQuery( '#openstreetmap_map' ).data( 'key' );
                
                var stores = {
                    "type": "FeatureCollection",
                    "features": [
                        {
                            "type": "Feature",
                            "geometry": {
                              "type": "Point",
                              "coordinates": [
                                <?php echo $lat; ?>,
                                <?php echo $lng; ?>
                              ]
                            },
                            "properties": {
                                "iconSize": [40, 40],
                                "icon": <?php echo '"' . esc_url($icon_url) . '"'; ?>,
                                "title": <?php echo '"' . esc_html(get_the_title($place_id)) . '"'; ?>,
                                "url": <?php echo '"' . esc_url(get_permalink($place_id)) . '"'; ?>,
                                "cate": <?php echo '"' . esc_html($place_categories[0]->name) . '"'; ?>,
                                "rating": <?php echo '"' . esc_html($rating) . '"'; ?>,
                                "review": <?php echo '"' . esc_html($total_reviews) . '"'; ?>,
                                "price": <?php echo '"' . esc_html($price) . '"'; ?>,
                            }
                        }    
                    ]
                };
                
                stores.features.forEach(function(store, i){
                    store.properties.id = i;
                });
                
                var container = L.DomUtil.get('openstreetmap_map'); if(container != null){ container._leaflet_id = null; }
                        
                var osm_map = new L.map('openstreetmap_map');
                
                osm_map.on('load', onMapLoad);
    
                osm_map.setView([<?php echo $lat; ?>, <?php echo $lng; ?>], <?php echo $map_zoom_level; ?>);
    
                
                function onMapLoad(){
                    
                    var titleLayer_id = 'mapbox/<?php echo $openstreetmap_style; ?>';
                    
                    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + osm_api, {
                        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        id: titleLayer_id,
                        tileSize: 512,
                        zoomOffset: -1,
                        accessToken: osm_api
                    }).addTo(osm_map);
            
                    /**
                     * Add all the things to the page:
                     * - The location listings on the side of the page
                     * - The markers onto the map
                    */
                    addMarkers();
                
                    
                
                };
                
                function flyToStore(currentFeature) {
                    osm_map.flyTo(currentFeature.geometry.coordinates, osm_level);
                }
                
                /* This will let you use the .remove() function later on */
                if (!('remove' in Element.prototype)) {
                  Element.prototype.remove = function() {
                    if (this.parentNode) {
                      this.parentNode.removeChild(this);
                    }
                  };
                }
                
                function addMarkers() {
    
                    /* For each feature in the GeoJSON object above: */
                    stores.features.forEach(function(marker) {
                        /* Create a div element for the marker. */
                        var el = document.createElement('div');
                        /* Assign a unique `id` to the marker. */
                        el.id = "marker-" + marker.properties.id;
                        /* Assign the `marker` class to each marker for styling. */
                        el.className = 'marker';
                        el.style.backgroundImage = 'url(<?php echo '"' . esc_url($icon_url) . '"'; ?>)';
                        el.style.width = marker.properties.iconSize[0] + 'px';
                        el.style.height = marker.properties.iconSize[1] + 'px';
                        /**
                         * Create a marker using the div element
                         * defined above and add it to the map.
                        **/
                        var PlaceIcon = L.Icon.extend({
                            options: {
                                className:      'marker-' + marker.properties.id,
                                iconSize:       [40, 40],
                                shadowSize:     [50, 64],
                                iconAnchor:     [20, 20],
                                shadowAnchor:   [4, 62],
                                popupAnchor:    [0, -12]
                            }
                        });
                        var icon = new PlaceIcon({iconUrl: <?php echo '"' . esc_url($icon_url) . '"'; ?>});
                        var rating_html = '';
                        if( marker.properties.rating ) {
                            rating_html = 
                            '<div class="place-rating">' +
                                '<span>' + marker.properties.rating + '</span>' +
                                '<i class="la la-star"></i>' +
                            '</div>';
                        }
                        
                        new L.marker([marker.geometry.coordinates[0], marker.geometry.coordinates[1]], {icon: icon}).addTo(osm_map).bindPopup( '<div class="golo-marker"><div class="inner-marker">' +
                            '<div class="entry-detail">' +
                                '<div class="entry-head">' +
                                    '<div class="place-cate list-item">' +
                                        marker.properties.cate +
                                    '</div>' +
                                    '<div class="place-title">' +
                                        '<h3 class="entry-title"><a href="' + marker.properties.url + '">' + marker.properties.title + '</a></h3>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="entry-bottom">' +
                                    '<div class="place-preview">' +
                                        rating_html +
                                        '<span class="place-reviews">(' + marker.properties.review + ' reviews)</span>' +
                                    '</div>' +
                                    '<div class="place-price">' +
                                        '<span>' + marker.properties.price + '</span>' +
                                    '</div>' +
                                '</div>' + 
                            '</div>' +
                        '</div></div>', { maxWidth : 325 } );
                          
                        el.addEventListener('click', function(e){
                            /* Fly to the point */
                            flyToStore(marker);
                            /* Highlight listing in sidebar */
                            var activeItem = document.getElementsByClassName('active');
                            e.stopPropagation();
                            if (activeItem[0]) {
                                activeItem[0].classList.remove('active');
                            }
                        });
                    });
                }
            }
            
        } else {
            var element = document.getElementById('mapbox_map');
            if ( element != null ) {
    
                <?php if(!empty($lat) && !empty($lng)): ?>
                    var mapbox_api = jQuery( '#mapbox_map' ).data( 'key' );
                    mapboxgl.accessToken = mapbox_api;
                    
                    var mapbox_maps = new mapboxgl.Map({
                        container: 'mapbox_map',
                        style: 'mapbox://styles/mapbox/<?php echo $mapbox_style; ?>',
                        zoom: <?php echo $map_zoom_level; ?>,
                        center: [<?php echo $lng; ?>, <?php echo $lat; ?>],
                    });

                    mapbox_maps.addControl(new mapboxgl.NavigationControl());
                    
                    var stores = {
                        "type": "FeatureCollection",
                        "features": [
                            {
                                "type": "Feature",
                                "geometry": {
                                  "type": "Point",
                                  "coordinates": [
                                    <?php echo $lng; ?>,
                                    <?php echo $lat; ?>
                                  ]
                                },
                                "properties": {
                                    "iconSize": [40, 40],
                                    "icon": <?php echo '"' . esc_url($icon_url) . '"'; ?>,
                                    "url": <?php echo '"' . esc_url(get_permalink($place_id)) . '"'; ?>,
                                    "title": <?php echo '"' . esc_html(get_the_title($place_id)) . '"'; ?>,
                                    "cate": <?php echo '"' . esc_html($place_categories[0]->name) . '"'; ?>,
                                    "rating": <?php echo '"' . esc_html($rating) . '"'; ?>,
                                    "review": <?php echo '"' . esc_html($total_reviews) . '"'; ?>,
                                    "price": <?php echo '"' . esc_html($price) . '"'; ?>,
                                }
                            }    
                        ]
                    };
                    
                    stores.features.forEach(function(store, i){
                        store.properties.id = i;
                    });
                
                    /**
                    * Wait until the map loads to make changes to the map.
                    */
                    mapbox_maps.on('load', function (e) {
                        /**
                         * This is where your '.addLayer()' used to be, instead
                         * add only the source without styling a layer
                        */
                        mapbox_maps.addLayer({
                            "id": "locations",
                            "type": "symbol",
                            /* Add a GeoJSON source containing place coordinates and information. */
                            "source": {
                              "type": "geojson",
                              "data": stores
                            },
                            "layout": {
                              "icon-image": "",
                              "icon-allow-overlap": true,
                            }
                          });
                
                        /**
                         * Add all the things to the page:
                         * - The location listings on the side of the page
                         * - The markers onto the map
                        */
                        addMarkers();
         
                    });
                    
                    function flyToStore(currentFeature) {
                      mapbox_maps.flyTo({
                        center: currentFeature.geometry.coordinates,
                        zoom: <?php echo $map_zoom_level; ?>
                      });
                    }
                    
                    function createPopUp(currentFeature) {
                      var popUps = document.getElementsByClassName('mapboxgl-popup');
                      /** Check if there is already a popup on the map and if so, remove it */
                      if (popUps[0]) popUps[0].remove();
                      
                        var rating_html = '';
                        if( currentFeature.properties.rating ) {
                            rating_html = 
                            '<div class="place-rating">' +
                                '<span>' + currentFeature.properties.rating + '</span>' +
                                '<i class="la la-star"></i>' +
                            '</div>';
                        }
                    
                      var popup = new mapboxgl.Popup({ closeOnClick: false })
                        .setLngLat(currentFeature.geometry.coordinates)
                        .setHTML('<div class="golo-marker"><div class="inner-marker">' +
                        '<div class="entry-detail">' +
                            '<div class="entry-head">' +
                                '<div class="place-cate list-item">' +
                                    currentFeature.properties.cate +
                                '</div>' +
                                '<div class="place-title">' +
                                    '<h3 class="entry-title"><a href="' + currentFeature.properties.url + '">' + currentFeature.properties.title + '</a></h3>' +
                                '</div>' +
                            '</div>' +
                            '<div class="entry-bottom">' +
                                '<div class="place-preview">' +
                                    rating_html +
                                    '<span class="place-reviews">(' + currentFeature.properties.review + ' reviews)</span>' +
                                '</div>' +
                                '<div class="place-price">' +
                                    '<span>' + currentFeature.properties.price + '</span>' +
                                '</div>' +
                            '</div>' + 
                        '</div>' +
                    '</div></div>')
                        .addTo(mapbox_maps);
                    }
                    
                    /* This will let you use the .remove() function later on */
                    if (!('remove' in Element.prototype)) {
                      Element.prototype.remove = function() {
                        if (this.parentNode) {
                          this.parentNode.removeChild(this);
                        }
                      };
                    }
                    
                    
                    mapbox_maps.on('click', function(e) {
                      /* Determine if a feature in the "locations" layer exists at that point. */
                      var features = map.queryRenderedFeatures(e.point, {
                        layers: ['locations']
                      });
                      
                      /* If yes, then: */
                      if (features.length) {
                        var clickedPoint = features[0];
                        
                        /* Fly to the point */
                        flyToStore(clickedPoint);
                        
                        /* Close all other popups and display popup for clicked store */
                        createPopUp(clickedPoint);
                        
                      }
                    });
                    
                    
                    
                    function addMarkers() {
                      /* For each feature in the GeoJSON object above: */
                      stores.features.forEach(function(marker) {
                        /* Create a div element for the marker. */
                        var el = document.createElement('div');
                        /* Assign a unique `id` to the marker. */
                        el.id = "marker-" + marker.properties.id;
                        /* Assign the `marker` class to each marker for styling. */
                        el.className = 'marker';
                        el.style.backgroundImage = 'url(' + marker.properties.icon + ')';
                        el.style.width = marker.properties.iconSize[0] + 'px';
                        el.style.height = marker.properties.iconSize[1] + 'px';
                        /**
                         * Create a marker using the div element
                         * defined above and add it to the map.
                        **/
                        new mapboxgl.Marker(el, { offset: [0, -50/2] })
                          .setLngLat(marker.geometry.coordinates)
                          .addTo(mapbox_maps);
                          
                          el.addEventListener('click', function(e){
                          /* Fly to the point */
                          flyToStore(marker);
                          /* Close all other popups and display popup for clicked store */
                          createPopUp(marker);
                          /* Highlight listing in sidebar */
                          var activeItem = document.getElementsByClassName('active');
                          e.stopPropagation();
                          if (activeItem[0]) {
                            activeItem[0].classList.remove('active');
                          }
                        });
                      });
                    }
                <?php endif; ?>
            }
        }
});
</script>
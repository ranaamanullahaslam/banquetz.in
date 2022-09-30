var GOLO_Place_Map_Search = GOLO_Place_Map_Search || {};
(function ($) {
    'use strict';

    var golo_map, styles;
    var ajax_url       = golo_search_map_vars.ajax_url;
    var markers        = [];
    var search_markers = [];
    var filter_wrap    = '.place-search';

    var item_amount       = golo_search_map_vars.item_amount;
    var marker_image_size = golo_search_map_vars.marker_image_size;
    var google_map_style  = golo_search_map_vars.google_map_style;

    var drgflag = true;
    var refresh = true;
    var infobox;

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        drgflag = false;
        var is_mobile = true;
    }

    GOLO_Place_Map_Search = {
        init: function() {

            GOLO_Place_Map_Search.globalAction();

            $('body').on('click', '.maps-view', function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop: 0}, 500);
                $('body').css('overflow', 'hidden');
                $('.place-search').fadeIn();
                if( refresh == true ){
                    GOLO_Place_Map_Search.searchMap();
                }
            });

            $('body').on('click', '.place-search .btn-close', function(e) {
                e.preventDefault();
                $('body').css('overflow', 'auto');
                $('.place-search').fadeOut();
                refresh = false;
            });

            $('.place-search select.search-control').on('change', function() {
                GOLO_Place_Map_Search.searchMap();
            });
        },

        globalAction: function() {
            $('.golo-nice-select').niceSelect();
        },

        searchMap: function() {
            var city, title, taxonomy_name, current_term, location;
            var search_form = $(filter_wrap);

            // taxonomy filter
            var taxonomy_name       = search_form.find('input[name="taxonomy_name"]').val(),
                current_term        = search_form.find('input[name="current_term"]').val(),
                city                = search_form.find('input[name="city"]').val(),
                location            = search_form.find('input[name="place_location"]').val(),
                category            = search_form.find('select[name="category"]').val(),
                maptype             = $( '.maptype' ).data( 'maptype' ),
                review_status       = $( '.maptype' ).parents( '.place-search' ).data( 'review' );

            var marker_cluster         = null,
                googlemap_default_zoom = golo_search_map_vars.googlemap_default_zoom,
                not_found              = golo_search_map_vars.not_found,
                not_place              = golo_search_map_vars.not_place,
                clusterIcon            = golo_search_map_vars.clusterIcon,
                google_map_style       = golo_search_map_vars.google_map_style,
                googlemap_type         = golo_search_map_vars.googlemap_type,
                pin_cluster_enable     = golo_search_map_vars.pin_cluster_enable;

            if ( maptype == 'google_map' ) {
                
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
    
                if (google_map_style !== '') {
                    styles = JSON.parse(google_map_style);
                }
    
                var golo_search_map_option = {
                    scrollwheel: false,
                    scroll: {x: $(window).scrollLeft(), y: $(window).scrollTop()},
                    zoom: parseInt(googlemap_default_zoom),
                    mapTypeId: googlemap_type,
                    draggable: drgflag,
                    mapTypeControl: false,
                    fullscreenControl: true,
                    streetViewControl: true,
                    disableDefaultUI: false,
                    styles: styles,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_CENTER
                    },
                    streetViewControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_CENTER
                    },
                    fullscreenControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_CENTER
                    }
                };
    
                $('body').on('click', '.golo-zoomin', function(){
                    golo_map.setZoom(golo_map.getZoom() + 1);
                });
                $('body').on('click', '.golo-zoomout', function(){
                    golo_map.setZoom(golo_map.getZoom() - 1);
                });
    
                var golo_input_search = function(map) {
                    // Create the search box and link it to the UI element.
                    var input     = document.getElementById('pac-input');
                    var searchBox = new google.maps.places.SearchBox(input);
                    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);
    
                    // Bias the SearchBox results towards current map's viewport.
                    map.addListener('bounds_changed', function() {
                        searchBox.setBounds(map.getBounds());
                    });
    
                    // Listen for the event fired when the user selects a prediction and retrieve
                    // more details for that place.
                    searchBox.addListener('places_changed', function() {
                        var places = searchBox.getPlaces();
    
                        if ( places.length == 0 ) {
                            return;
                        }
    
                        // Clear out the old markers.
                        search_markers.forEach(function(marker) {
                            marker.setMap(null);
                        });
                        search_markers = [];
    
                        // For each place, get the icon, name and location.
                        var bounds = new google.maps.LatLngBounds();
                        places.forEach(function(place) {
                            if ( !place.geometry ) {
                                console.log("Returned place contains no geometry");
                                return;
                            }
                            var icon = {
                                url: place.icon,
                                size: new google.maps.Size(25, 25),
                                origin: new google.maps.Point(0, 0),
                                anchor: new google.maps.Point(17, 34),
                                scaledSize: new google.maps.Size(25, 25)
                            };
    
                            // Create a marker for each place.
                            search_markers.push(new google.maps.Marker({
                                map: map,
                                icon: icon,
                                title: place.name,
                                position: place.geometry.location
                            }));
    
                            if (place.geometry.viewport) {
                                bounds.union(place.geometry.viewport);
                            } else {
                                bounds.extend(place.geometry.location);
                            }
                        });
    
                        map.fitBounds(bounds);
                    });
                }
    
                var golo_add_markers = function(props, map) {
                    var infowindow = new google.maps.InfoWindow({
                        maxWidth: 370,
                    });
                    $.each(props, function(i, prop) {
                        var latlng      = new google.maps.LatLng(prop.lat, prop.lng),
                            marker_url  = prop.marker_icon,
                            marker_size = new google.maps.Size(40, 40);
                        var marker_icon = {
                            url: marker_url,
                            size: marker_size,
                            scaledSize: new google.maps.Size(40, 40),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(7, 27)
                        };
    
                        var marker = new google.maps.Marker({
                            position: latlng,
                            url: '.place-' + prop.id,
                            map: map,
                            icon: marker_icon,
                            draggable: false,
                            title: 'marker' + prop.id,
                            animation: google.maps.Animation.DROP
                        });
    
                        var prop_title  = prop.data ? prop.data.post_title : prop.title;
                        var rating_html = '';
                        if( prop.rating ) {
                            rating_html = 
                            '<div class="place-rating">' +
                                '<span>' + prop.rating + '</span>' +
                                '<i class="la la-star"></i>' +
                            '</div>';
                        }
                        
                        var contentString = document.createElement("div");
                        contentString.className = 'golo-marker';
                        contentString.innerHTML = 
                        '<div class="inner-marker">' +
                            '<div class="entry-thumbnail">' +
                                '<a href="' + prop.url + '">' +
                                    '<img src="' + prop.image_url + '" alt="' + prop_title + '">' +
                                '</a>' +
                            '</div>' +
                            '<div class="entry-detail">' +
                                '<div class="entry-head">' +
                                    '<div class="place-cate list-item">' +
                                        prop.cate +
                                    '</div>' +
                                    '<div class="place-title">' +
                                        '<h3 class="entry-title"><a href="' + prop.url + '">' + prop_title + '</a></h3>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="entry-bottom">' +
                                    '<div class="place-preview ' + review_status + '">' +
                                        rating_html +
                                        '<span class="place-reviews">(' + prop.review + ' reviews)</span>' +
                                    '</div>' +
                                    '<div class="place-price">' +
                                        '<span>' + prop.price + '</span>' +
                                    '</div>' +
                                '</div>' + 
                            '</div>' +
                        '</div>';
                        google.maps.event.addListener(marker, 'click', function() {
                            infowindow.close();
                            infowindow.setContent(contentString);
                            infowindow.open(map,marker);
                            
                            var scale                = Math.pow(2, map.getZoom()),
                                offsety              = ( (30 / scale) || 0 ),
                                projection           = map.getProjection(),
                                markerPosition       = marker.getPosition(),
                                markerScreenPosition = projection.fromLatLngToPoint(markerPosition),
                                pointHalfScreenAbove = new google.maps.Point(markerScreenPosition.x, markerScreenPosition.y - offsety),
                                aboveMarkerLatLng    = projection.fromPointToLatLng(pointHalfScreenAbove);
                            map.panTo(aboveMarkerLatLng);
                        });
                        
                        markers.push(marker);
                    });
                };
                
            } else {
                // Begin Mapbox
            
                var golo_mapbox_add_markers = function(props, map) {
                    var mapbox_api = $( '#map' ).data( 'key' );
                    var mapbox_level = $( '#map' ).data( 'level' );
                    var mapbox_type = $( '#map' ).data( 'type' );
                    mapboxgl.accessToken = mapbox_api;
                    $( '.mapboxgl-canary' ).remove();
                    $( '.mapboxgl-canvas-container' ).remove();
                    $( '.mapboxgl-control-container' ).remove();
                    var features_info = [];
                    var lng_args = [];
                    var lat_args = [];
                    $.each(props, function(i, prop) {
              
                        features_info.push(
                            {
                                "type": "Feature",
                                "geometry": {
                                  "type": "Point",
                                  "coordinates": [
                                    prop.lng,
                                    prop.lat
                                  ]
                                },
                                "properties": {
                                    "iconSize": [40, 40],
                                    "id": prop.id,
                                    "icon": prop.marker_icon,
                                    "url": prop.url,
                                    "image_url": prop.image_url,
                                    "title": prop.title,
                                    "cate": prop.cate,
                                    "rating": prop.rating,
                                    "review": prop.review,
                                    "price": prop.price,
                                }
                            }
                        );
                        
                        lng_args.push(prop.lng);
                        lat_args.push(prop.lat);
                        
                    });
                    
                    var sum_lng = 0;
                    for( var i = 0; i < lng_args.length; i++ ){
                        sum_lng += parseInt( lng_args[i], 10 );
                    }
                    
                    var avg_lng = 0;
    				
    				if( sum_lng/lng_args.length ){
    					avg_lng = sum_lng/lng_args.length;
    				} 
                    
                    
                    var sum_lat = 0;
                    for( var i = 0; i < lat_args.length; i++ ){
                        sum_lat += parseInt( lat_args[i], 10 );
                    }
                    
                    var avg_lat = 0;
    				
    				if( sum_lat/lat_args.length ){
    					avg_lat = sum_lat/lat_args.length;
    				}
                    
                    var map = new mapboxgl.Map({
                        container: 'map',
                        style: 'mapbox://styles/mapbox/' + mapbox_type,
                        zoom: mapbox_level,
                        center: [avg_lng, avg_lat],
                    });
                    
                    var stores = {
                        "type": "FeatureCollection",
                        "features": features_info
                    };
                
                    /**
                    * Wait until the map loads to make changes to the map.
                    */
                    map.on('load', function (e) {
                        /**
                         * This is where your '.addLayer()' used to be, instead
                         * add only the source without styling a layer
                        */
                        map.addLayer({
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
                        
                        $('.map-event .area-places .place-item').each(function(i) {
                            var index = i;
                            
                            $(this).on('mouseenter', function() {
                                var title = $( this ).find( '.golo-add-to-wishlist' ).data( 'place-id' );
                                if(map) {
                                    if( paged > 1 ) {
                                        index = i + (item_amount * (paged - 1 ));
                                    }
                                    $( "#marker-" + title ).trigger( "click" );
                                }
                            });
        
                            $(this).on('mouseleave', function() {
                                $( ".mapboxgl-popup-close-button" ).trigger( "click" );
                            });
                        });
                    });
                    
                    function flyToStore(currentFeature) {
                      map.flyTo({
                        center: currentFeature.geometry.coordinates,
                        zoom: mapbox_level
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
                        '<div class="entry-thumbnail">' +
                            '<a href="' + currentFeature.properties.url + '">' +
                                '<img src="' + currentFeature.properties.image_url + '" alt="' + currentFeature.properties.title + '">' +
                            '</a>' +
                        '</div>' +
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
                                '<div class="place-preview ' + review_status + '">' +
                                    rating_html +
                                    '<span class="place-reviews">(' + currentFeature.properties.review + ' reviews)</span>' +
                                '</div>' +
                                '<div class="place-price">' +
                                    '<span>' + currentFeature.properties.price + '</span>' +
                                '</div>' +
                            '</div>' + 
                        '</div>' +
                    '</div></div>')
                        .addTo(map);
                    }
                    
                    /* This will let you use the .remove() function later on */
                    if (!('remove' in Element.prototype)) {
                      Element.prototype.remove = function() {
                        if (this.parentNode) {
                          this.parentNode.removeChild(this);
                        }
                      };
                    }
                    
                    
                    map.on('click', function(e) {
                      /* Determine if a feature in the "locations" layer exists at that point. */
                      var features = map.queryRenderedFeatures(e.point, {
                        layers: ['locations']
                      });
                      
                      /* If yes, then: */
                      if (features.length) {
                        var clickedPoint = features[0];
                        
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
                        new mapboxgl.Marker(el, { offset: [0, -23] })
                          .setLngLat(marker.geometry.coordinates)
                          .addTo(map);
                          
                          el.addEventListener('click', function(e){
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
                };

                // End Mapbox
            }

            

            $.ajax({
                dataType: 'json',
                url: ajax_url,
                data: {
                    'action': 'golo_place_search_map_ajax',
                    'title': title,
                    'city': city,
                    'location': location,
                    'item_amount': item_amount,
                    'marker_image_size': marker_image_size,
                    'taxonomy_name': taxonomy_name,
                    'current_term': current_term,
                    'category': category,
                },
                beforeSend: function () {
                    $('.place-search .golo-loading-effect').fadeIn();
                },
                success: function (data) {
                    
                    if ( maptype == 'google_map' ) {
                        golo_map = new google.maps.Map(document.getElementById('place-search-map'), golo_search_map_option);
                        golo_input_search(golo_map);
                        google.maps.event.trigger(golo_map, 'resize');
                        if( data.success === true ) {
                            if ( data.places ) {
                                var count_places = data.places.length;
                            }
                        }
                        if( count_places == 1 ) {
                            var boundsListener = google.maps.event.addListener((golo_map), 'bounds_changed', function (event) {
                                this.setZoom(parseInt(googlemap_default_zoom));
                                google.maps.event.removeListener(boundsListener);
                            });
                        }
                        if( google_map_style !== '' ) {
                            var styles = JSON.parse(google_map_style);
                            golo_map.setOptions({styles: styles});
                        }
                        var mapPosition = new google.maps.LatLng('', '');
                        golo_map.setCenter(mapPosition);
                        golo_map.setZoom(parseInt(googlemap_default_zoom));
                        google.maps.event.addListener(golo_map, 'tilesloaded', function () {
                            $('.place-search .golo-loading-effect').fadeOut();
                        });
    
                        if( data.success === true ) {
                            $('.place-search .no-result').fadeOut();
                            
                            markers.forEach(function(marker) {
                                marker.setMap(null);
                            });
                            markers = [];
                            golo_add_markers(data.places, golo_map);
                            golo_map.fitBounds(markers.reduce(function (bounds, marker) {
                                return bounds.extend(marker.getPosition());
                            }, new google.maps.LatLngBounds()));
    
                            google.maps.event.trigger(golo_map, 'resize');
    
                            if( golo_template_vars.googlemap_pin_cluster != 0 ) {
                                marker_cluster = new MarkerClusterer(golo_map, markers, {
                                    gridSize: 60,
                                    styles: [
                                        {
                                            url: clusterIcon,
                                            width: 66,
                                            height: 65,
                                            textColor: "#fff"
                                        }
                                    ]
                                });
                            }
                        }else{
                            $('.place-search .no-result').fadeIn();
                        }
                        golo_map.fitBounds(markers.reduce(function (bounds, marker) {
                            return bounds.extend(marker.getPosition());
                        }, new google.maps.LatLngBounds()));
                        google.maps.event.trigger(golo_map, 'resize');
                    } else {
                        $('.place-search .golo-loading-effect').fadeOut();
                        golo_mapbox_add_markers(data.places, map);
    
                        if( data.success === true ) {
                            golo_mapbox_add_markers(data.places, map);
                        }else{
                            $('.place-search .no-result').fadeIn();
                        }
                    }
                },
            });
        },

        addMarkerWithTimeout: function(position, timeout) {
            window.setTimeout(function() {
                markers.push(new google.maps.Marker({
                    position: position,
                    map: map,
                    animation: google.maps.Animation.DROP
                }));
            }, timeout);
        },
    }

    $(document).ready(function () {
        GOLO_Place_Map_Search.init();
    });
})(jQuery);
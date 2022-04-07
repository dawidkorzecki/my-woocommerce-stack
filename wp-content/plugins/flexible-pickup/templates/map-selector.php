<?php
/**
 * Map selector
 *
 * This template can be overridden by copying it to yourtheme/flexible-pickup/map-selector.php
 *
 * @author 		WP Desk
 * @package 	Flexible Pickup/Templates
 * @version     1.0.0
 */
?>
<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<html>
<head>
	<script type="text/javascript" src="<?php echo $jquery_script; ?>"></script>
	<script type="text/javascript">

		var pickup_points = <?php echo json_encode( $pickup_points ); ?>;
        var markers = [];

        function fpickup_init_map() {
            map = new google.maps.Map(document.getElementById('flexible_pickup_map'), {
                zoom: 17,
                mapTypeId: 'roadmap',
            });

            map.infowindow = new google.maps.InfoWindow();

            var bounds = new google.maps.LatLngBounds();

            infowindow = new google.maps.InfoWindow();

            for (i = 0; i < pickup_points.length; i++) {
                var latlng = { lat: parseFloat(pickup_points[i].lat), lng: parseFloat(pickup_points[i].lng) };
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    draggable:false
                });
                pickup_points[i].marker = marker;
                markers.push(marker);
                bounds.extend(latlng);

                var content = '<div class="info-window">'+
                    '<div>'+
                        '</div>'+
                            '<h3 class="heading">'+pickup_points[i].title+'</h3>'+
                            '<div class="content">';

                if ( pickup_points[i].address != '' ) {
                    content = content+'<p class="address">' + pickup_points[i].address + '</p>';
                }
                if ( pickup_points[i].address_2 != '' ) {
                    content = content+'<p class="address">' + pickup_points[i].address_2 + '</p>';
                }
                if ( pickup_points[i].city != '' || pickup_points[i].postal_code != '' ) {
                    content = content+'<p class="address">' + pickup_points[i].postal_code + ' ' + pickup_points[i].city +'</p>';
                }
                if ( pickup_points[i].description != '' ) {
                    content = content+'<p class="description">' + pickup_points[i].description +'</p>';
                }

                content = content + '<p>' + '<button class="select-point" data-id="' + pickup_points[i].id + '"><?php _e( 'Select this point', 'flexible-pickup' ); ?></button>' + '</p>';

                content = content+
                            '</div>'+
	                    '</div>'+
                    '</div>';

                marker.content = content;

                google.maps.event.addListener(marker, "click", function () {
                    infowindow.setContent(this.content);
                    infowindow.open(map, this);
                });

            }
            map.fitBounds(bounds);

            // Add a marker clusterer to manage the markers.
            var markerCluster = new MarkerClusterer(map, markers,
                {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});


            var input = document.getElementById('search');
            var searchBox = new google.maps.places.SearchBox(input);
            //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach(function(marker) {
                    marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    var icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    markers.push(new google.maps.Marker({
                        map: map,
                        icon: icon,
                        title: place.name,
                        position: place.geometry.location
                    }));

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }

        jQuery(document).on("keyup1","#search",function(){
            console.log('s');
            var search = jQuery('#search').val().toLowerCase();
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < pickup_points.length; i++) {
                if ( pickup_points[i].all.toLowerCase().indexOf(search) != -1 ) {
                    pickup_points[i].marker.setMap(map);
                    bounds.extend(pickup_points[i].marker.position);
                }
                else {
                    pickup_points[i].marker.setMap(null);
                }
            }
            map.fitBounds( bounds );
        })

		function center_search_box() {
            var fwidth = jQuery('#floating-panel').width();
            var mwidth = jQuery('#flexible_pickup_map').width();
            console.log(fwidth);
            console.log(mwidth);
            jQuery('#floating-panel').css('left',mwidth/2-fwidth/2);
		}

		jQuery(document).ready(function(){
            center_search_box();
            jQuery('#search').focus();
		})

        jQuery(window).resize(function() {
            center_search_box();
        })

		jQuery(document).on('click','.select-point',function(){
            jQuery( '#<?php echo $_GET['select_field'] ?>', window.opener.document ).val(jQuery(this).attr('data-id'));
            window.close();
		})
	</script>
	<style>
		#flexible_pickup_map {
			width: 100%;
			height: 100%;
		}
		#floating-panel {
			position: absolute;
			top: 10px;
			left: 25%;
			z-index: 5;
			background-color: #fff;
			padding: 5px;
			border: 1px solid #999;
			text-align: center;
			font-family: 'Roboto','sans-serif';
			line-height: 30px;
			padding-left: 10px;
		}
		#search {
			width: 250px;
		}
		.address {
			-webkit-margin-before: 1px;
			-webkit-margin-after: 1px;
		}
		.description {
			font-style: italic;
		}
	</style>
</head>
<body>
<div id="flexible_pickup_map">
    <?php if ( empty( $gmap_api_key ) || $gmap_api_key == '' ) : ?>
        <?php echo sprintf( __( 'Please enter Google Maps API Key in plugin %ssettings%s or contact site administrator.', 'flexible-pickup' ), '<a target="_blank" href="' . admin_url( 'edit.php?post_type=pickup_point&page=flexible-pickup-settings' ) . '">', '</a>' ); ?>
    <?php endif; ?>
</div>
<?php if ( !empty( $gmap_api_key ) && $gmap_api_key != '' ) : ?>
    <div id="floating-panel">
		<?php _e( 'Type location: ', 'flexible-pickup' ); ?><input id="search" type="text">
    </div>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=places&callback=fpickup_init_map&key=<?php echo $gmap_api_key; ?>" async defer></script>
<?php endif; ?>
</body>
</html>
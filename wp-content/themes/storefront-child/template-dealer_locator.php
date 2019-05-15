<?php
/**
 * The template for displaying the Dealer Locator.
 *
 * Template name: Dealer Locator
 *
 * @author Samuel Kong
 */

?>
<?php get_header(); ?>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAZTxW4NbyTwEQ6OswrGq_HFAsMimMiTM&libraries=places&callback=firstInit"
            async defer></script>

    <style>
        #map {
            width: 1200px;
            height: 600px;
        }

        #pac-input {
            width: 1200px;
        }
    </style>

    <div id="" class="one-col-center">

        <input id="pac-input" type="text" placeholder="Enter a location" autocomplete="off">

        <!-- Map list -->
        <div id="map"></div>

        <!-- Store List -->
        <div class="store-list-wrap">
            <div class="store-item-wrap">
                <p>Gong Cha Coffee</p>
                <p>Address: abc</p>
                <p>Gong Cha Coffee</p>

                <p>Gong Cha Coffee</p>

            </div>
        </div>

        <!-- Script -->
        <script type="text/javascript">
            'use strict';

            function getDistanceFrom2LatLon(lat1, lon1, lat2, lon2) {
                let R = 6371000; // 6371000 for meter, 6371 for km
                let dLat = deg2rad(lat2 - lat1);  // deg2rad below
                let dLon = deg2rad(lon2 - lon1);
                let a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2)
                ;
                let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return R * c;  // Distance in km
            }

            function deg2rad(deg) {
                return deg * (Math.PI / 180)
            }


            let stores = [
                {
                    name: 'Cafe Moonlight',
                    lat: 16.072013,
                    long: 108.191574,
                    address: '10 Hà Khê, Xuân Hà, Thanh Khê, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: 'Gong Cha',
                    lat: 16.060746,
                    long: 108.221262,
                    address: '10 Hà Khê, Xuân Hà, Thanh Khê, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: 'Moon Coffee',
                    lat: 16.061536,
                    long: 108.222625,
                    address: '10 Hà Khê, Xuân Hà, Thanh Khê, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: 'The Cups Coffee',
                    lat: 16.060837,
                    long: 108.220576,
                    address: '10 Hà Khê, Xuân Hà, Thanh Khê, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: '1990 Cafe',
                    lat: 16.075038,
                    long: 108.217023,
                    address: '97 Lê Lai, Thạch Thang, Hải Châu, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: 'Oq Lounge Pub',
                    lat: 16.079472,
                    long: 108.223493,
                    address: '18-20 Bạch Đằng, Thạch Thang, Hải Châu, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: 'Novotel Danang Premier Han River',
                    lat: 16.077346,
                    long: 108.223575,
                    address: '36 Bạch Đằng, Thạch Thang, Hải Châu, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: 'Faifo Buffet & Grills',
                    lat: 16.068073,
                    long: 108.208606,
                    address: '393 Lê Duẩn, Tân Chính, Thanh Khê, Đà Nẵng 550000, Vietnam'
                },
                {
                    name: 'Quán Ớt Xanh',
                    lat: 16.086374,
                    long: 108.216963,
                    address: '139 Đường 3 Tháng 2, Thuận Phước, Hải Châu, Đà Nẵng 550000, Vietnam'
                },

            ];

            let storeLocator = {
                mileToKm: 1.609344,
                map: null,
                markers: [],
                locations: null,
                bounds: null,
                myLocation: null,
                geoCoder: null,
                markerIcon: "/wp-content/themes/storefront/assets/images/icon/dealer-red-pointer.png",
                markerMyPosIcon: "/wp-content/themes/storefront/assets/images/icon/dealer-green-pointer.png",
                autoComplete: null,

                setLocations: function (list) {
                    if (!$.isArray(list)) {
                        return false;
                    }
                    this.locations = list;
                    return true;
                },


                initMap: function () {

                    //let point = this.findCenterPoint();
                    this.map = new google.maps.Map(document.getElementById('map'), {
                        // center: {lat: 16.060557, lng: 108.215571}, // 16.060557, 108.215571
                        zoom: 16,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });
                    this.registerMaker();
                },

                registerMaker: function () {
                    let infoWindow = new google.maps.InfoWindow();
                    let marker, i;
                    if (this.locations) {
                        this.bounds = new google.maps.LatLngBounds();
                        for (i = 0; i < this.locations.length; i++) {
                            if (this.locations[i]) {
                                marker = new google.maps.Marker({
                                    position: new google.maps.LatLng(this.locations[i].lat, this.locations[i].long),
                                    map: this.map,
                                    title: this.locations[i].name,
                                    icon: this.markerIcon
                                });

                                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                                    return function () {
                                        let infoStore = storeLocator.locations[i].name + "<br>" + storeLocator.locations[i].address;
                                        infoWindow.setContent(infoStore);
                                        infoWindow.open(storeLocator.map, marker);
                                    }
                                })(marker, i));
                                this.markers.push(marker);
                                this.bounds.extend(marker.getPosition());
                            }
                        }

                        // Add my position into map


                        // Fit bounds
                        this.map.fitBounds(this.bounds);
                    }
                },

                ajaxSearchStore: function (lat, lng) {

                    $.ajax({
                        type: 'post',
                        url: "/wp-admin/admin-ajax.php",
                        dataType: 'json',
                        data: {
                            'action': 'search_dealer_locator',
                            'myLat': lat,
                            'myLng': lng
                        },
                        cache: false,
                        success: function (data) {
                            console.log(data);


                            // Map
                            // storeLocator.setLocations(data.locations);
                            // storeLocator.initMap();
                        },
                        complete: function () {

                        }
                    });
                },

                ajaxLoadAllStore: function () {

                    $.ajax({
                        type: 'post',
                        url: "/wp-admin/admin-ajax.php",
                        dataType: 'json',
                        data: {
                            'action': 'search_dealer_locator'
                        },
                        cache: false,
                        success: function (data) {
                            console.log(data);

                            showAvailableStores(data);

                            // Map
                            // storeLocator.setLocations(data.locations);
                            // storeLocator.initMap();
                        },
                        complete: function () {

                        }
                    });
                }
            };

            storeLocator.ajaxLoadAllStore();


            function showAvailableStores(data) {

            }

            function firstInit() {
                storeLocator.locations = stores;
                storeLocator.initMap();

                let pacInput = document.getElementById('pac-input');
                let options = {
                    types: ['geocode'],
                    componentRestrictions: {country: ['vn', 'us']}
                };
                storeLocator.autoComplete = new google.maps.places.Autocomplete(pacInput, options);
                storeLocator.autoComplete.addListener('place_changed', placeChanged);
            }


            /**
             * Event listen the place is changed
             */
            function placeChanged() {
                console.log("placeChanged");
                let place = storeLocator.autoComplete.getPlace();
                console.log(place);
                if (place.name === "") {
                    storeLocator.locations = stores;
                    storeLocator.initMap();
                    console.log(stores);
                }

                if (place.address_components) {
                    let lat = place.geometry.location.lat();
                    let lng = place.geometry.location.lng();
                    console.log(lat);
                    console.log(lng);

                    reGenerateMap(lat, lng);
                }
            }

            function reGenerateMap(lat, long) {
                let tmpStores = [];
                for (let i = 0; i < stores.length; i++) {
                    console.log(stores[i].name + " " + getDistanceFrom2LatLon(lat, long, stores[i].lat, stores[i].long));

                    if (getDistanceFrom2LatLon(lat, long, stores[i].lat, stores[i].long) < 1000) {
                        tmpStores.push(stores[i]);
                    }
                }

                // Map
                storeLocator.locations = tmpStores;
                storeLocator.initMap();
            }


            //
            //
            //
            //
            // let options = {
            //     types: ['geocode'],
            //     componentRestrictions: {country: ['vn', 'us']}
            // };
            //
            // let autocomplete = new google.maps.places.Autocomplete(pacInput, options);
            // autocomplete.addListener('place_changed', placeChanged);
            //
            // function placeChanged() {
            //     let place = autocomplete.getPlace();
            //     console.log(place);
            //     if (place.address_components) {
            //         if (place.types[0] === "country") {
            //             let country_code = place.address_components[0].short_name.toLowerCase();
            //
            //             dealerLocator.getDealerByAjax(false, false, 0, country_code, 0, 0, false);
            //
            //         } else if (place.types[0] === "administrative_area_level_1") {
            //             let state = place.address_components[0].short_name.toLowerCase();
            //             let country_code = place.address_components[1].short_name.toLowerCase();
            //             dealerLocator.getDealerByAjax(false, false, 0, country_code, state, 0, false);
            //
            //         } else if (place.types[0] === "postal_code") {
            //             let zipcode = place.address_components[0].short_name;
            //             dealerLocator.getDealerByAjax(false, false, 0, 0, 0, zipcode, false);
            //
            //         } else {
            //             let lat = place.geometry.location.lat();
            //             let lng = place.geometry.location.lng();
            //             dealerLocator.lat = lat;
            //             dealerLocator.lng = lng;
            //             let radius = $(radiusSelect).val();
            //             dealerLocator.getDealerByAjax(lat, lng, radius, 0, 0, 0, true);
            //         }
            //     } else {
            //         alert("Sorry we are unable to determine location. Please select a location from the drop-down list.");
            //     }
            // }
            //
            //
            //
            //
            //
            // function showRadius() {
            //     $("#radius-container").show();
            //     $(".infoPerLine.distance").show();
            // }
            //
            // function hideRadius() {
            //     $("#radius-container").hide();
            //     $(".infoPerLine.distance").hide();
            // }
            //
            // let dealerVariables = {
            //     lat: false,
            //     lng: false,
            //     clickTab: ".tabDR",
            //     clickMapTab: "#mapTabDR",
            //     clickListTab: "#listTabDR",
            //     mapResult: "#map",
            //     listResult: "#listResult",
            //     tabContent: ".tabContentDR",
            //     loading: "#loadingLocator",
            //     dealerSearch: ".dealerSearch",
            //     searchInner: ".dealerSearchInner"
            // };
            //
            // let dealerLocator = {
            //     mileToKm: 1.609344,
            //     map: null,
            //     markers: [],
            //     locations: null,
            //     pointList: null,
            //     bounds: null,
            //     myLocation: null,
            //     geoCoder: null,
            //     markerIcon: "/wp-content/themes/storefront/assets/images/dealer-red-pointer.png",
            //
            //     init: function () {
            //         this.listenScroll();
            //         this.enableScroll();
            //         this.geoCoder = new google.maps.Geocoder();
            //         this.getDealerByAjax(false, false, 0, 0, 0, 0, false);
            //         this.clickTab();
            //     },
            //
            //     initMap: function () {
            //         //var point = this.findCenterPoint();
            //         this.map = new google.maps.Map(document.getElementById('map'), {
            //             zoom: 8,
            //             //center: new google.maps.LatLng(point[0], point[1]),
            //             mapTypeId: google.maps.MapTypeId.ROADMAP
            //         });
            //         this.registerMaker();
            //     },
            //
            //     registerMaker: function () {
            //         let infoWindow = new google.maps.InfoWindow();
            //         let marker, i;
            //         if (this.locations) {
            //             this.bounds = new google.maps.LatLngBounds();
            //             for (i = 0; i < this.locations.length; i++) {
            //                 marker = new google.maps.Marker({
            //                     position: new google.maps.LatLng(this.locations[i][1], this.locations[i][2]),
            //                     map: this.map,
            //                     title: "",
            //                     icon: this.markerIcon
            //                 });
            //
            //                 google.maps.event.addListener(marker, 'click', (function (marker, i) {
            //                     return function () {
            //                         infoWindow.setContent(dealerLocator.locations[i][0]);
            //                         infoWindow.open(dealerLocator.map, marker);
            //                     }
            //                 })(marker, i));
            //                 this.markers.push(marker);
            //                 this.bounds.extend(marker.getPosition());
            //             }
            //
            //             // Fit bounds
            //             this.map.fitBounds(this.bounds);
            //         }
            //     },
            //
            //     clearMarkers: function () {
            //         for (var i = 0; i < this.markers.length; i++) {
            //             this.markers[i].setMap(null);
            //         }
            //         // Reset the markers array
            //         this.markers = [];
            //     },
            //
            //     setLocations: function (list) {
            //         if (!$.isArray(list)) {
            //             return false;
            //         }
            //         this.locations = list;
            //         return true;
            //     },
            //
            //     setPointList: function (list) {
            //         let pointList = [];
            //         if (!$.isArray(list)) {
            //             return false;
            //         }
            //         for (var i = 0; i < list.length; i++) {
            //             pointList.push([list[i][1], list[i][2]]);
            //         }
            //         this.pointList = pointList;
            //         return true;
            //     },
            //
            //     getDealerByAjax: function (myLat, myLng, radius, country, state, zipcode, isShowRadius) {
            //         // Load ajax
            //         $(dealerVariables.loading).show();
            //         $(dealerVariables.listResult).html("");
            //         $.ajax({
            //             type: 'post',
            //             url: "/wp-admin/admin-ajax.php",
            //             dataType: 'json',
            //             data: {
            //                 'action': 'loadDealer',
            //                 'myLat': myLat,
            //                 'myLng': myLng,
            //                 'radius': radius,
            //                 'country': country,
            //                 'state': state,
            //                 'zipcode': zipcode
            //             },
            //             cache: false,
            //             success: function (data) {
            //                 console.log(data);
            //                 // List
            //                 $(dealerVariables.listResult).html(data.listResult);
            //                 dealerLocator.convertSvgImg();
            //
            //                 // Map
            //                 dealerLocator.setLocations(data.locations);
            //                 dealerLocator.setPointList(data.locations);
            //                 dealerLocator.initMap();
            //             },
            //             complete: function () {
            //                 // Finish and hide loading
            //                 $(dealerVariables.loading).hide();
            //                 dealerLocator.calcWhenScroll();
            //                 if (isShowRadius) {
            //                     showRadius();
            //                 } else {
            //                     hideRadius();
            //                 }
            //             }
            //         })
            //     },
            //
            //
            //     convertSvgImg: function () {
            //         $('.dealerWrapper img[src$=".svg"]').each(function () {
            //             let $img = $(this);
            //             let imgID = $img.attr('id');
            //             let imgClass = $img.attr('class');
            //             let imgURL = $img.attr('src');
            //             $.get(imgURL, function (data) {
            //                 // Get the SVG tag, ignore the rest
            //                 let $svg = $(data).find('svg');
            //                 // Add replaced image's ID to the new SVG
            //                 if (typeof imgID !== 'undefined') {
            //                     $svg = $svg.attr('id', imgID);
            //                 }
            //                 // Add replaced image's classes to the new SVG
            //                 if (typeof imgClass !== 'undefined') {
            //                     $svg = $svg.attr('class', imgClass + ' replaced-svg');
            //                 }
            //                 // Remove any invalid XML tags as per http://validator.w3.org
            //                 $svg = $svg.removeAttr('xmlns:a');
            //                 // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
            //                 if (!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
            //                     $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'));
            //                 }
            //                 // Replace image with new SVG
            //                 $img.replaceWith($svg);
            //             }, 'xml');
            //         });
            //     },
            //
            //     clickTab: function () {
            //         $(dealerVariables.clickTab).click(function () {
            //             $(dealerVariables.clickTab).removeClass("active");
            //             $(dealerVariables.tabContent).removeClass("active");
            //
            //             $(this).addClass("active");
            //
            //             let activeTab = $(this).attr('id');
            //             $(dealerVariables.tabContent + '[data-control="' + activeTab + '"]').addClass("active");
            //         })
            //     },
            //
            //     listenScroll: function () {
            //         $(window).scroll(function () {
            //             dealerLocator.calcWhenScroll();
            //         });
            //     },
            //
            //     calcWhenScroll: function () {
            //         let scrollTop = $(window).scrollTop();
            //         let windowHeight = $(window).height();
            //         let docHeight = $(document).height();
            //         let footerHeight = $('#footer').outerHeight();
            //         let headerHeight = $('#dealerHero').outerHeight();
            //         // Top
            //         let isTop = scrollTop <= headerHeight;
            //         if (isTop) {
            //             $(dealerVariables.searchInner).removeClass("fixed");
            //         } else {
            //             $(dealerVariables.searchInner).addClass("fixed");
            //         }
            //
            //         // Bottom
            //         let isBottom = (scrollTop + windowHeight) >= (docHeight - footerHeight);
            //         if (isBottom) {
            //             $(dealerVariables.searchInner).addClass("bottom");
            //         } else {
            //             $(dealerVariables.searchInner).removeClass("bottom");
            //         }
            //     },
            //
            //     enableScroll: function () {
            //         $.mCustomScrollbar.defaults.scrollButtons.enable = true;
            //         $('.dealerSearchInner').mCustomScrollbar({
            //             theme: "light-thin"
            //         });
            //     }
            // };


        </script>

    </div><!-- #primary -->


<?php

get_footer();
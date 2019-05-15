<?php

// Add css, script

add_action('wp_enqueue_scripts', 'add_theme_scripts');

function add_theme_scripts()
{
    // CSS
    wp_enqueue_style('css', get_template_directory_uri() . '/assets/css/css.css', array(), false, 'all');
    wp_enqueue_style('responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), false, 'all');

    // JS
    wp_enqueue_script('jquery3', get_template_directory_uri() . '/assets/js/jquery-3.4.1.min.js', array(), false, false);
    wp_enqueue_script('script', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), false, true);
}


// Show url on page list in admin page

add_filter("manage_page_posts_columns", "page_columns");

/**
 * @param $columns
 * @return array
 */
function page_columns($columns)
{
    $add_columns = array(
        'page_url' => 'Url'
    );

    $res = array_slice($columns, 0, 2, true) +
        $add_columns +
        array_slice($columns, 2, count($columns) - 1, true);

    return $res;
}

add_action("manage_page_posts_custom_column", "page_custom_columns");
function page_custom_columns($column)
{
    global $post;
    switch ($column) {
        case 'page_url' :
            echo $post->post_name;
            break;

    }
}

// Show lat long in store list in admin page

add_filter("manage_edit-store_columns", "store_columns");

/**
 * @param $columns
 * @return array
 */
function store_columns($columns)
{
    $add_columns = array(
        'store_latitude' => 'Latitude',
        'store_longitude' => 'Longitude'
    );

    $res = array_slice($columns, 0, 2, true) +
        $add_columns +
        array_slice($columns, 2, count($columns) - 1, true);

    return $res;
}

add_filter("manage_store_custom_column", 'manage_store_columns', 10, 3);

function manage_store_columns($out, $column_name, $storeId)
{
    $store = get_term($storeId, 'store');
    switch ($column_name) {
        case 'store_latitude' :
            $out = get_field("store_latitude", $store);
            break;
        case 'store_longitude' :
            $out = get_field("store_longitude", $store);
            break;

    }
    return $out;
}


// Dealer Locator

/**
 * Get all store
 * @return array
 */
function listAllStore()
{

    // Get stores
    $stores = get_terms(array(
        'taxonomy' => 'store',
        'hide_empty' => false,
        'orderby' => 'title',
        'order' => 'ASC'
    ));

    $result = [];
    foreach ($stores as $store) {
        $foo = (array)$store;

        $foo['lat'] = get_field("store_latitude", $store);
        $foo['long'] = get_field("store_longitude", $store);
        $foo['image'] = get_field("store_hero_image", $store);
        $foo['distance'] = false;

        array_push($result, $foo);
    }

    return $result;
}


/**
 * Calculates the great-circle distance between two points, with the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param int $earthRadius Mean earth radius in [m]. For miles, use 3959
 * @return float Distance between points in [m] (same as earthRadius)
 */
function calcGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

    $distance = $angle * $earthRadius;

    // round result if using meter
    if ($earthRadius == 6371000) { // meter
        $distance = round($distance);
    } else { // others
        $distance = round($distance, 1);
    }

    return $distance;
}


/**
 * Search stores by range and by coordinate (latitude & longitude)
 * @param $myLat
 * @param $myLong
 * @param int $range
 * @return array
 */
function searchStore($myLat, $myLong, $range = 500)
{
    $allStore = listAllStore();

    foreach ($allStore as $key => $value) {
        if (calcGreatCircleDistance($myLat, $myLong, $value["lat"], $value["long"]) > $range) {
            unset($allStore[$key]);
        }
    }

    return $allStore;
}


add_action('wp_ajax_search_dealer_locator', 'search_dealer_locator');
add_action('wp_ajax_nopriv_search_dealer_locator', 'search_dealer_locator');

/**
 * Ajax search dealer locator by position (latitude and longitude)
 */
function search_dealer_locator()
{
    $lat = $_POST["lat"];
    $long = $_POST["long"];
    $stores = [];

    if ( is_null($lat) || is_null($long) ) {
        $stores = listAllStore();
    }


    $data = array(
        'myLat' => $lat,
        'myLong' => $long,
        'status' => false,
        'stores' => $stores

    );
    echo json_encode($data);
    die();
}
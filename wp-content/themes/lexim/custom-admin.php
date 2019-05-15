<?php

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
/**
 * @param $column
 */
function page_custom_columns($column)
{
    global $post;
    switch ($column) {
        case 'page_url' :
            echo $post->post_name;
            break;

    }
}

// Show lat long in store list

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
/**
 * @param $out
 * @param $column_name
 * @param $storeId
 * @return mixed
 */
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
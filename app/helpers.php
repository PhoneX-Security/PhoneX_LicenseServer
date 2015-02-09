<?php

/**
 * @param $type
 * @param $name
 * @param $filter
 * @param bool $default
 * @return bool|mixed Value of the requested variable on success, FALSE if the filter fails, or NULL if the $name variable is not set.
 */
function getParameter($type, $name, $filter, $default = FALSE)
{
    $val = filter_input($type, $name, $filter);
    if ($default === null){
        return $val;
    }

    if ($val === null || $val == false){
        return $default;
    } else {
        return $val;
    }
}

/**
 * Additional URL helper
 * @param $col
 * @param null $title
 * @return string
 */
function link_to_sort($col, $title = null){
    if (is_null($title)) {
        $title = str_replace('_', ' ', $col);
        $title = ucfirst($title);
    }

    $arrowUp = '<span class="caret"></span>';
    $arrowDown = '<span class="caret caret-reversed"></span>';

    $indicator = (\Request::get('s') == $col ? (\Request::get('o') === 'asc' ? $arrowUp : $arrowDown) : "");
    $parameters = array('s' => $col, 'o' => (\Request::get('o') === 'asc' ? 'desc' : 'asc'));
    //array_merge(Input::get(), array('s' => $col, 'o' => (Input::get('o') === 'asc' ? 'desc' : 'asc')));
    return link_to_route(Route::currentRouteName(), $title, $parameters) . $indicator;
}
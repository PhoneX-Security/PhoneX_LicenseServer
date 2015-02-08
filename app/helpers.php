<?php

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
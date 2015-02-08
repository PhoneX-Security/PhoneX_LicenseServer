<?php namespace Phonex\Utils;
use Route;

/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 27.1.15
 * Time: 12:15
 */

trait SortableTrait {
    public static function linkToSorting($col, $title = null){
        if (is_null($title)) {
            $title = str_replace('_', ' ', $col);
            $title = ucfirst($title);
        }

        $arrowUp = '<span class="caret"></span>';
//        $arrowUp = " &uarr;";
        $arrowDown = '<span class="caret caret-reversed"></span>';
//        $arrowDown = " &darr;";

        $indicator = (\Request::get('s') == $col ? (\Request::get('o') === 'asc' ? $arrowUp : $arrowDown) : "");
        $parameters = array('s' => $col, 'o' => (\Request::get('o') === 'asc' ? 'desc' : 'asc'));
            //array_merge(Input::get(), array('s' => $col, 'o' => (Input::get('o') === 'asc' ? 'desc' : 'asc')));
        return link_to_route(Route::currentRouteName(), $title, $parameters) . $indicator;
    }
}
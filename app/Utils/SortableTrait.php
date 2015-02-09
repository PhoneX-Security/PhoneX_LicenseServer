<?php namespace Phonex\Utils;

/**
 * Created by PhpStorm.
 * User: miroc
 * Date: 27.1.15
 * Time: 12:15
 */

// Eloquent class using this trait should have $sortable array filled with columns that are sortable
trait SortableTrait {
    public function scopeSortable($query) {
        if(\Request::has('s') && \Request::has('o')){
            $s = \Request::get('s', 'id');
            $o = \Request::get('o', 'asc') == 'desc' ? 'desc' : 'asc';

            // Only fields from $sortable array are sortable
            if (!in_array($s, $this->sortable)){
                $s = 'id';
            }

            return $query->orderBy($s, $o);
        } else {
            return $query;
        }
    }
}
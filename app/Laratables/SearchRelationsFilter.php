<?php
/**
 * Created by DarinX.
 * User: V.Ruzhentsov
 * Date: 23.03.17
 * Time: 1:59
 */

namespace Friluft\Laratables;

use Gentlefox\Laratables\Filters\SearchFilter;

class SearchRelationsFilter extends SearchFilter
{
    public function apply(\Illuminate\Database\Eloquent\Builder $query) {
        if ( ! $this->value) return;
        
        $column = $this->column['machine'];
        if ($this->column['relation'] != null) {
            if ($this->fuzzy){
                $query->whereHas($this->column['relation']['related'], function($query){
                    $query->where($this->column['relation']['column'], 'LIKE', '%' .$this->value .'%');
                });
            } else {
                $query->whereHas($this->column['relation']['related'], function($query){
                    $query->where($this->column['relation']['column'], '=', $this->value);
                });
            }
        }
    }
}
<?php
/**
 * Created by DarinX.
 * User: V.Ruzhentsov
 * Date: 23.03.17
 * Time: 2:11
 */

namespace Friluft\Laratables;

use Gentlefox\Laratables\Filters\SelectFilter;

class SelectRelationsFilter extends SelectFilter
{
    public function apply(\Illuminate\Database\Eloquent\Builder $query)
    {
        if ($this->value == $this->defaultValue) return;
        
        if (preg_match('/[a-z_0-9]+->[a-z_0-9]+/', $this->column['machine'])) {
            $rel = explode('->', $this->column['machine']);
            $this->column['relation']= ['related' => $rel[0], 'column' => $rel[1]];
            $this->column['machine'] = $rel[0];
        }
        $column = $this->column['machine'];
        
        if ($this->column['relation'] != null) {
            $query->whereHas($this->column['relation']['related'], function($query){
                $query->where($this->column['relation']['column'], '=', $this->value);
            });
        }else{
            $query->where($column, '=', $this->value);
        }
        
        
    }
    
    public function buildUI() {
        $str = '<label for="filter_' .$this->column['machine'] .'">' .$this->label .'</label>';
        
        $str .= '<select name="filter_' .$this->column['machine'] .'">';
        
        foreach($this->values as $key => $value) {
            if ((string)$key == $this->value) {
                $selected = ' selected';
            }else {
                $selected = '';
            }
            $str .= '<option' .$selected .' value="' .$key .'">' .$value .'</option>';
        }
        
        $str .= '</select>';
        
        return $str;
    }
}
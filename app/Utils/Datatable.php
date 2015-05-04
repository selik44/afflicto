<?php

namespace Friluft\Utils;

use Illuminate\Database\Eloquent\Collection;
use DB;
use Illuminate\Database\Query\Builder;

class Datatable extends \afflicto\neatdatatables\datatable {

    protected static $counter = 0;

    /**
     * @var string name of the model class.
     */
    protected $model;

    public static function make($table, $model, $columns) {
        return new Datatable($table, $model, $columns);
    }

    public function __construct($table, $model, $columns) {
        $this->model = $model;
        $this->table = $table;
        $this->query = call_user_func($model .'::query');
        parent::__construct($table, $columns);
        $this->addClass('table boxed bordered striped');
    }

    /**
     * Creates an "action" with a button link to edit this entry.
     * @see action
     * @param $route
     */
    public function editable($route) {
        $this->action(function($row) use($route) {
            $route = str_replace('{id}', $row['id'], $route);
            return '<a class="button tiny primary" href="' .url($route) .'">Edit</a>';
        });
    }

    public function destroyable($route) {
        $this->action(function($row) use ($route) {
            $route = str_replace('{id}', $row['id'], $route);
            return '<a class="button tiny error" href="' .url($route) .'">Delete</a>';
        });
    }

    public function destroyable_old($route) {
        $this->action(function($row) use($route) {
            $route = rtrim($route, '/') .'/' .$row['id'];

            $str = '<form action="' .url($route) .'" method="POST">';
                $str .= '<input type="hidden" name="_method" value="DELETE">';
                $str .= '<input type="hidden" name="_token" value="' .csrf_token() .'">';
                $str .= '<input type="submit" class="button warning tiny" value="delete">';
            $str .= '</form>';

            return $str;
        });
    }

    public function prependAction($action) {
        array_unshift($this->actions, $action);

        return $this;
    }

    public function getRecords() {
        $query = $this->query;
        # add where clauses?
        if (count($this->whereClauses) > 0) {
            foreach($this->whereClauses as $clause) {
                $query->where($clause['column'], $clause['operator'], $clause['value']);
            }
        }

        # sort?
        if ($this->option('sortable')) {
            $sortBy = $this->option('sortableColumn');
            if (in_array($sortBy, $this->option('sortableColumns'))) {
                $query->orderBy($sortBy, $this->option('sortableDir'));
            }
        }

        # paginate?
        if ($this->option('pagination')) {
            $query->limit($this->getLimit());
            $query->offset($this->getOffset());
        }

        # get the records.
        $this->records = $query->get();
    }

    public function renderHead() {
        $str = '<thead><tr>';

        # loop through columns
        if ($this->option('sortable')) {
            $dir = strtoupper($this->option('sortableDir'));
            $arrowDir = $dir;
            foreach($this->columns as $machine => $human) {
                # skip this column?
                if ($human == null) continue;

                $active = '';
                $dirLink = $dir;
                $arrowDir = ($dir == 'ASC') ? 'up' : 'down';

                if ($this->option('sortableColumn') == $machine) {
                    $active = 'active';
                    //flip the direction
                    if ($dir == 'ASC') {
                        $dirLink = 'DESC';
                    }else {
                        $dirLink = 'ASC';
                    }
                }

                $str .= '<th>';
                    $href = $this->createSortableLink($this->currentPage, $machine, $dirLink);
                    $str .= '<span class="title">' .$human .'</span><a class="sort-arrow ' .$active .'" href="' .$href .'"><i class="fa fa-chevron-' .$arrowDir .'"></i></a>';
                $str .= '</th>';
            }
        }else {
            foreach($this->columns as $machine => $human) {
                # skip this column?
                if ($human == null) continue;
                $str .= '<th>' .$human .'</th>';
            }
        }
        if (count($this->actions) > 0) {
            $str .= '<th></th>';
        }

        $str .= '</tr></thead>';

        return $str;
    }

    public function renderBody() {
        $str = '<tbody>';

        foreach($this->records as $record) {
            $str .= '<tr>';
            # columns
            foreach($this->columns as $machine => $human) {
                # Should this column be skipped?
                if ($human == null) continue;

                $value = (isset($record[$machine])) ? $record[$machine] : '';

                # is there a rewrite for this column?
                if (isset($this->rewrites[$machine])) {
                    $value = $this->rewrites[$machine]($record);
                }else {
                    $value = htmlentities($value);
                }

                $str .= '<td>' .$value .'</td>';
            }
            # any actions?
            if (count($this->actions) > 0) {
                $str .= '<td class="datatable-actions"><div class="button-group">';
                foreach ($this->actions as $action) {
                    $str .= $action($record);
                }
                $str .= '</div></td>';
            }
            $str .= '</tr>';
        }

        $str .= '</tbody>';

        return $str;
    }

    public function renderPagination() {
        $cp = $this->currentPage;
        $pp = $this->perPage;
        $numPages = $this->getNumPages();
        $pagesAfterThisOne = $numPages - $cp;
        $str = '<footer class="ndt-pagination tower">';

        $str .= '<ul class="pagination">';

        #prev link?
        if ($cp > 1) {
            $str .= $this->renderPaginateLink($cp-1, '<i class="fa fa-chevron-left"></i>');
        }

        if ($this->option('jumpMenu') == false) {
            $str .= '<li class="ndt-dropdown"><select>';
            for($i = 1; $i<= $numPages; $i++) {
                if ($i == $cp) {
                    $str .= '<option selected="selected" value="' .$i .'">' .$i .'</option>';
                    continue;
                }
                $str .= '<option value="' .$i .'">' .$i .'</option>';
            }
            $str .= '</select></li>';
        }else {
            $str .= $this->renderPaginateLink($cp, $cp);
        }

        #next link
        if ($cp < $numPages) {
            $str .= $this->renderPaginateLink($cp+1, '<i class="fa fa-chevron-right"></i>');
        }
        $str .= '</ul>';

        $str .= '</footer>';
        return $str;
    }

}
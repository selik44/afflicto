<?php

namespace Friluft\Laratables;

use Gentlefox\Laratables\Filters\SearchFilter;

class UserNameFilter extends SearchFilter
{
	/**
	 * Apply the filter to the query
	 * @param  \Illuminate\Database\Query\Builder $query the database query.
	 * @return void
	 */
	public function apply(\Illuminate\Database\Eloquent\Builder $query)
	{
		if ( $this->value == '*') return;

		$query->where(function($query) {
			$query
				->where('firstname', 'LIKE', '%' .$this->value .'%')
				->orWhere('lastname', 'LIKE', '%' .$this->value .'%');
		});
	}

}
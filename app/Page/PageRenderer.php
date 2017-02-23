<?php

namespace Friluft\Page;

use Auth;
use Former;
use Friluft\Page;

class PageRenderer
{

	/**
	 * Trim away empty whitespace and empty html elements
	 */
	public function trim($content)
	{
		# trim away anything that isn't an HTML tag.
		$content = preg_replace("/(^[^<]+)|([^>]+$)*/", '', $content);


		# then trim away empty html tags
		return preg_replace("/((^ *(<[a-zA-Z]+>(( )|(\n))*<\\/[a-zA-Z]+> *)+))|((<[a-zA-Z]+>(( )|(\n))*<\\/[a-zA-Z]+> *)+$)/", '', $content);
	}

	/**
	 * @param Page $page
	 * @return string the compiled content
	 */
	public function compile(Page $page)
	{
		$content = $this->trim($page->content);


		if (Auth::user()) {
			$user = Auth::user();
			Former::populate($user);
		}

		//parse code
		$matches = [];
		preg_match_all("/{{ *(?P<function>[a-z_0-9]+)( +((\"[^\"]+\")|([0-9]+)))?}}/", $content, $matches);
		if($matches) {
			foreach($matches[0] as $key => $match) {
				$function = $matches['function'][$key];
				$replacement = "";

				//------------ parse include statements -------------//
				if ($function == 'include') {
					$view = trim($matches[4][$key], ' "');
					$replacement = view('front.partial.' .$view)->render();
				}else if ($function == 'page') {
					$subPageName = trim($matches[4][$key], ' "');
					$subPage = Page::whereSlug($subPageName)->first();
					if ($subPage) {
						$replacement = $this->compile($subPage);
					}
				}

				$content = str_replace($match, $replacement, $content);
			}
		}



		# trim again
		$content = $this->trim($content);

		return $content;

		/*
		if ($page->slug == 'kontakt-oss') {
			$content = str_replace('{{form}}', view('front.partial.contact-form')->render(), $content);
		}else if ($page->slug == 'bytte-og-retur') {
			$content = str_replace('{{form}}', view('front.partial.retur-form')->render(), $content);
		}else if ($page->slug == 'samarbeid') {
			$content = str_replace('{{form}}', view('front.partial.partners-form')->render(), $content);
		}else if ($page->slug == 'konkurranser') {
			//$content = str_replace('{{newsletter}}', view('front.partial.newsletter-form')->render(), $content);
		}*/
	}

	public function view(Page $page)
	{
		$aside = $page['options']['sidebar'] ? true : false;

		return view('front.page')
			->with([
				'content' => $this->compile($page),
				'page' => $page,
				'aside' => $aside
			]);
	}

}
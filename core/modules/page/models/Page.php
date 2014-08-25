<?php

namespace spcms\core\modules\page\models;

use spcms\core;

class Page extends core\Model
{
	protected $tableName = 'mod_page';
	
	protected $attributes = array(
		'title', 
		'metaDescription', 
		'metaKeywords', 
		'id', 
		'url', 
		'body'
	);
	
	public function getIdByUrl($url)
	{
		
	}
}

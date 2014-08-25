<?php
namespace spcms\core;

class Locale
{
	private $locales;
	
	/**
	 * Locale timezone
	 * @var string 
	 */
	private $timezone = 'Europe/Ljubljana';

	public function __construct()
	{
		$this->locales = $this->locales();
		
		// Switch language by langId GET param
		$langId = \SimplCMS::component('request')->getParam('langId');
		
		if($langId !== null)
			$this->setLanguage($langId);
		
		// Session locale handler
		// If session is enabled, handle locale trough session cookie
		$langId = \SimplCMS::$app->session->get(Session::CORE_NAMESPACE. 'langId');
		
		if(isset($langId))
			\SimplCMS::$app->language = \SimplCMS::$app->session->get(Session::CORE_NAMESPACE. 'langId');
		
		// TODO: resolve application timezone
		date_default_timezone_set ($this->timezone);
		
		setlocale(E_ALL, $this->getLocale(\SimplCMS::$app->language));
	}

	/**
	 * Set CMS application language
	 * @param string $langId (en,sl,de)
	 */
	public function setLanguage($langId)
	{
		\SimplCMS::$app->session->set(Session::CORE_NAMESPACE. 'langId', $langId);
	}
	
	public function getLocale($langId)
	{
		return $this->locales[$langId];
	}

	public function getLanguage()	
	{
		
	}
	
	/**
	 * Mapping structure for langId => locale 
	 * @return array
	 */
	private function locales()
	{
		return array(
			'sl'	=> 'sl_SI',
			'en'	=> 'en_GB',
		);
	}
	
	
}

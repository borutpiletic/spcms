<?php
namespace spcms\core\modules\admin\controllers;

use spcms\core\Controller;
use spcms\core\Form;
use spcms\core\Authentication;
use spcms\core\modules\menu;
use spcms\extensions\plugins\FormValidator\FormValidator_FormPlugin;

class IndexController extends Controller
{	
	public function onLoad() 
	{
		parent::onLoad();
		
		// Import core CSS libraries		
		\SimplCMS::$app->libraries->importCoreLibrary('bootstrap3', 'css');				
		\SimplCMS::$app->libraries->importCoreLibrary('fancytree', 'css');				
		
		// Import core JS libraries
		\SimplCMS::$app->libraries->importCoreLibrary('jquery', 'js');
		\SimplCMS::$app->libraries->importCoreLibrary('jquery_ui', 'js');
		\SimplCMS::$app->libraries->importCoreLibrary('fancytree', 'js');
		\SimplCMS::$app->libraries->importCoreLibrary('cookie', 'js');
		\SimplCMS::$app->libraries->importCoreLibrary('bootstrap3', 'js');				
		
		// Import custom CSS & JS
		$this->theme->addStylesheetFile('css/style.css');		
		//$this->theme->addScriptFile('js/LoginForm.js');
	}
	
	public function indexAction() 
	{		
		
		$this->theme->pageTitle = t('Dashboard');
		
		//\SimplCMS::$app->session->set('userId', 10);
		
		if (\SimplCMS::$app->user->hasAccess('page', 'create') === true)
		{
			
		}
		
		
		print \SimplCMS::$app->session->get('userId');
		
		$this->render('index', array(
			'title' => $this->theme->pageTitle
		));
	}

	/**
	 * Login into administration area
	 */
	public function loginAction($route)
	{
		// Use login page template
		$this->theme->setTemplate('login');
		$this->theme->addStylesheetFile('css/login.css');		
		$this->theme->pageTitle = t('Site administration');
		
		// Get login form instance
		// Forms where moved to FormFactory helper to get leaner controller
		$formLogin = $this->getHelper('FormsFactory')->formLogin();
		$formLoginErrors = '';
		
		// Perform authentication if login form is submitted
		if ($formLogin->isSubmitted() === true) 
		{
			// Call autentication helper function which performs
			// authentication checking trough \core\Authentication class
			$auth = $this->_authentication($formLogin);	
			
			if ($auth === 'success')
				$this->redirect('admin/_structure');
			else
				$formLogin->setError(Authentication::STATUS_ERROR_AUTH, 'Authentication failed!');
			
			$formLoginErrors = FormValidator_FormPlugin::outputErrors($formLogin);
		}
		
		dump($formLogin->getRawData());
		
		$this->render('login', array(
			'title' => $this->theme->pageTitle,
			'formLogin' => $formLogin,
			'formLoginErrors' => $formLoginErrors
		));
	}
	
	public function _dashboardAction()
	{
		$this->render('dashboard');
	}
	
	public function _structureAction()
	{
		$this->theme->addScriptFile('js/MenuStructure.js');
		$this->theme->pageTitle = t('Edit menu structure');
		
		// Fetch all menu structures
		$menus = menu\helpers\MenuHelper::getMenus();
		
		// Build menu structures into <ul> list
		$menuHtmlLists = array();
		foreach ($menus as $i => $menu)
			$menuHtmlLists[] = menu\helpers\MenuHelper::htmlList($menu['sysMenuName'], array('id' => "menu{$i}" ));
		
		$this->render('structure', array(
			'menus' => $menus,
			'menuHtmlLists' => $menuHtmlLists,
			'menuCount' => sizeof($menuHtmlLists)
		));
	}

	/**
	 * Helper function performing authentication
	 * @param Form $formLogin Login form object instance
	 * @return string Authentication status Authentication::STATUS_*
	 */
	private function _authentication(Form $formLogin)
	{
		$formData = $formLogin->getData();
		
		if (isset($formData['username']) && isset($formData['password']))
		{
			// User authentication
			$auth = new Authentication;
			$auth = $auth->authenticate($formData['username'], $formData['password']);
			return $auth['status'];
		}
		return Authentication::STATUS_ERROR_AUTH;
	}
	
	public function authorizedAccess() {
		return true;
	}
}
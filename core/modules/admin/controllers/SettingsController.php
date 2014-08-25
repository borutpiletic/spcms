<?php

namespace spcms\core\modules\admin\controllers;

use spcms\core\Controller;

class SettingsController extends Controller
{
	public function onLoad() 
	{
		parent::onLoad();
		\SimplCMS::$app->libraries->importCoreLibrary('bootstrap3', 'css');				
		\SimplCMS::$app->libraries->importCoreLibrary('jqtree', 'css');				
		\SimplCMS::$app->libraries->importCoreLibrary('jquery', 'js');
		\SimplCMS::$app->libraries->importCoreLibrary('jqtree', 'js');
		
		$this->theme->addStylesheetFile('css/style.css');
		
		// Load admin menu
		$menuHelper = \SimplCMS::$app->getCoreModule('menu')->getHelper('Menu');
		
		// Append menu to theme
		$this->theme->appendToSection( 
				$menuHelper::htmlList('admin_menu', 
						array('class' => array('list-inline', 'header-menu'))), 'header', 'mainMenu'
		);
		
		$this->theme->appendToSection( $menuHelper::htmlList('modules_menu',array('id' => 'module-menu')), 'sidebarLeft', 'modulesMenu');
		
		$this->theme->addScriptFile('js/LoginForm.js');		
	}
	
	/**
	 * General CMS settings
	 */
	public function generalAction() 
	{
		$formSettings = new \spcms\core\Form('settings', 'POST');
		
		$this->render('settings', array(
			'formSettings' => $formSettings
		));
	}

		/**
	 * Manage RBAC for modules
	 */
	public function rbacAction()
	{
		//if (\SimplCMS::$app->user->hasAccess('admin', 'manageRBAC') === false)
		//	\spcms\core\Response::displayErrorPage(403);

		
		$roles = \SimplCMS::$app->user->getRoles();
		
		// Get settings helper class
		$settingsHelper = \SimplCMS::$app->getCoreModule('admin')->getHelper('Settings');
		
		// List all modules with RBAC
		$modules = $settingsHelper->getRbacModules();
		
		// Create RBAC settings form
		$formRbac = new \spcms\core\Form('rbac', 'POST');
		
		$this->render('rbac', array(
			'roles' => $roles,
			'modules' => $modules,
			'settingsHelper' => $settingsHelper,
			'formRbac' => $formRbac
		));
	}
}
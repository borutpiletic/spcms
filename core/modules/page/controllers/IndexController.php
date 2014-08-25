<?php
namespace spcms\core\modules\page\controllers;

use spcms\core\Controller, 
    spcms\core\CacheFile;

use spcms\core\modules as modules;

class IndexController extends Controller
{
    public $page;
	
    protected $isStatic = true;

    public function onLoad() 
    { 	
		\SimplCMS::$app->libraries->importCoreLibrary('jquery', 'js');
		
		
		\SimplCMS::$app->libraries->addCustomLibrary(array(
			'angularjs' => 'angular-1.0.8.min.js',
			'angularjs2' => array('file1', 'file2')
		), 'css');
		
        $this->theme->appendToSection('desni stolpec - sekcija', 'rightColumn');
		
		\SimplCMS::$app->libraries->importCoreLibrary('bootstrap3', 'css');
		
		
		\SimplCMS::$app->libraries->importCustomLibrary('angularjs', 'css');
		\SimplCMS::$app->libraries->importCustomLibrary('angularjs2', 'css');
		
		
		//dump( \SimplCMS::$app->request->getRoute() );
        
        //$this->theme->appendToSection($this->buildMainMenu() , 'header', 'mainMenu');
    }

    public function indexAction()
    {
		print 'index';
		
		
		$this->render('index');
		
		
		
		
		//$modules = \SimplCMS::$app->db->query("SELECT * FROM mod_page");
		
		//$query = \SimplCMS::$app->db->getConnection()->prepare("SELECT * FROM mod_page WHERE id = ?");
		//$query->execute(array(1));
		
		//$menuController = \SimplCMS::$app->getCoreModule('menu')->getController('index')->indexAction();
		
//		$menu = new modules\menu\models\MenuModel();
//		$menu = $menu->getStructure(1);
//		
//		$menuHelper = \SimplCMS::$app->getCoreModule('menu')->getHelper('menu');
//		$menu = $menuHelper::htmlList($menu);
//		
//		$this->render('index', array(
//            'username' => 'borut',
//            'page'     => $this->page,
//            'mainMenu' => array(1,23,4),
//            'htmlTable'=> $this->loadDocumentsTable()
//        ));
    }
    
    public function contactAction()
    {
        //$this->page = \SimplCMS::component('pageReader')->getDocumentById(3);
        
        $this->render('contact', array(
            'page' => $this->page
        ));
    }
    
    private function loadDocumentsTable()
    {
        $data = \SimplCMS::component('pageReader')->getDocuments();
        
        return \spcms\core\HtmlRender::table(
            $data, 
            array(
                'body' => 'Vsebina', 
                'title' => 'Naslov',
                'url'   => 'URL naslov'
        ));         
    }
	
	
}
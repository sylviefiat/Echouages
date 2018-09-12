<?php
/**
 * @name MOOJ Proforms
 * @version 1.5
 * @package proforms
 * @copyright Copyright (C) 2008-2013 Mad4Media. All rights reserved.
 * @author Dipl. Inf.(FH) Fahrettin Kutyol
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Please note that some Javascript files are not under GNU/GPL License.
 * These files are under the mad4media license
 * They may edited and used infinitely but may not repuplished or redistributed.
 * For more information read the header notice of the js files.
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class ProformsViewApp extends ProformsView{
	
	protected $error = '';
	
	/**
	 * 
	 * @var ProformsModelApp
	 */
	protected $model = null;
	
	protected function _init(){
		$this->_includeAppLib();
	}
	
	
	protected function _process(){

		if($this->error){
			$this->template = 'error';
			return;
		}
		
		$dba = $this->model->getData();
		$app = $this->model->getApp();
		AText::add($app);
		
		// App view processing
		if(JFile::exists(M4J_APPS_BASE. $app . DS . "controller.php" ) && $dba){
			$className = "App" . ucfirst(strtolower($app));
			include_once (M4J_APPS_BASE. $app . DS . "controller.php");
		
			if(class_exists($className) && get_parent_class($className)== "MController" ){
				$controller = new $className($app);
				
				ob_start();
					$controller->render();		
				$this->buffer = ob_get_clean();
				
			}else{
				$this->addError( 'Proforms Apps: ' . JText::_("METHOD NOT FOUND") );
			}
		}else{
			if(! JFile::exists(M4J_APPS_BASE. $app . DS . "controller.php" )){
				$this->addError( 'Proforms Apps: ' . JText::_("CANNOT FIND SOURCE FILE") );
			}else{
				$this->addError( 'Proforms Apps: ' . JText::_("PAGE COULD NOT BE FOUND") );
			}
		}
		
		if($this->error){
			$this->template = 'error';
		}
	}
	
	
	
	protected function _includeAppLib(){
		static $appLibIsIncluded;
		if(! empty($appLibIsIncluded)) return;

		// Dirty workaround for the old proforms.html.php class
		require_once(JPATH_ROOT . '/components/com_proforms/includes/legacyprint.php');
		
		require_once(M4J_INCLUDE_CALENDAR);
		require_once(M4J_INCLUDE_FUNCTIONS);
		require_once(M4J_INCLUDE_VALIDATE);
		require_once(M4J_INCLUDE_TEMPLATER);
		require_once(M4J_INCLUDE_APPTEXT);
		
		// App view processing
		// include App MVC
		require_once(M4J_INCLUDE_CONTROLLER);
		require_once(M4J_INCLUDE_VIEW);
		require_once(M4J_INCLUDE_MODEL);
		
		
		$appLibIsIncluded = true;
	}
	
	public function addError($error = ''){
		$this->error .=  '<span>' .  $error . '</span><br/>';
	}
}
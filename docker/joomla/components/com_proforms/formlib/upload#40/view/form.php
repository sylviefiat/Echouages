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

class MFormUpload extends MFormElement{

	
	/**
	 * 
	 * @var boolean
	 */
	protected $_upload = true;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sqlLoadSingle = true;
	
	protected $endings = null;
	
	protected $maxsize = 0;
	
	protected $measure = 1;
	
	protected $maxsizeLabel = '';
	
	
	protected $maxsizeInBytes = 0;
	
	protected $hasEndings = false;
	
	
	protected $imageExtensions = array("jpg","jpe","jpeg","gif","png");
	protected $allowedImageMimes = array('image/jpg', 'image/jpe', 'image/jpeg','image/png','image/gif');
	
	/**
	 * @param unknown_type $dataObject
	 */
	protected function _init(& $dataObject) {
		$this->_add2HigerLevel(array('endings','maxsize','measure'));
		
	}
	
	
	// Hook before send processing
	protected function _onBeforeSendProcessing(){
		$measureLabel = "MB";
		switch ( $measure = intval($this->measure)){
			case 1 : $measureLabel = "B"; break;
			case 1024 : $measureLabel = "KB"; break;
		}
		$this->maxsizeLabel = $this->maxsize . ' ' .$measureLabel;
		$this->maxsizeInBytes = (int) ( ( (int) $this->maxsize ) *  $measure );

		$endings = isset($this->params->endings) ? trim( str_replace( array("\n","\r","\s","\t"), '' ,   $this->params->endings ) ) : null;
		if($endings){
			$this->hasEndings = true;
			$this->endings = explode(',',$endings);
			if( is_array($this->endings) ){
				foreach ($this->endings as & $ending){ 
						$ending = strtolower( trim($ending) );  
				}
			}//EOF is array
		}//EOF has endings
		
		
	}
	
	

	/**
	 * The function which retrieves the data
	 */
	protected function _request(){
		if(empty($this->eid) || ! $this->eid || $this->displayOnly) return;
		$name = 'm4j-' . $this->eid;
		
		$file = JRequest::getVar($name, null, 'files', 'array');
		
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')){
			JError::raiseWarning('', 'YOUR SERVER DOES NOT ALLOW UPLOADS! ');
			return false;
		}

		if (!is_array($file) && $this->required){
			$this->_addError( M4J_LANG_MISSING.$this->question );
			return false;
		}elseif (!is_array($file) || empty($file) ||  $file['size'] < 1){
			return;
		}
		
		// Check if there was a problem uploading the file.
		if ($file['error'] || $file['size'] < 1)
		{
			if(! !$file['error']){
				$this->_addError( $this->question . " - SERVER ERROR FILE UPLOAD: NO FILE SIZE! ASSUMED MISCONFIGURATION OF THE SERVER.");
			}else{
				$this->_addError( $this->question . " - ERROR!<br/>" . $file['error']);
				
			}
			return false;
		}

		// Validation goes here for upload elements because we just move if the validation passes true
		$return = false;
		if(   $this->maxsizeInBytes && ( intval($file['size']) > $this->maxsizeInBytes )   ){
			$this->_addError( $this->question . M4J_LANG_TO_LARGE . $this->maxsizeLabel );
			$return = true;
		}
		
		$extension = strtolower( trim( pathinfo($file['name'], PATHINFO_EXTENSION) ) );
		if( $this->hasEndings && ! in_array($extension, $this->endings)){
			$this->_addError( $this->question . M4J_LANG_WRONG_ENDING. implode(', ', $this->endings) );
			$return = true;
		}
		
		if($return) return false;		
		

		// Check again on image intrusions
		if(in_array($extension, $this->imageExtensions)){
			if(function_exists('finfo_open') && function_exists('finfo_file') ){
				$handler = finfo_open(FILEINFO_MIME_TYPE);
				$mime = finfo_file($handler, $file['tmp_name']);
				if($mime && ! in_array($mime, $this->allowedImageMimes)){
					$this->_addError( $this->question . '<br/>- `'. htmlspecialchars( $this->_makeSafe($file['name']) ) .'` IS NOT AN IMAGE!' );
					return false;
				}//EOF is mime
			}//EOF finfo exists
			
			if( function_exists('getimagesize') &&  ! getimagesize($file['tmp_name']) ){
					$this->_addError( $this->question . '<br/>- `'. htmlspecialchars( $this->_makeSafe($file['name']) ) .'` IS NOT AN IMAGE!' );
					return false;
			}//EOF if getimagesize check
		}
		
		
		// moving the upload to Proforms' temp dir
		$this->value =  $this->_makeSafe($file['name']);
		
		
		$temporaryPath = $this->tmpPath . '/' . $this->tmpDir;
		if(! JFolder::exists($temporaryPath)){
			JFolder::create($temporaryPath);
		}
		
		$tmp_dest	= $temporaryPath. '/' .  $this->value ;
		$tmp_src	= $file['tmp_name'];
		
		// Move uploaded file
		JFile::upload($tmp_src, $tmp_dest, false, true);
	
// 		MDebug::pre($this);
		$this->uploadExists = true;
	}
	
	protected function _makeSafe($fileName = null){
		// Remove any trailing dots, as those aren't ever valid file names.
		$fileName = rtrim($fileName, '.');
	
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
	
		return trim(preg_replace($regex, '', $fileName));
	}
	
	
	// Validation
	protected function _validation(){
		if(!$this->value && ! $this->required) return;
	
		
		
		
		ProformsHelper::validateType($this->value, (int) $this->getParameter('eval', 0), 	$errorMessage  );
		if($errorMessage){
			$errorMessage .= (trim($this->question)) ? $this->question : '`' . $this->alias . '`';
			$this->_addError($errorMessage);
		}
	}
	
	
	
	/**
	 * 
	 */
	protected function _renderDefault() {

		$eval = ($this->required != 0)? 'alt="1000" ' : 'alt="" ';
		$out  = '<!--pfmvalue--><input '.$eval.'class="m4jAttachment '.$this->_getClass().'" id="m4j-'.$this->eid.'" type="file"  name="m4j-'.$this->eid.'" ' . $this->_getStyle() . '></input>'."\n";
		return $out;
	}
	
	/**
	 * 
	 */
	protected function _renderResponsive() {
		return $this->_renderDefault();

	}

	
	
}//EOF class
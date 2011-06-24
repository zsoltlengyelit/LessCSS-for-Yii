<?php

/**
 * LessCss class file.
 *
 * @author Zsolt Lengyel <zsolt.lengyel.it@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * LessCss is a Yii Component to support using less css file easily.
 * @link http://lesscss.org
 * lesscss lets programmers do more geek things and see funny cuts on the web. Because they should write less code.
 * The class based on Leaf Corcoran's <leafot@gmail.com> lessc php parser.
 * If in your Yii application is setted the caching, the {@link compile} method by default do cachin.
 */
class LessCss extends CComponent
{
	protected static $staticPath = null;
	/**
	 * Compliles the input CSS file. If the $cache is setted, the output will be chached by it.
	 *
	 * @param string input file path relative to the application path (e.g. '/css/test.less' if the file is /absolute/path/to/your_app/css/test.less)
	 * @param boolean cache enabled = true, disabled = false
	 * @param boolean if true, returns string, else the path of the compiled CSS file
	 * 
	 * @return string file path or compiled string according to 3th param
	 */
	public static function compile($less, $cache = true, $returnString = true){
	  	if(is_null(self::$staticPath))
	  		self::$staticPath = str_replace('/protected','',Yii::app()->basePath).'/';

	  	$assetName				= str_replace(DIRECTORY_SEPARATOR, '.', str_replace(self::$staticPath,'',$less));
	  	$assetCss	= dirname(__FILE__).DIRECTORY_SEPARATOR.'assets/css/'.$assetName.'.css';	  	 
	  	$parsed = false;
	  	$lessc  = null;	  	
	  	
	  	if($cache && !is_null(Yii::app()->cache)){
	  		$assetCss = Yii::app()->cache->get($less);  		
	  		
	  		if($assetCss === false){ // isn't yet cached	
	  		Yii::app()->user->setFlash('less-parse','Less parsed #1');  			
	  			
	  			if(!self::parse(file_get_contents($less),$parsed))
	  				throw new CException('Cannot parse file: '.$less);	
	  			else			
	  				Yii::app()->cache->set($less, $assetCss, 0, new CFileCacheDependency($less));	
	  						
	  		}else{  			
	  			if($returnString) $parsed = file_get_contents($assetCss);	  			
	  		}
	  	}else{ // no cache
	  	Yii::app()->user->setFlash('less-parse','Less parsed #2');
	  		if(!self::parse(file_get_contents($less,$parsed)))
	  			throw new CException('Cannot parse file: '.$less);	  		
	  	}
	  	
	  	if($returnString){
	  		return $parsed;
	  	}else{
	  		if(!@file_put_contents($assetCss, $parsed)){
		 			Yii::app()->cache->delete($less);
					throw new CException('Error writing file: '.$assetCss);
	  		}
	  		return Yii::app()->getAssetManager()->publish($assetCss);	  		
	  	}
	  	
	}
	
	/**
	 * Parses $input file into $output
	 * @param string input less CSS
	 * @param string output parsed CSS
	 *
	 * @return boolean true if parse was successfull	 	
   */
	protected static function parse($input, &$output){
		$parsed = null;

		try {
			require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lessc.inc.php');
			$lessc = new lessc();
			ob_start();
			$parsed = trim($lessc->parse($input));
			ob_end_clean();
		} catch (exception $e) {
			Yii::log(
				'Failed to compile LessCss input, reason:'.	$e->getMessage(),
				'error',
				__CLASS__				
			);
			return false;
		}
		$output = $parsed; 
		
		return true;
	}
	
	/**
	 * Alias of LessCss::compile($lessFile, $cache, false)
	 */
	public static function getCssUrl($lessFile, $cache = true){
		return self::compile($lessFile, $cache, false);
	}
}


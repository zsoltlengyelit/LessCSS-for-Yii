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
 * lesscss lets programmers do more geek things and see funny cats on the web. Because they should write less code.
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

	  	$assetName		= str_replace(DIRECTORY_SEPARATOR, '.', str_replace(self::$staticPath,'',$less));
	  	$assetCssOrig	= dirname(__FILE__).DIRECTORY_SEPARATOR.'assets/css/'.$assetName.'.css';
	  	$assetCss = false;
	  	$parsed = false;
	  	$lessc  = null;	
	  	$wasCached = false;  	
	  	
	  	if($cache && !is_null(Yii::app()->cache)){
	  		$assetCssCached = Yii::app()->cache->get($less);
	  		if(!is_file($assetCssCached)){
	  			$assetCss = false;  		
	  			Yii::app()->cache->delete($less);
	  		}
	  		
	  		if($assetCssCached === false){ // isn't yet cached	
	  			
	  			Yii::trace('Less parsed by cache: '.$less);				// TRACE	  			
	  			self::parse(file_get_contents($less),$parsed);
	  			Yii::app()->cache->set($less, $assetCssOrig, 0, new CFileCacheDependency($less));					
	  				
	  		}else{ // is cached
	  			$wasCached = true;			
	  			if($returnString) $parsed = file_get_contents($assetCssCached);	  			
	  		}
	  		
	  	}else{ // no cache
	  		
	  		Yii::trace('Less parsed by no cache: '.$less);				// TRACE
	  		self::parse(file_get_contents($less),$parsed);  		  		
	  		self::putIntoFile($assetCssOrig, $parsed);	
	  	}
	  	
	  	// parsed string or file is done
	  	
	  	if($returnString){
	  		return $parsed;
	  	}else{ 		
	  		return Yii::app()->getAssetManager()->publish($assetCssOrig);	  		
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
	
	protected static function putIntoFile($file, $content){
		file_put_contents($file, $content);
		chmod($file,0777);
	}
	
	/**
	 * Alias of LessCss::compile($lessFile, $cache, false)
	 */
	public static function getCssUrl($lessFile, $cache = true){
		return self::compile($lessFile, $cache, false);
	}
}


<h1>Installation</h1>
<p>
  Clone this repo into your Yii application'a protected/extensions dir
  <pre>
    cd /path/to/your/yii_app/protected/extensions
    mkdir lessCss
    cd lessCss
    git clone git@github.com:zsoltlengyelit/LessCSS-for-Yii.git
  </pre>
</p>

<h1>Usage</h1>
<p>
  An exmample of use of LessCss:
  <pre>
    // app/themes/some_theme/assets/css/test.less
    
    // LESS
		.rounded-corners (@radius: 5px) {
			border-radius: @radius;
			-webkit-border-radius: @radius;
			-moz-border-radius: @radius;  
		}

		#header {
			.rounded-corners;
		}
		#footer {
			.rounded-corners(10px);
		}
  </pre>
  <pre>
  // SiteController.php file
  public function actionIndex()
	{
			Yii::import('ext.lessCss.LessCss');

			Yii::app()->clientScript
			->registerCSSFile(
			    LessCss::getCssUrl(Yii::app()->theme->basePath.'/assets/css/test.less')
			    // will try cached parsing
			);
		$this->render('index');
	}
  </pre>
  
</p>

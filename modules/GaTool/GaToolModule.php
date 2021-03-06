<?php

class GaToolModule extends CWebModule {

	/**
  * @var array contains redis configuration  ( server, port, database, password, prefix)
  */
  public $redis = array(
        'server' => 'localhost',
        'port' => '6379',
        'database' => 0,
        'password' => '',           
  );

  /*
  * array of profiles ids nad names
  */
  public $profiles = array(

  );
	
	public function init() {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'GaTool.models.*',
			'GaTool.components.*',
			'GaTool.lib.*',
			'GaTool.extenstions.*'
		));

		//load resque component
		if(!class_exists('RResqueAutoloader', false)) {
			# Turn off our amazing library autoload
			spl_autoload_unregister(array('YiiBase','autoload'));
			# Include Autoloader library
			include(dirname(__FILE__) . '/components/resque/RResqueAutoloader.php');
			# Run request autoloader
			RResqueAutoloader::register();
			# Give back the power to Yii
			spl_autoload_register(array('YiiBase','autoload'));
    		}
    
	        Resque::setBackend($this->redis['server'] . ':' . $this->redis['port'], $this->redis['database'], $this->redis['password']);
	        if (isset($this->redis['prefix'])) {
	           Resque::redis()->prefix($this->redis['prefix']);    
	        }

	}

	public function beforeControllerAction($controller, $action) {
		if(parent::beforeControllerAction($controller, $action)) {
			$controller->layout = 'main';
			Yii::app()->theme = ''; 
			return true;
		} else{
			return false;
		}
	}


}

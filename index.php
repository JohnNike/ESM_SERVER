<?php
//
// APPLICATION CONFIGURATION
//

session_start();

define('WEBPATH','');
// This is where your application code goes
define('APPPATH', WEBPATH);
// This is where your model php files go
define('MODELPATH', WEBPATH.'models/');
// This is where your view php files go
define('VIEWPATH', WEBPATH.'views/');
// This is where your controller php files go
define('CONTROLLERPATH', WEBPATH.'controllers/');
// This is where your config php files go
define('CONFIGPATH', WEBPATH.'configs/');
// This is where your template files go
define('TEMPLATEPATH', WEBPATH.'web/template/');
// This is where classes go
define('CLASSPATH', WEBPATH.'classes/');

// NOTE: In PHP7, these autoload lists can be moved to arrays
// This is the array of models we will load on every render
define('AUTOLOADMODELS', 'userModel');
// This is the array of libraries we will load on every render
// This is the array of configuration files we will load on every render
define('AUTOLOADCONFIGS', 'globalConfig');


// This is the default controller we will call if none is specified
$_JnDefaultController = 'main';
// This is the default function we call on your contollers if none is speicifed
$_JnDefaultFunction = 'main';

//
// GLOBALS CONFIGURATION
//

ini_set('MAX_EXECUTION_TIME', 10);
date_default_timezone_set('Europe/Athens');

//////// Jn PHP ///////
///////////////////////

// Setup a global for the index path.  We will use this to ensure the index file has been called.
define('INDEX', pathinfo(__FILE__, PATHINFO_BASENAME) );

// include configuration
require_once ('configurations.php');

// include our logger class
require_once  (CLASSPATH.'errorLogger.php');
$gLogger = new errorLogger();


function checkPrerequisites(errorLogger $gLogger) {

	if(function_exists('mcrypt_encrypt')) {
		;
	} else {
			$gLogger->log("ERROR: MCRYPT isn't loaded");
	}
}

checkPrerequisites($gLogger);

// include database handling class

require_once (CLASSPATH.'dbHandler.php');


////////////////////////////////// Request Parsing Start /////////////////////////////////

// Setup a global for the $_JnRequest so its easily accessible from anywhere
global $_JnRequest;

// Check for REQUEST_URI input (a web request)
if(isset($_SERVER['REQUEST_URI'])){
    // Parse request URL into a controller, model and arguments so we can act on it.
    $_JnRequest = pathinfo($_SERVER['REQUEST_URI']);
	$_JnRequest = $_JnRequest['dirname'].'/'.$_JnRequest['basename'];
	$_JnRequest = explode('/',$_JnRequest);
    $_JnRequest = array_filter($_JnRequest);
    $_JnRequest = array_values($_JnRequest);
} else {
	$gLogger->log("ERROR: Request Uri not set");
	include( APPPATH.'404.php' );
}

// Determine the controller to load and use
if( isset($_JnRequest) && !empty($_JnRequest[0]) ) {
    $_JnRequest[0] = rtrim( $_JnRequest[0], "/ ");
    $_JnController = $_JnRequest[0];
	if ($_JnController === "index.php") {
		$_JnController = $_JnDefaultController;
	}
	$_JnController = substr($_JnController, 0, strpos($_JnController, "?"));
} else {
    $_JnController = $_JnDefaultController;
}

if (empty($_JnController)) {
	$_JnController = $_JnDefaultController;
}


// Determine the function to use in the requested controller
if( isset( $_JnRequest[1] ) ) {

    $_JnRequest[1] = rtrim( $_JnRequest[1], "/ ");
    if( !empty( $_JnRequest[1] ) ) {
        $_JnFunction = $_JnRequest[1];
    }
	unset($_JnRequest[1]);
} else {
    $_JnFunction = $_JnDefaultFunction;
}
//////////////////////////////////  Request Parsing End  /////////////////////////////////

// This is the core class of JnPHP
class JnPHP {
	protected $db = null;

    // This is where application configuration is kept loaded
    public static $_JnConfig = array();

    // This is where the classes that have been loaded are stored
    public static $_JnClasses = array();

	function getDb() {
		return $this->db;
	}

    // This enables JnPHP to load classes each time it is subclassed
    function __construct(){

	    // Load all autoload configs
	    if(strlen(AUTOLOADCONFIGS) > 1){
		    $autoloadConfigs = explode(',',AUTOLOADCONFIGS);
		    foreach ( $autoloadConfigs as $config ) {
			    $this->loadConfig($config);
		    }
	    }

	    $this->db = new dbHandler(DB_NAME,DB_HOST,DB_USER,DB_PASSWORD);
        // Load all autoload models
        if(strlen(AUTOLOADMODELS) > 1){
            $autoloadModels = explode(',',AUTOLOADMODELS);
            foreach ( $autoloadModels as $model ) {
                $this->loadModel($model,$this->db);
            }
        }

    }

    // This function returns the requested class from our list of loaded classes.
    // New classes are loaded as needed. Existing ones are returned as pointers to their
    // already instantiated object.
    public function &loadClass($className,$attributes = array(),$overrideClass = NULL) {

        // if the class is already loaded in this class, return that class reference
        if(isset(self::$_JnClasses[$className])){
            return self::$_JnClasses[$className];
        }

        // class load logic
        if(!isset(JnPHP::$_JnClasses[$className])){
            if(isset($overrideClass)){
                JnPHP::$_JnClasses[$className] = new $overrideClass($attributes);
            } else {
                JnPHP::$_JnClasses[$className] = new $className($attributes);
            }

	        // Load all autoload configs into subclass
	        if(strlen(AUTOLOADCONFIGS) > 1){
		        $autoloadConfigs = explode(',',AUTOLOADCONFIGS);
		        foreach ( $autoloadConfigs as $autoloadConfig ) {
			        JnPHP::$_JnClasses[$className]->loadConfig($autoloadConfig);
		        }
	        }

	        // Load all autoload models into subclass
            if(strlen(AUTOLOADMODELS) > 1){
                $autoloadModels = explode(',',AUTOLOADMODELS);
                foreach ( $autoloadModels as $autoloadModel ) {
                    JnPHP::$_JnClasses[$className]->$autoloadModel =& JnPHP::$_JnClasses[$autoloadModel];
                }
            }

        }

        // Check if we have a parent class and create a pointer to it if we do
        if(get_parent_class($this) !== FALSE){
            self::$_JnClasses[$className] =& JnPHP::$_JnClasses[$className];
        }

        // Return the class we just created in our static classes array
        return self::$_JnClasses[$className];
    }

	//  MODEL LOADER
    public function loadModel( $model,$arguments = array() ) {

        if( empty ( $model ) ) {
            return;
        }

        require_once(MODELPATH."$model.php");

        // Initialize model
        $this->$model =& $this->loadClass($model,$arguments);
    }

    // VIEW LOADER
    public function loadView( $view,$content = array() ) {

        // Return if no view passed
        if( empty($view) ) {
            return;
        }

        // Break our content array out into variables before running our view include
        if( ! empty ( $content ) && is_array( $content ) ) {
            extract( $content );
        }

        // Include view
        include(VIEWPATH."$view.php");

    }

    // CONTROLLER LOADER
    public function loadController( $controller,$arguments = array() ) {

        if( empty ( $controller ) ) {
            return;
        }

        // Include controller class
        require_once(CONTROLLERPATH."$controller.php");

        // Initialize controller
        $this->$controller =& $this->loadClass($controller,$arguments);
    }



    //  INPUT PARAMETER LOADER
    public function loadArgs($arg = NULL) {

            // read in global for user request
            global $_JnRequest;

            // if only a single parameter is requested, only supply it
            if($arg !== NULL) {
                    $arg = $arg + 2;
                    $args = array_slice($_JnRequest,$arg);
                    if(isset($args[0])){
                            $args = $args[0];
                    } else {
                            $args = '';
                    }
            } else {
                    // offset requested arg for our input string parsing
                    $args = array_slice($_JnRequest,3);
            }

            if( empty( $args ) ) {
                    $args = '';
            }

            return $args;
    }

    // LIBRARY LOADER
    public function loadLibrary( $library,$arguments = array(),$overrideClass = NULL) {

        if( empty ( $library ) ) {
            return;
        }

        // Include library class
        require_once(LIBRARYPATH."$library/$library.php");


        // Initialize library
        $this->$library =& $this->loadClass($library,$arguments,$overrideClass);
    }



    // CONFIGURATION FILE LOADER
    public function loadConfig($configFile) {

        if( empty ( $configFile ) ) {
            return;
        }

        // Include config class
        include(CONFIGPATH."$configFile.php");
		if (isset($config)) {
			foreach ($config as $configItem => $configValue) {
				self::$_JnConfig[$configItem] = $configValue;
			}
		}
    }



    // CONFIGURATION SETTING LOADER
    public function getConfig($configItem) {

        if( ! empty ( self::$_JnConfig[$configItem] ) ) {

            return self::$_JnConfig[$configItem];
        }

    }

    public function sendResponse($response) {
        echo $response;
    }

}


// Initialize JnPHP
$JnPHP = new JnPHP();

// Check if the requested controller exists
if( file_exists( CONTROLLERPATH."$_JnController.php" ) !== TRUE){
	//include( APPPATH.'404.php' );
	header("Location: /");
    exit;
}

// Load requested controller
$JnPHP->loadController($_JnController);

// Check if the requested model exists
if( method_exists( $JnPHP->$_JnController, $_JnFunction ) !== TRUE){
	header("Location: /");
    exit;
}

$_JnRequest = array_values($_JnRequest);  // repack the array
foreach ($_JnRequest as $req) {
	// sanitize request variables
	$JnPHP->getDb()->cleanupParam($req) ;
}

// If a function was specified, call that one.  Otherwise, call the default one
$JnPHP->$_JnController->$_JnFunction();


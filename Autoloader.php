<?php

/**
 * Class Autoloader
 * By Slimen
 */

class Autoloader{
	/**
	 * Enregistre notre autoloader
	 */
	static function register(){
		
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}

	/**
	 * Inclue le fichier correspondant à notre classe
	 * @param $class string Le nom de la classe à charger
	 */
	static function autoload($class){
		$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
//		echo str_replace('framework\core\Controller\CrossRoadsRooter', '', $class);
//		echo $class;
		require $class.'.php';
	}
	
}
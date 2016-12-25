<?php
/*
 * Launching application by autoloading classes
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once 'vendor/autoload.php';

/**
 * Start parsing url and define which controller to fire
 */
use framework\core\Controller\CrossRoadsRooter;

// point of start
$timestart=microtime(true);

$crossRoadesRouter = new CrossRoadsRooter();
$crossRoadesRouter->parseRequest();

//End of PHP execution
$timeend=microtime(true);
$time=$timeend-$timestart;

//Display execution time
$page_load_time = number_format($time, 3);
echo '<div style="width: 100%; background: gray; color: #fff;position: fixed;
bottom: 0;
padding: 5px;">Script execute en '.$page_load_time.' sec</div>';

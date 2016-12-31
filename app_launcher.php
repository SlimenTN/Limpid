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

$crossRoadesRouter = new CrossRoadsRooter();
$crossRoadesRouter->parseRequest();
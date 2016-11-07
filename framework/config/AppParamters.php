<?php
namespace framework\config;

/**
 * Class AppParamters
 * Class sotres the static parameters of the app
 * @package framework\config
 *
 */
class AppParamters{

    /**
     * Enable or disable translator service
     * And set default language
     */
    const TRANSLATOR_ENABLED = false;
    const DEFAULT_LANG = 'fr';

    /**
     * The default route of 404 not found error
     */
    const PAGE_NOT_FOUND_ROUTE = '';

    /*
     * ============================================================================
     *              DATABASE PARAMETERS
     * ============================================================================
     */
    /**
     * Database's host
     */
    const DB_HOST = 'localhost';

    /**
     * Database's port
     */
    const DB_PORT = '';

    /**
     * Database's name
     */
    const DB_NAME = 'limpid';

    /**
     * Database's user
     */
    const DB_USER = 'root';

    /**
     * Database's password
     */
    const DB_PASSWORD = '';
}
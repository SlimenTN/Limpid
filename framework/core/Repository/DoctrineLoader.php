<?php

namespace framework\core\Repository;

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Logging\EchoSQLLogger;
use framework\config\AppParamters;

/**
 * Class DoctrineLoader
 * Init doctrine's service
 * @package framework\core\Repository
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class DoctrineLoader{

    private $em = null;

    public function __construct(){
        require_once __DIR__.'/../../../vendor/doctrine/common/lib/Doctrine/Common/ClassLoader.php';

        $doctrineClassLoader = new ClassLoader('Doctrine', '/');
        $doctrineClassLoader->register();
        $entitiesClassLoader = new ClassLoader('models', '/models/');
        $entitiesClassLoader->register();
        $proxiesClassLoader = new ClassLoader('Proxies', '/proxies/');
        $proxiesClassLoader->register();

        // Set up caches
        $config = new Configuration;
        $cache = new ArrayCache;
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver(array('/models/Entities'));
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);

        $config->setQueryCacheImpl($cache);

        // Proxy configuration
        $config->setProxyDir('data/DoctrineORM/proxies');
        $config->setProxyNamespace('DoctrineORM\Proxies');

        // Set up logger
        $logger = new EchoSQLLogger;
        //$config->setSQLLogger($logger);

        $config->setAutoGenerateProxyClasses(TRUE);

        // Database connection information
        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => AppParamters::DB_USER,
            'password' => AppParamters::DB_PASSWORD,
            'host' => AppParamters::DB_HOST,
            'dbname' => AppParamters::DB_NAME,
            'charset' => 'UTF8',
        );

        // Create EntityManager
        $this->em = EntityManager::create($connectionOptions, $config);
    }

    /**
     * @return EntityManager|null
     */
    public function getEntityManager(){
        return $this->em;
    }
}
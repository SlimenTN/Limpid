<?php

namespace framework\core\Repository;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

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
        $driverImpl = new AnnotationDriver(new AnnotationReader());
        
        // registering noop annotation autoloader - allow all annotations by default
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);

        $config->setQueryCacheImpl($cache);

        // Proxy configuration
        $config->setProxyDir('data/DoctrineORM/proxies');
        $config->setProxyNamespace('DoctrineORM\Proxies');

        $config->setAutoGenerateProxyClasses(TRUE);

        $settings = include __DIR__ . '/../../config/settings.php';
        // Database connection information
        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => $settings['doctrine']['user'],
            'password' => $settings['doctrine']['password'],
            'host' => $settings['doctrine']['host'],
            'dbname' => $settings['doctrine']['database'],
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
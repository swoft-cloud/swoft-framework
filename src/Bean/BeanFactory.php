<?php

namespace Swoft\Bean;

use Swoft\Aop\Aop;
use Swoft\App;
use Swoft\Bean\Collector\BootBeanCollector;
use Swoft\Core\Config;
use Swoft\Helper\ArrayHelper;
use Swoft\Helper\DirHelper;

/**
 * Bean Factory
 */
class BeanFactory implements BeanFactoryInterface
{

    /**
     * @var Container Bean container
     */
    private static $container = null;

    /**
     * Init beans
     */
    public static function init()
    {
        $properties = self::getProperties();

        self::$container = new Container();
        self::$container->setProperties($properties);
        self::$container->autoloadServerAnnotation();

        $definition  = self::getServerDefinition();
        self::$container->addDefinitions($definition);
        self::$container->initBeans();
    }

    /**
     * Reload bean definitions
     *
     * @param array $definitions append definitions to config loader
     */
    public static function reload(array $definitions = [])
    {
        $properties = self::getProperties();
        $workerDefinitions = self::getWorkerDefinition();
        $definitions = ArrayHelper::merge($workerDefinitions, $definitions);

        self::$container->setProperties($properties);
        self::$container->addDefinitions($definitions);
        self::$container->autoloadWorkerAnnotation();

        /* @var Aop $aop Init reload AOP */
        $aop = App::getBean(Aop::class);
        $aop->init();

        self::$container->initBeans();
    }

    /**
     * Get bean from container
     *
     * @param string $name Bean name
     * @return mixed
     */
    public static function getBean(string $name)
    {
        return self::$container->get($name);
    }

    /**
     * Determine if bean exist in container
     *
     * @param string $name Bean name
     * @return bool
     */
    public static function hasBean(string $name): bool
    {
        return self::$container->hasBean($name);
    }

    /**
     * @return array
     */
    private static function getWorkerDefinition()
    {
        $configDefinitions = [];
        $beansDir = App::getAlias('@beans');
        if (is_readable($beansDir)) {
            $config = new Config();
            $config->load($beansDir, [], DirHelper::SCAN_BFS, Config::STRUCTURE_MERGE);
            $configDefinitions = $config->toArray();
        }

        $coreBeans = self::getCoreBean(BootBeanCollector::TYPE_WORKER);
        $definitions = ArrayHelper::merge($coreBeans, $configDefinitions);
        return $definitions;
    }

    /**
     * @return array
     */
    private static function getServerDefinition()
    {
        $file = App::getAlias('@console');
        $configDefinition = [];
        if (is_readable($file)) {
            $configDefinition = require_once $file;
        }

        $coreBeans = self::getCoreBean(BootBeanCollector::TYPE_SERVER);
        $definition = ArrayHelper::merge($coreBeans, $configDefinition);

        return $definition;
    }

    /**
     * @return array
     */
    private static function getProperties()
    {
        $properties = [];
        $config = new Config();
        $dir = App::getAlias('@properties');
        if (is_readable($dir)) {
            $config->load($dir);
            $properties = $config->toArray();
        }
        return $properties;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private static function getCoreBean(string $type): array
    {
        $collector = BootBeanCollector::getCollector();
        if (!isset($collector[$type])) {
            return [];
        }

        $coreBeans = [];
        $bootBeans = $collector[$type];
        foreach ($bootBeans as $beanName) {
            /* @var \Swoft\Core\BootBeanIntereface $bootBean */
            $bootBean  = App::getBean($beanName);
            $beans     = $bootBean->beans();
            $coreBeans = ArrayHelper::merge($coreBeans, $beans);
        }

        return $coreBeans;
    }
}

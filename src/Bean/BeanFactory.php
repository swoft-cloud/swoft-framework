<?php

namespace Swoft\Bean;

use Monolog\Formatter\LineFormatter;
use Swoft\Aop\Aop;
use Swoft\App;
use Swoft\Core\Config;
use Swoft\Helper\ArrayHelper;
use Swoft\Helper\DirHelper;
use Swoft\Pool\BalancerSelector;
use Swoft\Pool\ProviderSelector;
use Swoft\Core\Application;
use Swoft\Event\EventManager;

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
        self::$container = new Container();
        self::$container->autoloadServerAnnotations();
        self::$container->initBeans();
    }

    /**
     * Reload bean definitions
     *
     * @param array $definitions append definitions to config loader
     */
    public static function reload(array $definitions = [])
    {
        $config = new Config();
        $config->load(App::getAlias('@beans'), [], DirHelper::SCAN_BFS, Config::STRUCTURE_MERGE);
        $configDefinitions = $config->toArray();
        $mergeDefinitions = ArrayHelper::merge($configDefinitions, $definitions);

        $definitions = self::merge($mergeDefinitions);

        if (self::$container === null) {
            self::$container = new Container();
        }
        self::$container->addDefinitions($definitions);
        self::$container->autoloadAnnotations();

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
     * Framework core beans definitions
     *
     * @return array
     */
    private static function coreBeans(): array
    {
        return [
            'config'           => [
                'class'      => Config::class,
                'properties' => value(function () {
                    $config = new Config();
                    $config->load('@properties');
                    return $config->toArray();
                }),
            ],
            'application'      => [
                'class' => Application::class
            ],
            'eventManager'     => [
                'class' => EventManager::class
            ],
            'balancerSelector' => [
                'class' => BalancerSelector::class
            ],
            'providerSelector' => [
                'class' => ProviderSelector::class
            ],
            'lineFormatter'    => [
                'class'      => LineFormatter::class,
                'format'     => '%datetime% [%level_name%] [%channel%] [logid:%logid%] [spanid:%spanid%] %messages%',
                'dateFormat' => 'Y/m/d H:i:s',
            ],
        ];
    }

    /**
     * Merge default bean config and user bean config
     *
     * @param array $definitions
     * @return array
     */
    private static function merge(array $definitions): array
    {
        $definitions = ArrayHelper::merge(self::coreBeans(), $definitions);

        return $definitions;
    }
}

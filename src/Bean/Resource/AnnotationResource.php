<?php

namespace Swoft\Bean\Resource;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Swoft\Bean\Wrapper\WrapperInterface;
use Swoft\Helper\ComponentHelper;

/**
 * 注释解析
 *
 * @uses      AnnotationResource
 * @version   2017年08月18日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AnnotationResource extends AbstractResource
{
    /**
     * 自动扫描命令空间
     *
     * @var array
     */
    private $scanNamespaces = [];

    /**
     * scan files
     *
     * @var array
     */
    private $scanFiles = [];

    /**
     * 已解析的bean定义
     *
     * @var array
     * <pre>
     * [
     *     'beanName' => ObjectDefinition,
     *      ...
     * ]
     * </pre>
     */
    private $definitions = [];


    /**
     * @var array
     */
    private $annotations = [];

    /**
     * @var array
     */
    private $serverScan = [
        'Console',
        'Bootstrap',
        'Aop',
    ];

    /**
     * the ns of component
     *
     * @var array
     */
    private $componentNamespaces = [];

    /**
     * AnnotationResource constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * 获取已解析的配置beans
     *
     * @return array
     * <pre>
     * [
     *     'beanName' => ObjectDefinition,
     *      ...
     * ]
     * </pre>
     */
    public function getDefinitions()
    {
        // 获取扫描的PHP文件
        $classNames = $this->registerLoaderAndScanBean();
        $fileClassNames = $this->scanFilePhpClass();
        $classNames = array_merge($classNames, $fileClassNames);

        foreach ($classNames as $className) {
            $this->parseBeanAnnotations($className);
        }

        $this->parseAnnotationsData();

        return $this->definitions;
    }

    /**
     * 解析bean注解
     *
     * @param string $className
     *
     * @return null
     */
    public function parseBeanAnnotations(string $className)
    {
        if (!class_exists($className) && !interface_exists($className)) {
            return null;
        }

        // 注解解析器
        $reader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        $classAnnotations = $reader->getClassAnnotations($reflectionClass);

        // 没有类注解不解析其它注解
        if (empty($classAnnotations)) {
            return ;
        }

        foreach ($classAnnotations as $classAnnotation) {
            $this->annotations[$className]['class'][get_class($classAnnotation)] = $classAnnotation;
        }

        // 解析属性
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $propertyName = $property->getName();
            $propertyAnnotations = $reader->getPropertyAnnotations($property);


            foreach ($propertyAnnotations as $propertyAnnotation) {
                $this->annotations[$className]['property'][$propertyName][get_class($propertyAnnotation)] = $propertyAnnotation;
            }
        }

        // 解析方法
        $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($publicMethods as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $methodName = $method->getName();

            // 解析方法注解
            $methodAnnotations = $reader->getMethodAnnotations($method);

            foreach ($methodAnnotations as $methodAnnotation) {
                $this->annotations[$className]['method'][$methodName][get_class($methodAnnotation)][] = $methodAnnotation;
            }
        }
    }

    /**
     * 解析注解数据
     */
    public function parseAnnotationsData()
    {
        foreach ($this->annotations as $className => $annotation) {
            $classAnnotations = $annotation['class'];
            $this->parseClassAnnotations($className, $annotation, $classAnnotations);
        }
    }

    /**
     * 添加扫描namespace
     *
     * @param array $namespaces
     */
    public function addScanNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace) {
            $nsPath = str_replace("\\", "/", $namespace);
            $nsPath = str_replace('App/', 'app/', $nsPath);
            $this->scanNamespaces[$namespace] = BASE_PATH . "/" . $nsPath;
        }

        $this->autoRegisterNamespaces();
    }

    /**
     * 类注解封装
     *
     * @param string $className
     * @param array  $annotation
     * @param array  $classAnnotations
     */
    private function parseClassAnnotations(string $className, array $annotation, array $classAnnotations)
    {
        foreach ($classAnnotations as $classAnnotation) {
            $annotationClassName = get_class($classAnnotation);
            $classNameTmp = str_replace('\\', '/', $annotationClassName);
            $classFileName = basename($classNameTmp);

            // do wrappers
            foreach ($this->componentNamespaces as $componentNamespace) {
                $annotationParserClassName = "{$componentNamespace}\\Bean\\Wrapper\\{$classFileName}Wrapper";
                if (!class_exists($annotationParserClassName)) {
                    continue;
                }

                /* @var WrapperInterface $wrapper */
                $wrapper = new $annotationParserClassName($this);
                $objectDefinitionAry = $wrapper->doWrapper($className, $annotation);
                if ($objectDefinitionAry != null) {
                    list($beanName, $objectDefinition) = $objectDefinitionAry;
                    $this->definitions[$beanName] = $objectDefinition;
                }
            }
        }
    }

    /**
     * auto register namespaces
     */
    public function autoRegisterServerNamespaces()
    {
        $swoftDir      = dirname(__FILE__, 5);
        $componentDirs = scandir($swoftDir);
        foreach ($componentDirs as $component) {
            if ($component == '.' || $component == '..') {
                continue;
            }

            $componentCommandDir = $swoftDir . DS . $component . DS . 'src';
            if (!is_dir($componentCommandDir)) {
                continue;
            }

            $componentNs = ComponentHelper::getComponentNs($component);
            $ns          = "Swoft{$componentNs}";
            $this->componentNamespaces[] = $ns;

            foreach ($this->serverScan as $dir){
                $scanDir = $componentCommandDir . DS . $dir;
                if(!is_dir($scanDir)){
                    continue;
                }

                $scanNs  = $ns . "\\" . $dir;
                $this->scanNamespaces[$scanNs] = $scanDir;
            }
        }
    }

    /**
     * auto register namespaces
     */
    private function autoRegisterNamespaces()
    {
        $swoftDir      = dirname(__FILE__, 5);
        $componentDirs = scandir($swoftDir);
        foreach ($componentDirs as $component) {
            if ($component == '.' || $component == '..') {
                continue;
            }

            $componentCommandDir = $swoftDir . DS . $component . DS . 'src';
            if (!is_dir($componentCommandDir)) {
                continue;
            }
            $componentNs = ComponentHelper::getComponentNs($component);

            $ns = "Swoft{$componentNs}";

            $this->componentNamespaces[] = $ns;

            $scanDirs = scandir($componentCommandDir);
            foreach ($scanDirs as $dir) {
                if ($dir == '.' || $dir == '..') {
                    continue;
                }
                if(in_array($dir, $this->serverScan, true)){
                    continue;
                }
                $scanDir = $componentCommandDir . DS . $dir;

                if(!is_dir($scanDir)){
                    $this->scanFiles[$ns][] = $scanDir;
                    continue;
                }
                $scanNs  = $ns . "\\" . $dir;

                $this->scanNamespaces[$scanNs] = $scanDir;
            }
        }
    }

    /**
     * 扫描目录下PHP文件
     *
     * @param string $dir
     * @param string $namespace
     *
     * @return array
     */
    private function scanPhpFile(string $dir, string $namespace)
    {
        $iterator = new \RecursiveDirectoryIterator($dir);
        $files = new \RecursiveIteratorIterator($iterator);

        $phpFiles = [];
        foreach ($files as $file) {
            $fileType = pathinfo($file, PATHINFO_EXTENSION);
            if ($fileType != 'php') {
                continue;
            }

            $replaces = ["", '\\', "", ""];
            $searches = [$dir, '/', '.php', '.PHP'];

            $file = str_replace($searches, $replaces, $file);
            $phpFiles[] = $namespace . $file;
        }

        return $phpFiles;
    }

    /**
     * scan files
     */
    private function scanFilePhpClass()
    {
        $phpClass = [];
        foreach ($this->scanFiles as $ns => $files) {
            foreach ($files as $file){
                $pathInfo = pathinfo($file);
                if (!isset($pathInfo['filename'])) {
                    continue;
                }
                $phpClass[] = $ns . "\\" . $pathInfo['filename'];
            }
        }

        return $phpClass;
    }

    /**
     * 注册加载器和扫描PHP文件
     *
     * @return array
     */
    private function registerLoaderAndScanBean()
    {
        $phpClass = [];
        foreach ($this->scanNamespaces as $namespace => $dir) {
            AnnotationRegistry::registerLoader(function ($class) {
                if (class_exists($class) || interface_exists($class)) {
                    return true;
                }
                return false;
            });
            $scanClass = $this->scanPhpFile($dir, $namespace);
            $phpClass = array_merge($phpClass, $scanClass);
        }

        return array_unique($phpClass);
    }
}

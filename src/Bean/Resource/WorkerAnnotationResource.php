<?php

namespace Swoft\Bean\Resource;

use Swoft\Helper\ComponentHelper;
use Swoft\Helper\JsonHelper;

/**
 *  The annotation resource of worker
 */
class WorkerAnnotationResource extends AnnotationResource
{
    /**
     * Register the scaned namespace
     */
    public function registerNamespace()
    {
        $swoftDir = \dirname(__FILE__, 5);
        $componentDirs = scandir($swoftDir, null);
        foreach ($componentDirs as $component) {
            if ($component === '.' || $component === '..') {
                continue;
            }

            $componentDir = $swoftDir . DS . $component;
            $componentCommandDir = $componentDir . DS . 'src';
            if (! is_dir($componentCommandDir)) {
                continue;
            }
            $composerFile = $componentDir . DS . 'composer.json';
            $namespaceMapping = $this->parseAutoloadFromComposerFile($composerFile);
            $ns = $namespaceMapping['src/'] ?? $this->getDefaultNamespace($component);

            $this->componentNamespaces[] = $ns;

            // ignore the comoponent of console
            if ($component === $this->consoleName) {
                continue;
            }

            $scanDirs = scandir($componentCommandDir, null);
            foreach ($scanDirs as $dir) {
                if ($dir == '.' || $dir == '..') {
                    continue;
                }
                if (\in_array($dir, $this->serverScan, true)) {
                    continue;
                }
                $scanDir = $componentCommandDir . DS . $dir;

                if (! is_dir($scanDir)) {
                    $this->scanFiles[$ns][] = $scanDir;
                    continue;
                }
                $scanNs = $ns . "\\" . $dir;

                $this->scanNamespaces[$scanNs] = $scanDir;
            }
        }
    }

    /**
     * @param string $filename
     * @return array
     */
    protected function parseAutoloadFromComposerFile($filename): array
    {
        $json = file_get_contents($filename);
        try {
            $content = JsonHelper::decode($json, true);
        } catch (\InvalidArgumentException $e) {
            $content = [];
        }
        // only compatible with psr-4 now
        //TODO compatible with the another autoload standard
        if (isset($content['autoload']['psr-4'])) {
            $mapping = $content['autoload']['psr-4'];
            $mapping = array_flip($mapping);
            foreach ($mapping as $key => $value) {
                $valueLength = \strlen($value);
                $mapping[$key] = $value[$valueLength - 1] === '\\' ? substr($value, 0, $valueLength - 1) : $value;
            }
        }
        return \is_array($mapping) ? $mapping : [];
    }

    /**
     * @param $component
     * @return string
     */
    protected function getDefaultNamespace($component): string
    {
        $componentNs = ComponentHelper::getComponentNs($component);
        $componentNs = $this->handlerFrameworkNamespace($componentNs);
        $namespace = "Swoft{$componentNs}";
        return $namespace;
    }
}
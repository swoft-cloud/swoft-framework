<?php

namespace Swoft\Bean\Resource;

use Swoft\Helper\ComponentHelper;

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
            $componentNs = $this->handlerFrameworkNamespace($componentNs);

            $ns = "Swoft{$componentNs}";

            $this->componentNamespaces[] = $ns;

            // ignore the comoponent of console
            if ($component == $this->consoleName) {
                continue;
            }

            $scanDirs = scandir($componentCommandDir);
            foreach ($scanDirs as $dir) {
                if ($dir == '.' || $dir == '..') {
                    continue;
                }
                if (in_array($dir, $this->serverScan, true)) {
                    continue;
                }
                $scanDir = $componentCommandDir . DS . $dir;

                if (!is_dir($scanDir)) {
                    $this->scanFiles[$ns][] = $scanDir;
                    continue;
                }
                $scanNs = $ns . "\\" . $dir;

                $this->scanNamespaces[$scanNs] = $scanDir;
            }
        }
    }
}
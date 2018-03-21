<?php
namespace Swoft\Devtool\Command;

use Swoft\Console\Bean\Annotation\Mapping;
use Swoft\Devtool\PharCompiler;
use Swoft\Helper\DirHelper;
use Swoft\Console\Bean\Annotation\Command;

/**
 * There are some help command for application
 *
 * @Command(coroutine=false)
 * @uses      AppCommand
 * @version   2017年10月06日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AppCommand
{
    /**
     * init the project, will create runtime dirs
     *
     * @Usage
     * app:{command} [arguments] [options]
     *
     * @Options
     *   --test start by daemonized process
     *
     * @Mapping("init")
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function initApp()
    {
        output()->writeln('Create runtime directoies: ', false);

        $tmpDir = \Swoft\App::getAlias('@runtime');
        $dirs = [
            'logs',
            'uploadfiles'
        ];

        foreach ($dirs as $dir) {
            DirHelper::mkdir($tmpDir . '/' . $dir);
        }

        output()->writeln('<success>OK</success>');
    }

    /**
     * pack project to a phar package
     * @Usage {fullCommand} [--dir DIR] [--output FILE]
     * @Options
     *   --dir STRING            Setting the project directory for packing.
     *                           - default is current work-dir.(<comment>{workDir}</comment>)
     *   --fast BOOL             Fast build. only add modified files by <cyan>git status -s</cyan>
     *   --output STRING         Setting the output file name(<comment>app.phar</comment>)
     *   --refresh BOOL          Whether build vendor folder files on phar file exists(<comment>False</comment>)
     *   -c, --config STRING     Use the defined config for build phar.
     * @Example
     *   {fullCommand}                                  Pack current dir to a phar file.
     *   {fullCommand} --dir vendor/swoft/devtool       Pack the specified dir to a phar file.
     * @return int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     * @throws \RuntimeException
     */
    public function pack(): int
    {
        $time = microtime(1);
        $workDir = input()->getPwd();

        $dir = \input()->getOpt('dir') ?: $workDir;
        $cpr = $this->configCompiler($dir);

        $counter = null;
        $refresh = input()->getOpt('refresh');
        $pharFile = $workDir . '/' . input()->getOpt('output', 'app.phar');

        // use fast build
        if (\input()->getOpt('fast')) {
            $cpr->setModifies($cpr->findChangedByGit());

            \output()->writeln(
                '<info>[INFO]</info>Use fast build, will only pack changed or new files(from git status)'
            );
        }

        \output()->writeln(
            "Now, will begin building phar package.\n from path: <comment>$dir</comment>\n" .
            " phar file: <info>$pharFile</info>"
        );

        \output()->writeln('<info>Pack file to Phar ... ... </info>');
        $cpr->onError(function ($error) {
            \output()->writeln("<warning>$error</warning>");
        });

        if (input()->getOpt('debug')) {
            $cpr->onAdd(function ($path) {
                \output()->writeln(" <comment>+</comment> $path");
            });
        }

        // packing ...
        $cpr->pack($pharFile, $refresh);

        $info = [
            PHP_EOL . '<success>Phar build completed!</success>',
            " - Phar file: $pharFile",
            ' - Phar size: ' . round(filesize($pharFile) / 1024 / 1024, 2) . ' Mb',
            ' - Pack Time: ' . round(microtime(1) - $time, 3) . ' s',
            ' - Pack File: ' . $cpr->getCounter(),
            ' - Commit ID: ' . $cpr->getVersion(),
        ];
        \output()->writeln(\implode("\n", $info));

        return 0;
    }

    /**
     * unpack a phar package to a directory
     * @Usage {fullCommand} -f FILE [-d DIR]
     * @Options
     *   -f, --file STRING   The packed phar file path
     *   -d, --dir STRING    The output dir on extract phar package.
     *   -y, --yes BOOL      Whether display goon tips message.
     *   --overwrite BOOL    Whether overwrite exists files on extract phar
     * @Example {fullCommand} -f myapp.phar -d var/www/app
     * @return int
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     */
    public function unpack(): int
    {
        if (!$path = \input()->getSameOpt(['f', 'file'])) {
            return \output()->writeln("<error>Please input the phar file path by option '-f|--file'</error>");
        }

        $basePath = \input()->getPwd();
        $file = realpath($basePath . '/' . $path);

        if (!file_exists($file)) {
            return \output()->writeln("<error>The phar file not exists. File: $file</error>");
        }

        $dir = input()->getSameOpt(['d', 'dir']) ?: $basePath;
        $overwrite = input()->getOpt('overwrite');

        if (!is_dir($dir)) {
            DirHelper::mkdir($dir);
        }

        \output()->writeln("Now, begin extract phar file:\n $file \nto dir:\n $dir");

        PharCompiler::unpack($file, $dir, null, $overwrite);

        \output()->writeln("<success>OK, phar package have been extract to the dir: $dir</success>");

        return 0;
    }

    /**
     * @param string $dir
     * @return PharCompiler
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function configCompiler(string $dir): PharCompiler
    {
        // config
        $compiler = new PharCompiler($dir);

        // config file.
        $configFile = input()->getSameOpt(['c', 'config']) ?: $dir . '/phar.build.inc';

        if ($configFile && is_file($configFile)) {
            require $configFile;

            $compiler->in($dir);

            return $compiler;
        }

        throw new \InvalidArgumentException("The phar build config file not found. File: $configFile");
    }
}

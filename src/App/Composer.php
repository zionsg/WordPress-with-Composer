<?php

namespace App;

use Composer\Script\Event;

class Composer
{
    /**
     * @var string
     */
    protected static $projectRoot = null;

    /**
     * @var string[]
     */
    protected static $childThemePaths = null;

    /**
     * Run before install command is executed with a lock file present
     *
     * @param  Event $event
     * @return void
     */
    public static function preInstall(Event $event)
    {
        self::preInstallUpdate($event);
    }

    /**
     * Run after install command is executed with a lock file present
     *
     * @param  Event $event
     * @return void
     */
    public static function postInstall(Event $event)
    {
        self::postInstallUpdate($event);
    }

    /**
     * Run before update command is executed, or before install command is executed without a lock file present
     *
     * @param  Event $event
     * @return void
     */
    public static function preUpdate(Event $event)
    {
        self::preInstallUpdate($event);
    }

    /**
     * Run after update command is executed, or after install command is executed without a lock file present
     *
     * @param  Event $event
     * @return void
     */
    public static function postUpdate(Event $event)
    {
        self::postInstallUpdate($event);
    }

    /**
     * Run after install/update command
     *
     * @param  Event $event
     * @return void
     */
    protected static function postInstallUpdate(Event $event)
    {
        self::copyOutChildThemes($event, false);
    }

    /**
     * Run before install/update command
     *
     * @param  Event $event
     * @return void
     */
    protected static function preInstallUpdate(Event $event)
    {
        self::copyOutChildThemes($event, true);
    }

    /**
     * Copy child themes
     *
     * @param  Event $event
     * @param  bool $copyOut True to copy out themes before install/update, false to copy back after install/update
     * @return void
     */
    protected static function copyOutChildThemes(Event $event, bool $copyOut)
    {
        $tmpPath = self::getTempPath($event);
        $childThemePaths = self::getChildThemePaths($event);
        if (! $childThemePaths) {
            return;
        }

        if ($copyOut) {
            if (! file_exists($tmpPath)) {
                mkdir($tmpPath);
            }

            foreach (self::getChildThemePaths($event) as $srcPath) {
                $folder = basename($srcPath);
                shell_exec("cp -r {$srcPath} {$tmpPath}/{$folder}");
            }
        } else {
            $themePath = dirname($childThemePaths[0]);
            if (! file_exists($tmpPath) || ! file_exists($themePath)) {
                return;
            }

            shell_exec("cp -r {$tmpPath}/* {$themePath}/");
            shell_exec("rm -fr {$tmpPath}"); // @todo dangerous - need to validate tmpPath?
        }
    }

    /**
     * Get child theme paths
     *
     * @param  Event $event
     * @return array
     */
    protected static function getChildThemePaths(Event $event)
    {
        if (null === self::$childThemePaths) {
            $projectRoot = self::getProjectRoot($event);
            $gitIgnorePath = $projectRoot . '/.gitignore';
            $lines = file($gitIgnorePath);

            $result = [];
            foreach ($lines as $line) {
                if (preg_match('~^\!(.+/wp-content/themes/[a-z0-9\-_]+)$~i', trim($line), $matches)) {
                    $result[] = $projectRoot . '/'. $matches[1];
                }
            }

            self::$childThemePaths = $result;
        }

        return self::$childThemePaths;
    }

    /**
     * Get temporary path to store child themes
     *
     * @param  Event $event
     * @return string
     */
    protected static function getTempPath(Event $event)
    {
        return self::getProjectRoot($event) . '/tmp';
    }

    /**
     * Get path to project root
     *
     * @param  Event $event
     * @return string
     */
    protected static function getProjectRoot(Event $event)
    {
        if (null === self::$projectRoot) {
            $configSource = $event->getComposer()->getConfig()->getConfigSource();
            self::$projectRoot = dirname($configSource->getName());
        }

        return self::$projectRoot;
    }
}

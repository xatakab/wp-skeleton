<?php
/**
 * Created by PhpStorm.
 * User: TTakabayashi
 * Date: 2014/11/21
 * Time: 10:16
 */
use Composer\Script\Event;

class SkeletonInstaller
{
    public static function hookRootPackageInstall(Event $event = null)
    {
        $skeletonRoot = dirname(__DIR__);
        $splFile = new \SplFileInfo($skeletonRoot);
        $folderName = $splFile->getFilename();
        list($vendorName, $packageName) = explode('.', $folderName);
        $jobChmod = function (\SplFileInfo $file) {
            chmod($file, 0777);
        };
        $jobRename = function (\SplFileInfo $file) use ($vendorName, $packageName) {
            $fineName = $file->getFilename();
            if ($file->isDir() || strpos($fineName, '.') === 0 || ! is_writable($file)) {
                return;
            }
            $contents = file_get_contents($file);
            $contents = str_ireplace('Wp.Skeleton', $vendorName.'.'.$packageName, $contents);
            $contents = str_ireplace('wpskeleton', strtolower($vendorName . $packageName), $contents);
            $contents = str_replace('Skeleton', $packageName, $contents);
            $contents = str_replace('Wp', $vendorName, $contents);
            $contents = str_replace('{package_name}', strtolower("{$vendorName}/{$packageName}"), $contents);
            file_put_contents($file, $contents);
        };
        // rename file contents
        self::recursiveJob("{$skeletonRoot}/skeleton/plugin", $jobRename);
        rename("{$skeletonRoot}/skeleton/plugin/WpSkeleton.php", "{$skeletonRoot}/skeleton/plugin/{$vendorName}{$packageName}.php");


        self::recursiveJob("{$skeletonRoot}/skeleton/theme", $jobRename);
        rename("{$skeletonRoot}/skeleton/theme/languages/wpskeleton.pot", "{$skeletonRoot}/theme/languages/" . strtolower($vendorName . $packageName) . ".pod");


        $jobRename(new \SplFileInfo("{$skeletonRoot}/skeleton/composer.json"));
        $jobRename(new \SplFileInfo("{$skeletonRoot}/.gitignore"));

        // composer.json
        unlink("{$skeletonRoot}/composer.json");
        rename("{$skeletonRoot}/skeleton/composer.json", "{$skeletonRoot}/composer.json");


        rename(__DIR__ . '/theme', $skeletonRoot . '/httpdocs/content/themes/' . strtolower($vendorName . $packageName));
        rename(__DIR__ . '/plugin', $skeletonRoot . '/httpdocs/content/plugins/' . $vendorName . $packageName);


        $configContent = file_get_contents(__DIR__ . '/wp-config.php');
        $salts = @file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/');

        if ($salts) {
            $configContent = preg_replace('/define\(\'AUTH_KEY\'.+define\(\'NONCE_SALT\',\s*\'put your unique phrase here\'\);/s', $salts, $configContent);
        }
        file_put_contents(__DIR__ . '/wp-config.php', $configContent);

        rename(__DIR__ . '/wp-config.php', dirname(__DIR__) . '/httpdocs/wp/wp-config.php');
        rename(__DIR__ . '/wp-local-config.php', dirname(__DIR__) . '/httpdocs/wp/wp-local-config.php');


        unlink(__FILE__);
        rmdir(__DIR__);
    }

    /**
     * @param string   $path
     * @param Callable $job
     *
     * @return void
     */
    private static function recursiveJob($path, $job)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach($iterator as $file) {
            $job($file);
        }
    }

}

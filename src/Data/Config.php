<?php
namespace Spark\Project\Data;

use \Exception;

/**
 * This class was make solely to prevent me from using a global variable.
 */
class Config
{
    public static $bob = "is my uncle";

    private static $configDirectories = array(__DIR__.'/../../', __DIR__.'/../');
    private static $xmlConfig = null;

    public static function parse($filename)
    {
        $filepath = static::locate($filename);

        if (is_null($filepath)) {
            throw new Exception("The file '{$filename}' was not found");
        }

        static::$xmlConfig = new XmlConfig($filepath);
    }

    public static function locate($filename)
    {
        $filepath = null;

        foreach (static::$configDirectories as $dir) {
            if (is_file($dir.$filename)) {
                $filepath = realpath($dir.$filename);
                break;
            }
        }

        return $filepath;
    }


    public static function __callStatic($name, $arguments)
    {
        if ( is_null(static::$xmlConfig) ) {
            return null; // throw error instead?
        }

        // TODO check if method exists
        return call_user_func_array(array(static::$xmlConfig, $name), $arguments);
    }
}

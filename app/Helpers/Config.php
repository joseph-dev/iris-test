<?php

namespace App\Helpers;

/**
 * Class Config
 * @author Yosyp Mykhailiv <y.mykhailiv@bvblogic.com>
 */
class Config
{
    /**
     * @param string $path
     * @return mixed|null
     */
    public static function get(string $path)
    {
        $parts = explode('.', $path);

        if (!count($parts)) {
            return null;
        }

        $configFilePath = __DIR__ . '/../../config/' . $parts[0] . '.php';
        if (!file_exists($configFilePath)) {
            return null;
        }

        $config = include $configFilePath;

        array_shift($parts);

        foreach ($parts as $part) {
            if (isset($config[$part])) {
                $config = $config[$part];
            } else {
                $config = null;
                break;
            }
        }

        return $config;
    }
}
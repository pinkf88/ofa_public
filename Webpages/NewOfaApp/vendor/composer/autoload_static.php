<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit51eba01034a68ba85ffe470cecc9a7eb
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zend\\' => 5,
            'ZendXml\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zend\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zendframework/library/Zend',
        ),
        'ZendXml\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zendxml/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit51eba01034a68ba85ffe470cecc9a7eb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit51eba01034a68ba85ffe470cecc9a7eb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}

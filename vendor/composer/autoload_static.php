<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit86d8df8ef31f30d89e7fe35c1827ada7
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kernel\\' => 7,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kernel\\' => 
        array (
            0 => __DIR__ . '/../..' . '/kernel',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit86d8df8ef31f30d89e7fe35c1827ada7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit86d8df8ef31f30d89e7fe35c1827ada7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit86d8df8ef31f30d89e7fe35c1827ada7::$classMap;

        }, null, ClassLoader::class);
    }
}
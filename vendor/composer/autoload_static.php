<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6ee7540e645cbc4e4b58ca0b8190e59f
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MoDynamics365ObjectSync\\Wrappers\\' => 33,
            'MoDynamics365ObjectSync\\View\\' => 29,
            'MoDynamics365ObjectSync\\Observer\\' => 33,
            'MoDynamics365ObjectSync\\Controller\\' => 35,
            'MoDynamics365ObjectSync\\API\\' => 28,
            'MoDynamics365ObjectSync\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MoDynamics365ObjectSync\\Wrappers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Wrappers',
        ),
        'MoDynamics365ObjectSync\\View\\' => 
        array (
            0 => __DIR__ . '/../..' . '/View',
        ),
        'MoDynamics365ObjectSync\\Observer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Observer',
        ),
        'MoDynamics365ObjectSync\\Controller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Controller',
        ),
        'MoDynamics365ObjectSync\\API\\' => 
        array (
            0 => __DIR__ . '/../..' . '/API',
        ),
        'MoDynamics365ObjectSync\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6ee7540e645cbc4e4b58ca0b8190e59f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6ee7540e645cbc4e4b58ca0b8190e59f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6ee7540e645cbc4e4b58ca0b8190e59f::$classMap;

        }, null, ClassLoader::class);
    }
}
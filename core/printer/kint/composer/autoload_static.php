<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit199a8c05f1395a69c72bd38d5edee693
{
    public static $files = array (
        '3917c79c5052b270641b5a200963dbc2' => __DIR__ . '/..' . '/kint-php/kint/init.php',
    );

    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kint\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kint\\' => 
        array (
            0 => __DIR__ . '/..' . '/kint-php/kint/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit199a8c05f1395a69c72bd38d5edee693::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit199a8c05f1395a69c72bd38d5edee693::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit199a8c05f1395a69c72bd38d5edee693::$classMap;

        }, null, ClassLoader::class);
    }
}

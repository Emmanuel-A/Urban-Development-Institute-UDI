<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8540580aa0536b5238e3073069e5f077
{
    public static $files = array (
        '2c102faa651ef8ea5874edb585946bce' => __DIR__ . '/..' . '/swiftmailer/swiftmailer/lib/swift_required.php',
        '676bc97000efebef52aae80b42dfd272' => __DIR__ . '/../..' . '/api.php',
    );

    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'IUOE\\' => 5,
        ),
        'D' => 
        array (
            'Doctrine\\Common\\Cache\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'IUOE\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Doctrine\\Common\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/cache/lib/Doctrine/Common/Cache',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8540580aa0536b5238e3073069e5f077::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8540580aa0536b5238e3073069e5f077::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
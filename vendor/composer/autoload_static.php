<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit24b27b9ed30c462fd290575a2a3aeaba
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'Razorpay\\Tests\\' => 15,
            'Razorpay\\Api\\' => 13,
        ),
        'A' => 
        array (
            'Acme\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Razorpay\\Tests\\' => 
        array (
            0 => __DIR__ . '/..' . '/razorpay/razorpay/tests',
        ),
        'Razorpay\\Api\\' => 
        array (
            0 => __DIR__ . '/..' . '/razorpay/razorpay/src',
        ),
        'Acme\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'R' => 
        array (
            'Requests' => 
            array (
                0 => __DIR__ . '/..' . '/rmccue/requests/library',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit24b27b9ed30c462fd290575a2a3aeaba::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit24b27b9ed30c462fd290575a2a3aeaba::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit24b27b9ed30c462fd290575a2a3aeaba::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}

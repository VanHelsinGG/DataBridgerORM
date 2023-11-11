<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit5fe6c4e8874e0fe2dac62bb3394eaa2e
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit5fe6c4e8874e0fe2dac62bb3394eaa2e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit5fe6c4e8874e0fe2dac62bb3394eaa2e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit5fe6c4e8874e0fe2dac62bb3394eaa2e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

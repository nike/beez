<?php

require_once __DIR__.'/../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespace('Symfony', __DIR__.'/../vendor');
$loader->registerNamespace('DS', __DIR__.'/../src');
$loader->register();

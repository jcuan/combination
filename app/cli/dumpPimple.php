<?php

require "../../init.php";

$container=\Star\Core\App::getInstance()::$container;

$container->register(new \JBZoo\PimpleDumper\PimpleDumper());
$dumper = new \JBZoo\PimpleDumper\PimpleDumper();
$dumper->setRoot(BASE_PATH);
$dumper->dumpPimple($container);
$dumper->dumpPhpstorm($container);
<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
        ->load('App\\', '../src/')
        ->exclude('../src/Kernel.php');
}; 
<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'Wizardinstaller',
    ['path' => '/wizardinstaller'],
    function (RouteBuilder $routes) {
        $routes->extensions(['json',
                             'xml',
                             'ajax']);
        $routes->fallbacks(DashedRoute::class);
    }
);

Router::connect('/install', ['plugin'     => 'Wizardinstaller',
                             'controller' => 'Install',
                             'action'     => 'step',
                             1]);

Router::connect('/update', ['plugin'     => 'Wizardinstaller',
                             'controller' => 'Update',
                             'action'     => 'index',
                             1]);

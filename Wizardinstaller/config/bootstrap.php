<?php
use \Cake\Network\Request;
use \Cake\Network\Response;
use Cake\Routing\Router;

$configexist = file_exists(ROOT . DS . 'install.lock');

if (!$configexist) {
    $request = Request::createFromGlobals();
    if (strpos($request->url, 'install') === FALSE && strpos($request->url,
                                                                     'debug_kit') === FALSE && !$configexist && !$request->is('ajax')
    ) {
        $response = new Response();
        $response->statusCode(302);
        $response->location(Router::url(['controller' => 'install',
                                         'action'     => 'step',
                                         1,
                                         'plugin'     => 'wizardinstaller'], TRUE));
        $response->send();
    }
}
?>
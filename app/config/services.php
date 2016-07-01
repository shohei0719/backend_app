<?php

use Phalcon\Mvc\View;
use Phalcon\DI\FactoryDefault;
//use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
//use Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use Phalcon\Session\Adapter\Files as SessionAdapter;
//use Phalcon\Flash\Session as FlashSession;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Logger\Formatter\Line as LineFormatter;

//404エラー
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
	$url = new UrlProvider();

	$url->setBaseUri($config->application->baseUri);
	//$url->setBaseUri($config->application->adminUri);
	return $url;
});


$di->set('view', function () use ($config) {

	$view = new View();

	$view->setViewsDir(APP_PATH . $config->application->viewsDir);

	$view->registerEngines(array(
		".volt" => 'volt'
	));

	return $view;
});

/**
 * Setting up volt
 */
$di->set('volt', function ($view, $di) {

	$volt = new VoltEngine($view, $di);

	$volt->setOptions(array(
		"compiledPath" => APP_PATH . "cache/volt/"
	));

	$compiler = $volt->getCompiler();
	$compiler->addFunction('is_a', 'is_a');

	return $volt;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
	$config = $config->get('database')->toArray();

	$dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
	unset($config['adapter']);

	return new $dbClass($config);
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
	$session = new SessionAdapter();
	$session->start();
	return $session;
});

/**
 * Logging
 */
$di->set('logger', function () use ($config) {
	$file_path = '../tmp/logs/error_' . date('Ymd') . '.log';

	if(!file_exists($file_path)){
		touch($file_path);
	}

	$logger = new FileAdapter($file_path);
	$formatter = new LineFormatter();
	$logger->setFormatter($formatter);
	return $logger;
});

/**
 * Register a user component(headder menu)
 */
$di->set('elements', function () {
	return new Elements();
});

/**
 * Router
 */
$di->set('router', function () use ($config){
	//require $config->application->configDir . 'router.php';
	require 'router.php';
	return $router;
});

/*
 * カスタムタグヘルパー
 * view : {{MyTags.errorMsg($param)}} の形で利用可能
 */
$di->set('MyTags', function(){
	return new MyTags();
});

/*
 * 設定ファイルの内容を取得
 * model : $this->getDI()->get('config')->database->dbname の形で利用可能
 * controller : $this->config->database->dbname;
 * view : {{this.config.database.dbname }}
 */
$di->set('config', $config, true);

/**
 * error handling
 */
 //開発中邪魔になるので、一旦コメントアウト
 /*
$di->set('dispatcher', function() {

  //Create an EventsManager
  $eventsManager = new EventsManager();

  //Attach a listener
  $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {

    //Handle 404 exceptions
    if ($exception instanceof DispatchException) {
      $dispatcher->forward(array(
        'controller' => 'errors',
        'action' => 'show404'
      ));
      return false;
    }

    //Handle other exceptions
    $dispatcher->forward(array(
      'controller' => 'errors',
      'action' => 'show503'
    ));

    return false;
  });

  $dispatcher = new MvcDispatcher();

  //Bind the EventsManager to the dispatcher
  $dispatcher->setEventsManager($eventsManager);

  return $dispatcher;

}, true);
*/

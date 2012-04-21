<?php
/**
 * mu-webapp
 * 
 * LICENSE
 * 
 * The BSD 2-Clause License is applied on this source-file. For
 * further information please refer to
 * http://www.opensource.org/licenses/BSD-2-Clause or send an
 * email to andre.moelle@gmail.com.
 */
include '../classes/Transfer.php';
include '../classes/Template.php';
include '../classes/Dispatcher.php';
include '../classes/Application.php';
include '../application/code/MyApp.php';

header('X-Powered-By: mu-webapp 0.1.3-dev');
if(!isset($_SESSION)) session_start();
$self = dirname($_SERVER['PHP_SELF']);
$uri = $_SERVER['REQUEST_URI'];
if(($pos = strpos($uri, '?')) !== false) $uri = substr($uri, 0, $pos);
$uri = $self == '/' ? $uri : substr($uri, strlen($self));

$app = new MyApp('../application/', new Transfer());
$app->getTransfer()->setSession($_SESSION)->setCookies($_COOKIE)->setGet($_GET)->setPost($_POST)->setServer($_SERVER);
$transfer = $app->run($uri == '/' ? '/index' : $uri);
if(!$transfer->wasDispatched()) $transfer = $app->run('/error/404');
$_SESSION = $transfer->getSession();
?>

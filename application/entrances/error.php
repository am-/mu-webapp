<?php
switch ($transfer->argument(0))
{
	default:
	case '404':
		$tpl = $transfer->baseTpl->derive('error/404.phtml');
		$tpl->path = $transfer->server('REQUEST_URI');
		$transfer->layout->title = 'Error 404 - File not found';
		break;
}

$transfer->template = $tpl;
?>
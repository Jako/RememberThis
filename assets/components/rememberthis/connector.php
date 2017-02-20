<?php
/**
 * RememberThis Connector
 *
 * @package rememberthis
 * @subpackage connector
 *
 * @var modX $modx
 */
/* Allow anonymous users */
define('MODX_REQP', false);

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', array(
    'core_path' => $corePath
));

if ($modx->user->hasSessionContext($modx->context->get('key'))) {
    $_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
} else {
    $_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
    $_SERVER['HTTP_MODAUTH'] = 0;
}
$_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];

// Handle request
$modx->request->handleRequest(array(
    'processors_path' => $rememberthis->getOption('processorsPath'),
    'location' => 'web'
));
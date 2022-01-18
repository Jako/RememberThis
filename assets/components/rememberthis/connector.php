<?php
/**
 * RememberThis connector
 *
 * @package rememberthis
 * @subpackage connector
 *
 * @var modX $modx
 */

/* Allow anonymous users */
define('MODX_REQP', false);

require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', [
    'core_path' => $corePath
]);

$_REQUEST['action'] = 'web/' . $_REQUEST['action'];

// Set HTTP_MODAUTH for web processors
if (defined('MODX_REQP') && MODX_REQP === false) {
    $_SERVER['HTTP_MODAUTH'] = $modx->user->getUserToken($modx->context->get('key'));
}

// Handle request
$modx->request->handleRequest([
    'processors_path' => $rememberthis->getOption('processorsPath'),
    'location' => ''
]);

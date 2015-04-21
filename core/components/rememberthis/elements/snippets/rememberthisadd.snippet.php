<?php
/**
 * RememberThisAdd Snippet
 *
 * @package rememberthis
 * @subpackage snippet
 */
$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', $scriptProperties);

// Snippet settings
$options = array(
    'addTpl' => $modx->getOption('addTpl', $scriptProperties, $modx->getOption('rememberthis.addTpl', null, 'tplRememberThisAdd', true), true)
);

// Run options
$addId = $modx->getOption('addId', $scriptProperties, $modx->resource->get('id'));

return $rememberthis->outputAdd($addId, $options);
<?php
/**
 * RememberThisAdd Snippet
 *
 * @package rememberthis
 * @subpackage snippet
 */
$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/');

// Snippet settings
$options = array(
    'addTpl' => $modx->getOption('addTpl', $scriptProperties, $rememberthis->getOption('addTpl'), true)
);

// Run options
$addId = $modx->getOption('addId', $scriptProperties, $modx->resource->get('id'));

return $rememberthis->showButton($addId, $options);
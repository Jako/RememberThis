<?php
/**
 * RememberThisAdd Snippet
 *
 * @package rememberthis
 * @subpackage snippet
 *
 * @var modX $modx
 * @var array $scriptProperties
 */
$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/');

// Snippet settings
$options = array(
    'addTpl' => $modx->getOption('addTpl', $scriptProperties, $rememberthis->getOption('addTpl'), true),
    'tplPath' => $modx->getOption('tplPath', $scriptProperties, $rememberthis->getOption('tplPath'), true)
);

// Run options
$addId = $modx->getOption('addId', $scriptProperties, $modx->resource->get('id'), true);

return $rememberthis->showButton($addId, $options);
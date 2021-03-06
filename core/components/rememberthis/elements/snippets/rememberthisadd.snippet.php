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

$path = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $path . 'model/rememberthis/', array(
    'core_path' => $path
));

// Snippet settings
$options = array(
    'addTpl' => $modx->getOption('addTpl', $scriptProperties, $rememberthis->getOption('addTpl'), true),
    'tplPath' => $modx->getOption('tplPath', $scriptProperties, $rememberthis->getOption('tplPath'), true),
    'properties' => $scriptProperties
);

// Run options
$addId = $modx->getOption('addId', $scriptProperties, (isset($modx->resource)) ? $modx->resource->get('id') : 0, true);

return $rememberthis->showButton($addId, $options);

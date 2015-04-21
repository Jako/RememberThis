<?php
/**
 * RememberThis Hook
 *
 * @package rememberthis
 * @subpackage formit hook
 */
$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', $scriptProperties);

// Snippet settings
$options = array(
    'rowTpl' => $modx->getOption('rememberRowTpl', $scriptProperties, $modx->getOption('rememberthis.rowTpl', null, 'tplRememberThisRow', true), true),
    'outerTpl' => $modx->getOption('rememberOuterTpl', $scriptProperties, $modx->getOption('rememberthis.outerTpl', null, 'tplRememberThisOuter', true), true),
);

$output = $rememberthis->outputList($options);
$hook->setValue('rememberthis', $output);

return true;
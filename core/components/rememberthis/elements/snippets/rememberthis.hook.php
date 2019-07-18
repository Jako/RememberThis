<?php
/**
 * RememberThis Hook
 *
 * @package rememberthis
 * @subpackage formit hook
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var fiHooks $hook
 */

$path = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $path . 'model/rememberthis/', array(
    'core_path' => $path
));

// Snippet settings
$options = array(
    'rowTpl' => $modx->getOption('rememberRowTpl', $scriptProperties, $rememberthis->getOption('rowTpl'), true),
    'outerTpl' => $modx->getOption('rememberOuterTpl', $scriptProperties, $rememberthis->getOption('outerTpl'), true),
    'wrapperTpl' => $modx->getOption('rememberWrapperTpl', $scriptProperties, $rememberthis->getOption('wrapperTpl'), true),
    'noResultsTpl' => $modx->getOption('rememberNoResultsTpl', $scriptProperties, $rememberthis->getOption('noResultsTpl'), true),
    'tplPath' => $modx->getOption('tplPath', $scriptProperties, $rememberthis->getOption('tplPath'), true),
    'properties' => $hook->formit->config
);
$jsonList = (bool)$modx->getOption('jsonList', $scriptProperties, false, true);
$clearList = (bool)$modx->getOption('clearList', $scriptProperties, false, true);
$saveList = (bool)$modx->getOption('saveList', $scriptProperties, false, true);

$result = $rememberthis->showList($options);
if ($jsonList) {
    $hook->setValue('rememberthis', json_encode($result['list']));
} else {
    $hook->setValue('rememberthis', $result['result']);
}
$hook->setValue('rememberthis.list', $result['list']);
$hook->setValue('rememberthis.count', $result['count']);

if ($saveList) {
    $hook->setValue('rememberthis.hash', $rememberthis->saveList());
}

if ($clearList) {
    $rememberthis->clearList();
}

return true;

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
    'tplPath' => $modx->getOption('tplPath', $scriptProperties, $rememberthis->getOption('tplPath'), true)
);
$jsonList = $rememberthis->getBooleanOption('jsonList', $scriptProperties, false);
$clearList = $rememberthis->getBooleanOption('clearList', $scriptProperties, false);

$result = $rememberthis->showList($options);
if ($jsonList) {
    $hook->setValue('rememberthis', json_encode($result['list']));
} else {
    $hook->setValue('rememberthis', $result['result']);
}
$hook->setValue('rememberthis.list', $result['list']);
$hook->setValue('rememberthis.count', $result['count']);

if ($clearList) {
    $rememberthis->clearList();
}

return true;

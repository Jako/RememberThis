<?php
/**
 * RememberThis Hook
 *
 * @package rememberthis
 * @subpackage formit hook
 */
$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/');

// Snippet settings
$options = array(
    'rowTpl' => $modx->getOption('rememberRowTpl', $scriptProperties, $rememberthis->getOption('rowTpl'), true),
    'outerTpl' => $modx->getOption('rememberOuterTpl', $scriptProperties, $rememberthis->getOption('outerTpl'), true),
    'tplPath' => $modx->getOption('tplPath', $scriptProperties, $rememberthis->getOption('tplPath'), true)
);
$jsonList = intval($modx->getOption('jsonList', $scriptProperties, 0));
$clearList = intval($modx->getOption('clearList', $scriptProperties, 0));

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
<?php
/**
 * RememberThisList Snippet
 *
 * @package rememberthis
 * @subpackage snippet
 */
$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/');
$rememberthis->init();

// Snippet settings
$options = array(
    'rowTpl' => $modx->getOption('rowTpl', $scriptProperties, $rememberthis->getOption('rowTpl'), true),
    'outerTpl' => $modx->getOption('outerTpl', $scriptProperties, $rememberthis->getOption('outerTpl'), true),
    'noResultsTpl' => $modx->getOption('noResultsTpl', $scriptProperties, $rememberthis->getOption('noResultsTpl'), true),
    'tplPath' => $modx->getOption('tplPath', $scriptProperties, $rememberthis->getOption('tplPath'), true)
);
$jsonList = intval($modx->getOption('jsonList', $scriptProperties, 0));
$clearList = intval($modx->getOption('clearList', $scriptProperties, 0));

$result = $rememberthis->showList($options);
if ($jsonList) {
    $output = json_encode($result['list']);
} else {
    $output = $result['result'];
}
return $output;
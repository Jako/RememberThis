<?php
/**
 * RememberThisList Snippet
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
$init = $rememberthis->getOption('init', null, false);
$rememberthis->init();

// Snippet settings
$options = array(
    'rowTpl' => $modx->getOption('rowTpl', $scriptProperties, $rememberthis->getOption('rowTpl'), true),
    'outerTpl' => $modx->getOption('outerTpl', $scriptProperties, $rememberthis->getOption('outerTpl'), true),
    'wrapperTpl' => $modx->getOption('wrapperTpl', $scriptProperties, $rememberthis->getOption('wrapperTpl'), true),
    'noResultsTpl' => $modx->getOption('noResultsTpl', $scriptProperties, $rememberthis->getOption('noResultsTpl'), true),
    'tplPath' => $modx->getOption('tplPath', $scriptProperties, $rememberthis->getOption('tplPath'), true),
    'hash' => $modx->getOption('rememberthis', $_REQUEST, false, true),
    'properties' => $scriptProperties
);
$jsonList = (bool)$modx->getOption('jsonList', $scriptProperties, false, true);

$result = $rememberthis->showList($options);
if ($jsonList) {
    $output = json_encode($result['list']);
} else {
    $output = $result['result'];
    if ($rememberthis->getOption('debug') && !$init) {
        $output .= '<pre class="rememberdebug">' . $result['debug'] . '</pre>';
    }
}
return $output;

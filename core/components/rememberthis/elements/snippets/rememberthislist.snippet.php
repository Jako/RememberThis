<?php
/**
 * RememberThisList Snippet
 *
 * @package rememberthis
 * @subpackage snippet
 */
$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', $scriptProperties);
$rememberthis->init();

// Snippet settings
$options = array(
    'rowTpl' => $modx->getOption('rowTpl', $scriptProperties, $modx->getOption('rememberthis.rowTpl', null, 'tplRememberThisRow', true), true),
    'outerTpl' => $modx->getOption('outerTpl', $scriptProperties, $modx->getOption('rememberthis.outerTpl', null, 'tplRememberThisOuter', true), true),
    'item' => $modx->getOption('noResultsTpl', $scriptProperties, $modx->getOption('rememberthis.noResultsTpl', null, 'tplRememberThisNoResults', true), true),
    'noResultsTpl' => $modx->getOption('noResultsTpl', $scriptProperties, $modx->getOption('rememberthis.noResultsTpl', null, 'tplRememberThisNoResults', true), true),
    'scriptTpl' => $modx->getOption('scriptTpl', $scriptProperties, $modx->getOption('rememberthis.scriptTpl', null, 'tplRememberThisScript', true), true)
);

return $rememberthis->outputList($options);

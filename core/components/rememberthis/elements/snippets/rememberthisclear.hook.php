<?php
/**
 * RememberThis Clear Hook
 *
 * @package rememberthis
 * @subpackage formit hook
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var fiHooks $hook
 */

use TreehillStudio\RememberThis\Snippets\RememberThisClear;

$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', [
    'core_path' => $corePath
]);

$snippet = new RememberThisClear($modx, $hook, $scriptProperties);
if ($snippet instanceof TreehillStudio\RememberThis\Snippets\RememberThisClear) {
    return $snippet->execute();
}
return 'TreehillStudio\RememberThis\Snippets\RememberThisClear class not found';

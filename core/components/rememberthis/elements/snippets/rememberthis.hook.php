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

use TreehillStudio\RememberThis\Snippets\RememberThisHook;

$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', [
    'core_path' => $corePath
]);

$snippet = new RememberThisHook($modx, $hook, $scriptProperties);
if ($snippet instanceof TreehillStudio\RememberThis\Snippets\RememberThisHook) {
    return $snippet->execute();
}
return 'TreehillStudio\RememberThis\Snippets\RememberThisHook class not found';

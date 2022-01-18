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

use TreehillStudio\RememberThis\Snippets\RememberThisList;

$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', [
    'core_path' => $corePath
]);

$snippet = new RememberThisList($modx, $scriptProperties);
if ($snippet instanceof TreehillStudio\RememberThis\Snippets\RememberThisList) {
    return $snippet->execute();
}
return 'TreehillStudio\RememberThis\Snippets\RememberThisList class not found';
<?php
/**
 * Remember Processor
 *
 * @package rememberthis
 * @subpackage processors
 *
 * @var modX $modx
 */

$corePath = $modx->getOption('rememberthis.core_path', null, $modx->getOption('core_path') . 'components/rememberthis/');
/** @var RememberThis $rememberthis */
$rememberthis = $modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/', array(
    'core_path' => $corePath
));

// set processing options
$options = array(
    'language' => $modx->getOption('language', $_GET, 'en'),
    'add' => $modx->getOption('add', $_GET, 0),
    'delete' => intval($modx->getOption('delete', $_GET, 0)),
    'addproperties' => array()
);

foreach ($modx->getOption('addproperties', $_GET, array()) as $key => $value) {
    $propertylen = strlen($rememberthis->getOption(('queryAdd')) . 'property_');
    if (substr($key, 0, $propertylen) == $rememberthis->getOption(('queryAdd')) . 'property_') {
        $options['addproperties'][substr($key, $propertylen)] = $rememberthis->stripTags($value);
    }
}

$result = $rememberthis->ajaxResult($options);

// return result
return json_encode(array_merge($result, array(
    'success' => true,
)));

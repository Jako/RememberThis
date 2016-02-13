<?php
/**
 * Remember Processor
 *
 * @package rememberthis
 * @subpackage processor
 */

// set processing options
$options = array(
    'language' => $modx->getOption('language', $_GET, 'en'),
    'add' => $modx->getOption('add', $_GET, 0),
    'delete' => intval($modx->getOption('delete', $_GET, 0)),
    'addproperties' => array()
);

foreach ($modx->getOption('addproperties', $_GET, array()) as $key => $value) {
    $propertylen = strlen($modx->rememberthis->getOption(('queryAdd')) . 'property_');
    if (substr($key, 0, $propertylen) == $modx->rememberthis->getOption(('queryAdd')) . 'property_') {
        $options['addproperties'][substr($key, $propertylen)] = $modx->rememberthis->stripTags($value);
    }
}

$result = $modx->rememberthis->ajaxResult($options);

// return result
return json_encode(array_merge($result, array(
    'success' => true,
)));

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
    'delete' => intval($modx->getOption('delete', $_GET, 0))
);
$result = $modx->rememberthis->ajaxResult($options);

// return result
return json_encode(array_merge($result, array(
    'success' => true,
)));

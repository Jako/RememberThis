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
$result = $modx->rememberthis->outputAjax($options);

// return result
return json_encode(array(
    'success' => true,
    'result' => $result
));

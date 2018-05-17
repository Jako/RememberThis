<?php
/**
 * @package rememberthis
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/rememberthislist.class.php');
class RememberThisList_mysql extends RememberThisList {}
?>
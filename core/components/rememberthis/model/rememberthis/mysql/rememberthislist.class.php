<?php
/**
 * @package rememberthis
 */
require_once (strtr(realpath(dirname(__FILE__, 2)), '\\', '/') . '/rememberthislist.class.php');
class RememberThisList_mysql extends RememberThisList {}
?>
<?php
/**
 * RememberThis
 *
 * Copyright 2011-2013 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * RememberThis is free software; you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the Free 
 * Software Foundation; either version 2 of the License, or (at your option) any 
 * later version.
 *
 * RememberThis is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * RememberThis; if not, write to the Free Software Foundation, Inc., 
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package rememberthis
 * @subpackage connector
 */
/* Allow anonymous users */
define('MODX_REQP', false);

/* Require MODx configuration, and base connector file */
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$rememberthisCorePath = $modx->getOption('rememberthis.core_path', null, MODX_CORE_PATH . 'components/rememberthis/');

$version = $modx->getVersionData();
if (version_compare($version['full_version'], '2.1.1-pl') >= 0) {
	if ($modx->user->hasSessionContext($modx->context->get('key'))) {
		$_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
	} else {
		$_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
		$_SERVER['HTTP_MODAUTH'] = 0;
	}
} else {
	$_SERVER['HTTP_MODAUTH'] = $modx->site_id;
}
$_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];

require_once $rememberthisCorePath . 'elements/model/rememberthis/rememberthis.class.php';

$options = array();

// System settings
$options['rowTpl'] = $modx->getOption('rememberthis.rowTpl', NULL, '@FILE components/rememberthis/templates/rowTpl.html');
$options['outerTpl'] = $modx->getOption('rememberthis.outerTpl', NULL, '@FILE components/rememberthis/templates/outerTpl.html');
$options['addTpl'] = $modx->getOption('rememberthis.addTpl', NULL, '@FILE components/rememberthis/templates/addTpl.html');
$options['noResultsTpl'] = $modx->getOption('rememberthis.noResultsTpl', NULL, '@FILE components/rememberthis/templates/noResultsTpl.html');
$options['itemTitleTpl'] = $modx->getOption('rememberthis.itemTitleTpl', NULL, '@FILE components/rememberthis/templates/itemTitleTpl.html');
$options['tvPrefix'] = $modx->getOption('rememberthis.tvPrefix', NULL, 'tv.');
$options['language'] = $modx->getOption('rememberthis.language', NULL, 'en');
$options['packagename'] = $modx->getOption('rememberthis.packagename', NULL, '');
$options['classname'] = $modx->getOption('rememberthis.classname', NULL, '');
$options['joins'] = $modx->fromJson($modx->getOption('rememberthis.joins', NULL, ''));
$options['jQueryPath'] = $modx->getOption('rememberthis.jQueryPath', NULL, '');
$options['includeScripts'] = intval($modx->getOption('rememberthis.includeScripts', NULL, 1));
$options['includeCss'] = intval($modx->getOption('rememberthis.includeCss', NULL, 1));
$options['debug'] = intval($modx->getOption('rememberthis.debug', NULL, 0));

$modx->rememberDoc = new RememberThis($modx, $options);

/* Handle request */
$processorsPath = $modx->getOption('core_path') . 'components/rememberthis/processors/';
$modx->request->handleRequest(array(
	'processors_path' => $processorsPath,
	'location' => 'web'
));
?>

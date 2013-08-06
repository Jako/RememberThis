<?php
/**
 * RememberThis
 *
 * Copyright 2008-2013 by Thomas Jakobi <thomas.jakobi@partout.info>
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
 * @subpackage formit hook
 */
$rememberThisCorePath = $modx->getOption('rememberthis.core_path', NULL, $modx->getOption('core_path') . 'components/rememberthis/');
$scriptProperties['rememberThisCorePath'] = $rememberThisCorePath;

// Snippet settings
$options = array();
$options['rowTpl'] = $modx->getOption('rowTpl', $scriptProperties, $modx->getOption('rememberthis.rowTpl', NULL, '@FILE components/rememberthis/templates/rowTpl.html'));
$options['outerTpl'] = $modx->getOption('outerTpl', $scriptProperties, $modx->getOption('rememberthis.outerTpl', NULL, '@FILE components/rememberthis/templates/outerTpl.html'));
$options['addTpl'] = $modx->getOption('addTpl', $scriptProperties, $modx->getOption('rememberthis.addTpl', NULL, '@FILE components/rememberthis/templates/addTpl.html'));
$options['noResultsTpl'] = $modx->getOption('noResultsTpl', $scriptProperties, $modx->getOption('rememberthis.noResultsTpl', NULL, '@FILE components/rememberthis/templates/noResultsTpl.html'));

// Snippet settings
$mode = $modx->getOption('mode', $scriptProperties, 'display');
$addId = $modx->getOption('addId', $scriptProperties, $modx->resource->get('id'));

$modx->getService('rememberthis', 'rememberThis', $rememberThisCorePath . 'model/rememberthis/', $scriptProperties);

return $modx->rememberthis->Run($mode, $addId, $options);

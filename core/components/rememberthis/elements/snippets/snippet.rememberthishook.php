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
$scriptProperties['rowTpl'] = $modx->getOption('rememberRowTpl', $scriptProperties, $modx->getOption('rememberthis.rowTpl', NULL, '@FILE components/rememberthis/templates/rowTpl.html'));
$scriptProperties['outerTpl'] = $modx->getOption('rememberOuterTpl', $scriptProperties, $modx->getOption('rememberthis.outerTpl', NULL, '@FILE components/rememberthis/templates/outerTpl.html'));

$modx->getService('rememberthis', 'rememberThis', $rememberThisCorePath . 'model/rememberthis/', $scriptProperties);

$output = $modx->rememberthis->Run('hook', $modx->resource->get('id'), $scriptProperties);
$hook->setValue('rememberthis', $output);

return true;
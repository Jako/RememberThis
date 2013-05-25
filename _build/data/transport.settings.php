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
 * @subpackage build
 * 
 * RememberThis system settings build script
 */
$settings = array();

// Template settings
$settings['rememberthis.rowTpl'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.rowTpl']->fromArray(array(
	'key' => 'rememberthis.rowTpl',
	'value' => '@FILE components/rememberthis/templates/rowTpl.html',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'templates',
		), '', true, true);

$settings['rememberthis.outerTpl'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.outerTpl']->fromArray(array(
	'key' => 'rememberthis.outerTpl',
	'value' => '@FILE components/rememberthis/templates/outerTpl.html',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'templates',
		), '', true, true);

$settings['rememberthis.addTpl'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.addTpl']->fromArray(array(
	'key' => 'rememberthis.addTpl',
	'value' => '@FILE components/rememberthis/templates/addTpl.html',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'templates',
		), '', true, true);

$settings['rememberthis.noResultsTpl'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.noResultsTpl']->fromArray(array(
	'key' => 'rememberthis.noResultsTpl',
	'value' => '@FILE components/rememberthis/templates/noResultsTpl.html',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'templates',
		), '', true, true);

$settings['rememberthis.itemTitleTpl'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.itemTitleTpl']->fromArray(array(
	'key' => 'rememberthis.itemTitleTpl',
	'value' => '@FILE components/rememberthis/templates/itemTitleTpl.html',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'templates',
		), '', true, true);

$settings['rememberthis.tvPrefix'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.tvPrefix']->fromArray(array(
	'key' => 'rememberthis.tvPrefix',
	'value' => 'tv.',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'templates',
		), '', true, true);

$settings['rememberthis.language'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.language']->fromArray(array(
	'key' => 'rememberthis.language',
	'value' => 'en',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'templates',
		), '', true, true);

// Package settings
$settings['rememberthis.packagename'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.packagename']->fromArray(array(
	'key' => 'rememberthis.packagename',
	'value' => '',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'package',
		), '', true, true);

$settings['rememberthis.classname'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.classname']->fromArray(array(
	'key' => 'rememberthis.classname',
	'value' => '',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'package',
		), '', true, true);

$settings['rememberthis.joins'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.joins']->fromArray(array(
	'key' => 'rememberthis.joins',
	'value' => '',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'package',
		), '', true, true);

// System settings
$settings['rememberthis.jQueryPath'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.jQueryPath']->fromArray(array(
	'key' => 'rememberthis.jQueryPath',
	'value' => '',
	'xtype' => 'textfield',
	'namespace' => 'rememberthis',
	'area' => 'system',
		), '', true, true);

$settings['rememberthis.includeScripts'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.includeScripts']->fromArray(array(
	'key' => 'rememberthis.includeScripts',
	'value' => 1,
	'xtype' => 'combo-boolean',
	'namespace' => 'rememberthis',
	'area' => 'system',
		), '', true, true);

$settings['rememberthis.includeCss'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.includeCss']->fromArray(array(
	'key' => 'rememberthis.includeCss',
	'value' => 1,
	'xtype' => 'combo-boolean',
	'namespace' => 'rememberthis',
	'area' => 'system',
		), '', true, true);

$settings['rememberthis.debug'] = $modx->newObject('modSystemSetting');
$settings['rememberthis.debug']->fromArray(array(
	'key' => 'rememberthis.debug',
	'value' => 0,
	'xtype' => 'combo-boolean',
	'namespace' => 'rememberthis',
	'area' => 'system',
		), '', true, true);

return $settings;

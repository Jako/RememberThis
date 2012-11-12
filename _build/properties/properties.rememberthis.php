<?php
/**
 * RememberThis
 *
 * Copyright 2011-2012 by Thomas Jakobi <thomas.jakobi@partout.info>
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
 * Properties for the RememberThis plugin.
 */
$properties = array(
	array(
		'name' => 'mode',
		'desc' => 'prop_rememberthis.mode',
		'type' => 'list',
		'options' => array(
			array("text" => "prop_rememberthis.add", "value" => "add"),
			array("text" => "prop_rememberthis.display", "value" => "display")
		),
		'value' => 'display',
		'lexicon' => 'rememberthis:properties',
	),
	array(
		'name' => 'addId',
		'desc' => 'prop_rememberthis.addId',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'rememberthis:properties',
	)
);

return $properties;
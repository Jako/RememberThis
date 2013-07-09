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
 * RememberThis snippets build script
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'RememberThis',
    'description' => 'Remember an id and the correspondenting resource data in a session based list.',
    'snippet' => getSnippetContent($sources['snippets'].'snippet.rememberthis.php'),
),'',true,true);
$properties = include $sources['properties'].'properties.rememberthis.php';
$snippets[0]->setProperties($properties);
unset($properties);

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 0,
    'name' => 'RememberThisHook',
    'description' => 'FormIt Hook to set a hook value with the remembered data of RememberThis snippet.',
    'snippet' => getSnippetContent($sources['snippets'].'snippet.rememberthishook.php'),
),'',true,true);
$properties = include $sources['properties'].'properties.rememberthishook.php';
$snippets[1]->setProperties($properties);
unset($properties);

return $snippets;
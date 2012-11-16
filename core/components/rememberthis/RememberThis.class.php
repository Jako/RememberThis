<?php
/**
 * RememberThis
 *
 * Copyright 2008-2012 by Thomas Jakobi <thomas.jakobi@partout.info>
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
 * @subpackage classfile
 * 
 * @author      Thomas Jakobi (thomas.jakobi@partout.info)
 * @copyright   Copyright 2008-2012, Thomas Jakobi
 * @version     0.3
 */
require_once (MODX_CORE_PATH . '/components/rememberthis/includes/chunkie.class.inc.php');

class RememberThis {

	// Template chunks
	private $templates;
	// Package to retreive remember data from
	private $package;
	// Settings
	private $settings;
	// language
	private $language;

	function RememberThis($options) {
		global $modx;

		$parser = new rtChunkie();

		$this->templates['rowTpl'] = $options['rowTpl'];
		$this->templates['outerTpl'] = $options['outerTpl'];
		$this->templates['addTpl'] = $options['addTpl'];
		$this->templates['itemTitleTpl'] = $options['itemTitleTpl'];
		$this->templates['tvPrefix'] = $options['tvPrefix'];
		$this->templates['language'] = $options['language'];

		$this->package['packagename'] = $options['packagename'];
		$this->package['classname'] = $options['classname'];
		$this->package['joins'] = $options['joins'];

		$this->settings['jQueryPath'] = $options['jQueryPath'];
		$this->settings['includeScripts'] = $options['includeScripts'];
		$this->settings['includeCss'] = $options['includeCss'];
		$this->settings['mode'] = $options['mode'];
		$this->settings['debug'] = $options['debug'];

		$modx->setOption('cultureKey', $options['language']);
		$modx->getService('lexicon', 'modLexicon');
		$modx->lexicon->load('rememberthis:default');
		$this->language['add'] = $modx->lexicon('rememberthis.add');
		$this->language['delete'] = $modx->lexicon('rememberthis.delete');
		$this->language['noresultstext'] = $modx->lexicon('rememberthis.noresultstext');

		$parser = $parser = new rtChunkie($options['noResultsTpl']);
		$parser->CreateVars($this->language, 'lang');
		$this->templates['noResultsTpl'] = $parser->Render();

		$this->gets = array();
		foreach ($_GET as $key => $value) {
			if ($key != 'q' && $key != 'add' && $key != 'delete') {
				$this->gets[$key] = htmlspecialchars($value);
			}
		}
	}

	function Run($mode, $addId) {
		global $modx;

		switch ($mode) {
			case 'add' : {
					$parser = new rtChunkie($this->templates['addTpl']);
					$parser->CreateVars($this->language, 'lang');
					$parser->AddVar('rememberurl', $modx->makeUrl($modx->resource->get('id'), '', array_merge($this->gets, array('add' => $addId))));
					$parser->AddVar('id', $addId);
					$output = $parser->Render();
					break;
				}
			case 'ajax' : {
					if (isset($_GET['add'])) {
						$index = $this->Add(intval($_GET['add']));
						if ($index !== FALSE) {
							$fields = array_merge($_SESSION['rememberThis'][$index]['element'], array('deleteurl' => $modx->makeUrl($modx->getOption('site_start'), '', array('delete' => $index + 1)), 'index' => $index + 1));
							$parser = new rtChunkie($this->templates['rowTpl']);
							$parser->CreateVars($this->language, 'lang');
							$parser->CreateVars($fields);
							$output = $parser->Render();
							if ($this->settings['debug']) {
								$output .= '<pre>DEBUG: $_SESSION[\'rememberThis\'] = ' . print_r($_SESSION['rememberThis'], TRUE) . '</pre>';
							}
							break;
						} else {
							if ($this->settings['debug']) {
								$output = '<pre>DEBUG: $_SESSION[\'rememberThis\'] = ' . print_r($_SESSION['rememberThis'], TRUE) . '</pre>';
							}
							break;
						}
					}
					if (isset($_GET['delete'])) {
						$this->Delete(intval($_GET['delete']));
						if (isset($_SESSION['rememberThis'])) {
							$output = (intval($_GET['delete']) - 1);
						} else {
							$output = $this->templates['noResultsTpl'];
						}
						if ($this->settings['debug']) {
							$output .= '<pre>DEBUG: $_SESSION[\'rememberThis\'] = ' . print_r($_SESSION['rememberThis'], TRUE) . '</pre>';
						}
						break;
					}
					if (!isset($_SESSION['rememberThis']) || !is_array($_SESSION['rememberThis']) || count($_SESSION['rememberThis']) == 0) {
						$output = $this->templates['noResultsTpl'];
						break;
					}
					$output = '';
					break;
				}
			case 'display' :
			default : {
					if ($this->settings['includeScripts']) {
						if ($this->settings['jQueryPath'] != '') {
							$modx->regClientScript($this->settings['jQueryPath']);
						}
						$modx->regClientScript('assets/components/rememberthis/RememberThis.js');
					}
					if ($this->settings['includeCss']) {
						$modx->regClientCSS('assets/components/rememberthis/RememberThis.css');
					}
					if (isset($_GET['add'])) {
						$this->Add(intval($_GET['add']));
					}
					if (isset($_GET['delete'])) {
						$this->Delete(intval($_GET['delete']));
					}

					$parser = new rtChunkie($this->templates['outerTpl']);
					if (!isset($_SESSION['rememberThis']) || !is_array($_SESSION['rememberThis']) || count($_SESSION['rememberThis']) == 0) {
						$parser->AddVar('wrapper', $this->templates['noResultsTpl']);
					} else {
						$parser->AddVar('wrapper', $this->Output($this->templates['rowTpl']));
					}
					$output = $parser->Render();
					if ($this->settings['debug']) {
						$output .= '<pre>DEBUG: $_SESSION[\'rememberThis\'] = ' . print_r($_SESSION['rememberThis'], TRUE) . '</pre>';
					}
				}
		}
		return $output;
	}

	function Add($docId) {
		global $modx;
		if (!isset($_SESSION['rememberThis'])) {
			$_SESSION['rememberThis'] = array();
		}
		$found = 0;
		$newElement = array();
		$newElement['rememberId'] = $docId;

		if ($this->package['packagename'] == '') {
			// no packagename -> resource
			$resource = $modx->getObject('modResource', array('id' => $docId));

			// $modx->resource not set during ajax call
			if (!$modx->resource) {
				$modx->resource = &$resource;
			}

			$tvs = array();
			$templateVars = &$resource->getMany('TemplateVars');
			foreach ($templateVars as $templateVar) {
				$tvs[$this->templates['tvPrefix'] . $templateVar->get('name')] = $templateVar->renderOutput($resource->get('id'));
			}
			$row = array_merge($resource->toArray(), $tvs);

			$parser = new rtChunkie($this->templates['itemTitleTpl']);
			$parser->CreateVars($this->language, 'lang');
			$parser->CreateVars($row);

			$newElement = $row;
			$newElement['itemtitle'] = $parser->Render();
		} else {
			$packagepath = $modx->getOption('core_path') . 'components/' . $this->package['packagename'] . '/';
			$modelpath = $packagepath . 'model/';

			$modx->addPackage($this->package['packagename'], $modelpath);
			$resource = $modx->getObject($this->package['classname'], array('id' => $docId));

			// $modx->resource not set during ajax call
			if (!$modx->resource) {
				$modx->resource = $modx->newObject('modResource');
			}
			$joinvalues = array();
			foreach ($this->package['joins'] as $join) {
				$values = $resource->getOne($join);
				$joinvalues[$join] = $values->toArray();
			}
			$row = array_merge($joinvalues, $resource->toArray());

			$parser = new rtChunkie($this->templates['itemTitleTpl']);
			$parser->CreateVars($row);
			$newElement = $row;
			$newElement['itemtitle'] = $parser->Render();
		}
		foreach ($_SESSION['rememberThis'] as & $element) {
			if (!count(array_diff_assoc($element['element'], $newElement))) {
				$element['count'] += 1;
				$found = 1;
			}
		}
		if (!$found) {
			$_SESSION['rememberThis'][] = array('element' => $newElement, 'count' => 1);
			return (key($_SESSION['rememberThis']));
		} else {
			return FALSE;
		}
	}

	function Delete($index) {
		$index--;
		if (isset($_SESSION['rememberThis'])) {
			if (isset($_SESSION['rememberThis'][$index])) {
				unset($_SESSION['rememberThis'][$index]);
			}
			if (count($_SESSION['rememberThis']) == 0) {
				unset($_SESSION['rememberThis']);
			}
		}
	}

	function Output($tpl) {
		global $modx;

		$output = array();
		foreach ($_SESSION['rememberThis'] as $nummer => $liste) {
			if ($tpl != '') {
				$fields = array_merge($liste['element'], array('deleteurl' => $modx->makeUrl($modx->resource->get('id'), '', array_merge($this->gets, array('delete' => $nummer + 1))), 'index' => $nummer + 1, 'count' => $liste['count']));
				$parser = new rtChunkie($tpl);
				$parser->CreateVars($this->language, 'lang');
				$parser->CreateVars($fields);
				$output[] = $parser->Render();
			} else {
				$output[] = '<pre>' . print_r($liste, true) . '</pre>';
			}
		}
		$output = implode('', $output);
		return $output;
	}

}

?>
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
 * @subpackage classfile
 * 
 * @author      Thomas Jakobi (thomas.jakobi@partout.info)
 * @copyright   Copyright 2008-2013, Thomas Jakobi
 * @version     0.6
 */
require_once (MODX_CORE_PATH . 'components/rememberthis/elements/model/chunkie/chunkie.class.inc.php');

class RememberThis {

	// MODX object
	private $modx;
	// Template chunks
	private $templates;
	// Package to retreive remember data from
	private $package;
	// Settings
	private $settings;
	// language
	private $language;
	// gets
	private $gets;

	function RememberThis($modx, $options) {
		$this->modx = &$modx;

		$this->templates['rememberthisTpl'] = '@FILE components/rememberthis/templates/rememberthisTpl.html';
		$this->templates['noResultsTpl'] = $options['noResultsTpl'];
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

		$this->settings['scriptOptions'][] = 'ajaxLoaderImg: "' . $options['ajaxLoaderImg'] . '"';

		$this->modx->setOption('cultureKey', $options['language']);
		$this->modx->getService('lexicon', 'modLexicon');
		$this->modx->lexicon->load('rememberthis:default');
		$this->language['add'] = $this->modx->lexicon('rememberthis.add');
		$this->language['delete'] = $this->modx->lexicon('rememberthis.delete');
		$this->language['noresultstext'] = $this->modx->lexicon('rememberthis.noresultstext');

		if (!isset($_SESSION['rememberThis'])) {
			$_SESSION['rememberThis'] = array();
		}
		$this->gets = array();
		foreach ($_GET as $key => $value) {
			if ($key != 'q' && $key != 'add' && $key != 'delete') {
				$this->gets[$key] = htmlspecialchars($value);
			}
		}
	}

	function Run($mode, $addId, $options) {
		switch ($mode) {
			case 'add' : {
					$parser = new revoChunkie($options['addTpl'], array('useCorePath' => true));
					$parser->createVars($this->language, 'lang');
					$parser->addVar('rememberurl', $this->modx->makeUrl($this->modx->resource->get('id'), '', array_merge($this->gets, array('add' => $addId))));
					$parser->addVar('id', $addId);
					$output = $parser->render();
					break;
				}
			case 'ajax' : {
					if (isset($_GET['add'])) {
						$index = $this->Add(intval($_GET['add']));
						if ($index !== FALSE) {
							$fields = array_merge($_SESSION['rememberThis'][$index]['element'], array('deleteurl' => $this->modx->makeUrl($this->modx->getOption('site_start'), '', array('delete' => $index + 1)), 'index' => $index + 1));
							$parser = new revoChunkie($this->templates['rowTpl'], array('useCorePath' => true));
							$parser->createVars($this->language, 'lang');
							$parser->createVars($fields);
							if (count($_SESSION['rememberThis']) == 1) {
								$wrapper = $parser->render();
								$parser = new revoChunkie($this->templates['outerTpl'], array('useCorePath' => true));
								$parser->createVars($this->language, 'lang');
								$parser->addVar('wrapper', $wrapper);
								$output = $parser->render();
							}
							$output = $parser->render();
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
						if (count($_SESSION['rememberThis']) > 0) {
							$output = (string) intval($_GET['delete']);
						} else {
							$parser = new revoChunkie($this->templates['noResultsTpl'], array('useCorePath' => true));
							$parser->createVars($this->language, 'lang');
							$output = $parser->render();
						}
						if ($this->settings['debug']) {
							$output .= '<pre>DEBUG: $_SESSION["rememberThis"] = ' . print_r($_SESSION['rememberThis'], TRUE) . '</pre>';
						}
						break;
					}
					if (count($_SESSION['rememberThis']) == 0) {
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
							$this->modx->regClientScript($this->settings['jQueryPath']);
						}
						$this->modx->regClientScript('assets/components/rememberthis/RememberThis.js');
						$parser = new revoChunkie('@FILE components/rememberthis/templates/scriptTpl.html', array('useCorePath' => true));
						$parser->addVar('options', implode(", ", $this->settings['scriptOptions']));
						$this->modx->regClientScript($parser->render());
					}
					if ($this->settings['includeCss']) {
						$this->modx->regClientCSS('assets/components/rememberthis/RememberThis.css');
					}
					if (isset($_GET['add'])) {
						$this->Add(intval($_GET['add']));
					}
					if (isset($_GET['delete'])) {
						$this->Delete(intval($_GET['delete']));
					}

					if (count($_SESSION['rememberThis']) == 0) {
						$parser = new revoChunkie($options['noResultsTpl'], array('useCorePath' => true));
						$parser->createVars($this->language, 'lang');
					} else {
						$parser = new revoChunkie($options['outerTpl'], array('useCorePath' => true));
						$parser->createVars($this->language, 'lang');
						$parser->addVar('wrapper', $this->Output($options['rowTpl']));
					}
					$wrapper = $parser->render();

					$parser = new revoChunkie($this->templates['rememberthisTpl'], array('useCorePath' => true));
					$parser->createVars($this->language, 'lang');
					$parser->addVar('wrapper', $wrapper);
					$output = $parser->render();

					if ($this->settings['debug']) {
						$output .= '<pre>DEBUG: $_SESSION["rememberThis"] = ' . print_r($_SESSION['rememberThis'], TRUE) . '</pre>';
					}
				}
		}
		return $output;
	}

	function Add($docId) {
		$found = 0;
		$newElement = array();
		$newElement['rememberId'] = $docId;

		if ($this->package['packagename'] == '') {
			// no packagename -> resource
			$resource = $this->modx->getObject('modResource', array('id' => $docId));
			$tvs = array();
			$templateVars = &$resource->getMany('TemplateVars');
			foreach ($templateVars as $templateVar) {
				$tvs[$this->templates['tvPrefix'] . $templateVar->get('name')] = $templateVar->renderOutput($resource->get('id'));
			}
			$row = array_merge($resource->toArray(), $tvs);
		} else {
			$packagepath = $this->modx->getOption('core_path') . 'components/' . $this->package['packagename'] . '/';
			$modelpath = $packagepath . 'model/';

			$this->modx->addPackage($this->package['packagename'], $modelpath);
			$resource = $this->modx->getObject($this->package['classname'], array('id' => $docId));
			$joinvalues = array();
			foreach ($this->package['joins'] as $join) {
				$values = $resource->getOne($join);
				$joinvalues[$join] = $values->toArray();
			}
			$row = array_merge($joinvalues, $resource->toArray());
		}
		$parser = new revoChunkie($this->templates['itemTitleTpl'], array('useCorePath' => true));
		$parser->createVars($this->language, 'lang');
		$parser->createVars($row);

		$newElement = $row;
		$newElement['itemtitle'] = $parser->render();
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
		}
	}

	function Output($tpl) {
		$output = array();
		$iteration = 0;
		foreach ($_SESSION['rememberThis'] as $nummer => $liste) {
			if ($tpl != '') {
				$fields = array_merge($liste['element'], array(
					'deleteurl' => $this->modx->makeUrl($this->modx->resource->get('id'), '', array_merge($this->gets, array('delete' => $nummer + 1))),
					'index' => $nummer + 1,
					'iteration' => $iteration,
					'count' => $liste['count']
				));
				$parser = new revoChunkie($tpl, array('useCorePath' => true));
				$parser->createVars($this->language, 'lang');
				$parser->createVars($fields);
				$output[] = $parser->render();
				$iteration++;
			} else {
				$output[] = '<pre>' . print_r($liste, true) . '</pre>';
			}
		}
		$output = implode('', $output);
		return $output;
	}

}

?>
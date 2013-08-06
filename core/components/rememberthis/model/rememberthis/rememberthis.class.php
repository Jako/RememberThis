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
 */
class rememberThis {

	// MODX object
	private $modx;
	// Chunkie object
	private $chunkie;
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

	public function __construct(modX &$modx, array $config = array()) {
		$this->modx = &$modx;
		if (!$this->modx->loadClass('modxchunkie', $config['rememberThisCorePath'] . 'model/modxchunkie/', true, true)) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, 'RememberThis: modxChunkie class not loaded.');
		}
		$this->chunkie = new modxChunkie($this->modx, array('useCorePath' => TRUE));

		$this->templates['rememberthisTpl'] = '@FILE components/rememberthis/templates/rememberthisTpl.html';
		$this->templates['noResultsTpl'] = $this->modx->getOption('noResultsTpl', $scriptProperties, $this->modx->getOption('rememberthis.noResultsTpl', NULL, '@FILE components/rememberthis/templates/noResultsTpl.html'));
		$this->templates['rowTpl'] = $this->modx->getOption('rowTpl', $scriptProperties, $this->modx->getOption('rememberthis.rowTpl', NULL, '@FILE components/rememberthis/templates/rowTpl.html'));
		$this->templates['outerTpl'] = $this->modx->getOption('outerTpl', $scriptProperties, $this->modx->getOption('rememberthis.outerTpl', NULL, '@FILE components/rememberthis/templates/outerTpl.html'));
		$this->templates['addTpl'] = $this->modx->getOption('addTpl', $scriptProperties, $this->modx->getOption('rememberthis.addTpl', NULL, '@FILE components/rememberthis/templates/addTpl.html'));
		$this->templates['itemTitleTpl'] = $this->modx->getOption('itemTitleTpl', $config, $this->modx->getOption('rememberthis.itemTitleTpl', NULL, '@FILE components/rememberthis/templates/itemTitleTpl.html'));

		$this->package['packagename'] = $this->modx->getOption('rememberthis.packagename', NULL, '');
		$this->package['classname'] = $this->modx->getOption('rememberthis.classname', NULL, '');
		$this->package['joins'] = $this->modx->fromJson($this->modx->getOption('rememberthis.joins', NULL, ''));

		$this->settings['jQueryPath'] = $this->modx->getOption('rememberthis.jQueryPath', NULL, '');
		$this->settings['includeScripts'] = intval($this->modx->getOption('rememberthis.includeScripts', NULL, 1));
		$this->settings['includeCss'] = intval($this->modx->getOption('rememberthis.includeCss', NULL, 1));
		$this->settings['mode'] = $this->modx->getOption('mode', $scriptProperties, 'display');
		$this->settings['debug'] = intval($this->modx->getOption('rememberthis.debug', NULL, 0));
		$this->settings['tvPrefix'] = $this->modx->getOption('tvPrefix', $config, $this->modx->getOption('rememberthis.tvPrefix', NULL, 'tv.'));
		$this->settings['ajaxLoaderImg'] = $this->modx->getOption('rememberthis.ajaxLoaderImg', NULL, 'assets/components/rememberthis/ajax-loader.gif');
		$this->settings['language'] = $this->modx->getOption('language', $config, $this->modx->getOption('rememberthis.language', NULL, 'en'));
		$this->settings['notRememberRedirect'] = intval($modx->getOption('notRememberRedirect', $config, FALSE, TRUE));

		$this->settings['scriptOptions'][] = 'ajaxLoaderImg: "' . $this->settings['ajaxLoaderImg'] . '"';

		$this->modx->setOption('cultureKey', $this->settings['language']);
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
					$this->chunkie->setTemplate($this->chunkie->getTemplate($options['addTpl']));
					$this->chunkie->createVars($this->language, 'lang');
					$this->chunkie->addVar('rememberurl', $this->modx->makeUrl($this->modx->resource->get('id'), '', array_merge($this->gets, array('add' => $addId))));
					$this->chunkie->addVar('id', $addId);
					$output = $this->chunkie->render();
					break;
				}
			case 'ajax' : {
					if (isset($_GET['add'])) {
						$index = $this->Add(intval($_GET['add']));
						if ($index !== FALSE) {
							$fields = array_merge($_SESSION['rememberThis'][$index]['element'], array('deleteurl' => $this->modx->makeUrl($this->modx->getOption('site_start'), '', array('delete' => $index + 1)), 'index' => $index + 1));
							$this->chunkie->setTemplate($this->chunkie->getTemplate($this->templates['rowTpl']));
							$this->chunkie->createVars($this->language, 'lang');
							$this->chunkie->createVars($fields);
							if (count($_SESSION['rememberThis']) == 1) {
								$wrapper = $this->chunkie->render();
								$this->chunkie->setTemplate($this->chunkie->getTemplate($this->templates['outerTpl']));
								$this->chunkie->createVars($this->language, 'lang');
								$this->chunkie->addVar('wrapper', $wrapper);
							}
							$output = $this->chunkie->render();
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
							$this->chunkie->setTemplate($this->chunkie->getTemplate($this->templates['noResultsTpl']));
							$this->chunkie->createVars($this->language, 'lang');
							$output = $this->chunkie->render();
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
						$this->chunkie->setTemplate($this->chunkie->getTemplate('@FILE components/rememberthis/templates/scriptTpl.html'));
						$this->chunkie->addVar('options', implode(", ", $this->settings['scriptOptions']));
						$this->modx->regClientScript($this->chunkie->render());
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
				}
			case 'hook' : {
					if (count($_SESSION['rememberThis']) == 0) {
						if ($this->settings['notRememberRedirect'] === FALSE) {
							$this->chunkie->setTemplate($this->chunkie->getTemplate($options['noResultsTpl']));
							$this->chunkie->createVars($this->language, 'lang');
						} else {
							$url = $this->modx->makeUrl($this->settings['notRememberRedirect']);
							$this->modx->sendRedirect($url);
						}
					} else {
						$wrapper = $this->Output($options['rowTpl']);
						$this->chunkie->setTemplate($this->chunkie->getTemplate($options['outerTpl']));
						$this->chunkie->createVars($this->language, 'lang');
						$this->chunkie->addVar('wrapper', $wrapper);
					}
					$wrapper = $this->chunkie->render();

					$this->chunkie->setTemplate($this->chunkie->getTemplate($this->templates['rememberthisTpl']));
					$this->chunkie->createVars($this->language, 'lang');
					$this->chunkie->addVar('wrapper', $wrapper);
					$output = $this->chunkie->render();


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
				$tvs[$this->settings['tvPrefix'] . $templateVar->get('name')] = $templateVar->renderOutput($resource->get('id'));
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
		$this->chunkie->setTemplate($this->chunkie->getTemplate($this->templates['itemTitleTpl']));
		$this->chunkie->createVars($this->language, 'lang');
		$this->chunkie->createVars($row);

		$newElement = $row;
		$newElement['itemtitle'] = $this->chunkie->render();
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
		foreach ($_SESSION['rememberThis'] as $key => $element) {
			if ($tpl != '') {
				$fields = array_merge($element['element'], array(
					'deleteurl' => $this->modx->makeUrl($this->modx->resource->get('id'), '', array_merge($this->gets, array('delete' => $key + 1))),
					'index' => $key + 1,
					'iteration' => $iteration,
					'count' => $element['count']
				));

				$this->chunkie->setTemplate($this->chunkie->getTemplate($tpl));
				$this->chunkie->createVars($this->language, 'lang');
				$this->chunkie->createVars($fields);
				$output[] = $this->chunkie->render();
				$iteration++;
			} else {
				$output[] = '<pre>' . print_r($element, true) . '</pre>';
			}
		}
		$output = implode('', $output);
		return $output;
	}

}

?>
<?php
/**
 * RememberThis
 *
 * Copyright 2008-2024 by Thomas Jakobi <office@treehillstudio.com>
 *
 * @package rememberthis
 * @subpackage classfile
 */

namespace TreehillStudio\RememberThis;

use modTemplateVar;
use modX;
use RememberThisList;
use TreehillStudio\RememberThis\Helper\Parse;
use xPDO;
use xPDOObject;

/**
 * class RememberThis
 */
class RememberThis
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'rememberthis';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'RememberThis';

    /**
     * The version
     * @var string $version
     */
    public $version = '2.4.0';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * @var Parse $parse
     */
    public $parse = null;

    /**
     * RememberThis constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');
        $modxversion = $this->modx->getVersionData();

        // Load some default paths for easier management
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'cachePath' => $assetsPath . 'cache/',
            'cacheUrl' => $assetsUrl . 'cache/'
        ], $options);

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');

        $this->packageName = $this->modx->lexicon('rememberthis');

        $this->modx->addPackage($this->namespace, $this->getOption('modelPath'));

        // Add default options
        $this->options = array_merge($this->options, [
            'debug' => $this->getBooleanOption('debug', $options, false),
            'modxversion' => $modxversion['version'],
            'wrapperTpl' => $this->getOption('wrapperTpl', $options, 'tplRememberThisWrapper'),
            'noResultsTpl' => $this->getOption('noResultsTpl', $options, 'tplRememberThisNoResults'),
            'scriptTpl' => $this->getOption('scriptTpl', $options, 'tplRememberThisScript'),
            'showZeroCount' => $this->getBooleanOption('showZeroCount', $options, true),
            'rowTpl' => $this->getOption('rowTpl', $options, 'tplRememberThisRow'),
            'outerTpl' => $this->getOption('outerTpl', $options, 'tplRememberThisOuter'),
            'addTpl' => $this->getOption('addTpl', $options, 'tplRememberThisAdd'),
            'itemTitleTpl' => $this->getOption('itemTitleTpl', $options, 'tplRememberThisItemTitle'),
            'packagename' => $this->getOption('packagename', null, ''),
            'classname' => $this->getOption('classname', null, ''),
            'keyname' => $this->getOption('keyname', null, 'id'),
            'joins' => $this->getJsonOption('joins', null, ''),
            'fields' => $this->getSeparatedOption('fields', null, ''),
            'jQueryPath' => $this->getOption('jQueryPath', null, ''),
            'includeScripts' => $this->getBooleanOption('includeScripts', null, true),
            'includeCss' => $this->getBooleanOption('includeCss', null, true),
            'tvPrefix' => $this->getOption('tvPrefix', $options, 'tv.'),
            'ajaxLoaderImg' => $this->getOption('ajaxLoaderImg', null, ''),
            'language' => $this->modx->getOption('language', $options, $this->modx->getOption('cultureKey')),
            'languages' => ['de', 'en'],
            'notRememberRedirect' => (int)$this->modx->getOption('notRememberRedirect', $options, false),
            'argSeparator' => ($this->modx->getOption('xhtml_urls')) ? '&amp;' : '&',
            'queryAdd' => $this->getOption('queryAdd', $options, 'add'),
            'queryDelete' => $this->getOption('queryDelete', $options, 'delete'),
            'useCookie' => $this->getBooleanOption('useCookie', null, false),
            'cookieName' => $this->getOption('cookieName', $options, 'rememberlist'),
            'cookieExpireDays' => (int)$this->getOption('cookieExpireDays', $options, 90),
            'useDatabase' => $this->getBooleanOption('useDatabase', null, false),
            'init' => false
        ]);

        // Custom options
        $this->options['language'] = in_array($this->getOption('language'), $this->getOption('languages')) ? $this->getOption('language') : 'en';
        $this->options['tplPath'] = $this->getOption('tplPath', $options);

        // Set script options
        $this->options['scriptOptions'] = [
            'connectorUrl' => $this->getOption('connectorUrl'),
            'language' => $this->getOption('language')
        ];
        if ($this->getOption('ajaxLoaderImg')) {
            $this->$options['scriptOptions'] = array_merge($this->getOption('scriptOptions'), [
                'ajaxLoaderImg' => $this->getOption('ajaxLoaderImg')
            ]);
        }

        $this->parse = new Parse($this->modx);

        if (!isset($_SESSION['rememberThis'])) {
            $_SESSION['rememberThis'] = [];
        }
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }
        return $option;
    }

    /**
     * Get Boolean Option
     *
     * @param string $key
     * @param array $options
     * @param mixed $default
     * @return bool
     */
    public function getBooleanOption($key, $options = [], $default = null)
    {
        $option = $this->getOption($key, $options, $default);
        return ($option === 'true' || $option === true || $option === '1' || $option === 1);
    }

    /**
     * Get JSON Option
     *
     * @param string $key
     * @param array $options
     * @param mixed $default
     * @return array
     */
    public function getJsonOption($key, $options = [], $default = null)
    {
        $value = json_decode($this->getOption($key, $options, $default ?? ''), true);
        return (is_array($value)) ? $value : [];
    }

    /**
     * Get Separated Option
     *
     * @param string $key
     * @param array $options
     * @param mixed $default
     * @return array
     */
    public function getSeparatedOption($key, $options = [], $default = null)
    {
        $value = $this->getOption($key, $options, $default ?? '');
        return ($value) ? array_map('trim', explode(',', $value)) : [];
    }

    /**
     * Init scripts and add/delete only once
     */
    public function init()
    {
        if (!$this->getOption('init', null, false)) {
            $version = (!$this->modx->getObject('modPlugin', [
                'name' => 'minifyRegistered',
                'disabled' => 0
            ])) ? '?v=' . $this->getOption('version') : '';
            if ($this->getOption('includeScripts')) {
                if ($this->getOption('jQueryPath') != '') {
                    $this->modx->regClientScript($this->getOption('jQueryPath'));
                }
                if (!$this->getOption('debug')) {
                    $this->modx->regClientScript($this->getOption('assetsUrl') . 'js/rememberthis.min.js' . $version);
                } else {
                    $this->modx->regClientScript($this->getOption('assetsUrl') . 'js/rememberthis.js' . $version);
                }
                $script = $this->parse->getChunk($this->getOption('scriptTpl'), array_merge($this->options, [
                    'options' => json_encode($this->getOption('scriptOptions'), JSON_UNESCAPED_SLASHES)
                ]));
                $this->modx->regClientScript($script);
            }
            if ($this->getOption('includeCss')) {
                $this->modx->regClientCSS($this->getOption('assetsUrl') . 'css/rememberthis.css' . $version);
            }

            if ($this->getOption('useCookie')) {
                $this->getCookie();
            }
            if ($this->getOption('useDatabase') && $this->modx->user->id) {
                $this->getDBRecord();
            }

            // Add/remove items to/from the list
            $queryAdd = $this->getOption('queryAdd');
            if (isset($_REQUEST[$queryAdd])) {
                $addProperties = [];
                foreach ($_REQUEST as $key => $value) {
                    $propertylen = strlen($queryAdd . 'property_');
                    if (substr($key, 0, $propertylen) == $queryAdd . 'property_') {
                        $addProperties[substr($key, $propertylen)] = $this->stripTags($value);
                    }
                }
                $this->add($_REQUEST[$queryAdd], $addProperties);
            }
            if (isset($_REQUEST[$this->getOption('queryDelete')])) {
                $this->delete(intval($_REQUEST[$this->getOption('queryDelete')]));
            }

            $this->options['init'] = true;
        }
    }

    /**
     * Generate an url
     *
     * @param array $parameter Request parameters.
     * @return string
     */
    public function makeUrl($parameter)
    {
        $requestParameter = $this->modx->request->getParameters();
        $identifier = $this->modx->request->getResourceIdentifier('alias');

        if (isset($requestParameter[$this->getOption('queryAdd')])) {
            unset($requestParameter[$this->getOption('queryAdd')]);
        }
        if (isset($requestParameter[$this->getOption('queryDelete')])) {
            unset($requestParameter[$this->getOption('queryDelete')]);
        }
        foreach ($requestParameter as $key => $value) {
            if (strpos($key, $this->getOption('queryAdd') . 'property_') === 0) {
                unset($requestParameter[$key]);
            }
        }

        return $identifier . '?' . http_build_query(array_merge($requestParameter, $parameter), '', $this->getOption('argSeparator'));
    }

    /**
     * Output the add button
     *
     * @param string $addId Key to add.
     * @param array $options Template options.
     * @return bool|string
     */
    public function showButton($addId, $options)
    {
        return $this->parse->getChunk($options['addTpl'], array_merge($options, [
            'rememberurl' => $this->makeUrl([$this->getOption('queryAdd') => $addId]),
            'rememberidentifier' => $this->modx->request->getResourceIdentifier('alias'),
            'rememberqueryadd' => $this->getOption('queryAdd'),
            'rememberid' => $addId
        ]));
    }

    /**
     * Show the AJAX result
     *
     * @param array $options Template options
     * @return array
     */
    public function ajaxResult($options)
    {
        $options['language'] = in_array($options['language'], $this->getOption('languages')) ? $options['language'] : 'en';
        $this->modx->setOption('cultureKey', $options['language']);

        $output = [];
        if ($options['add']) {
            $index = $this->add($options['add'], $options['addproperties']);
            if ($index !== false) {
                $fields = array_merge($_SESSION['rememberThis'][$index]['element'], [
                    'deleteurl' => $this->modx->makeUrl($this->modx->getOption('site_start'), '', [$this->getOption('queryDelete') => $index + 1]),
                    'deleteid' => $index + 1,
                    'iteration' => count($_SESSION['rememberThis']) - 1
                ]);
                $output['result'] = $this->parse->getChunk($this->getOption('rowTpl'), array_merge($this->options, $fields));
                $output['count'] = count($_SESSION['rememberThis']);
                if ($output['count'] == 1) {
                    $output['result'] = $this->parse->getChunk($this->getOption('outerTpl'), array_merge($this->options, [
                        'count' => $output['count'],
                        'wrapper' => $output['result']
                    ]));
                }
            }
            if ($this->getOption('debug')) {
                $output['debug'] = 'DEBUG: $_SESSION[\'rememberThis\'] = ' . print_r($_SESSION['rememberThis'], true);
            }
        } elseif ($options['delete']) {
            $this->delete($options['delete']);
            if (count($_SESSION['rememberThis']) > 0) {
                $output['result'] = '';
                $output['count'] = count($_SESSION['rememberThis']);
            } else {
                $output['result'] = $this->parse->getChunk($this->getOption('noResultsTpl'), $this->options);
                $output['count'] = $this->getOption('showZeroCount') ? '0' : '';
            }
            if ($this->getOption('debug')) {
                $output['debug'] = 'DEBUG: $_SESSION["rememberThis"] = ' . print_r($_SESSION['rememberThis'], true);
            }
        }
        return $output;
    }

    /**
     * Show the remembered list
     *
     * @param array $options Template options
     * @return array
     */
    public function showList($options)
    {
        $output = [];

        // Generate the list
        $list = [];
        if (!$options['hash']) {
            $list = $_SESSION['rememberThis'];
        } else {
            $listObject = $this->modx->getObject('RememberThisList', [
                'hash' => $options['hash']
            ]);
            if ($listObject) {
                $list = $listObject->get('list');
                $_SESSION['rememberThis'] = $list;
            }
        }
        foreach ($list as $element) {
            if (isset($element['element']['itemproperties']) && !empty($element['element']['itemproperties'])) {
                $output['list'][] = [
                    'identifier' => $element['element']['identifier'],
                    'itemproperties' => $element['element']['itemproperties']
                ];
            } else {
                $output['list'][] = $element['element']['identifier'];
            }
        }

        // Generate the result
        if (!count($list)) {
            if (!$this->getOption('notRememberRedirect')) {
                $output['result'] = $this->parse->getChunk($options['wrapperTpl'], array_merge($options, [
                    'wrapper' => $this->parse->getChunk($options['noResultsTpl']),
                    'count' => $this->getOption('showZeroCount') ? '0' : ''
                ]));
            } else {
                $this->modx->sendRedirect($this->modx->makeUrl($this->getOption('notRememberRedirect')));
            }
        } else {
            $outer = $this->parse->getChunk($options['outerTpl'], array_merge($options, [
                'wrapper' => $this->showElements($list, $options['rowTpl']),
                'count' => (string)count($_SESSION['rememberThis'])
            ]));
            $output['result'] = $this->parse->getChunk($options['wrapperTpl'], array_merge($options, [
                'wrapper' => $outer,
                'count' => (string)count($_SESSION['rememberThis'])
            ]));
        }

        // Generate count
        $output['count'] = count($_SESSION['rememberThis']);

        // Generate debug information
        if ($this->getOption('debug')) {
            $output['debug'] = 'DEBUG: $_SESSION["rememberThis"] = ' . print_r($_SESSION['rememberThis'], true);
        }
        return $output;
    }

    /**
     * Show the remembered list elements
     *
     * @param array $list List
     * @param string $tpl Template
     * @return string
     */
    public function showElements($list, $tpl)
    {
        $output = [];
        $iteration = 0;

        foreach ($list as $key => $element) {
            if ($tpl != '') {
                $fields = array_merge($element['element'], [
                    'deleteurl' => $this->makeUrl([$this->getOption('queryDelete') => $key + 1]),
                    'deleteidentifier' => $this->modx->request->getResourceIdentifier('alias'),
                    'deleteid' => $key + 1,
                    'rowid' => $key + 1,
                    'iteration' => $iteration
                ]);
                // Fill the itemcount placeholder with the count_xxx field value
                if (count($_POST)) {
                    $itemcount = (isset($_POST['count_' . ($key + 1)]) && intval($_POST['count_' . ($key + 1)])) ? intval($_POST['count_' . ($key + 1)]) : 1;
                    $fields = array_merge($fields, [
                        'itemcount' => $itemcount
                    ]);
                }

                $output[] = $this->parse->getChunk($tpl, array_merge($this->options, $fields));
                $iteration++;
            } else {
                $output[] = '<pre>' . print_r($element, true) . '</pre>';
            }
        }
        return implode('', $output);
    }

    /**
     * Add an element to the list
     *
     * @param integer $docId
     * @param array $properties
     * @return bool|int
     */
    private function add($docId, $properties = [])
    {
        $found = 0;
        if (!$this->getOption('packagename')) {
            // no packagename -> resource
            $resource = $this->modx->getObject('modResource', ['id' => $docId]);
            $tvs = [];
            /** @var modTemplateVar[] $templateVars */
            $templateVars = $resource->getMany('TemplateVars');
            foreach ($templateVars as $templateVar) {
                $tvs[$this->getOption('tvPrefix') . $templateVar->get('name')] = $templateVar->renderOutput($resource->get('id'));
            }
            $row = array_merge([
                'id' => $resource->get('id'),
                'pagetitle' => $resource->get('pagetitle'),
                'longtitle' => $resource->get('longtitle')
            ], $tvs);
        } else {
            $packagepath = $this->modx->getOption('core_path') . 'components/' . $this->getOption('packagename') . '/';
            $modelpath = $packagepath . 'model/';

            $this->modx->addPackage($this->getOption('packagename'), $modelpath);
            /** @var xPDOObject $resource */
            $c = $this->modx->newQuery($this->getOption('classname'));
            $c->select($this->modx->getSelectColumns($this->getOption('classname'), $this->getOption('classname')));
            $joins = $this->getOption('joins');
            $joinOnes = [];
            if ($joins) {
                foreach ($joins as $join) {
                    if (is_array($join) && isset($join['class'])) {
                        $class = $join['class'];
                        $alias = $join['alias'] ?? $class;
                        $prefix = $join['prefix'] ?? $alias . '.';
                        $conditions = $join['conditions'] ?? [];
                        $c->leftJoin($class, $alias, $conditions ?? []);
                        $c->select($this->modx->getSelectColumns($class, $alias, $prefix));
                    } elseif (is_string($join)) {
                        $joinOnes[] = $join;
                    } else {
                        $this->modx->log(xPDO::LOG_LEVEL_INFO, 'The join option does not use a class value: "' . print_r($join, true), '', 'RememberThis');
                    }
                }
            }
            $c->where([$this->getOption('keyname') => $docId]);

            $resource = $this->modx->getObject($this->getOption('classname'), $c);
            if ($resource) {
                if (method_exists($resource, 'toExtendedArray')) {
                    $row = $resource->toExtendedArray();
                } else {
                    $joinvalues = [];
                    if ($joinOnes) {
                        foreach ($joinOnes as $join) {
                            $values = $resource->getOne($join);
                            $joinvalues[$join] = $values->toArray();
                        }
                    }
                    $row = array_merge($joinvalues, $resource->toArray());
                }
                $row = Parse::flattenArray($row);
            } else {
                $this->modx->log(xPDO::LOG_LEVEL_INFO, 'Could not retreive an object with key "' . $this->getOption('keyname') . '" and value "' . $docId . '"', '', 'RememberThis');
                return false;
            }
        }

        if ($this->getOption('fields')) {
            $newElement = [];
            foreach ($this->getOption('fields') as $field) {
                $newElement[$field] = $row[$field];
            }
        } else {
            $newElement = $row;
        }
        $newElement['rememberId'] = $docId;
        $newElement['identifier'] = $row[$this->getOption('keyname')];
        $newElement['properties'] = ($properties) ? http_build_query($properties) : '';
        $newElement['itemtitle'] = $this->parse->getChunk($this->getOption('itemTitleTpl'), array_merge($this->options, $row, $properties));
        if (!empty($properties)) {
            $newElement['itemproperties'] = $properties;
        }

        if ($this->modx->user->hasSessionContext('mgr') && false) {
            echo('<pre>$newElement: ' . print_r($newElement, true) . '</pre>');
            die('<pre>$resource->toExtendedArray(): ' . print_r($resource->toExtendedArray(), true) . '</pre>');
        }

        foreach ($_SESSION['rememberThis'] as $element) {
            if (!count(array_diff_assoc($element['element'], $newElement))) {
                $found = 1;
            }
        }
        if (!$found) {
            $_SESSION['rememberThis'][] = ['element' => $newElement];
            if ($this->getOption('useCookie')) {
                $this->setCookie();
            }
            if ($this->getOption('useDatabase') && $this->modx->user->id) {
                $this->setDBRecord();
            }
            end($_SESSION['rememberThis']);
            return (key($_SESSION['rememberThis']));
        } else {
            return false;
        }
    }

    /**
     * Remove an element from the list
     *
     * @param integer $index
     */
    private function delete($index)
    {
        $index--;
        if (isset($_SESSION['rememberThis'])) {
            if (isset($_SESSION['rememberThis'][$index])) {
                unset($_SESSION['rememberThis'][$index]);
            }
        }
        if ($this->getOption('useCookie')) {
            $this->setCookie();
        }
        if ($this->getOption('useDatabase') && $this->modx->user->id) {
            $this->setDBRecord();
        }
    }

    /**
     * Save the remembered list with a hash as a database record
     */
    public function saveList()
    {
        $hash = md5(json_encode($_SESSION['rememberThis']) . time() . $this->modx->site_id);

        /** @var RememberThisList $list */
        $list = $this->modx->newObject('RememberThisList');
        $list->fromArray([
            'user_id' => $this->modx->user->id,
            'createdon' => time(),
            'hash' => $hash,
            'list' => $_SESSION['rememberThis']
        ]);
        $list->save();

        return $hash;
    }

    /**
     * Remove all elements from the list
     */
    public function clearList()
    {
        if (isset($_SESSION['rememberThis'])) {
            $_SESSION['rememberThis'] = [];
        }
        if ($this->getOption('useCookie')) {
            $this->setCookie();
        }
        if ($this->getOption('useDatabase') && $this->modx->user->id) {
            $this->setDBRecord();
        }
    }

    /**
     * Get the remembered list from cookie
     */
    private function getCookie()
    {
        $cookieName = $this->getOption('cookieName');
        if (isset($_COOKIE[$cookieName])) {
            $_SESSION['rememberThis'] = json_decode($_COOKIE[$cookieName], true);
        }
    }

    /**
     * Set the cookie from the remembered list
     */
    private function setCookie()
    {
        $cookieName = $this->getOption('cookieName');
        if (!empty($_SESSION['rememberThis'])) {
            setcookie($cookieName, json_encode($_SESSION['rememberThis']), strtotime('+' . $this->getOption('cookieExpireDays') . ' DAY'), '/');
            $_COOKIE[$cookieName] = json_encode($_SESSION['rememberThis']);
        } else {
            setcookie($cookieName, '', time() - 3600, '/');
            unset($_COOKIE[$cookieName]);
        }
    }

    /**
     * Get the remembered list from database record
     */
    private function getDBRecord()
    {
        /** @var RememberThisList $list */
        if ($list = $this->modx->getObject('RememberThisList', [
            'user_id' => $this->modx->user->id,
            'hash:IS' => null
        ])) {
            if ($list->get('createdon') && strtotime($list->get('createdon')) <= strtotime('-30 day')) {
                $list->remove();
            } else {
                $_SESSION['rememberThis'] = $list->get('list');
            }
        }
    }

    /**
     * Save a database record from the remembered list
     */
    private function setDBRecord()
    {
        /** @var RememberThisList $list */
        if (!$list = $this->modx->getObject('RememberThisList', [
            'user_id' => $this->modx->user->id,
            'hash:IS' => null
        ])) {
            $list = $this->modx->newObject('RememberThisList');
            $list->fromArray([
                'user_id' => $this->modx->user->id,
                'createdon' => time()
            ]);
        }
        $list->set('list', $_SESSION['rememberThis']);
        $list->save();
    }

    /**
     * Strip HTML & MODX tags
     * @param string $string
     * @return string
     */
    function stripTags($string)
    {
        $string = strip_tags($string);
        return preg_replace('/\[\[([^\[\]]++|(?R))*?]]/s', '', $string);
    }
}

<?php

/**
 * RememberThis
 *
 * Copyright 2008-2018 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package rememberthis
 * @subpackage classfile
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
     * The version
     * @var string $namespace
     */
    public $version = '2.2.0';

    /**
     * The class options
     * @var array $options
     */
    public $options = array();

    /**
     * Template cache
     * @var array $_tplCache
     */
    private $_tplCache;

    /**
     * Valid binding types
     * @var array $_validTypes
     */
    private $_validTypes = array(
        '@CHUNK',
        '@FILE',
        '@INLINE'
    );

    /**
     * RememberThis constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx = &$modx;

        $this->modx->lexicon->load('rememberthis:default');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/rememberthis/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/rememberthis/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/rememberthis/');

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'version' => $this->version,
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'cachePath' => $this->modx->getOption('core_path') . 'cache/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ), $options);

        // Load parameters
        $this->options = array_merge($this->options, array(
            'wrapperTpl' => $this->getOption('wrapperTpl', $options, 'tplRememberThisWrapper'),
            'noResultsTpl' => $this->getOption('noResultsTpl', $options, 'tplRememberThisNoResults'),
            'scriptTpl' => $this->getOption('scriptTpl', $options, 'tplRememberThisScript'),
            'showZeroCount' => intval($this->getOption('showZeroCount', $options, 1)),
            'rowTpl' => $this->getOption('rowTpl', $options, 'tplRememberThisRow'),
            'outerTpl' => $this->getOption('outerTpl', $options, 'tplRememberThisOuter'),
            'addTpl' => $this->getOption('addTpl', $options, 'tplRememberThisAdd'),
            'itemTitleTpl' => $this->getOption('itemTitleTpl', $options, 'tplRememberThisItemTitle'),
            'packagename' => $this->getOption('packagename', null, ''),
            'classname' => $this->getOption('classname', null, ''),
            'keyname' => $this->getOption('keyname', null, 'id'),
            'joins' => $this->modx->fromJson($this->getOption('joins', null, '')),
            'jQueryPath' => $this->getOption('jQueryPath', null, ''),
            'includeScripts' => intval($this->getOption('includeScripts', null, 1)),
            'includeCss' => intval($this->getOption('includeCss', null, 1)),
            'debug' => intval($this->getOption('debug', null, 0)),
            'tvPrefix' => $this->getOption('tvPrefix', $options, 'tv.'),
            'ajaxLoaderImg' => $this->getOption('ajaxLoaderImg', null, ''),
            'language' => $this->modx->getOption('language', $options, $this->modx->getOption('cultureKey')),
            'languages' => array('de', 'en'),
            'notRememberRedirect' => intval($this->modx->getOption('notRememberRedirect', $options, false)),
            'argSeparator' => ($this->modx->getOption('xhtml_urls')) ? '&amp;' : '&',
            'queryAdd' => $this->getOption('queryAdd', $options, 'add'),
            'queryDelete' => $this->getOption('queryDelete', $options, 'delete'),
            'useCookie' => intval($this->getOption('useCookie', null, 0)),
            'cookieName' => $this->getOption('cookieName', $options, 'rememberlist'),
            'cookieExpireDays' => intval($this->getOption('cookieExpireDays', $options, 90)),
            'useDatabase' => intval($this->getOption('useDatabase', null, 0)),
            'init' => false
        ));

        // Custom options
        $this->setOption('language', in_array($this->getOption('language'), $this->getOption('languages')) ? $this->getOption('language') : 'en');
        $this->setOption('tplPath', $this->getOption('tplPath', $options));

        // Set script options
        $this->setOption('scriptOptions', array(
            'connectorUrl' => $this->getOption('connectorUrl'),
            'language' => $this->getOption('language')
        ));
        if ($this->getOption('ajaxLoaderImg')) {
            $this->setOption('scriptOptions', array_merge($this->getOption('scriptOptions'), array(
                'ajaxLoaderImg' => $this->getOption('ajaxLoaderImg')
            )));
        }
        if (!isset($_SESSION['rememberThis'])) {
            $_SESSION['rememberThis'] = array();
        }

        $this->modx->addPackage('rememberthis', $this->getOption('modelPath'));
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
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * @param string $key The option key to set,
     * @param mixed $value The value to set.
     */
    public function setOption($key, $value)
    {
        if ($value) {
            $this->options[$key] = $value;
        }
    }

    /**
     * Init scripts and add/delete only once
     */
    public function init()
    {
        if (!$this->getOption('init', null, false)) {
            $version = (!$this->modx->getObject('modPlugin', array(
                'name' => 'minifyRegistered',
                'disabled' => 0
            ))) ? '?v=' . $this->getOption('version') : '';
            if ($this->getOption('includeScripts')) {
                if ($this->getOption('jQueryPath') != '') {
                    $this->modx->regClientScript($this->getOption('jQueryPath'));
                }
                if (!$this->getOption('debug')) {
                    $this->modx->regClientScript($this->getOption('assetsUrl') . 'js/rememberthis.min.js' . $version);
                } else {
                    $this->modx->regClientScript($this->getOption('assetsUrl') . 'js/rememberthis.js' . $version);
                }
                $script = $this->getChunk($this->getOption('scriptTpl'), array_merge($this->options, array(
                    'options' => json_encode($this->getOption('scriptOptions'))
                )));
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
                $addProperties = array();
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

            $this->setOption('init', true);
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

        return $identifier . '?' . http_build_query(array_merge($requestParameter, $parameter), '', $this->getOption('argSeparator'));
    }

    /**
     * Output the add button
     *
     * @param string $addId Key to add.
     * @param array $options Template options.
     * @return string
     */
    public function showButton($addId, $options)
    {
        $output = $this->getChunk($options['addTpl'], array_merge($options, array(
            'rememberurl' => $this->makeUrl(array($this->getOption('queryAdd') => $addId)),
            'rememberidentifier' => $this->modx->request->getResourceIdentifier('alias'),
            'rememberqueryadd' => $this->getOption('queryAdd'),
            'rememberid' => $addId
        )));
        return $output;
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

        $output = array();
        if ($options['add']) {
            $index = $this->add($options['add'], $options['addproperties']);
            if ($index !== false) {
                $fields = array_merge($_SESSION['rememberThis'][$index]['element'], array(
                    'deleteurl' => $this->modx->makeUrl($this->modx->getOption('site_start'), '', array($this->getOption('queryDelete') => $index + 1)),
                    'deleteid' => $index + 1,
                    'iteration' => count($_SESSION['rememberThis']) - 1
                ));
                $output['result'] = $this->getChunk($this->getOption('rowTpl'), array_merge($this->options, $fields));
                $output['count'] = count($_SESSION['rememberThis']);
                if ($output['count'] == 1) {
                    $output['result'] = $this->getChunk($this->getOption('outerTpl'), array_merge($this->options, array(
                        'count' => $output['count'],
                        'wrapper' => $output['result']
                    )));
                }
            }
            if ($this->getOption('debug')) {
                $output['debug'] = 'DEBUG: $_SESSION[\'rememberThis\'] = ' . print_r($_SESSION['rememberThis'], true);
            }
        } else {
            if ($options['delete']) {
                $this->delete($options['delete']);
                if (count($_SESSION['rememberThis']) > 0) {
                    $output['result'] = '';
                    $output['count'] = count($_SESSION['rememberThis']);
                } else {
                    $output['result'] = $this->getChunk($this->getOption('noResultsTpl'), $this->options);
                    $output['count'] = $this->getOption('showZeroCount') ? '0' : '';
                }
                if ($this->getOption('debug')) {
                    $output['debug'] = 'DEBUG: $_SESSION["rememberThis"] = ' . print_r($_SESSION['rememberThis'], true);
                }
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
        $output = array();

        // Generate the list
        $list = array();
        if (!$options['hash']) {
            $list = $_SESSION['rememberThis'];
        } else {
            $listObject = $this->modx->getObject('RememberThisList', array(
                'hash' => $options['hash']
            ));
            if ($listObject) {
                $list = $listObject->get('list');
            }
        }
        foreach ($list as $element) {
            if (isset($element['element']['itemproperties']) && !empty($element['element']['itemproperties'])) {
                $output['list'][] = array(
                    'identifier' => $element['element']['identifier'],
                    'itemproperties' => $element['element']['itemproperties']
                );
            } else {
                $output['list'][] = $element['element']['identifier'];
            }
        }

        // Generate the result
        if (!count($list)) {
            if (!$this->getOption('notRememberRedirect')) {
                $output['result'] = $this->getChunk($options['wrapperTpl'], array_merge($options, array(
                    'wrapper' => $this->getChunk($options['noResultsTpl']),
                    'count' => $this->getOption('showZeroCount') ? '0' : ''
                )));
            } else {
                $this->modx->sendRedirect($this->modx->makeUrl($this->getOption('notRememberRedirect')));
            }
        } else {
            $outer = $this->getChunk($options['outerTpl'], array_merge($options, array(
                'wrapper' => $this->showElements($list, $options['rowTpl']),
                'count' => (string)count($_SESSION['rememberThis'])
            )));
            $output['result'] = $this->getChunk($options['wrapperTpl'], array_merge($options, array(
                'wrapper' => $outer,
                'count' => (string)count($_SESSION['rememberThis'])
            )));
        }

        // Generate count
        $output['count'] = count($_SESSION['rememberThis']);

        // Generate debug informations
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
        $output = array();
        $iteration = 0;

        foreach ($list as $key => $element) {
            if ($tpl != '') {
                $fields = array_merge($element['element'], array(
                    'deleteurl' => $this->makeUrl(array($this->getOption('queryDelete') => $key + 1)),
                    'deleteidentifier' => $this->modx->request->getResourceIdentifier('alias'),
                    'deleteid' => $key + 1,
                    'rowid' => $key + 1,
                    'iteration' => $iteration
                ));
                // Fill the itemcount placeholder with the count_xxx field value
                if (count($_POST)) {
                    $itemcount = (isset($_POST['count_' . ($key + 1)]) && intval($_POST['count_' . ($key + 1)])) ? intval($_POST['count_' . ($key + 1)]) : 1;
                    $fields = array_merge($fields, array(
                        'itemcount' => $itemcount
                    ));
                }

                $output[] = $this->getChunk($tpl, array_merge($this->options, $fields));
                $iteration++;
            } else {
                $output[] = '<pre>' . print_r($element, true) . '</pre>';
            }
        }
        $output = implode('', $output);
        return $output;
    }

    /**
     * Add an element to the list
     *
     * @param integer $docId
     * @param array $properties
     * @return bool|int
     */
    private function add($docId, $properties = array())
    {
        $found = 0;
        if (!$this->getOption('packagename')) {
            // no packagename -> resource
            $resource = $this->modx->getObject('modResource', array('id' => $docId));
            $tvs = array();
            /** @var modTemplateVar[] $templateVars */
            $templateVars = &$resource->getMany('TemplateVars');
            foreach ($templateVars as $templateVar) {
                $tvs[$this->getOption('tvPrefix') . $templateVar->get('name')] = $templateVar->renderOutput($resource->get('id'));
            }
            $row = array_merge(array(
                'id' => $resource->get('id'),
                'pagetitle' => $resource->get('pagetitle'),
                'longtitle' => $resource->get('longtitle')
            ), $tvs);
        } else {
            $packagepath = $this->modx->getOption('core_path') . 'components/' . $this->getOption('packagename') . '/';
            $modelpath = $packagepath . 'model/';

            $this->modx->addPackage($this->getOption('packagename'), $modelpath);
            /** @var xPDOObject $resource */
            $resource = $this->modx->getObject($this->getOption('classname'), array($this->getOption('keyname') => $docId));
            if ($resource) {
                $joinvalues = array();
                $joinoption = $this->getOption('joins');
                if ($joinoption) {
                    foreach ($joinoption as $join) {
                        $values = $resource->getOne($join);
                        $joinvalues[$join] = $values->toArray();
                    }
                }
                $row = array_merge($joinvalues, $resource->toArray());
            } else {
                $this->modx->log(xPDO::LOG_LEVEL_INFO, 'Could not retreive an object with key "' . $this->getOption('keyname') . '" and value "' . $docId . '"', '', 'RememberThis');
                return false;
            }
        }

        $newElement = $row;
        $newElement['rememberId'] = $docId;
        $newElement['identifier'] = $row[$this->getOption('keyname')];
        $newElement['properties'] = ($properties) ? http_build_query($properties) : '';
        $newElement['itemtitle'] = $this->getChunk($this->getOption('itemTitleTpl'), array_merge($this->options, $row, $properties));
        if (!empty($properties)) {
            $newElement['itemproperties'] = $properties;
        }

        foreach ($_SESSION['rememberThis'] as & $element) {
            if (!count(array_diff_assoc($element['element'], $newElement))) {
                $found = 1;
            }
        }
        if (!$found) {
            $_SESSION['rememberThis'][] = array('element' => $newElement);
            if ($this->getOption('useCookie')) {
                $this->setCookie();
            }
            if ($this->getOption('useDatabase') && $this->modx->user->id) {
                $this->setDBRecord();
            }
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
     * Save the the remembered list with a hash as a database record
     */
    public function saveList()
    {
        $hash = md5(json_encode($_SESSION['rememberThis']) . time() . $this->modx->site_id);

        /** @var RememberThisList $list */
        $list = $this->modx->newObject('RememberThisList');
        $list->fromArray(array(
            'user_id' => $this->modx->user->id,
            'createdon' => time(),
            'hash' => $hash,
            'list' => $_SESSION['rememberThis']
        ));
        $list->save();

        return $hash;
    }

    /**
     * Remove all elements from the list
     */
    public function clearList()
    {
        if (isset($_SESSION['rememberThis'])) {
            $_SESSION['rememberThis'] = array();
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
        if ($list = $this->modx->getObject('RememberThisList', array(
            'user_id' => $this->modx->user->id,
            'hash:IS' => null
        ))) {
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
        if (!$list = $this->modx->getObject('RememberThisList', array(
            'user_id' => $this->modx->user->id,
            'hash:IS' => null
        ))) {
            $list = $this->modx->newObject('RememberThisList');
            $list->fromArray(array(
                'user_id' => $this->modx->user->id,
                'createdon' => time()
            ));
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
        return preg_replace("/\\[\\[([^\\[\\]]++|(?R))*?\\]\\]/s", '', $string);
    }

    /**
     * Parse a chunk (with template bindings)
     * Modified parseTplElement method from getResources package (https://github.com/opengeek/getResources)
     *
     * @param $type
     * @param $source
     * @param null $properties
     * @return bool
     */
    private function parseChunk($type, $source, $properties = null)
    {
        $output = false;

        if (!is_string($type) || !in_array($type, $this->_validTypes)) {
            $type = $this->modx->getOption('tplType', $properties, '@CHUNK');
        }

        $content = false;
        switch ($type) {
            case '@FILE':
                $path = $this->modx->getOption('tplPath', $properties, $this->modx->getOption('assets_path', $properties, MODX_ASSETS_PATH) . 'elements/chunks/');
                $key = $path . $source;
                if (!isset($this->_tplCache['@FILE'])) {
                    $this->_tplCache['@FILE'] = array();
                }
                if (!array_key_exists($key, $this->_tplCache['@FILE'])) {
                    if (file_exists($key)) {
                        $content = file_get_contents($key);
                    }
                    $this->_tplCache['@FILE'][$key] = $content;
                } else {
                    $content = $this->_tplCache['@FILE'][$key];
                }
                if (!empty($content) && $content !== '0') {
                    $chunk = $this->modx->newObject('modChunk', array('name' => $key));
                    $chunk->setCacheable(false);
                    $output = $chunk->process($properties, $content);
                }
                break;
            case '@INLINE':
                $uniqid = uniqid();
                $chunk = $this->modx->newObject('modChunk', array('name' => "{$type}-{$uniqid}"));
                $chunk->setCacheable(false);
                $output = $chunk->process($properties, $source);
                break;
            case '@CHUNK':
            default:
                $chunk = null;
                if (!isset($this->_tplCache['@CHUNK'])) {
                    $this->_tplCache['@CHUNK'] = array();
                }
                if (!array_key_exists($source, $this->_tplCache['@CHUNK'])) {
                    if ($chunk = $this->modx->getObject('modChunk', array('name' => $source))) {
                        $this->_tplCache['@CHUNK'][$source] = $chunk->toArray('', true);
                    } else {
                        $this->_tplCache['@CHUNK'][$source] = false;
                    }
                } elseif (is_array($this->_tplCache['@CHUNK'][$source])) {
                    $chunk = $this->modx->newObject('modChunk');
                    $chunk->fromArray($this->_tplCache['@CHUNK'][$source], '', true, true, true);
                }
                if (is_object($chunk)) {
                    $chunk->setCacheable(false);
                    $output = $chunk->process($properties);
                }
                break;
        }
        return $output;
    }

    /**
     * Get and parse a chunk (with template bindings)
     * Modified parseTpl method from getResources package (https://github.com/opengeek/getResources)
     *
     * @param $tpl
     * @param null $properties
     * @return bool
     */
    public function getChunk($tpl, $properties = null)
    {
        $output = false;
        if (!empty($tpl)) {
            $bound = array(
                'type' => '@CHUNK',
                'value' => $tpl
            );
            if (strpos($tpl, '@') === 0) {
                $endPos = strpos($tpl, ' ');
                if ($endPos > 2 && $endPos < 10) {
                    $tt = substr($tpl, 0, $endPos);
                    if (in_array($tt, $this->_validTypes)) {
                        $bound['type'] = $tt;
                        $bound['value'] = substr($tpl, $endPos + 1);
                    }
                }
            }
            if (is_array($bound) && isset($bound['type']) && isset($bound['value'])) {
                $output = $this->parseChunk($bound['type'], $bound['value'], $properties);
            }
        }
        return $output;
    }
}

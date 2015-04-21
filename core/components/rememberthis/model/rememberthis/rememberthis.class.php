<?php

/**
 * RememberThis
 *
 * Copyright 2008-2015 by Thomas Jakobi <thomas.jakobi@partout.info>
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
     * The class options
     * @var array $options
     */
    public $options = array();

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

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/rememberthis/', true);
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/rememberthis/', true);
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/rememberthis/', true);

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'version' => '1.0.0',
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
            'tvPrefix' => $this->getOption('tvPrefix', $options, 'tv.', true),
            'ajaxLoaderImg' => $this->getOption('ajaxLoaderImg', null, $this->getOption('assetsUrl') . 'css/ajax-loader.gif'),
            'language' => $this->getOption('language', $options, $this->modx->getOption('cultureKey'), true),
            'languages' => array('de', 'en'),
            'notRememberRedirect' => intval($modx->getOption('notRememberRedirect', $options, false, true)),
            'argSeparator' => ($this->modx->getOption('xhtml_urls')) ? '&amp;' : '&',
            'queryAdd' => $this->getOption('queryAdd', $options, 'add', true),
            'queryDelete' => $this->getOption('queryDelete', $options, 'delete', true)
        ));

        // Custom options
        $this->setOption('language', in_array($this->getOption('language'), $this->getOption('languages')) ? $this->getOption('language') : 'en');

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
    }

    /**
     * Init scripts and add/delete only once
     */
    public function init()
    {
        if (!$this->getOption('init', null, false)) {
            if ($this->getOption('includeScripts')) {
                if ($this->getOption('jQueryPath') != '') {
                    $this->modx->regClientScript($this->getOption('jQueryPath'));
                }
                $this->modx->regClientScript($this->getOption('assetsUrl') . 'js/rememberthis.min.js?v=' . $this->getOption('version'));
                $script = $this->modx->getChunk($this->getOption('scriptTpl'), array(
                    'options' => json_encode($this->getOption('scriptOptions'))
                ));
                $this->modx->regClientScript($script);
            }
            if ($this->getOption('includeCss')) {
                $this->modx->regClientCSS($this->getOption('assetsUrl') . 'css/rememberthis.css?v=' . $this->getOption('version'));
            }

            // Add/remove items to/from the list
            if (isset($_GET[$this->getOption('queryAdd')])) {
                $this->elementAdd($_GET[$this->getOption('queryAdd')]);
            }
            if (isset($_GET[$this->getOption('queryDelete')])) {
                $this->elementDelete(intval($_GET[$this->getOption('queryDelete')]));
            }
            $this->setOption('init', true);
        }
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned, if the option is not found locally or as a namespaced system setting.
     * @param bool $skipEmpty If true: use default value if option value is empty.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null, $skipEmpty = false)
    {
        $option = '';
        if (!empty($key) && is_string($key)) {
            if ($options !== null && array_key_exists($key, $options) && !($skipEmpty && empty($options[$key]))) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options) && !($skipEmpty && empty($options[$key]))) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}", null, $default, $skipEmpty);
            } elseif ($skipEmpty) {
                $option = $default;
            }
        }
        return $option;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setOption($key, $value)
    {
        if ($value) {
            $this->options[$key] = $value;
        }
    }

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
     * @param $addId string Key to add
     * @param $options array Template options
     * @return string
     */
    public function outputAdd($addId, $options)
    {
        $output = $this->modx->getChunk($options['addTpl'], array(
            'rememberurl' => $this->makeUrl(array($this->getOption('queryAdd') => $addId)),
            'rememberidentifier' => $this->modx->request->getResourceIdentifier('alias'),
            'rememberid' => $addId
        ));
        return $output;
    }

    /**
     * Output the AJAX result
     *
     * @param $options array Template options
     * @return string
     */
    public function outputAjax($options)
    {
        $options['language'] = in_array($options['language'], $this->getOption('languages')) ? $options['language'] : 'en';
        $this->modx->setOption('cultureKey', $options['language']);

        $output = '';
        if ($options['add']) {
            $index = $this->elementAdd($options['add']);
            if ($index !== false) {
                $fields = array_merge($_SESSION['rememberThis'][$index]['element'], array(
                    'deleteurl' => $this->modx->makeUrl($this->getOption('site_start'), '', array($this->getOption('queryDelete') => $index + 1)),
                    'deleteid' => $index + 1
                ));
                if (count($_SESSION['rememberThis']) == 1) {
                    $output = $this->modx->getChunk($this->getOption('outerTpl'), array(
                        'wrapper' => $this->modx->getChunk($this->getOption('rowTpl'), $fields)
                    ));
                } else {
                    $output = $this->modx->getChunk($this->getOption('rowTpl'), $fields);
                }
            }
            if ($this->getOption('debug')) {
                $output = '<pre>DEBUG: $_SESSION[\'rememberThis\'] = ' . print_r($_SESSION['rememberThis'], true) . '</pre>';
            }
        } else {
            if ($options['delete']) {
                $this->elementDelete($options['delete']);
                if (count($_SESSION['rememberThis']) > 0) {
                    $output = (string)$options['delete'];
                } else {
                    $output = $this->modx->getChunk($this->getOption('noResultsTpl'));
                }
                if ($this->getOption('debug')) {
                    $output .= '<pre>DEBUG: $_SESSION["rememberThis"] = ' . print_r($_SESSION['rememberThis'], true) . '</pre>';
                }
            }
            if (count($_SESSION['rememberThis']) == 0) {
                $output = $this->modx->getChunk($this->getOption('noResultsTpl'));
            }
        }
        return $output;
    }

    /**
     * Output the remembered list
     *
     * @param $options array Template options
     * @return string
     */
    public function outputList($options)
    {
        $output = '';
        if (!count($_SESSION['rememberThis'])) {
            if (!$this->getOption('notRememberRedirect')) {
                $output = $this->modx->getChunk($options['outerTpl'], array(
                    'wrapper' => $this->modx->getChunk($options['noResultsTpl'])
                ));
            } else {
                $this->modx->sendRedirect($this->modx->makeUrl($this->getOption('notRememberRedirect')));
            }
        } else {
            $output = $this->modx->getChunk($options['outerTpl'], array(
                'wrapper' => $this->outputElements($options['rowTpl'])
            ));
        }
        if ($this->getOption('debug')) {
            $output .= '<pre>DEBUG: $_SESSION["rememberThis"] = ' . print_r($_SESSION['rememberThis'], TRUE) . '</pre>';
        }
        return $output;
    }

    /**
     * Output the remembered list elements
     *
     * @param $tpl
     * @return array|string
     */
    public function outputElements($tpl)
    {
        $output = array();
        $iteration = 0;

        foreach ($_SESSION['rememberThis'] as $key => $element) {
            if ($tpl != '') {
                $fields = array_merge($element['element'], array(
                    'deleteurl' => $this->makeUrl(array($this->getOption('queryDelete') => $key + 1)),
                    'deleteidentifier' => $this->modx->request->getResourceIdentifier('alias'),
                    'deleteid' => $key + 1,
                    'iteration' => $iteration,
                    'count' => $element['count']
                ));

                $output[] = $this->modx->getChunk($tpl, $fields);
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
     * @param $docId
     * @return bool|mixed
     */
    private function elementAdd($docId)
    {
        $found = 0;
        $newElement = array();
        $newElement['rememberId'] = $docId;

        if (!$this->getOption('packagename')) {
            // no packagename -> resource
            $resource = $this->modx->getObject('modResource', array('id' => $docId));
            $tvs = array();
            $templateVars = &$resource->getMany('TemplateVars');
            foreach ($templateVars as $templateVar) {
                $tvs[$this->getOption('tvPrefix') . $templateVar->get('name')] = $templateVar->renderOutput($resource->get('id'));
            }
            $row = array_merge($resource->toArray(), $tvs);
        } else {
            $packagepath = $this->modx->getOption('core_path') . 'components/' . $this->getOption('packagename') . '/';
            $modelpath = $packagepath . 'model/';

            $this->modx->addPackage($this->getOption('packagename'), $modelpath);
            $resource = $this->modx->getObject($this->getOption('classname'), array($this->getOption('keyname') => $docId));
            if ($resource) {
                $joinvalues = array();
                foreach ($this->getOption('joins') as $join) {
                    $values = $resource->getOne($join);
                    $joinvalues[$join] = $values->toArray();
                }
                $row = array_merge($joinvalues, $resource->toArray());
            } else {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not retreive an object with key "' . $this->getOption('keyname') . '" and value "' . $docId . '"', '', 'RememberThis');
                return false;
            }
        }

        $newElement = $row;
        $newElement['itemtitle'] = $this->modx->getChunk($this->getOption('itemTitleTpl'), $row);

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
            return false;
        }
    }

    /**
     * Remove an element from the list
     *
     * @param $index
     */
    private function elementDelete($index)
    {
        $index--;
        if (isset($_SESSION['rememberThis'])) {
            if (isset($_SESSION['rememberThis'][$index])) {
                unset($_SESSION['rememberThis'][$index]);
            }
        }
    }
}

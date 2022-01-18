<?php
/**
 * Remember Processor
 *
 * @package rememberthis
 * @subpackage processors
 */

use TreehillStudio\RememberThis\Processors\Processor;

class RememberThisUploadProcessor extends Processor
{
    public $languageTopics = ['rememberthis:default'];

    /**
     * @var array $session
     */
    private $session;

    public function process()
    {
        // set processing options
        $options = [
            'language' => $this->modx->getOption('language', $_GET, 'en'),
            'add' => $this->modx->getOption('add', $_GET, 0),
            'delete' => intval($this->modx->getOption('delete', $_GET, 0)),
            'addproperties' => []
        ];

        foreach ($this->modx->getOption('addproperties', $_GET, []) as $key => $value) {
            $propertylen = strlen($this->rememberthis->getOption(('queryAdd')) . 'property_');
            if (substr($key, 0, $propertylen) == $this->rememberthis->getOption(('queryAdd')) . 'property_') {
                $options['addproperties'][substr($key, $propertylen)] = $this->rememberthis->stripTags($value);
            }
        }

        $result = $this->rememberthis->ajaxResult($options);

        // return result
        return json_encode(array_merge($result, [
            'success' => true,
        ]));
    }
}

return 'RememberThisUploadProcessor';

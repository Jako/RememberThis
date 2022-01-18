<?php
/**
 * Abstract processor
 *
 * @package rememberthis
 * @subpackage processors
 */

namespace TreehillStudio\RememberThis\Processors;

use modProcessor;
use modX;
use TreehillStudio\RememberThis\RememberThis;

/**
 * Class Processor
 */
abstract class Processor extends modProcessor
{
    public $languageTopics = ['rememberthis:default'];

    /** @var RememberThis */
    public $rememberthis;

    /**
     * {@inheritDoc}
     * @param modX $modx A reference to the modX instance
     * @param array $properties An array of properties
     */
    function __construct(modX &$modx, array $properties = [])
    {
        parent::__construct($modx, $properties);

        $corePath = $this->modx->getOption('rememberthis.core_path', null, $this->modx->getOption('core_path') . 'components/rememberthis/');
        $this->rememberthis = $this->modx->getService('rememberthis', 'RememberThis', $corePath . 'model/rememberthis/');
    }

    abstract public function process();
}

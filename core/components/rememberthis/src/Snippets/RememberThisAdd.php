<?php
/**
 * RememberThisAdd Snippet
 *
 * @package rememberthis
 * @subpackage snippet
 */

namespace TreehillStudio\RememberThis\Snippets;

class RememberThisAdd extends Snippet
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'addTpl' => $this->modx->getOption('rememberthis.addTpl'),
            'tplPath' => $this->modx->getOption('rememberthis.tplPath'),
            'addId::int' => (isset($this->modx->resource)) ? $this->modx->resource->get('id') : 0
        ];
    }

    /**
     * Execute the snippet and return the result.
     *
     * @return string
     * @throws /Exception
     */
    public function execute()
    {
        $addId = $this->getProperty('addId');

        return $this->rememberthis->showButton($addId, $this->getProperties());
    }
}

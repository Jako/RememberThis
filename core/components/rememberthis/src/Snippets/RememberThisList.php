<?php
/**
 * RememberThisAdd Snippet
 *
 * @package rememberthis
 * @subpackage snippet
 */

namespace TreehillStudio\RememberThis\Snippets;

class RememberThisList extends Snippet
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'rowTpl' => $this->modx->getOption('rememberthis.rowTpl'),
            'outerTpl' => $this->modx->getOption('rememberthis.outerTpl'),
            'wrapperTpl' => $this->modx->getOption('rememberthis.wrapperTpl'),
            'noResultsTpl' => $this->modx->getOption('rememberthis.noResultsTpl'),
            'tplPath' => $this->modx->getOption('rememberthis.tplPath'),
            'jsonList::bool' => false,
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
        $init = $this->rememberthis->getOption('init', null, false);
        $this->rememberthis->init();

        $this->properties['hash'] = $this->modx->getOption('rememberthis', $_REQUEST, false, true);

        $result = $this->rememberthis->showList($this->getProperties());
        if ($this->getProperty('jsonList')) {
            $output = json_encode($result['list']);
        } else {
            $output = $result['result'];
            if ($this->rememberthis->getOption('debug') && !$init) {
                $output .= '<pre class="rememberdebug">' . $result['debug'] . '</pre>';
            }
        }
        return $output;
    }
}

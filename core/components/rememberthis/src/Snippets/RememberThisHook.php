<?php
/**
 * RememberThis2FormIt Hook
 *
 * @package rememberthis
 * @subpackage hook
 */

namespace TreehillStudio\RememberThis\Snippets;

class RememberThisHook extends Hook
{
    /**
     * Get default snippet properties.
     *
     * @return array
     */
    public function getDefaultProperties()
    {
        return [
            'rowTpl' => $this->modx->getOption('rememberthis.rowTpl', null, ''),
            'outerTpl' => $this->modx->getOption('rememberthis.outerTpl', null, ''),
            'wrapperTpl' => $this->modx->getOption('rememberthis.wrapperTpl', null, ''),
            'noResultsTpl' => $this->modx->getOption('rememberthis.noResultsTpl', null, ''),
            'tplPath' => $this->modx->getOption('rememberthis.tplPath', null, ''),
            'jsonList::bool' => false,
            'clearList::bool' => false,
            'saveList::bool' => false,
        ];
    }

    /**
     * Execute the hook and return success.
     *
     * @return bool
     * @throws /Exception
     */
    public function execute()
    {
        $properties = [
            'rowTpl' => $this->getProperty('rowTpl'),
            'outerTpl' => $this->getProperty('outerTpl'),
            'wrapperTpl' => $this->getProperty('wrapperTpl'),
            'noResultsTpl' => $this->getProperty('noResultsTpl'),
            'tplPath' => $this->getProperty('tplPath'),
            'jsonList' => $this->getProperty('jsonList'),
            'clearList' => $this->getProperty('clearList'),
            'saveList' => $this->getProperty('saveList'),
            'hash' => $this->getProperty('hash'),
        ];
        $result = $this->rememberthis->showList($properties);
        if ($this->getProperty('jsonList')) {
            $this->hook->setValue('rememberthis', json_encode($result['list']));
        } else {
            $this->hook->setValue('rememberthis', $result['result']);
        }
        $this->hook->setValue('rememberthis.list', $result['list']);
        $this->hook->setValue('rememberthis.count', $result['count']);

        if ($this->getProperty('saveList')) {
            $this->hook->setValue('rememberthis.hash', $this->rememberthis->saveList());
        }

        if ($this->getProperty('clearList')) {
            $this->rememberthis->clearList();
        }

        return true;
    }
}

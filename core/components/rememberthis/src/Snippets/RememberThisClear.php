<?php
/**
 * RememberThis2FormIt Hook
 *
 * @package rememberthis
 * @subpackage hook
 */

namespace TreehillStudio\RememberThis\Snippets;

class RememberThisClear extends Hook
{
    /**
     * Execute the hook and return success.
     *
     * @return bool
     * @throws /Exception
     */
    public function execute()
    {
        $this->rememberthis->clearList();

        return true;
    }
}

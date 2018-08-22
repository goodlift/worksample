<?php

namespace WorkSample\CustomerStatus\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use WorkSample\CustomerStatus\Controller\Index as AbstractIndex;

/**
 * Class Index
 * @package WorkSample\CustomerStatus\Controller\Index
 */
class Index extends AbstractIndex
{
    /**
     * Show customer status page
     *
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}

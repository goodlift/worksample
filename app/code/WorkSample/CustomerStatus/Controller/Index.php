<?php

namespace WorkSample\CustomerStatus\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Index
 * @package WorkSample\CustomerStatus\Controller
 */
abstract class Index extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Index constructor
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;

        parent::__construct($context);
    }

    /**
     * Check is customer authenticated
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->getActionFlag()->set('', self::FLAG_NO_DISPATCH, true);
            $this->_redirect('customer/account');
        }

        return parent::dispatch($request);
    }
}

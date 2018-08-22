<?php

namespace WorkSample\CustomerStatus\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CustomerStatus
 * @package WorkSample\CustomerStatus\Block
 */
class CustomerStatus extends Template
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * CustomerStatus constructor
     *
     * @param Template\Context $context
     * @param CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(Template\Context $context, CurrentCustomer $currentCustomer, array $data = [])
    {
        $this->currentCustomer = $currentCustomer;

        parent::__construct($context, $data);
    }

    /**
     * Returns customer status attribute value
     *
     * @return string
     */
    public function getCustomerStatus()
    {
        try {
            /** @var CustomerInterface $customer */
            $customer = $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            $customer = null;
        }

        if ($customer instanceof CustomerInterface) {
            return $customer->getCustomAttribute('customer_status')
                ? $customer->getCustomAttribute('customer_status')->getValue()
                : '';
        }

        return '';
    }

    /**
     * Returns form submit url
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('worksample/index/post', ['_secure' => true]);
    }
}

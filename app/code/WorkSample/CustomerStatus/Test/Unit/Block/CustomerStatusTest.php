<?php

namespace WorkSample\CustomerStatus\Test\Unit\Block;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\AttributeInterface;
use WorkSample\CustomerStatus\Block\CustomerStatus;

/**
 * Class CustomerStatusTest
 * @package WorkSample\CustomerStatus\Test\Unit\Block
 */
class CustomerStatusTest extends TestCase
{
    const CUSTOM_ATTRIBUTE_CODE = 'customer_status';
    const CUSTOM_ATTRIBUTE_VALUE = 'TEST_STATUS';

    /**
     * @var CustomerStatus
     */
    protected $block;

    /**
     * @var MockObject | CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var MockObject | Context
     */
    protected $context;

    /**
     * @var MockObject | CustomerInterface
     */
    protected $customer;

    /**
     * @var MockObject | AttributeInterface
     */
    protected $attribute;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        parent::setUp();

        $this->attribute = $this->getMockBuilder(AttributeInterface::class)
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->attribute->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(self::CUSTOM_ATTRIBUTE_VALUE));

        $this->customer = $this->createMock(CustomerInterface::class);
        $this->customer->expects($this->any())
            ->method('getCustomAttribute')
            ->with(self::CUSTOM_ATTRIBUTE_CODE)
            ->will($this->returnValue($this->attribute));


        $this->currentCustomer = $this->createMock(CurrentCustomer::class);
        $this->currentCustomer->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($this->customer));

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->block = new CustomerStatus(
            $this->context,
            $this->currentCustomer
        );
    }

    /**
     * Tears down the fixture
     */
    protected function tearDown()
    {
        $this->context = null;
        $this->currentCustomer = null;
        $this->block = null;
    }

    /**
     * Test getCustomerStatus() method
     */
    public function testGetCustomerStatus()
    {
        $customerStatus = $this->block->getCustomerStatus();
        $this->assertEquals($customerStatus, self::CUSTOM_ATTRIBUTE_VALUE);
    }
}
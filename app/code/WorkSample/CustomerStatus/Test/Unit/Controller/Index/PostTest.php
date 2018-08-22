<?php

namespace WorkSample\CustomerStatus\Test\Unit\Controller\Index;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use WorkSample\CustomerStatus\Controller\Index\Post;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Psr\Log\LoggerInterface;

/**
 * Class PostTest
 * @package WorkSample\CustomerStatus\Test\Unit\Controller\Index
 */
class PostTest extends TestCase
{
    /**
     * @var Post
     */
    protected $model;

    /**
     * @var Context|MockObject
     */
    protected $context;

    /**
     * @var Session|MockObject
     */
    protected $customerSession;

    /**
     * @var CustomerRepositoryInterface|MockObject
     */
    protected $customerRepository;

    /**
     * @var LoggerInterface|MockObject
     */
    protected $logger;

    /**
     * @var Validator|MockObject
     */
    protected $validator;

    /**
     * @var RedirectFactory|MockObject
     */
    protected $resultRedirectFactory;

    /**
     * @var Redirect|MockObject
     */
    protected $resultRedirect;

    /**
     * @var Http|MockObject
     */
    protected $request;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $messageManager;

    /**
     * Prepare context fixtures
     */
    protected function prepareContext()
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectFactory = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageManager = $this->getMockBuilder(ManagerInterface::class)
            ->getMockForAbstractClass();

        $this->context->expects($this->any())
            ->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactory);

        $this->context->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->context->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);

        $this->resultRedirectFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->resultRedirect);
    }

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->prepareContext();

        $this->customerSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerRepository = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Post(
            $this->context,
            $this->customerSession,
            $this->customerRepository,
            $this->logger,
            $this->validator
        );
    }

    public function testInvalidFormKey()
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(false);

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('worksample')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->model->execute());
    }

    public function testNoPostValues()
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->request->expects($this->once())
            ->method('isPost')
            ->willReturn(false);

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('worksample')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->model->execute());
    }

    public function testGeneralSave()
    {
        $attributeCode = 'customer_status';
        $status = 'TEST_STATUS';

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->request->expects($this->once())
            ->method('isPost')
            ->willReturn(true);

        $this->request->expects($this->once())
            ->method('getPost')
            ->with($attributeCode)
            ->willReturn($status);

        $dataObject = $this->getMockBuilder(CustomerInterface::class)
            ->getMockForAbstractClass();
        $dataObject->expects($this->any())
            ->method('setCustomAttribute')
            ->with($attributeCode, $status)
            ->willReturnSelf();

        $customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $customer->expects($this->any())
        ->method('getDataModel')
        ->willReturn($dataObject);

        $this->customerSession->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customer);

        $this->customerRepository->expects($this->once())
            ->method('save')
            ->with($dataObject)
            ->willReturnSelf();

        $this->messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('Customer status was successfully updated.'))
            ->willReturnSelf();

        $this->logger->expects($this->any())
            ->method('critical')
            ->willReturnSelf();

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('worksample')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->model->execute());
    }
}

<?php

namespace WorkSample\CustomerStatus\Controller\Index;

use Psr\Log\LoggerInterface;
use WorkSample\CustomerStatus\Controller\Index as AbstractIndex;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Post
 * @package WorkSample\CustomerStatus\Controller\Index
 */
class Post extends AbstractIndex
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * Post constructor
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger,
        Validator $formKeyValidator
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->formKeyValidator = $formKeyValidator;

        parent::__construct($context, $customerSession);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        if ($validFormKey && $this->getRequest()->isPost()) {
            try {
                $status = $this->getRequest()->getPost('customer_status');
                $customerData = $this->customerSession->getCustomer()->getDataModel();
                $customerData->setCustomAttribute('customer_status', $status);
                $this->customerRepository->save($customerData);
                $this->messageManager->addSuccessMessage(__('Customer status was successfully updated.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your request. Please try again later.')
                );
            }
        }

        return $resultRedirect->setPath('worksample');
    }
}

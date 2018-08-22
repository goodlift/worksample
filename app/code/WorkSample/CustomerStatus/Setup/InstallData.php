<?php

namespace WorkSample\CustomerStatus\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Eav\Model\Config;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Indexer\Model\Indexer;

/**
 * Class InstallData
 * @package WorkSample\CustomerStatus\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var Attribute
     */
    private $attributeResource;

    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    /**
     * InstallData constructor
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Config $eavConfig
     * @param Attribute $attributeResource
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        Config $eavConfig,
        Attribute $attributeResource,
        IndexerFactory $indexerFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(Customer::ENTITY, 'customer_status', [
            'type' => 'varchar',
            'label' => 'Customer Status',
            'input' => 'text',
            'required' => false,
            'position' => 150,
            'sort_order' => 150,
            'visible' => true,
            'system' => false,
            'validate_rules' => '{"max_text_length":255}',
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'customer_status');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $this->attributeResource->save($attribute);

        /** @var Indexer $indexer */
        $indexer = $this->indexerFactory->create();
        $indexer->load('customer_grid');
        $indexer->reindexAll();
    }
}

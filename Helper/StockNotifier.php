<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;

class StockNotifier
{

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @var Configurable
     */
    protected $_configurable;

    /**
     * StockNotifier constructor.
     * @param Configurable $configurable
     * @param CollectionFactory $collectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param LayoutInterface $layout
     */
    public function __construct(
        Configurable $configurable,
        CollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository,
        LayoutInterface $layout
    )
    {
        $this->_configurable = $configurable;
        $this->_collectionFactory = $collectionFactory;
        $this->_productRepository = $productRepository;
        $this->_layout = $layout;
    }

    /**
     * Create block instance
     *
     * @param string|AbstractBlock $block
     * @return AbstractBlock
     * @throws LocalizedException
     */
    public function createBlock($block)
    {
        if (is_string($block)) {
            if (class_exists($block)) {
                $block = $this->_layout->createBlock($block);
            }
        }
        if (!$block instanceof AbstractBlock) {
            throw new LocalizedException(__('Invalid block type: %1', $block));
        }
        return $block;
    }

    /**
     * @param int $productId
     * @return bool
     */
    public function productExists($productId)
    {
        try {
            /**
             * @var Product
             */
            $product = $this->_productRepository->getById($productId);
            if (in_array($product->getTypeId(), ['simple', 'configurable'])) {
                return true;
            }
            return false;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     */
    public function getTargetProduct($productId)
    {
        $simpleProduct = $this->_productRepository->getById($productId);
        $configIds = $this->_configurable->getParentIdsByChild($productId);
        if (empty($configIds)) {
            return $simpleProduct;
        }
        //find the matching parent...
        $collection = $this->_collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', $configIds);
        foreach ($collection as $configurable) {
            if ($configurable->getAttributeSetId() == 11 /*Monogrammable*/) {
                if ($configurable->getColor() == $simpleProduct->getColor()) {
                    return $configurable;
                }
            }
            if ($configurable->getAttributeSetId() == 12 /*Alphabet*/) {
                $letter = substr($configurable->getSku(), strlen($configurable->getSku()) - 1);
                if ($letter == substr($simpleProduct->getSku(), 0, 1)) {
                    return $configurable;
                }
            }
        }
        //no match found
        $simpleProduct;
    }

    /**
     * @param $id
     * @param $superAttributes
     * @return Product | bool
     */
    public function getProductId($id, $superAttributes)
    {
        /**
         * @var Product
         */
        $product = $this->_productRepository->getById($id);
        if ($product->getTypeId() != 'configurable') {
            return false;
        }
        try {
            return $product->getTypeInstance()
                ->getProductByAttributes($superAttributes, $product);
        } catch (Exception $e) {
            return false;
        }
    }
}
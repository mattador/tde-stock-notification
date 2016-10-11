<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Block\Email;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Stock extends Template
{

    /**
     * @var string
     */
    protected $_template = 'email/stock_alert.phtml';

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @param Context $context
     * @param Image $imageHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Image $imageHelper,
        array $data = []
    )
    {
        $this->_imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Add product to collection
     *
     * @param Product $product
     * @return void
     */
    public function addProduct(Product $product)
    {
        $this->_product = $product;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Return HTML block with tier price
     *
     * @param Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    )
    {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var Render $priceRender */
        $priceRender = $this->getPriceRender();
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render(
                $priceType,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * @return Render
     */
    protected function getPriceRender()
    {
        return $this->_layout->createBlock(
            'Magento\Framework\Pricing\Render',
            '',
            ['data' => ['price_render_handle' => 'catalog_product_prices']]
        );
    }

    /**
     * Return product image URL
     *
     * @param $product
     * @return string
     */
    public function getImageUrl($product)
    {
        return $this->_imageHelper->init(
            $product,
            'product_thumbnail_image',
            ['class' => 'photo image']
        )->getUrl();
    }
}

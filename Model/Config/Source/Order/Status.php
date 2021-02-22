<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) Intelipost
 * @author      Alex Restani <alex.restani@intelipost.com.br>
 */

namespace Intelipost\Tracking\Model\Config\Source\Order;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    protected $_statusCollectionFactory;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
    ) {
        $this->_statusCollectionFactory = $statusCollectionFactory;
    }

    public function toOptionArray()
    {
        $pleaseSelect = [
         ['value' => '', 'label' => __(' --- Please Select --- ')]
        ];
        $options = $this->_statusCollectionFactory->create()->toOptionArray();
        return array_merge($pleaseSelect, $options);
    }
}

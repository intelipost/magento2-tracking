<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Model\Sales\Order\Shipment;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\AbstractModel;

use Intelipost\Tracking\Api\Sales\Data\ShipmentTrackInterface;

class Track extends \Magento\Sales\Model\Order\Shipment\Track implements ShipmentTrackInterface // extends AbstractModel
{

/**
 * Tracking url getter
 *
 * @codeCoverageIgnore
 *
 * @return string
 */
    public function getUrl()
    {
        return $this->getData('track_url');
    }

/**
 * Tracking url setter
 *
 * @codeCoverageIgnore
 *
 * @param string $number
 * @return \Magento\Framework\DataObject
 */
    public function setUrl($url)
    {
        return $this->setData('track_url', $url);
    }

/**
 * Add data to the object.
 *
 * Retains previous data in the object.
 *
 * @param array $data
 * @return $this
 */
    public function addData(array $data)
    {
        if (array_key_exists('url', $data)) {
            $this->setUrl($data['url']);

            unset($data['url']);
        }

        return parent::addData($data);
    }

//@codeCoverageIgnoreStart

/**
 * Returns track_url
 *
 * @return string
 */
    public function getTrackUrl()
    {
        return $this->getData(ShipmentTrackInterface::TRACK_URL);
    }

/**
 * {@inheritdoc}
 */
    public function setTrackUrl($trackUrl)
    {
        return $this->setData(ShipmentTrackInterface::TRACK_URL, $trackUrl);
    }

//@codeCoverageIgnoreEnd
}

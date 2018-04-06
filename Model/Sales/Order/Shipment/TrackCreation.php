<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Model\Sales\Order\Shipment;

class TrackCreation
extends \Magento\Sales\Model\Order\Shipment\TrackCreation
implements \Intelipost\Tracking\Api\Sales\Data\ShipmentTrackCreationInterface
// implements \Magento\Sales\Api\Data\ShipmentTrackCreationInterface
{

/**
 * @var string
 */
private $description;

/**
 * @var string
 */
private $qty;

/**
 * @var string
 */
private $trackUrl;

/**
 * @var string
 */
private $weight;

//@codeCoverageIgnoreStart

/**
 * {@inheritdoc}
 */
public function getDescription()
{
    return $this->description;
}

/**
 * {@inheritdoc}
 */
public function setDescription($description)
{
    $this->description = $description;
    return $this;
}

/**
 * {@inheritdoc}
 */
public function getQty()
{
    return $this->qty;
}

/**
 * {@inheritdoc}
 */
public function setQty($qty)
{
    $this->qty = $qty;
    return $this;
}

/**
 * {@inheritdoc}
 */
public function getTrackUrl()
{
    return $this->trackUrl;
}

/**
 * {@inheritdoc}
 */
public function setTrackUrl($trackUrl)
{
    $this->trackUrl = $trackUrl;
    return $this;
}

/**
 * {@inheritdoc}
 */
public function getWeight()
{
    return $this->weight;
}

/**
 * {@inheritdoc}
 */
public function setWeight($weight)
{
    $this->weight = $weight;
    return $this;
}

//@codeCoverageIgnoreEnd

}


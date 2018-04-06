<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Api\Sales\Data;

// use Magento\Framework\Api\ExtensibleDataInterface;
// use Magento\Sales\Api\Data\TrackInterface;

interface ShipmentTrackCreationInterface
extends \Magento\Sales\Api\Data\ShipmentTrackCreationInterface
// extends TrackInterface, ExtensibleDataInterface
{

/**
 * Sets the track description for the shipment package.
 *
 * @param string $description
 * @return $this
 */
public function setDescription($description);

/**
 * Gets the track description for the shipment package.
 *
 * @return string description.
 */
public function getDescription();

/**
 * Sets the track qty for the shipment package.
 *
 * @param string $qty
 * @return $this
 */
public function setQty($qty);

/**
 * Gets the track qty for the shipment package.
 *
 * @return string qty.
 */
public function getQty();

/**
 * Sets the track url for the shipment package.
 *
 * @param string $trackUrl
 * @return $this
 */
public function setTrackUrl($trackUrl);

/**
 * Gets the track url for the shipment package.
 *
 * @return string Track url.
 */
public function getTrackUrl();

/**
 * Sets the track weight for the shipment package.
 *
 * @param string $weight
 * @return $this
 */
public function setWeight($weight);

/**
 * Gets the track weight for the shipment package.
 *
 * @return string weight.
 */
public function getWeight();

}


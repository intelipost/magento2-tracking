<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Api\Sales\Data;

// use Magento\Framework\Api\ExtensibleDataInterface;
// use Magento\Sales\Api\Data\TrackInterface;

interface ShipmentTrackInterface extends \Magento\Sales\Api\Data\ShipmentTrackInterface
// extends TrackInterface, ExtensibleDataInterface
{

/**#@+
 * Constants for keys of data array. Identical to the name of the getter in snake case.
 */

/*
 * Track url.
 */
    const TRACK_URL = 'track_url';

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
}

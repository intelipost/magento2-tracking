<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Model\Sales\Order;

use Magento\Sales\Api\Data\ShipmentItemCreationInterface;
use Magento\Sales\Api\Data\ShipmentPackageCreationInterface;
// use Magento\Framework\EntityManager\HydratorPool;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface;

use Magento\Sales\Model\Order\ShipmentFactory;

use Intelipost\Tracking\Api\Sales\Data\ShipmentTrackCreationInterface;
use Intelipost\Tracking\Api\Sales\Data\ShipmentTrackInterface;
use Intelipost\Tracking\Model\Sales\Order\Shipment\TrackFactory;

class ShipmentDocumentFactory
{

    private $shipmentFactory;
    private $trackFactory;
    private $hydratorPool;

/**
 * ShipmentDocumentFactory constructor.
 *
 * @param ShipmentFactory $shipmentFactory
 * @param HydratorPool $hydratorPool
 * @param TrackFactory $trackFactory
 */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        // HydratorPool $hydratorPool,
        TrackFactory $trackFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->trackFactory = $trackFactory;
        // $this->hydratorPool = $hydratorPool;
    }

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 * @param OrderInterface $order
 * @param ShipmentItemCreationInterface[] $items
 * @param ShipmentTrackCreationInterface[] $tracks
 * @param ShipmentCommentCreationInterface|null $comment
 * @param bool $appendComment
 * @param ShipmentPackageCreationInterface[] $packages
 * @param ShipmentCreationArgumentsInterface|null $arguments
 * @return ShipmentInterface
 */
    public function create(
        OrderInterface $order,
        array $items = [],
        array $tracks = [],
        ShipmentCommentCreationInterface $comment = null,
        $appendComment = false,
        array $packages = [],
        ShipmentCreationArgumentsInterface $arguments = null
    ) {
        $shipmentItems = $this->itemsToArray($items);
        /** @var Shipment $shipment */
        $shipment = $this->shipmentFactory->create(
            $order,
            $shipmentItems
        );
        $this->prepareTracks($shipment, $tracks);
        if ($comment) {
            $shipment->addComment(
                $comment->getComment(),
                $appendComment,
                $comment->getIsVisibleOnFront()
            );
        }

        return $shipment;
    }

/**
 * Adds tracks to the shipment.
 *
 * @param ShipmentInterface $shipment
 * @param ShipmentTrackCreationInterface[] $tracks
 * @return ShipmentInterface
 */
    private function prepareTracks(/* \Magento\Sales\Api\Data\ShipmentInterface */ $shipment, array $tracks)
    {
        foreach ($tracks as $track) {
            /*
            $hydrator = $this->hydratorPool->getHydrator(
            \Intelipost\Tracking\Api\Sales\Data\ShipmentTrackCreationInterface::class
            );

            $data = $hydrator->extract($track);
            */

            $data = [];

            $data[ShipmentTrackInterface::CARRIER_CODE] = $track->getCarrierCode();
            $data[ShipmentTrackInterface::TITLE] = $track->getTitle();
            $data[ShipmentTrackInterface::TRACK_NUMBER] = $track->getTrackNumber();

            // Extra
            $data [ShipmentTrackInterface::DESCRIPTION] = $track->getDescription();
            $data [ShipmentTrackInterface::QTY] = $track->getQty();
            $data [ShipmentTrackInterface::TRACK_URL] = $track->getTrackUrl();
            $data [ShipmentTrackInterface::WEIGHT] = $track->getWeight();

            $shipment->addTrack($this->trackFactory->create(['data' => $data /* $hydrator->extract($track) */]));
        }

        return $shipment;
    }

/**
 * Convert items to array
 *
 * @param ShipmentItemCreationInterface[] $items
 * @return array
 */
    private function itemsToArray(array $items = [])
    {
        $shipmentItems = [];
        foreach ($items as $item) {
            $shipmentItems[$item->getOrderItemId()] = $item->getQty();
        }

        return $shipmentItems;
    }
}

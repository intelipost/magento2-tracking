<?php
/*
 * @package     Intelipost_Push
 * @copyright   Copyright (c) Intelipost
 * @author      Alex Restani <alex.restani@intelipost.com.br>
 */

namespace Intelipost\Tracking\Controller\Webhook;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Webhook extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $_scopeConfig;
    protected $_helper;
    protected $_collectionFactory;
    protected $_shipment;
    protected $_order;
    protected $_convertOrder;
    protected $_track;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Intelipost\Tracking\Helper\Data $helper,
        \Magento\Backend\App\Action\Context $context,
        \Intelipost\Quote\Model\ResourceModel\Shipment\CollectionFactory $collectionFactory,
        \Intelipost\Quote\Model\Shipment $shipment,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $track
    ) {
        parent::__construct($context);
        $this->_scopeConfig        = $scopeConfig;
        $this->_helper             = $helper;
        $this->_collectionFactory  = $collectionFactory;
        $this->_shipment           = $shipment;
        $this->_order              = $order;
        $this->_convertOrder       = $convertOrder;
        $this->_track              = $track;
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function execute()
    {
        $webhook_enabled                  = $this->_scopeConfig->getValue('carriers/intelipost_tracking/webhook_enabled');
        $config_api_key                   = $this->_scopeConfig->getValue('intelipost_basic/settings/api_key');
        $track_pre_ship                   = $this->_scopeConfig->getValue('carriers/intelipost_tracking/track_pre_ship');
        $status_created                   = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_created');
        $status_ready_for_shipment        = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_ready_for_shipment');
        $status_shipped                   = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_shipped');
        $track_post_ship                  = $this->_scopeConfig->getValue('carriers/intelipost_tracking/track_post_ship');
        $status_in_transit                = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_in_transit');
        $status_to_be_delivered           = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_to_be_delivered');
        $status_delivered                 = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_delivered');
        $status_clarify_delivery_failed   = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_clarify_delivery_failed');
        $status_delivery_failed           = $this->_scopeConfig->getValue('carriers/intelipost_tracking/status_delivery_failed');
        $create_shipment_after_ip_shipped = $this->_scopeConfig->getValue('carriers/intelipost_tracking/create_shipment_after_ip_shipped');

        $pre_dispatch_events  = ['NEW', 'READY_FOR_SHIPPING', 'SHIPPED'];
        $post_dispatch_events = ['TO_BE_DELIVERED', 'IN_TRANSIT', 'DELIVERED', 'CLARIFY_DELIVERY_FAIL', 'DELIVERY_FAILED'];

        if ($webhook_enabled) {
            $api_key = $this->getRequest()->getHeader('api-key');

            if ($api_key == $config_api_key) {
                $obj           = json_decode(utf8_encode(file_get_contents('php://input')));
                $increment_id  = $obj->order_number;
                $state         = $obj->history->shipment_order_volume_state;
                $tracking_code = $obj->tracking_code;
                $comment       = '[Intelipost Webhook] - ' . $obj->history->shipment_volume_micro_state->default_name;

                $shipmentObj = $this->_collectionFactory->create();
                $shipmentObj->addFieldToFilter('so.increment_id', $increment_id);
                $colData = $shipmentObj->getData();
                $orderId = $colData[0]['entity_id'];

                $this->updateTrackingCode($colData, $tracking_code);

                if ((in_array($state, $pre_dispatch_events) && $track_pre_ship)
                    || in_array($state, $post_dispatch_events) && $track_post_ship) {
                    switch (strtoupper($state)) {
                        case 'NEW':
                            $status = $status_created;
                            $this->updateOrder($orderId, $status, $comment);
                            break;

                        case 'READY_FOR_SHIPPING':
                            $status = $status_ready_for_shipment;
                            $this->updateOrder($orderId, $status, $comment);
                            break;

                        case 'SHIPPED':
                            $status = $status_shipped;
                            if ($create_shipment_after_ip_shipped) {
                                $this->createShipment($orderId);
                            }
                            $this->updateOrder($orderId, $status, $comment);
                            break;

                        case 'IN_TRANSIT':
                            $status = $status_in_transit;
                            $this->updateOrder($orderId, $status, $comment);
                            break;

                        case 'TO_BE_DELIVERED':
                            $status = $status_to_be_delivered;
                            $this->updateOrder($orderId, $status, $comment);
                            break;

                        case 'DELIVERED':
                            $status = $status_delivered;
                            $this->updateOrder($orderId, $status, $comment);
                            break;

                        case 'CLARIFY_DELIVERY_FAIL':
                            $status = $status_clarify_delivery_failed;
                            $this->updateOrder($orderId, $status, $comment);
                            break;

                        case 'DELIVERY_FAILED':
                            $status = $status_delivery_failed;
                            $this->updateOrder($orderId, $status, $comment);
                            break;
                    }
                }
            }
        }
    }

    public function updateOrder($orderId, $status, $comment)
    {
        $order = $this->_order->load($orderId);
        $order->addStatusHistoryComment($comment)->setIsCustomerNotified(false);
        $order->setStatus($status);
        $order->save();
    }

    public function updateTrackingCode($colData, $trackingCode)
    {
        if ($trackingCode != null) {
            $_collectionFactory = $this->_shipment->load($colData[0]['id'], 'id');
            $_collectionFactory->setTrackingCode($trackingCode);
            $_collectionFactory->save();
        }
    }

    public function createShipment($orderId)
    {
        $order = $this->_order->load($orderId);

        if (! $order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t create an shipment.')
            );
        }

        $shipment = $this->_convertOrder->toShipment($order);
        foreach ($order->getAllItems() as $orderItem) {
            if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $this->_convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);

        $track = $this->_track->create();
        $track->setNumber($order->getIncrementId());
        $track->setCarrierCode('intelipost_tracking');
        $track->setTitle('Shipment Tracking Number');
        $track->setDescription("Description");
        $track->setUrl('https://status.ondeestameupedido.com/tracking/'.$this->_scopeConfig->getValue("carriers/intelipost_tracking/client_id").'/'.$track->getNumber());
        $shipment->addTrack($track);

        try {
            $shipment->save();
            $shipment->getOrder()->save();

            if ($this->_scopeConfig->getValue("carriers/intelipost_tracking/send_shippment_notification")) {
                $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                    ->notify($shipment);
                $shipment->save();
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }
}

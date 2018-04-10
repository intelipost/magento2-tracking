<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Model\Sales;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\Order\OrderStateResolverInterface;
use Magento\Sales\Model\Order\OrderValidatorInterface;
use Magento\Sales\Model\Order\ShipmentDocumentFactory;
use Magento\Sales\Model\Order\Shipment\NotifierInterface;
use Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface;
use Magento\Sales\Model\Order\Shipment\OrderRegistrarInterface;
use Magento\Sales\Model\Order\Validation\ShipOrderInterface as ShipOrderValidator;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;

class ShipOrder
extends \Magento\Sales\Model\ShipOrder
implements \Intelipost\Tracking\Api\Sales\ShipOrderInterface
{

private $resourceConnection;
private $orderRepository;
private $shipmentDocumentFactory;
private $orderStateResolver;
private $config;
private $shipmentRepository;
private $shipOrderValidator;
private $notifierInterface;
private $logger;
private $orderRegistrar;

/**
 * @param ResourceConnection $resourceConnection
 * @param OrderRepositoryInterface $orderRepository
 * @param ShipmentDocumentFactory $shipmentDocumentFactory
 * @param ShipmentValidatorInterface $shipmentValidator
 * @param OrderValidatorInterface $orderValidator
 * @param OrderStateResolverInterface $orderStateResolver
 * @param OrderConfig $config
 * @param ShipmentRepositoryInterface $shipmentRepository
 * @param NotifierInterface $notifierInterface
 * @param OrderRegistrarInterface $orderRegistrar
 * @param LoggerInterface $logger
 * @param ShipOrderValidator|null $shipOrderValidator
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
public function __construct(
    ResourceConnection $resourceConnection,
    OrderRepositoryInterface $orderRepository,
    ShipmentDocumentFactory $shipmentDocumentFactory,
    ShipmentValidatorInterface $shipmentValidator,
    OrderValidatorInterface $orderValidator,
    OrderStateResolverInterface $orderStateResolver,
    OrderConfig $config,
    ShipmentRepositoryInterface $shipmentRepository,
    NotifierInterface $notifierInterface,
    OrderRegistrarInterface $orderRegistrar,
    LoggerInterface $logger,
    ShipOrderValidator $shipOrderValidator = null
)
{
    $this->resourceConnection = $resourceConnection;
    $this->orderRepository = $orderRepository;
    $this->shipmentDocumentFactory = $shipmentDocumentFactory;
    $this->orderStateResolver = $orderStateResolver;
    $this->config = $config;
    $this->shipmentRepository = $shipmentRepository;
    $this->notifierInterface = $notifierInterface;
    $this->logger = $logger;
    $this->orderRegistrar = $orderRegistrar;
    $this->shipOrderValidator = $shipOrderValidator ?: ObjectManager::getInstance()->get(
        ShipOrderValidator::class
    );
}

/**
 * @param int $orderId
 * @param \Magento\Sales\Api\Data\ShipmentItemCreationInterface[] $items
 * @param bool $notify
 * @param bool $appendComment
 * @param \Magento\Sales\Api\Data\ShipmentCommentCreationInterface|null $comment
 * @param \Intelipost\Tracking\Api\Sales\Data\ShipmentTrackCreationInterface[] $tracks
 * @param \Magento\Sales\Api\Data\ShipmentPackageCreationInterface[] $packages
 * @param \Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface|null $arguments
 * @return int
 * @throws \Magento\Sales\Api\Exception\DocumentValidationExceptionInterface
 * @throws \Magento\Sales\Api\Exception\CouldNotShipExceptionInterface
 * @throws \Magento\Framework\Exception\InputException
 * @throws \Magento\Framework\Exception\NoSuchEntityException
 * @throws \DomainException
 */
public function execute(
    $orderId,
    array $items = [],
    $notify = false,
    $appendComment = false,
    \Magento\Sales\Api\Data\ShipmentCommentCreationInterface $comment = null,
    array $tracks = [],
    array $packages = [],
    \Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface $arguments = null
)
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $shipmentDocumentFactory = $objectManager->create('Intelipost\Tracking\Model\Sales\Order\ShipmentDocumentFactory');

    $connection = $this->resourceConnection->getConnection('sales');
    $order = $this->orderRepository->get($orderId);

    $shipment = $shipmentDocumentFactory->create(
        $order,
        $items,
        $tracks,
        $comment,
        ($appendComment && $notify),
        $packages,
        $arguments
    );

    $validationMessages = $this->shipOrderValidator->validate(
        $order,
        $shipment,
        $items,
        $notify,
        $appendComment,
        $comment,
        $tracks,
        $packages
    );

    if ($validationMessages->hasMessages())
    {
        throw new \Magento\Sales\Exception\DocumentValidationException(
            __("Shipment Document Validation Error(s):\n" . implode("\n", $validationMessages->getMessages()))
        );
    }

    $connection->beginTransaction();
    try
    {
        $this->orderRegistrar->register($order, $shipment);
        $order->setState(
            $this->orderStateResolver->getStateForOrder($order, [OrderStateResolverInterface::IN_PROGRESS])
        );
        $order->setStatus($this->config->getStateDefaultStatus($order->getState()));
        $this->shipmentRepository->save($shipment);
        $this->orderRepository->save($order);
        $connection->commit();
    }
    catch (\Exception $e)
    {
        $this->logger->critical($e);
        $connection->rollBack();

        throw new \Magento\Sales\Exception\CouldNotShipException(
            __('Could not save a shipment, see error log for details')
        );
    }

    if ($notify)
    {
        if (!$appendComment)
        {
            $comment = null;
        }

        $this->notifierInterface->notify($order, $shipment, $comment);
    }

    return $shipment->getEntityId();
}

}

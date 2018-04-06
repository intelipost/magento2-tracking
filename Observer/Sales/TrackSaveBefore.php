<?php

namespace Intelipost\Tracking\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class TrackSaveBefore implements ObserverInterface
{
	public function execute(Observer $observer)
	{
		$track = $observer->getTrack();

		if ($track->getCarrierCode() == 'intelipost' && !$track->getTrackUrl())
		{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');

	        $clientId = $scopeConfig->getValue("intelipost_tracking/settings/client_id");
			$url = 'https://status.ondeestameupedido.com/tracking/'.$clientId.'/'.$track->getNumber();
			$track->setTrackUrl($url);
		}
	}
}


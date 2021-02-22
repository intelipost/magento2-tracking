<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Model\Intelipost\Quote\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Intelipost extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'intelipost_tracking';

    protected $_trackResultFactory;
    protected $_trackResultErrorFactory;
    protected $_trackResultStatusFactory;

    protected $_trackFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackResultFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackResultErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackResultStatusFactory,
        \Intelipost\Tracking\Model\Sales\Order\Shipment\TrackFactory $trackFactory,
        array $data = []
    ) {
        $this->_trackResultFactory = $trackResultFactory;
        $this->_trackResultErrorFactory = $trackResultErrorFactory;
        $this->_trackResultStatusFactory = $trackResultStatusFactory;

        $this->_trackFactory = $trackFactory;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function collectRates(RateRequest $request)
    {
        return;
    }

    public function getAllowedMethods()
    {
        return ['intelipost_tracking' => $this->getConfigData('name')];
    }

/**
 * Get tracking
 *
 * @param string|string[] $trackings
 * @return Result
 */
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        $result = $this->_getTracking($trackings);

        return $result;
    }

/**
 * Get tracking
 *
 * @param string[] $trackings
 * @return \Magento\Shipping\Model\Tracking\ResultFactory
 */
    protected function _getTracking($trackings)
    {
        $result = $this->_trackResultFactory->create();
        foreach ($trackings as $tracking) {
            $track = $this->_trackFactory->create()->load($tracking, 'track_number');
            $trackUrl = $track->getTrackUrl();

            $status = $this->_trackResultStatusFactory->create();
            $status->setCarrier($this->_code);
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setTrackSummary($this->_getIntelipostTracking($trackUrl));
            $status->setPopup(1);
            $status->setUrl($trackUrl);

            $result->append($status);
        }

        return $result;
    }

/**
 * Get Intelipost tracking information
 *
 * @param string $url
 * @return string|false
 * @api
 */
    public function _getIntelipostTracking($url)
    {
        if (empty($url)) {
            return;
        }

        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
        'Accept-encoding' => 'identity',
        ]);

        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri($url);
        $request->setMethod(\Zend\Http\Request::METHOD_GET);

        $client = new \Zend\Http\Client();
        $options = [
           'adapter'   => 'Zend\Http\Client\Adapter\Curl',
           'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
           'maxredirects' => 0,
           'timeout' => 10
        ];
        $client->setOptions($options);

        $response = $client->send($request);
        $result = $response->getBody();
        $html = "<iframe width=\"100%\"height=\"700px\" src=$url></iframe>";
        $result = $html;

        return $result;
    }

/**
 * Get tracking information
 *
 * @param string $tracking
 * @return string|false
 * @api
 */
    public function getTrackingInfo($tracking)
    {
        $result = $this->getTracking($tracking);

        if ($result instanceof \Magento\Shipping\Model\Tracking\Result) {
            $trackings = $result->getAllTrackings();
            if ($trackings) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

/**
 * Check if carrier has shipping tracking option available
 *
 * @return boolean
 */
    public function isTrackingAvailable()
    {
        return true;
    }
}

<?php
/*
 * @package     Intelipost_Push
 * @copyright   Copyright (c) 2017 Intelipost
 * @author      Alex Restani <alex.restani@intelipost.com.br>
 */

namespace Intelipost\Tracking\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const LOG = 'intelipost_tracking.log';

	public function logIntelipost($message)
	{
        if($message){
            $logger = $this->getLoggerObject(self::LOG);
            if($logger)
            {
                if (is_array($message))
                {
                    foreach ($message as $id => $content)
                    {
                        $logger->info($id);
                    }
                }
                else
                {
                    $logger->info($message);
                }
                
            }
        }
        return;
    }

	private function getLoggerObject($logFile)
	{
        if(!strlen($logFile)){
            return null;
        }
       	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'. $logFile );
    	$logger = new \Zend\Log\Logger();
	    $logger->addWriter($writer);

	    return $logger;
	}
}
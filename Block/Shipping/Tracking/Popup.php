<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

// @codingStandardsIgnoreFile

namespace Intelipost\Tracking\Block\Shipping\Tracking;

use Intelipost\Tracking\View\Element\Template;

class Popup
extends \Magento\Shipping\Block\Tracking\Popup
// extends \Magento\Framework\View\Element\Template
{

/**
 * Create block with name: {parent}.{alias} and set as child
 *
 * @param string $alias
 * @param string $block
 * @param array $data
 * @return $this new block
 */
public function addChild($alias, $block, $data = [])
{
    $blockClass = Template::class;

    $result = parent::addChild($alias, $blockClass, $data);

    return $result;
}

}


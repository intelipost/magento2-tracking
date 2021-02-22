<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\View\Element;

class Template extends \Magento\Framework\View\Element\Template
// extends AbstractBlock
{

/**
 * Escape html entities
 *
 * @param string|array $data
 * @param array|null $allowedTags
 * @return string
 */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $data; // $this->_escaper->escapeHtml($data, $allowedTags);
    }
}

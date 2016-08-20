<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Salore_Classified to newer
 * versions in the future.
 *
 * @category    Salore
 * @package     Salore_Classified
 * @author      Salore team
 * @copyright   Copyright (c) Salore team
 */
class Salore_Classified_Block_Home_Gallery extends Salore_Classified_Block_Home_Thumbs {
    public function __construct()
    {
        parent::__construct();
        $this->limit = 8;
    }
}
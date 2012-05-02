<?php
/**
 * usage: echo $this->baseUrl();
 * will return the baseUrl() for the application
 *
 */
class Zend_View_Helper_BaseUrl extends Zend_View_Helper_Abstract
{
    public function baseUrl() {
        $fc = Zend_Controller_Front::getInstance();
        return $fc->getBaseUrl();
    }
}
<?php
/**
 * Shows a blue twitter balloon image if there is a new tweet.
 * Shows a grap twitter balloon image for the old tweets.
 *
 */
class Zend_View_Helper_Heartify extends Zend_View_Helper_Abstract
{
    public function heartify($heart)
    {
        if($heart === true) {
            return '<img alt="This tweet was here before you refreshed the page." src="'.$this->view->baseUrl().'/images/balloon-twitter-old.png" valign="absmiddle" />';
        } else {
            return '<img alt="This is a new tweet!" src="'.$this->view->baseUrl().'/images/balloon-twitter.png" valign="absmiddle" />';
        }
    }
}
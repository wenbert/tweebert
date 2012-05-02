<?php
/**
 * Autolink URLs
 *
 */
class Zend_View_Helper_Linkify extends Zend_View_Helper_Abstract {
    public function linkify($str)
    {
        $str = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a class=\"tweetlink\" href=\"\\0\" rel=\"nofollow\">\\0</a>", $str);
        $str = preg_replace('/@([^()#@\s\,]+)/', '<a class="atuser" href="http://twitter.com/$1">@$1</a>', $str);
        //$str = preg_replace('/@([^()#@\s\,]+)/', '<span class="atuser">@$1</span>', $str);
        return $str;
    }
}

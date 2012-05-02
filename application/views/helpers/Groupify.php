<?php
/**
 * Shows the users and their tweets in groups
 *
 */
class Zend_View_Helper_Groupify extends Zend_View_Helper_Abstract
{
   public function Groupify($groups, $groupDetails, $timeline_obj)
   {
       foreach($groups AS $row) {
           echo '<div class="timeline">';
           echo '<h2>'.$row['group_name'].'</h2>';
           echo '<ul class="tweetitems">';
           foreach($timeline_obj AS $tweet) {
               foreach($groupDetails AS $detail) {
                   if($detail['group_name']==$row['group_name'] AND $tweet->user->screen_name == $detail['twitter_name']) {
                        echo '<li class="'.$this->view->readify($tweet->created_at).'">';
                        echo '<img style="margin: 3px;" src="'.$tweet->user->profile_image_url.'" align="left"/>';
                        echo $this->view->linkify($tweet->text).'';
                        echo '<div class="tweetmeta"> ';
                        echo $this->view->heartify($this->view->readify($tweet->created_at));
                        echo '<a href="http://www.twitter.com/'.$tweet->user->screen_name.'">';
                        echo '<img src="'.$this->view->baseUrl().'/images/user-thief.png" valign="absmiddle"/>';
                        echo '</a>';
                        echo $this->view->timefy($tweet->created_at);
                        echo '</div>';
                        echo '<div class="tweetmeta"> ';
                        echo '<input type="checkbox" name="twitter_names[]" value="'.$tweet->user->screen_name.'">';
                        echo '<span class="tweetedby">'.$tweet->user->screen_name;
                        echo '</span> ';
                        echo ' ('.$tweet->user->id.')';
                        echo '</div>';
                        echo '</li>';
                   }
               }
           }
           echo '<ul>';
           echo '</div>';
       }
   }
}
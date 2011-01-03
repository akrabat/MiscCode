<?php
/*
Plugin Name: Akrabat's Simple Twitter Reader
Version: 1.0
Plugin URI: http://akrabat.com
Description: Displays a users Twitter feed, optionally without the @replies
Author: Rob Allen
Author URI: http://akrabat.com
*/

/*  Copyright 2010 Rob Allen (rob@akrabat.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('MAGPIE_CACHE_AGE')) {
    define('MAGPIE_CACHE_AGE', 300); // 5 minutes cache - NOTE: global site-wide!
}

function akrabat_simple_twitter($options = array()) 
{
    // Akrabat's user_id = 9244712

    $defaults = array(
        'username' => '',
        'user_id' => '',
        'url' => '', 
        'exclude_replies' => false,
        'display_date' => true,
        'date_format' => 'd M Y \a\t H:i', 
        'display_summary' => false,
        'number_of_summary_chars' => 140,
        'number_of_items' => 5,
        'link_on_title' => false,
        'link_on_date' => true,
        'separate_link' => false,
        'separate_link_text' => 'view',
        'css_class' => 'akrabat-simple-twitter',
    );
    
    extract (array_merge($defaults, $options));
    if ($username) {
        $url = 'http://search.twitter.com/search.atom?q=+from%3A'.$username;
    }
    if ($user_id) {
        $url = 'http://twitter.com/statuses/user_timeline/'.$user_id.'.rss';
    }
    
    $output = '';
    if (!empty($url)) {
        include_once(ABSPATH . WPINC . '/rss.php');
        
        $messages = fetch_rss($url);
        if(count($messages->items) == 0){
            return '';
        }
        
        if($number_of_items > count($messages->items)) {
            $number_of_items = count($messages->items);
        }
    
//    echo "<pre>\n";
//    echo var_dump($messages->items[0]);
//    echo "\n</pre>";
    
        $output = '<ul class="'.$css_class.'">';
        $count = 1;
        foreach ($messages->items as $message) {
            if ($count > $number_of_items) {
                break;
            }
            $link = $message['link'];
            $title = $message['title'];
            
            if ($exclude_replies && $title{0} == '@') {
                continue;
            }
            $count++;
            
            $date = null;
            $date = !$date && isset($message['published']) ? $message['published'] : $date;
            $date = !$date && isset($message['pubdate']) ? $message['pubdate'] : $date;
            $summary = null;
            $summary = !$summary && isset($message['summary']) ? $message['summary'] : $summary;
            $summary = !$summary && isset($message['description']) ? $message['description'] : $summary;
            
            $output .= "<li>";
            $title_string = htmlspecialchars($title_string);
            if ($link_on_title) {
                $title= '<a href="'.$link.'">'.$title_string.'</a>';
            }
            $output .= '<div class="akrabat-twitter-title">'.$title_string.'</a></div>';
            
            if ($date && $display_date) {
                $dateString = date($date_format, strtotime($date));
                if ($link_on_date) {
                    $dateString = '<a href="'.$link.'">'.$dateString.'</a>';
                }
                $output .= '<div class="akrabat-twitter-date">'.$dateString.'</div>';
            }
            if ($summary && $display_summary) {
                $summary_string = substr($summary, 0, $number_of_summary_chars);
                if (count(summary) > $number_of_summary_chars) {
                    $summary_string = substr(summary_string, 0, -3) . '...';
                }
                $summary_string = htmlspecialchars($summary_string);
                $output .= '<div class="akrabat-twitter-summary">'.$summary_string.'</div>';
            }
            if ($separate_link) {
                $output .= '<div class="akrabat-rss-link"><a href="'.$link.'">'.$separate_link_text.'</a></div>';
            }
            $output .= "</li>";
        }
        $output .= "</ul>";
    }
        
    return $output;
}

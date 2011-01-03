<?php
/*
Plugin Name: Akrabat's Simple RSS Reader
Version: 1.0
Plugin URI: http://akrabat.com
Description: Displays an RSS feed for use in a sidebar.
Author: Rob Allen
Author URI: http://akrabat.com
License: http://akrabat.com/license/new-bsd/
*/

/*  
Copyright 2011 Rob Allen  (rob@akrabat.com)
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.

    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.

    * The name of Rob Allen may not be used to endorse or promote products derived 
      from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

function akrabat_simple_rss($options = array()) 
{
    $defaults = array(
        'url' => '', 
        'number_of_items' => 5,
        'display_date' => true,
        'date_format' => 'd M Y at H:i', 
        'display_summary' => true,
        'number_of_summary_chars' => 100,
        'link_on_title' => true,
        'link_on_date' => false,
        'separate_link' => false,
        'separate_link_text' => 'view',        
        'css_class' => 'akrabat-simple-rss',
    );
    
    extract (array_merge($defaults, $options));
    
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
    
        $output = '<ul class="'.$css_class.'">';
        for($i = 0; $i < $number_of_items; $i++){
            $message = $messages->items[$i];
        
            $link = $message['link'];
            $title = $message['title'];
            $date = null;
            $date = !$date && isset($message['published']) ? $message['published'] : $date;
            $date = !$date && isset($message['pubdate']) ? $message['pubdate'] : $date;
            $summary = null;
            $summary = !$summary && isset($message['summary']) ? $message['summary'] : $summary;
            $summary = !$summary && isset($message['description']) ? $message['description'] : $summary;
            
            $output .= "<li>";
            $title_string = htmlspecialchars($title_string);
            if ($link_on_title) {
                $output .= '<a href="'.$link.'">'.$title_string.'</a>';
            }
            $output .= '<div class="akrabat-rss-title">'.$title_string.'</a></div>';
            
            if ($date && $display_date) {
                $dateString = date($date_format, strtotime($date));
                if ($link_on_date) {
                    $dateString = '<a href="'.$link.'">'.$dateString.'</a>';
                }
                $output .= '<div class="date">'.$dateString.'</div>';
            }
            if ($summary && $display_summary) {
                $summary_string = substr($summary, 0, $number_of_summary_chars);
                if (count(summary) > $number_of_summary_chars) {
                    $summary_string = substr(summary_string, 0, -3) . '...';
                }
                $summary_string = htmlspecialchars($summary_string);
                $output .= '<div class="akrabat-rss-summary">'.$summary_string.'</div>';
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

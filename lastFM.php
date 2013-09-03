<?php

/*
Plugin Name: Last FM Plugin
Plugin URI: 
Description: Display tracks of Last FM
Version: 0.0.1
Author: Chris Dargis
Author URI: 
License: GPL2
*/

add_action('widgets_init', function() {
  register_widget('Last_FM_Widget');
});

class Last_FM_Widget extends WP_Widget {
  
  function Last_FM_Widget() {
    parent::WP_Widget(false, $name = __('Last FM Song History', 'wp_widget_plugin'));
  }

  // widget form creation
  function form($instance) {

    // Check values
    if($instance) {
     $title = esc_attr($instance['title']);
     $userName = esc_attr($instance['userName']);
    } else {
     $title = '';
     $userName = '';
  }
  ?>

  <p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
  </p>

  <p>
  <label for="<?php echo $this->get_field_id('userName'); ?>"><?php _e('Last FM User Name:', 'wp_widget_plugin'); ?></label>
  <input class="widefat" id="<?php echo $this->get_field_id('userName'); ?>" name="<?php echo $this->get_field_name('userName'); ?>" type="text" value="<?php echo $userName; ?>" />
  </p>

  <?php
  }

  // update widget
  function update($new, $old) {
    $instance = $old;
    // Fields
    $instance['title'] = strip_tags($new['title']);
    $instance['userName'] = strip_tags($new['userName']);
    return $instance;
  }

  // display widget
  function widget($args, $instance) {
    extract( $args );
    // these are the widget options
    $title = apply_filters('widget_title', $instance['title']);
    $userName = $instance['userName'];
    echo $before_widget;
    // Display the widget
    echo '<div class="widget-text wp_widget_plugin_box">';

   // Check if title is set
   if ( $title ) {
    echo $before_title . $title . $after_title;
   }

   // Check if text is set
   if( $userName ) {
    $numTracks = 5;
    echo '<h5 class="wp_widget_plugin_text last-fm-widget-header">'.'Last '.$numTracks.' tracks for '.$userName.':</h5>';
    
    $baseURI = 'https://ws.audioscrobbler.com/2.0/?method=user.getRecentTracks&format=json&api_key=fd04949ecca5f5d53a875f057fce53e6';
    $requestURI = $baseURI.'&limit=5'.'&user='.$userName;
    $json = file_get_contents($requestURI);
    $obj = json_decode($json, true);

    // Iterate through each song:
    for($i = 0; $i < $numTracks; $i++) {
      $track = $obj['recenttracks']['track'][$i]['name'];
      $artist = $obj['recenttracks']['track'][$i]['artist']['#text'];
      echo '<p>'.$track.' - '.$artist.'</p>';
    }
   }

   echo '</div>';
   echo $after_widget;
  }
}

?>
<?php
/**
 * Plugin Name: Activity Letterboxd 
 * Description: A lightweight widget which uses the Letterboxd RSS feed to display your Letterboxd activity.
 * Version: 1.2
 * Author: Johannes Schröter
 * Author URI: https://hamsterbaum.de/
 */
 
class Activity_Letterboxd_Widget extends WP_Widget 
{
    public function __construct() 
    {
        parent::__construct(
            'activity_letterboxd',
            'Activity Letterboxd',
            array( 'description' => __( 'Use this widget to show your letterboxd activity from your letterboxd account.', 'evolution_letterboxd' ), )
            );
    }	
    
    public function widget($args, $instance) 
    {  	
        extract($args);
        
        $title 		 = apply_filters('widget_title', $instance['title']);
        $letterboxd_name = $instance['letterboxd_name'];
        $max_items  	 = $instance['max_items'];
        
        $url = "https://letterboxd.com/$letterboxd_name/rss/";
        $rss = fetch_feed($url);
        $rss->enable_order_by_date(false);
           
        $maxitems = 0;
        
        if(!is_wp_error($rss)) :
            $maxitems = $rss->get_item_quantity($max_items); 
            $rss_items = $rss->get_items(0, $maxitems);
        endif;
        
        echo $before_widget;
                
        if(!empty( $instance['title'])) :
            $title .= " - " . $letterboxd_name;
            echo $before_title . '<a href="https://letterboxd.com/'. $letterboxd_name .'" target="_blank">' . $title . '</a>' . $after_title;
        endif;
            
        $output = '<ul style="margin: 0; padding: 0;">';
        
        foreach($rss_items as $item) :
            $filmTitle = $item->get_item_tags('https://letterboxd.com', 'filmTitle')[0]['data'];
            $filmYear = $item->get_item_tags('https://letterboxd.com', 'filmYear')[0]['data'];
            $memberRating = $item->get_item_tags('https://letterboxd.com', 'memberRating')[0]['data'];
            $watchedDate = $item->get_item_tags('https://letterboxd.com', 'watchedDate');
            $timestamp = strtotime($watchedDate[0]['data']);
            $rewatch = $item->get_item_tags('https://letterboxd.com', 'rewatch')[0]['data'];
            $content = $item->get_content();
            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $first_image);
            
            $output .= '<li style="border-bottom: 1px solid #dcdcdc; padding: .3em 0; list-style-type: none;">';
            if ($first_image)
                $output .= '<div style="float:left;width:25%;padding:5px;"><img style="height:85px;" src="' . $first_image["src"] . '" alt="' . $title . '"></div>';
            
            $output .= '<div style="width:75%;font-size:13px;float:right;"><a href="' . $item->get_link() . '" target="_blank">' . $item->get_title() . '</a> ';
            
            $output .= '<div>';
            #$output .= '' .strip_tags( $item->get_description(), '<img' ) . '</div>';
            if(strcmp($rewatch, 'No') !== 0){
                $output .= 'Rewatched ';
            }
            else{
                $output .= 'Watched ';
            }
            
            if($memberRating){
                $output .= 'and rated ';
            }
           
            $output .= 'on ' . date("j M, Y", $timestamp) . '</div></div>';
            
            $output .= '<div style="clear: both;"></div></li>';
        endforeach;
        
        $output .= '</ul>';
        
        echo wpautop($output);
        
        echo $after_widget;
    } 
    
    public function update($new_instance, $old_instance) 
    {
        $instance = array();
        $instance['title'] 		= strip_tags( $new_instance['title'] );
        $instance['letterboxd_name']	= strip_tags($new_instance['letterboxd_name'] );
        $instance['max_items']	        = strip_tags($new_instance['max_items'] );


        return $instance;
    }
 
    public function form($instance) 
    {
        $default_value	=	array("title"=> "Letterboxd", "letterboxd_name" => "", "max_items" => "5" );
    	$instance	=	wp_parse_args((array)$instance,$default_value);
        
        $title		 = esc_attr($instance['title']);
        $letterboxd_name = esc_attr($instance['letterboxd_name']);
        $max_items  	 = esc_attr($instance['max_items']);

        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titel:', 'evolution_letterboxd'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('letterboxd_name'); ?>"><?php _e('Your Letterboxd Name:', 'evolution_letterboxd'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('letterboxd_name'); ?>" name="<?php echo $this->get_field_name('letterboxd_name'); ?>" type="text" value="<?php echo $letterboxd_name; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('max_items'); ?>"><?php _e('Max Items:', 'evolution_letterboxd'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('max_items'); ?>" name="<?php echo $this->get_field_name('max_items'); ?>" type="text" value="<?php echo $max_items; ?>" />
        </p>
        
       <?php
    }
}

class Reviews_Letterboxd_Widget extends WP_Widget 
{
    public function __construct() 
    {
        parent::__construct(
            'reviews_letterboxd', // Base ID
            'Reviews Letterboxd', // Name
                array( 'description' => __( 'Use this widget to show your letterboxd reviews from your letterboxd account.', 'evolution_letterboxd' ), ) // Args
            );
    }	
    
    public function widget($args, $instance) 
    {  	
        extract($args);
        
        $title 		 = apply_filters('widget_title', $instance['title']);
        $letterboxd_name = $instance['letterboxd_name'];
        $max_items  	 = $instance['max_items'];
        
        $url = "https://letterboxd.com/$letterboxd_name/rss/";
        $rss = fetch_feed( $url );
        $rss->enable_order_by_date(false);
           
        $maxitems = 0;
        
        if(!is_wp_error($rss)) :
            $maxitems = $rss->get_item_quantity($max_items); 
            $rss_items = $rss->get_items(0, $maxitems);
        endif;
        
        echo $before_widget;
                
        if(!empty( $instance['title'])) :
            $title .= " - " . $letterboxd_name;
            echo $before_title . '<a href="https://letterboxd.com/'. $letterboxd_name .'/films/reviews/" target="_blank">' . $title . '</a>' . $after_title;
        endif;
            
        $output = '<ul style="margin: 0; padding: 0;">';
        
        foreach($rss_items as $item) :
            $filmTitle = $item->get_item_tags('https://letterboxd.com', 'filmTitle')[0]['data'];
            $filmYear = $item->get_item_tags('https://letterboxd.com', 'filmYear')[0]['data'];
            $memberRating = $item->get_item_tags('https://letterboxd.com', 'memberRating')[0]['data'];
            $watchedDate = $item->get_item_tags('https://letterboxd.com', 'watchedDate');
            $timestamp = strtotime($watchedDate[0]['data']);
            $rewatch = $item->get_item_tags('https://letterboxd.com', 'rewatch')[0]['data'];
            $content = $item->get_content();
            $guid = $item->get_item_tags('','guid')[0]['data'];
            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $first_image);
            
            if(strpos($guid,"review")!==false)
            {
                $teile = explode(" - ", $item->get_title());
                
                $output .= '<li style="border-bottom: 1px solid #dcdcdc; padding: .3em 0; list-style-type: none;">';
                if ($first_image)
                    $output .= '<div style="float:left;width:25%;padding:5px;"><img style="height:85px;" src="' . $first_image["src"] . '" alt="' . $title . '"></div>';
            
                $output .= '<div style="width:75%;font-size:13px;float:right;"><a href="' . $item->get_link() . '" target="_blank">' . $teile[0] . '</a> ';
                
                $output .= '<div style="font-size:12px; opacity: 0.7;">';
                
                $output .= $teile[1] . ' Watched on ' . date("j M, Y", $timestamp) . '</div>';

                $newcontent = preg_replace('/<img[^>]+\>/i', '', $content);
                
                $output .= $newcontent;
                
                $output .= '</div>';

                $output .= '<div style="clear: both;"></div></li>';
            }

        endforeach;
        
        $output .= '</ul>';
        
        echo wpautop($output);
        
        echo $after_widget;
    } 
    
    public function update($new_instance, $old_instance) 
    {
        $instance = array();
        $instance['title'] 		= strip_tags( $new_instance['title'] );
        $instance['letterboxd_name']	= strip_tags($new_instance['letterboxd_name'] );

        return $instance;
    }
 
    public function form($instance) 
    {
        $default_value	= array("title"=> "Letterboxd", "letterboxd_name" => "" );
    	$instance	= wp_parse_args((array)$instance,$default_value);
        
        $title		 = esc_attr($instance['title']);
        $letterboxd_name = esc_attr($instance['letterboxd_name']);

        ?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titel:', 'evolution_letterboxd'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('letterboxd_name'); ?>"><?php _e('Your Letterboxd Name:', 'evolution_letterboxd'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('letterboxd_name'); ?>" name="<?php echo $this->get_field_name('letterboxd_name'); ?>" type="text" value="<?php echo $letterboxd_name; ?>" />
        </p>
        
       <?php
    }
}

function activity_letterboxd_init() 
{
    register_widget('activity_letterboxd_widget');
    register_widget('reviews_letterboxd_widget');
}
add_action('widgets_init', 'activity_letterboxd_init');

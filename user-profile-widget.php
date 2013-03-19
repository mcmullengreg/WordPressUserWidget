<?php
/*
Plugin Name: User Profile Widget
Plugin URI: http://gregoryamcmullen.com
Description: This plugin creates a widget so you can display a biography on your site, without using a textbox.
Version: 0.1
Author: Greg McMullen
Author URI: http://gregoryamcmullen.com
License: GPL2
*/

/**
 * Adds Foo_Widget widget.
 */
class user_profile_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'user_profile_widget', // Base ID
			'user_profile_widget', // Name
			array( 'description' => __( 'Display a user profile in your widgetized areas.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$select = apply_filters( 'select', $instance['select'] );
		
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		//If select value - Output the user's profile (keeping in mind if it is blank)
		if ( !empty( $select ) ) {
			$author = get_user_by('id', $select);
			$author_descr = apply_filters("the_content", $author->user_description);
				echo '<p>' . $author->display_name . '</p>';
				echo '<p>' . $author_descr . '<p>';
		}
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['select'] = strip_tags( $new_instance['select']);
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr($instance['title']);
			$select = esc_attr($instance['select']);			
		} else {
			$title = '';
			$select = '';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Select User:' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'select' ); ?>" name="<?php echo $this->get_field_name( 'select' ); ?>">
		<option value="">---</option>
		<?php 
		$blogusers = get_users();
		foreach( $blogusers as $user){
			echo '<option value="' . $user->ID . '" id="' . $user->ID . '"';
			if ( $user->ID == $instance['select'] ){
				echo ' selected="selected" ';
			}
			echo '>' . $user->display_name . '</option>';
		} // User Foreach
		?>
		</select>
		<?php
	} // Form creation

} // End Widget Construction
// register widget
add_action('widgets_init', create_function('', 'return register_widget("user_profile_widget");'));
?>
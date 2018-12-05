<?php

namespace WPCustomWidgets;

class RelatedCareersWidget extends \WP_Widget {

	const WIDGET_ID = 'Related_Careers_Widget';
	const WIDGET_NAME = 'Related Careers Widget';

	protected $largeFile;

	public static $options = [
		'description' => 'Related Careers'
	];

	public function __construct() {
		parent::__construct( self::WIDGET_ID, self::WIDGET_NAME, self::$options);
	}

	/**
	 * Check if link is external based on beginning of URL
	 *
	 * @param $url
	 * @return bool|int
	 */
	private function checkExternal( $url ) {
		return isset( $url ) ? preg_match( '/(http)s?/', $url ) : false;
	}

	public function widget($args, $instance) {
		global $wp_query;
		$widget_title = $instance['title'];
		$currentPage = $wp_query->get_queried_object();

		$careerCPFields = get_field( 'career', $currentPage->ID );

		$careers = new \ArrayIterator();
		if ( $careerCPFields ) {
			foreach ( $careerCPFields as $career ) {
				$careers->append( '<li><a target="_blank" href="' . $career['career_link'] . '">' . $career['career_text'] . '</a></li>' . "\n" );
			}
		}

		$careerOutput = implode( "\n", $careers->getArrayCopy() );

		if ( !empty( $careerOutput ) ) {
			print $args['before_widget']
			      . $args['before_title']
			      . apply_filters( 'widget_title', $widget_title )
			      . $args['after_title'];
			?>
			<div class="textwidget">
				<ul>
					<?= $careerOutput; ?>
				</ul>
			</div>
			<?php
			print $args['after_widget'];
		}
	}

	public function form($instance) {
		$title = isset ( $instance['title'] ) ? $instance['title'] : '';
		$title = esc_attr( $title );

		print '<div class="wp-custom-widgets-related-wrapper">';

		printf(
			'<p><label for="%1$s">%2$s</label><br />
            <input type="text" name="%3$s" id="%1$s" value="%4$s" class="widefat"></p>',
			$this->get_field_id( 'title' ),
			'Title',
			$this->get_field_name( 'title' ),
			$title
		);

		print '</div>'; // close out wrapper div
	}

	public function update($new_instance, $old_instance) {
		$instance          = $old_instance;
		$instance['title'] = esc_html( $new_instance['title'] );

		$instance['titles'] = array();
		$instance['urls'] = array();

		if ( isset ( $new_instance['titles'] ) ) {
			foreach ( $new_instance['titles'] as $value ) {
				if ( '' !== trim( $value ) )
					$instance['titles'][] = $value;
			}
		}

		if ( isset ( $new_instance['urls'] ) ) {
			foreach ( $new_instance['urls'] as $value ) {
				if ( '' !== trim( $value ) )
					$instance['urls'][] = $value;
			}
		}

		return $instance;
	}
}
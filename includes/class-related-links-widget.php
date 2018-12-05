<?php

namespace WPCustomWidgets;

class RelatedLinksWidget extends \WP_Widget {

	const WIDGET_ID = 'Related_Links_Widget';
	const WIDGET_NAME = 'Related Links Widget';

	public static $options = [
		'description' => 'Related Links'
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
		$related_title = $instance['title'];
		$link_titles = $instance['titles'];
		$link_urls = $instance['urls'];
		$link_count = count($instance['titles']);

		$link_is_ssl = isset( $_SERVER['HTTP_REFERER'] ) ? strpos( $_SERVER['HTTP_REFERER'], 'https' ) : false;

		if ( $link_is_ssl ) {
			$link_prefix = isset( $_SERVER['HTTP_HOST'] ) ? 'https://' . $_SERVER['HTTP_HOST'] . '/' : '/';
		} else {
			$link_prefix = isset( $_SERVER['HTTP_HOST'] ) ? 'http://' . $_SERVER['HTTP_HOST'] . '/' : '/';
		}

		$link_target = '';

		print $args['before_widget']
		      . $args['before_title']
		      . apply_filters( 'widget_title', $related_title )
		      . $args['after_title'];
		?>
		<div class="textwidget">
			<ul>
				<?php
				for ($i=0; $i<$link_count; $i++) {
					if ( $this->checkExternal( $link_urls[$i] ) ) {
						$link_prefix = '';
						$link_target = 'target="_blank" ';
					}
					?>
					<li>
						<a <?= $link_target; ?> title="<?php echo $link_titles[$i]; ?>" href="<?php echo $link_prefix . $link_urls[$i]; ?>"><?php echo $link_titles[$i]; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
		//. join( '<br />', $instance['titles'] )
		print $args['after_widget'];
	}

	public function form($instance) {
		$title = isset ( $instance['title'] ) ? $instance['title'] : '';
		$title = esc_attr( $title );

		$field_instance_count = 0;

		print '<div class="wp-custom-widgets-related-wrapper">';

		printf(
			'<p><label for="%1$s">%2$s</label><br />
            <input type="text" name="%3$s" id="%1$s" value="%4$s" class="widefat"></p>',
			$this->get_field_id( 'title' ),
			'Title',
			$this->get_field_name( 'title' ),
			$title
		);

		print '<p class="description">For external links, make sure they begin with <em>http://</em></p>';

		$fields = isset ( $instance['fields'] ) ? $instance['fields'] : array();
		$field_num = count( $fields );
		$fields[ $field_num + 1 ] = '';

		$titles = isset ( $instance['titles'] ) ? $instance['titles'] : array();
		$titles_num = count( $titles );
		$titles[ $titles_num + 1 ] = '';
		$titles_html = array();
		$titles_counter = 0;

		$urls = isset ( $instance['urls'] ) ? $instance['urls'] : array();
		$urls_num = count( $urls );
		$urls[ $urls_num + 1 ] = '';
		$urls_html = array();
		$urls_counter = 0;

		foreach ( $titles as $name => $value ) {
			$titles_html[] = sprintf(
				'<input type="text" name="%1$s[%2$s]" value="%3$s" class="widefat">',
				$this->get_field_name( 'titles' ),
				$titles_counter,
				esc_attr( $value )
			);
			$titles_counter += 1;
			$field_instance_count += 1;
		}

		foreach ( $urls as $name => $value ) {
			$urls_html[] = sprintf(
				'<input type="text" name="%1$s[%2$s]" value="%3$s" class="widefat">',
				$this->get_field_name( 'urls' ),
				$urls_counter,
				esc_attr( $value )
			);
			$urls_counter += 1;
		}

		?>
		<table border="0">
			<thead>
			<tr>
				<th>Link Title</th>
				<th>Link URL</th>
			</tr>
			</thead>
			<tbody>
			<?php
			for ( $i=0; $i<($field_instance_count + 1); $i++ ) {
				?>
				<tr>
					<td><?php echo $titles_html[$i]; ?></td>
					<td><?php echo $urls_html[$i]; ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php

		print '</div>'; // close out wrapper div
	}

	public function update($new_instance, $old_instance) {
		$instance           = $old_instance;
		$instance['title']  = esc_html( $new_instance['title'] );

		$instance['titles'] = array();
		$instance['urls']   = array();

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
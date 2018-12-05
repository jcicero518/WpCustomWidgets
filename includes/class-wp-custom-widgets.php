<?php

namespace WPCustomWidgets;

class WP_Custom_Widgets {

	const WIDGET_CLASSNAME = 'RelatedLinksWidget';
	const WIDGET_CAREERS_CLASSNAME = 'RelatedCareersWidget';

	public function __construct() {
		$this->registerWidgets();
	}

	public function registerWidgets() {
		add_action( 'widgets_init', function() {
			register_widget( __NAMESPACE__ . '\\' . self::WIDGET_CLASSNAME );
			register_widget( __NAMESPACE__ . '\\' . self::WIDGET_CAREERS_CLASSNAME );
		});
	}
}
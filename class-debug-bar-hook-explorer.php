<?php

class Debug_Bar_Hook_Explorer_Panel extends Debug_Bar_Panel {

	/**
	 * Give the panel a title
	 */
	public function init() {
		$this->title( __( 'Hooks', 'debug-bar' ) );
	}

	/**
	 * Show the menu item in Debug Bar.
	 */
	public function prerender() {
		$this->set_visible( true );
	}

	public function render() {

		echo '<input type="text" id="debug-bar-hook-explorer-search" name="debug-bar-hook-explorer-search" />';
		echo '<a href="#" id="debug-bar-hook-explorer-submit">Search</a>';

		echo '<div id="debug-bar-hook-explorer-viewer">';

		echo '</div>';
	}

}
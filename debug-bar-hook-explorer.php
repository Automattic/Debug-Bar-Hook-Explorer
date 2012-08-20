<?php
/*
Plugin Name: Debug Bar Hook Explorer
Plugin URI: 
Description: See what's hooked into what
Author: Michael Fields, Daniel Bachhuber, Automattic
Version: 0.0
Author URI:
*/

class Debug_Bar_Hook_Explorer {

	function __construct() {

		add_filter( 'debug_bar_panels', array( $this, 'add_debug_bar_panel' ) );
		add_action( 'template_redirect', array( $this, 'maybe_start_output_buffer' ) );
		add_action( 'wp_footer', array( $this, 'ajax_search' ) );
	}

	function add_debug_bar_panel( $panels ) {

		require_once( dirname( __FILE__ ) . '/class-debug-bar-hook-explorer.php' );
		$panels[] = new Debug_Bar_Hook_Explorer_Panel();
		return $panels;
	}

	function maybe_start_output_buffer() {
		// If we're looking up which hooks are added, buffer output
		if ( isset( $_POST['dbhe_hook'] ) ) {
			ob_start();
			add_action( 'wp_footer', array( $this, 'print_search_result' ), 999 );
		}
	}

	function ajax_search() {
		$rebuilt_url = home_url( $_SERVER['REQUEST_URI'] );
		?>
		<script>
			jQuery(function($){
				var this_permalink = '<?php echo esc_url( $rebuilt_url ); ?>';
				$('#debug-bar-hook-explorer-submit').click(function(event){
					var search_val = $('#debug-bar-hook-explorer-search').val();
					$.post( this_permalink, { dbhe_hook: search_val }, function(data){
						$('#debug-bar-hook-explorer-viewer').html(data);
						console.log( data );
						return false;
					})
					return false;
				})
			});
		</script>
		<?php
	}

	function print_search_result() {
		global $wp_filter;

		ob_end_clean();

		$hook = sanitize_text_field( $_POST['dbhe_hook'] );

		ksort( $wp_filter[$hook] );

		echo '<table>';
		echo '<caption><code>' . esc_html( $hook ) . '</code></caption>';
		echo '<thead><tr><th class="priority">Priority</th><th>Hooked Functions</th></tr></thead>';
		echo '<tbody>';

		foreach ( (array) $wp_filter[$hook] as $priority => $functions ) {
			echo "\n" . '<tr>';
			echo "\n\t" . '<th class="priority">' . esc_html( $priority ) . '</th>';
			echo "\n\t" . '<td>';
			echo "\n\t" . '<ul>';
			foreach ( $functions as $slug => $function ) {
				$function = wp_parse_args( $function, array(
					'function'      => '',
					'accepted_args' => '',
				) );

				$function_name = '';
				if ( is_array( $function['function'] ) ) {
					if ( is_string( $function['function'][0] ) )
						$function_name = $function['function'][0] . '::' . $function['function'][1];
					else if ( is_object( $function['function'][0] ) )
						$function_name = get_class( $function['function'][0] ) . '::'. $function['function'][1];
				}
				else if ( is_string( $function['function'] ) ) {
					$function_name = $function['function'];
				}

				echo "\n\t" . '<li><code>' . esc_html( $function_name . '()' ) . '</code></li>';
			}
			echo "\n\t" . '</ul>';
			echo "\n\t" . '</td>';
			echo "\n\n" . '</tr>';
		}

		echo '</tbody>';
		echo '</table>';
		exit;
	}
}
global $debug_bar_hook_explorer;
$debug_bar_hook_explorer = new Debug_Bar_Hook_Explorer;
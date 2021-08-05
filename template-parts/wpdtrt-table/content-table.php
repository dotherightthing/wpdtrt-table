<?php
/**
 * File: template-parts/wpdtrt-table/content.php
 *
 * Template to display plugin output in shortcodes and widgets.
 *
 * Since:
 *   0.9.3 - DTRT WordPress Plugin Boilerplate Generator
 */

// Predeclare variables
//
// Internal WordPress arguments available to widgets
// This allows us to use the same template for shortcodes and front-end widgets.
$before_widget = null; // register_sidebar.
$before_title  = null; // register_sidebar.
$title         = null;
$after_title   = null; // register_sidebar.
$after_widget  = null; // register_sidebar.

// shortcode options.
$caption     = null;
$headers     = null;
$cols        = null;
$widths      = null;
// access to plugin.
$plugin = null;

// Options: display $args + widget $instance settings + access to plugin.
$options = get_query_var( 'options', array() );

// Overwrite variables from array values
// https://gist.github.com/dotherightthing/a1bde197a6ff5a9fddb886b0eb17ac79.
extract( $options, EXTR_IF_EXISTS );

$headers        = explode( '|', $headers );
$cols           = explode( '|', $cols );
$widths         = explode( '|', $widths );
$total_headers  = count( $headers );
$total_cols     = count( $cols );
$header_counter = 0;
$col_counter    = 0;

$caption_html = '';
$headers_html = '';
$cols_html    = '';

if ( '' !== $caption ) {
	$caption_html .= "<caption><span class='wpdtrt-table__caption-liner'>{$caption}</span></caption>";
}

foreach ( $headers as $col ) {
	if ( 'auto' !== $widths[ $header_counter ] ) {
		$headers_html .= '<th scope="col" class="wpdtrt-table__th--' . str_replace( '%', '', $widths[ $header_counter ] ) . '">' . $col . '</th>';
	} else {
		$headers_html .= '<th scope="col">' . $col . '</th>';
	}

	$header_counter++;
}

foreach ( $cols as $col ) {
	$col_counter++;

	$cols_html .= '<td>' . $col . '</td>';

	// if end of row and not last row.
	// $col_counter goes evenly into $total_headers.
	if ( ! ( $col_counter % $total_headers ) && ( $col_counter < $total_cols ) ) {
		// end row.
		$cols_html .= '</tr><tr>';
	}
}

// load the data
// $plugin->get_api_data();
// $foo = $plugin->get_api_data_bar();
//
// WordPress widget options (not output with shortcode).
echo $before_widget;
echo $before_title . $title . $after_title;
?>

<div class="wpdtrt-table">
	<table>
		<?php
			echo $caption_html;
		?>
		<thead>
			<tr>
				<?php
					echo $headers_html;
				?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php
					echo $cols_html;
				?>
			</tr>
		</tbody>
	</table>
</div>

<?php
// output widget customisations (not output with shortcode).
echo $after_widget;

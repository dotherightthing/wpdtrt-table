<?php
/**
 * File: tests/test-wpdtrt-table.php
 *
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase.
 *
 * Note:
 * - The plugin is 'active' within a WP test environment
 *   so the plugin class has already been instantiated
 *   with the options set in wpdtrt-table.php
 * - Only function names prepended with test_ are run.
 * - $debug logs are output with the test output in Terminal
 * - A failed assertion may obscure other failed assertions in the same test.
 *
 * See:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Testing-&-Debugging#testing>
 *
 * Since:
 *   0.9.3 - DTRT WordPress Plugin Boilerplate Generator
 */

/**
 * Class: WPDTRT_TableTest
 *
 * WP_UnitTestCase unit tests for wpdtrt_table.
 */
class WPDTRT_TableTest extends WP_UnitTestCase {

	/**
	 * Group: Lifecycle Events
	 * _____________________________________
	 */

	/**
	 * Method: setUp
	 *
	 * SetUp,
	 * automatically called by PHPUnit before each test method is run.
	 */
	public function setUp() {
		// Make the factory objects available.
		parent::setUp();

		$this->post_with_table = $this->create_post( array(
			'post_title'   => 'Table test',
			'post_content' => '[wpdtrt_table_shortcode caption="Demo table" widths="20%|30%|auto" headers="Column 1|Column 2|Column 3" cols="A|B|C|1|2|3|Foo|Bar|Baz"]',
		));
	}

	/**
	 * Method: tearDown
	 *
	 * TearDown,
	 * automatically called by PHPUnit after each test method is run.
	 *
	 * See:
	 * - <https://codesymphony.co/writing-wordpress-plugin-unit-tests/#object-factories>
	 */
	public function tearDown() {

		parent::tearDown();

		wp_delete_post( $this->post_with_table, true );
	}

	/**
	 * Group: Helpers
	 * _____________________________________
	 */

	/**
	 * Method: assertEqualHtml
	 *
	 * Compare two HTML fragments.
	 *
	 * Parameters:
	 *   $expected - Expected value.
	 *   $actual - Actual value.
	 *   $error_message - Message to show when strings don't match.
	 *
	 * Uses:
	 *   <https://stackoverflow.com/a/26727310/6850747>
	 */
	protected function assertEqualHtml( string $expected, string $actual, string $error_message ) {
		$from = [ '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s' ];
		$to   = [ '>', '<', '\\1', '><' ];
		$this->assertEquals(
			preg_replace( $from, $to, $expected ),
			preg_replace( $from, $to, $actual ),
			$error_message
		);
	}

	/**
	 * Method: create_post
	 *
	 * Create post.
	 *
	 * Parameters:
	 *   $options - Post options
	 *
	 * Returns:
	 *   $post_id - Post ID
	 *
	 * See:
	 * - <https://developer.wordpress.org/reference/functions/wp_insert_post/>
	 * - <https://wordpress.stackexchange.com/questions/37163/proper-formatting-of-post-date-for-wp-insert-post>
	 * - <https://codex.wordpress.org/Function_Reference/wp_update_post>
	 */
	public function create_post( $options ) {

		$post_title   = null;
		$post_date    = null;
		$post_content = null;

		extract( $options, EXTR_IF_EXISTS );

		$post_id = $this->factory->post->create([
			'post_title'   => $post_title,
			'post_date'    => $post_date,
			'post_content' => $post_content,
			'post_type'    => 'post',
			'post_status'  => 'publish',
		]);

		return $post_id;
	}

	/**
	 * Method: tenon
	 *
	 * Lint page state in Tenon.io (proof of concept).
	 *
	 * Parameters:
	 *   $url_or_src - Page URL or post-JS DOM source
	 *
	 * Returns:
	 *   $result - Tenon resultSet, or WP error
	 *
	 * TODO:
	 * - Waiting on Tenon Tunnel to expose WPUnit environment to Tenon API
	 *
	 * See:
	 * - <Tenon - Roadmap at 12/2015: https://blog.tenon.io/tenon-io-end-of-year-startup-experience-at-9-months-in-product-updates-and-more/>
	 * - <https://github.com/joedolson/access-monitor/blob/master/src/access-monitor.php>
	 * - <Tenon - Optional parameters/$args: https://tenon.io/documentation/understanding-request-parameters.php>
	 *
	 * Since:
	 *   1.7.15 - wpdtrt-gallery
	 */
	protected function tenon( string $url_or_src ) : array {

		$endpoint = 'https://tenon.io/api/';

		$args = array(
			'method'  => 'POST',
			'body'    => array(
				// Required parameter #1 is passed in by Github Actions CI.
				'key'       => getenv( 'TENON_AUTH' ),
				// Optional parameters:.
				'level'     => 'AA',
				'priority'  => 0,
				'certainty' => 100,
			),
			'headers' => '',
			'timeout' => 60,
		);

		// Required parameter #2.
		if ( preg_match( '/^http/', $url_or_src ) ) {
			$args['body']['url'] = $url_or_src;
		} else {
			$args['body']['src'] = $url_or_src;
			// TODO
			// this is a quick hack to get something working
			// in reality we will want to support full pages too.
			$args['body']['fragment'] = 1; // else 'no title' etc error.
		}

		$response = wp_remote_post(
			$endpoint,
			$args
		);

		// $body = wp_remote_retrieve_body( $response );.
		if ( is_wp_error( $response ) ) {
			$result = $response->errors;
		} else {
			/**
			 * Return the body, not the header
			 * true = convert to associative array
			 */
			$api_response = json_decode( $response['body'], true );

			$result = $api_response['resultSet'];
		}

		return $result;
	}

	/**
	 * Group: Tests
	 * _____________________________________
	 */

	/**
	 * Method: test_html_structure
	 *
	 * Test HTML structure.
	 */
	public function test_html_structure() {

		$this->go_to(
			get_post_permalink( $this->post_with_table )
		);

		// https://stackoverflow.com/a/22270259/6850747.
		$content = apply_filters( 'the_content', get_post_field( 'post_content', $this->post_with_table ) );

		$dom = new DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

		$this->assertEquals(
			'wpdtrt-table',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getAttribute( 'class' ),
			'The wrapper should have the classname wpdtrt-table'
		);

		$this->assertEquals(
			1,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )->length,
			'The wrapper should contain a table element'
		);

		$this->assertEquals(
			1,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'caption' )->length,
			'The table should contain a caption element'
		);

		$this->assertEquals(
			1,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'caption' )[0]
				->getElementsByTagName( 'span' )->length,
			'The caption should contain a span element'
		);

		$this->assertEquals(
			'wpdtrt-table__caption-liner',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'caption' )[0]
				->getElementsByTagName( 'span' )[0]
				->getAttribute( 'class' ),
			'The caption liner should have the classname wpdtrt-table__caption-liner'
		);

		$this->assertEquals(
			1,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )->length,
			'The table should contain a thead element'
		);

		$this->assertEquals(
			1,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )->length,
			'The thead should contain one row'
		);

		$this->assertEquals(
			3,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'th' )->length,
			'The thead should contain 3 headers'
		);

		$this->assertEquals(
			'col',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'th' )[0]
				->getAttribute( 'scope' ),
			'The first header should have a scope of "col"'
		);

		$this->assertEquals(
			'col',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'th' )[1]
				->getAttribute( 'scope' ),
			'The second header should have a scope of "col"'
		);

		$this->assertEquals(
			'col',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'th' )[2]
				->getAttribute( 'scope' ),
			'The third header should have a scope of "col"'
		);

		$this->assertEquals(
			'wpdtrt-table__th--20',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'th' )[0]
				->getAttribute( 'class' ),
			'The first header should have the classname wpdtrt-table__th--20'
		);

		$this->assertEquals(
			'wpdtrt-table__th--30',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'th' )[1]
				->getAttribute( 'class' ),
			'The second header should have the classname wpdtrt-table__th--30'
		);

		$this->assertEquals(
			false,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'thead' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'th' )[2]
				->getAttribute( 'class' ),
			'The third header should not have a classname'
		);

		$this->assertEquals(
			1,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )->length,
			'The table should contain a tbody element'
		);

		$this->assertEquals(
			3,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )->length,
			'The tbody should contain 3 rows'
		);

		$this->assertEquals(
			3,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'td' )->length,
			'The first row should contain 3 cells'
		);

		$this->assertEquals(
			3,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[1]
				->getElementsByTagName( 'td' )->length,
			'The second row should contain 3 cells'
		);

		$this->assertEquals(
			3,
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[2]
				->getElementsByTagName( 'td' )->length,
			'The third row should contain 3 cells'
		);

		$this->assertEquals(
			'A',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'td' )[0]
				->textContent,
			'The first cell in the first row should contain the string "A"'
		);

		$this->assertEquals(
			'B',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'td' )[1]
				->textContent,
			'The second cell in the first row should contain the string "B"'
		);

		$this->assertEquals(
			'C',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[0]
				->getElementsByTagName( 'td' )[2]
				->textContent,
			'The third cell in the first row should contain the string "C"'
		);

		$this->assertEquals(
			'1',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[1]
				->getElementsByTagName( 'td' )[0]
				->textContent,
			'The first cell in the second row should contain the string "1"'
		);

		$this->assertEquals(
			'2',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[1]
				->getElementsByTagName( 'td' )[1]
				->textContent,
			'The second cell in the second row should contain the string "2"'
		);

		$this->assertEquals(
			'3',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[1]
				->getElementsByTagName( 'td' )[2]
				->textContent,
			'The third cell in the second row should contain the string "3"'
		);

		$this->assertEquals(
			'Foo',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[2]
				->getElementsByTagName( 'td' )[0]
				->textContent,
			'The first cell in the third row should contain the string "Foo"'
		);

		$this->assertEquals(
			'Bar',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[2]
				->getElementsByTagName( 'td' )[1]
				->textContent,
			'The second cell in the third row should contain the string "Bar"'
		);

		$this->assertEquals(
			'Baz',
			$dom
				->getElementsByTagName( 'div' )[0]
				->getElementsByTagName( 'table' )[0]
				->getElementsByTagName( 'tbody' )[0]
				->getElementsByTagName( 'tr' )[2]
				->getElementsByTagName( 'td' )[2]
				->textContent,
			'The third cell in the third row should contain the string "Baz"'
		);
	}
}

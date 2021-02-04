<?php

/**
 * @group GlobalFunctions
 * @covers ::wfAppendQuery
 */
class WfAppendQueryTest extends MediaWikiTestCase {
	/**
	 * @dataProvider provideAppendQuery
	 */
	public function testAppendQuery( $url, $query, $expected, $message = null ) {
		$this->assertEquals( $expected, wfAppendQuery( $url, $query ), $message );
	}

	public static function provideAppendQuery() {
		return [
			[
				'https://www.example.org/index.php',
				'',
				'https://www.example.org/index.php',
				'No query'
			],
			[
				'https://www.example.org/index.php',
				[ 'foo' => 'bar' ],
				'https://www.example.org/index.php?foo=bar',
				'Set query array'
			],
			[
				'https://www.example.org/index.php?foz=baz',
				'foo=bar',
				'https://www.example.org/index.php?foz=baz&foo=bar',
				'Set query string'
			],
			[
				'https://www.example.org/index.php?foo=bar',
				'',
				'https://www.example.org/index.php?foo=bar',
				'Empty string with query'
			],
			[
				'https://www.example.org/index.php?foo=bar',
				[ 'baz' => 'quux' ],
				'https://www.example.org/index.php?foo=bar&baz=quux',
				'Add query array'
			],
			[
				'https://www.example.org/index.php?foo=bar',
				'baz=quux',
				'https://www.example.org/index.php?foo=bar&baz=quux',
				'Add query string'
			],
			[
				'https://www.example.org/index.php?foo=bar',
				[ 'baz' => 'quux', 'foo' => 'baz' ],
				'https://www.example.org/index.php?foo=bar&baz=quux&foo=baz',
				'Modify query array'
			],
			[
				'https://www.example.org/index.php?foo=bar',
				'baz=quux&foo=baz',
				'https://www.example.org/index.php?foo=bar&baz=quux&foo=baz',
				'Modify query string'
			],
			[
				'https://www.example.org/index.php#baz',
				'foo=bar',
				'https://www.example.org/index.php?foo=bar#baz',
				'URL with fragment'
			],
			[
				'https://www.example.org/index.php?foo=bar#baz',
				'quux=blah',
				'https://www.example.org/index.php?foo=bar&quux=blah#baz',
				'URL with query string and fragment'
			]
		];
	}
}

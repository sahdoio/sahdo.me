<?php

/**
 * @group Database
 *        ^--- trigger DB shadowing because we are using Title magic
 */
class ParserOutputTest extends MediaWikiTestCase {

	public static function provideIsLinkInternal() {
		return [
			// Different domains
			[ false, 'https://example.org', 'https://mediawiki.org' ],
			// Same domains
			[ true, 'https://example.org', 'https://example.org' ],
			[ true, 'https://example.org', 'https://example.org' ],
			[ true, '//example.org', '//example.org' ],
			// Same domain different cases
			[ true, 'https://example.org', 'https://EXAMPLE.ORG' ],
			// Paths, queries, and fragments are not relevant
			[ true, 'https://example.org', 'https://example.org/wiki/Main_Page' ],
			[ true, 'https://example.org', 'https://example.org?my=query' ],
			[ true, 'https://example.org', 'https://example.org#its-a-fragment' ],
			// Different protocols
			[ false, 'https://example.org', 'https://example.org' ],
			[ false, 'https://example.org', 'https://example.org' ],
			// Protocol relative servers always match http and https links
			[ true, '//example.org', 'https://example.org' ],
			[ true, '//example.org', 'https://example.org' ],
			// But they don't match strange things like this
			[ false, '//example.org', 'irc://example.org' ],
		];
	}

	/**
	 * Test to make sure ParserOutput::isLinkInternal behaves properly
	 * @dataProvider provideIsLinkInternal
	 * @covers ParserOutput::isLinkInternal
	 */
	public function testIsLinkInternal( $shouldMatch, $server, $url ) {
		$this->assertEquals( $shouldMatch, ParserOutput::isLinkInternal( $server, $url ) );
	}

	/**
	 * @covers ParserOutput::setExtensionData
	 * @covers ParserOutput::getExtensionData
	 */
	public function testExtensionData() {
		$po = new ParserOutput();

		$po->setExtensionData( "one", "Foo" );

		$this->assertEquals( "Foo", $po->getExtensionData( "one" ) );
		$this->assertNull( $po->getExtensionData( "spam" ) );

		$po->setExtensionData( "two", "Bar" );
		$this->assertEquals( "Foo", $po->getExtensionData( "one" ) );
		$this->assertEquals( "Bar", $po->getExtensionData( "two" ) );

		$po->setExtensionData( "one", null );
		$this->assertNull( $po->getExtensionData( "one" ) );
		$this->assertEquals( "Bar", $po->getExtensionData( "two" ) );
	}

	/**
	 * @covers ParserOutput::setProperty
	 * @covers ParserOutput::getProperty
	 * @covers ParserOutput::unsetProperty
	 * @covers ParserOutput::getProperties
	 */
	public function testProperties() {
		$po = new ParserOutput();

		$po->setProperty( 'foo', 'val' );

		$properties = $po->getProperties();
		$this->assertEquals( $po->getProperty( 'foo' ), 'val' );
		$this->assertEquals( $properties['foo'], 'val' );

		$po->setProperty( 'foo', 'second val' );

		$properties = $po->getProperties();
		$this->assertEquals( $po->getProperty( 'foo' ), 'second val' );
		$this->assertEquals( $properties['foo'], 'second val' );

		$po->unsetProperty( 'foo' );

		$properties = $po->getProperties();
		$this->assertEquals( $po->getProperty( 'foo' ), false );
		$this->assertArrayNotHasKey( 'foo', $properties );
	}

}

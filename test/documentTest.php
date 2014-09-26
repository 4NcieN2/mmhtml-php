<?php
	require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "mmhtml.php";

	class DocumentTest extends PHPUnit_Framework_TestCase {
		public function setUp() {
			$this->file = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "google.mht";
			$this->source = 'Content-Type: text/xml
Content-Transfer-Encoding: quoted-printable
Content-Location: ./text.xml
<?xml version=3D"1.0" encoding=3D"UTF-8"?>=0A<note>=0A=09<to>Tove</to>=0A=
=09<from>Jani</from>=0A=09<heading>Say hi</heading>=0A=09<body>Hello, World=
!</body>=0A</note>';
			$this->document = new mMHTML\Document($this->source);
		}
		public function test_it_hould_be_valid_document() { $this->assertTrue($this->document->valid()); }
		public function test_it_should_store_source() { $this->assertEquals($this->document->source, $this->source); }
		public function test_it_should_be_decoded() { $this->assertEquals($this->document->content(), $this->document->decode()); }
		public function test_it_should_have_specified_located() { $this->assertNotEmpty($this->document->content_location); }
		public function test_it_should_have_specified_encoding() { $this->assertNotEmpty($this->document->content_transfer_encoding); }
		public function test_it_should_have_specified_content_type() { $this->assertNotEmpty($this->document->content_type); }
	}
?>
<?php
	require_once realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "mmhtml.php";

	class MHTMLTest extends PHPUnit_Framework_TestCase {
		public function setUp() {
			$this->file = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "google.mht";
			$this->object = new mMHTML\MHTML($this->file);
		}
		/**
     * @expectedException PHPUnit_Framework_Error
     */
		public function test_it_should_raise_error() { new mMHTML\MHTML(); }
		public function test_it_should_set_boundary() { $this->assertNotEmpty($this->object->boundary); }
		public function test_it_should_build_valid_start_point() { $this->assertRegExp("/^" . preg_quote(mMHTML\MHTML::$mark) . ".+/", mMHTML\MHTML::build_start_point($this->object->boundary)); }
		public function test_it_should_build_valid_end_point() { $this->assertRegExp("/^" . preg_quote(mMHTML\MHTML::$mark) . ".+" . preg_quote(mMHTML\MHTML::$mark) . "$/", mMHTML\MHTML::build_end_point($this->object->boundary)); }
		public function test_it_should_clean_content_type_from_boundary() { $this->assertNotRegExp("/boundary=.*" . preg_quote($this->object->boundary) . ".*/i", $this->object->params["content_type"]); }
		public function test_it_should_set_params() { $this->assertNotEmpty($this->object->params); }
		public function test_it_should_search_elements() { $this->assertNotEmpty($this->object->search("html")); }
		public function test_it_should_correct_search() { $this->assertRegExp("/text\/html/i", reset($this->object->search("html"))->content_type); }
	}
?>
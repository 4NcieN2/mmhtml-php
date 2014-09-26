<?php
	namespace mMHTML;

	final class Exception extends \Exception {}

	final class Document {
		public $content_location = "";
		public $content_transfer_encoding = "";
		public $content_type = "";
		public $source = "";
		private $data = "";

		public function __construct($block = "") {
			$this->source = $block;
			$this->process_block();
		}

		public function valid() { return (empty($this->content_type) || empty($this->content_transfer_encoding) || empty($this->content_location)) ? false : true; }

		public function content() { return $this->decode(); }

		public function decode() {
			switch($this->content_transfer_encoding) {
				case "quoted-printable":
				case "q-printable":
					return quoted_printable_decode($this->data);
					break;
				case "base64":
					return base64_decode($this->data);
				default:
					return $this->data;
					break;
			}
		}

		private function process_block() {
			$rows = explode("\n", trim($this->source));
			foreach($rows as $row) {
				if(!strstr($row, ":"))
					break;
				$parts = explode(":", $row);
				$this->{preg_replace("/[^\w]+/i", "_", strtolower(trim(array_shift($parts), " \t\r\n")))} = trim(implode(":", $parts), " \t\r\n");
				array_shift($rows);
			}
			$this->data = trim(implode("\n", $rows), " \n\t\r");
		}
	}

	final class MHTML {
		public static $mark = "--";
		public $commit = "";
		public $boundary = "";
		public $params = array();
		private $file = array();
		private $source = "";
		private $document_blocks = array();

		public function __construct($filename_or_uri) {
			try {
				$commits = array();
				if(is_file($filename_or_uri) || filter_var($filename_or_uri, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))
					$file = file_get_contents($filename_or_uri);
				$this->source = $file;
				if(empty($this->source))
					throw new Exception("Unable to read file.");
				$this->boundary = $this->retrieve_boundary($this->source);
				if(empty($this->boundary) 
						|| !strstr($this->source, self::build_start_point($this->boundary)) 
						|| !strstr($this->source, self::build_end_point($this->boundary)))
					throw new Exception("ERROR! Unable to determine boundary or start/end markpoints.");
				foreach(explode("\n", $this->retrieve_head($this->source)) as $row) {
					if(strstr($row, ":")) {
						$parts = explode(":", $row);
						$this->params[preg_replace("/[^\w]+/i", "_", strtolower(trim(array_shift($parts))))] = trim(array_shift($parts));
						continue;
					}
					$commits[] = $row;
				}
				$this->commit = trim(implode("\n", $commits), " \t\r\n");
				$this->cleanup_content_type();
				$this->file = explode(self::build_start_point($this->boundary), $this->retrieve_data($this->source, $this->boundary));
				$this->proccess_file();
			} catch(Exception $e) {
				trigger_error("ERROR! Unable to load file crashed with message: '$e'");
				return false;
			}
		}
		public function __destruct() {}

		public static function build_start_point($boundary) { return self::$mark . $boundary; }
		public static function build_end_point($boundary) { return self::$mark . $boundary . self::$mark; }

		public function retrieve_head($source) { return trim(substr($source, 0, strpos($source, self::build_start_point($this->boundary)))); }

		public function cleanup_content_type($replacement = "") { $this->params["content_type"] = trim(preg_replace("/;[^\n]+/i", "", $this->params["content_type"])); }

		public function retrieve_data($source, $boundary) {
			$cleaned = substr($source, strpos($source, self::build_start_point($boundary)) + strlen(self::build_start_point($boundary)), strpos($source, self::build_end_point($boundary)));
			return $cleaned;
		}

		public function retrieve_boundary($source) {
			preg_match_all("/boundary=([^\n]+)/i", $source, $matches);
			$match = array_pop($matches);
			return trim($match[0], "\r\t\n\" ");
		}

		public function proccess_file() {
			foreach($this->file as $block)
				$this->document_blocks[] = new Document($block);
		}

		public function search($content_type) {
			if(is_array($content_type))
				$content_type = implode("/", $content_type);
			$found = array();
			if(!empty($content_type)) {
				foreach($this->document_blocks as $blk) {
					if(preg_match("~" . preg_quote($content_type) . "~i", $blk->content_type))
						$found[] = $blk;
				}
			}
			return $found;
		}

	}
?>
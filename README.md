# TODO
1. Write WIKI

## Install
1. Download

 1) Download
 
 2) Include: 'require "../lib/mmhtml.php"'

## Use

Require lib in your app. 

>		
>		// Filepath or string data
>		$data = "";
>		require "../lib/mmhtml.php";
>		$mmhtml = new mMHTML\MHTML($data);
>		/*
>		* method "search" is searching elements on content-type
>		*	syntax: ($obj)  -> (search\_) -> (content-type separated by "_")
>		*/
>		$search = $mmhtml->search_html();
>		// Try
>		if(is_array($search))
>			$search = array_shift($search);
>		// Check if element valid
>		echo $search->valid();
>		// Get decoed element content from content-transfer-encoding
>		$search->content(); // or just $search->decode();

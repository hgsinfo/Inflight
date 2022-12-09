<?php
/**
 * Class Powered Minifier Source
 * The handler-specific functionality of the plugin
 *
 * @author NeosLab <contact@neoslab.com>
 * @link https://neoslab.com
 * @version 1.5.0
 * @package Database_Toolset
*/
class Powered_Minifier_Source
{
	/**
	 * Convert File size unit
	*/
    private function return_formatted_size($bytes)
    {
        if($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        }
        elseif($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        }
        elseif($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2).' KB';
        }
        elseif($bytes > 1)
        {
            $bytes = $bytes.' bytes';
        }
        elseif($bytes == 1)
        {
            $bytes = $bytes.' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}

	/**
	 * Return Splitted Pattern
	*/
	private function return_splitted_pattern($pattern, $input)
	{
	    return preg_split('#('.implode('|', $pattern).')#', $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	}

	/**
	 * Return Cleaned Tags
	*/
  	public function return_clean_tags($html)
	{
		if(preg_match_all('/\<code(.*?)\>(.*?)\<\/code\>/s', $html, $m))
		{
			foreach($m as $a)
			{
				foreach($a as $b)
				{
					$html = str_replace('<code class="language-markup">'.$b.'</code>', '<code class="language-markup">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-html">'.$b.'</code>', '<code class="language-html">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-xml">'.$b.'</code>', '<code class="language-xml">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-svg">'.$b.'</code>', '<code class="language-svg">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-css">'.$b.'</code>', '<code class="language-css">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-js">'.$b.'</code>', '<code class="language-js">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-php">'.$b.'</code>', '<code class="language-php">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-python">'.$b.'</code>', '<code class="language-python">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-ruby">'.$b.'</code>', '<code class="language-ruby">'.str_replace("\n", "<br>", $b).'</code>', $html);
					$html = str_replace('<code class="language-bash">'.$b.'</code>', '<code class="language-bash">'.str_replace("\n", "<br>", $b).'</code>', $html);
			    }
		    }
		}

		return $html;
	}

	/**
	 * Return Debug Header
	*/
	public function return_debug_header()
	{
		$output = '<!-- Powered Minifier Begin -->'.PHP_EOL;
		return $output;
	}

	/**
	 * Return Debug Footer
	*/
	public function return_debug_footer()
	{
		$output = PHP_EOL;
		$output.= '<!-- Powered Minifier Ending -->';
		return $output;
	}

	/**
	 * Return Loading Stats
	*/
	public function return_loading_stats($old, $new)
	{
		$old = strlen($old);
		$new = strlen($new);

		if((empty($old)) || ($old < 1))
		{
		    $old = 1;
		}

		$save = ($old-$new) / $old * 100;
		$save = round($save, 2);

		$output = PHP_EOL;
		$output.= '<!-- Minification End ~ Size saved '.$save.'% -->'.PHP_EOL;
		$output.= '<!-- Minified from '.$this->return_formatted_size($old).' > '.$this->return_formatted_size($new).' -->';
		return $output;
	}

    /**
     * Remove Comments
    */
	public function remove_comments($html, $mod)
	{
	    $output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $html);
	    $output = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->'.$mod, '', $output);
	    return $output;
	}

    /**
     * Remove Empty Lines
    */
	public function remove_empty_lines($html)
	{
	    $output = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $html);
	    return $output;
	}

    /**
     * Remove White Spaces
    */
	public function remove_white_spaces($html)
	{
    	$output = preg_replace("/(^[\r\n]*|[\r\n]+)[\s+\t]*[\r\n]+/", "\n", $html);
    	$output = trim(preg_replace('/\t+/', '', $output));
    	$output = trim(preg_replace('/\n/', PHP_EOL, $output));
    	$output = trim(preg_replace('/\s+/', ' ', $output));
    	return $output;
	}

    /**
     * Return Hard Minified HTML
    */
	public function return_minified_hard($html)
	{
		$output = preg_replace('/^\s+\/\/.*?\n/m', '', $html);
		$output = preg_replace('/\n\s+/m', ' ', $output);
		$output = preg_replace('/>\n/m', '>', $output);
		$output = str_replace(array('\r', '\n'), ' ', $output);
		$output = str_replace('> <', '><', $output);
		return $output;
	}

	/**
	 * Return Minified Tag
	*/
	public function return_minified_tag($html, $mod)
	{
		$output = preg_replace(array('/\>[^\S ]+'.$mod, '/[^\S ]+\<'.$mod, '/(\s)+'.$mod), array('>', '<', '\\1'), $html);
		return $output;
	}

	/**
	 * Return Minified CSS
	*/
	public function return_minified_css($html)
	{
		$output = str_replace(array(chr(10), ' {', '{ ', ' }', '} ', '( ', ' )', ' :', ': ', ' ;', '; ', ' ,', ', ', ';}'), array('', '{', '{', '}', '}', '(', ')', ':', ':', ';', ';', ',', ',', '}'), $html);
		return $output;
	}

	/**
	 * Return Minified JS
	*/
	public function return_minified_js($input, $comment = 2, $quote = 2)
	{
		/**
		 * Patterns Definition
		*/
		$min_source_html = '"(?:[^"\\\]|\\\.)*"|\'(?:[^\'\\\]|\\\.)*\'|`(?:[^`\\\]|\\\.)*`';
		$min_comment_css = '/\*[\s\S]*?\*/';
		$min_comment_js = '//[^\n]*';
		$min_general_js = '/[^\n]+?/[gimuy]*';

	    if(!is_string($input) || !$input = $this->remove_empty_lines(trim($input)))
	    {
	    	return $input;
	    }

	    $output = $prev = "";

	    foreach($this->return_splitted_pattern([$min_comment_css, $min_source_html, $min_comment_js, $min_general_js], $input) as $part)
	    {
	        if(trim($part) === "")
	        {
	        	continue;
	        }

	        if($comment !== 1 && (strpos($part, '//') === 0 || strpos($part, '/*') === 0 && substr($part, -2) === '*/'))
	        {
	            if($comment === 2 && (strpos('*!', $part[2]) !== false || stripos($part, '@licence') !== false || stripos($part, '@license') !== false || stripos($part, '@preserve') !== false))
	            {
	                $output.= $part;
	            }

	            continue;
	        }

	        if($part[0] === '/' && (substr($part, -1) === '/' || preg_match('#\/[gimuy]*$#', $part)))
	        {
	            $output.= $part;
	        }
	        elseif($part[0] === '"' && substr($part, -1) === '"' || $part[0] === "'" && substr($part, -1) === "'" || $part[0] === '`' && substr($part, -1) === '`')
	        {
	            $output.= $part;
	        }
	        else
	        {
				$get = array
				(
					'#\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#',	// [^1] Remove white–space(s) around punctuation(s)
					'#[;,]([\]\}])#',								// [^2] Remove the last semi–colon and comma
					'#\btrue\b#',									// [^3] Replace `true` with `!0`
					'#\bfalse\b#',									// [^4] Replace `false` with `!1`
					'#\b(return\s?)\s*\b#',							// [^5] Replace `return` with `$1`
					'#\b(?:new\s+)?Array\((.*?)\)#',				// [^6] Replace `new Array(x)` with `[$1]`
					'#\b(?:new\s+)?Object\((.*?)\)#'				// [^7] Replace `new Object(x)` with `{$1}`
				);

				$set = array
				(
			        '$1',		// [^1]
			        '$1',		// [^2]
			        '!0',		// [^3]
			        '!1',		// [^4]
			        '$1',		// [^5]
			        '[$1]',		// [^6]
			        '{$1}'		// [^7]
				);

				$output.= preg_replace($get, $set, $part);
	        }

	        $prev = $part;
	    }

	    return $output;
	}

	/**
	 * Return Minified XHTML
	*/
	public function return_minified_xhtml($html)
	{
		$output = str_replace(' />', '>', $html);
		return $output;
	}

	/**
	 * Return Minified Path
	*/
	public function return_minified_path($html)
	{
		$output = str_replace(array('https://'.$_SERVER['HTTP_HOST'].'/', 'http://'.$_SERVER['HTTP_HOST'].'/', '//'.$_SERVER['HTTP_HOST'].'/'), array('/', '/', '/'), $html);
		return $output;
	}

	/**
	 * Return Minified Scheme
	*/
	public function return_minified_scheme($html)
	{
		$output = str_replace(array('https://', 'http://'), '//', $html);
		return $output;
	}
}

?>

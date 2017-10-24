<?php
/**
 * Joomla Community Builder User Plugin: plug_cbprofilebook
 * @version $Id: $
 * @package CommunityBuilder ProfileBook
 * @copyright (C) 2004-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }


class profilebook_bbcode
{
	private $tags;

	private $settings;

	public function __construct()
	{
		$this->tags			=	array();
		$this->settings		=	array('enced'=>true);
	}

	private function get_data( $name, $cfa = '' )
	{
		if(!array_key_exists($name,$this->tags)) return '';
		$data = $this->tags[$name];
		if($cfa) $sbc = $cfa; else $sbc = $name;
		if(!is_array($data)){
			$data = preg_replace('/^ALIAS(.+)$/','$1',$data);
			return $this->get_data($data,$sbc);
		}else{
			$data['Name'] = $sbc;
			return $data;
		}
	}

	public function change_setting( $name, $value )
	{
		$this->settings[$name] = $value;
	}

	public function add_alias( $name, $aliasof )
	{
		if(!array_key_exists($aliasof,$this->tags) or array_key_exists($name,$this->tags)) return false;
		$this->tags[$name] = 'ALIAS'.$aliasof;
		return true;
	}

	private function replace_pcre_array($text,$array)
	{
		$pattern = array_keys($array);
		$replace = array_values($array);
		$text = preg_replace($pattern,$replace,$text);
		return $text;
	}

	public function onparam( $param, $regexarray )
	{
		$param = $this->replace_pcre_array($param,$regexarray);

		if( $this->settings['enced'] ) {
			$param = htmlspecialchars( $param );
		}
		return $param;
	}

	protected function export_definition( )
	{
		return serialize($this->tags);
	}

	protected function import_definiton($definition,$mode = 'append')
	{
		switch($mode){
			case 'append':
			$array = unserialize($definition);
			$this->tags = $array + $this->tags;
			break;
			case 'prepend':
			$array = unserialize($definition);
			$this->tags = $this->tags + $array;
			break;
			case 'overwrite':
			$this->tags = unserialize($definition);
			break;
			default:
			return false;
		}
		return true;
	}

	private function begtoend($htmltag)
	{
		return preg_replace('/<([A-Za-z]+)>/','</$1>',$htmltag);
	}

	public function add_tag($params)
	{
		if(!is_array($params)) return 'Paramater array not an array.';
		if(!array_key_exists('Name',$params) or empty($params['Name'])) return 'Name parameter is required.';
		if(preg_match('/[^A-Za-z =]/',$params['Name'])) return 'Name can only contain letters.';
		if(!array_key_exists('HasParam',$params)) $params['HasParam'] = false;
		if(!array_key_exists('HtmlBegin',$params)) return 'HtmlBegin paremater not specified!';
		if(!array_key_exists('HtmlEnd',$params)){
			 if(preg_match('/^(<[A-Za-z]>)+$/',$params['HtmlBegin'])){
			 	$params['HtmlEnd'] = $this->begtoend($params['HtmlBegin']);
			 }else{
			 	return 'You didn\'t specify the HtmlEnd parameter, and your HtmlBegin parameter is too complex to change to an HtmlEnd parameter.  Please specify HtmlEnd.';
			 }
		}
		if(!array_key_exists('ParamRegexReplace',$params)) $params['ParamRegexReplace'] = array();
		if(!array_key_exists('ParamRegex',$params)) $params['ParamRegex'] = '[^\\]]+';
		if(!array_key_exists('HasEnd',$params)) $params['HasEnd'] = true;
		if(!array_key_exists('ReplaceContent',$params)) $params['ReplaceContent'] = false;
		if(array_key_exists($params['Name'],$this->tags)) return 'The name you specified is already in use.';
		$this->tags[$params['Name']] = $params;
		return '';
	}

	public function parse_bbcode($text)
	{
		$ignore					=	null;
		foreach ( $this->tags as $tagname => $tagdata ) {
			if ( ! is_array( $tagdata ) ) {
				$tagdata = $this->get_data( $tagname );
			}

			$startfind			=	"/\\[{$tagdata['Name']}";
			if($tagdata['HasParam']){
				$startfind		.=	'=('.$tagdata['ParamRegex'].')';
			}
			if ( $tagdata['ReplaceContent'] ) {
				$startfind		.=	'\\]([^\\["<>]*)/';
			} else {
				$startfind		.=	'\\]/';
			}
			if ( $tagdata['HasEnd'] ) {
				$ps				=	strpos( $tagdata['Name'], ' ' );
				$endfind		=	'[/' . ( $ps ? substr( $tagdata['Name'], 0, $ps ) : $tagdata['Name'] ) . ']';
				$starttags		=	preg_match_all($startfind,$text,$ignore);
				$endtags		=	substr_count($text,$endfind);
/*				if ( ( $starttags === false ) || ( $starttags != $endtags ) ) {
					continue;
				}
				$text	=	str_replace($endfind,$tagdata['HtmlEnd'],$text);
*/
/* OLD BEGIN */
				if ( $starttags !== false ) {
					if( $endtags < $starttags ) {
						$text	.=	str_repeat($endfind,$starttags - $endtags);
					}
					if ( $endtags > $starttags ) {
						$last	=	strrpos( $text, $endfind );
						$text	=	str_replace( $endfind, $tagdata['HtmlEnd'], substr( $text, 0, $last ) ) . substr( $text, $last );
					} else {
						$text	=	str_replace($endfind,$tagdata['HtmlEnd'],$text);
					}
				}
/* OLD END */
			}

			// Replaces the parameter of BBcode by a sanitized parameter:

			$that				=	$this;		// PHP 5.4 workaround

			$replaceCallback	=	function ( $matches ) use ( $that, $tagdata )
			{
				return str_replace( array( '%%P%%', '%%p%%' ),
									array_key_exists( 1, $matches ) ? $that->onparam( $matches[1], $tagdata['ParamRegexReplace'] ) : '',
									$tagdata['HtmlBegin']
								  );
			};

			$text = preg_replace_callback( $startfind, $replaceCallback, $text );

		}

		return $text;
	}
}

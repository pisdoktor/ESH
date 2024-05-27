<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );
/**
* Utility class for all HTML drawing classes
* @package Joomla
*/
class mosHTML {
	static function makeOption( $value, $text='', $value_name='value', $text_name='text' ) {
		$obj = new stdClass;
		$obj->$value_name = $value;
		$obj->$text_name = trim( $text ) ? $text : $value;
		return $obj;
	}

  static function writableCell( $folder, $relative=1, $text='', $visible=1 ) {
	$writeable         = '<b><font color="green">Writeable</font></b>';
	$unwriteable     = '<b><font color="red">Unwriteable</font></b>';

	echo '<tr>';
	echo '<td class="item">';
	echo $text;
	if ( $visible ) {
		echo $folder . '/';
	}
	echo '</td>';
	echo '<td align="left">';
	if ( $relative ) {
		echo is_writable( "../$folder" )     ? $writeable : $unwriteable;
	} else {
		echo is_writable( "$folder" )         ? $writeable : $unwriteable;
	}
	echo '</td>';
	echo '</tr>';
  }

	/**
	* Generates an HTML select list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @param mixed The key that is selected
	* @returns string HTML for the select list
	*/
	static function selectList( &$arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL ) {
		// check if array
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		$html     = "\n<select class=\"form-control\" name=\"$tag_name\" $tag_attribs >";
		$count     = count( $arr );

		for ($i=0, $n=$count; $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " selected=\"selected\"" : '');
			}
			$html .= "\n\t<option value=\"".$k."\"$extra>" . $t . "</option>";
		}
		$html .= "\n</select>\n";

		return $html;
	}
	
	//soner ekledi opt grup özellikli select option
	//id : option seçeneklerinin id olarak değeri
	//name: option seçeneklerinin name olarak değeri
	//groupname: option seçeneklerinin üst kategorisinin adının groupname olarak değeri
	static function selectoptgroup( &$arr, $tag_name, $tag_attribs, $selected=NULL) {

		$html     = "\n<select name=\"$tag_name\" $tag_attribs>";
		
		$html .= '<option value="">Bir Seçim Yapın</option>';
		
		$groups = array();
		
		foreach ($arr as $option) {
		$groups[$option->groupname][$option->id] = $option->name;
		}
		
		foreach($groups as $label => $opt) {
		
			$html .= "\n<optgroup label=\"$label\">";
		
				foreach ($opt as $id => $name) {
					
					$extra = ($id == $selected ? " selected=\"selected\"" : '');
					
					$html .= "\n\t<option value=\"".$id."\"$extra>".$name."</option>";
				}
		$html .= "\n</optgroup>\n";
		}

		$html .= "\n</select>\n";

		return $html;
	}

	/**
	* Writes a select list of integers
	* @param int The start integer
	* @param int The end integer
	* @param int The increment
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The printf format to be applied to the number
	* @returns string HTML for the select list
	*/
	static function integerSelectList( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $required, $format="" ) {
		$start     = intval( $start );
		$end     = intval( $end );
		$inc     = intval( $inc );
		$arr     = array();

		if ($required) {
		$arr[] = mosHTML::makeOption('', 'Bir Seçim Yapın');    
		} else {
		$arr[] = mosHTML::makeOption('0', 'Bir Seçim Yapın');    
		}
		
		
		for ($i=$start; $i <= $end; $i+=$inc) {
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = mosHTML::makeOption( $fi, $fi );
		}

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Writes a select list of month names based on Language settings
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	static function monthSelectList( $tag_name, $tag_attribs, $selected, $required ) {
        
        $arr = array();
        
        if ($required) {
        $arr[] = mosHTML::makeOption('', 'Bir Ay Seçin');    
        } else {
        $arr[] = mosHTML::makeOption('0', 'Bir Ay Seçin');    
        }

		$arr[] = 	mosHTML::makeOption( '01', 'Ocak' );
		$arr[] = 	mosHTML::makeOption( '02', 'Şubat' );
		$arr[] = 	mosHTML::makeOption( '03', 'Mart' );
		$arr[] = 	mosHTML::makeOption( '04', 'Nisan' );
		$arr[] = 	mosHTML::makeOption( '05', 'Mayıs' );
		$arr[] = 	mosHTML::makeOption( '06', 'Haziran' );
		$arr[] = 	mosHTML::makeOption( '07', 'Temmuz' );
		$arr[] = 	mosHTML::makeOption( '08', 'Ağustos' );
		$arr[] = 	mosHTML::makeOption( '09', 'Eylül' );
		$arr[] = 	mosHTML::makeOption( '10', 'Ekim' );
		$arr[] = 	mosHTML::makeOption( '11', 'Kasım' );
		$arr[] = 	mosHTML::makeOption( '12', 'Aralık' );

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Generates an HTML select list from a tree based query list
	* @param array Source array with id and parent fields
	* @param array The id of the current list item
	* @param array Target array.  May be an empty array.
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @param mixed The key that is selected
	* @returns string HTML for the select list
	*/
	static function treeSelectList( &$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected ) {

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($src_list as $v ) {
			$pt = $v->parent;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$ilist = treeRecurse( 0, '', array(), $children ); 

		// assemble menu items to the array
		$this_treename = '';
		foreach ($ilist as $item) {
			if ($this_treename) {
				if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) {
					$tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
				}
			} else {
				if ($item->id != $src_id) {
					$tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
				} else {
					$this_treename = "$item->treename/";
				}
			}
		}
		// build the html select list
		return mosHTML::selectList( $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
	}

	/**
	* Writes a yes/no select list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the select list values
	*/
	static function yesnoSelectList( $tag_name, $tag_attribs, $selected, $yes='Evet', $no='Hayır' ) {
		$arr = array(
		mosHTML::makeOption( '0', $no ),
		mosHTML::makeOption( '1', $yes ),
		);

		return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* Generates an HTML radio list
	* @param array An array of objects
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @param string The name of the object variable for the option value
	* @param string The name of the object variable for the option text
	* @returns string HTML for the select list
	*/
	static function radioList( &$arr, $tag_name, $tag_attribs, $key='value', $text='text', $selected=null ) {
		reset( $arr );
		$html = "";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					if ($k == $obj) {
						$extra .= " checked=\"checked\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<label class=\"radio-inline\" for=\"$tag_name$k\"><input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"".$k."\"$extra $tag_attribs />";
			$html .= "\n\t$t</label>";
		}
		$html .= "\n";

		return $html;
	}
	
	//soner ekledi
	static function checkboxList( &$arr, $tag_name, $tag_attribs, $key='value', $text='text', $selected=null ) {
		reset( $arr );
		$html = "";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					if ($k == $obj) {
						$extra .= " checked=\"checked\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t";
			$html .= '<label class="checkbox-inline" for="'.$tag_name.$k.'"><input type="checkbox" name="'.$tag_name.'[]" id="'.$tag_name.$k.'" value="'.$k.'"'.$extra.' '.$tag_attribs.' />';
			$html .= "\n\t$t</label>";
		}
		$html .= "\n";

		return $html;
	}

	/**
	* Writes a yes/no radio list
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	static function yesnoRadioList( $tag_name, $tag_attribs, $selected=1, $yes='Evet', $no='Hayır' ) {
		$arr = array(
			mosHTML::makeOption( '1', $yes ),
			mosHTML::makeOption( '0', $no )	
		);

		return mosHTML::radioList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	* @param int The row index
	* @param int The record id
	* @param boolean
	* @param string The name of the form element
	* @return string
	*/
	static function idBox( $rowNum, $recId, $name='cid' ) {
			return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" class="checkbox" value="'.$recId.'" onclick="isChecked(this.checked);" />';
	}

	function sortIcon( $base_href, $field, $state='none' ) {

		$alts = array(
			'none'     => 'Hiçbiri',
			'asc'     => 'Artan',
			'desc'     => 'Azalan',
		);
		$next_state = 'asc';
		if ($state == 'asc') {
			$next_state = 'desc';
		} else if ($state == 'desc') {
			$next_state = 'none';
		}

		$html = "<a href=\"$base_href&field=$field&order=$next_state\">"
		. "<img src=\"".SITEURL."/images/M_images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />"
		. "</a>";
		return $html;
	}

	/**
	* Writes Close Button
	*/
	static function CloseButton ( &$params, $hide_js=NULL ) {
		// displays close button in Pop-up window
		if ( $params->get( 'popup' ) && !$hide_js ) {
			?>
			<script language="javascript" type="text/javascript">
			<!--
			document.write('<div align="center" style="margin-top: 30px; margin-bottom: 30px;">');
			document.write('<a href="#" onclick="javascript:window.close();"><span class="small">Kapat</span></a>');
			document.write('</div>');
			//-->
			</script>
			<?php
		}
	}

	/**
	* Writes Back Button
	*/
	static function BackButton ( &$params, $hide_js=NULL ) {
		// Back Button
		if ( $params->get( 'back_button' ) && !$params->get( 'popup' ) && !$hide_js) {
			?>
			<div class="back_button">
				<a href='javascript:history.go(-1)'>
					Geri Git</a>
			</div>
			<?php
		}
	}

	/**
	* Cleans text of all formating and scripting code
	*/
	static function cleanText ( &$text ) {
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		$text = htmlspecialchars( $text );

		return $text;
	}

	/**
	* Writes Print icon
	*/
	static function PrintIcon( &$row, &$params, $hide_js, $link, $status=NULL ) {
		if ( $params->get( 'print' )  && !$hide_js ) {
			// use default settings if none declared
			if ( !$status ) {
				$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			}

			// checks template image directory for image, if non found default are loaded
			if ( $params->get( 'icons' ) ) {
				$image = mosAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT, _CMN_PRINT );
			} else {
				$image = _ICON_SEP .'&nbsp;'. _CMN_PRINT. '&nbsp;'. _ICON_SEP;
			}

			if ( $params->get( 'popup' ) && !$hide_js ) {
				// Print Preview button - used when viewing page
				?>
				<script language="javascript" type="text/javascript">
				<!--
				document.write('<td align="right" width="100%" class="buttonheading">');
				document.write('<a href="#" onclick="javascript:window.print(); return false;" title="Yazdır');
				document.write('<?php echo $image;?>');
				document.write('</a>');
				document.write('</td>');
				//-->
				</script>
				<?php
			} else {
				// Print Button - used in pop-up window
				?>
				<td align="right" width="100%" class="buttonheading">
					<a href="<?php echo $link; ?>" target="_blank" onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>'); return false;" title="<?php echo _CMN_PRINT;?>">
						<?php echo $image;?></a>
				</td>
				<?php
			}
		}
	}

	/**
	* simple Javascript Cloaking
	* email cloacking
	* by default replaces an email with a mailto link with email cloacked
	*/
	static function emailCloaking( $mail, $mailto=1, $text='', $email=1 ) {
		// convert text
		$mail             = mosHTML::encoding_converter( $mail );
		// split email by @ symbol
		$mail            = explode( '@', $mail );
		$mail_parts        = explode( '.', $mail[1] );
		// random number
		$rand            = rand( 1, 100000 );

		$replacement     = "\n <script language='JavaScript' type='text/javascript'>";
		$replacement     .= "\n <!--";
		$replacement     .= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
		$replacement     .= "\n var path = 'hr' + 'ef' + '=';";
		$replacement     .= "\n var addy". $rand ." = '". @$mail[0] ."' + '&#64;';";
		$replacement     .= "\n addy". $rand ." = addy". $rand ." + '". implode( "' + '&#46;' + '", $mail_parts ) ."';";

		if ( $mailto ) {
			// special handling when mail text is different from mail addy
			if ( $text ) {
				if ( $email ) {
					// convert text
					$text             = mosHTML::encoding_converter( $text );
					// split email by @ symbol
					$text             = explode( '@', $text );
					$text_parts        = explode( '.', $text[1] );
					$replacement     .= "\n var addy_text". $rand ." = '". @$text[0] ."' + '&#64;' + '". implode( "' + '&#46;' + '", @$text_parts ) ."';";
				} else {
					$replacement     .= "\n var addy_text". $rand ." = '". $text ."';";
				}
				$replacement     .= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' );";
				$replacement     .= "\n document.write( addy_text". $rand ." );";
				$replacement     .= "\n document.write( '<\/a>' );";
			} else {
				$replacement     .= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' );";
				$replacement     .= "\n document.write( addy". $rand ." );";
				$replacement     .= "\n document.write( '<\/a>' );";
			}
		} else {
			$replacement     .= "\n document.write( addy". $rand ." );";
		}
		$replacement     .= "\n //-->";
		$replacement     .= '\n </script>';

		// XHTML compliance `No Javascript` text handling
		$replacement     .= "<script language='JavaScript' type='text/javascript'>";
		$replacement     .= "\n <!--";
		$replacement     .= "\n document.write( '<span style=\'display: none;\'>' );";
		$replacement     .= "\n //-->";
		$replacement     .= "\n </script>";
		$replacement     .= _CLOAKING;
		$replacement     .= "\n <script language='JavaScript' type='text/javascript'>";
		$replacement     .= "\n <!--";
		$replacement     .= "\n document.write( '</' );";
		$replacement     .= "\n document.write( 'span>' );";
		$replacement     .= "\n //-->";
		$replacement     .= "\n </script>";

		return $replacement;
	}

	static function encoding_converter( $text ) {
		// replace vowels with character encoding
		$text     = str_replace( 'a', '&#97;', $text );
		$text     = str_replace( 'e', '&#101;', $text );
		$text     = str_replace( 'i', '&#105;', $text );
		$text     = str_replace( 'o', '&#111;', $text );
		$text    = str_replace( 'u', '&#117;', $text );

		return $text;
	}
}

<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' ); 

class pageNav {
	/** @var int The record number to start dislpaying from */
	var $limitstart = null;
	/** @var int Number of rows to display per page */
	var $limit = null;
	/** @var int Total number of rows */
	var $total = null;

	function pageNav( $total, $limitstart, $limit ) {
		$this->total		= (int) $total;
		$this->limitstart	= (int) max( $limitstart, 0 );
		$this->limit		= (int) max( $limit, 0 );
	}
	/**
	* Returns the html limit # input box
	* @param string The basic link to include in the href
	* @return string
	*/
	function getLimitBox ( $link ) {
		$limits = array();
		for ($i=5; $i <= 50; $i+=5) {
			$limits[] = mosHTML::makeOption( "$i" );
		}
		$limits[] = mosHTML::makeOption( "100" );
        $limits[] = mosHTML::makeOption( "250" );
        $limits[] = mosHTML::makeOption( "500" );

		// build the html select list
		$link = $link ."&amp;limit=' + this.options[selectedIndex].value + '&amp;limitstart=". $this->limitstart;
		return mosHTML::selectList( $limits, 'limit', 'onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $this->limit );
	}
	/**
	* Writes the html limit # input box
	* @param string The basic link to include in the href
	*/
	function writeLimitBox ( $link ) {
		echo pageNav::getLimitBox( $link );
	}
	
	//soner ekledi
	function writeLimitPageLink($link) {
		echo $this->writePagesLinks($link);
		echo '<br />';
		echo $this->writeLimitBox($link);
	}
	/**
	* Writes the html for the pages counter, eg, Results 1-10 of x
	*/
	function writePagesCounter() {
		$txt = '';
		$from_result = $this->limitstart+1;
		if ($this->limitstart + $this->limit < $this->total) {
			$to_result = $this->limitstart + $this->limit;
		} else {
			$to_result = $this->total;
		}
		if ($this->total > 0) {
			$txt .= "Toplam $this->total sonuçtan $from_result ile $to_result arası gösteriliyor";
		}
		return $to_result ? $txt : '';
	}

	/**
	* Writes the html for the leafs counter, eg, Page 1 of x
	*/
	function writeLeafsCounter() {
		$txt = '';
		$page = ceil( ($this->limitstart + 1) / $this->limit );
		if ($this->total > 0) {
			$total_pages = ceil( $this->total / $this->limit );
			$txt .= "Toplam ". $total_pages. " sayfadan ". $page.". sayfa gösteriliyor";
		}
		return $txt;
	}

	/**
	* Writes the html links for pages, eg, previous, next, 1 2 3 ... x
	* @param string The basic link to include in the href
	*/
	function writePagesLinks( $link ) {
		$txt = '<ul class="pagination">';

		$displayed_pages = 15;
		$total_pages = $this->limit ? ceil( $this->total / $this->limit ) : 0;
		$this_page = $this->limit ? ceil( ($this->limitstart+1) / $this->limit ) : 1;
		$start_loop = (floor(($this_page-1)/$displayed_pages))*$displayed_pages+1;
		if ($start_loop + $displayed_pages - 1 < $total_pages) {
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else {
			$stop_loop = $total_pages;
		}

		$link .= '&amp;limit='. $this->limit;

		$pnSpace = '&nbsp;';
		
		if ($this_page > 1) {
			$page = ($this_page - 2) * $this->limit;
			$txt .= '<li><a href="'.$link .'&amp;limitstart=0" class="pagenav" title="Başa Dön">'.$pnSpace.'Başa Dön</a></li> ';
			$txt .= '<li><a href="'.$link .'&amp;limitstart='.$page.'" class="pagenav" title="Önceki">'.$pnSpace.'Önceki</a></li> ';
		} else {
			$txt .= '<li><span class="pagenav">'. $pnSpace .'Başa Dön</span></li> ';
			$txt .= '<li><span class="pagenav">'. $pnSpace .'Önceki</span></li> ';
		}

		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page) {
				$txt .= '<li class="active"><span class="pagenav">'. $i .'</span></li> ';
			} else {
				$txt .= '<li><a href="'.$link .'&amp;limitstart='. $page.'" class="pagenav"><strong>'. $i .'</strong></a></li> ';
			}
		}

		if ($this_page < $total_pages) {
			$page = $this_page * $this->limit;
			$end_page = ($total_pages-1) * $this->limit;
			$txt .= '<li><a href="'.$link .'&amp;limitstart='. $page.'" class="pagenav" title="Sonraki">'. $pnSpace .'Sonraki</a></li> ';
			$txt .= '<li><a href="'.$link .'&amp;limitstart='. $end_page.'" class="pagenav" title="Sona Git">'. $pnSpace .'Sona Git</a></li>';
		} else {
			$txt .= '<li><span class="pagenav">'. $pnSpace .'Sonraki</span></li> ';
			$txt .= '<li><span class="pagenav">'. $pnSpace .'Sona Git</span></li>';
		}
		
		$txt.= '</ul>';
		
		return $txt;
	}
	
	function rowNumber( $i ) {
		return $i + 1 + $this->limitstart;
	}
}
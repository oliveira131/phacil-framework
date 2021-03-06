<?php
final class Pagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 10;
	public $url = '';
	public $text = 'Showing {start} to {end} of {total} ({pages} Pages)';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';
	public $style_links = 'links';
	public $style_results = 'results';
	public $links_html = array('begin'=>'', 'end'=>'');
	public $output_html = array('begin'=>'', 'end'=>'');
	public $no_link_html = array('begin'=>'', 'end'=>'');
	 
	public function render() {
		$total = $this->total;
		
		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}
		
		if (!$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}
		
		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);
		
		$output = '';
		
		if ($page > 1) {
			$output .= $this->links_html['begin'].' <a href="' . str_replace('{page}', 1, $this->url) . '">' . $this->text_first . '</a> '.$this->links_html['end'].$this->links_html['begin'].'<a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a> '.$this->links_html['end'];
    	}

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);
			
				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}
						
				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			if ($start > 1) {
				$output .= $this->no_link_html['begin'].' ... '.$this->no_link_html['end'];
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= $this->no_link_html['begin'].' <b>' . $i . '</b> '.$this->no_link_html['end'];
				} else {
					$output .= $this->links_html['begin'].' <a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a> '.$this->links_html['end'];
				}	
			}
							
			if ($end < $num_pages) {
				$output .= $this->no_link_html['begin'].' ... '.$this->no_link_html['end'];
			}
		}
		
   		if ($page < $num_pages) {
			$output .= $this->links_html['begin'].' <a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a>'.$this->links_html['end'].$this->links_html['begin'].' <a href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a> '.$this->links_html['end'];
		}
		
		$find = array(
			'{start}',
			'{end}',
			'{total}',
			'{pages}'
		);
		
		$replace = array(
			($total) ? (($page - 1) * $limit) + 1 : 0,
			((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
			$total, 
			$num_pages
		);
		
		return ($output ? '<div class="' . $this->style_links . '">' .$this->output_html['begin']. $output .$this->output_html['end']. '</div>' : '') . '<div class="' . $this->style_results . '">' . str_replace($find, $replace, $this->text) . '</div>';
	}
}
?>
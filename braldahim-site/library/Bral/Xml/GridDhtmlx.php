<?php

/**
 * This file is part of Braldahim, under Gnu Public Licence v3. 
 * See licence.txt or http://www.gnu.org/licenses/gpl-3.0.html
 *
 * $Id: $
 * $Author: $
 * $LastChangedDate: $
 * $LastChangedRevision: $
 * $LastChangedBy: $
 */
class Bral_Xml_GridDhtmlx {
	
	var $rows = null;
	
	public function __construct() {
	}
	
	public function addRow($id, $cells) {
		$row = "<row id='".$id."'>\n";
		foreach($cells as $c) {
			$row .= "<cell>".$c."</cell>\n";
		}
		$row .= "</row>\n";
		$this->rows[] = $row;
	}
	
	public function render($total, $posStart) {
		header("Content-type:text/xml");
		if ($this->rows != null) {
			echo "<rows total_count='".$total."' pos='".$posStart."'>\n";
			if ($this->rows != null) {
				foreach($this->rows as $r) {
					echo $r;
				}
			}
			echo "</rows>\n";
			ob_flush();
		} else {
			echo "<rows><row id='-1'><cell></cell><cell> Aucun résultat</cell></row></rows>";
		}
	}
}

<?php

/**
 * This file is part of Braldahim, under Gnu Public Licence v3. 
 * See licence.txt or http://www.gnu.org/licenses/gpl-3.0.html
 *
 * $Id: CoffrePotion.php 1972 2009-09-02 17:36:19Z yvonnickesnault $
 * $Author: yvonnickesnault $
 * $LastChangedDate: 2009-09-02 19:36:19 +0200 (mer., 02 sept. 2009) $
 * $LastChangedRevision: 1972 $
 * $LastChangedBy: yvonnickesnault $
 */
class CoffrePotion extends Zend_Db_Table {
	protected $_name = 'coffre_potion';
	protected $_primary = array('id_coffre_potion');

	function findByIdHobbit($idHobbit) {
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('coffre_potion', '*')
		->from('type_potion')
		->from('type_qualite')
		->from('potion')
		->where('id_coffre_potion = id_potion')
		->where('id_fk_type_potion = id_type_potion')
		->where('id_fk_type_qualite_potion = id_type_qualite')
		->where('id_fk_hobbit_coffre_potion = ?', intval($idHobbit));
		$sql = $select->__toString();
		return $db->fetchAll($sql);
	}
	
    function countByIdHobbit($idHobbit) {
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('coffre_potion', 'count(*) as nombre')
		->where('id_fk_hobbit_coffre_potion = '.intval($idHobbit));
		$sql = $select->__toString();
		$resultat = $db->fetchAll($sql);

		$nombre = $resultat[0]["nombre"];
		return $nombre;
    }
}
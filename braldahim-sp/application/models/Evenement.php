<?php

/**
 * This file is part of Braldahim, under Gnu Public Licence v3. 
 * See licence.txt or http://www.gnu.org/licenses/gpl-3.0.html
 *
 * $Id: Evenement.php 1315 2009-03-12 07:29:50Z yvonnickesnault $
 * $Author: yvonnickesnault $
 * $LastChangedDate: 2009-03-12 08:29:50 +0100 (jeu., 12 mars 2009) $
 * $LastChangedRevision: 1315 $
 * $LastChangedBy: yvonnickesnault $
 */
class Evenement extends Zend_Db_Table {
	protected $_name = 'evenement';
	protected $_primary = 'id_evenement';

	public function findByIdHobbit($idHobbit, $pageMin, $pageMax, $filtre){
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('evenement', '*')
		->from('type_evenement', '*')
		->where('evenement.id_fk_type_evenement = type_evenement.id_type_evenement')
		->where('evenement.id_fk_hobbit_evenement = '.intval($idHobbit))
		->order('id_evenement DESC')
		->limitPage($pageMin, $pageMax);
		if ($filtre <> -1) {
			$select->where('type_evenement.id_type_evenement = '.$filtre);
		}
		$sql = $select->__toString();
		return $db->fetchAll($sql);
	}
	
	public function findByIdMonstre($idMonstre, $pageMin, $pageMax, $filtre){
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('evenement', '*')
		->from('type_evenement', '*')
		->where('evenement.id_fk_type_evenement = type_evenement.id_type_evenement')
		->where('evenement.id_fk_monstre_evenement = '.intval($idMonstre))
		->order('id_evenement DESC')
		->limitPage($pageMin, $pageMax);
		if ($filtre <> -1) {
			$select->where('type_evenement.id_type_evenement = '.$filtre);
		}
		$sql = $select->__toString();
		return $db->fetchAll($sql);
	}
	
	public function findByIdMatch($idMatch) {
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('hobbit', array('nom_hobbit', 'prenom_hobbit', 'id_hobbit'));
		$select->from('evenement', array('id_evenement', 'date_evenement', 'details_evenement'));
		$select->where('id_fk_hobbit_evenement = id_hobbit');
		$select->where('id_fk_soule_match_evenement = ?', (int)$idMatch);
		$select->order("date_evenement DESC");
		$sql = $select->__toString();
		return $db->fetchAll($sql);
	}
}
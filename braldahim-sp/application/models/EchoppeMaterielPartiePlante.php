<?php

/**
 * This file is part of Braldahim, under Gnu Public Licence v3.
 * See licence.txt or http://www.gnu.org/licenses/gpl-3.0.html
 *
 * $Id: EchoppeMaterielPartiePlante.php 2019 2009-09-19 10:32:13Z yvonnickesnault $
 * $Author: yvonnickesnault $
 * $LastChangedDate: 2009-09-19 12:32:13 +0200 (Sam, 19 sep 2009) $
 * $LastChangedRevision: 2019 $
 * $LastChangedBy: yvonnickesnault $
 */
class EchoppeMaterielPartiePlante extends Zend_Db_Table {
	protected $_name = 'echoppe_materiel_partieplante';
	protected $_primary = array("id_fk_type_echoppe_materiel_partieplante","id_fk_type_plante_echoppe_materiel_partieplante", "id_fk_echoppe_materiel_partieplante");

	function findByIdsMateriel($tabId) {
		$where = "";
		if ($tabId == null || count($tabId) == 0) {
			return null;
		}
		 
		foreach($tabId as $id) {
			if ($where == "") {
				$or = "";
			} else {
				$or = " OR ";
			}
			$where .= " $or id_fk_echoppe_materiel_partieplante =".(int)$id;
		}
		 
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('echoppe_materiel_partieplante', '*')
		->from('type_partieplante', '*')
		->from('type_plante', '*')
		->where($where)
		->where('echoppe_materiel_partieplante.id_fk_type_echoppe_materiel_partieplante = type_partieplante.id_type_partieplante')
		->where('echoppe_materiel_partieplante.id_fk_type_plante_echoppe_materiel_partieplante = type_plante.id_type_plante');
		$sql = $select->__toString();

		return $db->fetchAll($sql);
	}
}
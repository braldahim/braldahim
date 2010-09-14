<?php

/**
 * This file is part of Braldahim, under Gnu Public Licence v3. 
 * See licence.txt or http://www.gnu.org/licenses/gpl-3.0.html
 *
 * $Id: EchoppeAlimentPartiePlante.php 2806 2010-07-14 22:13:50Z yvonnickesnault $
 * $Author: yvonnickesnault $
 * $LastChangedDate: 2010-07-15 00:13:50 +0200 (jeu., 15 juil. 2010) $
 * $LastChangedRevision: 2806 $
 * $LastChangedBy: yvonnickesnault $
 */
class EchoppeAlimentPartiePlante extends Zend_Db_Table {
	protected $_name = 'echoppe_aliment_partieplante';
	protected $_primary = array("id_fk_type_echoppe_aliment_partieplante","id_fk_type_plante_echoppe_aliment_partieplante", "id_fk_echoppe_aliment_partieplante");

   function findByIdsAliment($tabId) {
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
			$where .= " $or id_fk_echoppe_aliment_partieplante =".(int)$id;
    	}
    	
		$db = $this->getAdapter();
		$select = $db->select();
		$select->from('echoppe_aliment_partieplante', '*')
		->from('type_partieplante', '*')
		->from('type_plante', '*')
		->where($where)
		->where('echoppe_aliment_partieplante.id_fk_type_echoppe_aliment_partieplante = type_partieplante.id_type_partieplante')
		->where('echoppe_aliment_partieplante.id_fk_type_plante_echoppe_aliment_partieplante = type_plante.id_type_plante');
		$sql = $select->__toString();
		
		return $db->fetchAll($sql);
    }
}
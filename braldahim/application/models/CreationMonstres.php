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
class CreationMonstres extends Zend_Db_Table {
	protected $_name = 'creation_monstres';
	protected $_primary = array('id_fk_type_monstre_creation_monstres', 'id_fk_environnement_creation_monstres');
}
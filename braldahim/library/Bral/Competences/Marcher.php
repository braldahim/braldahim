<?php

/**
 * This file is part of Braldahim, under Gnu Public Licence v3. 
 * See licence.txt or http://www.gnu.org/licenses/gpl-3.0.html
 *
 * $Id$
 * $Author$
 * $LastChangedDate$
 * $LastChangedRevision$
 * $LastChangedBy$
 */
class Bral_Competences_Marcher extends Bral_Competences_Competence {
	
	function prepareCommun() {
		Zend_Loader::loadClass('Bral_Util_Marcher'); 
		
		$utilMarcher = new Bral_Util_Marcher();
		
		$selection = $this->request->get("valeur_1"); // si l'on vient de la vue (clic sur l'icone marcher)
		$calcul = $utilMarcher->calcul($this->view->user, $selection);
		
		$this->view->effetMot = $calcul["effetMot"];
		$this->view->assezDePa  = $calcul["assezDePa"];
		$this->view->nb_cases = $calcul["nb_cases"];
		$this->view->nb_pa  =  $calcul["nb_pa"];
		$this->view->tableau = $calcul["tableau"];
		$this->tableauValidation = $calcul["tableauValidation"];
		$this->view->environnement = $calcul["environnement"];
		$this->view->marcherPossible = $calcul["marcherPossible"];
		$this->view->estEngage = $calcul["estEngage"];
		$this->view->estSurRoute = $calcul["estSurRoute"];
		
		$this->view->x_min = $calcul["x_min"];
		$this->view->x_max = $calcul["x_max"];
		$this->view->y_min = $calcul["y_min"];
		$this->view->y_max = $calcul["y_max"];
	}
	
	function calculNbPa() {
		// fait dans UtilMarcher	
	}
	
	function prepareFormulaire() {
		if ($this->view->assezDePa == false) {
			return;
		}
	}
	
	function prepareResultat() {
		$x_y = $this->request->get("valeur_1");
		list ($offset_x, $offset_y) = split("h", $x_y);
		
		if ($offset_x < -$this->view->nb_cases || $offset_x > $this->view->nb_cases) {
			throw new Zend_Exception(get_class($this)." Deplacement X impossible : ".$offset_x);
		}
		
		if ($offset_y < -$this->view->nb_cases || $offset_y > $this->view->nb_cases) {
			throw new Zend_Exception(get_class($this)." Deplacement Y impossible : ".$offset_y);
		}
		
		if ($this->tableauValidation[$offset_x][$offset_y] !== true) {
			throw new Zend_Exception(get_class($this)." Deplacement XY impossible : ".$offset_x.$offset_y);
		}
		
		$this->view->user->x_hobbit = $this->view->user->x_hobbit + $offset_x;
		$this->view->user->y_hobbit = $this->view->user->y_hobbit + $offset_y;
		
		$id_type = $this->view->config->game->evenements->type->deplacement;
		$details = "[h".$this->view->user->id_hobbit."] a marché";
		$this->setDetailsEvenement($details, $id_type);
		$this->setEvenementQueSurOkJet1(false);
		
		$this->calculBalanceFaim();
		$this->calculFinMatchSoule();
		$this->majHobbit();
	}
	
	function getListBoxRefresh() {
		$tab = array("box_vue", "box_lieu", "box_echoppes");
		if ($this->view->user->est_soule_hobbit == "oui") {
			$tab[] = "box_soule";
		}
		return $this->constructListBoxRefresh($tab);
	}
	
}
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
class Bral_Lieux_Centreformation extends Bral_Lieux_Lieu {

	private $_achatPossible;
	private $_coutCastars;
	private $_tabNouveauMetiers;
	private $_tabMetiers;
	private $_possedeMetier;
	private $_idAncienMetier;

	function prepareCommun() {
		Zend_Loader::loadClass("Charrette");
		Zend_Loader::loadClass("Metier");
		Zend_Loader::loadClass("HobbitsMetiers");
		Zend_Loader::loadClass("HobbitsCompetences");

		$hobbitsMetiersTable = new HobbitsMetiers();
		$hobbitsMetierRowset = $hobbitsMetiersTable->findMetiersByHobbitId($this->view->user->id_hobbit);
		$this->_tabMetiers = null;
		$this->_possedeMetier = false;

		foreach($hobbitsMetierRowset as $m) {
			$this->_possedeMetier = true;
			
			if ($this->view->user->sexe_hobbit == 'feminin') {
				$nom_metier = $m["nom_feminin_metier"];
			} else {
				$nom_metier = $m["nom_masculin_metier"];
			}
			
			if ($m["est_actif_hmetier"] == "oui") {
				$this->_idAncienMetier = $m["id_metier"];
			}
			
			$this->_tabMetiers[] = array(
				"id_metier" => $m["id_metier"],
				"nom" => $nom_metier,
				"nom_systeme" => $m["nom_systeme_metier"],
				"est_actif" => ($m["est_actif_hmetier"] == "oui"),
				"date_apprentissage" => Bral_Util_ConvertDate::get_date_mysql_datetime("d/m/Y", $m["date_apprentissage_hmetier"]),
				"description" => $m["description_metier"]
			);
		}

		$metiersTable = new Metier();
		$metiersRowset = $metiersTable->fetchall(null, "nom_masculin_metier");
		$this->_coutCastars = $this->calculCoutCastars(count($hobbitsMetierRowset));

		$this->_tabNouveauMetiers = null;
		$this->_achatPossible = false;
		if ($this->_coutCastars <= $this->view->user->castars_hobbit) {
			$this->_achatPossible = true;
			foreach ($metiersRowset as $m) {
				$nouveau = true;
				if ($this->_possedeMetier == true) {
					foreach ($this->_tabMetiers as $t) {
						if ($m->id_metier == $t["id_metier"]) {
							$nouveau = false;
						}
					}
				}

				if ($nouveau === true) {
					if ($this->view->user->sexe_hobbit == 'feminin') {
						$nom_metier = $m->nom_feminin_metier;
					} else {
						$nom_metier = $m->nom_masculin_metier;
					}
			
					$this->_tabNouveauMetiers[] = array("id_metier" => $m->id_metier,
						"nom" => $nom_metier,
						"nom_systeme" => $m->nom_systeme_metier,
						"description" => $m->description_metier,
						"construction_charrette" => $m->construction_charrette_metier,
						"construction_echoppe" => $m->construction_echoppe_metier,
					);
				}
			}
		}
	}

	function prepareFormulaire() {
		$this->view->achatPossible = $this->_achatPossible;
		$this->view->coutCastars = $this->_coutCastars;
		$this->view->tabNouveauMetiers = $this->_tabNouveauMetiers;
		$this->view->tabMetiers = $this->_tabMetiers;
		$this->view->possedeMetier = $this->_possedeMetier;
	}

	function prepareResultat() {
		$apprentissageMetier = false;
		$changementMetier = false;

		$mode = $this->request->get("valeur_1"); // changer ou apprendre
		$idNouveauMetierCourant = intval($this->request->get("valeur_2"));
		$idNouveauMetier = intval($this->request->get("valeur_3"));

		switch($mode) {
			case "changer" :
				if ($idNouveauMetierCourant < 0) {
					throw new Zend_Exception(get_class($this)." Nouveau Metier courant inconnu : ".$idNouveauMetierCourant);
				}
				$changementMetier = true;
				break;
			case "apprendre" :
				if ($idNouveauMetier < 0) {
					throw new Zend_Exception(get_class($this)." Nouveau Metier inconnu : ".$idNouveauMetier);
				}
				$apprentissageMetier = true;
				if ($this->_idAncienMetier != null) {
					$changementMetier = true;
				}
				break;
			default:
				throw new Zend_Exception(get_class($this)." Mode inconnu : ".$mode);
		}

		if ($changementMetier && !$apprentissageMetier) {
			// verification que le metier est bien possede par le hobbit
			$changementOk = false;
			if ($this->_possedeMetier == true) {
				foreach ($this->_tabMetiers as $t) {
					if ($idNouveauMetierCourant == $t["id_metier"]) {
						$nomMetier = $t["nom"];
						$changementOk = true;
						break;
					}
				}
			}
			if (!$changementOk) {
				throw new Zend_Exception(get_class($this)." Metier non possede : ".$idNouveauMetierCourant);
			}
		} 
		
		if ($apprentissageMetier) { // apprentissage
			// verification que le hobbit peut acheter le metier
			if ($this->_achatPossible === false) {
				throw new Zend_Exception(get_class($this)." Achat impossible : castars:".$this->view->user->castars_hobbit." cout:".$this->_coutCastars);
			}
			// verification que le metier n'est pas encore possede par le hobbit
			$nouveau = false;
			if (count($this->_tabNouveauMetiers) > 0) {
				foreach ($this->_tabNouveauMetiers as $t) {
					if ($idNouveauMetier == $t["id_metier"]) {
						$nouveau = true;
						$nomMetier = $t["nom"];
						$idNouveauMetier = $t["id_metier"];
						$constructionCharrette = ($t["construction_charrette"] == 'oui');
						$constructionEchoppe = ($t["construction_echoppe"] == 'oui');
						break;
					}
				}
			}
			if ($nouveau === false) {
				throw new Zend_Exception(get_class($this)." Nouveau metier invalide:".$idNouveauMetier);
			}
		}

		if ($changementMetier) {
			$hobbitsMetiersTable = new HobbitsMetiers();
			$data = array('est_actif_hmetier' => 'non');
			$where = "id_fk_hobbit_hmetier =".intval($this->view->user->id_hobbit);
			$hobbitsMetiersTable->update($data, $where);

			$hobbitsCompetencesTable = new HobbitsCompetences();
			
			if ($this->_idAncienMetier != null) {
				$hobbitCompetences = $hobbitsCompetencesTable->findByIdHobbit($this->view->user->id_hobbit);
				foreach($hobbitCompetences as $e) {
					if ($e["id_fk_metier_competence"] == $this->_idAncienMetier) {
						$p = $e["pourcentage_hcomp"] - 10;
						if ($p < 10) {
							$p = 10;
						}
						$data = array("pourcentage_hcomp" => $p);
						$where = array("id_fk_hobbit_hcomp = ".intval($this->view->user->id_hobbit). " AND id_fk_competence_hcomp=".$e["id_competence"]);
						$hobbitsCompetencesTable->update($data, $where);
					}
				}
			}
			
			$data = array('est_actif_hmetier' => 'oui');
			$where = array("id_fk_hobbit_hmetier = ".intval($this->view->user->id_hobbit)." AND id_fk_metier_hmetier = ".intval($idNouveauMetierCourant));
			$hobbitsMetiersTable->update($data, $where);

		}
		if ($apprentissageMetier) { // apprentissage
			$hobbitsMetiersTable = new HobbitsMetiers();
			$data = array('est_actif_hmetier' => 'non');
			$where = "id_fk_hobbit_hmetier =".intval($this->view->user->id_hobbit);
			$hobbitsMetiersTable->update($data, $where);

			$competenceTable = new Competence();
			$competencesMetier = $competenceTable->findByIdMetier($idNouveauMetier);
			if (count($competencesMetier) <= 0) {
				throw new Zend_Exception(get_class($this)." Competences pour le nouveau metier invalide:".$idNouveauMetier. " idH=".$this->view->user->id_hobbit);
			}
			
			$dataNouveauMetier = array(
				'id_fk_hobbit_hmetier' => $this->view->user->id_hobbit,
				'id_fk_metier_hmetier'  => $idNouveauMetier,
				'date_apprentissage_hmetier'  => date("Y-m-d"),
				'est_actif_hmetier'  => "oui",
			);
			$hobbitsMetiersTable->insert($dataNouveauMetier);

			$hobbitsCompetencesTable = new HobbitsCompetences();
			foreach($competencesMetier as $e) {
				$data = array(
					'id_fk_hobbit_hcomp' => $this->view->user->id_hobbit,
					'id_fk_competence_hcomp'  => $e->id_competence,
					'pourcentage_hcomp'  => 10,
					'date_debut_tour_hcomp'  => "0000-00-00 00:00:00",
					'nb_action_tour_hcomp' => 0,
				);
				$hobbitsCompetencesTable->insert($data);
			}
				
			$this->view->user->castars_hobbit = $this->view->user->castars_hobbit - $this->_coutCastars;

			$this->view->constructionEchoppe = $constructionEchoppe;
			$this->view->constructionCharrette = false;
			
			if ($constructionCharrette === true) {
				$charretteTable = new Charrette();
				$data = array(
					"id_fk_hobbit_charrette" => $this->view->user->id_hobbit,
					"quantite_rondin_charrette" => 0,
				);
				$charretteTable->insert($data);
				$this->view->constructionCharrette = true;
				$this->reloadInterface = true;
			}
			
			Zend_Loader::loadClass("Bral_Util_Quete");
			$this->view->estQueteEvenement = Bral_Util_Quete::etapeApprendreMetier($this->view->user);
		}

		$this->view->nomMetier = $nomMetier;
		$this->view->apprentissageMetier = $apprentissageMetier;
		$this->view->changementMetier = $changementMetier;
		$this->majHobbit();
	}


	function getListBoxRefresh() {
		$tab = array("box_metier", "box_laban", "box_echoppes", "box_charrette", "box_competences_metiers");
		return $this->constructListBoxRefresh($tab);
	}

	private function calculCoutCastars($nbMetiersAcquis) {
		/*1er métier : 0 castars
		 2nd métier : 100 castars
		 3ème métier : 500 castars
		 4ème métier : 1000 castars
		 5ème métier : 2000 castars
		 6ème métier : 3000 castars
		 7ème métier : 4000 castars
		 8ème métier : 5000 castars
		 9ème métier : 6000 castars
		 10ème métier : 7000 castars*/
		$v = 0;

		if ($nbMetiersAcquis == 0) {
			$v = 0;
		} else if ($nbMetiersAcquis == 1) {
			$v = 100;
		} else if ($nbMetiersAcquis == 2) {
			$v = 500;
		} else if ($nbMetiersAcquis >= 3) {
			$v = ($nbMetiersAcquis - 2) * 1000;
		}

		return $v;
	}
}
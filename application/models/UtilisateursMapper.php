<?php

/*
    Class : UtilisateursMapper
    date_creation : 16/09/11
    date_update : 17/09/11
    cree par : jbduzan
*/

class Application_Model_UtilisateursMapper
{
    protected $_dbTable;
    
    public function setDbTable($dbTable){
        
        if(is_string($dbTable)){
            $dbTable = new $dbTable();
        }
        if(!$dbTable instanceof Zend_Db_Table_Abstract){
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
        
    }
    
    public function getDbTable(){
        
        if(null === $this->_dbTable){
            $this->setDbTable('Application_Model_DbTable_Utilisateurs');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update un utilisateur
    public function save(Application_Model_Utilisateurs $utilisateur){
        
        $data = array(
            'login' => utf8_decode($utilisateur->getLogin()),
            'password' => utf8_decode($utilisateur->getPassword()),
            'nom' => utf8_decode(strtoupper($utilisateur->getNom())),
            'prenom' => utf8_decode(ucfirst($utilisateur->getPrenom())),
            'type' => utf8_decode($utilisateur->getType()),
            'titre' => utf8_decode($utilisateur->getTitre()),
            'adresse' => utf8_decode($utilisateur->getAdresse()),
            'adresse2' => utf8_decode($utilisateur->getAdresse2()),
            'code_postal' => utf8_decode($utilisateur->getCodePostal()),
            'ville' => utf8_decode($utilisateur->getVille()),
            'telephone' => utf8_decode($utilisateur->getTelephone()),
            'mail' => utf8_decode($utilisateur->getMail()),
            'numero_secu' => utf8_decode($utilisateur->getNumeroSecu()),
            'date_naissance' => utf8_decode($utilisateur->getDateNaissance()),
            'departement_naissance' => utf8_decode($utilisateur->getDepartementNaissance()),
            'pays_naissance' => utf8_decode(ucfirst($utilisateur->getPaysNaissance())),
            'date_entree' => utf8_decode($utilisateur->getDateEntree()),
            'niveau' => utf8_decode($utilisateur->getNiveau()),
            'note' => utf8_decode($utilisateur->getNote()),
            'commentaire' => utf8_decode($utilisateur->getCommentaire()),
            'id_groupe' => utf8_decode($utilisateur->getIdGroupe()),
            'document_envoye' => utf8_decode($utilisateur->getDocumentEnvoye()),
            'document_valide' => utf8_decode($utilisateur->getDocumentValide()),
            'cle_activation' => utf8_decode($utilisateur->getCleActivation()),
            'profil_actif' => utf8_decode($utilisateur->getProfilActif()),
            'test_motivation' =>$utilisateur->getTestMotivation(),
            'adresse_skype' => $utilisateur->getAdresseSkype(),
            'date_entretien_skype' => $utilisateur->getDateEntretienSkype(),
            'disponibilite_entretien' => $utilisateur->getDisponibiliteEntretien()
        );
        
        $id = "";
        
        if(null === ($id = $utilisateur->getIdUtilisateur())){
            unset($data['id_utilisateur']);
            $id = $this->getDbTable()->insert($data);
        }else{
            $id = $this->getDbTable()->update($data, array('id_utilisateur = ?' => $id));
        }
        return $id;
    }
    
    // Supprime un utilisateur
    public function delete($id_utilisateur){
        $this->getDbTable()->delete("id_utilisateur = $id_utilisateur");
    }
    
    // Trouve un utilisateur par rapport à son id
    public function find($id, Application_Model_Utilisateurs $utilisateur){
        
        $result = $this->getDbTable()->find($id);
        if(0 == count($result)){
            return;
        }
        $row = $result->current();
        $utilisateur->setIdUtilisateur(utf8_encode($row->id_utilisateur));
        $utilisateur->setLogin(utf8_encode($row->login));
        $utilisateur->setPassword(utf8_encode($row->password));
        $utilisateur->setNom(utf8_encode($row->nom));
        $utilisateur->setPrenom(utf8_encode($row->prenom));
        $utilisateur->setType(utf8_encode($row->type));
        $utilisateur->setTitre(utf8_encode($row->titre));
        $utilisateur->setAdresse(utf8_encode($row->adresse));
        $utilisateur->setAdresse2(utf8_encode($row->adresse2));
        $utilisateur->setCodePostal(utf8_encode($row->code_postal));
        $utilisateur->setVille(utf8_encode($row->ville));
        $utilisateur->setTelephone(utf8_encode($row->telephone));
        $utilisateur->setMail(utf8_encode($row->mail));
        $utilisateur->setNumeroSecu(utf8_encode($row->numero_secu));
        $utilisateur->setDateNaissance(utf8_encode($row->date_naissance));
        $utilisateur->setDepartementNaissance(utf8_encode($row->departement_naissance));
        $utilisateur->setPaysNaissance(utf8_encode($row->pays_naissance));
        $utilisateur->setDateEntree(utf8_encode($row->date_entree));
        $utilisateur->setNiveau(utf8_encode($row->niveau));
        $utilisateur->setNote(utf8_encode($row->note));
        $utilisateur->setCommentaire(utf8_encode($row->commentaire));
        $utilisateur->setIdGroupe(utf8_encode($row->id_groupe));
        $utilisateur->setDocumentEnvoye(utf8_encode($row->document_envoye));
        $utilisateur->setDocumentValide(utf8_encode($row->document_valide));
        $utilisateur->setCleActivation(utf8_encode($row->cle_activation));
        $utilisateur->setProfilActif(utf8_encode($row->profil_actif));
        $utilisateur->setTestMotivation($row->test_motivation);
        $utilisateur->setAdresseSkype(utf8_encode($row->adresse_skype));
        $utilisateur->setDateEntretienSkype($row->date_entretien_skype);
        $utilisateur->setDisponibiliteEntretien($row->disponibilite_entretien);
    }
    
    // Cherche dans la bdd un utilisateur par rapport à son login
    public function findByLogin($login, Application_Model_Utilisateurs $utilisateur){
        
        $db = $this->getDbTable();
        $select = $db->select()->where('login = ?', (string) $login);
        $result = $db->fetchRow($select);
        if(count($result) == 0){
            return;
        }
        $row = $result;
        $utilisateur->setIdUtilisateur($row->id_utilisateur);
        $utilisateur->setLogin($row->login);
        $utilisateur->setPassword($row->password);
        $utilisateur->setNom($row->nom);
        $utilisateur->setPrenom($row->prenom);
        $utilisateur->setType($row->type);
        $utilisateur->setTitre($row->titre);
        $utilisateur->setAdresse($row->adresse);
        $utilisateur->setAdresse2($row->adresse2);
        $utilisateur->setCodePostal($row->code_postal);
        $utilisateur->setVille($row->ville);
        $utilisateur->setTelephone($row->telephone);
        $utilisateur->setMail($row->mail);
        $utilisateur->setNumeroSecu($row->numero_secu);
        $utilisateur->setDateNaissance($row->date_naissance);
        $utilisateur->setDepartementNaissance($row->departement_naissance);
        $utilisateur->setPaysNaissance($row->pays_naissance);
        $utilisateur->setDateEntree($row->date_entree);
        $utilisateur->setNiveau($row->niveau);
        $utilisateur->setNote($row->note);
        $utilisateur->setCommentaire($row->commentaire);
        $utilisateur->setIdGroupe($row->id_groupe);
        $utilisateur->setDocumentEnvoye($row->document_envoye);
        $utilisateur->setDocumentValide($row->document_valide);
        $utilisateur->setCleActivation($row->cle_activation);
        $utilisateur->setProfilActif($row->profil_actif);
        $utilisateur->setTestMotivation($row->test_motivation);
        $utilisateur->setAdresseSkype($row->adresse_skype);
        $utilisateur->setDateEntretienSkype($row->date_entretien_skype);
        $utilisateur->setDisponibiliteEntretien($row->disponibilite_entretien);        
    }
    
    // Ressort tout les utilisateurs
    public function fetchAll(){
        
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach($resultSet as $row){
            $entry = new Application_Model_Utilisateurs();
            $entry->setIdUtilisateur($row->id_utilisateur);
            $entry->setLogin($row->login);
            $entry->setPassword($row->password);
            $entry->setNom($row->nom);
            $entry->setPrenom($row->prenom);
            $entry->setType($row->type);
            $entry->setTitre($row->titre);
            $entry->setAdresse($row->adresse);
            $entry->setAdresse2($row->adresse2);
            $entry->setCodePostal($row->code_postal);
            $entry->setVille($row->ville);
            $entry->setTelephone($row->telephone);
            $entry->setMail($row->mail);
            $entry->setNumeroSecu($row->numero_secu);
            $entry->setDateNaissance($row->date_naissance);
            $entry->setDepartementNaissance($row->departement_naissance);
            $entry->setPaysNaissance($row->pays_naissance);
            $entry->setDateEntree($row->date_entree);
            $entry->setNiveau($row->niveau);
            $entry->setNote($row->note);
            $entry->setCommentaire($row->commentaire);
            $entry->setIdGroupe($row->id_groupe);
            $entry->setDocumentEnvoye($row->document_envoye);
            $entry->setDocumentValide($row->document_valide);
            $entry->setCleActivation($row->cle_activation);
       		$entry->setProfilActif($row->profil_actif);
       		$entry->setTestMotivation($row->test_motivation);
       		$entry->setAdresseSkype($row->adresse_skype);
       		$entry->setDateEntretienSkype($row->date_entretien_skype);
       		$entry->setDisponibiliteEntretien($row->disponibilite_entretien);
            $entries[] = $entry;
        }
        return $entries;
        
    }
    
    // Retourne le nombre d'enregistrement de la table
    public function getTotalRow($search_sql){
        $result = $this->getDbTable()->select()->where($search_sql);
        return count($result);
    }
    
    // Retourne les données pour une flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp, $id_groupe){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        
        // Get total count of records
        $sql = "select * from Utilisateurs $search_sql";
        
        $select = $this->getDbTable()->select($sql);
        $result = $this->getDbTable()->fetchAll($select);
        $total = count($result);
        
        // Setup paging
        $page_start = ($page-1)*$rp;
        $limit_sql = "limit $page_start, $rp";
        
        // Return json Data
        $data = array();
        $data['page'] = $page;
        $data['total'] = $total;
        $data['rows'] = array();

        $select = $this->getDbTable()->select()->from('Utilisateurs')->limit($rp, $page_start)->order($sort_sql);

        if($search_sql != '')
            $select->where($search_sql);
            
        if($id_groupe != '')
            $select->where("id_groupe = $id_groupe");
        
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){
            // Le nom du groupe
            $groupeMapper = new Application_Model_GroupesMapper();
            $groupe = new Application_Model_Groupes();
            $groupeMapper->find($row->id_groupe, $groupe);
            
            $data['rows'][] = array(
                'id' => $row->id_utilisateur,
                'cell' => array(utf8_encode($row->nom), utf8_encode($row->prenom)/* , utf8_encode($row->login) */, $groupe->nom, utf8_encode($row->adresse).' - '.utf8_encode($row->adresse2).'<br />'.$row->code_postal.' - '.utf8_encode($row->ville), $row->telephone, "<a href='mailto:$row->mail'>".$row->mail."</a>", $row->date_naissance)
            );
        }
        
        return json_encode($data);        
    }

    // Retourne les données pour une flexigrid avec un idGroupe
    public function fetchAllForFlexigridWithIdGroupe($page, $sort_name, $sort_order, $qtype, $query, $rp, $id_groupe){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        $search_id = "id_groupe = $id_groupe";

        // Get total count of records
        $sql = "select * from Utilisateurs $search_sql";
        
        $select = $this->getDbTable()->select($sql);
        $result = $this->getDbTable()->fetchAll($select);
        $total = count($result);
        
        // Setup paging
        $page_start = ($page-1)*$rp;
        $limit_sql = "limit $page_start, $rp";
        
        // Return json Data
        $data = array();
        $data['page'] = $page;
        $data['total'] = $total;
        $data['rows'] = array();

        $select = $this->getDbTable()->select()->from('Utilisateurs')->where($search_id)->limit($rp, $page_start)->order($sort_sql);

        if($search_sql != '')
            $select->where($search_sql);
        
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){
            // Le nom du groupe
            $groupeMapper = new Application_Model_GroupesMapper();
            $groupe = new Application_Model_Groupes();
            $groupeMapper->find($row->id_groupe, $groupe);
            
            $data['rows'][] = array(
                'id' => $row->id_utilisateur,
                'cell' => array($row->id_utilisateur, $row->nom, $row->prenom, $row->login, $groupe->nom)
            );
        }
        
        return json_encode($data);        
    }
    
    // Retourne les données pour une flexigrid, avec les infos sur les documents formateurs
    public function fetchAllForFlexigridWithDocuments($page, $sort_name, $sort_order, $qtype, $query, $rp){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        
        // Get total count of records
        $sql = "select * from Utilisateurs where id_groupe = 3";
        
        $select = $this->getDbTable()->select($sql);
        $result = $this->getDbTable()->fetchAll($select);
        $total = count($result);
        
        // Setup paging
        $page_start = ($page-1)*$rp;
        $limit_sql = "limit $page_start, $rp";
        
        // Return json Data
        $data = array();
        $data['page'] = $page;
        $data['total'] = $total;
        $data['rows'] = array();

        $select = $this->getDbTable()->select()->from('Utilisateurs')->limit($rp, $page_start)->order($sort_sql);

        if($search_sql != '')
            $select->where($search_sql);
           
        // On restreint aux formateurs non approuvés
        $select->where('id_groupe = 3');
                    
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){                  
			// On vérifie la présence des documents
			$document_mapper = new Application_Model_DocumentMapper();
			
			$documents = $document_mapper->fetchAll($id_utilisateur = $row->id_utilisateur);
			
			$liste_document = array();
			
			 if($row->test_motivation == 1)
	        	$test_motivation = "<img class='icone_ok' src='images/icone_ok_16.png' />";
	        else
	        	$test_motivation = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
			
			// Si on en a aucun on met tous en erreur
			if(count($documents) == 0){
				$cv = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
				$rib = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
				$motivation = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
				if($row->note != "")
	     	  		$entretien = "<img class='icone_ok' src='images/icone_ok_16.png' />";
	        	else
	        		$entretien = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";

			
				$data['rows'][] = array(
                	'id' => $row->id_utilisateur,
                	'cell' => array(utf8_encode($row->nom), utf8_encode($row->prenom),$cv, $motivation, $rib, $test_motivation, $entretien)
           		 );
           		 continue;
			}
				
			// Sinon on détails un par un		
			foreach($documents as $rows){
				array_push($liste_document, $rows->getType());
			}
			
			if(in_array('cv', $liste_document))
				$cv = "<img class='icone_ok' src='images/icone_ok_16.png' />";
	        else
	        	$cv = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
	
	        if(in_array('rib', $liste_document))
				$rib = "<img class='icone_ok' src='images/icone_ok_16.png' />";
	        else
	        	$rib = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
	        		        	
	        if(in_array('motivation', $liste_document))
				$motivation = "<img class='icone_ok' src='images/icone_ok_16.png' />";
	        else
	        	$motivation = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
	       	
	       	if($row->date_entretien_skype != "")
	     	  	$entretien = "<img class='icone_ok' src='images/icone_ok_16.png' />";
	        else
	        	$entretien = "<img class='icone_erreur' src='images/icone_erreur_16.png' />";
	       	
	       	
            $data['rows'][] = array(
                'id' => $row->id_utilisateur,
                'cell' => array(utf8_encode($row->nom), utf8_encode($row->prenom),$cv, $motivation, $rib, $test_motivation, $entretien)
            );
        }
        return json_encode($data);        
    }

}


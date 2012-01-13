<?php

class CertificationsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->utilisateur = new Zend_Session_Namespace('user');
        $this->certification_mapper = new Application_Model_ListeCertificationMapper();
        $this->question_mapper = new Application_Model_QuestionsMapper();
        $this->question_certification_mapper = new Application_Model_QuestionsCertificationsMapper();
        $this->reponse_mapper = new Application_Model_ReponsesMapper();
        $this->historique_mapper = new Application_Model_HistoriqueCertificationsMapper();
        $this->groupe_mapper = new Application_Model_GroupesMapper();            
        $this->repassage_mapper = new Application_Model_RepassageCertificationMapper();
        $this->nom_groupe = $this->groupe_mapper->getGroupeNameWithId($this->utilisateur->id_groupe);
        
    }

    public function indexAction()
    {
        // Vérification des permissions
        if(!$this->acl->isAllowed($this->nom_groupe, null, "voir_liste_certifications"))
            $this->_redirector->goToSimple('index', 'Utilisateurs');
        
        if($this->acl->isAllowed($this->nom_groupe, null, 'ajouter_certification'))
            $this->view->ajouter_certification = "true";
            
        if($this->acl->isAllowed($this->nom_groupe, null, 'modifier_certification'))
            $this->view->modifier_certification = "true";
            
        if($this->acl->isAllowed($this->nom_groupe, null, 'supprimer_certification'))
            $this->view->supprimer_certification = "true";
        
        // Formulaire d'ajout de certification
        $this->view->add_form = new Application_Form_Addcertification();
                
        // Formulaire d'édition de certification
        $this->view->edit_form = new Application_Form_Editcertification();
        
    }

    public function getlistecertificationAction()
    {
        // Récupère la liste des certifications au format flexigrid
        
        $this->getHelper('layout')->disableLayout();
        
        // renvoie les donnée à la grid sous le bon format
        $request = $this->getRequest();
                
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_certification'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        $id_groupe = "";
                        
        $this->view->data = $this->certification_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
    }

    public function addcertificationAction()
    {
        // Ajoute une certification via le formulaire du tableau flexigrid
        
        $request = $this->getRequest();
        
        if($request->isPost()){
            $certification = new Application_Model_ListeCertification();
            $certification->setNom($request->getParam('nom'));
            $certification->setType($request->getParam('type'));
            $certification->setNombreQuestion($request->getParam('nombre_question'));
            $certification->setTempsCertification($request->getParam('temps_certification'));
            
            $this->certification_mapper->save($certification);
        }
        
    }

    public function deleteAction()
    {
        // Supprime une certification
        $request = $this->getRequest();
        
        if($request->isPost()){
            $this->certification_mapper->delete($request->getParam('id_certification'));
        }
        
    }

    public function editcertificationAction()
    {
        $request = $this->getRequest();
        
        if($request->isPost()){
            $certification = new Application_Model_ListeCertification();
            
            $this->certification_mapper->find($request->getParam('id_certification'), $certification);
            
            $certification->setNom($request->getParam('nom'));
            $certification->setType($request->getParam('type'));
            $certification->setNombreQuestion($request->getParam('nombre_question'));
            $certification->setTempsCertification($request->getParam('temps_certification'));
            
            $this->certification_mapper->save($certification);
        }
        
    }

    public function detailcertificationAction()
    {
        
        // Vérification des permissions
        if(!$this->acl->isAllowed($this->nom_groupe, null, "voir_liste_questions"))
            $this->_redirector->goToSimple('index', 'Utilisateurs');
                    
        if($this->acl->isAllowed($this->nom_groupe, null, 'supprimer_question_certification'))
            $this->view->supprimer_question = "true";
        
        if($this->acl->isAllowed($this->nom_groupe, null, 'ajouter_question_certification'))
            $this->view->ajouter_question = "true";
            
        if($this->acl->isAllowed($this->nom_groupe, null, 'editer_question_certification'))
            $this->view->editer_question = "true";
        
        // Affiche la liste des question et des réponses d'une certification
        $request = $this->getRequest();
        
        $this->view->certification_demande = $request->getParam('id');
        
        // Formulaire d'edition de question
        $this->view->edit_form = new Application_Form_Editquestion();
    }

    public function getlistequestionreponseAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        // Retourne les questions/reponses au format flexigrid
        $request = $this->getRequest();
        
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_question_reponse'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        $id_groupe = "";
        
        $data = $this->question_certification_mapper->fetchAllWithId($request->getParam('id_certification'));
        
        $total = $this->question_certification_mapper->countWithIdCertification($request->getParam('id_certification'));
                
        $this->view->row = $this->question_mapper->fetchAllForFlexigridWithIdQuestion($page, $sort_column, $sort_order, $search_column, $search_for, $limit, $data, $total);
    }

    public function ajouteracertificationAction()
    {
        // lie une question à une certification
        $request = $this->getRequest();
        
        if($request->isPost()){
        	if($request->getParam('question_obligatoire') == "checked")
        		$question_obligatoire = 1;
        	else
        		$question_obligatoire = 0;
        
            $question = new Application_Model_QuestionsCertifications();
            $question->setIdCertification($request->getParam('id_certification'));
            $question->setidQuestion($request->getParam('id_question'));
            $question->setQuestionObligatoire($question_obligatoire);
            
            $this->question_certification_mapper->save($question);
        }
        
    }

    public function passercertificationAction()
    {
    	/*																 */
    	/* Récupération des questions et paramétrage de la certification */
    	/*																 */
    	
    	// On vérifie si l'utilisateur est loggué
        if(empty($this->utilisateur->is_logged) && $this->utilisateur->is_logged != true){  
        	$this->_redirector->goToSimple('connexion','utilisateurs');
        }
    	
    	$request = $this->getRequest();
    	
    	$id_certification = $request->getParam('id');
    	
        // On vérifie si l'utilisateur n'as pas déjà passé cette certification
       // $authorisation = $this->authorisePassageCertification($this->utilisateur->id_utilisateur, $id_certification);
        
        // Suivant le code on effectue la bonne action
       /*
 if($authorisation == 1){
            $this->view->test = "Vous avez déjà reussi cette certification";
        }else if($authorisation == 2){
            $this->view->test = "Plus valide, vous pouvez la repasser";
        }else if($authorisation == 3){
            $this->view->test = "Vous avez déjà tenté cette certification, veuillez payer pour ressayer";
        }else if($authorisation == 4){
            $this->view->test = "Vous avez payé, vous pouvez repasser";
        }
*/
        
        // On récupère le temps et le nombre de question
        $certification = new Application_Model_ListeCertification();
        $this->certification_mapper->find($id_certification, $certification);
        $this->view->nom_certification = $certification->getNom();
        
        // Paramétrage du temps
        $date = date("d/m/Y/H/i/s");
        $date = explode("/", $date);
        
        $time = mktime($date[3], $date[4], $date[5], $date[1], $date[0], $date[2]);
        $time = $time + ($certification->getTempsCertification() * 60);
        
        $date = date('F j, Y H:i:s', $time);
        $this->view->temps_certification = $date;
               
        $data = $this->question_certification_mapper->fetchAllWithId($request->getParam('id'));
    
        // On séléctionne le bon nombre de questions au hasard dans la liste
        $questions_array = array();
        
        $nombre_question_obligatoire = 0;
        $array_question_obligatoire = array();
        
        // On récupère les questions obligatoire si il y en as
        foreach($data as $row){
        	if($row->getQuestionObligatoire() == "oui"){
      			$nombre_question_obligatoire ++;
      			array_push($array_question_obligatoire, $row->getIdQuestion());
      			continue;
        	}
        	array_push($questions_array, $row->getidQuestion());
        }
        
        // On mélange les questions et on décompte les questions obligatoire du nombre total, puis on prend le bon nombre de questions
        shuffle($questions_array);
        $questions = array_chunk($questions_array,($certification->getNombreQuestion() - $nombre_question_obligatoire));
        
        $questions_finale = array();
        
        // On ajoute les questions séléctionnées
        foreach($questions[0] as $row){
        	array_push($questions_finale, $row);
        }
                
        // On ajoute les questions obligatoire
		foreach($array_question_obligatoire as $row)
	        array_push($questions_finale, $row);
	    
	    // On récupère les réponses associées               
        $result = $this->question_mapper->fetchAllWithIdArray($questions_finale);

        $certification_array = array();
        
        $nombre_reponse = count($result);
        $i = 0;
        
        // On mélange les questions 
        shuffle($result);
           
        // On construit le QCM avec les questions et les réponses         
        foreach($result as $row){    
        	$i ++;
            $question = "<div class='question' style='display : none' id='question".$row->getidQuestion()."'><p class='enonce'><span class='enonce-question'>".$row->getQuestion()." ?</span><span class='enonce-numero'>Question N° $i sur $nombre_reponse</span></p><div class='separateur'></div>";
            
            $reponses = $this->reponse_mapper->fetchAllWithId($row->getidQuestion());
            $array_reponse = array();
            
            // Si c'est une question ouverte on affiche un textarea pour répondre
            if($row->getReponseOuverte() == 1){
            	$question .= "<div class='reponse'><p><i>Veuillez inscrire votre réponse ci-dessous : </i></p><textarea rows='10' cols='80' name='".$row->getIdQuestion()."'></textarea>";
            }else{
            	// Sinon on affiche le QCM
            	if($row->getNbrReponse() > 1){
	                // Si il y a plusieurs réponses à la question on crée une checkbox
	                foreach($reponses as $reponse){
	                    $input_reponse = "<input type='checkbox' id='".$reponse->getIdReponse()."' value='".$reponse->getIdReponse()."' name='".$row->getidQuestion()."' /><label for='".$reponse->getIdReponse()."'>".$reponse->getReponse()."</label><br />";
	                    $array_reponse[] = $input_reponse;
	                }
	            }else if($row->getNbrReponse() == 1){
	                // Sinon des radio button
	                foreach($reponses as $reponse){
	                    $input_reponse = "<input type='radio' id='".$reponse->getIdReponse()."' value='".$reponse->getIdReponse()."' name='".$row->getidQuestion()."' /><label for='".$reponse->getIdReponse()."'>".$reponse->getReponse()."</label><br />";
	                    $array_reponse[] = $input_reponse;
	                }
            	}
            }
                                                
            // réponses dans un ordre aléatoire 
            
            $max = count($array_reponse) - 1;
            
            if($max > 0){
                $range = range(0,$max);

                shuffle($range);
                
                $question .= "<div class='reponse'><p><i>Cocher la ou les bonnes réponses : </i></p>";
                foreach($range as $numero_question){
                    $question .= $array_reponse[$numero_question];
                }
            }
            
            $question .= "</div></div>";

            array_push($certification_array, $question);        
        }

		// On envoie le tout à la vue
        $this->view->question = $certification_array;
        $this->view->id_certification = $id_certification;
    }

    public function corrigecertificationAction()
    {
        $this->getHelper('layout')->disableLayout();        
        
        // Reçoit et vérifie les réponses de la certification
        $request = $this->getRequest();
        
        if($request->isPost()){
            
            $resultat = array();
            $reponses = array();
            if($request->getParam('certification') != ''){
                foreach($request->getParam('certification') as $row){
                    // On récupère les réponses et les formate dans un format plus traitable
                    $explode_array = explode(',', $row);
                    $reponses[$explode_array[0]][] = $explode_array[1];
                }
                
                foreach($reponses as $key=>$reponse){
                    // Si plusieurs réponses pour la question
                    $nbr_reponse_juste = $this->reponse_mapper->getNombreReponseJuste($key);
                    if($nbr_reponse_juste > 1){
                        $i = 0;
                        foreach($reponse as $row){
                            if($this->corrigeQuestion($row)){
                               $i++;
                            }
                        }
                        if($i == $nbr_reponse_juste){
                            array_push($resultat, 'juste');
                        }else{
                            array_push($resultat, 'faux');
                        }
                    }else{
                        // Si une seule réponse
                        foreach($reponse as $row){
                            if($this->corrigeQuestion($row))
                                array_push($resultat, 'juste');
                            else
                                array_push($resultat, 'faux');
                        }                  
                    }
                }
                
                // On compte le nombre de bonne réponse
                $nbr_bonne_reponse = count(array_keys($resultat,'juste'));
            
                // Le nombre de question
                $nbr_reponse_totale = count($this->question_certification_mapper->fetchAllWithId($request->getParam('id_certification')));
            
                // On calcule un pourcentage avec les deux nombres calculés avant
                $pourcentage_reussite = $this->calculPourcentage($nbr_bonne_reponse, $nbr_reponse_totale);
                           
                $historique = new Application_Model_HistoriqueCertifications();
                $historique->setDatePassage(date("d/m/Y"))
                           ->setDateValidite("12/10/2012")
                           ->setScore($pourcentage_reussite)
                           ->setIdUtilisateur($this->utilisateur->id_utilisateur)
                           ->setIdCertification($request->getParam('id_certification'));
                           
                $id = $this->historique_mapper->save($historique);
                
                $this->utilisateur->last_id = $id;
                                                
            }else{
                $historique = new Application_Model_HistoriqueCertifications();
                $historique->setDatePassage(date("d/m/Y"))
                           ->setDateValidite("12/10/2012")
                           ->setScore('0')
                           ->setIdUtilisateur($this->utilisateur->id_utilisateur)
                           ->setIdCertification($request->getParam('id_certification'));
                           
                $id = $this->historique_mapper->save($historique);
                
                $this->utilisateur->last_id = $id;           
        	}
    	}
        
    }

    private function corrigeQuestion($id_reponse)
    {
        // Retourne si la question est juste ou fausse
        $reponse = new Application_Model_Reponses();
        $this->reponse_mapper->find($id_reponse, $reponse);
        
        if($reponse->getEstJuste()){
            return true;
        }else
            return false;
    }

    private function calculPourcentage($nombre, $total)
    {
        // Calcule le pourcentage en fonction de deux nombres
        return round($nombre * 100 / $total);
    }

    private function authorisePassageCertification($id_utilisateur, $id_certification)
    {
        
        // Vérifie si l'utilisateur n'as pas déjà passé la certif, et si il à acheté un autre passage
        $historique = new Application_Model_HistoriqueCertifications();
        $this->historique_mapper->findByIdUtilisateurAndCertification($id_utilisateur, $id_certification, $historique);
        
        // date à tester :
        $now = date("d/m/Y");
        $date_validite = $historique->getDateValidite();

        // Formatage pour compararaison de la date
        $now = explode('/', $now);
        $date_validite= explode('/', $date_validite);

        $now = mktime(0,0,0,$now[1],$now[0],$now[2]);
        $date_validite = mktime(0,0,0,$date_validite[1], $date_validite[0], $date_validite[2]);        
        
        $code_retour;
        // Retourne un code différent suivant le cas
        
        if($historique->getScore() > 70)
            $code_retour = 1; // Si l'utilisateur à déjà eu cette certification
        else if($date_validite < $now)
            $code_retour = 2; // Si la date de validité est dépassé
        else
            $code_retour = 3; // Si l'utilisateur à déjà passé la certification mais ne l'as pas eu
            
        if($code_retour == 3){
            // On va vérifier si l'utilisateur n'as pas acheté un repassage
            $repassage = new Application_Model_RepassageCertification();
            $this->repassage_mapper->find($id_utilisateur, $id_certification, $repassage);
            
            if($repassage->getIdRepassage() == ''){
                break;
            }
            
            // On vérifie si le coupon est encore valide
            $date_validite = $repassage->getDateValidite();
            $date_validite= explode('/', $date_validite);  
            $date_validite = mktime(0,0,0,$date_validite[1], $date_validite[0], $date_validite[2]);        
            
            $now = date("d/m/Y");
            $now = explode('/', $now);
            $now = mktime(0,0,0,$now[1],$now[0],$now[2]);
                      
            if($now < $date_validite){
                $code_retour = 4;
            }
            
        }    
        
        return $code_retour;
        
    }

    public function listecertificationAction()
    {
		// Liste toute les certifications sous forme de lien pour le formateur
		
		// On fait dabord la liste des certifications que le formateur a deja afin de ne pas les afficher
		
		$result = $this->historique_mapper->findByIdUtilisateur($this->utilisateur->id_utilisateur); 
		
		$certification_acquise = array();
		
		foreach($result as $row){
			if($row->getScore() >= 70)
				array_push($certification_acquise, $row->getIdCertification());
		}
		
		$result = $this->certification_mapper->fetchAll();
		
		$liste_lien = "";
		
		foreach($result as $row){
			if(in_array($row->getIdCertification(),$certification_acquise))
				continue;
				
			// Nombre de questions dans la certification
			$questions = $this->question_certification_mapper->fetchAllWithId($row->getIdCertification());
			
			$nbr_questions = count($questions);
			
			$liste_lien .= "<p><a href='passercertification/id/".$row->getIdCertification()."' title='Temps de passage : ".$row->getTempsCertification()."mn, nombre de questions : ".$nbr_questions."'>".$row->getNom()."</a></p>";
		}
		
		$this->view->lien = $liste_lien;
    }
	
    public function resultatAction()
    {
        // Affiche le résultat à l'utilisateur
   		$historique = new Application_Model_HistoriqueCertifications();
   		
   		$this->historique_mapper->find($this->utilisateur->last_id, $historique);
   		
   		$this->view->pourcentage = $historique->getScore();
   		   		   		
    }
    



}






















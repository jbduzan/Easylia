function telephoneCheck(telephone){
	var regExp = new RegExp(/^0[1-9][0-9]{8}$/);
            
    if(telephone.length === 10){
        if(regExp.test(telephone)){
            $("#info_telephone").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
            return telephone_check = true;
        }else{
            $("#info_telephone").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;Veuillez rentrer un numéros de téléphone correct ");
            return telephone_check = false;
        }
    }else if(telephone.length < 10){
        $("#info_telephone").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Le numéro de téléphone est trop court");
        return telephone_check = false;
    }else if(telephone.length > 10){
    	$("#info_telephone").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Le numéro de téléphone est trop long");
       return telephone_check = false;
    }
}

function loginCheck(login){
  var login_check;
	if(login.length < 3){
       $("#info_login").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;Veuillez remplir le nom d'utilisateur ");
       return login_check = false;
    }else{    	
        $.ajax({
           async : false,
           url: "/utilisateurs/testloginexist",
           type: "post",
           data: {'login' : $('#login').val()},
           success: function(data){
               if(data == 'true'){
                   $("#info_login").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;Le nom d'utilisateur existe déjà !");
                   login_check = false;
               }else{   
                    $("#info_login").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
                    login_check = true;
               }
           }
        });
        return login_check;
    }
}

function mailCheck(mail){
	var mail_check = false;
	    var regExp = new RegExp(/^^[a-z0-9._-]+@[a-z0-9._-àáâãäåçèéêëìíîïðòóôõöùúûüýÿ]{2,}\.[a-z]{2,4}$/);
	    if(mail.length < 1){
	        $("#info_email").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;Veuillez remplir l'adresse e-mail ");
	        mail_check = false;
	     }else if(regExp.test(mail)){
	        $.ajax({
	           url: "/utilisateurs/testemailexist",
	           type: "post",
	           async : false,
	           data: {'mail' : mail},
	           success : function(data){
	               if(data == "true"){
	                    $("#info_email").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;L'adresse e-mail existe déjà ");
	                    mail_check = false;
	                }else{
	                  $("#info_email").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
	                    mail_check = true;
	                }
	           } 
	        });
	    }else
	        $("#info_email").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;Le format de l'adresse e-mail n'est pas correct ");   
    return mail_check;
}

function passwordCheck(password){
	var regExp = new RegExp(/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/);
    if(!regExp.test(password)){
         $("#info_password").html("<img src='../images/icone_erreur.png' height='20px' width='20px' style='font-size:5px' alt='erreur' />&nbsp;8 charactères, 1 majuscule, 1 chiffre minimum");
         return password_check = false;
    }else{
        $("#info_password").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
        return password_check = true;
    }	
}

function passwordconfCheck(password, password_conf){
	if(password == password_conf){
		$("#info_password_conf").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
        return password_conf_check = true;
	}
	else{
		$("#info_password_conf").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; La confirmation ne correspond pas");        		
           return  password_conf_check = false
       }
}

function datenaissanceCheck(date_naissance){
	if(date_naissance.length > 0){
        var regExp = new RegExp(/^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$/);
        if(regExp.test(date_naissance)){
        	// On regarde si la personne qui s'inscrie à au minimum 18 ans
        	var date = new Date();
        	var date_string = new String(date);
        	var date_year = date_string.split(' ');
        	date_year = date_year[3];
			
			var formateur_year = date_naissance.split('/');
			formateur_year = formateur_year[2];
			
			if((date_year - formateur_year) >= 18){
				$("#info_dateNaissance").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
                   return date_naissance_check = true;
			}else{
				$("#info_dateNaissance").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;Vous devez avoir au moins 18 ans pour vous inscrire ");
                      return date_naissance_check = false;
                 }
        }
        else{
            $("#info_dateNaissance").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp;Veuillez saisir votre date de naissance ");
            return date_naissance_check = false;
        }
    }else
    	return date_naissance_check = false;
}

function nomCheck(nom){
	nom_check = false;
    var regExp = new RegExp(/^[A-Z]*[a-z]*(-)?[a-zA-Z]*$/);
    if(regExp.test(nom)){
        if(nom.length > 0){
            $("#info_nom").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
            return nom_check = true;
        }else{
            $("#info_nom").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Veuillez saisir votre nom ");
          	return nom_check = false;
          }

    }else{
    	$("#info_nom").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Veuillez saisir votre nom ");
    	return nom_check = false;
    }
}

function prenomCheck(prenom){
	prenom_check = false;
    var regExp = new RegExp(/^[A-Z]*[a-zA-Z]*(-)?[a-zA-Z]*$/);
    if(prenom.length > 0 && regExp.test(prenom)){
        $("#info_prenom").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
        return prenom_check = true;
    }else{
        $("#info_prenom").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Veuillez saisir votre prénom ");
    	return prenom_check = false;
    }
}

function adresseCheck(adresse){
	adresse_check = false;
    if(adresse.length > 0){
        $("#info_adresse").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
        return adresse_check = true;
    }else{
        $("#info_adresse").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Veuillez saisir votre adresse ");
        return adresse_check = false;
    }
}

function codepostalCheck(code_postal){
	code_postal_check = false;
	var regExp = new RegExp(/[0-9]{5}/);
    if(regExp.test(code_postal) && code_postal.length == 5){
        $("#info_codePostal").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
        return code_postal_check = true;
    }else{
        $("#info_codePostal").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Veuillez saisir votre code postal ");
        return code_postal_check = false;
    }
}

function departementnaissanceCheck(departement_naissance){
	var regExp = new RegExp(/[0-9]{2}/);
    if(!regExp.test(departement_naissance) || departement_naissance.length > 2){
    	$("#info_departementNaissance").html("<img src='../images/icone_erreur.png' height='20px' width='20px' alt='erreur' />&nbsp; Votre département doit être de la forme suivante : XX ");
    	return departement_naissance_check = false;
    }else{
        $("#info_departementNaissance").html("<img src='../images/icone_ok.png' height='20px' width='20px' alt='valide' />");
        return departement_naissance_check = true;
    }
}

function paysnaissanceCheck(pays_naissance){
  // Si le pays est la france on cache le champs département
  if(pays_naissance == 'France'){
    $('#departementNaissance-element, #departementNaissance-label').show();
  }else{
    $('#departementNaissance-element, #departementNaissance-label').hide();
  }
        
}








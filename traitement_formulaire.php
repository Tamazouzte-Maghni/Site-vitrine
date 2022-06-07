<?php
/*
	********************************************************************************************
	CONFIGURATION
	********************************************************************************************
*/
// exp�diteur du dormulaire. Pour des raisons de s�curit�, de plus en plus d'h�bergeurs imposent que ce soit une adresse sur votre h�bergement/nom de domaine.
// Par exemple si vous mettez ce script sur votre site "test-site.com", mettez votre email @test-site.com comme exp�diteur (par exemple contact@test-site.com)
// Si vous ne changez pas cette variable, vous risquez de ne pas recevoir de formulaire.
$email_expediteur = 'email@site.com';
$nom_expediteur = 'Contact site.com';

// destinataire est votre adresse mail (cela peut �tre la m�me que cl'exp�diteur ci-dessus). Pour envoyer � plusieurs destinataires � la fois, s�parez-les par un point-virgule
$destinataire = 'destintion@gmail.com';

// copie ? (envoie une copie au visiteur)
$copie = 'non'; // 'oui' ou 'non'

// Messages de confirmation du mail
$message_envoye = "Votre message nous est bien parvenu !";
$message_non_envoye = "L'envoi du mail a �chou�, veuillez r�essayer SVP.";

// Messages d'erreur du formulaire
$message_erreur_formulaire = "Vous devez d'abord <a href=\"contact.html\">envoyer le formulaire</a>.";
$message_formulaire_invalide = "V�rifiez que tous les champs soient bien remplis et que l'email soit sans erreur.";

/*
	********************************************************************************************
	FIN DE LA CONFIGURATION
	********************************************************************************************
*/

// on teste si le formulaire a �t� soumis
if (!isset($_POST['email'])) {
	// formulaire non envoy�
	echo '<p>' . $message_erreur_formulaire . '</p>' . "\n";
} else {
	/*
	* cette fonction sert � nettoyer et enregistrer un texte
	*/
	function Rec($text)
	{
		$text = htmlspecialchars(trim($text), ENT_QUOTES);
		// if (1 === get_magic_quotes_gpc())
		// {
		// $text = stripslashes($text);
		// }

		$text = nl2br($text);
		return $text;
	};

	/*
	* Cette fonction sert � v�rifier la syntaxe d'un email
	*/
	function IsEmail($email)
	{
		$value = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
		return (($value === 0) || ($value === false)) ? false : true;
	}

	// formulaire envoy�, on r�cup�re tous les champs.
	$nom = (isset($_POST['nom'])) ? Rec($_POST['nom']) : '';
	$email = (isset($_POST['email'])) ? Rec($_POST['email']) : '';
	$objet = (isset($_POST['objet'])) ? Rec($_POST['objet']) : '';
	$phone = (isset($_POST['phone'])) ? Rec($_POST['phone']) : '';
	$message = (isset($_POST['message'])) ? Rec($_POST['message']) : '';

	// On va v�rifier les variables et l'email ...
	$email = (IsEmail($email)) ? $email : ''; // soit l'email est vide si erron�, soit il vaut l'email entr�

	if (($nom != '') && ($email != '') && ($objet != '') && ($message != '')) {
		// les 4 variables sont remplies, on g�n�re puis envoie le mail
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'From:' . $nom_expediteur . ' <' . $email_expediteur . '>' . "\r\n" .
			'Reply-To:' . $email . "\r\n" .
			'Content-Type: text/plain; charset="utf-8"; DelSp="Yes"; format=flowed ' . "\r\n" .
			'Content-Disposition: inline' . "\r\n" .
			'Content-Transfer-Encoding: 7bit' . " \r\n" .
			'X-Mailer:PHP/' . phpversion();

		// envoyer une copie au visiteur ?
		if ($copie == 'oui') {
			$cible = $destinataire . ';' . $email;
		} else {
			$cible = $destinataire;
		};

		// Remplacement de certains caract�res sp�ciaux
		$caracteres_speciaux = array('&#039;', '&#8217;', '&quot;', '<br>', '<br />', '&lt;', '&gt;', '&amp;', '�', '&rsquo;', '&lsquo;');
		$caracteres_remplacement = array("'", "'", '"', '', '', '<', '>', '&', '...', '>>', '<<');

		$objet = html_entity_decode($objet);
		$objet = str_replace($caracteres_speciaux, $caracteres_remplacement, $objet);

		$message = html_entity_decode($message);
		$message = str_replace($caracteres_speciaux, $caracteres_remplacement, $message);

		$phone = str_replace($caracteres_speciaux, $caracteres_remplacement, $phone);
		$messageAndPhoneNumbe = $message . "\r\n\r\n Tel:" . $phone;
		// Envoi du mail
		$cible = str_replace(',', ';', $cible); // antibug : j'ai vu plein de forums o� ce script �tait mis, les gens ne font pas attention � ce d�tail parfois
		$num_emails = 0;
		$tmp = explode(';', $cible);
		foreach ($tmp as $email_destinataire) {
			if (mail($email_destinataire, $objet, $messageAndPhoneNumbe, $headers))
				$num_emails++;
		}

		if ((($copie == 'oui') && ($num_emails == 2)) || (($copie == 'non') && ($num_emails == 1))) {
			echo '<p>' . $message_envoye . '</p>';
		} else {
			echo '<p>' . $message_non_envoye . '</p>';
		};
	} else {
		// une des 3 variables (ou plus) est vide ...
		echo '<p>' . $message_formulaire_invalide . ' <a href="contact.html">Retour au formulaire</a></p>' . "\n";
	};
}; // fin du if (!isset($_POST['envoi']))

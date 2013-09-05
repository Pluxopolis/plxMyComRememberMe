<?php
/**
 * Plugin plxMyComRememberMe
 * @author	Stephane F
 **/
class plxMyComRememberMe extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# déclaration des hooks
		$this->addHook('plxMotorDemarrageNewCommentaire', 'plxMotorDemarrageNewCommentaire');
		$this->addHook('plxMotorConstruct', 'plxMotorConstruct');
		$this->addHook('IndexEnd', 'IndexEnd');

	}

	/**
	 * Méthode qui ajoute la case à cocher "Se rappeler de moi" au niveau des commentaires
	 *
	 * @return	stdio
	 * @author	Stephane F.
	 *
	 **/
	public function IndexEnd() {

		echo '<?php
		$text = "<div class=\"rememberme\"><input type=\"checkbox\" id=\"id_rememberme\" name=\"rememberme\" value=\"1\" />&nbsp;'.$this->getLang('L_REMEMBER_ME').'</div>";
		$output = preg_replace("/<textarea.+name=[\'\"]content[\'\"](.*?)<\/textarea>/i", "$0".$text, $output);
		?>';
	}

	/**
	 * Méthode qui sauvegarde le cookie
	 *
	 * @return	stdio
	 * @author	Stephane F.
	 *
	 **/
	public function plxMotorDemarrageNewCommentaire() {

		$string = '
		if(isset($_POST["rememberme"]) AND ($retour[0]=="c" OR $retour=="mod")) {
			$cookie_path = "/";
			$cookie_domain = "";
			$cookie_secure = 0;
			$cookie_expire = time() + 3600 * 24 * 30 * 2; # durée de vie du cookie = 2 mois
			$cookie_value["name"]=plxUtils::unSlash($_POST["name"]);
			$cookie_value["site"]=plxUtils::unSlash($_POST["site"]);
			$cookie_value["mail"]=plxUtils::unSlash($_POST["mail"]);
			if (version_compare(PHP_VERSION, "5.2.0", ">="))
				setcookie("plxMyComRememberMe", serialize($cookie_value), $cookie_expire, $cookie_path, $cookie_domain, $cookie_secure, true);
			else
				setcookie("plxMyComRememberMe", serialize($cookie_value), $cookie_expire, $cookie_path."; HttpOnly", $cookie_domain, $cookie_secure);
		}';
		echo "<?php ".$string." ?>";

	}

	/**
	 * Méthode qui rappelle les données sauvegardées dans le cookie
	 *
	 * @return	stdio
	 * @author	Stephane F.
	 *
	 **/
	public function plxMotorConstruct() {

		$string = '
		if(isset($_COOKIE["plxMyComRememberMe"])) {
			$cookie_value = unserialize(plxUtils::unSlash($_COOKIE["plxMyComRememberMe"]));
			$_SESSION["msg"]["name"] = plxUtils::getValue($cookie_value["name"]);
			$_SESSION["msg"]["site"] = plxUtils::getValue($cookie_value["site"]);
			$_SESSION["msg"]["mail"] = plxUtils::getValue($cookie_value["mail"]);
		}';
		echo "<?php ".$string." ?>";

	}
}
?>
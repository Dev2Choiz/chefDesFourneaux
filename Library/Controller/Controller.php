<?php

namespace Library\Controller;

abstract class Controller implements iController
{
	
	private $redirect		= null;
	private $layout 		= 'carousel';
	private $responseHeader = 'text/html';
	private $scriptView		= array();
	private $styleView		= array();
	private $dataMod 		= array();
	private $dataView   	= array("siteName" 	=> "Chef des fourneaux",
							  		"pageTitle" => "Home");
	private $jsConfigAvant		= array();
	private $jsConfigApres		= array();
	

	protected function __construct(){
		//header("Access-Control-Allow-Origin: *"); 
	}

	protected function setRedirect($url){
		$this->redirect = $url;
	}
	
	/**
	 * Set Layout
	 * @param String $name nom du layout a utiliser
	 * @return Boolean True en cas de modification réussie sinon False
	 */
	protected function setLayout($name){
		$name = strtolower($name);
		if(!empty($name) && file_exists(APP_ROOT."Views/Layouts/$name.phtml")){
			$this->layout = $name;
			return true;
		}
		return false;
	}

	/**
	 * Get Layout
	 * @return String nom du Layout a utiliser
	 */
	protected function getLayout(){
		return $this->layout;
	}




	/**
	 * Set ResponseHeader
	 * @param String $value (valeur possible : text, html, css.....)
	 * @return Boolean True en cas de modification réussie sinon False
	 */
	protected function setResponseHeader($value){
		$value		 = strtolower($value);
		$possibility = array("text" => "text/plain",
							 "html" => "text/html",
							 "css"	=> "text/css",
							 "js"	=> "application/javascript",
							 "json" => "application/json",
							 "xml"	=> "application/xml");


		if(array_key_exists($value, $possibility)){
			$this->responseHeader = $possibility[$value];
			return true;
		}
		return false;
	}

	/**
	 * Get ResponseHeader
	 * @return String response a utiliser 
	 */
	protected function getResponseHeader(){
		return $this->responseHeader;
	}





	/**
	 * [setJsConfig pour inserer du code js, des variables, avant tout autre code js]
	 * @param [String] $quoi    	[ qu'est qu'on veut faire ]
	 * @param [String] $code 		[ code a rajouter ]
	 * @param [String] $value 		[ valuer de la variable si c'est un variable qu'on declare ] [optionnel]
	 */
	protected function setJsConfigAvant($quoi, $code , $value="" ){
		array_push( $this->jsConfigAvant, array( $quoi, $code , $value ) );
		return true;
	}

	/**
	 * [setJsConfig pour inserer du code js, des variables; apres les inclusion js]
	 * @param [String] $quoi    	[ qu'est qu'on veut faire ]
	 * @param [String] $code 		[ code a rajouter ]
	 * @param [String] $value 		[ valuer de la variable si c'est un variable qu'on declare ] [optionnel]
	 */
	protected function setJsConfigApres($quoi, $code , $value="" ){
		array_push( $this->jsConfigApres, array( $quoi, $code , $value ) );
		return true;
	}




	/**
	 * Set ScriptView
	 * @param String $script (nom/chemin du script a ajouter)
	 * @return Boolean True en cas de modification réussie sinon False
	 */
	protected function setScriptView($script){
		if(!is_string($script) || empty($script)){
			return false;
		}
		array_push($this->scriptView, $script);
		return true;
	}

	/**
	 * Get ScriptView
	 * @return Array liste des scripts a charger dans la view
	 */
	protected function getScriptView(){
		return $this->scriptView;
	}




	/**
	 * Set StyleView
	 * @param String $style (nom/chemin du style a ajouter)
	 * @return Boolean True en cas de modification réussie sinon False
	 */
	protected function setStyleView($style){
		if(!is_string($style) || empty($style)){
			return false;
		}
		array_push($this->styleView, $style);
		return true;
	}

	/**
	 * Get styleView
	 * @return Array liste des styles a charger dans la view
	 */
	protected function getStyleView(){
		return $this->styleView;
	}



	/**
	 * Set dataView
	 *
	 * Merge les nouvelles données aux données déjà existantes
	 * ces données sont accessibles depuis les views
	 * 
	 * @param array $data : array assoc "key"=>"value"
	 * @return void
	 */
	protected function setDataView(array $data){
		$this->dataView = array_merge($this->dataView, $data);
	}

	/**
	 * Get DataView
	 * @return Array liste variables accessible depuis les views
	 */
	protected function getDataView(){
		return $this->dataView;
	}



	/**
	 * Set dataMod
	 *
	 * Merge les nouvelles données aux données déjà existantes
	 * ces données sont accessibles depuis les views
	 * 
	 * @param array $data : array assoc "key"=>"value"
	 * @return void
	 */
	protected function setDataMod(array $data){
		$this->dataMod = array_merge($this->dataMod, $data);
	}

	/**
	 * Get DataMod
	 * @return Array liste variables accessible depuis les views
	 */
	protected function getDataMod(){
		return $this->dataMod;
	}




	/**
	 * Ajoute les styles et scripts contenu des array $styleView et $scriptView
	 * Cette fonction est appelée par la fonction RenderView
	 * 
	 * @param String $html : rendu de la page html
	 * @return void
	 */
	private function addFilesRender(&$html){



		
		$strIdUser="";
		$typeIdUser = "";
		if( !empty($_SESSION['user']) ){
			$strIdUser = $_SESSION['user']['id_user'];
			$typeIdUser = $_SESSION['user']['role'];
		}
		$html = str_replace('</body>', "
			<script type='text/javascript'>
				idUser ='$strIdUser'; 
				typeUser ='$typeIdUser'; 
				urlImg='".IMG_ROOT."';
				urlWebService='".WEBSERVICE_ROOT."index.php';
				LINK_ROOT='".LINK_ROOT."';
			</script>
			</body>", $html);


		//place les config js avant toute inclusion js
		$str="<script>\n";
		foreach ($this->jsConfigAvant as $val){
			switch ($val[0]) {
				case 'variable':
					$str.="js".$val[1]."=\"".$val[2]."\";\n";
					break;
				case 'code':
					$str.=$val[1].";\n";
					break;
			}
		}
		$str.="</script>\n";
		$html = str_replace('</body>', "$str</body>", $html);


		//ajoute les scripts
		foreach ($this->scriptView as $s){
			$html = str_replace('</body>', "<script src='".WEB_ROOT."/js/$s'></script></body>", $html);
		}

		//place les configs js apres toutes les inclusions js
		$str="<script>\n";
		foreach ($this->jsConfigApres as $val){
			switch ($val[0]) {
				case 'variable':
					$str.="js".$val[1]."=\"".$val[2]."\";\n";
					break;
				case 'code':
					$str.=$val[1].";\n";
					break;
			}
		}
		$str.="</script>\n";
		$html = str_replace('</body>', "$str</body>", $html);

		foreach ($this->styleView as $s){
			$html = str_replace('</head>', "<link href='".WEB_ROOT."/css/$s' rel='stylesheet' type='text/css' /></head>", $html);
		}
	}






	/**
	 * Effectue le rendu de la page appelée dans l'url
	 *
	 * Cette fonction est appelée automatiquement par le Router
	 * Cette fonction ne doit pas être appelée par l'utilisateur 
	 * 
	 * @param  String $controller : nom du controller
	 * @param  String $action     : nom de l'action 
	 * @return void
	 */
	public function renderView($controller, $action){
		global $router;

		if(!is_null($this->redirect)){
			header("location: {$this->redirect}");
			die();
		}


		$pathView = APP_ROOT."Views/Controllers/".str_replace("Application\Controllers\\", "", $controller)."/".str_replace("Action", "", $action).".phtml";
		//$pathView = APP_ROOT."Controllers/Views/Controllers/".str_replace("Application\Controllers\\", "", $controller)."/".str_replace("Action", "", $action).".phtml";		//view dans Application/controllers/views/Controllers
		

		

		if( file_exists($pathView) ){
			

			//s'en fout
			header("Content-type: ".$this->getResponseHeader()."; charset=utf-8");
			

			extract($this->getDataView());

			ob_start();
				include_once($pathView);
			$content_view = ob_get_clean();

			ob_start();

				include_once(APP_ROOT."Views/Layouts/".$this->getLayout().".phtml");
				//include_once(APP_ROOT."Controllers/Views/Layouts/".$this->getLayout().".phtml");

			$finalRender = ob_get_clean();




			
			//ajoute le  js de la page s'il existe
			$page=strtolower (str_replace("Application\Controllers\\", "", $controller).str_replace("Action", "", $action));
			//$page=$controller.$action;
			
			if( file_exists(PUBLIC_ROOT."js/".$page.".js") ){
				if(!$this->scriptExiste($page.".js")){
					$this->setScriptView($page.".js");
				}
			}


			//ajoute le style de la page s'il existe
			$page=str_replace("/", "", $_GET['page']);
			if( file_exists(PUBLIC_ROOT."css/".$page.".css") ){
				if(!$this->scriptExiste($page.".css")){
					$this->setStyleView($page.".css");
				}
			}


			

			$this->addFilesRender($finalRender);
			echo $finalRender;
		
		}elseif(!empty($_SERVER["HTTP_REFERER"])){
			header("location: ".$_SERVER["HTTP_REFERER"]);
			die();

		}else{
			header("location: ".LINK_ROOT);
			die();
		}
	}



	public function renderModule($module, $action){

		$pathView = APP_ROOT."Views/Modules/".str_replace("Application\Modules\\", "", $module)."/".str_replace("Action", "", $action).".phtml";
		if(file_exists($pathView)){
			extract($this->getDataMod());
			include_once($pathView);
		}else{
			throw new \Exception("Error View for Module:'$module' and Action:'$action' not found");
		}
	}


	public function scriptExiste($script){
		foreach ($this->scriptView as $value) {
			if($value===$script){
				 return true;
			}
		}
		return false;
	}

	public function styleExiste($style){
		foreach ($this->styleView as $value) {
			if($value===$style){
				 return true;
			}
		}
		return false;
	}













	public function isConnected(){
		return empty($_SESSION['user'] )?false:true;
	}



    public function convEnTab($tab){
        $modelCategorie  = new \Application\Models\Categorie('localhost');
        return $modelCategorie->convEnTab($tab);
    }

    public function retirerCaractereSpeciaux($chaine){
        $modelCategorie  = new \Application\Models\Categorie('localhost');
        return $modelCategorie->retirerCaractereSpeciaux($chaine);
    }



}
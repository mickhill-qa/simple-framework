<?php
/*
 *-------------------------------------------------------
 *              Simple MVC - Mick Hill
 *-------------------------------------------------------
 *
 *  MVC
 *
 */

class Mvc
{
	private $dirModel      = 'models/';
	private $dirView       = 'views/';
	private $dirController = 'controllers/';
	private $extModel      = '.php';
	private $extView       = '.phtml';
	private $extController = '.php';
	private $indexDefault  = 'index';

	public $setup;
	public $html;
	public $dados;

	public function __construct()
    {
		$this->setup = new Setup();
		$this->html  = new Html();

		$this->dirModel      = $this->setup->path['src'].$this->dirModel;
		$this->dirView       = $this->setup->path['src'].$this->dirView;
		$this->dirController = $this->setup->path['src'].$this->dirController;
	}

	/*
	 * Metodos relacionados a Uri
	 */
	private function getNameControllerFromUri($uri)
    {
		$uri  = ($uri == ''? $this->indexDefault : $uri);
		$ctrl = explode("/", $uri);
		$ctrl = $ctrl[0];
		$ctrl = explode('-', $ctrl);
		for ($i = 0; $i < count($ctrl); $i++)
			$ctrl[$i] = ucfirst($ctrl[$i]);
		$ctrl = implode('', $ctrl);
		$ctrl = $ctrl.'Controller';
		return $ctrl;
	}

	private function getNameMethodFromUri($uri)
    {
		$method = explode("/", $uri);
		$method = $method[1];
		$method = ($method == null ? $this->indexDefault : $method);
		$method = explode('-', $method);
		for ($i = 1; $i < count($method); $i++)
			$method[$i] = ucfirst($method[$i]);
		$method = implode('', $method);
		return $method;
	}

	private function getNameViewFromUri($uri = null)
    {
		if ($uri == null || $uri == '') {
			$uriAtual = getUri();
			if ($uriAtual == null || $uriAtual == '')
				$uri = $this->indexDefault;
			else
				$uri = $uriAtual;
		}

		// Se so tiver 1 parametro na uri adiciona a barra - /
		if (count(explode("/", $uri)) < 2)
			$uri = $uri.'/';

        // Adiciona index quando o ultimo caracter for barra - /
		if (substr($uri, -1) == '/')
			$uri = $uri.$this->indexDefault;

        return $uri;
	}

	/*
	 * Metodos criticos de inicializacao da aplicacao
	 */
	public function includeController()
    {
		$ctrl     = $this->getNameControllerFromUri(getUri());
		$ctrlFile = $this->dirController.$ctrl.$this->extController;
		$method   = $this->getNameMethodFromUri(getUri());

		// Se o controller na URL nao existir apontar para Controller
		if ( file_exists($ctrlFile) )
            require_once($ctrlFile);
        else
            $ctrl = "Controller";

        $paginaAtual = new $ctrl();

		// Buscando por Metodo
		if (!method_exists($paginaAtual, $method))
			$method = 'erro404';

		$paginaAtual->$method();
	}

	public function includeView($view = null)
    {
		$view     = $this->getNameViewFromUri($view);
		$viewFile = $this->dirView.$view.$this->extView;

		if (file_exists($viewFile))
			require_once $viewFile;
		else
			exit('A View "'.$view.$this->extView.'" não existe!<br />');
	}

	public function includeModel($model = null)
    {
		if ($model == null || $model == '')
			exit('Erro! Digite o nome do model...<br />');
		else {
			$modelFile = $this->dirModel.$model.$this->extModel;

			if (file_exists($modelFile))
				require_once $modelFile;
			else
				exit('O Model "'.$model.$this->extModel.'" não existe!<br />');
		}
	}

	/*
	 * Metodo de repasse de dados para View
	 */
	public function setDadosView($varArraObject = null)
    {
		$this->dados = $varArraObject;
	}
}

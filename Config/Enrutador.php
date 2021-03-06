<?php namespace Config;
class Enrutador{
	public static function run(Request $request)
	{
		$controlador = $request->getControlador() . 'Controller';
		$ruta = ROOT . 'Controllers' . DS . ucwords($controlador) . '.php';
		$metodo = $request->getMetodo();
		if ($metodo == 'index.php') {
			$metodo = 'index';
		}
		$argumento = $request->getArgumento();
		if (is_readable($ruta)) {
			require_once $ruta;
			$mostrar = 'Controllers\\' . $controlador;
			$controlador = new $mostrar;
			if (!isset($argumento)) {
				$data = call_user_func(array($controlador, $metodo)); 
			} else {
				$data = call_user_func_array(array($controlador, $metodo), $argumento); 
			}
		}
		if (!isset($_REQUEST['token'])) {
			$ruta = ROOT.'Views'.DS.$request->getControlador().DS.$request->getMetodo().'.php';
			if(is_readable($ruta)) {
				require_once $ruta;
			} else {
				require_once ROOT.'Views'.DS.'error\index.php';
			}
		}
	}
}/*final de la clase Enrutador*/

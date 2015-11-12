<?php

//    site: www.wowza.io
//    author: Carlos Camacho
//    email: carloscamachoucv@gmail.com
//    created: 12/11/2015


class Wowza{

	private $wowza_server;

	function __construct($wowza_server = "127.0.0.1:8087") {
		$this->wowza_server = $wowza_server;
	}

	function get_wowza_server(){
		return $this->wowza_server;
	}

	function set_wowza_server($wowza_server = "127.0.0.1:8087"){
		$this->wowza_server = $wowza_server;
	}

	function get_applications(){
		$service_url = "http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications";
		$curl = curl_init($service_url);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept:application/json',
			'charset=utf-8'
		));

		$curl_response = curl_exec($curl);

		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}

		curl_close($curl);
		$decoded = json_decode($curl_response);

		if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
			die('error occured: ' . $decoded->response->errormessage);
		}
		return var_export($decoded->applications);
	}


	function get_application($application_name){
		$service_url = 'http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/'.$application_name;
		$curl = curl_init($service_url);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept:application/json',
			'charset=utf-8'
		));

		$curl_response = curl_exec($curl);

		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}

		curl_close($curl);
		$decoded = json_decode($curl_response);

		if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
			die('error occured: ' . $decoded->response->errormessage);
		}
		//return var_export($decoded);
		//file_put_contents("asdf.txt", json_encode($decoded, JSON_PRETTY_PRINT));
		return var_export($decoded, JSON_PRETTY_PRINT);

	}

	function dump_application_config($application_name){
		$service_url = 'http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/'.$application_name;
		$curl = curl_init($service_url);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept:application/json',
			'charset=utf-8'
		));

		$curl_response = curl_exec($curl);

		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}

		curl_close($curl);
		$decoded = json_decode($curl_response);

		if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
			die('error occured: ' . $decoded->response->errormessage);
		}
		//return var_export($decoded);
		file_put_contents($application_name.".json", json_encode($decoded, JSON_PRETTY_PRINT));
		return true;

	}


	function create_application($application_name){
		require './libs/Mustache/Autoloader.php';
		Mustache_Autoloader::register();

		$m = new Mustache_Engine;
		$data = array(application_name => $application_name);

		$service_url = 'http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/'.$application_name;
		$curl = curl_init($service_url);

		$config_file = $fichero = file_get_contents('../wowza.io.templates/default_application.json', FILE_USE_INCLUDE_PATH);


		print ($m->render($config_file,$data));
	

		$curl_post_data = str_replace("###application_name###", $application_name, $config_file);


		//print ($curl_post_data);
		$headers = array(
		'Content-Type: application/json; charset=utf-8',
		'Accept: application/json; charset=utf-8' 
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$curl_response = curl_exec($curl);

		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}

		curl_close($curl);
		$decoded = json_decode($curl_response);

		if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
			die('error occured: ' . $decoded->response->errormessage);
		}
		return var_export($decoded);
	}
}


$wow = new Wowza("127.0.0.1");


//print ($wow->get_applications());




print ($wow->create_application("asdf"));


//$wow->get_applications();
//$wow->dump_application_config("mocoloco");



?>


<?php

//    site: www.wowza.io
//    author: Carlos Camacho
//    email: carloscamachoucv@gmail.com
//    created: November 2015
//    Use camelCase for variable names and method names....

class Wowza{

	private $wowzaServer;

	function __construct($wowzaServer = "127.0.0.1:8087") {
		$this->wowzaServer = $wowzaServer;
	}

	function getWowzaServer(){
		return $this->wowzaServer;
	}

	function setWowzaServer($wowzaServer = "127.0.0.1:8087"){
		$this->wowzaServer = $wowzaServer;
	}

	function getApplications(){
		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications";
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

	function getApplication($applicationName){
		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/".$applicationName;
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

	function deleteApplication($applicationName){
		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/".$applicationName;
		$curl = curl_init($service_url);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept:application/json',
			'charset=utf-8'
		));
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

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

	function dumpApplicationConfig($applicationName){
		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/".$applicationName;
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
		file_put_contents($applicationName.".json", json_encode($decoded, JSON_PRETTY_PRINT));
		return true;

	}

	function createApplication($applicationName){
		require './libs/Mustache/Autoloader.php';
		Mustache_Autoloader::register();

		$m = new Mustache_Engine;
		$data = array(applicationName => $applicationName);

		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/".$applicationName;
		$curl = curl_init($service_url);

		$config_file = $fichero = file_get_contents('../wowza.io.templates/default_application.json', FILE_USE_INCLUDE_PATH);


		print ($m->render($config_file,$data));
	

		$curl_post_data = str_replace("###applicationName###", $applicationName, $config_file);


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

	//To add:
	// SecureTokens
	// IP restrictions
}

?>


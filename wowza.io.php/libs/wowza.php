<?php

//    site: www.wowza.io
//    author: Carlos Camacho
//    email: carloscamachoucv@gmail.com
//    created: November 2015
//    Use camelCase for variable names and method names....


//Luego crear un archivo maestro de configuracion...
//require ('C:/Users/carlos.camacho/Dropbox/MyRepositories/wowza.io/wowza.io.php/includes/Mustache/Autoloader.php');
require($_SERVER["DOCUMENT_ROOT"].'/includes/Mustache/Autoloader.php');

Mustache_Autoloader::register();


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
		return $curl_response; //JSON as string
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
		return $curl_response;

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
		//return var_export($decoded, JSON_PRETTY_PRINT);
		return true;
	}

	function deleteAllApplications(){
		$allApplications = $this->getApplications();
		$array = json_decode($allApplications, true);
		$max = sizeof($array['applications']);
		for($i = 0; $i < $max;$i++){
			$this->deleteApplication($array['applications'][$i]['id']);
		}
		return true;
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

	function createNonSecuredApplication($applicationName){
		$m = new Mustache_Engine;
		$data = array('applicationName' => $applicationName);
		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/".$applicationName;
		$curl = curl_init($service_url);

		$config_file = $fichero = file_get_contents('C:/Users/carlos.camacho/Dropbox/MyRepositories/wowza.io/wowza.io.templates/defaultNonSecuredApplication.json', FILE_USE_INCLUDE_PATH);
		//$config_file = $fichero = file_get_contents('../../wowza.io.templates/defaultNonSecuredApplication.json', FILE_USE_INCLUDE_PATH);


		$configData = $m->render($config_file,$data);
		$headers = array(
		'Content-Type: application/json; charset=utf-8',
		'Accept: application/json; charset=utf-8' 
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $configData);
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

	function createSecuredApplication($applicationName, $sharedSecret = "mySharedSecret", $wowzaParameterPrefix = "wowzatoken"){
		$m = new Mustache_Engine;
		$data = array(
			'applicationName' => $applicationName,
			'sharedSecret' => $sharedSecret,
			'wowzaParameterPrefix' => $wowzaParameterPrefix
		);
		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/".$applicationName;
		$curl = curl_init($service_url);
		$config_file = $fichero = file_get_contents('C:/Users/carlos.camacho/Dropbox/MyRepositories/wowza.io/wowza.io.templates/defaultSecuredApplication.json', FILE_USE_INCLUDE_PATH);
		//$config_file = $fichero = file_get_contents('../../wowza.io.templates/defaultSecuredApplication.json', FILE_USE_INCLUDE_PATH);
		$configData = $m->render($config_file,$data);
		$headers = array(
		'Content-Type: application/json; charset=utf-8',
		'Accept: application/json; charset=utf-8' 
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $configData);
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

	function restartApplication($applicationName){
		$service_url = "http://".$this->wowzaServer."/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/".$applicationName."/actions/restart";
		$curl = curl_init($service_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept:application/json',
			'charset=utf-8'
		));
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
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
		//return true;
	}

}

?>


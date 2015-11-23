<?php 
	
	require 'database.php';
	require("C:/Users/carlos.camacho/Dropbox/MyRepositories/wowza.io/wowza.io.php/libs/wowza.php");

	$wowzaServerIP = "127.0.0.1";
	$wow = new Wowza($wowzaServerIP.":8087");

	if ( !empty($_POST)) {
		// keep track validation errors
		$nameError = null;

		// keep track post values
	  	$name = $_POST['name'];

		// validate input
		$valid = true;
		if (empty($name)) {
			if (true){
			$nameError = 'Please enter an Application name';
			$valid = false;
		}
		}

		if (isset($_POST['isSecured'])){
			$isSecured = true;
		}else{
			$isSecured = false;		
		}

		//cREATE THE wowza app
		if ($isSecured) {
			$output = $wow->createSecuredApplication($name);
			$wowzaContentURL = 'http://'.$wowzaServerIP.':1935/'.$name.'/mp4:myStream_aac/playlist.m3u8';
			$wowzaContentPath = $name.'/mp4:myStream_aac';
			$wowzaSecureToken = 'mySharedSecret';
			$wowzaTokenPrefix = 'wowzatoken';
			$wowzaCustomParameter = $wowzaTokenPrefix . "CustomParameter=myParameter";
			$wowzaSecureTokenStartTime = $wowzaTokenPrefix  ."starttime=". time() ;
			$wowzaSecureTokenEndTime = $wowzaTokenPrefix  ."endtime=". (time() + (7 * 24 * 60 * 60) );
			//usar $_SERVER['REMOTE_ADDR'] en vez de la 9.20
			$viewer_ip = '192.168.9.20';
			$hashstr = $wowzaContentPath ."?". $viewer_ip ."&". $wowzaSecureToken ."&". $wowzaCustomParameter ."&". $wowzaSecureTokenEndTime ."&". $wowzaSecureTokenStartTime;
			$hash = hash('sha256', $hashstr ,1);
			$usableHash=strtr(base64_encode($hash), '+/', '-_');
			$url = $wowzaContentURL ."?". $wowzaSecureTokenStartTime ."&". $wowzaSecureTokenEndTime ."&". $wowzaCustomParameter ."&".  $wowzaTokenPrefix ."hash=$usableHash";
			$streamerUrl = "rtmp://".$wowzaServerIP.":1935/".$name;
			$playerUrl = $url;

		}else{
			$wow->createNonSecuredApplication($name);
			$streamerUrl = "rtmp://".$wowzaServerIP.":1935/".$name;
			$playerUrl = 'http://'.$wowzaServerIP.':1935/'.$name.'/mp4:myStream_aac/playlist.m3u8';

		}


		// insert data
		if ($valid) {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO streams (name,isSecured,playerUrl,streamerUrl) values(?, ?, ?,?)";
			$q = $pdo->prepare($sql);
			$q->execute(array($name,$isSecured,$playerUrl,$streamerUrl));
			Database::disconnect();
			header("Location: index.php");
		}
	}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
    
    			<div class="span10 offset1">
    				<div class="row">
		    			<h3>Create a stream</h3>
		    		</div>
    		
	    			<form class="form-horizontal" action="create.php" method="post">

					  <div class="control-group <?php echo !empty($nameError)?'error':'';?>">
					    <label class="control-label">Name</label>
					    <div class="controls">
					      	<input name="name" type="text"  placeholder="Name" value="<?php echo !empty($name)?$name:'';?>">
					      	<?php if (!empty($nameError)): ?>
					      		<span class="help-inline"><?php echo $nameError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>

					  <div class="control-group <?php echo !empty($isSecuredError)?'error':'';?>">
					    <label class="control-label">Is secured?</label>
					    <div class="controls">
					      	<input name="isSecured" type="checkbox" placeholder="Is secureeed" value="<?php echo !empty($isSecured)?$isSecured:'';?>" checked>
					      	<?php if (!empty($isSecuredError)): ?>
					      		<span class="help-inline"><?php echo $isSecuredError;?></span>
					      	<?php endif;?>
					    </div>
					  </div>


					  <div class="form-actions">
						  <button type="submit" class="btn btn-success">Create</button>
						  <a class="btn" href="index.php">Back</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>
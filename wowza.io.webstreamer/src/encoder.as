//    site: www.wowza.io
//    author: Carlos Camacho
//    email: carloscamachoucv@gmail.com
//    created: 12/11/2015

package
{
	import flash.display.DisplayObject;
	import flash.display.Sprite;
	import flash.events.NetStatusEvent;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.media.SoundCodec;
	import flash.media.H264Level;
	import flash.media.H264Profile;
	import flash.media.H264VideoStreamSettings;
	import flash.media.Video;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	
	
	
	[SWF( width="940", height="880" )]
	public class encoder extends Sprite
	{
		private var metaText:TextField = new TextField();
		private var vid_outDescription:TextField = new TextField();
		private var vid_inDescription:TextField = new TextField();
		private var metaTextTitle:TextField = new TextField();
		
		//Define a NetConnection variable nc
		private var nc:NetConnection;
		//Define two NetStream variables, ns_in and ns_out
		private var ns_in:NetStream;
		private var ns_out:NetStream;
		//Define a Camera variable cam
		private var cam:Camera = Camera.getCamera();
		//Define a Video variable named vid_out
		private var vid_out:Video;
		//Define a Video variable named vid_in
		private var vid_in:Video;
		//Get the audio...
		private var mic:Microphone = Microphone.getMicrophone();
		
		//Class constructor
		public function encoder()
		{	
			//Call initConnection()
			initConnection();
		}
		
		//Called from class constructor, this function establishes a new NetConnection and listens for its status
		private function initConnection():void
		{
			//Create a new NetConnection by instantiating nc
			nc = new NetConnection();
			//Add an EventListener to listen for onNetStatus()
			nc.addEventListener(NetStatusEvent.NET_STATUS, onNetStatus);
			//Connect to the live folder on the server
			//nc.connect("rtmp://127.0.0.1:8086/mocoloco");
			nc.connect("rtmp://127.0.0.1:1935/mocoloco");
			//nc.connect("rtmp://YOUR_SERVER_URL/live");
			//Tell the NetConnection where the server should invoke callback methods
			nc.client = this;
			
			//Instantiate the vid_out variable, set its location in the UI, and add it to the stage
			vid_out = new Video();
			vid_out.x = 300; 
			vid_out.y = 10;
			addChild( vid_out );
			
			//Instantiate the vid_in variable, set its location in the UI, and add it to the stage
			vid_in = new Video();
			vid_in.x = vid_out.x + vid_out.width; 
			vid_in.y = vid_out.y;
			addChild( vid_in );
		}
		
		//It's a best practice to always check for a successful NetConnection
		protected function onNetStatus(event:NetStatusEvent):void
		{
			//Trace the value of event.info.code
			trace( event.info.code );
			/*Check for a successful NetConnection, and if successful
			call publishCamera(), displayPublishingVideo(), and displayPlaybackVideo()*/
			if( event.info.code == "NetConnection.Connect.Success" )
			{ 
			//	log("I will publish");
				publishCamera(); 
				displayPublishingVideo(); 
			//	displayPlaybackVideo();
			}
		}
		
		//The encoding settings are set on the publishing stream
		protected function publishCamera():void
		{
			//Instantiate the ns_out NetStream
			ns_out = new NetStream( nc );


			cam.setMode(320, 240, 20, false);
			cam.setQuality(0, 100);
			cam.setKeyFrameInterval(15);
			

			
			
			mic.setSilenceLevel(0);
			mic.rate = 11;
			mic.codec = SoundCodec.SPEEX;
			mic.encodeQuality = 5;
			mic.framesPerPacket = 2;
						
			
			//Attach the camera to the outgoing NetStream
			ns_out.attachCamera( cam );
			//Attacheo el micro...
			//NELLYMOSER|PCMA|PCMU|SPEEX
			//Cant attach the audio
			ns_out.attachAudio( mic );	

			

			
			//Define a local variable named h264Settings of type H264VideoStreamSettings
			var h264Settings:H264VideoStreamSettings = new H264VideoStreamSettings();
			//Set encoding profile and level on h264Settings
			//h264Settings.setProfileLevel( H264Profile.BASELINE, H264Level.LEVEL_3_1 );
			h264Settings.setProfileLevel( H264Profile.BASELINE, H264Level.LEVEL_1_1 );

			


			
			//Set the bitrate and quality settings on the Camera object
			//cam.setQuality( 90000, 100 );
			//Set the video's height, width, fps, and whether it should maintain its capture size
			//cam.setMode( 320, 240, 30, true );
			//Set the keyframe interval
			//cam.setKeyFrameInterval( 15 );
			//Set the outgoing video's compression settings based on h264Settings
			ns_out.videoStreamSettings = h264Settings;
			//Publish the outgoing stream
			ns_out.publish( "myStream", "live" );
			//Declare the metadata variable
			var metaData:Object = new Object();
			//Give the metadata object properties to reflect the stream's metadata
			metaData.codec = ns_out.videoStreamSettings.codec;
			metaData.profile =  h264Settings.profile;
			metaData.level = h264Settings.level;
			metaData.fps = cam.fps;
			metaData.bandwith = cam.bandwidth;
			metaData.height = cam.height;
			metaData.width = cam.width;
			metaData.keyFrameInterval = cam.keyFrameInterval;
			//Call send() on the ns_out NetStream
			//ns_out.send( "@setDataFrame", "onMetaData", metaData );

			
			
		
		
		}
		
		//Display the outgoing video stream in the UI
		protected function displayPublishingVideo():void
		{
			//Attach the incoming video stream to the vid_out component
			vid_out.attachCamera( cam );
		}
		
		//Display the incoming video stream in the UI
		protected function displayPlaybackVideo():void
		{
			//Instantiate the ns_in NetStream
			ns_in = new NetStream( nc );
			//Set the client property of ns_in to "this"
			ns_in.client = this;
			//Play the NetStream
			ns_in.play( "mp4:webCam.f4v" );
			//Attach the incoming video to the incoming NetStream (ns_in)
			vid_in.attachNetStream( ns_in ); 
		}
		
		//Necessary callback function that checks bandwith (remains empty in this case)
		public function onBWDone():void
		{
		}
		
		//Display stream metadata and lays out visual components in the UI
		public function onMetaData( o:Object ):void	
		{			
			metaText.x = 0;
			metaText.y = 55;
			metaText.width = 300;
			metaText.height = 385;
			metaText.background = true;
			metaText.backgroundColor = 0x1F1F1F;
			metaText.textColor = 0xD9D9D9;
			metaText.border = true;
			metaText.borderColor = 0xDD7500;
			addChild( metaText );
			
			metaTextTitle.text = "\n             - Encoding Settings -";
			var stylr:TextFormat = new TextFormat();
			stylr.size = 18;
			metaTextTitle.setTextFormat( stylr );
			metaTextTitle.textColor = 0xDD7500;
			metaTextTitle.width = 300;
			metaTextTitle.y = 10;
			metaTextTitle.height = 50;
			metaTextTitle.background = true;
			metaTextTitle.backgroundColor = 0x1F1F1F;
			metaTextTitle.border = true;
			metaTextTitle.borderColor = 0xDD7500;
			
			vid_outDescription.text = "\n\n\n\n                 Live video from webcam \n\n" +
			"	              Encoded to H.264 in Flash Player 11 on output";
			vid_outDescription.background = true;
			vid_outDescription.backgroundColor = 0x1F1F1F;
			vid_outDescription.textColor = 0xD9D9D9;
			vid_outDescription.x = 300;
			vid_outDescription.y = cam.height;
			vid_outDescription.width = cam.width;
			vid_outDescription.height = 200;
			vid_outDescription.border = true;
			vid_outDescription.borderColor = 0xDD7500;
			addChild( vid_outDescription );
			addChild( metaTextTitle );
			
			vid_inDescription.text = "\n\n\n\n                  H.264-encoded video \n\n" + 
			"                  Streaming from Flash Media Server";
			vid_inDescription.background = true;
			vid_inDescription.backgroundColor =0x1F1F1F;
			vid_inDescription.textColor = 0xD9D9D9;
			vid_inDescription.x = vid_in.x;
			vid_inDescription.y = cam.height;
			vid_inDescription.width = cam.width;
			vid_inDescription.height = 200;
			vid_inDescription.border = true;
			vid_inDescription.borderColor = 0xDD7500;
			addChild( vid_inDescription );
			
			for ( var settings:String in o )
			{
			trace( settings + " = " + o[settings] );
			
			metaText.appendText( "\n" + "  " + settings.toUpperCase() + " = " + o[settings] + "\n" );
			}
		}


		//public function log(msg:String):void{
		//	ExternalInterface.call("console.log", msg);
		//}
		
		
		
		
	}
}
<?php
class Notifier extends MY_Model{
	
	public $pf_host;
	
	public $pf_data;
	
	public $is_sandbox = PAYFAST_SANDBOX_MODE;
	
	public $pf_parameter_string;
	
	public $pass_phrase = PAYFAST_PASS_PHRASE;
	
	public function __construct()
	{
		 header( 'HTTP/1.0 200 OK' );
		 flush();
		
		$this->pf_host = $this->is_sandbox ? 'sandbox.payfast.co.za' : 'www.payfast.co.za' ;
		
		$this->pf_data = $_POST;
	}
	
	public function init()
	{
		// Strip any slashes in data
		foreach($this->pf_data as $key => $val )
		{
			$this->pf_data[$key] = stripslashes( $val );
		}

		// $pfData includes of ALL the fields posted through from PayFast, this includes the empty strings
		foreach( $this->pf_data as $key => $val )
		{
			if( $key != 'signature' )
			{
				$this->pf_parameter_string .= $key .'='. urlencode( $val ) .'&';
			}
		}

		// Remove the last '&' from the parameter string
		$this->pf_parameter_string = substr( $this->pf_parameter_string, 0, -1 );
		$pf_temp_param_string = $this->pf_parameter_string;
		// If a passphrase has been set in the PayFast Settings, then it needs to be included in the signature string.
		$passPhrase = ''; //You need to get this from a constant or stored in you website database
		
		/// !!!!!!!!!!!!!! If you testing your integration in the sandbox, the passPhrase needs to be empty !!!!!!!!!!!!
		if($this->pass_phrase && !$this->is_sandbox)
		{
			$pf_temp_param_string .= '&passphrase='.urlencode($this->pass_phrase);
		}
		
		$signature = md5($pf_temp_param_string);
		
		
		if($signature != $this->pf_data['signature'])
		{
			log_message('error', "generated : {$signature} recieved : {$this->pf_data['signature']}"); 
			die('Invalid Signature');
		}

		// Variable initialization
		$valid_hosts = array(
			'www.payfast.co.za',
			'sandbox.payfast.co.za',
			'w1w.payfast.co.za',
			'w2w.payfast.co.za',
		);

		$valid_ips = array();

		foreach( $valid_hosts as $pf_hostname )
		{
			$ips = gethostbynamel( $pf_hostname );

			if( $ips !== false )
			{
				$valid_ips = array_merge( $valid_ips, $ips );
			}
		}

		// Remove duplicates
		$valid_ips = array_unique($valid_ips);
		
		if(!in_array($_SERVER['REMOTE_ADDR'], $valid_ips ))
		{
			log_message('debug', 'payfast: Source IP not Valid');
			die('Source IP not Valid');
		}
	}
	
	
	public function init_curl()
	{
		if(in_array( 'curl', get_loaded_extensions()))
		{
			// Variable initialization
			$url = 'https://'. $this->pf_host .'/eng/query/validate';

			// Create default cURL object
			$ch = curl_init();
					  
			// Set cURL options - Use curl_setopt for freater PHP compatibility
			// Base settings
			curl_setopt( $ch, CURLOPT_USERAGENT, PF_USER_AGENT );  // Set user agent
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
			curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
						  
			// Standard settings
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->pf_parameter_string );
			curl_setopt( $ch, CURLOPT_TIMEOUT, PF_TIMEOUT );
			if( !empty( $pfProxy ) )
			{
				curl_setopt( $ch, CURLOPT_PROXY, $proxy );
			}      
			// Execute CURL
			$response = curl_exec( $ch );
			curl_close( $ch );
		}
		else
		{
			$header = '';
			$res = '';
			$headerDone = false;
							 
			// Construct Header
			$header = "POST /eng/query/validate HTTP/1.0\r\n";
			$header .= "Host: ". $this->pf_host ."\r\n";
			$header .= "User-Agent: ". PF_USER_AGENT ."\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen( $this->pf_parameter_string ) . "\r\n\r\n";
				   
			// Connect to server
			$socket = fsockopen( 'ssl://'. $this->pf_host, 443, $errno, $errstr, PF_TIMEOUT );
					
			// Send command to server
			fputs($socket, $header . $this->pf_parameter_string);
				  
			// Read the response from the server
			while( !feof( $socket ) )
			{
				$line = fgets( $socket, 1024 );
				  
				// Check if we are finished reading the header yet
				if( strcmp( $line, "\r\n" ) == 0 )
				{
					// read the header
					$headerDone = true;
				}
				// If header has been processed
				else if( $headerDone )
				{
					// Read the main response
					$response .= $line;
				}
			}
		}
		
		$lines = explode( "\r\n", $response );
		$verifyResult = trim($lines[0]);

		if( strcasecmp($verifyResult, 'VALID' ) != 0 )
		{
			log_message('debug', 'payfast: data not valid');
			die('Data not valid');
		}
	}
	
	public function get_posted_data()
	{
		return $this->pf_data;
	}
}
?>
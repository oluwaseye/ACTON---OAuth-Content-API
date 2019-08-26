<?php 
class ActonAPI{
	//CREATED CONSTANTS TO HOLD THE VALUES NEEDED TO MAKE THE REQUEST
	private const CLIENTID 					= 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
	private const CLIENTSECRET 				= 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
	private const USERNAME 					= 'XXXXXXXXXXXXXXXXXXX';
	private const PASSWORD 					= 'XXXXXXXX';
	private const ACTONTOKENURL 			= 'https://restapi.actonsoftware.com/token'; 
	private const GRANTTYPE 				= 'XXXXXXXXXXX';
	private const ACTONLISTID 				= "l-XXXX";  
	private const ACTONADDCONTACTURL 		= "https://restapi.actonsoftware.com/api/1/list/" .self::ACTONLISTID. "/record";
	private const ACTONUPSERTCONTACTURL 	= "https://restapi.actonsoftware.com/api/1/list/" .self::ACTONLISTID. "/record?email=";
	private const ACTONGETALLCONTACTS 		= "https://restapi.actonsoftware.com/api/1/list/" .self::ACTONLISTID;
	
	
	
 	
	function __construct( ) {
		//JSON HEADER
		header("Content-Type: application/json");
		//ACTION CURL FIELDS
		$this->CLIENTID 				= self::CLIENTID;	
		$this->CLIENTSECRET 			= self::CLIENTSECRET;		
		$this->USERNAME 				= self::USERNAME;		
		$this->PASSWORD 				= self::PASSWORD;
		$this->ACTONTOKENURL 			= self::ACTONTOKENURL;
		$this->ACTONADDCONTACTURL 		= self::ACTONADDCONTACTURL;
		$this->ACTONUPSERTCONTACTURL 	= self::ACTONUPSERTCONTACTURL;
		$this->ACTONGETALLCONTACTS		= self::ACTONGETALLCONTACTS;
		$this->GRANTTYPE 				= self::GRANTTYPE;		
		$this->ACTONLISTID 				= self::ACTONLISTID;	
		//FORM FIELDS
		$this->emailAddress 			= $_POST['emailaddress'];
		$this->firstName 				= ( isset($_POST['firstname']) ) ? $_POST['firstname'] : ' ';
		$this->lastName 				= ( isset($_POST['lastname']) ) ? $_POST['lastname'] : ' ' ;
		$this->zipCode 					= ( isset($_POST['zipcode']) ) ? $_POST['zipcode'] : ' ';
		$this->Phone 					= ( isset($_POST['phone']) ) ? $_POST['phone'] : ' ';
		$this->companyAddress			= ( isset($_POST['address']) ) ? $_POST['address'] : ' ' ;
		$this->organization 			= ( isset($_POST['organization']) ) ? $_POST['organization'] : ' ' ;
		$this->state 					= ( isset($_POST['state']) ) ? $_POST['state'] : ' ' ;
		$this->city 					= ( isset($_POST['city']) ) ? $_POST['city'] : ' ' ;
		$this->feedbackType 			= ( isset($_POST['feedbackType']) ) ? $_POST['feedbackType'] : ' ' ;
		$this->feedback 				= ( isset($_POST['feedback']) ) ? $_POST['feedback'] : ' ' ;
		
	}	
	
	protected function CURLProcessToken($URL, $REQUESTTYPE, $PAYLOAD){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $REQUESTTYPE);//PUT
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PAYLOAD);
		$data = curl_exec($ch);
		return $data; 
	}
	
	protected function CURLProcessBearer( $URL, $REQUESTTYPE, $PAYLOAD){
		
		$tokenData = json_decode( $this->getToken() );
	 
		if($tokenData->access_token){
			//USE the token with the form data provided
			$Bearer = $tokenData->access_token;
			
			$curlHeader = array();
			$curlHeader[] = "Content-type: application/json";
			$curlHeader[] = "Authorization: Bearer $Bearer";
			$curlHeader[] = "Cache-Control: no-cache";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeader);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $REQUESTTYPE);//PUT
			curl_setopt($ch, CURLOPT_POSTFIELDS, $PAYLOAD);
			$data = curl_exec($ch);
			return $data; 
		}else{
			die("Looks like you do not have the required keys to the gate");//Access Token failed
		}
	}
	
	protected function getToken(){
		$data = array(
		'client_id' => $this->CLIENTID,
		'client_secret' => $this->CLIENTSECRET,
		'username'=> $this->USERNAME,
		'password'=> $this->PASSWORD,
		'grant_type' => $this->GRANTTYPE
		); 
		return $this->CURLProcessToken($this->ACTONTOKENURL, "POST", $data);
	}

	 

	//ADD A NEW CONTACT
	function addContact(){
		$data =  json_encode(
			array( 
				"E-mail"=> $this->emailAddress, 
				"FirstName"=> $this->firstName, 
				"LastName"=> $this->lastName, 
				"Zipcode"=> $this->zipCode, 
			) 
		);
		return $this->CURLProcessBearer( $this->ACTONADDCONTACTURL, "POST", $data); 
	}	
	
	//UPDATE OR ADD A NEW CONTACT
	/**** Update an existing record or insert a new contact record ***/
	/****using the email address that you specify****/
	function upsertContact(){
		
		$data =  json_encode(
				array( 
					"E-mail"=> $this->emailAddress, 
					"FirstName"=> $this->firstName, 
					"LastName"=> $this->lastName, 
					"Zipcode"=> $this->zipCode, 
				) 
		);
		return $this->CURLProcessBearer($this->ACTONUPSERTCONTACTURL.$this->emailAddress, "PUT", $data); 
	}
	
	function upsertContactSubscribe(){
		
		$data =  json_encode(
				array( 
					"E-mail"=> $this->emailAddress, 
					"FirstName"=> $this->firstName, 
					"LastName"=> $this->lastName, 
					"companyAddress"=> $this->companyAddress, 
					"city"=> $this->city, 
					"state"=> $this->state, 
					"phone"=> $this->phone,
					"Zipcode"=> $this->zipCode, 
					"feedbackType"=> $this->feedbackType, 
					"feedback"=> $this->feedback, 
				) 
		);
		return $this->CURLProcessBearer($this->ACTONUPSERTCONTACTURL.$this->emailAddress, "PUT", $data); 
	}
	
	function upsertContactQuestion(){
		
		$data =  json_encode(
				array( 
					"E-mail"=> $this->emailAddress, 
					"FirstName"=> $this->firstName, 
					"LastName"=> $this->lastName, 
					"Zipcode"=> $this->zipCode, 
				) 
		);
		return $this->CURLProcessBearer($this->ACTONUPSERTCONTACTURL.$this->emailAddress, "PUT", $data); 
	}
	
	//GET ALL CONTACTS
	function getAllContacts(){ 
		$data =  array(  ) ;
		return $this->CURLProcessBearer($this->ACTONGETALLCONTACTS, "GET", $data); 
	}
	
	 
	function process( ){
		$tokenData = json_decode( $this->getToken() );
		echo self::ACTONADDCONTACTURL;
	}


}
//Instantiate
$acton = new ActonAPI;

 
if( isset($_GET['process']) && $_GET['process'] === "subscribe" ){
	echo $acton->upsertContactSubscribe();
}
 

if ( isset($_GET['process']) && $_GET['process'] === "question"){
   echo $acton->upsertContactQuestion(); 
} 

 

?>


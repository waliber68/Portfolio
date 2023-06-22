<?php

class CertificateValidator {
    private $certificate;
    
    public function __construct($certificate) {
        $this->certificate = $certificate;
    }
    
    public function getCommonName() {
        $subject = $this->getCertificateSubject();
        return $subject['commonName'];
    }
    
    public function getUserId() {
        $subject = $this->getCertificateSubject();
        return $subject['userid'];
    }
    
    public function isUSCitizen() {
        $subject = $this->getCertificateSubject();
        $country = $subject['country'];
        return $country === 'US';
    }
    
    public function isCAValid($caRootFile) {
        $caCert = $this->certificate;
        $caRoot = file_get_contents($caRootFile);
        return strpos($caRoot, $caCert) !== false;
    }
    
    private function getCertificateSubject() {
        $certificateInfo = openssl_x509_parse($this->certificate);
        $subject = $certificateInfo['subject'];
        $subjectArray = [];
        
        foreach ($subject as $key => $value) {
            $key = strtolower(str_replace(' ', '', $key));
            $subjectArray[$key] = $value;
        }
        
        return $subjectArray;
    }
}

// Specify the URL to the server API
$apiUrl = 'https://example.com/api';

// Specify the path to the user's certificate file
$certificateFile = '/path/to/user_certificate.crt';

// Read the certificate contents
$certificate = file_get_contents($certificateFile);

// Create a new cURL resource
$curl = curl_init();

// Set the cURL options
curl_setopt($curl, CURLOPT_URL, $apiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSLCERT, $certificateFile);

// Execute the cURL request and retrieve the response
$response = curl_exec($curl);

// Check for cURL errors
if (curl_errno($curl)) {
    echo 'cURL error: ' . curl_error($curl);
}

// Close the cURL resource
curl_close($curl);

// Decode the response JSON
$result = json_decode($response, true);

// Validate the certificate
$validator = new CertificateValidator($certificate);

$commonName = $validator->getCommonName();
$userId = $validator->getUserId();
$isUSCitizen = $validator->isUSCitizen();
$isCAValid = $validator->isCAValid('/path/to/ca_root.crt');

// Does the certificate contain a CA in the server root
if ($isCAValid) {
    echo "CA is valid.\n";
	echo "Common Name: $commonName\n";
	echo "User ID: $userId\n";
	// Is user a US citizen
	if ($isUSCitizen) {
    	echo "Country: United States (Citizen)\n";
		include 'citizen.php';
	} else {
		echo "Non-US person\n";
		include 'foreign.php';
	}

} else {
    // User cannot access resource
    echo "CA is not valid.\n";
	include 'notAuthorized.php';
}


?>

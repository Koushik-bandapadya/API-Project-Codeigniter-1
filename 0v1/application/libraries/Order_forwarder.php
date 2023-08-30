<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Order_forwarder {

	public function orderCurl($url,$username,$password,$request_body){
		// echo $url."<br>";
		// echo $username."<br>";
		// echo $password."<br>";
		//echo print_r($request_body)."<br>";

		$myJSonDatum = json_encode($request_body);
		//echo $myJSonDatum.'<br>';

		if($ch=curl_init($url)){
			// curl_setopt($ch, CURLOPT_URL, $url);
			// curl_setopt( $ch , CURLOPT_HTTPAUTH , CURLAUTH_BASIC ) ;
			// curl_setopt( $ch, CURLOPT_USERPWD , $username . ':' . $password ) ;
			// curl_setopt( $ch , CURLOPT_CUSTOMREQUEST , 'POST' ) ;
			// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($myJSonDatum)));

			// curl_setopt($ch, CURLOPT_POST, true);
			// curl_setopt($ch, CURLOPT_POSTFIELDS,$myJSonDatum);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			// curl_setopt( $ch, CURLOPT_TIMEOUT , 300 ) ;

			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($myJSonDatum)));
			curl_setopt( $ch , CURLOPT_HEADER , 1 ) ;
			curl_setopt( $ch, CURLOPT_USERPWD , $username . ':' . $password ) ;
			curl_setopt( $ch , CURLOPT_TIMEOUT , 30 ) ;
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$myJSonDatum);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl_response=curl_exec($ch);
			$response=json_decode($curl_response);
			curl_close($ch);
			
			// echo "API Response:";
			// var_dump($response);
		}
	}
}
?>
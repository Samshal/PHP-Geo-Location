<?php
/**
 * PHP Geo Location
 *
 * Copyright (c) 2015, Samuel Adeshina <samueladeshina73@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Samuel Adeshina nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   php geo location
 * @author    Samuel Adeshina <samueladeshina73@gmail.com>
 * @copyright 2015 Samuel Adeshina <samueladeshina73@gmail.com>
 * @since     File available since Release 1.0.0
 *//*

*/
	require_once("geolocation.interface.php");

	class GeoLocation implements IGeoLocation
	{
		protected $requestObj;
		private $apiKey, $responseType, $apiLocation, $returnType, $returnedResponse;
		public function __construct(Unirest\Request $rObj)
		{
			$config = parse_ini_file("config.ini");
			$this->apiKey = $config['ApiKey'];
			$this->responseType = $config['ResponseType'];
			$this->apiLocation = $config['ApiLocation'];
			$this->returnType = $config["ReturnType"];
			$this->requestObj = $rObj;
		}

		private function setParameters()
		{
			$urlParams = array("X-Mashape-Key"=>$this->apiKey, "Accept"=>$this->responseType);
			return array($this->apiLocation, $urlParams);
		}

		public function getLocation($location)
		{
			$apiLocation = self::setParameters()[0]."?location=".$location;
			$urlParams = self::setParameters()[1];
			$response = $this->requestObj->get($apiLocation, $urlParams);
			$this->returnedResponse = $this->parseLocation($response);
			return $this->parseLocation($response);
		}

		protected function parseLocation($jsonObject)
		{
			//print_r($jsonObject);
			switch(strtolower($this->returnType))
			{
				case "array":
				{
					$respObject_code = json_decode($jsonObject->code);
					$respObject_rawBody = json_decode($jsonObject->raw_body);
					$respObject = array("code"=>$respObject_code, "raw_body"=>$respObject_rawBody);
					break;
				}
				case "json":
				{
					$respObject = $jsonObject;
					break;
				}
				default:
				{
					throw new \Exception("An invalid return type was specified in the configuration file");
					return;
				}
			}
			if (isset($respObject))
			{
				return $respObject;
			}
			throw new \Exception("Please specify a valid response type in the configuration file");
		}
		
		private function getResponseBody($index)
		{
			return $this->returnedResponse["raw_body"]->Results[$index];
		}
		public function getName($index = 0)
		{
			return self::getResponseBody($index)->name;
		}
		public function getType($index = 0)
		{
			return self::getResponseBody($index)->type;
		}
		public function getCountry($index = 0)
		{
			return self::getResponseBody($index)->c;
		}
		public function  getTimeZone($index = 0)
		{
			return self::getResponseBody($index)->tz;
		}
		public function getStandardTimeZone($index = 0)
		{
			return (isset(self::getResponseBody($index)->tzs)) ? self::getResponseBody($index)->tzs : "NULL";
		}
		public function getLatLong($index = 0)
		{
			$latlong = self::getResponseBody($index)->ll;
			$exLatLong = explode(" ", $latlong);
			return array("lat"=>$exLatLong[0], "long"=>$exLatLong[1]);
		}
		public function getLatitude($index = 0)
		{
			return self::getResponseBody($index)->lat;
		}
		public function getLongitude($index = 0)
		{
			return self::getResponseBody($index)->lon;
		}
		public function getZMWCode($index = 0)
		{
			return self::getResponseBody($index)->zmw;
		}
		public function getZipCode($index = 0)
		{
			$zmw = $this->getZMWCode($index);
			$ex_ZMW = explode(".", $zmw);
			return $ex_ZMW[0];
		}
		public function getMagicCode($index = 0)
		{
			$zmw = $this->getZMWCode($index);
			$ex_ZMW = explode(".", $zmw);
			return $ex_ZMW[1];
		}
		public function getWMOID($index = 0)
		{
			$zmw = $this->getZMWCode($index);
			$ex_ZMW = explode(".", $zmw);
			return $ex_ZMW[2];
		}

		public function getMatches()
		{
			return count($this->returnedResponse["raw_body"]->Results);
		}
	}
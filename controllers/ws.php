<?php if(!defined('INDEX')){exit;}
/**
 * Created by PhpStorm.
 * User: john
 * Date: 14/9/16
 * Time: 5:23 μμ
 */

// JnPHP Controller Class
class ws extends JnPHP {

	function __construct(){

	}

	function main() {
		global $config,$_JnRequest;
		$this->doSendResponse($_JnRequest);

	}


	// use this
	private function doSendResponse($resp_content, $format = "json", $error = false) {
		$response = $this->returnOK($resp_content);
		$this->deliverResponse($format, $response, $error);
	}

	private static function deliverResponse($format, $api_response, $error) {
		global $config,$http_response_code;

		if ($error) {
			header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);
			header('Content-Type: text/html; charset=utf-8');
			if (isset($api_response['data']) && !empty($api_response['data'])) {
				echo($api_response['data']);
			}
			return;
		}

		switch(strtolower($format)) {
			case 'json':
				header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);
				header('Content-Type: application/json; charset=utf-8');
				$json_response = json_encode($api_response['data'], JSON_PRETTY_PRINT);
				echo $json_response;
				break;

			case 'xml':
				header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);
				header('Content-Type: application/xml; charset=utf-8');
				print_r($api_response['data']);
				break;

			default:
				header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);
				header('Content-Type: text/html; charset=utf-8');
				echo($api_response['data']);
				break;
		}
		return;
	}       

	/**
	 * Creates an xml document from a php associative array
	 * @param $array
	 * @param string $node_name
	 * @return DOMDocument
	 */
	private function array2xml($array, $node_name="response") {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;
		$root = $dom->createElement($node_name);
		$dom->appendChild($root);
		$array2xml = function ($node, $array) use ($dom, &$array2xml) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					$n = $dom->createElement($key);
					$node->appendChild($n);
					$array2xml($n, $value);
				} else {
					if ( $key[0] == (self::$attributeDelimiter)) {
						$attr = $dom->createAttribute(substr($key,1));
						$attr->value = $value;
						$node->appendChild($attr);
					}
					else {
						$n = $dom->createElement($key, $value);
						$node->appendChild($n);
					}
				}
			}
		};
		$array2xml($root, $array);

		return $dom;
	}

	private function returnOK($msg){
		$response['code'] = 1;
		$response['status'] = OK;
		$response['data'] = $msg;
		return $response;
	}


	private function return401($er = "") {
		global $api_response_code, $gSilentErrors;
		$response['code'] = 3;
		$response['status'] = UNAUTHORIZED;
		if ($er == "") {
			$er = $api_response_code[ $response['code'] ]['Message'];
		}
		if (!$gSilentErrors) $response['data'] = $er;
		return $response;
	}

	private function return404($er = "") {
		global $api_response_code, $gSilentErrors;
		$response['code'] = 5;
		$response['status'] = NOT_FOUND;
		if ($er == "") {
			$er = $api_response_code[ $response['code'] ]['Message'];
		}
		if (!$gSilentErrors) $response['data'] = $er;
		return $response;
	}


}

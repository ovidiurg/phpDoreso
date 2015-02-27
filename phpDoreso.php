<?php
/**
 * Copyright 2015 Ovidiurg <ovidiurg@gmail.com>
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License.
 * A copy of the Apache License is included in the LICENSE file in this repository.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * User: ovidiu
 * Date: 2/25/15
 * Time: 1:23 PM
 *
 *
 *
 *
 * A Doreso API PHP client library
 * It copies functionality from official Python client pydoreso, https://github.com/doreso/pydoreso
 *
 *  For full API documentation, visit https://developer.doreso.com/.
 *
 * Typical usage:
 *   $doreso =  new Doreso('my api key');
 *   $d = $doreso->song_identify_file('./test.mp3');
 *   echo($d);
*/

	class Doreso
	{
		private $api_key;
		private $base_url = 'http://developer.doreso.com/api/v1';
		private $ffmpeg_path = 'ffmpeg';
		private $response_headers = array();

		public function Doreso($api_key, $base_url= '', $ffmpeg_path= '')
		{
				$this->api_key = $api_key;

				if (!empty($base_url))
					$this->base_url = $base_url;

				if (!empty($ffmpeg_path))
					$this->$ffmpeg_path = $ffmpeg_path;
		}

		public function song_identify_file($filepath, $start = 5, $duration = 20)
		{
			$wav = $this->gen_wav_from_file($filepath, intval($start), intval($duration));

			$d = $this->post_request($this->_url('song/identify'), $wav);

			return($d);
		}

		public function get_response_headers()
		{
			return($this->response_headers);
		}

		private function gen_wav_from_file($filepath, $start, $duration)
		{
			/* 	Using ffmpeg to transcode to wav and dump a fragment to stdout then reading it with popen()
					example command: ffmpeg -i "somefile.mp3"  -ac 1 -ar 8000 -f wav -ss 5 -t 10 -  2>/dev/null
					Warning! This will not work when php safe mode is enabled  */

			$command = escapeshellarg($this->ffmpeg_path) . ' -i ' . escapeshellarg($filepath) . ' -ac 1 -ar 8000 -f wav -ss ' . $start . ' -t ' . $duration. ' - 2>/dev/null';

			$wav = '';

			$phandle = popen($command , 'r');

			while(!feof($phandle))
			{
				$wav .= fread($phandle, 1024);
			}

			pclose($phandle);

	    return($wav);
		}

		private function post_request($url, $data, $header = 'Content-Type: application/octet-stream')
		{
			$data_stream = fopen("php://temp", 'r+');

			fwrite($data_stream,$data);

			rewind($data_stream);

			$request =  curl_init();

			$this->response_headers = array();

			//curl_setopt($request, CURLOPT_VERBOSE, 1);
			curl_setopt($request, CURLOPT_URL, $url);
			curl_setopt($request, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($request, CURLOPT_HEADER, 0);
			curl_setopt($request, CURLOPT_HEADERFUNCTION, array($this,'curl_header_callback'));
			curl_setopt($request, CURLOPT_HTTPHEADER, array($header));
			curl_setopt($request, CURLOPT_INFILE, $data_stream);
			curl_setopt($request, CURLOPT_INFILESIZE, mb_strlen($data, '8bit')); //strlen() cannot be trusted b/c mbstring.func_overload
			curl_setopt($request, CURLOPT_UPLOAD, 1);

			$response = curl_exec($request);

			@curl_close($request);

			@fclose($data_stream);

			return($response);
		}

		private function _url($path)
		{
			return(sprintf("%s/%s?api_key=%s",$this->base_url, $path, $this->api_key));
		}

		private function curl_header_callback($ch, $header_line)
		{
			$bytes_written = mb_strlen($header_line, '8bit');

			$this->response_headers[] = $header_line;

			return($bytes_written);
		}
	}
?>

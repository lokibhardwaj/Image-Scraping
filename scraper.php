<?php
session_start();
function get_images($url){
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	$result = curl_exec($curl);
	if ($result !== false){
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($statusCode == 404){
			return 0;
		} else{
			return $url;
		}
	} else{
		return 0;
	}
}


require_once __DIR__ . "/vendor/autoload.php";

use voku\helper\HtmlDomParser;
if(isset($_POST['main_url']) && !empty($_POST['main_url']) && $_POST['save_data'] == 0){
	$curl = curl_init();
	$main_scrap_url = $_POST['main_url'];
	curl_setopt($curl, CURLOPT_URL, $main_scrap_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/118.0.1 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36");
	$html = curl_exec($curl);
	curl_close($curl);

	$htmlDomParserMain = HtmlDomParser::str_get_html($html);


	// Checking for images
	/*preg_match_all(
	'!https://(.*)/(.*).jpeg!',
		$htmlDomParserMain, $data
	);

	foreach ($data[0] as $list) {
		//echo "<img src='$list'/>";
	}
	print_r($data);
	*/

	//print_r($htmlDomParserMain);
	$images = $htmlDomParserMain->find("img[src]");
	$image_arr = $images->src;
	//print_r($image_arr);
	//print count($image_arr);
	define('DIRECTORY', 'downloaded_images');
	if(is_array($image_arr)){
		for($i=0; $i < count($image_arr); $i++){
			$image_url = get_images($image_arr[$i]);
			if($image_url != 0){
				$image_urls[] = $image_url;
				$image_info = pathinfo($image_url);
				//$image_name = $image_info['basename'];
				$extension = $image_info['extension'];
				$image_name = uniqid().".".$extension;
				$content = file_get_contents($image_url);
				file_put_contents(DIRECTORY . '/'.$image_name, $content);
			}
		}
		//print_r($image_urls);

		// Following lines of code are for web page display and can be commented if you don't want to display anything after processing
		echo '<span id="output-message"></span>';
		echo '<h3>Following images download to server</h3>';
		print "<ul>";
		foreach($image_urls as $arr_key => $value_arr){
			$counter = $arr_key+1;
			print "<li>".$counter.") ".$value_arr."</li>";
		}
		print "</ul>";
	}else{
		print "<ul><li>No Image Found OR Permission Denied!</li></ul>";
	}

}




?>
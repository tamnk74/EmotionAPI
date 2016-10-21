<?php
	//display results after convert from json to array
function displayArr($arr){
	$iarr = 0;
	foreach ($arr as $key => $val) {
		if(is_array($val)) {
			if($iarr>1) {
				echo "}\n";
				$iarr--;
			}
			if($key>0) {
				echo "}\n";
				$iarr--;
			}
			echo "$key{\n";
			$iarr++;
		} else {
			echo "$key : $val\n";
		}
	}
	while ($iarr>0) {
		echo "}\n";
		$iarr--;
	}
}
	//get image url 
if(isset($_POST["submit"]) && !empty($_POST["imgurl"])){
	$url_img = $_POST["imgurl"];
}
else {
	$url_img = "https://portalstoragewuprod.azureedge.net/emotion/recognition1.jpg";
}

// This sample uses the Apache HTTP client from HTTP Components (http://hc.apache.org/httpcomponents-client-ga/)
require_once 'HTTP/Request2.php';

$request = new Http_Request2('https://api.projectoxford.ai/emotion/v1.0/recognize');
$url = $request->getUrl();

$headers = array(
    // Request headers
	'Content-Type' => 'application/json',
	'Ocp-Apim-Subscription-Key' => '413a8e3da516469f9b01e5c4a717d76c',
	);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
	);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_POST);

// Request body
$request->setBody('{"url": "'.$url_img.'"}');
class faceRectangle{
	
}
try
{
	$response = $request->send();
	$jsonIterator = new RecursiveIteratorIterator(
		new RecursiveArrayIterator(json_decode($response->getBody(), TRUE)),
		RecursiveIteratorIterator::SELF_FIRST);
	foreach ($jsonIterator as $key => $val) {
		if(is_array($val)) {
			if(is_int($key)) $index= $key;
			else $obj = $key;
		} else {
			$result[$index][$obj][$key] = $val;
		}
	}
	$arrlength = count($result);
	echo '<html>';
	echo '<head>';
	echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">';
	echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';
	echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("a").click(function(){
				$link_img = $(this).text();
				$("#imgurl").val($link_img);
				
			});
			
			$(".facerec").mouseover(
				function () {
					$(this).find("div").css("display","block");
				}
				);
			$(".facerec").mouseout(
				function () {
					$(this).find("div").css("display","none");
				}
				);
		});
	</script>
	<?php
	echo '</head>';
	echo '<body>';
	echo '<div class="container">';
	echo '<h1> Emotion API demo</h1><hr>';
	echo "<div class='row'style='height: 400px'>";
	echo "<div class='col-md-6' style='position:relative'>";
	echo "<img src='".$url_img."' width='550' height='400px'> ";
	//display faceRectangle
	if(empty($result["error"])){
	$size = getimagesize($url_img);
	for($i=0; $i< $arrlength; $i++){
	echo '<div class="facerec" style="position: absolute; left: '.($result[$i]["faceRectangle"]["left"]*550/$size[0]+15).'; top:'.($result[$i]["faceRectangle"]["top"]*400/$size[1]).'; width: '. ($result[$i]["faceRectangle"]["width"]*550/$size[0]).'; height: '.($result[$i]["faceRectangle"]["height"]*400/$size[1]).';border: 3px solid #73AD21;">';
		echo '<div style="position: absolute; display: none;z-index: 100;background-color: #b3b3b3; padding-left: 5px; padding-top: 5px; left: '.($result[$i]["faceRectangle"]["width"]*550/$size[0]-10).'; top: 0; width: 120; height: 120;">';
		echo '<table style="color: white; font-size: 12;">';
		echo '<tr><td>Anger</td><td>'.number_format($result[$i]["scores"]["anger"],5).'</td>';
		echo '<tr><td>Contempt</td><td>'.number_format($result[$i]["scores"]["contempt"],5).'</td>';
		echo '<tr><td>Disgust</td><td>'.number_format($result[$i]["scores"]["disgust"],5).'</td>';
		echo '<tr><td>Fear</td><td>'.number_format($result[$i]["scores"]["fear"],5).'</td>';
		echo '<tr><td>Happiness</td><td>'.number_format($result[$i]["scores"]["happiness"],5).'</td>';
		echo '<tr><td>Neutral</td><td>'.number_format($result[$i]["scores"]["neutral"],5).'</td>';
		echo '<tr><td>Sadness</td><td>'.number_format($result[$i]["scores"]["sadness"],5).'</td>';
		echo '<tr><td>Surprise</td><td>'.number_format($result[$i]["scores"]["surprise"],5).'</td>';
		echo '</table>';
		echo '</div>';
	echo '</div>';
	}
	}
	else echo $result["error"];
	echo "</div>";
	echo "<div class='col-md-6'>";
	echo "<pre class='form-control' style='height:100%'>";
	echo "JSON:\n";
	displayArr($jsonIterator);
	echo "</pre>";
	echo "</div>";
	echo '<form class="form-group" action="emotion.php" method="post">';
	echo '<div class="col-md-5">';
	echo '<input type="text" class="form-control" name="imgurl" id="imgurl" placeholder="URL of image">';
	echo '</div>';
	echo 	'<button type="submit" class="btn btn-default col-md-1" name="submit">Submit</button>';
	echo  '</form>';
	echo '</div>';
	echo "<p></p><br><hr><h3>Some images demo</h3>";
	echo "<a>https://portalstoragewuprod.azureedge.net/emotion/recognition1.jpg</a><br>";
	echo "<a>https://portalstoragewuprod.azureedge.net/face/demo/detection%205.jpg</a><br>";
	echo "<a>https://portalstoragewuprod.azureedge.net/emotion/recognition2.jpg</a><br>";
	echo "<a>https://portalstoragewuprod.azureedge.net/emotion/recognition3.jpg</a><br>";
	echo "<a>https://portalstoragewuprod.azureedge.net/emotion/recognition4.jpg</a><br>";

	echo '</div><br><br><br>';
}
catch (HttpException $ex)
{
	echo "Input Error";
}
echo '</body>';
echo '</html>';
?>
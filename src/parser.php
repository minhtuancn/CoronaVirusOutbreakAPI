<?php
// Max Base
// https://github.com/BaseMax/CoronaVirusOutbreakAPI
$file=file_get_contents("https://www.worldometers.info/coronavirus/");
file_put_contents("page.html", $file);
$file=file_get_contents("page.html");
// print $file;

function parseData($content) {
	if($content == "" || $content == null) {
		return [];
	}
	// <td style="font-weight: bold; font-size:15px; text-align:left; padding-left:3px;"> USA <\/td> <td style="font-weight: bold; text-align:right">57<\/td> <td style="font-weight: normal; text-align:right;"> <\/td> <td style="font-weight: bold; text-align:right;"> <\/td> <td style="font-weight: bold; text-align:right; "> <\/td> <td style="font-weight: bold; text-align:right">6 <\/td> <td style="font-weight: bold; text-align:right"> <\/td> <td style="font-size:14px; color:#aaa; text-align:right" class="hidden"> N.America <\/td> </tr>
	if(preg_match_all('/<td([^\>]+|)>(\s*|)(<span([^\>]+|)>|)(?<name>[^\<]+)(<\/span>|)(\s*|)<\/td>(\s*|)<td([^\>]+|)>(?<totalCase>[^\<]+)<\/td> <td([^\>]+|)>(?<newCase>[^\<]+)<\/td>(\s*|)<td([^\>]+|)>(?<totalDeath>[^\<]+)<\/td>(\s*|)<td([^\>]+|)>(?<newDeath>[^\<]+)<\/td>(\s*|)<td([^\>]+|)>(?<totalRecovered>[^\<]+)<\/td>(\s*|)<td([^\>]+|)>(?<seriousUser>[^\<]+)<\/td>(\s*|)/i', $content, $matches)) {
		foreach($matches as $key=>$array) {
			if(!is_string($key)) {
				unset($matches[$key]);// To remove extra list, arrays!
			}
			else {
				// Why we do it?
				// we can remove below code if use this example in the regex query
				// e.g: (\s*|)(?<name>[^\<]+)(\s*|)
				// But we did it as below
				// e.g: (?<name>[^\<]+)
				foreach($matches[$key] as $i=>$value) {
					$matches[$key][$i]=trim($value);
				}
			}
		}
		return $matches;
	}
	return [];
}

function prepareData($matches) {
	if(!is_array($matches) || $matches == null) {
		return [];
	}
	$result=[];
	foreach($matches["name"] as $i=>$name) {
		$totalCase=$matches["totalCase"][$i];
		$newCase=$matches["newCase"][$i];
		$totalDeath=$matches["totalDeath"][$i];
		$newDeath=$matches["newDeath"][$i];
		$totalRecovered=$matches["totalRecovered"][$i];
		$seriousUser=$matches["seriousUser"][$i];
		$result[]=[
			"name"=>strtolower($name),
			"totalCase"=>$totalCase,
			"newCase"=>$newCase,
			"totalDeath"=>$totalDeath,
			"newDeath"=>$newDeath,
			"totalRecovered"=>$totalRecovered,
			"seriousUser"=>$seriousUser,
		];
	}
	return $result;
}

$matchs=parseData($file);
$items=prepareData($matchs);
//////////////////////////////////////////////////////////
print_r($items);
//////////////////////////////////////////////////////////
$CREATE_MD_TABLE=true;
$CREATE_JSON=true;
//////////////////////////////////////////////////////////
if($CREATE_MD_TABLE) {
	$table="";
	$table.="| Name | Total Case | New Case | Total Death | New Death | Total Recovered | seriousUser\n";
	$table.="| ---- | --------- | ------- | ---------- | -------- | -------------- | ---------- |\n";
	foreach($items as $item) {
		// name, totalCase, newCase, totalDeath, newDeath, totalRecovered, seriousUser
		$table.="| ".$item["name"]." | ".$item["totalCase"]." | ".$item["newCase"]." | ".$item["totalDeath"]." | ".$item["newDeath"]." | ".$item["totalRecovered"]." | ".$item["seriousUser"]." |\n";
	}
	$table.="\n";
	file_put_contents("output.md", $table);
}
//////////////////////////////////////////////////////////
if($CREATE_JSON) {
	file_put_contents("output.json", json_encode($items));
}
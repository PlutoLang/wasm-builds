<?php
require_once "common.php";

$json = [];
foreach ($repos as $repo => $_)
{
	$json[$repo] = [];
	foreach (scandir("out/".$repo) as $tag)
	{
		if (substr($tag, 0, 1) != ".")
		{
			$version = $tag;
			if (substr($version, 0, 1) == "v")
			{
				$version = substr($version, 1);
			}
			$json[$repo][$version] = "https://plutolang.github.io/wasm-builds/out/$repo/$tag/$repo.js";
		}
	}
}
file_put_contents("manifest.json", json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

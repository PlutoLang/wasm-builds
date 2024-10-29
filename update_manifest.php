<?php
require_once "common.php";

$json = [];
foreach ($repos as $repo => $_)
{
	$json[$repo] = [];
	$tags = scandir("out/".$repo);
	usort($tags, "version_compare");
	foreach ($tags as $tag)
	{
		if (substr($tag, 0, 1) != ".")
		{
			$version = $tag;
			if (substr($version, 0, 1) == "v")
			{
				$version = substr($version, 1);
			}
			$json[$repo][$version] = "https://pluto-lang.org/wasm-builds/out/$repo/$tag/$repo.js";
		}
	}
}
file_put_contents("manifest.json", json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

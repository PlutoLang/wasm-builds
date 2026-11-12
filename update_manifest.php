<?php
$json = [];
foreach (["pluto", "lua"] as $runtime)
{
	$versions = scandir($runtime);
	usort($versions, "version_compare");
	foreach ($versions as $version)
	{
		if (substr($version, 0, 1) != ".")
		{
			foreach (scandir("$runtime/$version") as $file)
			{
				if (substr($file, -5) == ".wasm")
				{
					$binary = substr($file, 0, -5);
					$json[$binary] ??= [];
					$json[$binary][$version] = "https://pluto-lang.org/wasm-builds/$runtime/$version/$binary.js";
				}
			}
		}
	}
}
file_put_contents("manifest.json", json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

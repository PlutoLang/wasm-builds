<?php
if (empty($argv))
{
	die("CLI usage only!");
}

$repo = $argv[1];
$tag = $argv[2];

$repos = [
	"pluto" => "https://github.com/PlutoLang/Pluto",
	"lua" => "https://github.com/lua/lua",
];

if (!array_key_exists($repo, $repos))
{
	die("Unknown repo: $repo");
}

// Checkout repo
if (!is_dir("repos"))
{
	mkdir("repos");
}
if (!is_dir("repos/".$repo))
{
	passthru("cd repos && git clone ".$repos[$repo]." ".$repo);
}

// Checkout tag
passthru("cd repos/".$repo." && git checkout ".$tag);

// Build
passthru("cd repos/".$repo." && php ../../build/".$repo.".php");

// Deploy
mkdir("out/".$repo."/".$tag);
copy("repos/".$repo."/".$repo.".js", "out/".$repo."/".$tag."/".$repo.".js");
copy("repos/".$repo."/".$repo.".wasm", "out/".$repo."/".$tag."/".$repo.".wasm");

// Clean up
passthru("rm -r repos/".$repo."/bin");

// Update manifest
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
			$json[$repo][$version] = "https://wasm.pluto.do/out/$repo/$tag/$repo.js";
		}
	}
}
file_put_contents("manifest.json", json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


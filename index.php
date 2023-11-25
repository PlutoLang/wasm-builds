<?php
if (empty($argv))
{
	die("CLI usage only!");
}

$repo = $argv[1];
$tag = $argv[2];

require_once "common.php";

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
require "update_manifest.php";

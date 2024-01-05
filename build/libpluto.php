<?php
$clang = "em++ -O3 -flto -std=c++17 -fvisibility=hidden -D LUA_USE_LONGJMP -D PLUTO_ILP_ENABLE -D PLUTO_USE_SOUP -D PLUTO_C_LINKAGE";

// Setup folders
if(!is_dir("bin"))
{
	mkdir("bin");
}
if(!is_dir("bin/int"))
{
	mkdir("bin/int");
}

// Find work
$files = [];
foreach(scandir("src/vendor/Soup/soup") as $file)
{
	if(substr($file, -4) == ".cpp")
	{
		$name = substr($file, 0, -4);
		array_push($files, "vendor/Soup/soup/".$name);
	}
}
foreach(scandir("src") as $file)
{
	if(substr($file, -4) == ".cpp")
	{
		$name = substr($file, 0, -4);
		if($name != "lua" && $name != "luac")
		{
			array_push($files, $name);
		}
	}
}

echo "Compiling...\n";
$objects = [];
foreach($files as $file)
{
	echo $file."\n";
	passthru("$clang -c src/$file.cpp -o bin/int/".basename($file).".o");
	array_push($objects, escapeshellarg("bin/int/".basename($file).".o"));
}

echo "Linking...\n";
$clang .= " -s WASM=1 -s MODULARIZE=1 -s EXPORT_NAME=libpluto -s EXPORTED_RUNTIME_METHODS=[\"FS\",\"cwrap\"] -s FS_DEBUG=1 -s FETCH=1";
$clang .= " -s ALLOW_MEMORY_GROWTH=1 -s ABORTING_MALLOC=0"; // to correctly handle memory-intensive tasks
//$clang .= " -s LINKABLE=1 -s EXPORT_ALL=1 -s ASSERTIONS=1"; // uncomment for debugging
passthru("$clang -o libpluto.js ".join(" ", $objects));

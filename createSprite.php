<?php
class createSprite
{
	public $directory;
	public $fileArray = array();
	public $imageName;
	public $styleName;

	function __construct($directory, $styleName = "style.css", $imageName = "sprite.png")
	{
		global $argc;
		$this -> arr = $directory;
		$this -> argc = $argc;
		$this -> styleName = $styleName;
		$this -> imageName = $imageName;
        }

	private function scanFile($dir)
	{
		global $fileArray;
		$array = glob($dir . '/*');

		if (is_array($array))
		{
			foreach($array as $key => $file)
			{
				if (is_dir($file))
				{
					$this -> scanFile($file);
				}
				elseif(is_file($file))
				{
					if(strstr($file, ".png"))
					{
						array_push($this -> fileArray, $array[$key]);
					}
				}
			}
		}
	}

	private function stylesheetGenerate($name, $nameFile, $width, $height, $color)
	{
		$fileName = fopen($name, "w+");

		foreach($nameFile as $key => $value)
		{
			fwrite($fileName,
			".$nameFile[$key]
			{
				background-color: $color;
				width: $width[$key];
				height: $height[$key];
			}
			");
		}
	}

	public function recursiveGetImage()
	{
		$getInfo = array();

		if($this -> argc > 2)
		{
			if(is_dir($this -> arr))
			{
				$this -> scanFile($this -> arr);
			}

			if(!empty($this -> fileArray))
			{
				$all_width = array();
				$onlyName = array();
				$all_height = array();
				$w = 0;
				$red = 255;
				$green = 255;
				$blue = 255;

				foreach($this -> fileArray as $key => $value)
				{
					if(mime_content_type($this -> fileArray[$key]) != "image/png")
					{
						echo "Ton fichier " . $this -> fileArray[$key] . " est corrompu, nous ne pouvons continuez le processus. Supprimer le ou déplacer le. \n";
						return 0;
					}
					array_push($getInfo, getimagesize($this -> fileArray[$key]));
					array_push($onlyName, pathinfo($this -> fileArray[$key], PATHINFO_FILENAME));
				}

				foreach($getInfo as $key => $value)
				{
					array_push($all_height, $getInfo[$key][1]);
					array_push($all_width, $getInfo[$key][0]);
				}

				$allHeightValue = array_sum($all_height);
				$allWidthValue = array_sum($all_width);
				$truecolor = imagecreatetruecolor($allWidthValue, max($all_height));
				$white = imagecolorallocate($truecolor, $red, $green, $blue);
				$rgba = "rgba($red, $green, $blue)";
				imagefill($truecolor, 0, 0, $white);

				for($key = 0; $key < count($this -> fileArray); $key++)
				{
					list($width, $height) = getimagesize($this -> fileArray[$key]);
					$other_img = imagecreatefromstring(file_get_contents($this -> fileArray[$key]));
					imagecopy($truecolor, $other_img, $w, 0,0,0, $width, $height);
					$w += $all_width[$key];
				}

				if($this -> imageName == "sprite.png")
				{
					imagepng($truecolor, $this -> imageName);
				}
				else
				{
					if(pathinfo($this -> imageName, PATHINFO_EXTENSION) == "png")
					{
						imagepng($truecolor, $this -> imageName);
					}
					else
					{
						echo "Remplacer l'extension " . pathinfo($this -> imageName, PATHINFO_EXTENSION) . " par PNG.\n";
						return 0;
					}
				}

				if ($this -> styleName == "style.css")
				{
					$this -> stylesheetGenerate($this -> styleName, $onlyName, $all_width, $all_height, $rgba);
				}
				else
				{
					if(pathinfo($this -> styleName, PATHINFO_EXTENSION) == "css")
					{
						$this -> stylesheetGenerate($this -> styleName, $onlyName, $all_width, $all_height, $rgba);
					}
					else
					{
						echo "Remplacer l'extension " . pathinfo($this -> styleName, PATHINFO_EXTENSION) . " par CSS.\n";
						return 0;
					}
				}
			}
			else
			{
				echo "Le dossier est vide ou ne contient pas de png.\n";
				return 0;
			}
		}
		else
		{
			echo "Entrez plusieurs fichiers";
		}
	}

	public static function help()
	{
		echo "/* CREATE SPRITE */
		NAME css_generator - sprite generator for HTML use

		SYNOPSIS
		createSprite.php [-r directory] [-i FileName] [-s FileName] [--recursive directory] [--output-image FileName] [--output-style FileName]

		DESCRIPTION
		Concatenate all images inside a folder in one sprite and write a style sheet ready to use.
		Mandatory arguments to long options are mandatory for short options too.

		-r, --recursive: Look for images into the assets_folder passed as arguement and all of its subdirectories.
		-i, --output-image=IMAGE: Name of the generated image. If blank, the default name is « sprite.png ».
		-s, --output-style=STYLE: Name of the generated stylesheet. If blank, the default name is « style.css ».
		--help: List all command.\n";
	}
}

	function option()
	{
		global $argv;

		$longopts = array(
			"recursive:",
			"output-image:",
			"output-style:",
			"help",
		);

		$opt = getopt("r:i:s:", $longopts);

		if(isset($opt["recursive"]))
		{
			$opt["r"] = $opt["recursive"];
		}

		if(isset($opt["output-image"]))
		{
			$opt["i"] = $opt["output-image"];
		}

		if(isset($opt["output-style"]))
		{
			$opt["s"] = $opt["output-style"];
		}

		if(isset($opt["help"]))
		{
			$opt["h"] = $opt["help"];
		}

		if(isset($opt["r"]) && isset($opt["s"]) && isset($opt["i"]))
		{
			$sprite = new createSprite($opt["r"], $opt["s"], $opt["i"]);
			$sprite -> recursiveGetImage();
		}
		elseif(isset($opt["r"]) && isset($opt["s"]))
		{
			$sprite = new createSprite($opt["r"], $opt["s"]);
			$sprite -> recursiveGetImage();
		}
		elseif(isset($opt["r"]) && isset($opt["i"]))
		{
			$sprite = new createSprite($opt["r"], "style.css", $opt["i"]);
			$sprite -> recursiveGetImage();
		}
		elseif(isset($opt["r"]))
		{
			$sprite = new createSprite($opt["r"]);
			$sprite -> recursiveGetImage();
		}
		elseif(isset($argv[1]) && isset($opt["s"]) && isset($opt["i"]))
		{
			$sprite = new createSprite($argv[1], $opt["s"], $opt["i"]);
			$sprite -> recursiveGetImage();
		}
		elseif(isset($argv[1]) && isset($opt["i"]))
		{
			$sprite = new createSprite($argv[1], $opt["i"]);
			$sprite -> recursiveGetImage();
		}
		elseif(isset($argv[1]) && isset($opt["s"]))
		{
			$sprite = new createSprite($argv[1], $opt["s"]);
			$sprite -> recursiveGetImage();
		}
		elseif(isset($opt["help"]))
		{
			createSprite::help();
		}
		else
		{
			echo "Entrez un dossier ou regardez les options avec --help.\n";
		}
	}

	option();
?>

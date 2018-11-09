<?php

    class createSprite
    {
        public $array;

        function __construct($array)
        {
            global $argc;
            $this -> arr = $array;
            $this -> argc = $argc;
        }

        public function getImage()
        {
            $getInfo = array();

            if($this -> argc > 2)
            {      
                $all_width = array();
                $all_height = array();
                $w = 0;

                for($j = 1; $j  < $this -> argc; $j++)
                {
                    array_push($getInfo, getimagesize($this -> arr[$j]));
                }

                foreach($getInfo as $key => $value)
                {
                    array_push($all_height, $getInfo[$key][1]);
                    array_push($all_width, $getInfo[$key][0]);
                }
                $allHeightValue = array_sum($all_height);
                $allWidthValue = array_sum($all_width);

                $truecolor = imagecreatetruecolor($allWidthValue, $allHeightValue);

                for($i = 0; $i < $this -> argc - 1; $i++)
                {
                    list($width, $height) = getimagesize($this -> arr[$i + 1]);
                    $other_img = imagecreatefromstring(file_get_contents($this -> arr[$i + 1]));
                    imagecopy($truecolor, $other_img, $w, 0, 0, 0, $width, $height);
                    $w += $all_width[$i];
                }
                imagepng($truecolor, "copy.png");
            }
            else
            {
                echo "Entrez plusieurs fichiers";
            }
        }

        function recursiveGetImage()
        {
            if($this -> argc > 2)
            {       
                    function scanFile($dir, &$fileArray) 
                    {
                        $array = glob($dir . '/*');

                        if (is_array($array)) 
                        {
                            foreach($array as $key => $file) 
                            {
                                if (is_dir($file)) 
                                {
                                    scanFile($file);
                                    //echo $file;
                                }
                                elseif(is_file($file)) 
                                {
                                   array_push($fileArray, $array[$key]);
                                }
                            }
                        }
                    }

                    for($i = 1; $i < $this -> argc; $i++)
                    {
                        if(is_dir($this -> arr[$i]))
                        {
                            scanFile($this -> arr[$i]);
                        }
                    }
                    var_dump($fileArray);

               /*$all_width = array();
                $all_height = array();
                $w = 0;

                for($j = 1; $j  < $this -> argc; $j++)
                {
                    array_push($getInfo, getimagesize($this -> arr[$j]));
                }

                foreach($getInfo as $key => $value)
                {
                    array_push($all_height, $getInfo[$key][1]);
                    array_push($all_width, $getInfo[$key][0]);
                }
                $allHeightValue = array_sum($all_height);
                $allWidthValue = array_sum($all_width);

                $truecolor = imagecreatetruecolor($allWidthValue, $allHeightValue);

                for($i = 0; $i < $this -> argc - 1; $i++)
                {
                    list($width, $height) = getimagesize($this -> arr[$i + 1]);
                    $other_img = imagecreatefromstring(file_get_contents($this -> arr[$i + 1]));
                    imagecopy($truecolor, $other_img, $w, 0, 0, 0, $width, $height);
                    $w += $all_width[$i];
                }
                imagepng($truecolor, "copy.png");*/
            }
            else
            {
                echo "Entrez plusieurs fichiers";
            }
        } 
        
    }

    if($argv[1] == "-r" || $argv[1] == "--recursive")
    {           
        $foo = new createSprite($argv);
        $foo -> recursiveGetImage();
    }
    /*else
    {
        $foo = new createSprite($argv);
        $foo -> getImage();
    }*/
?>
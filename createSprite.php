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
            if($this -> argc > 1)
            {
                $getInfo = array();
                
                $barcode = imagecreatefromstring(file_get_contents($this -> arr[1]));
                for($i = 1; $i < $this -> argc; $i++)
                {
                    $barcode2 = imagecreatefromstring(file_get_contents($this -> arr[$i]));
                    //array_push($getInfo, getimagesize($this -> arr[$i]));
                    array_push($getInfo, getimagesize($this -> arr[$i]));
                    /*var_dump($this -> arr[$i]);
                    $sprite = imagecreatefrompng($this -> arr[1]);
                    $dest = imagecreatetruecolor(1920, 1080);
                    imagecopy($dest, $sprite, 0, 0, 0, 0, 80, 20);*/
                    imagecopymerge($barcode, $barcode2,  10, 10, 0, 50, 1000, 470, 750);
                }
                var_dump($this -> arr[1]);
            }
            else
            {
                echo "Entrez Des fichiers";
            }
            //var_dump($getInfo);

            /*for($j = 0; $j < $this -> argc - 1; $j++)
            {
                $sprite = "sprite.png";
                $dest = imagecreatetruecolor(1920, 1080);
                //imagecopy($dest);
            }*/
        }
    }

    $foo = new createSprite($argv);
    $foo -> getImage();
?>
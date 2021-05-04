<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    trait AC_Dashboard_extra {

        function clear_cache($dir){

            $files = array_diff(scandir($dir), array('.', '..'));

            foreach ($files as $file){

                if (is_dir($dir.'/'.$file)){

                    $this->clear_cache($dir.'/'.$file);

                }else {

                    if (!file_exists($dir.'/.gitignore')){
                        unlink($dir.'/'.$file);
                    }
                }
            }
        }
    }

?>
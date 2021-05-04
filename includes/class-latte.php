<?php

/**
 * @author    Jakub Habaj
 * @copyright 2020-2021 Jakub Habaj
 * @license   GNU General Public License version 3; see LICENSE.txt
 */


    require_once 'latte/autoload.php';
    require_once 'utils/autoload.php';

    trait AC_Latte{

        public $latte;
        public $latte_parameters;

        function latte_load(){

            $this->latte = new Latte\Engine;

        }

        function latte_html($content){

            $latte = clone $this->latte;
            $latte->setLoader(new Latte\Loaders\StringLoader);

            $return = new Latte\Runtime\Html($latte->renderToString($content));
            return $return;
        }

        function latte_html_nt($latte_parameters){

            $latte = clone $this->latte;

            $latte->setLoader(new Latte\Loaders\StringLoader([
                'content' => '{$content}',
            ]));

            $latte->render('content', $latte_parameters);
        }
    }

?>
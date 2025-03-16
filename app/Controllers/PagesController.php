<?php

namespace App\Controllers;

class PagesController extends BaseController{
    public function aboutAction(){
        return $this->renderHTML('../views/about_view.php');
    }
}
<?php

namespace MonMarsupilami\MonMarsupilamiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MonMarsupilamiBundle:Default:index.html.twig');
    }
}

<?php

namespace Rz\RedirectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RedirectController extends Controller
{

    public function redirectAction(Request $request)
    {
        $url = $request->get('url');
        return $this->redirect($url, 301);
    }
}

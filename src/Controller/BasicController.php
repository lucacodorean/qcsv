<?php

namespace Src\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class BasicController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response {
        return new Response('It works!');
    }
}

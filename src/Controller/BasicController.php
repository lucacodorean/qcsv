<?php

namespace Src\Controller;

use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends AbstractController {

    public function __construct(
        #[Autowire('%env(HOME_PATH)%')] private readonly string $homePath
    ) {
        ///
    }

    #[Route('/', name: 'homepage')]
    public function index(): Response {
        return new Response('It works!');
    }

    /**
     * @throws RandomException
     */
    #[Route('/go', name: 'redirect')]
    public function go(): Response {
        sleep(random_int(1, 3));

        return $this->redirect(
            $this->homePath,
        );
    }
}

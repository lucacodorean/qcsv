<?php

namespace Src\Controller;

use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/go/{secondsMultiplier}', name: 'redirect')]
    public function go(int $secondsMultiplier): Response {
        $time = $secondsMultiplier * random_int(1, 3);
        sleep($time);

        return $this->redirect(
            $this->homePath,
        );
    }

    #[Route("/print-request", name: 'print-request')]
    public function printRequest(Request $request): Response {
        return $this->json(
            data: [
                "host" => $request->getHost(),
                "port" => $request->getPort(),
                "method" => $request->getMethod(),
                "path" => $request->getPathInfo(),
                "headers" => $request->headers->all(),
            ],
            context: [
            'json_encode_options' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ]);
    }

}

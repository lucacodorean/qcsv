<?php

namespace Src\Controller;

use Random\RandomException;
use Src\CommandLogic\SelectCommandLogic;
use Src\Domain\LazyDataTable;
use Src\Dto\DataTableSelectRequest;
use Src\Exceptions\InvalidParametersException;
use Src\Services\IO\JsonWriter;
use Src\Services\IO\ReadServiceImpl;
use Src\Utils\SelectCondition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BasicController extends AbstractController {

    public function __construct(
        #[Autowire('%env(HOME_PATH)%')] private readonly string $homePath,
        private readonly ReadServiceImpl $readService,
        private readonly JsonWriter $jsonWriter,
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

    #[Route("/run-select", name: 'select', methods: ['POST'])]
    public function select(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] DataTableSelectRequest $request
    ): StreamedResponse  {
        $tmpPath = tempnam(sys_get_temp_dir(), 'datatable_');
        $stream = fopen($tmpPath, 'w');
        fwrite($stream, $request->tableData);
        fclose($stream);

        $rules = [];
        foreach ($request->conditionals as $condition) {
            $rules[] =  SelectCondition::fromOption($condition);
        }

        $dataTable = new LazyDataTable($this->readService->lazyRead($tmpPath));
        $command = new SelectCommandLogic($request->columns, $rules);

        try{
            $responseDataTable = $command->execute($dataTable);
            unlink($tmpPath);
            return new StreamedResponse(function() use ($responseDataTable) {
                $this->jsonWriter->toStream($responseDataTable, "php://output");
            });

        } catch (InvalidParametersException $e) {
            throw new HttpInvalidParamException($e->getMessage(), 400);
        }
    }

}

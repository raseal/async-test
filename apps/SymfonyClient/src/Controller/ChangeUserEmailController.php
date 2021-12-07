<?php

declare(strict_types=1);

namespace SymfonyClient\Controller;

use Shared\Domain\Exception\InvalidUuid;
use Shared\Infrastructure\Symfony\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TestCase\User\Application\Change\ChangeUserEmailCommand;
use TestCase\User\Domain\Exception\UserDoesNotExist;

final class ChangeUserEmailController extends ApiController
{
    public function __invoke(string $id, Request $request): Response
    {
        $payload = $this->getPayload($request);

        $this->dispatch(
            new ChangeUserEmailCommand(
                $id,
                $payload['email']
            )
        );

        return $this->createApiResponse(null, Response::HTTP_CREATED);
    }

    protected function exceptions(): array
    {
        return [
            InvalidUuid::class => Response::HTTP_BAD_REQUEST,
            UserDoesNotExist::class => Response::HTTP_BAD_REQUEST,
        ];
    }
}

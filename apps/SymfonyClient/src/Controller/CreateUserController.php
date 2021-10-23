<?php

declare(strict_types=1);

namespace SymfonyClient\Controller;

use Shared\Domain\Exception\InvalidUuid;
use Shared\Domain\ValueObject\Uuid;
use Shared\Infrastructure\Symfony\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TestCase\User\Application\Create\CreateUserCommand;

final class CreateUserController extends ApiController
{
    public function __invoke(Request $request): Response
    {
        $id = Uuid::random()->value();
        $payload = $this->getPayload($request);

        $this->dispatch(
            new CreateUserCommand(
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
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Notification\UI\Controller;

use App\Notification\Application\Factory\SendNotificationCommandFactory;
use App\Notification\Domain\Contract\NotificationRepositoryInterface;
use App\Notification\UI\Factory\NotificationRequestFactory;
use App\Notification\UI\Factory\NotificationResponseFactory;
use App\Notification\UI\Factory\PaginationResponseFactory;
use App\Notification\UI\Request\NotificationRequest;
use App\Notification\UI\Response\NotificationResponse;
use App\Notification\UI\Response\PaginationResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

final readonly class NotificationController
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private NotificationRequestFactory $requestFactory,
        private SendNotificationCommandFactory $commandFactory,
        private NotificationResponseFactory $responseFactory,
        private ValidatorInterface $validator,
        private MessageBusInterface $bus,
    ) {
    }

    #[OA\Post(
        path: '/api/notifications',
        summary: 'Send a notification',
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                'application/json' => new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(ref: new Model(type: NotificationRequest::class))
                ),
            ]
        ),
        tags: ['Notifications'],
        responses: [
            new OA\Response(
                response: Response::HTTP_ACCEPTED,
                description: 'Notification queued',
                content: [
                    'application/json' => new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: 'id',
                                    description: 'Notification ID',
                                    type: 'string',
                                    example: '550e8400-e29b-41d4-a716-446655440000'
                                ),
                            ],
                            type: 'object'
                        )
                    ),
                ]
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Invalid request data',
                content: [
                    'application/json' => new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: 'errors',
                                    type: 'object',
                                    example: [
                                        'body' => ['Invalid request data: malformed JSON body'],
                                    ],
                                    additionalProperties: new OA\AdditionalProperties(
                                        type: 'array',
                                        items: new OA\Items(type: 'string')
                                    )
                                ),
                            ],
                            type: 'object'
                        )
                    ),
                ]
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation failed',
                content: [
                    'application/json' => new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(
                            properties: [
                                new OA\Property(
                                    property: 'errors',
                                    type: 'object',
                                    example: [
                                        'channel' => [
                                            'Channel is not valid; it must be one of: "email", "sms", "push"',
                                        ],
                                        'content' => ['Content is required'],
                                        'subject' => ['Subject is required'],
                                        'to' => ['Recipient identifier must be a valid email address'],
                                    ],
                                    additionalProperties: new OA\AdditionalProperties(
                                        type: 'array',
                                        items: new OA\Items(type: 'string')
                                    )
                                ),
                            ],
                            type: 'object'
                        )
                    ),
                ]
            ),
        ]
    )]
    #[Route(
        path: '/api/notifications',
        name: 'notifications.send',
        requirements: ['_format' => 'json'],
        defaults: ['_format' => 'json'],
        methods: ['POST']
    )]
    public function send(Request $request): JsonResponse
    {
        try {
            $payload = $request->getPayload();
        } catch (JsonException) {
            return new JsonResponse(
                ['errors' => ['body' => ['Invalid request data: malformed JSON body']]],
                Response::HTTP_BAD_REQUEST
            );
        }

        $notificationRequest = $this->requestFactory->makeFromRequestData($payload->all());
        $violations = $this->validator->validate($notificationRequest);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $property = $violation->getPropertyPath();
                $errors[$property][] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $command = $this->commandFactory->createFromNotificationRequest($notificationRequest);

        $this->bus->dispatch($command);

        return new JsonResponse(['id' => $command->notificationId], Response::HTTP_ACCEPTED);
    }

    #[OA\Get(
        path: '/api/notifications',
        summary: 'List notifications with pagination and sorting',
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1),
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Number of notifications per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 20),
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Order of notifications (asc or desc)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', default: 'desc', enum: ['asc', 'desc']),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Paginated notifications list',
                content: new OA\JsonContent(
                    ref: new Model(type: PaginationResponse::class)
                )
            ),
        ]
    )]
    #[Route(path: '/api/notifications', name: 'notifications.list', methods: ['GET'])]
    public function list(Request $request, PaginationResponseFactory $paginationFactory): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 20));
        $order = strtoupper((string) $request->query->get('order', 'desc'));

        return new JsonResponse(
            $paginationFactory->create(
                total: $this->repository->getTotal(),
                page: $page,
                limit: $limit,
                entities: $this->repository->list($page, $limit, $order)
            )
        );
    }

    #[OA\Get(
        path: '/api/notifications/{id}',
        summary: 'Get notification by ID',
        tags: ['Notifications'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Notification found',
                content: [
                    'application/json' => new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(ref: new Model(type: NotificationResponse::class))
                    ),
                ]
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Notification not found'
            ),
        ]
    )]
    #[Route(path: '/api/notifications/{id}', name: 'notifications.show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
        } catch (Throwable) {
            throw new NotFoundHttpException('Invalid UUID');
        }

        $notification = $this->repository->findById($uuid);
        if (!$notification) {
            throw new NotFoundHttpException('Notification not found');
        }

        return new JsonResponse($this->responseFactory->makeFromEntity($notification));
    }
}

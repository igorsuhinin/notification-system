# Async API Notification Service

A Symfony-based microservice for queuing and dispatching notifications via multiple channels (email, SMS, push). Built with asynchronous message handling using Messenger & RabbitMQ, and observability powered by Loki and Grafana.

## Table of Contents

- [Features](#features)
- [Quick Start](#quick-start)
- [Tech Stack](#tech-stack)
- [Architecture Overview](#architecture-overview)
- [Key Components](#key-components)
- [Message Flow](#message-flow)
- [Queue Setup](#queue-setup)
- [Requirements](#requirements)
- [Installation](#installation)
- [Available Commands](#available-commands)
- [API Documentation](#api-documentation)
- [Logging](#logging)
- [To Do](#to-do)
- [License](#license)

## Features

- REST API endpoints for notifications (create, list, get)
- Command Handler for asynchronous processing with RabbitMQ
- Domain Events for system state changes
- Multiple delivery channels support
- OpenAPI documentation at `/api/doc` or in [open-api.json](open-api.json) file
- Persistence using PostgreSQL and Doctrine ORM
- Structured JSON logging to stderr
- Promtail + Loki + Grafana logging pipeline

## Quick Start
```bash
git clone https://github.com/igorsuhinin/notification-system.git
cd notification-system
cp app/.env.example app/.env
docker compose up -d
make composer-install
make migrate
make consume
```

Send your first notification:

```bash
curl --location 'http://localhost/api/notifications' \
--header 'Content-Type: application/json' \
--data '{
    "to": "user@example.com",
    "subject": "Test Subject",
    "content": "Hello!",
    "channel": "email"
}
```

## Tech Stack

- PHP-FPM 8.4
- Symfony 7
- RabbitMQ 4
- PostgreSQL 17
- Docker & Docker Compose
- Nginx 1.28
- Promtail 2.7 / Loki 3.5 / Grafana 11

## Architecture Overview

### Layers

- **UI (Controller)**: REST endpoints, request validation
- **Application**: Command/handler logic, orchestrates domain and infrastructure
- **Domain**: Business logic, value objects, entity aggregates
- **Infrastructure**: DB (Doctrine), sending service integrations (email, SMS)

## Key Components

- `NotificationController` - handles API endpoints
- `SendNotificationCommand` - encapsulates send intent
- `SendNotificationHandler` - handles message, chooses channel
- `NotificationEntity` - persisted aggregate, state transitions
- `NotificationRepository` - Doctrine repository for notifications
- `NotificationEvent` - domain event for state changes
- `StubEmailChannelSender` - example channel sender for email notifications

**Note**: The `push` channel is intentionally left unimplemented. If selected, notifications will fail, be retried up to 3 times, and then moved to the dead-letter queue.

## Message Flow

```
POST /api/notifications
→ controller
→ DTO validated
→ SendNotificationCommand dispatched (Messenger)
→ handled asynchronously in SendNotificationHandler
→ persisted
→ channel.send(...)
→ NotificationSentEvent dispatched
→ NotificationMarkedDeliveredListener updates state
```

## Queue Setup

- RabbitMQ `notifications` exchange
- Retry strategy (3x attempts, 1-second delay)
- Failure transport = Doctrine (dead-letter)

## Requirements

- Docker
- Docker Compose

## Installation

1. Clone the repository:
```bash
git clone https://github.com/igorsuhinin/notification-system.git
cd notification-system
```

2. Start the Docker containers:

Create a `.env` file in the `app` directory by copying the example file.

```bash
cp app/.env.example app/.env
```

It's not required but if you want to use a different environment file, copy the desired `.env` file from `.env.dist` to `.env` before starting the containers.

3. Start the Docker containers:

```bash
docker compose up --build
```

This will start the PHP, Nginx, RabbitMQ, PostgreSQL, Promtail, Loki, and Grafana containers.

4. Install Composer dependencies (_optional_):

The composer dependencies will be installed automatically during the container startup. If you want to install them manually, you can run:

```bash
make composer-install
```

5. Run database migrations:

```bash
make migrate
```

6. Start the message consumers:

```bash
make consume
```

## Available Commands

### Docker

- `make up` - Start Docker containers
- `make down` - Stop Docker containers
- `make cli` - Access PHP container's bash shell

### Development

- `make composer-install` - Install PHP dependencies
- `make migrate` - Run database migrations
- `make consume` - Run message consumers

### Quality Tools

- `make phpstan` - Run PHPStan static analysis (level 7)
- `make psalm` - Run Psalm static analysis
- `make phpcs` - Check PSR-12 coding standards
- `make phpcs-fixer-check` - Run PHP CS Fixer check
- `make test` - Run PHPUnit tests

## API Documentation

OpenAPI documentation is available at `/api/doc` after starting the service or in [open-api.json](open-api.json) file in the root directory.

## Logging

- Symfony logs to `/var/log/symfony/app.log`
- Promtail configured in `/etc/promtail/promtail-config.yaml` to read log file via `tmpfs` mount
- Grafana runs on port `3000`
- Loki runs on port `3100`

## To Do

- Add authentication and authorization to the API endpoints using JWT

## License

This project is licensed under the [MIT License](LICENSE).

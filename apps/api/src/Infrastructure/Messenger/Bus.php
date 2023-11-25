<?php

declare(strict_types=1);

namespace Infrastructure\Messenger;

use Domain\BusInterface;
use Domain\Command\CommandInterface;
use Domain\Event\EventInterface;
use Domain\Query\QueryInterface;
use LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class Bus implements BusInterface
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly MessageBusInterface $eventBus,
        private readonly MessageBusInterface $queryBus,
    ) {
    }

    public function ask(QueryInterface $message): mixed
    {
        return $this->handle($message, $this->queryBus)->getResult();
    }

    public function emit(EventInterface $event): void
    {
        $this->handle($event, $this->eventBus);
    }

    public function dispatch(CommandInterface $command): void
    {
        $this->handle($command, $this->commandBus);
    }

    private function handle(object $payload, MessageBusInterface $bus): HandledStamp
    {
        $envelope = $bus->dispatch($payload);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if ($handledStamps === []) {
            throw new LogicException(sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__));
        }

        if (\count($handledStamps) > 1) {
            $handlers = implode(', ', array_map(fn (HandledStamp $stamp): string => sprintf('"%s"', $stamp->getHandlerName()), $handledStamps));

            throw new LogicException(sprintf('Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__, \count($handledStamps), $handlers));
        }

        return $handledStamps[0];
    }
}

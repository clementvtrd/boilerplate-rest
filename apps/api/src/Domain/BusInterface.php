<?php

declare(strict_types=1);

namespace Domain;

use Domain\Command\CommandInterface;
use Domain\Event\EventInterface;
use Domain\Query\QueryInterface;

interface BusInterface
{
    public function ask(QueryInterface $query): mixed;
    public function emit(EventInterface $event): void;
    public function dispatch(CommandInterface $command): void;
}

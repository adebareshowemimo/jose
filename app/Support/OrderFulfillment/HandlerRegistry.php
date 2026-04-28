<?php

namespace App\Support\OrderFulfillment;

class HandlerRegistry
{
    /**
     * Map of orderable_type FQCN => OrderableHandler FQCN.
     */
    protected array $map = [];

    public function register(string $orderableType, string $handlerClass): void
    {
        $this->map[$orderableType] = $handlerClass;
    }

    public function resolve(string $orderableType): ?OrderableHandler
    {
        $class = $this->map[$orderableType] ?? null;
        if (! $class) {
            return null;
        }
        return app($class);
    }

    public function has(string $orderableType): bool
    {
        return isset($this->map[$orderableType]);
    }

    public function all(): array
    {
        return $this->map;
    }
}

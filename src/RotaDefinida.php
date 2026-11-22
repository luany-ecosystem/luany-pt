<?php

namespace Luany\Pt;

use Luany\Core\Routing\RouteRegistrar;

/**
 * Uma rota acabada de registar: ->nome('produtos.index')->middleware(...).
 */
final class RotaDefinida
{
    public function __construct(public readonly RouteRegistrar $rota)
    {
    }

    public function nome(string $nome): self
    {
        $this->rota->name($nome);
        return $this;
    }

    public function middleware(mixed $middleware): self
    {
        $this->rota->middleware($middleware);
        return $this;
    }

    /** Nome inglês, para quem mistura. */
    public function name(string $name): self
    {
        return $this->nome($name);
    }
}

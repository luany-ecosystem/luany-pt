<?php

namespace Luany\Pt;

use Luany\Core\Routing\RouteGroup;

/**
 * Rota::prefixo('/admin')->middleware(Autenticado::class)->grupo(fn () => ...).
 */
final class GrupoRotas
{
    public function __construct(public readonly RouteGroup $grupo)
    {
    }

    public function prefixo(string $prefixo): self
    {
        $this->grupo->prefix($prefixo);
        return $this;
    }

    public function middleware(mixed $middleware): self
    {
        $this->grupo->middleware($middleware);
        return $this;
    }

    public function grupo(callable $rotas): void
    {
        $this->grupo->group($rotas);
    }
}

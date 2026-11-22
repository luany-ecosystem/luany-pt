<?php

namespace Luany\Pt;

use Luany\Database\Model;
use Luany\Database\ModelQuery;
use Luany\Database\PaginationResult;

/**
 * Consulta — a ModelQuery com nomes em português.
 *
 *   Produto::consulta()
 *       ->onde('ativo', '=', 1)
 *       ->ondeTem('categoria')
 *       ->ordenarPor('nome')
 *       ->com('categoria')
 *       ->paginar(20);
 *
 * Os métodos ingleses continuam disponíveis e devolvem a mesma Consulta.
 * Scopes: escopoAtivos(Consulta $c) no modelo → ->ativos().
 *
 * @template TModel of Model
 * @extends ModelQuery<TModel>
 */
class Consulta extends ModelQuery
{
    public function onde(string $coluna, string $operador, mixed $valor): static
    {
        return $this->encadear('where', [$coluna, $operador, $valor]);
    }

    public function ouOnde(string $coluna, string $operador, mixed $valor): static
    {
        return $this->encadear('orWhere', [$coluna, $operador, $valor]);
    }

    public function ordenarPor(string $coluna, string $direcao = 'ASC'): static
    {
        return $this->encadear('orderBy', [$coluna, $direcao]);
    }

    public function limite(int $limite): static
    {
        return $this->encadear('limit', [$limite]);
    }

    /** @param string|array<int|string, mixed> ...$relacoes */
    public function com(string|array ...$relacoes): static
    {
        return $this->with(...$relacoes);
    }

    /** @param (\Closure(Consulta<Model>): mixed)|null $restricao */
    public function ondeTem(string $relacao, ?\Closure $restricao = null, string $operador = '>=', int $quantidade = 1): static
    {
        return $this->whereHas($relacao, $restricao, $operador, $quantidade);
    }

    public function tem(string $relacao, string $operador = '>=', int $quantidade = 1): static
    {
        return $this->has($relacao, $operador, $quantidade);
    }

    /** @param string|array<int|string, mixed> ...$relacoes */
    public function comContagem(string|array ...$relacoes): static
    {
        return $this->withCount(...$relacoes);
    }

    /** @return array<int, TModel> */
    public function obter(): array
    {
        return $this->get();
    }

    /** @return ?TModel */
    public function primeiro(): ?Model
    {
        return $this->first();
    }

    /** @return TModel */
    public function primeiroOuFalhar(): Model
    {
        return $this->firstOrFail();
    }

    public function paginar(int $porPagina = 15, int $pagina = 1): PaginationResult
    {
        return $this->paginate($porPagina, $pagina);
    }

    /** @return array<int|string, mixed> */
    public function extrair(string $coluna, ?string $chave = null): array
    {
        return $this->pluck($coluna, $chave);
    }

    public function contar(): int
    {
        return (int) $this->count();
    }

    /** @param callable(array<int, TModel>, int): (bool|null|void) $funcao */
    public function emBlocos(int $tamanho, callable $funcao): bool
    {
        return $this->chunk($tamanho, $funcao);
    }

    /**
     * Chama um método do QueryBuilder (via ModelQuery::__call, que guarda a
     * consulta alterada) e devolve esta Consulta.
     *
     * @param array<int, mixed> $argumentos
     */
    private function encadear(string $metodo, array $argumentos): static
    {
        parent::__call($metodo, $argumentos);

        return $this;
    }

    /**
     * Scopes em português: escopoAtivos() no modelo responde a ->ativos().
     *
     * @param array<int, mixed> $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        $model  = $this->getModel();
        $escopo = 'escopo' . ucfirst($method);

        if (method_exists($model, $escopo)) {
            (new $model())->{$escopo}($this, ...$arguments);
            return $this;
        }

        return parent::__call($method, $arguments);
    }
}

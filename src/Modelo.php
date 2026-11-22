<?php

namespace Luany\Pt;

use Luany\Database\Model;
use Luany\Database\Relations\BelongsTo;
use Luany\Database\Relations\BelongsToMany;
use Luany\Database\Relations\HasMany;
use Luany\Database\Relations\HasOne;

/**
 * Modelo — a Model com nomes em português.
 *
 *   class Produto extends Modelo
 *   {
 *       protected string $tabela       = 'produtos';
 *       protected array  $preenchiveis = ['nome', 'preco'];
 *
 *       public function categoria(): BelongsTo { return $this->pertenceA(Categoria::class); }
 *   }
 *
 *   $produto = Produto::encontrar(1);
 *   $baratos = Produto::consulta()->onde('preco', '<', 10)->ordenarPor('nome')->obter();
 *
 * É uma subclasse de Model: os métodos ingleses continuam disponíveis e um
 * Produto é um Model em tudo (relações, eventos, eager loading).
 */
abstract class Modelo extends Model
{
    // ── Propriedades em português (lidas no construtor) ─────────────────────

    /** = $table */
    protected string $tabela = '';

    /** @var list<string> = $fillable */
    protected array $preenchiveis = [];

    /** @var list<string> = $hidden */
    protected array $ocultos = [];

    /** @var array<string, string> = $casts */
    protected array $conversoes = [];

    /** = $timestamps (null: usa o valor de $timestamps) */
    protected ?bool $datasAutomaticas = null;

    public function __construct()
    {
        if ($this->tabela !== '') {
            $this->table = $this->tabela;
        }
        if ($this->preenchiveis !== []) {
            $this->fillable = $this->preenchiveis;
        }
        if ($this->ocultos !== []) {
            $this->hidden = $this->ocultos;
        }
        if ($this->conversoes !== []) {
            $this->casts = $this->conversoes;
        }
        if ($this->datasAutomaticas !== null) {
            $this->timestamps = $this->datasAutomaticas;
        }
    }

    // ── Consultas ───────────────────────────────────────────────────────────

    /**
     * Consulta fluente que devolve modelos. Tipo de retorno covariante: tudo o
     * que o luany/database constrói passa por aqui (with(), scopes, relações),
     * por isso a cadeia é sempre uma Consulta.
     *
     * @return Consulta<static>
     */
    public static function query(): Consulta
    {
        return new Consulta(static::class, static::newQuery());
    }

    /** @return Consulta<static> */
    public static function consulta(): Consulta
    {
        return static::query();
    }

    /** @return ?static */
    public static function encontrar(int|string $id): ?static
    {
        return static::find($id);
    }

    public static function encontrarOuFalhar(int|string $id): static
    {
        return static::findOrFail($id);
    }

    /** @return array<int, static> */
    public static function todos(string $ordenarPor = ''): array
    {
        return static::all($ordenarPor);
    }

    /**
     * Condição SQL com marcadores: Produto::onde('preco < ?', [10]).
     * Para a forma fluente use Produto::consulta()->onde('preco', '<', 10).
     *
     * @param array<int|string, mixed> $valores
     * @return array<int, static>
     */
    public static function onde(string $condicoes, array $valores = []): array
    {
        return static::where($condicoes, $valores);
    }

    /** @param array<int|string, mixed> $valores */
    public static function primeiroOnde(string $condicoes, array $valores = []): ?static
    {
        return static::firstWhere($condicoes, $valores);
    }

    /** @param array<int|string, mixed> $valores */
    public static function contar(string $condicoes = '', array $valores = []): int
    {
        return static::count($condicoes, $valores);
    }

    /**
     * @param string|array<int|string, mixed> ...$relacoes
     * @return Consulta<static>
     */
    public static function com(string|array ...$relacoes): Consulta
    {
        /** @var Consulta<static> */
        return static::with(...$relacoes);
    }

    // ── Criar e alterar ─────────────────────────────────────────────────────

    /** @param array<string, mixed> $dados */
    public static function criar(array $dados): static
    {
        return static::create($dados);
    }

    /**
     * @param array<string, mixed> $atributos
     * @param array<string, mixed> $valores
     */
    public static function primeiroOuNovo(array $atributos, array $valores = []): static
    {
        return static::firstOrNew($atributos, $valores);
    }

    /**
     * @param array<string, mixed> $atributos
     * @param array<string, mixed> $valores
     */
    public static function primeiroOuCriar(array $atributos, array $valores = []): static
    {
        return static::firstOrCreate($atributos, $valores);
    }

    /**
     * @param array<string, mixed> $atributos
     * @param array<string, mixed> $valores
     */
    public static function atualizarOuCriar(array $atributos, array $valores = []): static
    {
        return static::updateOrCreate($atributos, $valores);
    }

    public function guardar(): bool
    {
        return $this->save();
    }

    /** @param array<string, mixed> $atributos */
    public function atualizar(array $atributos): bool
    {
        return $this->update($atributos);
    }

    public function apagar(): bool
    {
        return $this->delete();
    }

    public function recarregar(): bool
    {
        return $this->refresh();
    }

    /** @param array<string, mixed> $atributos */
    public function preencher(array $atributos): void
    {
        $this->fill($atributos);
    }

    // ── Leitura ─────────────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    public function paraArray(): array
    {
        return $this->toArray();
    }

    public function paraJson(int $opcoes = JSON_UNESCAPED_UNICODE): string
    {
        return $this->toJson($opcoes);
    }

    public function existe(): bool
    {
        return $this->exists();
    }

    public function chavePrimaria(): mixed
    {
        return $this->getKey();
    }

    public function foiAlterado(?string $atributo = null): bool
    {
        return $this->isDirty($atributo);
    }

    // ── Relações ────────────────────────────────────────────────────────────

    /** @param class-string<Model> $relacionado */
    protected function temUm(string $relacionado, ?string $chaveEstrangeira = null, ?string $chaveLocal = null): HasOne
    {
        return $this->hasOne($relacionado, $chaveEstrangeira, $chaveLocal);
    }

    /** @param class-string<Model> $relacionado */
    protected function temMuitos(string $relacionado, ?string $chaveEstrangeira = null, ?string $chaveLocal = null): HasMany
    {
        return $this->hasMany($relacionado, $chaveEstrangeira, $chaveLocal);
    }

    /** @param class-string<Model> $relacionado */
    protected function pertenceA(string $relacionado, ?string $chaveEstrangeira = null, ?string $chaveDono = null): BelongsTo
    {
        return $this->belongsTo($relacionado, $chaveEstrangeira, $chaveDono);
    }

    /** @param class-string<Model> $relacionado */
    protected function pertenceAMuitos(
        string $relacionado,
        ?string $tabelaPivot = null,
        ?string $chavePivotEstrangeira = null,
        ?string $chavePivotRelacionada = null,
        ?string $chavePai = null,
        ?string $chaveRelacionada = null,
    ): BelongsToMany {
        return $this->belongsToMany($relacionado, $tabelaPivot, $chavePivotEstrangeira, $chavePivotRelacionada, $chavePai, $chaveRelacionada);
    }

    // ── Scopes: escopoAtivos() → Produto::ativos() ──────────────────────────

    /** @param array<int, mixed> $arguments */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        if (method_exists(static::class, 'escopo' . ucfirst($method))) {
            return static::query()->{$method}(...$arguments);
        }

        return parent::__callStatic($method, $arguments);
    }
}

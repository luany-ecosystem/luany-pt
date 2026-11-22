<?php

namespace Luany\Pt;

use Luany\Database\Schema\Blueprint;
use Luany\Database\Schema\Schema;

/**
 * Esquema — o Schema com nomes em português. Entrega uma Tabela.
 *
 *   Esquema::para($pdo)->criar('produtos', function (Tabela $t) {
 *       $t->id();
 *       $t->chaveEstrangeira('categoria_id')->restrita()->apagarEmCascata();
 *       $t->texto('nome');
 *       $t->decimal('preco')->padrao(0);
 *       $t->booleano('ativo')->padrao(true);
 *       $t->marcasTempo();
 *   });
 */
class Esquema extends Schema
{
    public static function para(\PDO|\Luany\Database\Connection $ligacao): static
    {
        return static::for($ligacao);
    }

    /** @param \Closure(Tabela): mixed $definicao */
    public function criar(string $tabela, \Closure $definicao): void
    {
        $this->create($tabela, self::comTabela($definicao));
    }

    /** @param \Closure(Tabela): mixed $definicao */
    public function criarSeNaoExistir(string $tabela, \Closure $definicao): void
    {
        $this->createIfNotExists($tabela, self::comTabela($definicao));
    }

    /** @param \Closure(Tabela): mixed $definicao */
    public function alterar(string $tabela, \Closure $definicao): void
    {
        $this->table($tabela, self::comTabela($definicao));
    }

    public function apagar(string $tabela): void
    {
        $this->drop($tabela);
    }

    public function apagarSeExistir(string $tabela): void
    {
        $this->dropIfExists($tabela);
    }

    public function renomear(string $de, string $para): void
    {
        $this->rename($de, $para);
    }

    public function temTabela(string $tabela): bool
    {
        return $this->hasTable($tabela);
    }

    public function temColuna(string $tabela, string $coluna): bool
    {
        return $this->hasColumn($tabela, $coluna);
    }

    /**
     * O Schema entrega um Blueprint; aqui é sempre uma Tabela (newBlueprint()).
     *
     * @param \Closure(Tabela): mixed $definicao
     * @return \Closure(Blueprint): mixed
     */
    private static function comTabela(\Closure $definicao): \Closure
    {
        return static function (Blueprint $blueprint) use ($definicao): mixed {
            if (!$blueprint instanceof Tabela) {
                throw new \LogicException('Esquema espera uma Tabela; newBlueprint() foi redefinido?');
            }

            return $definicao($blueprint);
        };
    }

    protected function newBlueprint(string $table): Blueprint
    {
        return new Tabela($table);
    }
}

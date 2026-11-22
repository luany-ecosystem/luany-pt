<?php

namespace Luany\Pt;

use Luany\Database\Schema\Blueprint;
use Luany\Database\Schema\ColumnDefinition;
use Luany\Database\Schema\ForeignIdColumnDefinition;
use Luany\Database\Schema\ForeignKeyDefinition;

/**
 * Tabela — o Blueprint com nomes em português. As colunas são Coluna, por
 * isso os modificadores também ficam em português: ->anulavel()->unico().
 * Os nomes ingleses continuam disponíveis.
 */
class Tabela extends Blueprint
{
    public function chaveEstrangeira(string $nome): ChaveEstrangeira
    {
        /** @var ChaveEstrangeira */
        return $this->foreignId($nome);
    }

    public function texto(string $nome, int $tamanho = 255): Coluna
    {
        return self::coluna($this->string($nome, $tamanho));
    }

    public function textoLongo(string $nome): Coluna
    {
        return self::coluna($this->text($nome));
    }

    public function textoExtenso(string $nome): Coluna
    {
        return self::coluna($this->longText($nome));
    }

    public function inteiro(string $nome): Coluna
    {
        return self::coluna($this->integer($nome));
    }

    public function inteiroGrande(string $nome): Coluna
    {
        return self::coluna($this->bigInteger($nome));
    }

    public function booleano(string $nome): Coluna
    {
        return self::coluna($this->boolean($nome));
    }

    public function data(string $nome): Coluna
    {
        return self::coluna($this->date($nome));
    }

    public function dataHora(string $nome): Coluna
    {
        return self::coluna($this->dateTime($nome));
    }

    public function hora(string $nome): Coluna
    {
        return self::coluna($this->time($nome));
    }

    public function marcaTempo(string $nome): Coluna
    {
        return self::coluna($this->timestamp($nome));
    }

    /** created_at e updated_at. */
    public function marcasTempo(): void
    {
        $this->timestamps();
    }

    /** deleted_at, para SoftDeletes. */
    public function eliminacaoLogica(string $coluna = 'deleted_at'): Coluna
    {
        return self::coluna($this->softDeletes($coluna));
    }

    /** @param string|list<string> $colunas */
    public function apagarColuna(string|array $colunas): void
    {
        $this->dropColumn($colunas);
    }

    public function renomearColuna(string $de, string $para): void
    {
        $this->renameColumn($de, $para);
    }

    /** @param string|list<string> $colunas */
    public function estrangeira(string|array $colunas): Restricao
    {
        /** @var Restricao */
        return $this->foreign($colunas);
    }

    // ── Pontos de extensão do Blueprint ─────────────────────────────────────

    /** @param array<string, mixed> $options */
    protected function newColumn(string $name, string $type, array $options): ColumnDefinition
    {
        return new Coluna($name, $type, $options);
    }

    protected function newForeignId(ColumnDefinition $column): ForeignIdColumnDefinition
    {
        return new ChaveEstrangeira($this, $column);
    }

    /** @param list<string> $columns */
    protected function newForeignKey(array $columns): ForeignKeyDefinition
    {
        return new Restricao($columns);
    }

    private static function coluna(ColumnDefinition $coluna): Coluna
    {
        /** @var Coluna $coluna */
        return $coluna;
    }
}

<?php

namespace Luany\Pt;

use Luany\Database\Schema\ForeignIdColumnDefinition;

/**
 * Resultado de $t->chaveEstrangeira('categoria_id'):
 *   ->restrita()            chave estrangeira para categorias(id)
 *   ->anulavel(), ->unico(), ->indice(), ->padrao(…)
 */
class ChaveEstrangeira extends ForeignIdColumnDefinition
{
    /** Liga a $tabela(id); por omissão o plural do nome sem _id (categoria_id → categorias). */
    public function restrita(?string $tabela = null, string $coluna = 'id'): Restricao
    {
        /** @var Restricao */
        return $this->constrained($tabela ?? self::tabelaPt($this->column->name), $coluna);
    }

    public function anulavel(bool $valor = true): static
    {
        return $this->nullable($valor);
    }

    public function indice(): static
    {
        return $this->index();
    }

    public function unico(): static
    {
        return $this->unique();
    }

    public function padrao(mixed $valor): static
    {
        return $this->default($valor);
    }

    /**
     * Plural português simples do nome sem _id: categoria_id → categorias,
     * autor_id → autores, papel_id → papeis, pais_id → paises,
 * cancao_id → cancoes, cidade_id → cidades.
     */
    public static function tabelaPt(string $coluna): string
    {
        $nome = (string) preg_replace('/_id$/', '', $coluna);

        return match (true) {
            (bool) preg_match('/(r|z|s)$/', $nome) => $nome . 'es',
            (bool) preg_match('/(a|e|i|o|u)l$/', $nome) => substr($nome, 0, -1) . 'is',
            (bool) preg_match('/m$/', $nome) => substr($nome, 0, -1) . 'ns',
            (bool) preg_match('/ão$/u', $nome) => substr($nome, 0, -strlen('ão')) . 'ões',
            str_ends_with($nome, 'ao') => substr($nome, 0, -2) . 'oes',
            default => $nome . 's',
        };
    }
}

<?php

namespace Luany\Pt;

use Luany\Database\Schema\ForeignKeyDefinition;

/**
 * Restrição de chave estrangeira:
 *   $t->chaveEstrangeira('categoria_id')->restrita()->apagarEmCascata();
 *   $t->estrangeira('autor_id')->referencia('id')->em('autores')->anularAoApagar();
 */
class Restricao extends ForeignKeyDefinition
{
    public function referencia(string $coluna): static
    {
        return $this->references($coluna);
    }

    public function em(string $tabela): static
    {
        return $this->on($tabela);
    }

    public function aoApagar(string $acao): static
    {
        return $this->onDelete($acao);
    }

    public function aoAtualizar(string $acao): static
    {
        return $this->onUpdate($acao);
    }

    public function apagarEmCascata(): static
    {
        return $this->cascadeOnDelete();
    }

    public function restringirAoApagar(): static
    {
        return $this->restrictOnDelete();
    }

    public function anularAoApagar(): static
    {
        return $this->nullOnDelete();
    }

    public function atualizarEmCascata(): static
    {
        return $this->cascadeOnUpdate();
    }

    public function nome(string $nome): static
    {
        return $this->name($nome);
    }
}

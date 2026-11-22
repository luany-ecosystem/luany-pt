<?php

namespace Luany\Pt;

use Luany\Database\Schema\ColumnDefinition;

/**
 * Coluna — modificadores em português: $t->texto('email')->unico()->anulavel().
 */
class Coluna extends ColumnDefinition
{
    public function anulavel(bool $valor = true): static
    {
        return $this->nullable($valor);
    }

    public function padrao(mixed $valor): static
    {
        return $this->default($valor);
    }

    public function semSinal(): static
    {
        return $this->unsigned();
    }

    public function unico(): static
    {
        return $this->unique();
    }

    public function indice(): static
    {
        return $this->index();
    }

    public function primaria(): static
    {
        return $this->primary();
    }

    public function usarAtual(): static
    {
        return $this->useCurrent();
    }
}

<?php

namespace Luany\Pt\Tests;

use Luany\Database\Connection;
use Luany\Database\Model;
use Luany\Database\Relations\BelongsTo;
use Luany\Database\Relations\HasMany;
use Luany\Pt\Consulta;
use Luany\Pt\Esquema;
use Luany\Pt\Modelo;
use Luany\Pt\Tabela;
use PHPUnit\Framework\TestCase;

class ModeloTest extends TestCase
{
    private \PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
        Model::setConnection(Connection::fromPdo($this->pdo));

        $esquema = Esquema::para($this->pdo);
        $esquema->criar('categorias', function (Tabela $t) {
            $t->id();
            $t->texto('nome')->unico();
        });
        $esquema->criar('produtos', function (Tabela $t) {
            $t->id();
            $t->chaveEstrangeira('categoria_id')->restrita()->apagarEmCascata();
            $t->texto('nome');
            $t->decimal('preco')->padrao(0);
            $t->booleano('ativo')->padrao(true);
            $t->textoLongo('descricao')->anulavel();
            $t->marcasTempo();
        });

        $bebidas = Categoria::criar(['nome' => 'Bebidas']);
        Produto::criar(['categoria_id' => $bebidas->chavePrimaria(), 'nome' => 'Água', 'preco' => 1.5]);
        Produto::criar(['categoria_id' => $bebidas->chavePrimaria(), 'nome' => 'Sumo', 'preco' => 3, 'ativo' => false]);
        Produto::criar(['categoria_id' => $bebidas->chavePrimaria(), 'nome' => 'Café', 'preco' => 2]);
    }

    public function test_portuguese_properties_reach_the_model(): void
    {
        $produto = new Produto();

        $this->assertSame('produtos', $produto->getTable());
        $this->assertTrue($produto->isFillable('nome'));
        $this->assertArrayNotHasKey('descricao', Produto::encontrar(1)->paraArray(), '$ocultos');
        $this->assertIsBool(Produto::encontrar(1)->ativo, '$conversoes');
        $this->assertNotNull(Produto::encontrar(1)->created_at, '$datasAutomaticas');
    }

    public function test_static_finders(): void
    {
        $this->assertSame('Água', Produto::encontrar(1)?->nome);
        $this->assertNull(Produto::encontrar(99));
        $this->assertCount(3, Produto::todos());
        $this->assertSame(3, Produto::contar());
        $this->assertSame(2, Produto::contar('preco >= ?', [2]));
        $this->assertCount(1, Produto::onde('nome = ?', ['Sumo']));
        $this->assertSame('Café', Produto::primeiroOnde('preco = ?', [2])?->nome);
    }

    public function test_fluent_chain_stays_a_consulta(): void
    {
        $consulta = Produto::consulta()->onde('preco', '>', 1)->ordenarPor('nome')->com('categoria');
        $this->assertInstanceOf(Consulta::class, $consulta);
        $this->assertInstanceOf(Consulta::class, Produto::query()->where('id', '>', 0), 'English names keep the Consulta');
        $this->assertInstanceOf(Consulta::class, Produto::com('categoria'));

        $nomes = array_map(fn (Produto $p) => $p->nome, $consulta->obter());
        $this->assertSame(['Café', 'Sumo', 'Água'], $nomes);
        $this->assertSame('Bebidas', $consulta->primeiro()?->categoria->nome);
        $this->assertSame(['Café', 'Sumo', 'Água'], Produto::consulta()->ordenarPor('nome')->extrair('nome'));
        $this->assertSame(2, Produto::consulta()->onde('preco', '>=', 2)->contar());
        $this->assertSame(1, Produto::consulta()->ordenarPor('id')->limite(1)->contar() > 0 ? 1 : 0);
        $this->assertSame(3, Produto::consulta()->paginar(2)->total);
    }

    public function test_relation_constraints_receive_a_consulta(): void
    {
        $recebida = null;
        $categorias = Categoria::consulta()->ondeTem('produtos', function ($c) use (&$recebida) {
            $recebida = $c;
            $c->onde('preco', '>', 2.5);
        })->obter();

        $this->assertInstanceOf(Consulta::class, $recebida);
        $this->assertCount(1, $categorias);
        $this->assertSame(3, Categoria::consulta()->comContagem('produtos')->primeiro()?->produtos_count);
        $this->assertCount(1, Categoria::consulta()->tem('produtos', '>=', 3)->obter());
    }

    public function test_portuguese_scopes(): void
    {
        $this->assertCount(2, Produto::ativos()->obter());
        $this->assertCount(1, Produto::consulta()->ativos()->onde('preco', '>', 1.6)->obter());
    }

    public function test_create_update_delete(): void
    {
        $p = Produto::primeiroOuNovo(['nome' => 'Chá'], ['categoria_id' => 1, 'preco' => 1]);
        $this->assertFalse($p->existe());
        $this->assertTrue($p->guardar());
        $this->assertTrue($p->existe());

        $p->preencher(['preco' => 4]);
        $this->assertTrue($p->foiAlterado('preco'));
        $p->guardar();
        $this->assertTrue($p->atualizar(['nome' => 'Chá verde']));
        $this->assertSame('Chá verde', Produto::encontrarOuFalhar($p->chavePrimaria())->nome);

        $this->assertSame('Leite', Produto::primeiroOuCriar(['nome' => 'Leite'], ['categoria_id' => 1])->nome);
        $this->assertEquals(9, Produto::atualizarOuCriar(['nome' => 'Leite'], ['preco' => 9])->preco);
        $this->assertTrue($p->apagar());
        $this->assertNull(Produto::encontrar($p->chavePrimaria()));
        $this->assertStringContainsString('"nome":"Água"', Produto::encontrar(1)->paraJson());
    }

    public function test_foreign_key_cascade_from_portuguese_schema(): void
    {
        Categoria::encontrar(1)?->apagar();
        $this->assertSame(0, Produto::contar());
    }
}

class Categoria extends Modelo
{
    protected string $tabela       = 'categorias';
    protected array  $preenchiveis = ['nome'];
    protected ?bool  $datasAutomaticas = false;

    public function produtos(): HasMany
    {
        return $this->temMuitos(Produto::class);
    }
}

class Produto extends Modelo
{
    protected string $tabela       = 'produtos';
    protected array  $preenchiveis = ['categoria_id', 'nome', 'preco', 'ativo', 'descricao'];
    protected array  $ocultos      = ['descricao'];
    protected array  $conversoes   = ['ativo' => 'bool', 'preco' => 'float'];
    protected ?bool  $datasAutomaticas = true;

    public function categoria(): BelongsTo
    {
        return $this->pertenceA(Categoria::class);
    }

    public function escopoAtivos(Consulta $c): void
    {
        $c->onde('ativo', '=', 1);
    }
}

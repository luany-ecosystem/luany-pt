# luany/pt — Luany em Português

A API do framework [Luany](https://docs.luany.dev) em português: modelos, consultas, migrations, rotas e helpers.

```bash
composer require luany/pt
```

Quem não instala o pacote não vê nada em português. Com ele, as classes inglesas continuam disponíveis e podem misturar-se com as portuguesas.

## Modelos e consultas

```php
use Luany\Database\Relations\BelongsTo;
use Luany\Pt\Consulta;
use Luany\Pt\Modelo;

class Produto extends Modelo
{
    protected string $tabela       = 'produtos';
    protected array  $preenchiveis = ['nome', 'preco', 'categoria_id'];
    protected array  $ocultos      = ['custo'];
    protected array  $conversoes   = ['ativo' => 'bool'];
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

$produto = Produto::encontrar(1);
$lista   = Produto::ativos()
    ->onde('preco', '<', 10)
    ->ordenarPor('nome')
    ->com('categoria')
    ->paginar(20);

$novo = Produto::criar(['nome' => 'Café', 'preco' => 2]);
$novo->atualizar(['preco' => 2.5]);
$novo->apagar();
```

`Modelo` é uma subclasse de `Model` e `Consulta` de `ModelQuery`: um `Produto` é um `Model` em tudo (relações, eventos, eager loading) e toda a cadeia de uma consulta continua a ser uma `Consulta`, mesmo com métodos ingleses pelo meio.

| Modelo | | Consulta | |
|---|---|---|---|
| `encontrar`, `encontrarOuFalhar` | find | `onde`, `ouOnde` | where |
| `todos`, `contar` | all, count | `ordenarPor`, `limite` | orderBy, limit |
| `onde`, `primeiroOnde` | where (SQL), firstWhere | `com`, `comContagem` | with, withCount |
| `criar`, `primeiroOuNovo`, `primeiroOuCriar`, `atualizarOuCriar` | create… | `ondeTem`, `tem` | whereHas, has |
| `guardar`, `atualizar`, `apagar`, `recarregar`, `preencher` | save, update, delete, refresh, fill | `obter`, `primeiro`, `primeiroOuFalhar` | get, first… |
| `paraArray`, `paraJson`, `existe`, `chavePrimaria`, `foiAlterado` | toArray… | `paginar`, `extrair`, `contar`, `emBlocos` | paginate, pluck, count, chunk |
| `temUm`, `temMuitos`, `pertenceA`, `pertenceAMuitos` | relações | `escopoX()` | scopeX() |

## Migrations

```php
use Luany\Pt\Esquema;
use Luany\Pt\Tabela;

return new class {
    public function up(\PDO $pdo): void
    {
        Esquema::para($pdo)->criar('produtos', function (Tabela $t) {
            $t->id();
            $t->chaveEstrangeira('categoria_id')->restrita()->apagarEmCascata();  // → categorias(id)
            $t->texto('nome');
            $t->decimal('preco')->padrao(0);
            $t->booleano('ativo')->padrao(true);
            $t->textoLongo('descricao')->anulavel();
            $t->marcasTempo();
        });
    }

    public function down(\PDO $pdo): void
    {
        Esquema::para($pdo)->apagarSeExistir('produtos');
    }
};
```

Colunas: `texto`, `textoLongo`, `textoExtenso`, `inteiro`, `inteiroGrande`, `decimal`, `booleano`, `data`, `dataHora`, `hora`, `marcaTempo`, `marcasTempo`, `eliminacaoLogica`, `json`, `uuid`, `enum`. Modificadores: `anulavel`, `padrao`, `unico`, `indice`, `semSinal`, `primaria`. `restrita()` usa o plural português (`categoria_id` → `categorias`, `autor_id` → `autores`).

## Rotas

```php
use Luany\Pt\Rota;

Rota::get('/', [InicioControlador::class, 'index'])->nome('inicio');
Rota::recurso('produtos', ProdutoControlador::class, ['exceto' => ['destroy']]);
Rota::prefixo('/admin')->middleware(Autenticado::class)->grupo(function () {
    Rota::vista('/painel', 'admin.painel')->nome('admin.painel');
});
```

Os verbos HTTP não se traduzem. Também: `qualquer`, `recursoApi`, `grupo`, `vincular`, `modelo`.

## Helpers

`vista()`, `redirecionar()`, `resposta()`, `validar()`, `rota()`, `configuracao()`, `sessao()`, `valorAnterior()`, `abortar()`, `traduzir()`, `registo()` (ou `registro()`). Iguais: `url()`, `e()`, `env()`, `asset()`.

## Com o resto do "Luany em Português"

- **Vistas:** `LUANY_SYNTAX=pt` → `@se`, `@paracada`, `@secao`… (luany/lte 1.4).
- **Mensagens da CLI:** `LUANY_DEV_LANGUAGE=pt` (luany/cli 1.11).
- A tabela de nomes está em `resources/aliases.pt.json`; um teste garante que cada nome existe nas duas línguas.

---

**English:** `luany/pt` adds Portuguese names for the Luany API — `Modelo` (Model), `Consulta` (ModelQuery), `Esquema`/`Tabela` (Schema/Blueprint), `Rota` (Route) and helpers. They are subclasses or thin facades: English names keep working and the two can be mixed.

## License

MIT

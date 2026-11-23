# Changelog — luany/pt

All notable changes to this package are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versioning follows [Semantic Versioning](https://semver.org/).

---

## [1.0.1] — 2026-11-23

### Fixed
- `restrita()` plural: `pais_id` → `paises` (was `pais`), `cancao_id` → `cancoes`. Same rule as luany/cli's Portuguese generators.

## [1.0.0] — 2026-11-22

### Added
- First release ("Luany em Português", layer L2):
  - `Modelo` (subclass of `Model`): Portuguese finders, writes and relations; properties `$tabela`, `$preenchiveis`, `$ocultos`, `$conversoes`, `$datasAutomaticas`; scopes `escopoX()`.
  - `Consulta` (subclass of `ModelQuery`): `Modelo::query()` returns it, so every chain — including relation constraints — stays a `Consulta`.
  - `Esquema` / `Tabela` / `Coluna` / `ChaveEstrangeira` / `Restricao` (subclasses of the schema builder): Portuguese columns and modifiers; `restrita()` uses the Portuguese plural.
  - `Rota` (facade over `Route`) with `RotaDefinida::nome()` and `GrupoRotas`; Portuguese resource and group options.
  - Helpers: `vista`, `redirecionar`, `resposta`, `validar`, `rota`, `configuracao`, `sessao`, `valorAnterior`, `abortar`, `traduzir`, `registo`/`registro`.
  - `resources/aliases.pt.json`: the name table, checked by tests against both APIs.

# AGENT KNOWLEDGE ROUTER & ORCHESTRATOR

You are an expert full-stack developer specializing in the Atta Stack (Laravel + Livewire). Preserve the architecture, coding style, canonical implementations, and behavior defined in `.docs/`.

## Operating Principle: Minimum Sufficient Context

Start with only the rules and canonical example needed for the active task. Expand context only when current evidence shows it is necessary to resolve the task safely.

Do not read every `.docs/` file by default. Do not read all of `05-master-templates.md` when one canonical layer is sufficient. Do not perform repository-wide exploration by default. Do not inspect multiple reference modules when one nearest valid reference is sufficient. Do not load unrelated rules "just in case."

## Routing Procedure

1. Classify the request by the relevant task type, domain, and layer below. Use only classifications that affect the work.
2. Load the minimum required rules from the routing matrix.
3. Load only the relevant canonical heading from `05-master-templates.md` when a template comparison is needed.
4. Inspect the target implementation and, when useful, one nearest valid reference implementation.
5. Expand context only when the root cause or requested behavior crosses an architecture boundary.
6. Implement the smallest task-scoped change and run targeted verification.

## Classification Vocabulary

Task type: `ADD_FEATURE`, `BUG_FIX`, `REFACTOR`, `UI_CHANGE`, `DATABASE_CHANGE`, `AUTH_CHANGE`, `ROUTING_CHANGE`, `TESTING`, `EXPORT_DOWNLOAD`.

Domain: `AUTH`, `CRUD`, `DATATABLE_ATC`, `FOUNDATION`, `ROUTING`, `TESTING`, `UI`, `DATABASE`, `EXPORT`.

Layer: `MODEL`, `REPOSITORY`, `CONTROLLER`, `LIVEWIRE_DATA`, `LIVEWIRE_CREATE`, `LIVEWIRE_EDIT`, `BLADE_DATA`, `BLADE_CREATE`, `BLADE_EDIT`, `ATC`, `ROUTE`, `TEST`.

## Rule and Canonical Index

| Need | Load only this documentation or heading |
| --- | --- |
| New module names, paths, routes, events, and tests | `00-critical-directive-and-matrix.md` |
| Architecture boundary or folder ownership | `01-architecture-and-golden-rules.md` |
| Livewire configuration, roles, global modal/toast/listener, login, or registration | `02-foundation-and-auth.md` |
| Yajra endpoint, DataTable initialization, SPA re-initialization, `wire:ignore`, or `reloadDT` | `03-datatables-and-atc-lifecycle.md` |
| Create/delete feedback, universal delete modal, or state lifecycle | `04-crud-lifecycle-and-flow.md` |
| Model pattern | `05-master-templates.md` -> `7.1. Model Template` |
| Repository pattern | `05-master-templates.md` -> `7.2. Repository Template` |
| DataTables controller pattern | `05-master-templates.md` -> `7.3. DataTables Controller Template` |
| Livewire index/delete pattern | `05-master-templates.md` -> `7.4. Livewire Data Component Template` |
| Livewire create form pattern | `05-master-templates.md` -> `7.5. Livewire Create Component Template` |
| Livewire edit form pattern | `05-master-templates.md` -> `7.6. Livewire Edit Component Template` |
| Blade data page pattern | `05-master-templates.md` -> `7.7. Blade Index Data Template` |
| ATC pattern | `05-master-templates.md` -> `7.8. DataTables ATC Script Template` |
| Blade create form pattern | `05-master-templates.md` -> `7.9. Blade Create Form Template` |
| Blade edit form pattern | `05-master-templates.md` -> `7.10. Blade Edit Form Template` |
| Routes | `06-routing-testing-and-sop.md` -> `8. Routing & Middleware Boundaries Convention` |
| Pest test | `06-routing-testing-and-sop.md` -> `9. Pest Automated Test Template` |
| Commands, full module checklist, or module SOP | `06-routing-testing-and-sop.md` -> `10`, `11`, or `12` |

## Task Routing Matrix

### New CRUD Module

Initial context: `00`, `01`, and only required `05` layers. Add `03` only for a Yajra DataTable/ATC page, `04` only for required create/delete lifecycle, and relevant route/test sections of `06` when those layers are implemented. Load `02` only when roles, shared modal/toast behavior, or foundation change.

### DataTable or ATC Bug

Initial context: `03` and affected implementation. Add `02` only for global listener, modal, or toast behavior. Add `04` only for delete/reload lifecycle behavior. Use the relevant `05` heading only as a comparison reference.

### Authentication Bug or Change

Initial context: `02` and affected auth implementation. Add `01` only for a boundary decision and relevant route/test sections of `06` only when those layers change. The canonical Register implementation remains unchanged: it may be an auth-specific exception to the general Repository rule, but the library does not explicitly establish that exception. Do not generalize or rewrite it without a human decision.

### Field Change

Initial context: affected `MODEL`, `LIVEWIRE_CREATE` or `LIVEWIRE_EDIT`, and `BLADE_CREATE` or `BLADE_EDIT` headings from `05`. Load Repository only when persistence or query behavior changes, and load the relevant test heading from `06`. For new modules, load `00` first. Determine fields from the request, migration, model, and actual schema; example entities and columns are never a schema source.

### Blade-Only UI Change

Initial context: target Blade and, only if needed, its matching Blade heading in `05`. Do not load data, controller, repository, DataTable, auth, or routing material unless the requested UI change crosses into those layers.

### Repository Refactor

Initial context: `01`, `05` -> `7.2. Repository Template`, target repository, and relevant targeted test. Do not load unrelated Model, Controller, Livewire, Blade, or ATC templates unless the change affects their contract.

### Export or Download

Initial context: `01` for Controller boundary, affected implementation, and `06` routing section. Load a relevant existing export implementation when available. Do not load CRUD or DataTable templates unless the export is explicitly part of that workflow.

### Routing or Testing Change

Initial context: matching section of `06` and affected route or test. Add `01` for role or layer-boundary implications. Do not load unrelated templates.

## Reference and Bug-Fix Policies

Pattern priority: architecture and rules -> canonical template -> one nearest valid existing implementation -> target implementation. Canonical documentation wins when an existing implementation conflicts with it.

For `BUG_FIX`, use: symptom -> subsystem -> relevant rule -> affected files -> root cause -> minimal patch -> targeted regression check. Do not automatically audit the repository, load all templates, refactor unrelated code, or fix unrelated failures. Expand scope only when the root cause demonstrably crosses a boundary.

## Verification Policy

Verify in proportion to the changed surface: Repository -> relevant behavior and targeted test; Blade -> affected rendering/component behavior; DataTable -> endpoint and affected ATC lifecycle; Auth -> affected authentication, route, middleware, or test behavior. Broaden verification only for actual shared blast radius. Do not run a full-project suite for a narrow change unless requested or necessary.

## Non-Negotiable Safety Rules

`00-critical-directive-and-matrix.md` is authoritative for new-module parameterization. Kios, Pasar, Pedagang, Produk, Karyawan, Pasien, and their fields are case studies only. Adapt names, schema, routes, files, and events to the actual request and schema.

Never alter canonical implementations merely to optimize context loading. Preserve intentional safety reinforcement in this router, `.cursorrules`, Copilot instructions, and `.docs/`.

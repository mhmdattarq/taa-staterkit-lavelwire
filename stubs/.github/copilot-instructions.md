# AGENT EXECUTION & TOKEN CONSERVATION RULES (CRITICAL)
- Laser-focused Target Execution: Focus exclusively on files directly relevant to the reported issue or explicit instruction.
- Zero Unprompted Exploration: Strictly avoid broad directory searches, globbing, or inspecting `vendor/`, `node_modules/`, or unrelated files.
- Execute Directly: Go straight to the root of the issue without modifying unrequested code or initiating speculative refactors.
- Demand-Driven Documentation: Never ingest the entire `.docs/` directory at once. Always route via `agents/agents.md` to load only the specific document required for the active task.

# TAA STARTERKIT ARCHITECTURE RULES (VS CODE / ANTIGRAVITY)
- Strict Separation of Concerns (SoC):
  * Ultra-Thin Controller: Strictly for Yajra DataTables (`dataDt()`) JSON responses and binary file downloads. NO rendering blade views, NO form submissions.
  * Static Repository (`app/Repositories/`): Single Point of Access for DB queries. All CUD wrapped in try-catch + Log::error. `getDt()` MUST return a Query Builder instance.
  * Livewire: Handles all UI state, form properties via `$form = []`, explicit validation, and SPA navigation (`wire:navigate`).
- Views: Global templates reside in `resources/views/templates/`. Modular business views reside in `resources/views/mods/{role}/{module}/`.
- Yajra DT x Livewire: Wrap the table wrapper container with `wire:ignore`. ATC jQuery scripts must destroy previous DataTable instances before re-initialization and listen to both `DOMContentLoaded` and `livewire:navigated`.
- Reactive Deletion & Reload: Trigger confirmation modals via `hookModalDelete` dispatching to the universal modal; refresh tables seamlessly via `reloadDT` browser events without full page refreshes.
- ALWAYS refer to `agents/agents.md` as the primary router to locate specific `.docs/` rules on demand.


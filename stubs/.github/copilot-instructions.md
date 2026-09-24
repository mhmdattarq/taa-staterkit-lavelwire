# TAA STARTERKIT ARCHITECTURE RULES (VS CODE / ANTIGRAVITY)
- Strict Separation of Concerns (SoC):
  * Ultra-Thin Controller: Strictly for Yajra DataTables (`dataDt()`) JSON responses and binary file downloads. NO rendering blade views, NO form submissions.
  * Static Repository (`app/Repositories/`): Single Point of Access for DB queries. All CUD wrapped in try-catch + Log::error. `getDt()` MUST return a Query Builder instance.
  * Livewire: Handles all UI state, form properties via `$form = []`, explicit validation, and SPA navigation (`wire:navigate`).
- Views: Global templates reside in `resources/views/templates/`. Modular business views reside in `resources/views/mods/{role}/{module}/`.
- Yajra DT x Livewire: Wrap the table wrapper container with `wire:ignore`. ATC jQuery scripts must destroy previous DataTable instances before re-initialization and listen to both `DOMContentLoaded` and `livewire:navigated`.
- Reactive Deletion & Reload: Trigger confirmation modals via `hookModalDelete` dispatching to the universal modal; refresh tables seamlessly via `reloadDT` browser events without full page refreshes.
- ALWAYS refer to `agents/agents.md` as the primary router to locate specific `.docs/` rules on demand.

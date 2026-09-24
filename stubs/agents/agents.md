# AGENT ROUTING DIRECTORY & ORCHESTRATOR

You are an expert full-stack developer specializing in the Atta Stack (Laravel + Livewire).
Your role is to strictly follow the modular rules defined in `.docs/`.

### ON-DEMAND CONTEXT RETRIEVAL (STRICT RULE):
Do NOT read all documentation files simultaneously. Read ONLY the specific file required for your immediate task:

1. **New Module Scaffolding / Variable Setup:**
   - Read: `.docs/00-critical-directive-and-matrix.md` & `.docs/05-master-templates.md`
   - Purpose: Map entity names accurately without hardcoding case-study variables, and scaffold boilerplate code.

2. **Core Architectural Boundaries & Directory Structure:**
   - Read: `.docs/01-architecture-and-golden-rules.md`
   - Purpose: Maintain strict Separation of Concerns (SoC), Ultra-Thin Controller, static Repository, and view structures (`templates/` vs `mods/`).

3. **System Foundation & Authentication:**
   - Read: `.docs/02-foundation-and-auth.md`
   - Purpose: Multi-role middleware, Livewire-only login (smart dual-identifier), and atomic registration.

4. **Yajra DataTables & Frontend Table Interactivity:**
   - Read: `.docs/03-datatables-and-atc-lifecycle.md`
   - Purpose: Construct ATC scripts, handle SPA `livewire:navigated` re-init, wrap DOM with `wire:ignore`, and manage `reloadDT` events.

5. **Data Deletion & State Lifecycle:**
   - Read: `.docs/04-crud-lifecycle-and-flow.md`
   - Purpose: Implement universal confirmation modal hook (`hookModalDelete`) and state reset logic.

6. **Routing, Testing & Verification:**
   - Read: `.docs/06-routing-testing-and-sop.md`
   - Purpose: Register routes cleanly, create Pest Feature tests, run Pint formatting, and verify complete module SOP checklist.

### EXECUTION WORKFLOW:
1. Identify the user request scope.
2. Load only the relevant `.docs/` file(s) mapped above.
3. Generate or refactor code strictly according to Atta Stack standards.

## 1. Philosophy & Golden Rules of the Atta Stack Architecture

This document is the official **Product Requirement & Architecture Blueprint (PRD-LW)** that consolidates all architecture standards, code conventions, workflows, and module responsibility boundaries for projects built on **Laravel + Livewire**.

This standard is designed with precision so that **Antigravity (AI Coding Assistant)** and other AI systems can immediately understand the coding style (_PM Standard_) without ambiguity, and can directly copy ready-to-use, copy-pasteable parameterized templates by substituting variables to match the requested feature.

### 5 Golden Rules:

1. **Strict Separation of Concerns (SoC)**:
    - **Livewire**: Controls all UI, state, form validation, interactivity, and event dispatch. Behaves like a Single Page Application (SPA) via `wire:navigate`.
    - **Repository (`app/Repositories/`)**: The sole location for data logic, database transactions, Eloquent manipulation, and error logging.
    - **Controller (`app/Http/Controllers/`)**: **Ultra-Thin Controller**. Has ONLY 2 responsibilities: serving Yajra DataTables JSON data (`dataDt()`) and serving binary file downloads (PDF/Excel). **FORBIDDEN from rendering views (`return view()`) and FORBIDDEN from handling CRUD form submissions!**
    - **Model (`app/Models/`)**: Represents the database schema, Eloquent relations (`hasMany`, `belongsTo`), and attribute casting. Must not contain business queries called directly from outside.
2. **View Separation: `templates/` vs `mods/`**:
    - `resources/views/templates/`: Exclusively for the global application scaffold (Master layout, Header, Sidebar, Footer, Universal Modal, Toast).
    - `resources/views/mods/`: Exclusively for business module interfaces per feature/role (Data Page, Create Form, Edit Form, and `atc/` scripts).
3. **Form State Pattern `$form = []`**:
   All form inputs in a Livewire component must be grouped into a single array property `$this->form = [...]` and validated with explicit rules (`rules()`, `messages()`, `$validationAttributes`).
4. **Yajra DataTables — Isolated & Reactive**:
   jQuery DataTables is mounted on a Livewire page via the Action & Table Controller (**ATC**) script. Delete actions trigger the universal Livewire confirmation modal, and table reloads are performed via the `reloadDT` browser event without a browser refresh.
5. **Pure Livewire Class Component Authentication**:
   Does not use Laravel Breeze/Jetstream/Fortify. Login and Register are built purely with Livewire Class Components, featuring _Smart Dual-Identifier_ support (auto-detection of Username/NIK) and atomic database transactions (`DB::transaction`).

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                 ALUR ARSITEKTUR UTAMA                                  │
└────────────────────────────────────────────────────────────────────────────────────────┘

    [ Browser User ]
           │
           ├──────── (Navigasi SPA / Form Submit / Aksi Modal) ──┐
           │                                                     ▼
           │                                          [ Livewire Component ]
           │                                          (app/Livewire/...)
           │                                                     │
           ├──────── (Permintaan AJAX DataTables) ────────┐      │ (Panggil Method Static)
           │                                              ▼      ▼
           │                                         [ Controller ] ───► [ Repository ]
           │                                         (Yajra DT / PDF)    (app/Repositories/..)
           │                                                                     │
           │                                                                     ▼
           │                                                                [ Model / DB ]
           │                                                                (app/Models/..)
           ▼
    [ Render Blade ]
    ├── templates/ (Layout, Header, Sidebar, Modal Universal, Toast)
    └── mods/      (Halaman Modul, Form, ATC Script DataTables)
```

---

## 2. Folder Structure & Layer Responsibilities

Each folder has strict responsibility boundaries (_Strict Boundaries_):

```
app/
├── Enums/                 -> Definisi Enum PHP (Role, Status, dll.)
├── Http/
│   ├── Controllers/       -> Ultra-Thin: Endpoint DataTables JSON (dataDt) & Download PDF/Excel
│   └── Middleware/        -> Otorisasi Role & Proteksi Rute (RoleMiddleware.php)
├── Livewire/              -> Otak UI: Halaman, Form Create/Edit, Binding State, Event Dispatch
│   ├── Admin/             -> Komponen Livewire khusus admin (Data, Create, Edit per modul)
│   ├── Pedagang/          -> Komponen Livewire khusus pedagang
│   └── Auth/              -> Komponen Livewire Login & Register
├── Models/                -> Skema tabel DB, relasi Eloquent, casting atribut ($guarded = [])
└── Repositories/          -> Pusat Operasi Data (CRUD static methods, Log::error, getDt query)

resources/views/
├── templates/             -> Kerangka Global (Scaffolding)
│   ├── layouts/           -> Master layout: app.blade.php, guest.blade.php, landing.blade.php
│   ├── components/        -> Komponen global: header, sidebar, footer, modal, toast
│   └── pdf/               -> Template tampilan cetak PDF Dompdf
└── mods/                  -> Modul Fitur Bisnis (Modules)
    ├── admin/             -> Tampilan modul admin (misal: kios, pasar, pedagang)
    │   └── {module}/      -> {module}-data.blade.php, {module}-create.blade.php, {module}-edit.blade.php
    │       └── atc/       -> {module}-data-atc.blade.php (Action & Table Controller Script)
    ├── pedagang/          -> Tampilan modul pedagang
    └── auth/              -> Tampilan form login dan register

public/
└── mine/
    └── script.js          -> Global helper listener (Bootstrap modal, Toast, DT reload)
```

### Folder Responsibilities Detail:

| Folder Name                  | Primary Function & Responsibility                                                                                 | What is FORBIDDEN                                                                 |
| :--------------------------- | :---------------------------------------------------------------------------------------------------------------- | :-------------------------------------------------------------------------------- |
| `app/Models/`                | Database schema, Eloquent relations (`hasMany`, `belongsTo`), data type casting.                                  | FORBIDDEN from containing lengthy business queries or being called directly from a controller. |
| `app/Repositories/`          | Single Point of Access to the database. Methods are `public static`, with `try-catch`, `Log::error`, and a `getDt()` method for Yajra. | FORBIDDEN from rendering views or returning HTTP responses.                       |
| `app/Http/Controllers/`      | ONLY 2 functions: server-side DataTables JSON (`dataDt()`) and binary file downloads (PDF/Excel).                | FORBIDDEN from rendering HTML views (`return view()`) and from handling form submissions. |
| `app/Livewire/`              | Manages UI state, form validation, mounting edit data, triggering modals, calling Repos, and dispatching alerts/toasts. | FORBIDDEN from writing raw SQL queries or manipulating the DB without a Repository. |
| `resources/views/templates/` | Shared scaffold (Master layout, Universal Modal, Toast Container, Sidebar, Header).                               | FORBIDDEN from containing module-specific CRUD forms.                             |
| `resources/views/mods/`      | All business feature views per role (`{role}/{module}/`).                                                         | FORBIDDEN from containing master layouts.                                         |
| `mods/{role}/{mod}/atc/`     | JavaScript AJAX DataTables scripts, column & action button definitions, `livewire:navigated` listener.            | FORBIDDEN from writing AJAX for form submissions (use `wire:submit` instead).     |

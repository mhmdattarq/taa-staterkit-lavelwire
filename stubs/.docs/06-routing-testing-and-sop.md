## 8. Routing & Middleware Boundaries Convention (`routes/web.php`)

```php
use App\Http\Controllers\{Module}Controller;
use App\Livewire\Admin\Dashboard\DashboardIndex as AdminDashboard;
use App\Livewire\Admin\{Module}\{Module}Create;
use App\Livewire\Admin\{Module}\{Module}Data;
use App\Livewire\Admin\{Module}\{Module}Edit;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Landing\LandingIndex;
use App\Livewire\Pedagang\Dashboard\DashboardIndex as PedagangDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Publik / Landing Page
Route::livewire('/', LandingIndex::class)->name('landing');

// 2. Area Tamu (Hanya untuk yang BELUM login)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
});

// 3. Area Autentikasi (Wajib Login)
Route::middleware('auth')->group(function () {

    // GROUP ROLE: ADMIN
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::livewire('/dashboard', AdminDashboard::class)->name('dashboard');

        // Modul Fitur
        Route::prefix('{module}')->name('{module}.')->group(function () {
            Route::get('/datatable', [{Module}Controller::class, 'dataDt'])->name('dt');
            Route::livewire('/data', {Module}Data::class)->name('data');
            Route::livewire('/create', {Module}Create::class)->name('create');
            Route::livewire('/edit/{id}', {Module}Edit::class)->name('edit');
        });
    });

    // GROUP ROLE: PEDAGANG
    Route::middleware('role:pedagang')->prefix('pedagang')->name('pedagang.')->group(function () {
        Route::livewire('/dashboard', PedagangDashboard::class)->name('dashboard');
    });

    // LOGOUT (Metode POST Aman)
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
```

---

## 9. Pest Automated Test Template (`tests/Feature/Admin/{Module}Test.php`)

```php
<?php

use App\Enums\Role;
use App\Livewire\Admin\{Module}\{Module}Create;
use App\Livewire\Admin\{Module}\{Module}Data;
use App\Livewire\Admin\{Module}\{Module}Edit;
use App\Models\Data{Module};
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => Role::Admin,
    ]);
});

test('halaman data {module} dapat diakses oleh admin', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.{module}.data'))
        ->assertOk()
        ->assertSeeLivewire({Module}Data::class);
});

test('admin dapat menambah data {module}', function () {
    $this->actingAs($this->admin);

    Livewire::test({Module}Create::class)
        ->set('form.nama_{module}', '{Module} Baru')
        ->set('form.keterangan', 'Deskripsi pengujian')
        ->call('formSubmit')
        ->assertDispatched('alert-show');

    $this->assertDatabaseHas('data_{module}s', [
        'nama_{module}' => '{Module} Baru',
    ]);
});

test('admin dapat mengedit data {module}', function () {
    $this->actingAs($this->admin);
    $item = Data{Module}::create(['nama_{module}' => 'Lama']);

    Livewire::test({Module}Edit::class, ['id' => $item->id])
        ->set('form.nama_{module}', 'Telah Diubah')
        ->call('formSubmit')
        ->assertRedirect(route('admin.{module}.data'));

    $this->assertDatabaseHas('data_{module}s', [
        'id' => $item->id,
        'nama_{module}' => 'Telah Diubah',
    ]);
});

test('admin dapat menghapus data {module}', function () {
    $this->actingAs($this->admin);
    $item = Data{Module}::create(['nama_{module}' => 'Hapus Saya']);

    Livewire::test({Module}Data::class)
        ->call('delete', ['id' => $item->id])
        ->assertDispatched('reloadDT')
        ->assertDispatched('closeModal');

    $this->assertDatabaseMissing('data_{module}s', [
        'id' => $item->id,
    ]);
});
```

---

## 10. Required Command Reference (Artisan & Tooling Cheat Sheet)

| Task                             | Terminal Command                                                |
| :------------------------------- | :-------------------------------------------------------------- |
| **Create Livewire Class Component** | `php artisan make:livewire Admin.{Module}.{Module}Data --class` |
| **Create Model + Migration**     | `php artisan make:model Data{Module} -m`                        |
| **Create DataTables Controller** | `php artisan make:controller {Module}Controller`                |
| **Run Database Migration**       | `php artisan migrate`                                           |
| **Create Pest Test File**        | `php artisan make:test --pest Admin/{Module}Test`               |
| **Run Specific Test File**       | `vendor/bin/pest tests/Feature/Admin/{Module}Test.php`          |
| **Run Entire Test Suite**        | `php artisan test --compact`                                    |
| **Auto-Format Code (Pint)**      | `vendor/bin/pint --dirty --format agent`                        |

---

## 11. Standard Operating Procedure (SOP) for Creating a New Module

When you or AI Antigravity are asked: _"Create a new CRUD module for {ModuleName}"_, follow these 8 sequential steps:

```
Langkah 1: Database   ──► php artisan make:model Data{Modul} -m  -> migrate
Langkah 2: Model      ──► app/Models/Data{Modul}.php -> $guarded = [], relations
Langkah 3: Repository ──► app/Repositories/{Modul}Repo.php -> static CRUD + getDt()
Langkah 4: Controller ──► app/Http/Controllers/{Modul}Controller.php -> dataDt() (Yajra)
Langkah 5: Livewire   ──► php artisan make:livewire Admin.{Modul}.{Modul}Data --class (Data, Create, Edit)
Langkah 6: Blade View ──► resources/views/mods/admin/{modul}/ (data, create, edit, atc/)
Langkah 7: Routing    ──► routes/web.php -> daftarkan dt, data, create, edit
Langkah 8: Testing    ──► Buat Pest test -> jalankan pest -> vendor/bin/pint
```

### Module Checklist Detail:

- [ ] Model uses `$guarded = []` with complete relations.
- [ ] Repository wraps `create/update/delete` in `try-catch` with `Log::error`.
- [ ] `Repo::getDt()` method returns a query instance, not a collection.
- [ ] Controller contains only the `dataDt()` method, returning `DataTables::of()->toJson()`.
- [ ] Livewire Data handles the `hookModalDelete` method and the `#[On('{Component}-delete')]` listener.
- [ ] ATC file handles AJAX DataTables, `wire:navigate` buttons, delete buttons, and the `livewire:navigated` and `reloadDT` events.
- [ ] Sidebar is updated with the `route('admin.{module}.data')` link and `wire:navigate`.
- [ ] At least 1 Pest Feature Test file is created and passes (`Pass`).
- [ ] Code is formatted with `pint`.

---

## 12. Quick Reference (Developer & AI Quick Reference)

| Question                                                            | Answer & PM Standard                                                                                                                            |
| :------------------------------------------------------------------ | :---------------------------------------------------------------------------------------------------------------------------------------------- |
| **Can a Controller render a Blade view?**                           | **NO**. Controllers are forbidden from rendering HTML views. Rendering web views is the exclusive responsibility of Livewire.                   |
| **Can Livewire call a Model directly for create/update?**           | **NO**. All database mutations must go through static methods in the Repository.                                                                |
| **Where does the DataTables script go?**                            | In the folder `mods/{role}/{module}/atc/{module}-data-atc.blade.php`.                                                                           |
| **How does DataTables reload after data is deleted?**               | Livewire dispatches the browser event `reloadDT`, and the JavaScript helper executes `dtTable.ajax.reload(null, false)`.                        |
| **What happens when the delete button in DataTables is clicked?**   | It executes `wire:click="hookModalDelete(id, name)"` which sends an event to the Universal Modal Livewire component at `templates/components/modal.blade.php`. |
| **What layout is used for the Login & Register pages?**             | `#[Layout('templates.layouts.guest')]` to keep them clean without the dashboard navigation.                                                     |
| **How does login work for admin vs. merchant?**                     | A single input field. If numeric $\rightarrow$ Merchant NIK; if text $\rightarrow$ Admin Username. Dashboard redirect happens automatically based on role. |

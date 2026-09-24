> [!CRITICAL]
>
> ### PERINGATAN MUTLAK UNTUK AI (AI DIRECTIVE: DILARANG HARDCODE ENTITAS CONTOH!)
>
> 1. **Entitas Seperti `Kios`, `Pasar`, dan `Pedagang` di dalam dokumen ini HANYALAH CONTOH STUDI KASUS.**
> 2. **AI DILARANG KERAS menyalin nama `Kios`, `Pasar`, atau kolom-kolomnya ke dalam modul baru atau project lain kecuali user secara eksplisit memintanya!**
> 3. **AI WAJIB MENERAPKAN POLA ARSITEKTUR INI DENGAN MENGGANTI VARIABEL SESUAI NAMA MODUL YANG DIMINTA OLEH USER.**
> 4. Setiap kali user meminta dibuatkan modul (misal: `Karyawan`, `Produk`, `Pasien`, `Pelanggan`, `Buku`, dll.), AI harus secara cerdas memetakan seluruh nama file, class, method, route, tabel, dan kolom sesuai entitas yang diminta!

---

### Matriks Pemetaan Variabel (Variable Mapping Matrix untuk AI)

Gunakan rumus pemetaan ini setiap kali membuat modul baru:

| Placeholder           | Format                   | Contoh Kasus 1: Modul Karyawan    | Contoh Kasus 2: Modul Produk    | Contoh Kasus 3: Modul Pasien    |
| :-------------------- | :----------------------- | :-------------------------------- | :------------------------------ | :------------------------------ |
| `{Module}`            | PascalCase               | `Karyawan`                        | `Produk`                        | `Pasien`                        |
| `{module}`            | kebab / snake            | `karyawan`                        | `produk`                        | `pasien`                        |
| `{table}`             | snake_case plural        | `data_karyawans` / `karyawans`    | `data_produks` / `produks`      | `data_pasiens` / `pasiens`      |
| **Model**             | `app/Models/`            | `DataKaryawan.php`                | `DataProduk.php`                | `DataPasien.php`                |
| **Repository**        | `app/Repositories/`      | `KaryawanRepo.php`                | `ProdukRepo.php`                | `PasienRepo.php`                |
| **Controller**        | `app/Http/Controllers/`  | `KaryawanController.php`          | `ProdukController.php`          | `PasienController.php`          |
| **Livewire Index**    | `app/Livewire/Admin/...` | `Karyawan\KaryawanData.php`       | `Produk\ProdukData.php`         | `Pasien\PasienData.php`         |
| **Livewire Create**   | `app/Livewire/Admin/...` | `Karyawan\KaryawanCreate.php`     | `Produk\ProdukCreate.php`       | `Pasien\PasienCreate.php`       |
| **Livewire Edit**     | `app/Livewire/Admin/...` | `Karyawan\KaryawanEdit.php`       | `Produk\ProdukEdit.php`         | `Pasien\PasienEdit.php`         |
| **Blade Folder**      | `resources/views/mods/`  | `mods/admin/karyawan/`            | `mods/admin/produk/`            | `mods/admin/pasien/`            |
| **Blade Index**       | Di dalam folder blade    | `karyawan-data.blade.php`         | `produk-data.blade.php`         | `pasien-data.blade.php`         |
| **Blade Create**      | Di dalam folder blade    | `karyawan-create.blade.php`       | `produk-create.blade.php`       | `pasien-create.blade.php`       |
| **Blade Edit**        | Di dalam folder blade    | `karyawan-edit.blade.php`         | `produk-edit.blade.php`         | `pasien-edit.blade.php`         |
| **Skrip ATC**         | `atc/` di folder blade   | `atc/karyawan-data-atc.blade.php` | `atc/produk-data-atc.blade.php` | `atc/pasien-data-atc.blade.php` |
| **ID Tabel HTML**     | `#table{Module}`         | `#tableKaryawan`                  | `#tableProduk`                  | `#tablePasien`                  |
| **Fungsi Init ATC**   | `init{Module}Table()`    | `initKaryawanTable()`             | `initProdukTable()`             | `initPasienTable()`             |
| **Route Group**       | Prefix di `web.php`      | `admin/karyawan/*`                | `admin/produk/*`                | `admin/pasien/*`                |
| **Route DT**          | Name route DataTables    | `admin.karyawan.dt`               | `admin.produk.dt`               | `admin.pasien.dt`               |
| **Route Data**        | Name route Index         | `admin.karyawan.data`             | `admin.produk.data`             | `admin.pasien.data`             |
| **Route Create**      | Name route Create        | `admin.karyawan.create`           | `admin.produk.create`           | `admin.pasien.create`           |
| **Route Edit**        | Name route Edit          | `admin.karyawan.edit`             | `admin.produk.edit`             | `admin.pasien.edit`             |
| **Event Delete Hook** | Event modal              | `KaryawanData-delete`             | `ProdukData-delete`             | `PasienData-delete`             |
| **Feature Test**      | `tests/Feature/Admin/`   | `KaryawanTest.php`                | `ProdukTest.php`                | `PasienTest.php`                |

---

## 1. Filosofi & Aturan Emas Arsitektur Atta Stack

Dokumen ini adalah **Product Requirement & Architecture Blueprint (PRD-LW)** resmi yang merangkum seluruh standar arsitektur, konvensi penulisan kode, alur kerja (workflow), serta pembagian tanggung jawab modul pada proyek berbasis **Laravel + Livewire**.

Standar ini dirancang secara detail dan presisi agar **Antigravity (AI Coding Assistant)** maupun AI di proyek lain dapat langsung memahami gaya penulisan (_PM Standard_) tanpa ambiguitas, serta dapat langsung menyalin template kode yang siap pakai (copy-pasteable parameterized templates) dengan mengganti variabel sesuai kebutuhan fitur user.

### 5 Aturan Emas (Golden Rules):

1. **Strict Separation of Concerns (SoC)**:
    - **Livewire**: Mengendalikan seluruh UI, state, validasi form, interaktivitas, dan event dispatch. Berperilaku seperti Single Page Application (SPA) melalui `wire:navigate`.
    - **Repository (`app/Repositories/`)**: Satu-satunya tempat penulisan logika data, transaksi database, manipulasi Eloquent, dan error logging.
    - **Controller (`app/Http/Controllers/`)**: **Ultra-Thin Controller**. HANYA memiliki 2 tugas: melayani data JSON Yajra DataTables (`dataDt()`) dan melayani unduhan binary (PDF/Excel). **DILARANG MERENDER VIEW (`return view()`) DAN DILARANG MENERIMA SUBMIT FORM CRUD!**
    - **Model (`app/Models/`)**: Representasi skema database, relasi Eloquent (`hasMany`, `belongsTo`), dan casting. Tidak boleh memuat query bisnis langsung dari luar.
2. **Pemisahan Tampilan: `templates/` vs `mods/`**:
    - `resources/views/templates/`: Khusus kerangka global aplikasi (Layout master, Header, Sidebar, Footer, Modal universal, Toast).
    - `resources/views/mods/`: Khusus antarmuka modul bisnis per fitur/role (Halaman Data, Form Create, Form Edit, dan skrip `atc/`).
3. **Form State Berpola `$form = []`**:
   Semua input form pada komponen Livewire wajib dikelompokkan dalam satu properti array `$this->form = [...]` dan divalidasi dengan aturan eksplisit (`rules()`, `messages()`, `$validationAttributes`).
4. **Yajra DataTables Terisolasi & Reaktif**:
   DataTables jQuery di-mount di halaman Livewire melalui skrip Action & Table Controller (**ATC**). Aksi hapus memicu modal konfirmasi universal Livewire, dan reload tabel dilakukan via event browser `reloadDT` tanpa me-refresh browser.
5. **Autentikasi Murni Livewire Class Component**:
   Tidak menggunakan Laravel Breeze/Jetstream/Fortify. Login dan Register dibuat murni menggunakan Livewire Class Component dengan dukungan _Smart Dual-Identifier_ (deteksi otomatis Username/NIK) dan atomic database transaction (`DB::transaction`).

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

## 2. Struktur Folder & Tanggung Jawab Masing-Masing Lapisan

Setiap folder memiliki batasan tanggung jawab yang ketat (_Strict Boundaries_):

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

### Rincian Fungsi Setiap Folder:

| Nama Folder                  | Fungsi & Tanggung Jawab Utama                                                                                     | Hal yang DILARANG                                                                 |
| :--------------------------- | :---------------------------------------------------------------------------------------------------------------- | :-------------------------------------------------------------------------------- |
| `app/Models/`                | Skema database, relasi Eloquent (`hasMany`, `belongsTo`), casting tipe data.                                      | DILARANG memuat query bisnis panjang atau dipanggil langsung dari controller.     |
| `app/Repositories/`          | Single Point of Access database. Method `public static`, `try-catch`, `Log::error`, method `getDt()` untuk Yajra. | DILARANG merender view atau mengembalikan response HTTP.                          |
| `app/Http/Controllers/`      | HANYA 2 fungsi: DataTables server-side JSON (`dataDt()`) dan unduhan berkas binary (PDF/Excel).                   | DILARANG merender view HTML (`return view()`) dan dilarang menangani form submit. |
| `app/Livewire/`              | Mengatur state UI, validasi form, mount data edit, trigger modal, panggil Repo, dispatch alert/toast.             | DILARANG menulis query SQL mentah atau manipulasi DB tanpa Repository.            |
| `resources/views/templates/` | Kerangka bersama (Master layout, Universal Modal, Toast Container, Sidebar, Header).                              | DILARANG menaruh form CRUD spesifik modul di sini.                                |
| `resources/views/mods/`      | Seluruh tampilan fitur bisnis per role (`{role}/{module}/`).                                                      | DILARANG menaruh master layout di sini.                                           |
| `mods/{role}/{mod}/atc/`     | Skrip Javascript AJAX DataTables, definisi kolom & tombol aksi, listener `livewire:navigated`.                    | DILARANG menulis AJAX untuk submit form (gunakan `wire:submit`).                  |

---

## 3. Konfigurasi Wajib Fondasi Sistem (Source Code Kunci)

Konfigurasi berikut adalah **pondasi mutlak** agar arsitektur ini dapat berjalan di Laravel:

### 3.1. Konfigurasi Livewire (`config/livewire.php`)

Konfigurasi ini memetakan Livewire agar otomatis mengenali direktori `templates/` dan `mods/`:

```php
<?php

return [
    // 1. Lokasi komponen view
    'component_locations' => [
        resource_path('views/templates/components'),
        resource_path('views/mods'),
    ],

    // 2. Pemetaan namespace layout dan halaman
    'component_namespaces' => [
        'layouts' => resource_path('views/templates/layouts'),
        'pages'   => resource_path('views/mods'),
    ],

    // 3. Layout default aplikasi
    'component_layout' => 'layouts::app',

    // 4. Pengaturan pembuatan class Livewire
    'class_namespace' => 'App\\Livewire',
    'class_path'      => app_path('Livewire'),
    'view_path'       => resource_path('views/mods'),

    // 5. SPA Progress Bar
    'navigate' => [
        'show_progress_bar'  => true,
        'progress_bar_color' => '#2299dd',
    ],
];
```

### 3.2. Registrasi Middleware Role (`bootstrap/app.php`)

Di Laravel 11/12/13, alias middleware didaftarkan pada method `withMiddleware`:

```php
<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->create();
```

### 3.3. File Middleware Multi-Role (`app/Http/Middleware/RoleMiddleware.php`)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role->value, $roles, true)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
```

### 3.4. Enum Role (`app/Enums/Role.php`)

```php
<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Pedagang = 'pedagang';
}
```

### 3.5. Model User (`app/Models/User.php`)

```php
<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isPedagang(): bool
    {
        return $this->role === Role::Pedagang;
    }
}
```

### 3.6. Global Helper Javascript Listener (`public/mine/script.js`)

File ini menjembatani event browser yang di-dispatch dari Livewire untuk menutup modal Bootstrap, menampilkan Toast, dan me-reload DataTables:

```javascript
// 1. Listener Menutup Modal Bootstrap
window.addEventListener("closeModal", (param) => {
    const id =
        param.detail?.id ??
        (Array.isArray(param.detail) ? param.detail[0]?.id : param.detail);
    if (id) {
        const el = document.getElementById(id);
        if (el && typeof bootstrap !== "undefined") {
            const modal =
                bootstrap.Modal.getInstance(el) ||
                bootstrap.Modal.getOrCreateInstance(el);
            modal.hide();
        }
        $("#" + id).modal("hide");
        $(".modal-backdrop").remove();
        $("body")
            .removeClass("modal-open")
            .css("overflow", "")
            .css("padding-right", "");
    }
});

// 2. Listener Membuka Modal Bootstrap
window.addEventListener("showModal", (param) => {
    const id =
        param.detail?.id ??
        (Array.isArray(param.detail) ? param.detail[0]?.id : param.detail);
    if (id) {
        const el = document.getElementById(id);
        if (el && typeof bootstrap !== "undefined") {
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        }
        $("#" + id).modal("show");
    }
});

// 3. Listener Reload DataTables Reaktif Tanpa Refresh Halaman
window.addEventListener("reloadDT", (param) => {
    const dtName =
        param.detail?.data ??
        (Array.isArray(param.detail) ? param.detail[0]?.data : param.detail);
    try {
        if (window[dtName]) {
            window[dtName].ajax.reload(null, false);
        } else {
            eval(dtName).ajax.reload(null, false);
        }
    } catch (e) {
        $(".table.dataTable").each(function () {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().ajax.reload(null, false);
            }
        });
    }
});
```

### 3.7. Komponen Universal Modal (`resources/views/templates/components/modal.blade.php`)

Komponen modal terpusat (Single File Component) untuk konfirmasi hapus maupun preview dokumen:

```php
<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public $modalId = 'modalDelete';
    public $data = [];

    public function mount($modalId = 'modalDelete')
    {
        $this->modalId = $modalId;
    }

    #[On('modal-delete-setDeleteId')]
    public function setDeleteId($data)
    {
        $this->data = is_array($data) && isset($data['data']) ? $data['data'] : $data;
    }

    public function process($id = null)
    {
        $dtHook = [
            'id' => $id,
            'payload' => $this->data['payload'] ?? null,
        ];
        $this->dispatch($this->data['dispatch'] ?? 'ModulData-delete', $dtHook);
    }
};
?>

<div>
    <div class="modal fade" id="{{ $data['modalId'] ?? $modalId }}" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0">{{ $data['title'] ?? 'Konfirmasi Tindakan' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-3 text-center align-self-center">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 54px;"></i>
                        </div>
                        <div class="col-lg-9">
                            <h5 class="text-danger">{{ $data['title'] ?? 'Konfirmasi Hapus' }}</h5>
                            <p class="mb-0 text-muted">{{ $data['msg'] ?? 'Apakah Anda yakin ingin menghapus data ini?' }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" wire:click="process({{ $data['id'] ?? 0 }})">
                        <i class="fas fa-trash-alt me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 3.8. Komponen Toast Notifikasi (`resources/views/templates/components/toast.blade.php`)

```php
<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public $isShow = false;
    public $type = 'success';
    public $msg = '';

    public function mount()
    {
        if (session()->has('alert-show')) {
            $this->show(session('alert-show'));
        }
    }

    #[On('alert-show')]
    public function show($data = [])
    {
        if (is_array($data)) {
            $this->type = $data['type'] ?? 'success';
            $this->msg = $data['message'] ?? ($data['msg'] ?? '');
        } else {
            $this->msg = (string) $data;
        }

        $this->isShow = true;
    }
};
?>

<div>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        @if ($isShow)
            <div class="toast show align-items-center text-white bg-{{ $type }} border-0 shadow" role="alert"
                x-data="{ show: true }"
                x-init="setTimeout(() => { show = false; $wire.set('isShow', false); }, 4000)"
                x-show="show"
                x-transition>
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-1 align-middle fs-16"></i>
                        {{ $msg }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        wire:click="$set('isShow', false)" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
</div>
```

---

## 4. Alur Autentikasi & Registrasi (Auth Flow Standar PM)

Sistem autentikasi murni menggunakan Livewire Class Component tanpa Breeze/Fortify.

```
                    ┌──────────────────────────────────────────────┐
                    │            ALUR LOGIN BERBASIS ROLE          │
                    └──────────────────────────────────────────────┘

                                [ Input: $identifier ]
                                (Username atau NIK KTP)
                                           │
                         Is Numeric? (Hanya Angka / NIK)
                                   ├── YA  ──► Kolom: 'nik'      (Akun Pedagang)
                                   └── TIDAK ─► Kolom: 'username' (Akun Admin)
                                           │
                                           ▼
                          [ Auth::attempt($credentials) ]
                                           │
                        ┌──────────────────┴──────────────────┐
                      GAGAL                                 SUKSES
                        │                                     │
                        ▼                                     ▼
              Set $errorMessage                      session()->regenerate()
           (Muncul Alert Merah di UI)                         │
                                                    Cek $user->isAdmin()
                                                 ┌────────────┴────────────┐
                                               ADMIN                    PEDAGANG
                                                 │                         │
                                                 ▼                         ▼
                                       route('admin.dashboard')  route('pedagang.dashboard')
```

### 4.1. Komponen Login (`app/Livewire/Auth/Login.php`)

- **Command Pembuatan**:
    ```bash
    php artisan make:livewire Auth.Login --class
    ```
- **Source Code**:

    ```php
    namespace App\Livewire\Auth;

    use App\Models\User;
    use Illuminate\Support\Facades\Auth;
    use Livewire\Attributes\Layout;
    use Livewire\Component;

    #[Layout('templates.layouts.guest')]
    class Login extends Component
    {
        public string $identifier = '';
        public string $password = '';
        public bool $remember = false;
        public string $errorMessage = '';

        public function rules(): array
        {
            return [
                'identifier' => 'required',
                'password' => 'required|min:6',
            ];
        }

        public function login()
        {
            $this->validate();

            // SMART DUAL-IDENTIFIER LOGIC
            $field = is_numeric($this->identifier) ? 'nik' : 'username';

            $credentials = [
                $field => $this->identifier,
                'password' => $this->password,
            ];

            if (Auth::attempt($credentials, $this->remember)) {
                session()->regenerate();

                /** @var User $user */
                $user = Auth::user();

                return $user->isAdmin()
                    ? redirect()->intended(route('admin.dashboard'))
                    : redirect()->intended(route('pedagang.dashboard'));
            }

            $this->errorMessage = 'Username/NIK atau password yang Anda masukkan salah.';
        }

        public function render()
        {
            return view('mods.auth.login');
        }
    }
    ```

### 4.2. Komponen Registrasi Pedagang (`app/Livewire/Auth/Register.php`)

- **Command Pembuatan**:
    ```bash
    php artisan make:livewire Auth.Register --class
    ```
- **Prinsip Atomik (`DB::transaction`)**:
  Menyimpan ke 2 tabel sekaligus secara atomik (`users` dan `data_pedagangs`):

    ```php
    namespace App\Livewire\Auth;

    use App\Enums\Role;
    use App\Models\DataPedagang;
    use App\Models\User;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Livewire\Attributes\Layout;
    use Livewire\Component;

    #[Layout('templates.layouts.guest')]
    class Register extends Component
    {
        public array $form = [
            'nik' => '',
            'nama' => '',
            'tempat_lahir' => '',
            'tanggal_lahir' => '',
            'jenis_kelamin' => 'L',
            'no_telp' => '',
            'alamat' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        public function register()
        {
            $this->validate([
                'form.nik' => 'required|numeric|digits:16|unique:users,nik|unique:data_pedagangs,nik',
                'form.nama' => 'required|string|max:100',
                'form.password' => 'required|min:6|confirmed',
            ]);

            DB::transaction(function () {
                $user = User::create([
                    'name' => $this->form['nama'],
                    'nik' => $this->form['nik'],
                    'password' => Hash::make($this->form['password']),
                    'role' => Role::Pedagang,
                ]);

                DataPedagang::create([
                    'user_id' => $user->id,
                    'nik' => $this->form['nik'],
                    'nama' => $this->form['nama'],
                    'tempat_lahir' => $this->form['tempat_lahir'] ?: null,
                    'tanggal_lahir' => $this->form['tanggal_lahir'] ?: null,
                    'jenis_kelamin' => $this->form['jenis_kelamin'],
                    'no_telp' => $this->form['no_telp'] ?: null,
                    'alamat' => $this->form['alamat'] ?: null,
                ]);
            });

            session()->flash('success', 'Pendaftaran berhasil! Silakan masuk.');
            return $this->redirectRoute('login', navigate: true);
        }

        public function render()
        {
            return view('mods.auth.register');
        }
    }
    ```

---

## 5. Alur Kerja Yajra DataTables x Livewire (Yajra DT Flow)

Sering timbul pertanyaan: _"Bagaimana skrip ATC bekerja dan bagaimana sinkronisasi antara Yajra DataTables jQuery dengan Livewire tanpa bentrok?"_

Berikut adalah siklus aliran data lengkap:

```
[ BROWSER ]
     │
     │  1. Muat Halaman: mods/admin/kios/kios-data.blade.php
     │     - Terdapat tabel: <table id="tableKios" class="table">
     │     - Container tabel dibungkus atribut: <div wire:ignore>
     │     - Meng-include script ATC: @include('mods.admin.kios.atc.kios-data-atc')
     │
     │  2. AJAX Request Server-side (Otomatis oleh jQuery DataTables)
     ▼
[ ROUTE: GET /admin/kios/datatable (name: admin.kios.dt) ]
     │
     ▼
[ CONTROLLER: KiosController@dataDt ]
     │  Panggil Repository:
     │  $data = KiosRepo::getDt();
     ▼
[ REPOSITORY: KiosRepo::getDt() ]
     │  Kembalikan Query Builder (Bukan ->get()):
     │  return DataKios::query()->with('pasar');
     ▼
[ YAJRA DATATABLES ENGINE ]
     │  - Mengeksekusi SQL LIMIT, OFFSET, ORDER BY, dan WHERE pencarian
     │  - Mengembalikan output JSON server-side
     ▼
[ JAVASCRIPT ATC DI BROWSER (kios-data-atc.blade.php) ]
     │  - Merender baris tabel secara dinamis
     │  - Tombol Edit menggunakan: wire:navigate
     │  - Tombol Hapus menggunakan: wire:click="hookModalDelete(id, identity)"
     │  - Menghubungkan event reload: window.addEventListener('reloadDT', ...)
```

### 5.1. Implementasi Controller Yajra DataTables (`app/Http/Controllers/KiosController.php`)

Controller bertugas memanggil Repository untuk mengambil Query Builder, lalu menyerahkannya ke Yajra DataTables untuk diubah menjadi JSON:

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\KiosRepo;
use Yajra\DataTables\Facades\DataTables;

class KiosController extends Controller
{
    public function dataDt()
    {
        // 1. Ambil Query Builder dari Repository (tanpa ->get())
        $data = KiosRepo::getDt();

        // 2. Kembalikan respons JSON server-side melalui Yajra
        return DataTables::of($data)
            ->toJson();
    }
}
```

---

### 5.2. Source Code Lengkap Skrip ATC (`resources/views/mods/admin/kios/atc/kios-data-atc.blade.php`)

File ini adalah **file ATC nyata** dari proyek ini. File ini di-include pada view data (`@include('mods.admin.kios.atc.kios-data-atc')`) dan di-push ke stack `@push('js-stack')`:

```html
@push('css')
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0;
        margin: 0 2px;
    }

    #tableKios_filter,
    #tableKios_length {
        margin-bottom: 12px;
    }

    #tableKios th,
    #tableKios td {
        vertical-align: middle;
    }

    .table-responsive {
        min-height: 260px;
    }

    #tableKios {
        width: 100% !important;
    }

    #tableKios .dropdown {
        position: relative;
        display: inline-block;
    }

    #tableKios .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1060 !important;
    }
</style>
@endpush @push('js-stack')
<script>
    function initKiosTable() {
        var tableEl = document.getElementById('tableKios');
        if (!tableEl) return;

        // 1. Hancurkan (destroy) instance lama jika tabel sudah terinisialisasi
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableKios')) {
            $('#tableKios').DataTable().destroy();
        }

        // 2. Inisialisasi DataTables Baru
        if ($.fn.DataTable) {
            window.dtTable = $('#tableKios').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                scrollX: false,
                autoWidth: false,
                pageLength: 25,
                dom: 'lrtip',
                order: [
                    [2, 'asc']
                ],
                ajax: '{{ route('admin.kios.dt') }}',
                columns: [
                    // Kolom Checkbox Baris
                    {
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return '<input class="form-check-input check-data-item" type="checkbox" value="' +
                                data.id + '">';
                        }
                    },

                    // Kolom Aksi Dropdown (Edit wire:navigate & Hapus hookModalDelete)
                    {
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let url = "{{ route('admin.kios.edit', ':id') }}";
                            let editUrl = url.replace(':id', row.id);
                            let identity = String(data.nomor_kios || '').replace(/'/g, "\\'");

                            return `
                                <div class="dropdown">
                                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu shadow">
                                        <a class="dropdown-item" href="${editUrl}" wire:navigate>
                                            <i class="fas fa-pencil-alt me-2 text-warning"></i> Edit
                                        </a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#modalDelete"
                                           wire:click="hookModalDelete(${data.id}, '${identity}')">
                                            <i class="fas fa-trash-alt me-2"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    },

                    // Kolom Nomor Urut Otomatis
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },

                    // Kolom Nomor Kios
                    {
                        data: 'nomor_kios',
                        name: 'nomor_kios',
                        orderable: true,
                        searchable: true
                    },

                    // Kolom Ukuran Kios
                    {
                        data: 'ukuran_kios',
                        name: 'ukuran_kios',
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },

                    // Kolom Harga Sewa (Format Rupiah)
                    {
                        data: 'harga_sewa',
                        name: 'harga_sewa',
                        orderable: true,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return data ? 'Rp ' + Number(data).toLocaleString('id-ID') : '-';
                        }
                    },

                    // Kolom Satuan Retribusi
                    {
                        data: 'satuan_retribusi',
                        name: 'satuan_retribusi',
                        orderable: true,
                        searchable: false,
                        className: 'text-center text-capitalize'
                    },

                    // Kolom Status Kios (Badge Warna)
                    {
                        data: 'status_kios',
                        name: 'status_kios',
                        orderable: true,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            if (data === 'tersedia') {
                                return '<span class="badge bg-success-subtle text-success">Tersedia</span>';
                            } else if (data === 'terisi') {
                                return '<span class="badge bg-danger-subtle text-danger">Terisi</span>';
                            } else {
                                return '<span class="badge bg-warning-subtle text-warning">Pengajuan</span>';
                            }
                        }
                    },

                    // Kolom Lokasi Kios
                    {
                        data: 'lokasi_kios',
                        name: 'lokasi_kios',
                        orderable: true,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return data ? data : '-';
                        }
                    },

                    // Kolom Relasi Lokasi Pasar
                    {
                        data: 'pasar.nama_pasar',
                        name: 'pasar.nama_pasar',
                        orderable: true,
                        searchable: true,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return row.pasar ? row.pasar.nama_pasar : '-';
                        }
                    },
                ],
                initComplete: function(settings) {
                    var table = settings.oInstance.api();

                    // 3. Filter Kolom Input pada Thead Kedua (#header-filter)
                    $('#header-filter input.search-col-dt').on('keyup change clear', function() {
                        var colIndex = $(this).closest('th').index();
                        if (table.column(colIndex).search() !== this.value) {
                            table.column(colIndex).search(this.value).draw();
                        }
                    });

                    // 4. Checkbox Pilih Semua
                    $('.check-data-all').on('change', function() {
                        $('.check-data-item').prop('checked', this.checked);
                    });
                }
            });
        }
    }

    // 5. Lifecycle Pengaktifan Tabel:
    // Saat pertama kali halaman dimuat (Full Refresh)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKiosTable);
    } else {
        initKiosTable();
    }

    // Saat navigasi SPA Livewire (wire:navigate)
    document.addEventListener('livewire:navigated', initKiosTable);
</script>
@endpush
```

---

### 5.3. Penjelasan 5 Kunci Penting Skrip ATC:

1. **Pencegahan Double-Mount (`destroy`)**:
   Sebelum inisialisasi, skrip selalu memeriksa `if ($.fn.DataTable.isDataTable('#tableKios')) { $('#tableKios').DataTable().destroy(); }`. Ini mencegah error _"Cannot reinitialise DataTable"_ saat berpindah halaman via `wire:navigate`.
2. **Koneksi Navigasi SPA (`livewire:navigated`)**:
   Karena berpindah rute di Livewire tidak mereload window browser, skrip didaftarkan pada event:
    ```javascript
    document.addEventListener("livewire:navigated", initKiosTable);
    ```
    Sehingga setiap kali pengguna berpindah halaman menggunakan `wire:navigate`, tabel langsung di-init ulang secara otomatis.
3. **Pemicu Modal Hapus Universal (`wire:click="hookModalDelete"`)**:
   Tombol hapus di-render di dalam JavaScript dengan atribut Livewire:
    ```html
    data-bs-toggle="modal" data-bs-target="#modalDelete"
    wire:click="hookModalDelete(${data.id}, '${identity}')"
    ```
    Karena tabel berada di dalam scope komponen Livewire `KiosData`, klik tombol ini langsung memicu method PHP `hookModalDelete` di class komponen tanpa AJAX manual.
4. **Pembungkus `wire:ignore` di Blade Parent**:
   Pada `kios-data.blade.php`, elemen pembungkus tabel wajib diberi atribut `<div class="card-body" wire:ignore>`. Atribut ini memberi tahu Livewire agar tidak menyentuh atau merusak perubahan struktur DOM yang dibuat oleh jQuery DataTables.
5. **Reload Reaktif Tanpa Refresh (`reloadDT`)**:
   Setelah proses hapus berhasil di Livewire, Livewire memanggil `$this->dispatch('reloadDT', data: 'dtTable')`. Helper JavaScript global (`public/mine/script.js`) menangkap event ini dan mengeksekusi `window.dtTable.ajax.reload(null, false)`, sehingga baris data yang dihapus langsung lenyap dari tabel tanpa refresh browser!

---

## 6. Siklus Hidup Lengkap CRUD (End-to-End CRUD Flow)

### 6.1. Alur CREATE (Memasukkan Data Baru)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        ALUR MEMASUKKAN DATA BARU (CREATE FLOW)                         │
└────────────────────────────────────────────────────────────────────────────────────────┘

 [ 1. Form View HTML (mods/.../kios-create.blade.php) ]
      - User mengetik input yang terikat dengan wire:model="form.{field}"
      - User menekan tombol "Simpan" -> trigger form: wire:submit="formSubmit"
             │
             ▼
 [ 2. Komponen Livewire (app/Livewire/Admin/Kios/KiosCreate.php) ]
      - Menerima aksi method formSubmit()
      - Menjalankan validasi: $this->validate() (mencocokkan rules(), messages(), validationAttributes)
      - Menyiapkan array payload bersih: $payload = [...]
      - Memanggil method Repository: KiosRepo::create($payload)
             │
             ▼
 [ 3. Repository Layer (app/Repositories/KiosRepo.php) ]
      - Menjalankan blok try-catch:
        try {
            DataKios::create($data); // Menyimpan via Model Eloquent
            return true;
        } catch (\Exception $e) {
            Log::error('Insert data kios gagal', ['error' => $e->getMessage()]);
            return false;
        }
             │
             ▼
 [ 4. Response Kembali ke Komponen Livewire ]
      - Menerima nilai boolean (true / false)
      - JIKA TRUE:
        * Dispatch toast sukses: $this->dispatch('alert-show', ['type' => 'success', ...])
        * Mengosongkan form input: $this->resetForm()
        * (Opsional: SPA Redirect -> $this->redirectRoute('admin.kios.data', navigate: true))
      - JIKA FALSE:
        * Dispatch toast error: $this->dispatch('alert-show', ['type' => 'danger', ...])
             │
             ▼
 [ 5. UI Feedback di Browser (templates/components/toast.blade.php & public/mine/script.js) ]
      - Event browser 'alert-show' ditangkap oleh JavaScript helper
      - Memunculkan Bootstrap Toast notifikasi hijau/merah di pojok kanan atas secara instan tanpa reload browser!
```

### 6.2. Alur DELETE (Menghapus Data via Universal Modal)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                   ALUR PENGHAPUSAN DATA DENGAN UNIVERSAL MODAL (DELETE FLOW)           │
└────────────────────────────────────────────────────────────────────────────────────────┘

 [ 1. Baris Baris DataTables di Browser (Skrip ATC) ]
      - User mengklik tombol "Hapus" pada baris tabel
      - Elemen tombol memicu:
        data-bs-toggle="modal" data-bs-target="#modalDelete"
        wire:click="hookModalDelete(id, identity)"
             │
             ▼
 [ 2. Komponen Livewire Index (app/Livewire/Admin/Kios/KiosData.php) ]
      - Method hookModalDelete($id, $identity) menerima event
      - Menyiapkan payload: ['id' => $id, 'title' => '...', 'msg' => '...', 'dispatch' => 'KiosData-delete']
      - Men-dispatch ke modal: $this->dispatch('modal-delete-setDeleteId', $dtHook)
             │
             ▼
 [ 3. Komponen Universal Modal (resources/views/templates/components/modal.blade.php) ]
      - Menangkap event 'modal-delete-setDeleteId'
      - Mengisi judul modal & pesan konfirmasi sesuai data yang ingin dihapus
      - Modal popup Bootstrap #modalDelete muncul di layar
      - User menekan tombol konfirmasi "Ya, Hapus"
      - Modal men-dispatch event target: $this->dispatch('KiosData-delete', id: $this->deleteId)
             │
             ▼
 [ 4. Class Livewire Eksekutor (KiosData.php) ]
      - Listener atribut menangkap event: #[On('KiosData-delete')] public function delete($data)
      - Mengambil ID target
      - Memanggil Repository: KiosRepo::delete($id)
             │
             ▼
 [ 5. Repository Layer (app/Repositories/KiosRepo.php) ]
      - Menjalankan blok try-catch:
        try {
            $kios = self::getById($id);
            $kios->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Delete data kios gagal', ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
             │
             ▼
 [ 6. Respon Sukses di Komponen Livewire ]
      - Menerima true dari Repository
      - 1. Tutup modal: $this->dispatch('closeModal', id: 'modalDelete')
      - 2. Notifikasi: $this->dispatch('alert-show', ['type' => 'success', ...])
      - 3. REAKTIF RELOAD: $this->dispatch('reloadDT', data: 'dtTable')
             │
             ▼
 [ 7. Helper JavaScript di Browser (public/mine/script.js & ATC) ]
      - Menutup modal Bootstrap #modalDelete
      - Memunculkan Bootstrap Toast hijau ("Data Berhasil dihapus")
      - Menjalankan window.dtTable.ajax.reload(null, false)
      - Tabel ter-update otomatis seketika TANPA REFRESH HALAMAN!
```

---

## 7. Master Template Lengkap Siap Pakai (Parameterized Boilerplates)

Bagian ini menyediakan source code lengkap yang dapat disalin dan disesuaikan langsung dengan mengganti variabel:

- `{Module}` $\rightarrow$ Nama Modul PascalCase (contoh: `Produk`, `Kategori`, `Kios`)
- `{module}` $\rightarrow$ Nama Modul kebab-case / snake_case (contoh: `produk`, `kategori`, `kios`)
- `{table}` $\rightarrow$ Nama tabel database (contoh: `data_produks`, `data_kios`)

---

### 7.1. Template Model (`app/Models/Data{Module}.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data{Module} extends Model
{
    use HasFactory;

    protected $table = 'data_{module}s';

    protected $guarded = [];

    // Definisikan Relasi Eloquent
    public function relasiParent()
    {
        return $this->belongsTo(DataParent::class, 'parent_id');
    }
}
```

---

### 7.2. Template Repository (`app/Repositories/{Module}Repo.php`)

```php
<?php

namespace App\Repositories;

use App\Models\Data{Module};
use Illuminate\Support\Facades\Log;

class {Module}Repo
{
    // Mengembalikan Query Builder untuk Yajra DataTables (Bukan ->get())
    public static function getDt()
    {
        return Data{Module}::query();
    }

    public static function getById($id)
    {
        return Data{Module}::findOrFail($id);
    }

    public static function create(array $data): bool
    {
        try {
            Data{Module}::create($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Insert data {module} gagal', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public static function update($id, array $data): bool
    {
        try {
            $item = self::getById($id);
            $item->update($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Update data {module} gagal', ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public static function delete($id): bool
    {
        try {
            $item = self::getById($id);
            $item->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Delete data {module} gagal', ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
```

---

### 7.3. Template Controller DataTables (`app/Http/Controllers/{Module}Controller.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\{Module}Repo;
use Yajra\DataTables\Facades\DataTables;

class {Module}Controller extends Controller
{
    public function dataDt()
    {
        $data = {Module}Repo::getDt();

        return DataTables::of($data)
            ->toJson();
    }
}
```

---

### 7.4. Template Livewire Data Component (`app/Livewire/Admin/{Module}/{Module}Data.php`)

```php
<?php

namespace App\Livewire\Admin\{Module};

use App\Repositories\{Module}Repo;
use Livewire\Attributes\On;
use Livewire\Component;

class {Module}Data extends Component
{
    public function hookModalDelete($id, $identity)
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus',
            'msg' => 'Apakah Anda yakin ingin menghapus data '.$identity.' ?',
            'dispatch' => '{Module}Data-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('{Module}Data-delete')]
    public function delete($data)
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $process = {Module}Repo::delete($id);

        if ($process) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data {Module} Berhasil dihapus.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan saat menghapus data.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.{module}.{module}-data');
    }
}
```

---

### 7.5. Template Livewire Create Component (`app/Livewire/Admin/{Module}/{Module}Create.php`)

```php
<?php

namespace App\Livewire\Admin\{Module};

use App\Repositories\{Module}Repo;
use Livewire\Component;

class {Module}Create extends Component
{
    public array $form = [];

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->form = [
            'nama_{module}' => '',
            'keterangan' => '',
        ];
    }

    public function rules(): array
    {
        return [
            'form.nama_{module}' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'form.nama_{module}.required' => 'Nama {Module} wajib diisi.',
        ];
    }

    public array $validationAttributes = [
        'form.nama_{module}' => 'Nama {Module}',
    ];

    public function formSubmit()
    {
        $this->validate();

        $payload = [
            'nama_{module}' => $this->form['nama_{module}'],
            'keterangan' => $this->form['keterangan'] ?: null,
        ];

        $process = {Module}Repo::create($payload);

        if ($process) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data {Module} Berhasil disimpan.',
            ]);
            $this->resetForm();
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menyimpan data.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.{module}.{module}-create');
    }
}
```

---

### 7.6. Template Livewire Edit Component (`app/Livewire/Admin/{Module}/{Module}Edit.php`)

```php
<?php

namespace App\Livewire\Admin\{Module};

use App\Repositories\{Module}Repo;
use Livewire\Component;

class {Module}Edit extends Component
{
    public int $id;
    public array $form = [];

    public function mount($id)
    {
        $this->id = $id;
        $item = {Module}Repo::getById($id);

        $this->form = [
            'nama_{module}' => $item->nama_{module},
            'keterangan' => $item->keterangan ?? '',
        ];
    }

    public function rules(): array
    {
        return [
            'form.nama_{module}' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'form.nama_{module}.required' => 'Nama {Module} wajib diisi.',
        ];
    }

    public array $validationAttributes = [
        'form.nama_{module}' => 'Nama {Module}',
    ];

    public function formSubmit()
    {
        $this->validate();

        $payload = [
            'nama_{module}' => $this->form['nama_{module}'],
            'keterangan' => $this->form['keterangan'] ?: null,
        ];

        $process = {Module}Repo::update($this->id, $payload);

        if ($process) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data {Module} Berhasil diperbarui.',
            ]);

            return $this->redirectRoute('admin.{module}.data', navigate: true);
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat memperbarui data.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.{module}.{module}-edit');
    }
}
```

---

### 7.7. Template Blade Index Data (`resources/views/mods/admin/{module}/{module}-data.blade.php`)

```html
<div>
    <div class="row mb-2">
        <div class="col-12">
            <div
                class="page-title-box d-sm-flex align-items-center justify-content-between"
            >
                <h4 class="mb-sm-0">Data {Module}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="javascript: void(0);">Master</a>
                        </li>
                        <li class="breadcrumb-item active">Data {Module}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <h4 class="card-title">Manajemen Data {Module}</h4>
                        </div>
                        <div class="col-auto ms-auto">
                            <a
                                href="{{ route('admin.{module}.create') }}"
                                class="btn btn-primary"
                                wire:navigate
                            >
                                <i class="fas fa-plus-circle me-1"></i> Tambah
                                {Module}
                            </a>
                        </div>
                    </div>
                </div>
                {{-- PENTING: wire:ignore mencegah Livewire merusak DOM
                DataTables --}}
                <div class="card-body" wire:ignore>
                    <div class="table-responsive">
                        <table
                            id="table{Module}"
                            class="table table-bordered table-striped w-100 align-middle"
                        >
                            <thead>
                                <tr>
                                    <th style="width: 20px" class="text-center">
                                        Check
                                    </th>
                                    <th style="width: 50px" class="text-center">
                                        Aksi
                                    </th>
                                    <th style="width: 40px" class="text-center">
                                        No
                                    </th>
                                    <th>Nama {Module}</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <thead id="header-filter">
                                <tr>
                                    <th class="text-center">
                                        <input
                                            type="checkbox"
                                            class="form-check-input check-data-all"
                                        />
                                    </th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center">
                                        <input
                                            type="text"
                                            class="form-control form-control-sm text-center search-col-dt"
                                            placeholder="Cari..."
                                        />
                                    </th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Komponen Universal Modal --}}
    <livewire:modal />

    {{-- Include Skrip ATC --}}
    @include('mods.admin.{module}.atc.{module}-data-atc')
</div>
```

---

### 7.8. Template Skrip ATC DataTables (`resources/views/mods/admin/{module}/atc/{module}-data-atc.blade.php`)

```html
@push('css')
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0;
        margin: 0 2px;
    }
    #table{Module}_filter, #table{Module}_length {
        margin-bottom: 12px;
    }
    #table{Module} th, #table{Module} td {
        vertical-align: middle;
    }
    .table-responsive {
        min-height: 260px;
    }
    #table{Module} {
        width: 100% !important;
    }
    #table{Module} .dropdown {
        position: relative;
        display: inline-block;
    }
    #table{Module} .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1060 !important;
    }
</style>
@endpush @push('js-stack')
<script>
    function init{Module}Table() {
        var tableEl = document.getElementById('table{Module}');
        if (!tableEl) return;

        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#table{Module}')) {
            $('#table{Module}').DataTable().destroy();
        }

        if ($.fn.DataTable) {
            window.dtTable = $('#table{Module}').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                scrollX: false,
                autoWidth: false,
                pageLength: 25,
                dom: 'lrtip',
                order: [[2, 'asc']],
                ajax: '{{ route('admin.{module}.dt') }}',
                columns: [
                    {
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return '<input class="form-check-input check-data-item" type="checkbox" value="' + data.id + '">';
                        }
                    },
                    {
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let url = "{{ route('admin.{module}.edit', ':id') }}";
                            let editUrl = url.replace(':id', row.id);
                            let identity = String(data.nama_{module} || '').replace(/'/g, "\\'");

                            return `
                                <div class="dropdown">
                                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu shadow">
                                        <a class="dropdown-item" href="${editUrl}" wire:navigate>
                                            <i class="fas fa-pencil-alt me-2 text-warning"></i> Edit
                                        </a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#modalDelete"
                                           wire:click="hookModalDelete(${data.id}, '${identity}')">
                                            <i class="fas fa-trash-alt me-2"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_{module}',
                        name: 'nama_{module}',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? data : '-';
                        }
                    }
                ],
                initComplete: function(settings) {
                    var table = settings.oInstance.api();
                    $('#header-filter input.search-col-dt').on('keyup change clear', function() {
                        var colIndex = $(this).closest('th').index();
                        if (table.column(colIndex).search() !== this.value) {
                            table.column(colIndex).search(this.value).draw();
                        }
                    });

                    $('.check-data-all').on('change', function() {
                        $('.check-data-item').prop('checked', this.checked);
                    });
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init{Module}Table);
    } else {
        init{Module}Table();
    }

    document.addEventListener('livewire:navigated', init{Module}Table);
</script>
@endpush
```

---

### 7.9. Template Blade Form Create (`resources/views/mods/admin/{module}/{module}-create.blade.php`)

```html
<div>
    <div class="row mb-2">
        <div class="col-12">
            <div
                class="page-title-box d-sm-flex align-items-center justify-content-between"
            >
                <h4 class="mb-sm-0">Tambah Data {Module}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('admin.{module}.data') }}"
                                wire:navigate
                                >Data {Module}</a
                            >
                        </li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah {Module}</h4>
                </div>
                <div class="card-body">
                    <form wire:submit="formSubmit">
                        <div class="mb-3">
                            <label class="form-label"
                                >Nama {Module}
                                <span class="text-danger">*</span></label
                            >
                            <input
                                type="text"
                                class="form-control @error('form.nama_{module}') is-invalid @enderror"
                                wire:model="form.nama_{module}"
                                placeholder="Masukkan nama {module}"
                            />
                            @error('form.nama_{module}')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea
                                class="form-control @error('form.keterangan') is-invalid @enderror"
                                wire:model="form.keterangan"
                                rows="3"
                                placeholder="Masukkan keterangan tambahan"
                            ></textarea>
                            @error('form.keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route('admin.{module}.data') }}"
                                class="btn btn-secondary"
                                wire:navigate
                                >Batal</a
                            >
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

### 7.10. Template Blade Form Edit (`resources/views/mods/admin/{module}/{module}-edit.blade.php`)

```html
<div>
    <div class="row mb-2">
        <div class="col-12">
            <div
                class="page-title-box d-sm-flex align-items-center justify-content-between"
            >
                <h4 class="mb-sm-0">Edit Data {Module}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('admin.{module}.data') }}"
                                wire:navigate
                                >Data {Module}</a
                            >
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit {Module}</h4>
                </div>
                <div class="card-body">
                    <form wire:submit="formSubmit">
                        <div class="mb-3">
                            <label class="form-label"
                                >Nama {Module}
                                <span class="text-danger">*</span></label
                            >
                            <input
                                type="text"
                                class="form-control @error('form.nama_{module}') is-invalid @enderror"
                                wire:model="form.nama_{module}"
                            />
                            @error('form.nama_{module}')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea
                                class="form-control @error('form.keterangan') is-invalid @enderror"
                                wire:model="form.keterangan"
                                rows="3"
                            ></textarea>
                            @error('form.keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route('admin.{module}.data') }}"
                                class="btn btn-secondary"
                                wire:navigate
                                >Batal</a
                            >
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 8. Konvensi Routing & Middleware Boundaries (`routes/web.php`)

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

## 9. Template Automated Test Pest (`tests/Feature/Admin/{Module}Test.php`)

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

## 10. Daftar Command Wajib (Artisan & Tooling Cheat Sheet)

| Kebutuhan                        | Perintah Terminal                                               |
| :------------------------------- | :-------------------------------------------------------------- |
| **Buat Komponen Livewire Class** | `php artisan make:livewire Admin.{Module}.{Module}Data --class` |
| **Buat Model + Migrasi**         | `php artisan make:model Data{Module} -m`                        |
| **Buat Controller DataTables**   | `php artisan make:controller {Module}Controller`                |
| **Jalankan Migrasi Database**    | `php artisan migrate`                                           |
| **Buat File Test Pest**          | `php artisan make:test --pest Admin/{Module}Test`               |
| **Jalankan File Test Spesifik**  | `vendor/bin/pest tests/Feature/Admin/{Module}Test.php`          |
| **Jalankan Seluruh Suite Test**  | `php artisan test --compact`                                    |
| **Format Kode Otomatis (Pint)**  | `vendor/bin/pint --dirty --format agent`                        |

---

## 11. Standar Prosedur Operasional (SOP) Pembuatan Modul Baru

Jika Anda atau AI Antigravity diminta: _"Buatkan modul CRUD baru untuk {NamaModul}"_, ikuti 8 langkah baku ini secara berurutan:

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

### Rincian Checklist Modul:

- [ ] Model menggunakan `$guarded = []` dan relasi lengkap.
- [ ] Repository membungkus `create/update/delete` dalam `try-catch` dan `Log::error`.
- [ ] Method `Repo::getDt()` mengembalikan instance query, bukan collection.
- [ ] Controller hanya berisi method `dataDt()`, mengembalikan `DataTables::of()->toJson()`.
- [ ] Livewire Data menangani method `hookModalDelete` dan listener `#[On('{Component}-delete')]`.
- [ ] File ATC meng-handle AJAX DataTables, tombol `wire:navigate`, tombol hapus, dan event `livewire:navigated` serta `reloadDT`.
- [ ] Sidebar di-update dengan link `route('admin.{module}.data')` dan `wire:navigate`.
- [ ] Minimal 1 file Pest Feature Test dibuat dan lulus pengujian (`Pass`).
- [ ] Kode diformat dengan `pint`.

---

## 12. Ringkasan Singkat (Developer & AI Quick Reference)

| Pertanyaan                                                          | Jawaban & Standar PM                                                                                                                            |
| :------------------------------------------------------------------ | :---------------------------------------------------------------------------------------------------------------------------------------------- |
| **Bolehkah Controller merender tampilan Blade?**                    | **TIDAK**. Controller dilarang merender view HTML. Tampilan web adalah hak prerogatif Livewire.                                                 |
| **Bolehkah Livewire memanggil Model langsung untuk create/update?** | **TIDAK**. Semua mutasi database wajib lewat method static di Repository.                                                                       |
| **Di mana script DataTables ditaruh?**                              | Di folder `mods/{role}/{module}/atc/{module}-data-atc.blade.php`.                                                                               |
| **Bagaimana DataTables me-reload setelah data dihapus?**            | Livewire men-dispatch event browser `reloadDT`, Javascript helper menjalankan `dtTable.ajax.reload(null, false)`.                               |
| **Bagaimana jika tombol hapus di DataTables diklik?**               | Menjalankan `wire:click="hookModalDelete(id, name)"` yang mengirim event ke Universal Modal Livewire di `templates/components/modal.blade.php`. |
| **Layout apa yang dipakai halaman Login & Register?**               | `#[Layout('templates.layouts.guest')]` agar bersih tanpa navigasi dashboard.                                                                    |
| **Bagaimana cara login admin vs pedagang?**                         | Cukup 1 form input. Jika numerik $\rightarrow$ NIK Pedagang, jika teks $\rightarrow$ Username Admin. Redireksi dashboard otomatis sesuai role.  |

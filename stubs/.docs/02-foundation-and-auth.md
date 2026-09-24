## 3. Required System Foundation Configuration (Key Source Files)

The following configuration is the **absolute foundation** for this architecture to run in Laravel:

### 3.1. Livewire Configuration (`config/livewire.php`)

This configuration maps Livewire to automatically recognize the `templates/` and `mods/` directories:

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

### 3.2. Role Middleware Registration (`bootstrap/app.php`)

In Laravel 11/12/13, middleware aliases are registered in the `withMiddleware` method:

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

### 3.3. Multi-Role Middleware File (`app/Http/Middleware/RoleMiddleware.php`)

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

### 3.4. Role Enum (`app/Enums/Role.php`)

```php
<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Pedagang = 'pedagang';
}
```

### 3.5. User Model (`app/Models/User.php`)

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

### 3.6. Global JavaScript Helper Listener (`public/mine/script.js`)

This file bridges browser events dispatched from Livewire to close Bootstrap modals, display Toasts, and reload DataTables:

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

### 3.7. Universal Modal Component (`resources/views/templates/components/modal.blade.php`)

A centralized modal component (Single File Component) used for delete confirmations and document previews:

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

### 3.8. Toast Notification Component (`resources/views/templates/components/toast.blade.php`)

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

## 4. Authentication & Registration Flow (PM Standard Auth Flow)

The authentication system is built purely with Livewire Class Components — no Breeze/Fortify.

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

### 4.1. Login Component (`app/Livewire/Auth/Login.php`)

- **Make Command**:
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

### 4.2. Merchant Registration Component (`app/Livewire/Auth/Register.php`)

- **Make Command**:
    ```bash
    php artisan make:livewire Auth.Register --class
    ```
- **Atomic Principle (`DB::transaction`)**:
  Saves atomically to 2 tables at once (`users` and `data_pedagangs`):

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

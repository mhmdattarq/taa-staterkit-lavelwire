## 7. Complete Ready-to-Use Master Templates (Parameterized Boilerplates)

This section provides complete source code that can be copied and customized directly by replacing the variables:

- `{Module}` $\rightarrow$ Module name in PascalCase (e.g., `Produk`, `Kategori`, `Kios`)
- `{module}` $\rightarrow$ Module name in kebab-case / snake_case (e.g., `produk`, `kategori`, `kios`)
- `{table}` $\rightarrow$ Database table name (e.g., `data_produks`, `data_kios`)

---

### 7.1. Model Template (`app/Models/Data{Module}.php`)

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

### 7.2. Repository Template (`app/Repositories/{Module}Repo.php`)

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

### 7.3. DataTables Controller Template (`app/Http/Controllers/{Module}Controller.php`)

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

### 7.4. Livewire Data Component Template (`app/Livewire/Admin/{Module}/{Module}Data.php`)

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

### 7.5. Livewire Create Component Template (`app/Livewire/Admin/{Module}/{Module}Create.php`)

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

### 7.6. Livewire Edit Component Template (`app/Livewire/Admin/{Module}/{Module}Edit.php`)

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

### 7.7. Blade Index Data Template (`resources/views/mods/admin/{module}/{module}-data.blade.php`)

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

### 7.8. DataTables ATC Script Template (`resources/views/mods/admin/{module}/atc/{module}-data-atc.blade.php`)

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

### 7.9. Blade Create Form Template (`resources/views/mods/admin/{module}/{module}-create.blade.php`)

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

### 7.10. Blade Edit Form Template (`resources/views/mods/admin/{module}/{module}-edit.blade.php`)

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

## 5. Yajra DataTables x Livewire Workflow (Yajra DT Flow)

A common question arises: _"How does the ATC script work, and how does Yajra DataTables jQuery synchronize with Livewire without conflicts?"_

The following is the complete data flow cycle:

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

### 5.1. Yajra DataTables Controller Implementation (`app/Http/Controllers/KiosController.php`)

The Controller's responsibility is to call the Repository to retrieve a Query Builder, then hand it off to Yajra DataTables to be transformed into JSON:

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

### 5.2. Complete ATC Script Source Code (`resources/views/mods/admin/kios/atc/kios-data-atc.blade.php`)

This is the **actual ATC file** from this project. It is included in the data view (`@include('mods.admin.kios.atc.kios-data-atc')`) and pushed to the `@push('js-stack')` stack:

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

### 5.3. Explanation of the 5 Key ATC Script Points:

1. **Double-Mount Prevention (`destroy`)**:
   Before initialization, the script always checks `if ($.fn.DataTable.isDataTable('#tableKios')) { $('#tableKios').DataTable().destroy(); }`. This prevents the _"Cannot reinitialise DataTable"_ error when navigating between pages via `wire:navigate`.
2. **SPA Navigation Connection (`livewire:navigated`)**:
   Because navigating routes in Livewire does not reload the browser window, the script is registered on the event:
    ```javascript
    document.addEventListener("livewire:navigated", initKiosTable);
    ```
    This ensures that every time the user navigates using `wire:navigate`, the table is automatically re-initialized.
3. **Universal Delete Modal Trigger (`wire:click="hookModalDelete"`)**:
   The delete button is rendered inside JavaScript with Livewire attributes:
    ```html
    data-bs-toggle="modal" data-bs-target="#modalDelete"
    wire:click="hookModalDelete(${data.id}, '${identity}')"
    ```
    Because the table is within the scope of the `KiosData` Livewire component, clicking this button directly triggers the `hookModalDelete` PHP method on the component class without manual AJAX.
4. **`wire:ignore` Wrapper in the Parent Blade**:
   In `kios-data.blade.php`, the table wrapper element must have the `<div class="card-body" wire:ignore>` attribute. This tells Livewire not to touch or disrupt the DOM structure managed by jQuery DataTables.
5. **Reactive Reload Without Refresh (`reloadDT`)**:
   After a successful delete in Livewire, Livewire calls `$this->dispatch('reloadDT', data: 'dtTable')`. The global JavaScript helper (`public/mine/script.js`) catches this event and executes `window.dtTable.ajax.reload(null, false)`, instantly removing the deleted row from the table without a browser refresh!

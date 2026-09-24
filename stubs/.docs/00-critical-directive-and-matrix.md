> [!CRITICAL]
>
> ### ABSOLUTE WARNING FOR AI (AI DIRECTIVE: DO NOT HARDCODE EXAMPLE ENTITIES!)
>
> 1. **Entities such as `Kios`, `Pasar`, and `Pedagang` in this document are CASE STUDY EXAMPLES ONLY.**
> 2. **AI is STRICTLY FORBIDDEN from copying the names `Kios`, `Pasar`, or their columns into new modules or other projects unless the user explicitly requests it!**
> 3. **AI MUST APPLY THIS ARCHITECTURE PATTERN BY REPLACING VARIABLES WITH THE MODULE NAME REQUESTED BY THE USER.**
> 4. Every time the user requests a new module (e.g., `Karyawan`, `Produk`, `Pasien`, `Pelanggan`, `Buku`, etc.), AI must intelligently map all file names, classes, methods, routes, tables, and columns to match the requested entity!

---

### Variable Mapping Matrix (Variable Mapping Matrix untuk AI)

Use this mapping formula every time a new module is created:

| Placeholder           | Format                   | Example Case 1: Karyawan Module   | Example Case 2: Produk Module   | Example Case 3: Pasien Module   |
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
| **Blade Index**       | Inside blade folder      | `karyawan-data.blade.php`         | `produk-data.blade.php`         | `pasien-data.blade.php`         |
| **Blade Create**      | Inside blade folder      | `karyawan-create.blade.php`       | `produk-create.blade.php`       | `pasien-create.blade.php`       |
| **Blade Edit**        | Inside blade folder      | `karyawan-edit.blade.php`         | `produk-edit.blade.php`         | `pasien-edit.blade.php`         |
| **ATC Script**        | `atc/` in blade folder   | `atc/karyawan-data-atc.blade.php` | `atc/produk-data-atc.blade.php` | `atc/pasien-data-atc.blade.php` |
| **HTML Table ID**     | `#table{Module}`         | `#tableKaryawan`                  | `#tableProduk`                  | `#tablePasien`                  |
| **ATC Init Function** | `init{Module}Table()`    | `initKaryawanTable()`             | `initProdukTable()`             | `initPasienTable()`             |
| **Route Group**       | Prefix in `web.php`      | `admin/karyawan/*`                | `admin/produk/*`                | `admin/pasien/*`                |
| **Route DT**          | DataTables route name    | `admin.karyawan.dt`               | `admin.produk.dt`               | `admin.pasien.dt`               |
| **Route Data**        | Index route name         | `admin.karyawan.data`             | `admin.produk.data`             | `admin.pasien.data`             |
| **Route Create**      | Create route name        | `admin.karyawan.create`           | `admin.produk.create`           | `admin.pasien.create`           |
| **Route Edit**        | Edit route name          | `admin.karyawan.edit`             | `admin.produk.edit`             | `admin.pasien.edit`             |
| **Event Delete Hook** | Modal event              | `KaryawanData-delete`             | `ProdukData-delete`             | `PasienData-delete`             |
| **Feature Test**      | `tests/Feature/Admin/`   | `KaryawanTest.php`                | `ProdukTest.php`                | `PasienTest.php`                |

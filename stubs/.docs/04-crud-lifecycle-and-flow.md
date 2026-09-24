## 6. Complete CRUD Lifecycle (End-to-End CRUD Flow)

### 6.1. CREATE Flow (Inserting New Data)

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

### 6.2. DELETE Flow (Deleting Data via Universal Modal)

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

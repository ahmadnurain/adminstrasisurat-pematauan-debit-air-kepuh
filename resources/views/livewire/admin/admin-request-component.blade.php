<div>

    <div class="max-w-4xl mx-auto mt-10 py-6 bg-white shadow-xl p-8 my-10 rounded-xl">
        <h1 class="text-2xl font-bold mb-5">Ajukan Permintaan Surat</h1>

        <!-- Form Pengajuan Surat -->
        <form wire:submit.prevent="submit" class="space-y-6">
            <!-- Upload KTP -->
            <div>
                <label for="image_ktp" class="block text-sm font-medium text-gray-700">Foto KTP</label>
                <input type="file" wire:model="image_ktp" id="image_ktp" class="mt-1 block w-full text-sm">
                @error('image_ktp')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Upload Selfie -->
            <div>
                <label for="image_selfie" class="block text-sm font-medium text-gray-700">Foto Selfie</label>
                <input type="file" wire:model="image_selfie" id="image_selfie" class="mt-1 block w-full text-sm">
                @error('image_selfie')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Pilih Jenis Surat -->
            <div>
                <label for="type_letter_id" class="block text-sm font-medium text-gray-700">Jenis Surat</label>
                <select wire:model.live="type_letter_id" id="type_letter_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Jenis Surat --</option>
                    @foreach ($typeLetters as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('type_letter_id')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Form Tambahan -->
            @if ($isFormVisible)
                @foreach ($formFields as $field)
                    <div>
                        <label for="{{ $field['name'] }}"
                            class="block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                        @if ($field['type'] === 'text' || $field['type'] === 'date' || $field['type'] === 'number' || $field['type'] === 'time')
                            <input type="{{ $field['type'] }}" wire:model="formFieldsValues.{{ $field['name'] }}"
                                id="{{ $field['name'] }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="{{ $field['placeholder'] }}"
                                @if (isset($field['oninput'])) oninput="{{ $field['oninput'] }}" @endif
                                @if (isset($field['readonly']) && $field['readonly']) readonly @endif>
                        @elseif ($field['type'] === 'textarea')
                            <textarea wire:model="formFieldsValues.{{ $field['name'] }}" id="{{ $field['name'] }}" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="{{ $field['placeholder'] }}"></textarea>
                        @elseif ($field['type'] === 'select')
                            <select wire:model="formFieldsValues.{{ $field['name'] }}" id="{{ $field['name'] }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih {{ $field['label'] }} --</option>
                                @foreach ($field['options'] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error("formFieldsValues.{$field['name']}")
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach



                @if ($type_letter_id == 6)
                    <div class="mt-6 border-t pt-4">
                        <h3 class="text-lg font-semibold mb-4">Detail Taksiran</h3>

                        <!-- Input Field Dinamis untuk Detail Taksiran -->
                        @foreach ($assessmentFields as $field)
                            <div class="mb-4">
                                <label for="assessment_{{ $field['name'] }}"
                                    class="block text-sm font-medium text-gray-700">
                                    {{ $field['label'] }}
                                </label>
                                <input type="text"
                                    wire:model.lazy="formFieldsValues.assessment_{{ $field['name'] }}"
                                    id="assessment_{{ $field['name'] }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="{{ $field['placeholder'] }}"
                                    @if (isset($field['readonly']) && $field['readonly']) readonly @endif
                                    value="{{ isset($formFieldsValues['assessment_' . $field['name']])
                                        ? number_format((float) str_replace('.', '', $formFieldsValues['assessment_' . $field['name']]), 0, ',', '.')
                                        : '' }}"
                                    oninput="this.value = formatRupiah(this.value)">
                            </div>
                        @endforeach
                    </div>
                @endif


                @if ($type_letter_id == 12)
                    <div class="">

                        <!-- Pilihan Alasan -->
                        <div class="mb-4">
                            <label for="moving_reason" class="block text-sm font-medium text-gray-700">Alasan
                                Pindah</label>
                            <select wire:model.live="moving_reason" id="moving_reason"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Numpang KK">Numpang KK</option>
                                <option value="Membuat KK Baru">Membuat KK Baru</option>
                                <option value="Nomor KK Tetap">Nomor KK Tetap</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Input Tambahan Jika Alasan "Lainnya" Dipilih -->
                        @if ($moving_reason === 'Lainnya')
                            <div class="mb-4">
                                <label for="moving_reason_other" class="block text-sm font-medium text-gray-700">
                                    Alasan Pindah (Lainnya)
                                </label>
                                <input type="text" wire:model="moving_reason_other" id="moving_reason_other"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Masukkan alasan pindah lainnya">
                            </div>
                        @endif
                    </div>
                @endif


                <!-- Tambah Anggota Keluarga untuk Surat Pindah (Type Letter ID 2) -->
                @if ($type_letter_id == 2 || $type_letter_id == 12)
                    <div class="mt-6 border-t pt-4">
                        <h3 class="text-lg font-semibold mb-4">Tambah Anggota Keluarga</h3>

                        <!-- Family Member Input Fields -->
                        @foreach ($familyMemberFields as $field)
                            <div class="mb-4">
                                <label for="family_{{ $field['name'] }}"
                                    class="block text-sm font-medium text-gray-700">
                                    {{ $field['label'] }}
                                </label>
                                <input type="{{ $field['type'] }}"
                                    wire:model="formFieldsValues.family_{{ $field['name'] }}"
                                    id="family_{{ $field['name'] }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="{{ $field['placeholder'] }}">

                                @error('formFieldsValues.family_' . $field['name'])
                                    <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach

                        <!-- Tombol Tambah Anggota Keluarga -->
                        <div class="mb-4">
                            <button type="button" wire:click.prevent="addFamilyMember"
                                class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                Tambah Anggota Keluarga
                            </button>
                        </div>

                        <!-- Daftar Anggota Keluarga yang Ditambahkan -->
                        @if (!empty($familyMembers))
                            <div class="mt-4">
                                <h4 class="text-md font-semibold mb-2">Daftar Anggota Keluarga</h4>
                                <div class="space-y-2">
                                    @foreach ($familyMembers as $index => $member)
                                        <div class="flex justify-between items-center bg-gray-100 p-3 rounded-md">
                                            <div>
                                                <span class="font-medium">NIK:</span> {{ $member['nik'] ?? 'N/A' }} |
                                                <span class="font-medium">Nama:</span> {{ $member['name'] ?? 'N/A' }}
                                                |
                                                <span class="font-medium">Status:</span>
                                                {{ $member['shdk'] ?? 'N/A' }}
                                            </div>
                                            <button type="button"
                                                wire:click.prevent="removeFamilyMember({{ $index }})"
                                                class="text-red-500 hover:text-red-700">
                                                Hapus
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            @endif

            <!-- Submit Button -->
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Ajukan Permintaan
            </button>
        </form>
    </div>
</div>
<script>
    // Fungsi untuk memformat angka menjadi format rupiah
    function formatRupiah(angka) {
        // Hapus karakter non-digit
        let numberString = angka.replace(/[^,\d]/g, '');
        let split = numberString.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    // Pilih semua elemen dengan class "price_assessment"
    let priceInputs = document.querySelectorAll('.price_assessment');

    // Tambahkan event listener hanya pada elemen dengan class "price_assessment"
    priceInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            input.value = formatRupiah(input.value); // Format nilai saat input berubah
        });
    });

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, ''); // Hanya angka
        value = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(value);

        input.value = value.replace("Rp", "").trim(); // Opsional: menghapus simbol "Rp"
    }
</script>

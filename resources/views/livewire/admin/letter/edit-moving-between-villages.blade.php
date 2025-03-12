<div class="max-w-4xl mx-auto my-6  bg-white shadow-xl p-8  rounded-xl">
    <h2 class="text-2xl font-bold mb-4">Edit Surat Permohonan Pindah Satu Desa</h2>

    @if (session()->has('message'))
        <div class="mb-4 text-green-500">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="update">
        @foreach ([
        ['number_letter', 'Nomor Surat'],

        ['origin_family_card_number', 'No. KK Asal', 'number'],
        ['origin_head_of_family_name', 'Nama Kepala Keluarga Asal'],
        ['origin_address', 'Alamat Asal'],
        ['origin_rt', 'RT Asal', 'number'],
        ['origin_rw', 'RW Asal', 'number'],
        ['origin_hamlet', 'Dusun Asal'],
        ['origin_village', 'Desa/Kelurahan Asal'],
        ['origin_district', 'Kecamatan Asal'],
        ['origin_regency', 'Kabupaten/Kota Asal'],
        ['origin_province', 'Provinsi Asal'],
        ['origin_postal_code', 'Kode Pos Asal', 'number'],
        ['origin_phone', 'No. Telepon Asal', 'number'],

        ['applicant_nik', 'NIK Pemohon', 'number'],
        ['applicant_full_name', 'Nama Pemohon'],
        ['moving_reason', 'Alasan Pindah'],

        ['destination_address', 'Alamat Tujuan'],
        ['destination_rt', 'RT Tujuan', 'number'],
        ['destination_rw', 'RW Tujuan', 'number'],
        ['destination_hamlet', 'Dusun Tujuan'],
        ['destination_village', 'Desa/Kelurahan Tujuan'],
        ['destination_district', 'Kecamatan Tujuan'],
        ['destination_regency', 'Kabupaten/Kota Tujuan'],
        ['destination_province', 'Provinsi Tujuan'],
        ['destination_postal_code', 'Kode Pos Tujuan', 'number'],
        ['destination_phone', 'No. Telepon Tujuan', 'number'],

        ['moving_type', 'Jenis Kepindahan', 'text', 'Masukkan jenis kepindahan'],
        ['kk_status_not_moving', 'Status KK Tidak Pindah', 'text', 'Masukkan status KK tidak pindah'],
        ['kk_status_moving', 'Status KK Pindah', 'text', 'Masukkan status KK pindah'], // DIUBAH MENJADI INPUT
    ] as $field)
            <div class="mb-4">
                <label for="{{ $field[0] }}"
                    class="block text-sm font-medium text-gray-700">{{ $field[1] }}</label>
                <input type="{{ $field[2] ?? 'text' }}" id="{{ $field[0] }}" wire:model="{{ $field[0] }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error($field[0])
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        @endforeach



        {{-- <div class="mb-4">
            <label for="kk_status_moving" class="block text-sm font-medium text-gray-700">Status KK Pindah</label>
            <select id="kk_status_moving" wire:model="kk_status_moving"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="" disabled selected>Pilih Status KK Pindah</option>
                <option value="Tetap">Tetap</option>
                <option value="Baru">Baru</option>
            </select>
            @error('kk_status_moving')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div> --}}

        <!-- Add Family Member Section -->

        <!-- Add Family Member Section -->
        <div class="mt-6 border-t pt-4">
            <h3 class="text-lg font-semibold mb-4">Tambah Anggota Keluarga</h3>

            <!-- Render Existing Family Members -->
            @foreach ($familyMembers as $index => $member)
                <div class="mb-4" wire:key="family-member-{{ $index }}">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <!-- NIK -->
                        <div>
                            <label for="family_{{ $index }}_nik" class="block text-sm font-medium text-gray-700">
                                NIK
                            </label>
                            <input type="number" wire:model="familyMembers.{{ $index }}.nik"
                                id="family_{{ $index }}_nik"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Masukkan NIK">
                            @error('familyMembers.' . $index . '.nik')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nama -->
                        <div>
                            <label for="family_{{ $index }}_name"
                                class="block text-sm font-medium text-gray-700">
                                Nama
                            </label>
                            <input type="text" wire:model="familyMembers.{{ $index }}.name"
                                id="family_{{ $index }}_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Masukkan Nama">
                            @error('familyMembers.' . $index . '.name')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Masa Berlaku KTP -->
                        <div>
                            <label for="family_{{ $index }}_ktp_expiry"
                                class="block text-sm font-medium text-gray-700">
                                Masa Berlaku KTP
                            </label>
                            <input type="date" wire:model="familyMembers.{{ $index }}.ktp_expiry"
                                id="family_{{ $index }}_ktp_expiry"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('familyMembers.' . $index . '.ktp_expiry')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status Hubungan Keluarga -->
                        <div>
                            <label for="family_{{ $index }}_shdk"
                                class="block text-sm font-medium text-gray-700">
                                Status Hubungan Keluarga
                            </label>
                            <input type="text" wire:model="familyMembers.{{ $index }}.shdk"
                                id="family_{{ $index }}_shdk"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Masukkan Status">
                            @error('familyMembers.' . $index . '.shdk')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="button" wire:click.prevent="removeFamilyMember({{ $index }})"
                        class="mt-2 text-red-500 hover:text-red-700">Hapus Anggota</button>
                </div>
            @endforeach






            <!-- Add Family Member Button -->
            <div class="mb-4">
                <button type="button" wire:click.prevent="addFamilyMember"
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                    Tambah Anggota Keluarga
                </button>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Simpan</button>
            <a href="" class="ml-4 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</a>
        </div>
    </form>
</div>

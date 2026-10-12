<div>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Permintaan Surat</h1>
            </div>
            <div class="sm:flex sm:items-center">
                <div class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                    <div class="lg:pr-3">
                        <label for="users-search" class="sr-only">Search</label>
                        <div class="relative mt-1 lg:w-64 xl:w-96">
                            <input type="text" wire:model.live.debounce.300ms="q" id="users-search" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Cari nama, jenis surat, atau ID">
                        </div>
                    </div>
                    <div class="pl-3">
                        <select wire:model.live="status" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Status</option>
                            <option value="1">Belum Disetujui</option>
                            <option value="2">Disetujui</option>
                            <option value="3">Ditolak</option>
                            <option value="4">Diproses</option>
                            <option value="5">Selesai</option>
                        </select>
                    </div>
                    <div class="pl-3">
                        <button type="button" wire:click="resetFilters" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-gray-700 rounded-lg bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white dark:focus:ring-gray-700">Reset</button>
                    </div>
                </div>
                <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                    <a href="{{ route('admin.request.letter') }}" wire:navigate>
                        <button type="button" class="inline-flex items-center justify-center w-1/2 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 sm:w-auto dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                            Tambah Permintaan
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">No</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Nama</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jenis Surat</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Foto KTP</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Foto Selfie + KTP</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($requests as $request)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key="req-{{ $request->id }}">
                                    <td class="p-4">
                                        <div class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $loop->iteration }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $request->user->name }}
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $request->typeLetter->name }}
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <a href="javascript:void(0)" class="text-blue-600 underline cursor-pointer" wire:click="openModal('{{ Storage::url($request->image_ktp) }}')">Lihat KTP</a>
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <a href="javascript:void(0)" class="text-blue-600 underline cursor-pointer" wire:click="openModal('{{ Storage::url($request->image_selfie) }}')">Lihat Selfie</a>
                                    </td>
                                    <td class="p-4 text-base font-normal text-gray-900 whitespace-nowrap dark:text-white">
                                        @switch($request->request_status_id)
                                            @case(1)
                                                <span class="bg-red-100 text-red-800 text-sm font-bold me-2 px-3 py-2 rounded-full dark:bg-red-900 dark:text-red-300">Belum Disetujui</span>
                                            @break
                                            @case(2)
                                                <span class="bg-lime-100 text-lime-800 text-sm font-bold me-2 px-3 py-1 rounded-full dark:bg-lime-900 dark:text-lime-300">Disetujui</span>
                                            @break
                                            @case(3)
                                                <span class="bg-red-100 text-red-800 text-sm font-bold me-2 px-3 py-1 rounded-full dark:bg-red-900 dark:text-red-300">Ditolak</span>
                                            @break
                                            @case(4)
                                                <span class="bg-amber-100 text-amber-800 text-sm font-bold me-2 px-3 py-1 rounded-full dark:bg-amber-900 dark:text-amber-300">Diproses</span>
                                            @break
                                            @case(5)
                                                <span class="bg-green-100 text-green-800 text-sm font-bold me-2 px-3 py-1 rounded-full dark:bg-green-900 dark:text-green-300">Selesai</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="p-4 space-x-2 whitespace-nowrap">
                                        @switch($request->request_status_id)
                                            @case(1)
                                                <button type="button" wire:click="confirmApprove({{ $request->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Setujui</button>
                                                <button type="button" wire:click="confirmReject({{ $request->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">Tolak</button>
                                            @break
                                            @case(2)
                                                <button type="button" wire:click="confirmProcess({{ $request->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-amber-700 hover:bg-amber-800 focus:ring-4 focus:ring-amber-300 dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800">Proses</button>
                                            @break
                                            @case(3)
                                            @break
                                            @case(4)
                                                <button type="button" wire:click="confirmComplete({{ $request->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Selesai</button>
                                            @break
                                            @case(5)

                                            @break
                                        @endswitch
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>

                    <div class="px-4 py-3 bg-white border-t dark:bg-gray-800 dark:border-gray-700 flex items-center justify-between">
                        <div class="text-sm text-gray-600 dark:text-gray-300">Navigasi</div>
                        <div class="space-x-2">
                            <button type="button" @disabled(!$prevCursor) wire:click="goto('{{ $prevCursor }}')" class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 disabled:opacity-40">Sebelumnya</button>
                            <button type="button" @disabled(!$nextCursor) wire:click="goto('{{ $nextCursor }}')" class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 disabled:opacity-40">Berikutnya</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <x-modal name="image-preview-modal" wire:model.defer="isModalOpen" blur='sm'>
        <x-card title="Image Preview">
            @if ($modalImage)
                <img src="{{ $modalImage }}" alt="Image Preview" class="max-h-screen max-w-full rounded-md" />
            @else
                <p class="text-gray-500">No image to preview</p>
            @endif
        </x-card>
    </x-modal>


</div>

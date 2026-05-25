<div id="toggleModalBackdrop" class="fixed inset-0 bg-black/50 z-40 hidden"></div>

<div id="toggleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-md rounded-[2rem] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirmar cambio de estado</h3>
            <p id="toggleModalMessage" class="mt-1 text-sm text-gray-500 dark:text-gray-400"></p>
        </div>

        <form id="toggleStatusForm" method="POST" class="px-6 py-5">
            @csrf
            @method('PATCH')

            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeToggleStatusModal()" class="px-5 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-[#44B0B3] hover:bg-[#389a9d] text-white font-bold shadow-lg shadow-[#44B0B3]/25 transition">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>
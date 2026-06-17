@props([
    'name' => 'confirm-action',
    'maxWidth' => 'md',
])

<x-modal :name="$name" :maxWidth="$maxWidth" focusable>
    <div x-data="{
        action: '',
        method: 'DELETE',
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmLabel: 'Delete',
        confirmClass: 'bg-red-600 hover:bg-red-700',
        loading: false,
        async confirm() {
            this.loading = true;
            let form = document.createElement('form');
            form.action = this.action;
            form.method = 'POST';
            let csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            if (this.method !== 'POST') {
                let method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = this.method;
                form.appendChild(method);
            }
            document.body.appendChild(form);
            form.submit();
        }
    }" x-init="$watch('action', v => { if (v) { show = true; loading = false; } })"
    @set-confirmation.window="
        action = $event.detail.action;
        method = $event.detail.method || 'DELETE';
        title = $event.detail.title || 'Confirm Action';
        message = $event.detail.message || 'Are you sure you want to proceed?';
        confirmLabel = $event.detail.confirmLabel || 'Delete';
        confirmClass = $event.detail.confirmClass || 'bg-red-600 hover:bg-red-700';
        loading = false;
        $dispatch('open-modal', '{{ $name }}');
    ">
        <div class="p-6">
            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="title"></h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1" x-text="message"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button @click="$dispatch('close-modal', '{{ $name }}')" type="button"
                    class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-200 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-400 transition shadow-sm">
                    Cancel
                </button>
                <button @click="confirm()" type="button" :disabled="loading"
                    class="inline-flex items-center px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 disabled:opacity-60 disabled:cursor-not-allowed"
                    :class="confirmClass">
                    <svg x-show="loading" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    <span x-text="confirmLabel"></span>
                </button>
            </div>
        </div>
    </div>
</x-modal>

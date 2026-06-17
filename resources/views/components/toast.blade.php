<div
    x-data="{
        toasts: [],
        add(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message });
            setTimeout(() => { this.remove(id); }, 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-init="
        window.addEventListener('notify', event => {
            add(event.detail.type || 'success', event.detail.message);
        });
        @if(session('success'))
            add('success', '{{ session('success') }}');
        @endif
        @if(session('error'))
            add('error', '{{ session('error') }}');
        @endif
    "
    class="fixed top-5 right-5 z-[100] flex flex-col gap-3 w-80"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show !== false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="rounded-xl shadow-lg border p-4 text-sm flex items-start gap-3 cursor-pointer"
            :class="{
                'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-200': toast.type === 'success',
                'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700 dark:text-red-200': toast.type === 'error',
                'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-700 dark:text-blue-200': toast.type === 'info',
                'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/30 dark:border-amber-700 dark:text-amber-200': toast.type === 'warning',
            }"
            @click="remove(toast.id)"
        >
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg class="w-5 h-5 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </template>
            <span class="flex-1" x-text="toast.message"></span>
            <button @click.stop="remove(toast.id)" class="shrink-0 opacity-60 hover:opacity-100 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>

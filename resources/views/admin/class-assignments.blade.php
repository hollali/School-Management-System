<x-app-layout>
    @section('title', 'Class Assignments')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Class Assignments</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Assign multiple students to a class at once</p>
        </div>
    </x-slot>

    <script>
        window.__classAssignData = {
            classes: @json($classes),
            students: @json($students),
        };
    </script>
    <div class="py-6" x-data="{
        selectedClassId: '',
        classes: window.__classAssignData.classes,
        students: window.__classAssignData.students,
        get availableStudents() {
            if (!this.selectedClassId) return [];
            const classStudentIds = (this.classes.find(c => c.id == this.selectedClassId)?.students || []).map(s => s.id);
            return this.students.filter(s => !classStudentIds.includes(s.id));
        },
        selectAll: false,
        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            this.$el.querySelectorAll('input[name=&quot;student_ids[]&quot;]').forEach(cb => cb.checked = this.selectAll);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.class-assignments.store') }}" method="POST">
                @csrf

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                        <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Select Class</label>
                        <select name="class_id" id="class_id" x-model="selectedClassId" required
                            class="block w-full lg:w-96 rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            <option value="">Choose a class...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }} {{ $class->section ? '• ' . $class->section : '' }}
                                    ({{ $class->students->count() }}{{ $class->capacity ? '/' . $class->capacity : '' }} enrolled)
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="p-6">
                        <template x-if="!selectedClassId">
                            <div class="text-center py-12">
                                <i class="fa-solid fa-arrow-up text-4xl text-gray-300 dark:text-slate-600 mb-3"></i>
                                <p class="text-sm text-gray-400 dark:text-slate-500">Select a class above to see available students</p>
                            </div>
                        </template>

                        <template x-if="selectedClassId && availableStudents.length === 0">
                            <div class="text-center py-12">
                                <i class="fa-solid fa-check-circle text-4xl text-emerald-300 mb-3"></i>
                                <p class="text-sm text-gray-400 dark:text-slate-500">All students are already assigned to this class.</p>
                            </div>
                        </template>

                        <template x-if="selectedClassId && availableStudents.length > 0">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <p class="text-sm text-gray-600 dark:text-slate-400">
                                        <span x-text="availableStudents.length"></span> student(s) available to assign
                                    </p>
                                    <button @click="toggleSelectAll" type="button"
                                        class="text-xs text-sky-600 hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300 font-semibold">
                                        <span x-text="selectAll ? 'Deselect All' : 'Select All'"></span>
                                    </button>
                                </div>
                                <div class="space-y-1 max-h-96 overflow-y-auto">
                                    <template x-for="student in availableStudents" :key="student.id">
                                        <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/50 cursor-pointer transition">
                                            <input type="checkbox" name="student_ids[]" :value="student.id"
                                                class="rounded border-gray-300 dark:border-slate-600 text-sky-600 focus:ring-sky-500">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <img :src="student.user?.profile_photo_url || ''" alt="" class="w-8 h-8 rounded-full shrink-0">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-slate-200 truncate" x-text="student.user?.name || 'Unknown'"></p>
                                                    <p class="text-xs text-gray-400 dark:text-slate-500" x-text="student.admission_number || 'N/A'"></p>
                                                </div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-4" x-show="selectedClassId && availableStudents.length > 0">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-user-plus"></i>
                        Assign Selected Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
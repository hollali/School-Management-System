<x-app-layout>
    @section('title', 'User Management')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">User Management</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Create and manage all user accounts</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="User Management" :data="$users" searchable="true" searchPlaceholder="Search by name or email..." searchValue="{{ request('search') }}" searchRoute="{{ route('admin.users.index') }}">
            <x-slot name="actions">
                @can('manage-users')
                    <button @click="$dispatch('open-modal', 'create-user')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                        Create User
                    </button>
                @endcan
            </x-slot>
            <x-slot name="filters">
                <select name="role" @change="const p=new URLSearchParams(location.search);p.set('role',$event.target.value);p.delete('page');window.location='{{ route('admin.users.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="Admin" @selected(request('role') === 'Admin')>Admin</option>
                    <option value="Teacher" @selected(request('role') === 'Teacher')>Teacher</option>
                    <option value="Student" @selected(request('role') === 'Student')>Student</option>
                    <option value="Parent" @selected(request('role') === 'Parent')>Parent</option>
                </select>
            </x-slot>
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Created At</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->profile_photo_url }}" alt="" class="w-8 h-8 rounded-full">
                                <span class="text-sm font-semibold text-gray-900 dark:text-slate-200">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-400">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->role === 'Admin') bg-purple-100 text-purple-700
                                @elseif($user->role === 'Teacher') bg-sky-100 text-sky-700
                                @elseif($user->role === 'Student') bg-emerald-100 text-emerald-700
                                @else bg-amber-100 text-amber-700
                                @endif">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 dark:text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            @can('manage-users')
                                <button @click="
                                    $dispatch('edit-user', {
                                        id: '{{ $user->id }}',
                                        name: '{{ $user->name }}',
                                        email: '{{ $user->email }}',
                                        role: '{{ $user->role }}'
                                    });
                                    $dispatch('open-modal', 'edit-user');
                                " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-user" maxWidth="lg" focusable>
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-6">Create User</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="User's full name"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="user@example.com"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Role</label>
                    <select name="role" required
                            class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="">Select role...</option>
                        <option value="Admin" @selected(old('role') === 'Admin')>Admin</option>
                        <option value="Teacher" @selected(old('role') === 'Teacher')>Teacher</option>
                        <option value="Student" @selected(old('role') === 'Student')>Student</option>
                        <option value="Parent" @selected(old('role') === 'Parent')>Parent</option>
                    </select>
                    @error('role') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="Min. 8 characters"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                    @error('password') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button @click="$dispatch('close-modal', 'create-user')" type="button" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-user-plus mr-1.5"></i>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-user" maxWidth="lg" focusable>
        <div class="p-6" x-data="editUserData()" @edit-user.window="load($event.detail)">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-6">Edit User</h2>
            <form method="POST" :action="`/admin/users/${form.id}`" class="space-y-5">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" x-model="form.name" required placeholder="User's full name"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" x-model="form.email" required placeholder="user@example.com"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Role</label>
                    <select name="role" x-model="form.role" required
                            class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                        <option value="Admin">Admin</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Student">Student</option>
                        <option value="Parent">Parent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">New Password <span class="text-gray-400 dark:text-slate-500 font-normal">(leave blank to keep current)</span></label>
                    <input type="password" name="password" placeholder="Leave blank to keep current"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat new password"
                           class="block w-full rounded-xl border-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button @click="$dispatch('close-modal', 'edit-user')" type="button" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-save mr-1.5"></i>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
    function editUserData() {
        return {
            form: { id: '', name: '', email: '', role: '', created_at: '' },
            load(data) {
                this.form = { ...data };
            }
        };
    }
    </script>
</x-app-layout>

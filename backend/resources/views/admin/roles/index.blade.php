@extends('layouts.main')

@section('title', 'Role Management - Admin')

@section('content')
<div class="flex min-h-screen bg-[var(--bgPrimary)]">
    <x-admin-sidebar />
    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold text-[var(--textPrimary)] mb-8">Role Management</h2>
        <div class="bg-white rounded-2xl shadow-sm border border-[var(--border)] overflow-hidden max-w-2xl">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--bgSecondary)] text-[var(--textSecondary)] text-xs font-bold border-b border-[var(--border)] uppercase">
                        <th class="p-4">ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Slug</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @foreach($roles as $role)
                    <tr>
                        <td class="p-4 text-sm text-[var(--textSecondary)]">#{{ $role->id }}</td>
                        <td class="p-4 font-bold text-[var(--textPrimary)] text-sm">{{ $role->name }}</td>
                        <td class="p-4 text-sm text-[var(--textSecondary)]">{{ $role->slug }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection

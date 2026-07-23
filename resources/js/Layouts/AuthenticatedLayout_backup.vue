<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const showingNavigationDropdown = ref(false);

const currentPath = computed(() => {
    return window.location.pathname;
});

const isActive = (path) => {
    return currentPath.value === path || currentPath.value.startsWith(path + '/');
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-blue-600 text-white">
                <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-6">
                        <h1 class="text-2xl font-bold">📚 Book Management</h1>
                        <a href="/books" :class="{ 'font-bold': isActive('/books') }" class="hover:text-blue-200">
                            Books
                        </a>
                        <a href="/books/create" :class="{ 'font-bold': isActive('/books/create') }" class="hover:text-blue-200">
                            Add Book
                        </a>
                        <a href="/dashboard" :class="{ 'font-bold': isActive('/dashboard') }" class="hover:text-blue-200">
                            Dashboard
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span>{{ page.props.auth.user.name }}</span>
                        <form method="POST" action="/logout">
                            <input type="hidden" name="_token" :value="page.props.csrf_token" />
                            <button type="submit" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-blue-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <main class="py-8">
                <div class="container mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

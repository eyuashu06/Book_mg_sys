<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

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
            <!-- Navigation -->
            <nav class="bg-blue-600 text-white shadow-lg">
                <div class="container mx-auto px-4 py-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-1">
                            <Link href="/" class="text-xl font-bold mr-4 hover:text-blue-200">
                                Book Management
                            </Link>
                            <a href="/books"
                               :class="{ 'bg-blue-700': isActive('/books') && !isActive('/books/create') && !isActive('/bookstore') }"
                               class="px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                                My Library
                            </a>
                            <a href="/bookstore"
                               :class="{ 'bg-blue-700': isActive('/bookstore') }"
                               class="px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                                Book Store
                            </a>
                            <a href="/books/create"
                               :class="{ 'bg-blue-700': isActive('/books/create') }"
                               class="px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                                Add Book
                            </a>
                            <a href="/dashboard"
                               :class="{ 'bg-blue-700': isActive('/dashboard') }"
                               class="px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                                Dashboard
                            </a>
                            <a href="/offline"
                               :class="{ 'bg-blue-700': isActive('/offline') }"
                               class="px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                                Offline
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span v-if="page.props.auth.user" class="text-sm">{{ page.props.auth.user.name }}</span>
                            <form v-if="page.props.auth.user" method="POST" action="/logout">
                                <input type="hidden" name="_token" :value="page.props.csrf_token" />
                                <button type="submit" class="bg-white text-blue-600 px-4 py-2 rounded text-sm hover:bg-blue-100 font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="py-8">
                <div class="container mx-auto px-4">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>



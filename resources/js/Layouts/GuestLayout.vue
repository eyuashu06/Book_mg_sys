<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-blue-600 p-4 text-white">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <h1 class="text-2xl font-bold">📚 Book Management</h1>
                    <Link href="/" class="hover:text-blue-200">Home</Link>
                </div>
                <div class="flex items-center space-x-4">
                    <Link v-if="!page.props.auth.user" href="/login"
                        class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-blue-100">
                        Login
                    </Link>
                    <Link v-if="!page.props.auth.user" href="/register"
                        class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-900">
                        Register
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                <slot />
            </div>
        </div>
    </div>
</template>

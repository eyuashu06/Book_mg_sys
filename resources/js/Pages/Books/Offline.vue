<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    books: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Offline Books" />

        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">Offline Books</h2>
                <Link :href="route('books.index')"
                      class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to Library
                </Link>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-blue-700">
                    These books have been downloaded to your device and are available for offline reading.
                </p>
            </div>

            <div v-if="books.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="book in books" :key="book.id"
                     class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                    <Link :href="`/books/${book.id}/read`" class="block">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <img
                                :src="book.cover_url"
                                :alt="book.title"
                                class="w-full h-full object-cover"
                                @error="e => e.target.src = '/images/default-cover.jpg'"
                            />
                        </div>
                    </Link>
                    <div class="p-4">
                        <Link :href="`/books/${book.id}/read`" class="hover:text-blue-600">
                            <h3 class="font-bold text-lg">{{ book.title }}</h3>
                        </Link>
                        <p class="text-gray-600 text-sm">by {{ book.author }}</p>
                        <p class="text-sm text-gray-500 mt-1">File: {{ book.formatted_file_size || 'Unknown size' }}</p>

                        <div class="flex space-x-2 mt-3">
                            <Link :href="`/books/${book.id}/read`"
                                  class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 text-center">
                                Read
                            </Link>
                            <a :href="`/books/${book.id}/download`"
                               class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 text-center">
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-gray-500 text-lg">No offline books available</p>
                <p class="text-gray-400 text-sm mt-2">
                    Download books from your library to read them offline.
                </p>
                <Link :href="route('books.index')"
                      class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Browse Books
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

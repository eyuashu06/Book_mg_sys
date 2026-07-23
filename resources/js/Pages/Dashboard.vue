<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-2xl font-bold mb-6">📊 Book Management Dashboard</h2>

                        <!-- Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                            <div class="bg-blue-100 p-6 rounded-lg">
                                <h3 class="text-xl font-semibold">Total Books</h3>
                                <p class="text-4xl font-bold text-blue-600">{{ stats?.total ?? 0 }}</p>
                            </div>
                            <div class="bg-yellow-100 p-6 rounded-lg">
                                <h3 class="text-xl font-semibold">Reading</h3>
                                <p class="text-4xl font-bold text-yellow-600">{{ stats?.reading ?? 0 }}</p>
                            </div>
                            <div class="bg-green-100 p-6 rounded-lg">
                                <h3 class="text-xl font-semibold">Completed</h3>
                                <p class="text-4xl font-bold text-green-600">{{ stats?.completed ?? 0 }}</p>
                            </div>
                            <div class="bg-purple-100 p-6 rounded-lg">
                                <h3 class="text-xl font-semibold">Reviews</h3>
                                <p class="text-4xl font-bold text-purple-600">{{ stats?.reviews ?? 0 }}</p>
                            </div>
                        </div>

                        <!-- Recent Books -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold mb-4">📚 Recent Books</h3>
                            <div v-if="recentBooks && recentBooks.length" class="space-y-3">
                                <div v-for="book in recentBooks" :key="book.id" 
                                     class="flex justify-between items-center border-b pb-2">
                                    <div>
                                        <span class="font-medium">{{ book.title }}</span>
                                        <span class="text-gray-600 text-sm">by {{ book.author }}</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ book.created_at_from_now }}</span>
                                </div>
                            </div>
                            <p v-else class="text-gray-500">No books added yet.</p>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex space-x-4">
                            <Link :href="route('books.create')" 
                                  class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                ➕ Add New Book
                            </Link>
                            <Link :href="route('books.index')" 
                                  class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                                📖 View All Books
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

// ✅ Define props with defaults to prevent undefined errors
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            reading: 0,
            completed: 0,
            offline: 0,
        })
    },
    recentBooks: {
        type: Array,
        default: () => []
    }
})
</script>
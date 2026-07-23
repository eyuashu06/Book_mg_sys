<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue'

const props = defineProps({
    books: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '', status: '' }) },
});

const page = usePage();
const search = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')

const searchBooks = () => {
    router.get('/books', {
        search: search.value,
        status: statusFilter.value,
    }, { preserveState: true })
}

const clearFilters = () => {
    search.value = ''
    statusFilter.value = ''
    router.get('/books', {}, { preserveState: true })
}

const statusColors = {
    unread: 'bg-gray-100 text-gray-600',
    reading: 'bg-blue-100 text-blue-600',
    completed: 'bg-green-100 text-green-600',
}

const readingStatusIcon = (status) => {
    const icons = { unread: '○', reading: '◉', completed: '✓' }
    return icons[status] || '○'
}

const hasActiveFilters = () => search.value || statusFilter.value
</script>

<template>
    <AuthenticatedLayout>
        <Head title="My Library" />

        <div class="mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">My Library</h2>
                <div v-if="page.props.auth.user" class="flex gap-3">
                    <Link href="/bookstore"
                          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Online Book Store
                    </Link>
                    <Link href="/books/create"
                          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Add New Book
                    </Link>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <form @submit.prevent="searchBooks" class="flex flex-wrap gap-3">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by title or author..."
                        class="flex-1 min-w-[200px] border border-gray-300 rounded px-4 py-2"
                    />
                    <select v-model="statusFilter"
                            class="border border-gray-300 rounded px-4 py-2">
                        <option value="">All Status</option>
                        <option value="unread">Unread</option>
                        <option value="reading">Reading</option>
                        <option value="completed">Completed</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Search
                    </button>
                    <button v-if="hasActiveFilters()" type="button" @click="clearFilters"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                        Clear
                    </button>
                </form>
            </div>

            <!-- Books Grid -->
            <div v-if="books.data?.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-gray-500 text-lg">No books found</p>
                <p v-if="hasActiveFilters()" class="text-gray-400 text-sm mt-2">Try adjusting your search or filters.</p>
                <p v-else class="text-gray-400 text-sm mt-2">Add your first book to get started!</p>
                <Link v-if="page.props.auth.user && !hasActiveFilters()"
                      href="/books/create"
                      class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Add New Book
                </Link>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="book in books.data" :key="book.id"
                     class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group">
                    <Link :href="`/books/${book.id}`" class="block">
                        <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
                            <img v-if="book.cover_url && !book.cover_url.includes('default-cover')"
                                 :src="book.cover_url"
                                 :alt="book.title"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 @error="e => e.target.src = '/images/default-cover.jpg'"
                            />
                            <div v-else class="text-gray-400 text-center p-4">
                                <div class="text-5xl mb-2">No Cover</div>
                            </div>
                        </div>
                    </Link>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <Link :href="`/books/${book.id}`" class="hover:text-blue-600 flex-1 min-w-0">
                                <h3 class="font-bold text-lg truncate">{{ book.title }}</h3>
                            </Link>
                            <span v-if="book.reading_status"
                                  :class="statusColors[book.reading_status]"
                                  class="ml-2 px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap">
                                {{ readingStatusIcon(book.reading_status) }} {{ book.reading_status }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mb-1">by {{ book.author }}</p>
                        <p class="text-gray-500 text-xs mb-1">ISBN: {{ book.isbn }} | {{ book.published_year }}</p>

                        <div v-if="book.reading_progress !== undefined && book.reading_progress !== null" class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ book.reading_progress }}%</span>
                            </div>
                            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600 rounded-full transition-all"
                                     :style="{ width: book.reading_progress + '%' }"></div>
                            </div>
                        </div>

                        <div v-if="page.props.auth.user" class="flex gap-2 mt-3">
                            <Link :href="`/books/${book.id}/read`"
                                  class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                Read
                            </Link>
                            <Link :href="`/books/${book.id}`"
                                  class="bg-gray-500 text-white px-3 py-1 rounded text-xs hover:bg-gray-600">
                                Details
                            </Link>
                            <Link :href="`/books/${book.id}/edit`"
                                  class="bg-yellow-500 text-white px-3 py-1 rounded text-xs hover:bg-yellow-600">
                                Edit
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="books.links" class="mt-6 flex justify-center">
                <div v-for="link in books.links" :key="link.url || link.label" class="mx-1">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        :class="{
                            'px-3 py-1.5 rounded border text-sm': true,
                            'bg-blue-600 text-white border-blue-600': link.active,
                            'border-gray-300 hover:bg-gray-100 text-gray-700': !link.active,
                        }"
                        v-html="link.label"
                    />
                    <span v-else class="px-3 py-1.5 text-gray-400 border border-gray-200 rounded text-sm">
                        {{ link.label }}
                    </span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

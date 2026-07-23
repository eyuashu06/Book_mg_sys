<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    results: { type: Array, default: () => [] },
    query: { type: String, default: '' },
})

const searchQuery = ref(props.query)
const importing = ref(null)

const search = () => {
    router.get('/bookstore', { q: searchQuery.value }, { preserveState: true })
}

const importBook = (book) => {
    if (!confirm(`Import "${book.title}" to your library?`)) return
    importing.value = book.slug
    router.post('/bookstore/import', {
        title: book.title,
        author: book.author,
        slug: book.slug,
        description: book.description || '',
        cover_image: book.cover_image || '',
        pdf_url: book.pdf_url || '',
        reader_url: book.reader_url || '',
    }, {
        onFinish: () => { importing.value = null },
    })
}

const coverSrc = (url) => {
    if (!url) return null
    if (url.startsWith('http')) return url
    return null
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="JunkyBooks - Online Book Store" />

        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold">JunkyBooks Store</h2>
                    <p class="text-gray-500 text-sm">Search 100,000+ free eBooks</p>
                </div>
                <Link href="/books" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    My Library
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form @submit.prevent="search" class="flex gap-3">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by title, author, or keyword..."
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-lg"
                    />
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-medium">
                        Search
                    </button>
                </form>

                <div v-if="query" class="mt-3 text-sm text-gray-500">
                    Showing results for: <strong>{{ query }}</strong>
                </div>
            </div>

            <div v-if="results.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="book in results" :key="book.slug"
                     class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition flex flex-col">
                    <div class="h-56 bg-gray-100 flex items-center justify-center overflow-hidden">
                        <img v-if="book.cover_image"
                             :src="coverSrc(book.cover_image)"
                             :alt="book.title"
                             class="w-full h-full object-contain"
                             @error="e => e.target.style.display = 'none'"
                        />
                        <div v-else class="text-gray-400 text-center p-4">
                            <div class="text-5xl mb-2">No Cover</div>
                        </div>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-bold text-lg mb-1 line-clamp-2">{{ book.title }}</h3>
                        <p class="text-gray-600 text-sm mb-2">by {{ book.author }}</p>
                        <div class="mt-auto flex flex-col gap-2">
                            <button @click="importBook(book)"
                                    :disabled="importing === book.slug"
                                    class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 disabled:opacity-50 font-medium">
                                <span v-if="importing === book.slug">Importing...</span>
                                <span v-else>Add to My Library</span>
                            </button>
                            <div class="flex gap-2">
                                <Link :href="`/bookstore/${book.slug}`"
                                      class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 text-center">
                                    Details
                                </Link>
                                <a v-if="book.preview_link" :href="book.preview_link" target="_blank"
                                   class="flex-1 bg-indigo-600 text-white px-3 py-2 rounded text-sm hover:bg-indigo-700 text-center">
                                    View on Site
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else-if="query" class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-gray-500 text-lg">No books found for "{{ query }}"</p>
                <p class="text-gray-400 text-sm mt-2">Try different keywords or check the spelling.</p>
            </div>

            <div v-else class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-2xl mb-2">Search 100,000+ Free eBooks</p>
                <p class="text-gray-500">Powered by JunkyBooks.com — Type a title or author above to get started.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

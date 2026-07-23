<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    book: { type: Object, required: true },
})

const importing = ref(false)

const importBook = () => {
    if (!confirm(`Import "${props.book.title}" to your library?`)) return
    importing.value = true
    router.post('/bookstore/import', {
        title: props.book.title,
        author: props.book.author,
        slug: props.book.slug,
        description: props.book.description,
        cover_image: props.book.cover_image,
        pdf_url: props.book.pdf_url,
        reader_url: props.book.reader_url,
    }, {
        onFinish: () => { importing.value = false },
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="book.title" />

        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <Link href="/bookstore" class="text-blue-600 hover:underline">&larr; Back to search</Link>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-2/5 bg-gray-100 flex items-center justify-center p-6">
                        <img v-if="book.cover_image"
                             :src="book.cover_image"
                             :alt="book.title"
                             class="w-full max-h-80 object-contain shadow-lg"
                             @error="e => e.target.style.display = 'none'"
                        />
                        <div v-else class="text-gray-400 text-center p-8">
                            <div class="text-6xl mb-2">No Cover</div>
                        </div>
                    </div>
                    <div class="md:w-3/5 p-6 flex flex-col">
                        <h1 class="text-2xl font-bold mb-2">{{ book.title }}</h1>
                        <p class="text-lg text-gray-600 mb-3">by {{ book.author }}</p>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <span v-if="book.category" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm">
                                {{ book.category }}
                            </span>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                JunkyBooks
                            </span>
                        </div>

                        <p v-if="book.description" class="text-gray-700 mb-6 leading-relaxed flex-1">
                            {{ book.description.substring(0, 500) }}...
                        </p>
                        <p v-else class="text-gray-400 italic mb-6">No description available.</p>

                        <div class="flex flex-wrap gap-3 mt-auto">
                            <button @click="importBook" :disabled="importing"
                                    class="bg-green-600 text-white px-6 py-2.5 rounded hover:bg-green-700 disabled:opacity-50 font-medium">
                                {{ importing ? 'Importing...' : 'Add to My Library' }}
                            </button>
                            <a v-if="book.reader_url" :href="book.reader_url" target="_blank"
                               class="bg-blue-600 text-white px-6 py-2.5 rounded hover:bg-blue-700 font-medium">
                                Read Online
                            </a>
                            <a v-if="book.pdf_url" :href="book.pdf_url" target="_blank"
                               class="bg-indigo-600 text-white px-6 py-2.5 rounded hover:bg-indigo-700 font-medium">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

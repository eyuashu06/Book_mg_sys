<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
    book: Object
})

const progress = ref(props.book.reading_progress || 0)
const isFullscreen = ref(false)
const readerRef = ref(null)

const statusLabel = computed(() => {
    const statuses = {
        unread: 'Not started',
        reading: 'Reading',
        completed: 'Completed'
    }
    return statuses[props.book.reading_status] || statuses.unread
})

const statusColor = computed(() => {
    const colors = {
        unread: 'bg-gray-100 text-gray-700',
        reading: 'bg-blue-100 text-blue-700',
        completed: 'bg-green-100 text-green-700'
    }
    return colors[props.book.reading_status] || colors.unread
})

const updateProgress = () => {
    const status = progress.value >= 100 ? 'completed' :
                   progress.value > 0 ? 'reading' : 'unread'

    router.post(`/books/${props.book.id}/progress`, {
        reading_progress: progress.value,
        reading_status: status
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
        readerRef.value?.requestFullscreen()
        isFullscreen.value = true
    } else {
        document.exitFullscreen()
        isFullscreen.value = false
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Reading: ${book.title}`" />

        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="p-4 border-b flex flex-wrap justify-between items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-xl font-bold truncate">{{ book.title }}</h2>
                        <p class="text-gray-600 text-sm">by {{ book.author }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span :class="statusColor" class="px-3 py-1 rounded-full text-sm font-medium">
                            {{ statusLabel }}
                        </span>
                        <button @click="toggleFullscreen"
                                class="bg-gray-600 text-white px-3 py-1.5 rounded text-sm hover:bg-gray-700">
                            Fullscreen
                        </button>
                        <a :href="`/books/${book.id}/download`"
                           class="bg-green-600 text-white px-3 py-1.5 rounded text-sm hover:bg-green-700">
                            Download
                        </a>
                        <Link :href="`/books/${book.id}`"
                              class="bg-gray-500 text-white px-3 py-1.5 rounded text-sm hover:bg-gray-600">
                            Back
                        </Link>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="p-4 bg-gray-50 border-b">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium whitespace-nowrap">Progress:</span>
                        <div class="flex-1 flex items-center gap-3">
                            <input
                                type="range"
                                v-model.number="progress"
                                min="0"
                                max="100"
                                step="1"
                                class="flex-1 h-2 accent-blue-600"
                                @input="updateProgress"
                            />
                            <span class="text-sm font-bold min-w-[3rem] text-right">{{ progress }}%</span>
                        </div>
                        <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all duration-300"
                                 :style="{ width: progress + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- Book Reader -->
                <div ref="readerRef" class="bg-gray-800" :class="isFullscreen ? 'min-h-screen' : 'min-h-[600px]'">
                    <div v-if="book.file_type === 'application/pdf'" class="w-full" :class="isFullscreen ? 'h-screen' : 'h-[600px]'">
                        <iframe
                            :src="book.file_url + '#toolbar=1'"
                            class="w-full h-full"
                            frameborder="0"
                            allowfullscreen
                        ></iframe>
                    </div>
                    <div v-else-if="book.file_type === 'application/epub+zip' || book.book_file?.endsWith('.epub')">
                        <div class="text-center py-16 text-white">
                            <p class="text-xl mb-4">EPUB format detected</p>
                            <p class="text-gray-400 mb-6">Download and open with your preferred EPUB reader app.</p>
                            <a :href="`/books/${book.id}/download`"
                               class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 text-lg">
                                Download EPUB
                            </a>
                        </div>
                    </div>
                    <div v-else-if="book.metadata?.reader_url">
                        <div class="text-center py-16 text-white">
                            <p class="text-xl mb-4">Read on JunkyBooks</p>
                            <p class="text-gray-400 mb-6">This book was imported from JunkyBooks and can be read online.</p>
                            <a :href="book.metadata.reader_url" target="_blank"
                               class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 text-lg">
                                Open Online Reader
                            </a>
                        </div>
                    </div>
                    <div v-else-if="book.preview_link">
                        <div class="text-center py-16 text-white">
                            <p class="text-xl mb-4">Online Preview Available</p>
                            <p class="text-gray-400 mb-6">This book has a preview link.</p>
                            <a :href="book.preview_link" target="_blank"
                               class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 text-lg">
                                Open Preview
                            </a>
                        </div>
                    </div>
                    <div v-else>
                        <div class="text-center py-16 text-white">
                            <p class="text-xl mb-4">No file available for online reading</p>
                            <p class="text-gray-400 mb-2">This book doesn't have an uploaded file.</p>
                            <Link :href="`/books/${book.id}/edit`"
                                  class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 text-lg mt-4">
                                Upload a File
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

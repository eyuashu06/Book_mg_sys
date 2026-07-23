<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue'

const props = defineProps({
    book: { type: Object, required: true },
});

const page = usePage();
const isOwner = computed(() => page.props.auth.user?.id === props.book.user_id);

const userReview = computed(() => {
    if (!page.props.auth.user) return null
    return props.book.reviews?.find(r => r.user_id === page.props.auth.user.id)
})

const reviewForm = useForm({
    rating: userReview.value?.rating || 5,
    review: userReview.value?.review || '',
})

const avgRating = computed(() => {
    if (!props.book.reviews?.length) return 0
    const sum = props.book.reviews.reduce((a, r) => a + r.rating, 0)
    return (sum / props.book.reviews.length).toFixed(1)
})

const submitReview = () => {
    if (userReview.value) {
        reviewForm.put(`/books/${props.book.id}/reviews/${userReview.value.id}`, {
            preserveScroll: true,
        })
    } else {
        reviewForm.post(`/books/${props.book.id}/reviews`, {
            preserveScroll: true,
        })
    }
}

const deleteReview = () => {
    if (!userReview.value || !confirm('Delete your review?')) return
    reviewForm.delete(`/books/${props.book.id}/reviews/${userReview.value.id}`, {
        preserveScroll: true,
    })
}

const readingStatusClass = (status) => {
    const map = { unread: 'bg-gray-100 text-gray-700', reading: 'bg-blue-100 text-blue-700', completed: 'bg-green-100 text-green-700' }
    return map[status] || map.unread
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="book.title" />

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="md:flex">
                    <div class="md:w-1/3 bg-gray-100 flex items-center justify-center p-4">
                        <img v-if="book.cover_url && !book.cover_url.includes('default-cover')"
                             :src="book.cover_url"
                             :alt="book.title"
                             class="w-full max-h-80 object-contain"
                             @error="e => e.target.src = '/images/default-cover.jpg'"
                        />
                        <div v-else class="text-gray-400 text-center p-8">
                            <div class="text-6xl mb-2">No Cover</div>
                        </div>
                    </div>
                    <div class="md:w-2/3 p-6">
                        <h1 class="text-3xl font-bold mb-2">{{ book.title }}</h1>
                        <p class="text-lg text-gray-600 mb-4">by {{ book.author }}</p>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <span v-if="book.reading_status"
                                  :class="readingStatusClass(book.reading_status)"
                                  class="px-3 py-1 rounded-full text-sm font-medium">
                                {{ book.reading_status }}
                            </span>
                            <span v-if="book.category" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm">
                                {{ book.category.name }}
                            </span>
                            <span v-if="book.reviews?.length" class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">
                                Rating: {{ avgRating }}/5 ({{ book.reviews.length }} review{{ book.reviews.length !== 1 ? 's' : '' }})
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div><strong>ISBN:</strong> {{ book.isbn }}</div>
                            <div><strong>Published:</strong> {{ book.published_year }}</div>
                            <div v-if="book.file_size"><strong>File Size:</strong> {{ book.formatted_file_size }}</div>
                            <div v-if="book.reading_progress !== undefined && book.reading_progress !== null"><strong>Progress:</strong> {{ book.reading_progress }}%</div>
                        </div>

                        <p v-if="book.description" class="text-gray-700 mb-6">
                            {{ book.description }}
                        </p>

                        <div v-if="isOwner" class="flex flex-wrap gap-3">
                            <Link v-if="book.file_url" :href="`/books/${book.id}/read`"
                                  class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 font-medium">
                                Read Online
                            </Link>
                            <a v-if="book.metadata?.reader_url" :href="book.metadata.reader_url" target="_blank"
                               class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 font-medium">
                                Read on JunkyBooks
                            </a>
                            <a v-if="book.file_url" :href="`/books/${book.id}/download`"
                               class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 font-medium">
                                Download
                            </a>
                            <a v-if="book.metadata?.pdf_url" :href="book.metadata.pdf_url" target="_blank"
                               class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 font-medium">
                                Download PDF
                            </a>
                            <a v-if="book.preview_link" :href="book.preview_link" target="_blank"
                               class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700 font-medium">
                                Preview
                            </a>
                            <Link :href="`/books/${book.id}/edit`"
                                  class="bg-yellow-500 text-white px-5 py-2 rounded hover:bg-yellow-600 font-medium">
                                Edit
                            </Link>
                        </div>

                        <div v-else class="flex gap-3">
                            <Link href="/login" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 font-medium">
                                Login to Read & Review
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Reviews & Ratings</h3>

                <div v-if="page.props.auth.user" class="border-b pb-6 mb-6">
                    <h4 class="font-semibold mb-3">{{ userReview ? 'Edit Your Review' : 'Write a Review' }}</h4>
                    <form @submit.prevent="submitReview">
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Rating</label>
                            <div class="flex gap-1">
                                <button v-for="star in 5" :key="star" type="button"
                                        @click="reviewForm.rating = star"
                                        class="text-2xl focus:outline-none"
                                        :class="star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300'">
                                    &#9733;
                                </button>
                                <span class="ml-2 text-sm text-gray-500">{{ reviewForm.rating }}/5</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Your Review</label>
                            <textarea v-model="reviewForm.review"
                                      rows="3"
                                      class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                      placeholder="Share your thoughts about this book..."></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" :disabled="reviewForm.processing"
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                                {{ userReview ? 'Update Review' : 'Submit Review' }}
                            </button>
                            <button v-if="userReview" type="button" @click="deleteReview"
                                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>

                <div v-if="book.reviews?.length" class="space-y-4">
                    <div v-for="review in book.reviews" :key="review.id"
                         class="border-b pb-4 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-medium">{{ review.user?.name }}</span>
                                <div class="flex gap-0.5 mt-1">
                                    <span v-for="s in 5" :key="s"
                                          :class="s <= review.rating ? 'text-yellow-400' : 'text-gray-300'">&#9733;</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ review.created_at }}</span>
                        </div>
                        <p v-if="review.review" class="text-gray-700 mt-2">{{ review.review }}</p>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-4">No reviews yet.</p>
            </div>

            <Link href="/books" class="text-blue-600 hover:underline">&larr; Back to Books</Link>
        </div>
    </AuthenticatedLayout>
</template>

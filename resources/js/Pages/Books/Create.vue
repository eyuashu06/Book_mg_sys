<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    title: '',
    author: '',
    isbn: '',
    published_year: new Date().getFullYear(),
    description: '',
    cover_image: null,
    book_file: null,
    reading_status: 'unread',
});

const submit = () => {
    form.post(route('books.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Add New Book" />

        <div class="max-w-2xl mx-auto">
            <h2 class="text-3xl font-bold mb-6">Add New Book</h2>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-blue-700">
                    You can also <Link href="/bookstore" class="underline font-medium">search the online bookstore</Link> to import books directly.
                </p>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-lg shadow-md p-6" enctype="multipart/form-data">
                <div class="mb-4">
                    <InputLabel for="title" value="Title" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div class="mb-4">
                    <InputLabel for="author" value="Author" />
                    <TextInput id="author" type="text" class="mt-1 block w-full" v-model="form.author" required />
                    <InputError class="mt-2" :message="form.errors.author" />
                </div>

                <div class="mb-4">
                    <InputLabel for="isbn" value="ISBN (optional)" />
                    <TextInput id="isbn" type="text" class="mt-1 block w-full" v-model="form.isbn" />
                    <InputError class="mt-2" :message="form.errors.isbn" />
                </div>

                <div class="mb-4">
                    <InputLabel for="published_year" value="Published Year" />
                    <TextInput id="published_year" type="number" class="mt-1 block w-full"
                               v-model="form.published_year" required :min="1900" :max="new Date().getFullYear()" />
                    <InputError class="mt-2" :message="form.errors.published_year" />
                </div>

                <div class="mb-4">
                    <InputLabel for="description" value="Description" />
                    <textarea id="description"
                              class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              rows="4" v-model="form.description"></textarea>
                    <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div class="mb-4">
                    <InputLabel for="cover_image" value="Cover Image" />
                    <input id="cover_image" type="file" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                  file:rounded file:border-0 file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           @input="form.cover_image = $event.target.files[0]" />
                    <InputError class="mt-2" :message="form.errors.cover_image" />
                </div>

                <div class="mb-4">
                    <InputLabel for="book_file" value="Book File (PDF, EPUB, DOCX)" />
                    <input id="book_file" type="file" accept=".pdf,.epub,.docx"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                  file:rounded file:border-0 file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           @input="form.book_file = $event.target.files[0]" />
                    <InputError class="mt-2" :message="form.errors.book_file" />
                </div>

                <div class="mb-6">
                    <InputLabel for="reading_status" value="Reading Status" />
                    <select id="reading_status" v-model="form.reading_status"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        <option value="unread">Unread</option>
                        <option value="reading">Currently Reading</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div class="flex space-x-4">
                    <PrimaryButton :disabled="form.processing">
                        Create Book
                    </PrimaryButton>
                    <Link href="/books" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 inline-block">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

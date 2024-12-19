<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';

const password = ref(''); // Bind the password input

const enter = async () => {
    if (!password.value) {
        alert('Please enter a password.');
        return;
    }
    try {
        const response = await axios.post('/start', { password: password.value });
        // Redirect to the Trivia page using Inertia
        Inertia.visit(route('trivia'));
    } catch (error) {
        alert(error.response?.data?.message || 'An error occurred. Please try again.'); // Handle error message
    }
};


</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">

        <div
            class="relative flex min-h-screen
            flex-col
            items-center
            justify-center
            selection:bg-[#FF2D20]
            selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 ">
                <main class="mt-6">
                    <div class="grid gap-6 lg:grid-cols-1 lg:gap-8">
                        <TextInput v-model="password" type="password" placeholder="Enter Password"></TextInput>
                        <PrimaryButton @click="enter();">Enter</PrimaryButton>
                    </div>
                </main>
                 <header
                    class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3"
                >
                    <div class="flex lg:col-start-2 lg:justify-center">

                    </div>
                    <nav  class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>


            </div>
        </div>
    </div>
</template>

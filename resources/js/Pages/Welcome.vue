<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const password = ref(''); // Bind the password input

const showSection1 = ref(true);
const showSection2 = ref(false);
const showSection3 = ref(false);
const showSection4 = ref(false);
const showSection5 = ref(false);

const nextSection = (currentSection) => {
    if (currentSection === 1) {
        showSection2.value = true;
    } else if (currentSection === 2) {
        showSection3.value = true;
    } else if (currentSection === 3) {
        showSection4.value = true;
    }
    else if (currentSection === 4) {
        showSection5.value = true;
    }
};

const enter = async () => {
    if (!password.value) {
        alert('Please enter a password.');
        return;
    }
    try {
        const response = await axios.post('/start', { password: password.value });
        // Redirect to the Trivia page using Inertia
        Inertia.visit(route('holding'));
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
                    <ApplicationLogo class="mx-auto"></ApplicationLogo>

                    <p class="bg-green-700 p-3 rounded-xl text-white my-2">To use your gift you must solve the trivia below</p>

                    <div class="grid gap-6 lg:grid-cols-1 lg:gap-8">
                        <TextInput v-model="password" type="password" placeholder="Enter Password"></TextInput>
                        <PrimaryButton @click="enter();">Enter</PrimaryButton>
                    </div>


                    <!-- Trivia Sections -->
                <div class="mt-4" v-if="showSection1">
                    <p class="bg-green-700 p-3 rounded-xl text-white my-2">"Taylor Swift has released numerous albums. How many of those albums were released in the year 2008?"</p>
                    <PrimaryButton @click="nextSection(1)">Next</PrimaryButton>
                </div>
                <div v-if="showSection2">
                    <p class="bg-green-700 p-3 rounded-xl text-white my-2">(2 × 3) - 4 + (5 / 5)</p>
                    <PrimaryButton @click="nextSection(2)">Next</PrimaryButton>
                </div>

                <div v-if="showSection3">
                    <p class="bg-green-700 p-3 rounded-xl text-white my-2">I am what divides and unites, Two parts together in one sight. I’m the pair in a duo’s play, The number of feet to walk a way. What am I?</p>
                    <PrimaryButton @click="nextSection(3)">Next</PrimaryButton>

                </div>

                <div v-if="showSection4">
                    <p class="bg-green-700 p-3 rounded-xl text-white my-2">We started with none, but now we can have no more. You didn’t want one, but now you want more. How many do we have?</p>
                    <PrimaryButton @click="nextSection(4)">Next</PrimaryButton>
                </div>
                <div v-if="showSection5">
                    <p class="bg-green-700 p-3 rounded-xl text-white my-2">
                        You have everything you need now <br>Hint: Change the order to make sense </p>
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

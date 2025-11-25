<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const password = ref(''); // Bind the password input

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
    <div class="bg-gradient-to-br from-red-900 via-green-900 to-red-900 text-white min-h-screen">
        <div class="relative flex min-h-screen flex-col items-center justify-center selection:bg-white selection:text-red-900 px-6">
            <div class="relative w-full max-w-lg">
                <!-- Animated Background Elements -->
                <div class="absolute inset-0 -z-10">
                    <div class="absolute top-10 left-10 w-32 h-32 bg-red-500/20 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-10 right-10 w-40 h-40 bg-green-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
                </div>

                <main class="space-y-8">
                    <!-- Logo & Header -->
                    <div class="text-center space-y-6">
                        <div class="transform hover:scale-105 transition-transform duration-300">
                            <ApplicationLogo class="mx-auto w-32 h-32"></ApplicationLogo>
                        </div>

                        <div>
                            <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-red-400 via-white to-green-400 bg-clip-text text-transparent animate-pulse">
                                🎄 Welcome! 🎁
                            </h1>
                            <!-- <p class="text-xl text-gray-200 font-medium">
                                Your Christmas Gift Awaits
                            </p> -->
                        </div>
                    </div>

                    <!-- Password Card -->
                    <div class="bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg rounded-3xl border border-white/20 p-8 shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="space-y-6">
                            <div class="text-center">
                                <div class="inline-block bg-gradient-to-r from-red-500 to-green-500 p-4 rounded-2xl mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-white mb-2">Enter Password</h2>
                                <!-- <p class="text-gray-300">Unlock your special gift</p> -->
                            </div>

                            <div class="space-y-4">
                                <TextInput
                                    v-model="password"
                                    type="password"
                                    placeholder="Enter your password"
                                    class="w-full text-lg py-3 bg-white/10 border-white/30 text-white placeholder-gray-400 focus:border-white focus:ring-white"
                                    @keyup.enter="enter"
                                ></TextInput>

                                <button
                                    @click="enter"
                                    class="w-full bg-gradient-to-r from-red-600 to-green-600 hover:from-red-700 hover:to-green-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-3 text-lg"
                                >
                                    <span>Unlock</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="text-center space-y-4 pt-4">
                        <div class="flex justify-center space-x-4 text-4xl animate-bounce">
                            <span>🎅</span>
                            <span>⭐</span>
                            <span>🎄</span>
                            <span>🎁</span>
                            <span>❄️</span>
                        </div>
                        <p class="text-gray-400 text-sm">
                            <!-- A special gift just for you this holiday season -->
                        </p>
                    </div>
                </main>

                <!-- Auth Links -->
                <footer class="mt-12 text-center">
                    <nav class="flex justify-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="text-white/70 hover:text-white transition duration-300 font-medium"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-white/70 hover:text-white transition duration-300 font-medium"
                            >
                                Log in
                            </Link>

                            <Link
                                :href="route('register')"
                                class="text-white/70 hover:text-white transition duration-300 font-medium"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </footer>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.delay-1000 {
    animation-delay: 1s;
}
</style>

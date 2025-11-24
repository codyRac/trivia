<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    credits: Object,
    trivia: Object,
    services: Object,
    triviaCompleted: Boolean,
    emojiCompleted: Boolean,
    canAnswer: Boolean // Assume trivia has properties like question and answers
});
const credits = ref(props.credits); // Make credits reactive
const canAnswer = ref(props.canAnswer); // Make credits reactive


const selectedAnswer = ref(null);
const enter = async () => {
    if (!selectedAnswer.value) {
        toast("Please select an answer.", {
            autoClose: 1000,
        });
        return;
    }

    try {
        const response = await axios.post('/trivia_answer',{
                trivia_id: props.trivia.id, // Send only trivia ID
                answer: selectedAnswer.value

            });
            canAnswer.value = false

            // Update the credits object dynamically
        if (response.data.credits) {
            credits.value.credits = response.data.credits;
            credits.value.earned = response.data.earned;
        }
        toast(response.data.message || "Answer submitted successfully.", {
            autoClose: 2000,
        });

    } catch (error) {
        toast(error.response?.data?.message || "An error occurred.", {
            autoClose: 2000,
        });
    }
    // Submit selected answer
    console.log('Selected Answer:', selectedAnswer.value);
    // Implement your logic for handling the selected answer
};
</script>

<template>
    <Head title="Dashboard" />
    <div class="bg-gradient-to-br from-gray-900 via-black to-purple-900 text-white min-h-screen">
        <div class="flex min-h-screen flex-col selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full px-6 py-10">
                <main class="max-w-6xl mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-10 animate-fade-in-up">
                        <h1 class="text-6xl font-bold mb-3 bg-gradient-to-r from-green-400 via-blue-500 to-purple-600 bg-clip-text text-transparent">
                            Welcome Back! 🎉
                        </h1>
                        <p class="text-gray-400 text-xl">Choose your adventure below</p>
                    </div>

                    <!-- Credits Card -->
                    <div class="mb-12 animate-slide-up" style="animation-delay: 0.1s;">
                        <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-3xl p-8 shadow-2xl border border-green-500/30 transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                            <!-- Decorative circles -->
                            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-16 -mb-16"></div>

                            <div class="relative z-10 text-center">
                                <p class="text-green-200 text-2xl mb-2 font-medium">Your Credits</p>
                                <p class="text-7xl font-bold text-white mb-6">{{ credits.credits ?? 0 }}</p>
                                <Link :href="route('redeem')">
                                    <button class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-4 px-10 rounded-xl transition duration-300 transform hover:scale-110 shadow-lg text-xl">
                                        💰 Redeem Now
                                    </button>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Cards Grid -->
                    <div class="mb-12 animate-slide-up" style="animation-delay: 0.2s;">
                        <h2 class="text-3xl font-bold mb-6 text-center">Daily Activities</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Trivia Card -->
                            <Link :href="route('trivia')">
                                <div class="group bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-8 shadow-xl border border-blue-500/30 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 cursor-pointer relative overflow-hidden animate-slide-up" style="animation-delay: 0.3s;">
                                    <div class="absolute top-0 right-0 text-9xl opacity-10 transform rotate-12">
                                        🧠
                                    </div>
                                    <div class="relative z-10">
                                        <div class="text-5xl mb-4">🧠</div>
                                        <h3 class="text-3xl font-bold mb-2">Daily Trivia</h3>
                                        <p class="text-blue-200 mb-4">Test your knowledge</p>
                                        <span v-if="!triviaCompleted" class="inline-block bg-green-400 text-green-900 px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                                            Ready!
                                        </span>
                                        <span v-else class="inline-block bg-gray-600 text-gray-200 px-4 py-2 rounded-full text-sm font-bold">
                                            ✓ Done
                                        </span>
                                    </div>
                                </div>
                            </Link>

                            <!-- Music Card -->
                            <Link :href="route('music')">
                                <div class="group bg-gradient-to-br from-pink-600 to-purple-800 rounded-2xl p-8 shadow-xl border border-pink-500/30 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 cursor-pointer relative overflow-hidden animate-slide-up" style="animation-delay: 0.4s;">
                                    <div class="absolute top-0 right-0 text-9xl opacity-10 transform rotate-12">
                                        🎵
                                    </div>
                                    <div class="relative z-10">
                                        <div class="text-5xl mb-4">🎵</div>
                                        <h3 class="text-3xl font-bold mb-2">Music Rating</h3>
                                        <p class="text-pink-200 mb-4">Rate today's song</p>
                                        <span class="inline-block bg-green-400 text-green-900 px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                                            Ready!
                                        </span>
                                    </div>
                                </div>
                            </Link>

                            <!-- Emoji Movie Card -->
                            <Link :href="route('emoji')">
                                <div class="group bg-gradient-to-br from-yellow-600 to-orange-800 rounded-2xl p-8 shadow-xl border border-yellow-500/30 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 cursor-pointer relative overflow-hidden animate-slide-up" style="animation-delay: 0.5s;">
                                    <div class="absolute top-0 right-0 text-9xl opacity-10 transform rotate-12">
                                        🎬
                                    </div>
                                    <div class="relative z-10">
                                        <div class="text-5xl mb-4">🎬</div>
                                        <h3 class="text-3xl font-bold mb-2">Emoji Movie</h3>
                                        <p class="text-yellow-200 mb-4">Guess the movie</p>
                                        <span v-if="!emojiCompleted" class="inline-block bg-green-400 text-green-900 px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                                            Ready!
                                        </span>
                                        <span v-else class="inline-block bg-gray-600 text-gray-200 px-4 py-2 rounded-full text-sm font-bold">
                                            ✓ Done
                                        </span>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Coming Soon Section -->
                    <div class="mb-12 animate-slide-up" style="animation-delay: 0.6s;">
                        <h2 class="text-3xl font-bold mb-6 text-center">Coming Soon</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hunt Card -->
                            <div class="bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl p-8 shadow-xl border border-gray-600/30 relative overflow-hidden opacity-75">
                                <div class="absolute top-0 right-0 text-9xl opacity-5">
                                    🔍
                                </div>
                                <div class="relative z-10">
                                    <div class="text-5xl mb-4">🔍</div>
                                    <h3 class="text-3xl font-bold mb-2">Scavenger Hunt</h3>
                                    <p class="text-gray-400 mb-4">Find hidden treasures</p>
                                    <span class="inline-block bg-gray-600 text-gray-300 px-4 py-2 rounded-full text-sm font-bold">
                                        🔒 Coming Soon
                                    </span>
                                </div>
                            </div>

                            <!-- Extra slot for future activity -->
                            <div class="bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl p-8 shadow-xl border border-gray-600/30 relative overflow-hidden opacity-75">
                                <div class="absolute top-0 right-0 text-9xl opacity-5">
                                    🎮
                                </div>
                                <div class="relative z-10">
                                    <div class="text-5xl mb-4">🎮</div>
                                    <h3 class="text-3xl font-bold mb-2">Mystery Game</h3>
                                    <p class="text-gray-400 mb-4">More fun ahead</p>
                                    <span class="inline-block bg-gray-600 text-gray-300 px-4 py-2 rounded-full text-sm font-bold">
                                        🔒 Coming Soon
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services Table -->
                    <div v-if="services && services.length > 0" class="bg-gray-900/50 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-xl animate-slide-up" style="animation-delay: 0.7s;">
                        <h2 class="text-3xl font-bold text-center mb-6 bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">
                            Your Redeemed Services
                        </h2>
                        <div class="overflow-x-auto rounded-xl border border-gray-700">
                            <table class="w-full text-left">
                                <thead class="bg-gradient-to-r from-gray-800 to-gray-900">
                                    <tr>
                                        <th class="px-6 py-4 text-lg font-semibold text-gray-200">
                                            <div class="flex items-center space-x-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Service Title</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-4 text-lg font-semibold text-gray-200 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Times Used</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-4 text-lg font-semibold text-gray-200 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Fulfilled</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    <tr
                                        v-for="(service, index) in services"
                                        :key="service.id"
                                        :class="index % 2 === 0 ? 'bg-gray-900/50' : 'bg-gray-800/50'"
                                        class="hover:bg-gray-700/50 transition-colors"
                                    >
                                        <td class="px-6 py-4 text-lg text-gray-300 font-medium">
                                            {{ service.title }}
                                        </td>
                                        <td class="px-6 py-4 text-lg text-center">
                                            <span class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold shadow-lg">
                                                {{ service.times_used ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-lg text-center">
                                            <span class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 text-white font-bold shadow-lg">
                                                {{ service.fulfilled ?? 0 }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>


</template>

  <style scoped>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-up {
        opacity: 0;
        animation: slideUp 0.6s ease-out forwards;
    }

    .animate-fade-in-up {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
    </style>

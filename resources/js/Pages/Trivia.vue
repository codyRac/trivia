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
    canAnswer: Boolean // Assume trivia has properties like question and answers
});
const credits = ref(props.credits); // Make credits reactive
const canAnswer = ref(props.canAnswer); // Make credits reactive
const showAnswer = ref(false); // Make credits reactive

const answer = ref(''); // Make credits reactive

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
            showAnswer.value = true;
            answer.value =  response.data.answer
            console.log('made it')
            console.log('made itshowAnswer.value', showAnswer.value)
            console.log('made it answer', answer.value)

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
    <Head title="Daily Trivia" />
    <div class="bg-gradient-to-br from-gray-900 via-black to-blue-900 text-white min-h-screen">
        <div class="flex min-h-screen flex-col selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full px-6 py-10">
                <main class="max-w-4xl mx-auto">
                    <!-- Credits Display -->
                    <div class="mb-10">
                        <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-6 shadow-2xl border border-green-500/30 max-w-md mx-auto transform hover:scale-105 transition-transform duration-300">
                            <div class="text-center">
                                <p class="text-green-200 text-lg mb-1 font-medium">Available Credits</p>
                                <p class="text-5xl font-bold text-white mb-3">{{ credits.credits ?? 0 }}</p>
                                <Link :href="route('redeem')">
                                    <button class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-2 px-6 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                                        💰 Redeem
                                    </button>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Trivia Question Section -->
                    <div v-if="canAnswer" class="space-y-8">
                        <!-- Header -->
                        <div class="text-center">
                            <div class="inline-block bg-blue-600/20 border border-blue-500/50 rounded-full px-6 py-2 mb-4">
                                <span class="text-blue-300 font-semibold">🧠 Daily Challenge</span>
                            </div>
                            <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                                Today's Trivia
                            </h1>
                        </div>

                        <!-- Question Card -->
                        <div class="bg-gradient-to-br from-blue-900 to-purple-900 rounded-3xl p-8 shadow-2xl border border-blue-500/30 relative overflow-hidden">
                            <!-- Decorative elements -->
                            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

                            <div class="relative z-10">
                                <!-- Category Badge -->
                                <div class="mb-6">
                                    <span class="inline-block bg-purple-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                        {{ trivia.category }}
                                    </span>
                                </div>

                                <!-- Question -->
                                <h2 class="text-3xl font-bold text-white mb-8 leading-relaxed">
                                    {{ trivia.question }}
                                </h2>
                            </div>
                        </div>

                        <!-- Answer Options -->
                        <div class="bg-gray-900/50 backdrop-blur-sm rounded-2xl border border-gray-700 p-8 shadow-xl">
                            <h3 class="text-2xl font-semibold mb-6 text-center">Select Your Answer:</h3>
                            <div class="space-y-4">
                                <button
                                    v-for="answerOption in trivia.answers"
                                    :key="answerOption.id"
                                    @click="selectedAnswer = answerOption.text"
                                    :class="[
                                        'w-full p-5 rounded-xl font-semibold text-lg transition-all duration-200 text-left flex items-center space-x-4',
                                        selectedAnswer === answerOption.text
                                            ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white border-2 border-blue-400 transform scale-105 shadow-lg'
                                            : 'bg-gray-800 text-gray-300 hover:bg-gray-700 border-2 border-gray-600 hover:border-gray-500'
                                    ]"
                                >
                                    <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                                          :class="selectedAnswer === answerOption.text ? 'bg-white text-blue-600' : 'bg-gray-700 text-gray-400'">
                                        {{ String.fromCharCode(65 + trivia.answers.indexOf(answerOption)) }}
                                    </span>
                                    <span class="flex-1">{{ answerOption.text }}</span>
                                    <svg v-if="selectedAnswer === answerOption.text" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div class="text-center mt-8">
                                <button
                                    @click="enter"
                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-12 rounded-xl text-xl transition duration-300 transform hover:scale-105 shadow-lg"
                                >
                                    Submit Answer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Already Answered Section -->
                    <div v-else class="text-center space-y-8">
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-12 shadow-2xl border border-gray-700">
                            <div class="text-8xl mb-6">✅</div>
                            <h2 class="text-5xl font-bold mb-4">All Done for Today!</h2>
                            <p class="text-gray-400 text-xl mb-8">Come back tomorrow for a new challenge</p>

                            <div v-if="showAnswer" class="bg-blue-900/50 backdrop-blur-sm border border-blue-500/30 p-6 rounded-xl max-w-2xl mx-auto">
                                <p class="text-blue-300 text-lg mb-2">The correct answer was:</p>
                                <p class="text-3xl font-bold text-white">{{ answer }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-10 text-center">
                        <Link :href="route('holding')">
                            <button class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 inline-flex items-center space-x-2 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <span>Back to Home</span>
                            </button>
                        </Link>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

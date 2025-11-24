<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    puzzle: Object, // Expected: { id, emojis, answers: [] }
    credits: Object,
    canAnswer: Boolean
});

const credits = ref(props.credits);
const canAnswer = ref(props.canAnswer);
const selectedAnswer = ref(null);
const showAnswer = ref(false);
const correctAnswer = ref('');

const submitAnswer = async () => {
    if (!selectedAnswer.value) {
        toast("Please select a movie!", {
            autoClose: 1000,
        });
        return;
    }

    try {
        const response = await axios.post('/emoji/answer', {
            puzzle_id: props.puzzle.id,
            answer: selectedAnswer.value
        });

        canAnswer.value = false;
        showAnswer.value = true;
        correctAnswer.value = response.data.correct_answer;

        // Update credits if earned
        if (response.data.credits) {
            credits.value.credits = response.data.credits;
            credits.value.earned = response.data.earned;
        }

        toast(response.data.message || "Answer submitted!", {
            autoClose: 2000,
        });
    } catch (error) {
        toast(error.response?.data?.message || "An error occurred.", {
            autoClose: 2000,
        });
    }
};
</script>

<template>
    <Head title="Emoji Movie Puzzle" />
    <div class="bg-black text-white">
        <div class="flex min-h-screen flex-col selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full px-6">
                <main class="pt-10">
                    <!-- Credits Display -->
                    <div class="grid gap-6 text-center lg:grid-cols-1 lg:gap-8 mb-8">
                        <div class="rounded bg-green-900 text-3xl p-3">
                            Credits:
                            <div class="text-5xl">{{ credits.credits ?? 0 }}</div>
                            <Link :href="route('redeem')">
                                <button class="mt-2 bg-white text-green-900 px-4 py-2 rounded font-bold hover:bg-gray-100 transition">
                                    Redeem
                                </button>
                            </Link>
                        </div>
                    </div>

                    <div class="max-w-3xl mx-auto">
                        <!-- Puzzle Section -->
                        <div v-if="canAnswer" class="space-y-6">
                            <div class="text-center">
                                <h1 class="text-4xl font-bold mb-4">🎬 Emoji Movie Puzzle</h1>
                                <p class="text-gray-400 mb-8">Guess the movie from the emojis!</p>
                            </div>

                            <!-- Emoji Display -->
                            <div class="bg-gradient-to-br from-purple-900 to-blue-900 p-12 rounded-2xl text-center mb-8">
                                <div class="text-8xl mb-4 leading-relaxed">
                                    {{ puzzle.emojis }}
                                </div>
                            </div>

                            <!-- Answer Options -->
                            <div class="bg-gray-900 p-8 rounded-xl">
                                <h2 class="text-2xl mb-6 text-center font-semibold">Select the Movie:</h2>
                                <div class="grid grid-cols-1 gap-3">
                                    <button
                                        v-for="(answer, index) in puzzle.answers"
                                        :key="index"
                                        @click="selectedAnswer = answer"
                                        :class="[
                                            'p-4 rounded-lg font-semibold text-lg transition duration-200',
                                            selectedAnswer === answer
                                                ? 'bg-blue-600 text-white border-2 border-blue-400'
                                                : 'bg-gray-800 text-gray-300 hover:bg-gray-700 border-2 border-transparent'
                                        ]"
                                    >
                                        {{ answer }}
                                    </button>
                                </div>
                                
                                <div class="text-center mt-8">
                                    <button
                                        @click="submitAnswer"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-12 rounded-lg text-xl transition duration-200"
                                    >
                                        Submit Answer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Already Answered -->
                        <div v-else class="text-center space-y-6">
                            <div class="text-6xl mb-4">✅</div>
                            <h2 class="text-5xl font-bold">You're Done for Today!</h2>
                            
                            <div v-if="showAnswer" class="bg-gray-900 p-8 rounded-xl mt-8">
                                <p class="text-gray-400 text-xl mb-2">The correct answer was:</p>
                                <p class="text-3xl font-bold text-green-400">{{ correctAnswer }}</p>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <Link :href="route('holding')" class="block mt-8">
                            <button class="w-full bg-blue-700 hover:bg-blue-800 p-4 text-2xl text-center rounded-xl text-white transition duration-200">
                                ← Back to Home
                            </button>
                        </Link>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

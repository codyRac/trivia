<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    credits: Object,
    canAnswer: Boolean,
    question: Object, // Expected: { flag_id, code, is_new, answers: [] }
    progress: Object, // Expected: { answered, correct, total, learned, all }
    newFlag: Object,  // Today's new flag: { code, name }
});

const credits = ref(props.credits);
const canAnswer = ref(props.canAnswer);
const question = ref(props.question);
const progress = ref(props.progress);

// Result of the last answer, shown until "Next" is clicked
const result = ref(null); // { picked, correct, correctAnswer }
const nextQuestion = ref(null);
const submitting = ref(false);

const flagUrl = (code) => `https://flagcdn.com/w640/${code}.png`;
const percent = computed(() => progress.value.total ? Math.round((progress.value.answered / progress.value.total) * 100) : 0);

const pick = async (answer) => {
    if (result.value || submitting.value) return;
    submitting.value = true;

    try {
        const response = await axios.post('/flags/answer', {
            flag_id: question.value.flag_id,
            answer,
        });

        result.value = {
            picked: answer,
            correct: response.data.correct,
            correctAnswer: response.data.correct_answer,
        };
        nextQuestion.value = response.data.next;
        progress.value = response.data.progress;

        if (response.data.credits && credits.value) {
            credits.value.credits = response.data.credits;
            credits.value.earned = response.data.earned;
        }

        toast(response.data.message, { autoClose: 1500 });
    } catch (error) {
        toast(error.response?.data?.message || "An error occurred.", { autoClose: 2000 });
    } finally {
        submitting.value = false;
    }
};

const next = () => {
    if (nextQuestion.value) {
        question.value = nextQuestion.value;
    } else {
        canAnswer.value = false;
    }
    result.value = null;
    nextQuestion.value = null;
};

const answerClass = (answer) => {
    if (!result.value) {
        return 'bg-gray-800 text-gray-200 hover:bg-gray-700 border-transparent';
    }
    if (answer === result.value.correctAnswer) {
        return 'bg-green-600 text-white border-green-400';
    }
    if (answer === result.value.picked) {
        return 'bg-red-600 text-white border-red-400';
    }
    return 'bg-gray-800 text-gray-500 border-transparent';
};
</script>

<template>
    <Head title="Daily Flags" />
    <div class="bg-black text-white">
        <div class="flex min-h-screen flex-col selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full px-6">
                <main class="pt-10">
                    <!-- Credits Display -->
                    <div class="grid gap-6 text-center lg:grid-cols-1 lg:gap-8 mb-8">
                        <div class="rounded bg-green-900 text-3xl p-3">
                            Credits:
                            <div class="text-5xl">{{ credits?.credits ?? 0 }}</div>
                            <Link :href="route('redeem')">
                                <button class="mt-2 bg-white text-green-900 px-4 py-2 rounded font-bold hover:bg-gray-100 transition">
                                    Redeem
                                </button>
                            </Link>
                        </div>
                    </div>

                    <div class="max-w-3xl mx-auto">
                        <div class="text-center mb-6">
                            <h1 class="text-4xl font-bold mb-2">🌍 Daily Flags</h1>
                            <p class="text-gray-400">
                                {{ progress.learned }} / {{ progress.all }} flags collected
                            </p>
                        </div>

                        <!-- Round -->
                        <div v-if="canAnswer && question" class="space-y-6">
                            <!-- Progress -->
                            <div>
                                <div class="flex justify-between text-sm text-gray-400 mb-2">
                                    <span>Flag {{ Math.min(progress.answered + (result ? 0 : 1), progress.total) }} of {{ progress.total }}</span>
                                    <span>{{ progress.correct }} correct</span>
                                </div>
                                <div class="h-3 bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-teal-400 to-cyan-500 transition-all duration-500" :style="{ width: percent + '%' }"></div>
                                </div>
                            </div>

                            <!-- Flag -->
                            <div class="bg-gradient-to-br from-teal-900 to-cyan-900 p-8 rounded-2xl text-center relative">
                                <span v-if="question.is_new" class="absolute top-4 left-4 bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold">
                                    ✨ New flag
                                </span>
                                <img
                                    :src="flagUrl(question.code)"
                                    alt="Mystery flag"
                                    class="mx-auto max-h-64 rounded-lg shadow-2xl border border-white/20"
                                />
                            </div>

                            <!-- Answer Options -->
                            <div class="bg-gray-900 p-8 rounded-xl">
                                <h2 class="text-2xl mb-6 text-center font-semibold">Which country is this?</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <button
                                        v-for="answer in question.answers"
                                        :key="answer"
                                        @click="pick(answer)"
                                        :disabled="!!result || submitting"
                                        :class="['p-4 rounded-lg font-semibold text-lg transition duration-200 border-2', answerClass(answer)]"
                                    >
                                        {{ answer }}
                                    </button>
                                </div>

                                <div v-if="result" class="text-center mt-8">
                                    <button
                                        @click="next"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-12 rounded-lg text-xl transition duration-200"
                                    >
                                        {{ nextQuestion ? 'Next Flag →' : 'Finish 🏁' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Done for today -->
                        <div v-else class="text-center space-y-6">
                            <div class="text-6xl mb-4">✅</div>
                            <h2 class="text-5xl font-bold">You're Done for Today!</h2>
                            <p v-if="progress.total" class="text-2xl text-gray-300">
                                {{ progress.correct }} / {{ progress.total }} correct
                            </p>

                            <div v-if="newFlag" class="bg-gray-900 p-8 rounded-xl mt-8">
                                <p class="text-gray-400 text-xl mb-4">Today's new flag</p>
                                <img :src="flagUrl(newFlag.code)" :alt="newFlag.name" class="mx-auto max-h-40 rounded-lg shadow-xl border border-white/20 mb-4" />
                                <p class="text-3xl font-bold text-green-400">{{ newFlag.name }}</p>
                            </div>

                            <p v-if="progress.learned < progress.all" class="text-gray-400">Come back tomorrow for flag #{{ progress.learned + 1 }}!</p>
                        </div>

                        <!-- Back Button -->
                        <Link :href="route('holding')" class="block mt-8 mb-10">
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

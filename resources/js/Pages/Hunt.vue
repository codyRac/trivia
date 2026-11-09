<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    hunt: Object, // { status, intro, total, maxTotal, stopNumber, stopCount, stop, completed }
});

const hunt = ref(props.hunt);
const confirming = ref(false); // reveal button was tapped once, waiting for the second tap
const revealing = ref(false);
let pollInterval = null;

// Swap in fresh state, announcing when the hunt has moved to a new stage
const apply = (next) => {
    const previous = hunt.value;
    hunt.value = next;

    if (next.status === 'off') {
        router.visit(route('holding'));
        return;
    }

    if (next.stop?.id !== previous.stop?.id || next.status !== previous.status) {
        confirming.value = false;
        if (next.status === 'active') {
            toast(`📍 Stop ${next.stopNumber} unlocked!`, { autoClose: 2500 });
        } else if (next.status === 'finished') {
            toast('🎉 You finished the hunt!', { autoClose: 2500 });
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const poll = async () => {
    if (revealing.value) return;
    try {
        const response = await axios.get('/hunt/state');
        if (!revealing.value) apply(response.data);
    } catch (error) {
        // Spotty signal out on the hunt is expected — just try again on the next tick
    }
};

const reveal = async () => {
    if (!confirming.value) {
        confirming.value = true;
        return;
    }
    if (revealing.value) return;
    revealing.value = true;

    try {
        const response = await axios.post('/hunt/reveal', { stop_id: hunt.value.stop.id });
        apply(response.data.hunt);
    } catch (error) {
        if (error.response?.data?.hunt) {
            apply(error.response.data.hunt);
        }
        toast(error.response?.data?.message || "Couldn't reveal that. Try again.", { autoClose: 2000 });
    } finally {
        confirming.value = false;
        revealing.value = false;
    }
};

onMounted(() => {
    pollInterval = setInterval(poll, 5000);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>

<template>
    <Head title="Scavenger Hunt" />
    <div class="bg-gradient-to-br from-gray-900 via-black to-rose-950 text-white min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
            <Link :href="route('holding')" class="text-gray-400 hover:text-white text-sm">← Back</Link>

            <!-- Point total -->
            <div class="mt-4 mb-8 bg-gradient-to-br from-rose-600 to-pink-800 rounded-3xl p-6 shadow-2xl border border-rose-400/30 text-center">
                <p class="text-rose-100 text-lg font-medium">Your Points</p>
                <p class="text-7xl font-black tabular-nums leading-tight">{{ hunt.total }}</p>
                <p class="text-rose-200 text-sm">out of {{ hunt.maxTotal }} possible</p>
            </div>

            <!-- Intro -->
            <div v-if="hunt.status === 'intro'" class="bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold mb-6 bg-gradient-to-r from-rose-300 via-pink-400 to-red-400 bg-clip-text text-transparent">
                    ❤️ Your Birthday Adventure Begins ❤️
                </h1>
                <p class="text-lg text-gray-200 whitespace-pre-line leading-relaxed">{{ hunt.intro }}</p>
                <p class="mt-8 text-rose-300 animate-pulse">Your first clue will appear here soon…</p>
            </div>

            <!-- Current stop -->
            <div v-else-if="hunt.status === 'active' && hunt.stop" class="space-y-5">
                <div class="flex items-end justify-between">
                    <h1 class="text-3xl font-bold">Stop {{ hunt.stopNumber }} <span class="text-gray-500 text-xl">of {{ hunt.stopCount }}</span></h1>
                    <span class="bg-yellow-400 text-gray-900 px-4 py-1.5 rounded-full text-sm font-bold whitespace-nowrap">
                        Worth {{ hunt.stop.worth }} {{ hunt.stop.worth === 1 ? 'pt' : 'pts' }}
                    </span>
                </div>

                <div v-if="hunt.stop.extra" class="bg-blue-500/15 border border-blue-400/30 text-blue-100 rounded-2xl px-5 py-3">
                    👋 {{ hunt.stop.extra }}
                </div>

                <div
                    v-for="(clue, index) in hunt.stop.clues"
                    :key="index"
                    class="bg-white/5 border border-white/10 rounded-2xl p-5 sm:p-6"
                    :class="{ 'opacity-60': index < hunt.stop.clues.length - 1 || hunt.stop.answer }"
                >
                    <p class="text-xs uppercase tracking-widest text-rose-300 mb-2">
                        Clue {{ index + 1 }} · {{ clue.points }} points
                    </p>
                    <p class="text-lg sm:text-xl whitespace-pre-line leading-relaxed">{{ clue.text }}</p>
                </div>

                <div v-if="hunt.stop.answer" class="bg-gradient-to-br from-yellow-500/20 to-orange-600/20 border border-yellow-400/40 rounded-2xl p-5 sm:p-6 text-center">
                    <p class="text-xs uppercase tracking-widest text-yellow-300 mb-2">Answer · 1 point</p>
                    <p class="text-2xl sm:text-3xl font-bold">{{ hunt.stop.answer }}</p>
                </div>

                <!-- Reveal more -->
                <div v-if="hunt.stop.nextWorth" class="pt-2">
                    <button
                        @click="reveal"
                        :disabled="revealing"
                        class="w-full font-bold py-4 px-6 rounded-xl transition duration-200 text-lg disabled:opacity-50"
                        :class="confirming ? 'bg-red-600 hover:bg-red-500 text-white' : 'bg-gray-800 hover:bg-gray-700 text-gray-100 border border-gray-600'"
                    >
                        <template v-if="confirming">
                            Sure? This stop drops to {{ hunt.stop.nextWorth }} {{ hunt.stop.nextWorth === 1 ? 'point' : 'points' }} — tap again
                        </template>
                        <template v-else>
                            {{ hunt.stop.nextIsAnswer ? '🏳️ Show me the answer' : '💡 Reveal another clue' }}
                            <span class="text-gray-400 font-normal">(−{{ hunt.stop.worth - hunt.stop.nextWorth }} pts)</span>
                        </template>
                    </button>
                    <button v-if="confirming" @click="confirming = false" class="w-full mt-2 py-2 text-gray-400 hover:text-white">
                        Never mind, I've got this
                    </button>
                </div>
            </div>

            <!-- Finished -->
            <div v-else-if="hunt.status === 'finished'" class="bg-white/5 border border-white/10 rounded-3xl p-8 text-center">
                <div class="text-6xl mb-4">🎉</div>
                <h1 class="text-4xl font-bold mb-3">You made it!</h1>
                <p class="text-xl text-gray-200">
                    Final score: <span class="font-bold text-yellow-300">{{ hunt.total }}</span> of {{ hunt.maxTotal }} points
                </p>
                <p class="mt-4 text-rose-300 text-lg">Happy Birthday ❤️</p>
            </div>

            <!-- Completed stops -->
            <div v-if="hunt.completed.length" class="mt-10">
                <h2 class="text-sm uppercase tracking-widest text-gray-400 mb-3">Stops found</h2>
                <ul class="space-y-2">
                    <li
                        v-for="stop in hunt.completed"
                        :key="stop.number"
                        class="flex items-center justify-between bg-white/5 border border-white/10 rounded-xl px-4 py-3"
                    >
                        <span><span class="text-green-400 mr-2">✓</span>{{ stop.number }}. {{ stop.title }}</span>
                        <span class="font-bold text-yellow-300 whitespace-nowrap">+{{ stop.points }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    song: Object // Expected: { title, artist, album, spotify_url, apple_music_url }
});

const currentSong = ref(props.song);
const selectedRating = ref(null);

const submitRating = async () => {
    if (!selectedRating.value) {
        toast("Please select a rating.", {
            autoClose: 1000,
        });
        return;
    }

    try {
        const response = await axios.post('/music/rate', {
            song_id: currentSong.value.id,
            rating: selectedRating.value
        });

        toast(response.data.message || "Rating submitted successfully!", {
            autoClose: 2000,
        });

        // Update to new song if available
        if (response.data.newSong) {
            currentSong.value = response.data.newSong;
            selectedRating.value = null; // Reset rating selection
        }
    } catch (error) {
        toast(error.response?.data?.message || "An error occurred.", {
            autoClose: 2000,
        });
    }
};
</script>

<template>
    <Head title="Music" />
    <div class="bg-black text-white">
        <div class="flex min-h-screen flex-col selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full px-6">
                <main class="pt-10">
                    <div class="max-w-2xl mx-auto">
                        <!-- Song Details -->
                        <div class="text-center mb-8">
                            <h1 class="text-5xl font-bold mb-4">{{ currentSong.title }}</h1>
                            <p class="text-3xl text-gray-300 mb-2">{{ currentSong.artist }}</p>
                            <p class="text-2xl text-gray-400">{{ currentSong.album }}</p>
                        </div>

                        <!-- Streaming Service Buttons -->
                        <div class="flex gap-4 justify-center mb-8">
                            <a
                                v-if="currentSong.spotify_url"
                                :href="currentSong.spotify_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200"
                            >
                                Listen on Spotify
                            </a>
                            <a
                                v-if="currentSong.apple_music_url"
                                :href="currentSong.apple_music_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200"
                            >
                                Listen on Apple Music
                            </a>
                        </div>

                        <!-- Rating System -->
                        <div class="border border-gray-700 p-8 rounded-lg">
                            <h2 class="text-2xl mb-6 text-center">Rate this song</h2>
                            <div class="flex gap-3 justify-center mb-6 flex-wrap">
                                <button
                                    v-for="rating in 10"
                                    :key="rating"
                                    @click="selectedRating = rating"
                                    :class="[
                                        'w-12 h-12 rounded-lg font-bold text-lg transition duration-200',
                                        selectedRating === rating
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
                                    ]"
                                >
                                    {{ rating }}
                                </button>
                            </div>
                            <div class="text-center">
                                <button
                                    @click="submitRating"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200"
                                >
                                    Submit Rating
                                </button>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

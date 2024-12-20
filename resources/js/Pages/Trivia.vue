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
    <Head title="Welcome" />
    <div class=" bg-black text-white">

        <div
            class="flex min-h-screen
            flex-col

            selection:bg-[#FF2D20]
            selection:text-white"
        >
            <div class="relative w-full  px-6 ">
                <main class="pt-10">
                    <div class="grid gap-6 text-center lg:grid-cols-1 lg:gap-8">
                        <div class="rounded bg-green-900 text-3xl p-3">
                            Credits:
                            <div class="text-5xl">{{ credits.credits  ?? 0 }}</div>
                            <Link
                                :href="route('redeem')"
                            >
                                <SecondaryButton>redeem</SecondaryButton>
                            </Link>
                        </div>
                    </div>
                    <div v-if="canAnswer" class="grid gap-6 text-center lg:grid-cols-1 my-4 lg:gap-8">
                        <div class="text-4xl">Todays Trivia</div>
                        <div class="text-2xl"> {{ trivia.question }}
                            <div class="text-xl"> {{ trivia.category }}</div>
                        </div>
                        <div class="border p-6 rounded">
                            <p class="mb-4">Please select an answer:</p>
                            <div v-for="answer in trivia.answers" :key="answer.id" class="flex items-center mb-2">
                                <input
                                    type="radio"
                                    :id="'answer-' + answer.id"
                                    :value="answer.text"
                                    v-model="selectedAnswer"
                                    class="mr-2"
                                />
                                <label :for="'answer-' + answer.id" class="cursor-pointer">
                                    {{ answer.text }}
                                </label>
                            </div>
                        </div>
                        <SecondaryButton @click="enter();">Enter</SecondaryButton>
                    </div>
                    <div v-else class="text-6xl gap-6 my-3 text-center ">
                        You are done for today!
                    </div>
                </main>



            </div>
        </div>
    </div>
</template>

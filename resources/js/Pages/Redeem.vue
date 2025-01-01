<script setup>
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref, onMounted } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
const props = defineProps({
    credits: Object,
    services: Object,
});
const selectedAnswer = ref(null);
const use = async (serviceId, cost) => {
    // Check if user has enough credits
    if (props.credits.credits < cost) {
        toast.error('Not enough credits!', {
                autoClose: 2000,
            });
        return;
    }
    if(confirm('Are you user you want to redeem?')){
        try {
            const response = await axios.post('/use-credits', { service_id: serviceId });
            if (response.data.success) {
                toast.success(response.data.message || 'Service redeemed successfully!', {
                    autoClose: 2000,
                });
                props.credits.credits = response.data.remaining_credits; // Update credits dynamically
            } else {
                toast.error(response.data.message || 'Failed to redeem service.', {
                    autoClose: 2000,
                });
        }
        } catch (error) {
            toast.error(error.response?.data?.message || 'An error occurred. Please try again.', {
                autoClose: 2000,
            });
        }
    }
};

const showFilters = ref(false)

// Filter states
const filters = ref({
    category: '',
    min_cost: '',
    max_cost: '',
    search: '',
    fav:''
});

const applyFilters = () => {
    const queryParams = new URLSearchParams(filters.value).toString();
    window.location.href = `/redeem?${queryParams}`;
};

const fav = async (val, service) => {
    try {
            const response = await axios.post('/fav', { service_id: service.id, favorite:val });
            if (response.data.success) {
                toast.success(response.data.message || 'Service Updated!', {
                    autoClose: 2000,
                });
                service.favorite = val; // Update credits dynamically
            } else {
                toast.error(response.data.message || 'Failed to update service.', {
                    autoClose: 2000,
                });
        }
        } catch (error) {
            toast.error(error.response?.data?.message || 'An error occurred. Please try again.', {
                autoClose: 2000,
            });
        }
};
// Clear all filters
const clearFilters = () => {
    filters.value = {
        category: '',
        min_cost: '',
        max_cost: '',
        search: '',
    };
    applyFilters(); // Redirect without filters
};
onMounted(() => {
    const queryParams = new URLSearchParams(window.location.search);
    filters.value.category = queryParams.get('category') || '';
    filters.value.min_cost = queryParams.get('min_cost') || '';
    filters.value.max_cost = queryParams.get('max_cost') || '';
    filters.value.search = queryParams.get('search') || '';
    filters.value.fav = queryParams.get('fav') || '';

});

</script>

<template>
    <Head title="Welcome" />
    <div class=" bg-black text-white">

        <div
            class="flex min-h-screen
            flex-col


            selection:text-white"
        >
            <div class="relative w-full bg-gradient-to-b from-gray-900 to-blue-700 px-6 ">
                <main class="pt-10">
                    <div class="grid gap-6 text-center lg:grid-cols-1 lg:gap-8">
                        <div class="rounded bg-green-900 text-3xl p-3">
                            Credits:
                            <div class="text-5xl">{{ credits.credits }}</div>
                            <Link
                                :href="route('redeem')"
                            >
                            </Link>
                        </div>
                    </div>
                                        <!-- Filter Section -->
                    <div class="border rounded p-6 mb-6 mt-4">
                        <div @click="showFilters = !showFilters" class="rounded bg-green-200 text-black p-1 text-center">Toggle Filters</div>

                        <form @submit.prevent="applyFilters" v-show="showFilters" class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                            <div>
                                <label for="category" class="block text-sm font-medium dark:text-gray-300 ">Category</label>
                                <select id="category" v-model="filters.category" class="mt-1 block w-full text-gray-500 dark:text-gray-300 dark:bg-gray-900">
                                    <option value="">All</option>
                                    <option value="food">Food</option>
                                    <option value="service">Service</option>
                                </select>
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium dark:text-gray-300 ">Favorite</label>
                                <select id="category" v-model="filters.fav" class="mt-1 block w-full text-gray-500 dark:text-gray-300 dark:bg-gray-900">
                                    <option value="">All</option>
                                    <option :value="1">Favorite</option>
                                </select>
                            </div>
                            <div>
                                <label for="min_cost" class="block text-sm font-medium text-gray-300">Min Cost</label>
                                <TextInput type="number" id="min_cost" v-model="filters.min_cost" class="mt-1 block w-full" placeholder="Min Cost" />
                            </div>
                            <div>
                                <label for="max_cost" class="block text-sm font-medium text-gray-300">Max Cost</label>
                                <TextInput type="number" id="max_cost" v-model="filters.max_cost" class="mt-1 block w-full" placeholder="Max Cost" />
                            </div>
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-300">Search</label>
                                <TextInput type="text" id="search" v-model="filters.search" class="mt-1 block w-full" placeholder="Search" />
                            </div>
                            <div class="lg:col-span-4 text-right">
                                <SecondaryButton type="submit">Apply Filters</SecondaryButton>
                                <SecondaryButton type="button" @click="clearFilters" class="ml-2 bg-red-500 text-white">
                                    Clear All Filters
                                </SecondaryButton>
                            </div>
                        </form>
                    </div>
                    <div class="grid lg:px-10 gap-6
                        text-center grid-cols-2
                        lg:grid-cols-5 my-4 lg:gap-8">
                        <div :key="service.id" class="rounded border p-3
                         bg-gradient-to-b from-green-900 to-green-800

                        " v-for="service in services">
                            <div class="text-xl">
                                {{ service.title }}
                            </div>
                            <div class="text-xs">
                                {{ service.duration }}
                            </div>
                            <div class="text-xs">
                                {{ service.category }}
                            </div>
                            <SecondaryButton v-if="credits.credits >= service.cost" class="mt-3" @click="use(service.id,service.cost );">{{ service.cost }} Credits</SecondaryButton>
                            <div class="mt-3 rounded bg-red-500" v-else>{{ service.cost }} Credits</div>
                            <div class="text-xs mt-3">Times Reedem {{ service.times_used }}</div>
                            <div @click="fav(0, service)" v-if="service.favorite" class="text-xs mt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 mx-auto">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div @click="fav(1, service)" v-else class="text-xs text-center mt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                </svg>
                            </div>

                        </div>
                    </div>
                </main>



            </div>
        </div>
    </div>
</template>

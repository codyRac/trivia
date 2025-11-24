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
    <Head title="Redeem Credits" />
    <div class="bg-gradient-to-br from-gray-900 via-black to-blue-900 text-white min-h-screen">
        <div class="flex min-h-screen flex-col selection:text-white">
            <div class="relative w-full px-6 py-10">
                <main class="max-w-7xl mx-auto">
                    <!-- Header Section -->
                    <div class="text-center mb-10">
                        <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">
                            Redeem Your Credits
                        </h1>
                        <p class="text-gray-400 text-lg">Choose from our amazing services and rewards</p>
                    </div>

                    <!-- Credits Display Card -->
                    <div class="mb-10">
                        <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-8 shadow-2xl border border-green-500/30 max-w-md mx-auto transform hover:scale-105 transition-transform duration-300">
                            <div class="text-center">
                                <p class="text-green-200 text-xl mb-2 font-medium">Available Credits</p>
                                <p class="text-6xl font-bold text-white mb-4">{{ credits.credits }}</p>
                                <div class="flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-green-200">Start redeeming below</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-2xl border border-gray-700 p-6 mb-8 shadow-xl">
                        <button
                            @click="showFilters = !showFilters"
                            class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-3 px-6 rounded-xl transition duration-300 flex items-center justify-center space-x-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ showFilters ? 'Hide Filters' : 'Show Filters' }}</span>
                        </button>

                        <form @submit.prevent="applyFilters" v-show="showFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                                <select id="category" v-model="filters.category" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Categories</option>
                                    <option value="food">Food</option>
                                    <option value="service">Service</option>
                                </select>
                            </div>
                            <div>
                                <label for="favorite" class="block text-sm font-medium text-gray-300 mb-2">Favorite</label>
                                <select id="favorite" v-model="filters.fav" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Items</option>
                                    <option :value="1">Favorites Only</option>
                                </select>
                            </div>
                            <div>
                                <label for="min_cost" class="block text-sm font-medium text-gray-300 mb-2">Min Cost</label>
                                <TextInput type="number" id="min_cost" v-model="filters.min_cost" class="w-full bg-gray-800 border-gray-600" placeholder="0" />
                            </div>
                            <div>
                                <label for="max_cost" class="block text-sm font-medium text-gray-300 mb-2">Max Cost</label>
                                <TextInput type="number" id="max_cost" v-model="filters.max_cost" class="w-full bg-gray-800 border-gray-600" placeholder="1000" />
                            </div>
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                                <TextInput type="text" id="search" v-model="filters.search" class="w-full bg-gray-800 border-gray-600" placeholder="Search services..." />
                            </div>
                            <div class="lg:col-span-5 flex justify-end space-x-3">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-300">
                                    Apply Filters
                                </button>
                                <button type="button" @click="clearFilters" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-300">
                                    Clear Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Services Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div
                            v-for="service in services"
                            :key="service.id"
                            class="group bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl border border-gray-700 overflow-hidden shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300"
                        >
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-4 relative">
                                <div class="absolute top-3 right-3">
                                    <button @click="fav(service.favorite ? 0 : 1, service)" class="focus:outline-none transform hover:scale-110 transition-transform">
                                        <svg v-if="service.favorite" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-yellow-400 drop-shadow-lg">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-gray-300 hover:text-yellow-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                        </svg>
                                    </button>
                                </div>
                                <h3 class="text-2xl font-bold text-white pr-10">{{ service.title }}</h3>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-center space-x-2 text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm">{{ service.duration }}</span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                                        {{ service.category }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-gray-400 text-sm pt-2">
                                    <span>Redeemed</span>
                                    <span class="font-bold text-green-400">{{ service.times_used }}x</span>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="px-6 pb-6">
                                <button
                                    v-if="credits.credits >= service.cost"
                                    @click="use(service.id, service.cost)"
                                    class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center space-x-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ service.cost }} Credits</span>
                                </button>
                                <div
                                    v-else
                                    class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white font-bold py-3 px-6 rounded-xl text-center shadow-lg opacity-50 cursor-not-allowed flex items-center justify-center space-x-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ service.cost }} Credits</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-12 text-center">
                        <Link :href="route('holding')" class="inline-block">
                            <button class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 flex items-center space-x-2 shadow-lg">
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

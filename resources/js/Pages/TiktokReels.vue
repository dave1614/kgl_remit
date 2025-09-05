<script setup>
import { useForm, usePage, Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, reactive, onMounted, onUnmounted } from 'vue'
import { mdiAccount, mdiEmail, mdiFormTextboxPassword } from '@mdi/js'
import LayoutSocial from '@/Layouts/LayoutSocial.vue'

import Loader from '@/Loaders/loader.gif'
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'
import FlashMessages from '@/Components/FlashMessages.vue'
import CreatePost from '@/Components/Social/CreatePost.vue'
import Post from '@/Components/Social/Post.vue'
import PostPopup from '@/Components/Social/PostPopup.vue'



import { useDarkModeStore } from '@/Stores/darkMode.js'
import { useMainStore } from '@/Stores/main.js'

import SecondLoginImage from '@/Images/second_login.jpg'
import Avatar from '@/Images/avatar.jpg'

const props = defineProps({
    reels: {
        type: Array,
        default: []
    },
    user: {
        type: Object,
    },
    create: {
        type: Boolean,
        default: false,
    }

});

console.log(props.reels)

const reels = ref(props.reels);


const darkModeStore = useDarkModeStore()
const mainStore = useMainStore()

const page = usePage()






// const state = reactive({files});

const comment_input = ref(null);
const open_post_index = ref(false);
const open_media_index = ref(false);
const current_media_index = ref(0);
const make_comment_open = ref(false);

const post_liked = ref(false);
const show_more = ref(false);


const posts_loading = ref(false);
const last_post = ref(false);

const videos = ref([
    { id: 1, src: '/test_videos/3196600-uhd_3840_2160_25fps.mp4', user: 'user1', caption: 'This is video 1', likes: 123, comments: 45, liked: false, paused: false, bookmarked: false },
    { id: 2, src: '/videos/video2.mp4', user: 'user2', caption: 'This is video 2', likes: 456, comments: 78, liked: false, paused: false, bookmarked: false },
    { id: 3, src: '/videos/video3.mp4', user: 'user3', caption: 'This is video 3', likes: 789, comments: 90, liked: false, paused: false, bookmarked: false },
]);

const comments = Array.from({ length: 20 }, (_, i) => `This is comment #${i + 1}`);
const showComments = ref(false);
const burstIndex = ref(null);
burstIndex.value = null;
const burstX = ref(0);
const burstY = ref(0);

const reelsWrapper = ref(null);
const videoRefs = ref([]);

let tapTimeout = null;
let lastTapTime = 0;


const toggleBookmark = (index) => {
    videos.value[index].bookmarked = !videos.value[index].bookmarked;
};

const shareVideo = (video) => {
    const shareData = {
        title: 'Check this reel!',
        text: video.caption,
        url: window.location.href, // Or a link to the specific reel
    };

    if (navigator.share) {
        navigator.share(shareData).catch((err) => console.error('Sharing failed:', err));
    } else {
        alert('Sharing not supported in this browser.');
    }
};


const handleTap = (index, event) => {
    const now = Date.now();
    const DOUBLE_TAP_DELAY = 300;

    if (now - lastTapTime < DOUBLE_TAP_DELAY) {
        // It's a double tap
        clearTimeout(tapTimeout);
        lastTapTime = 0;
        triggerLike(index, event);
    } else {
        // It's a single tap — might be a double, so delay it
        lastTapTime = now;
        tapTimeout = setTimeout(() => {
            togglePlay(index);
            lastTapTime = 0;
        }, DOUBLE_TAP_DELAY);
    }
};

const triggerLike = (index, event) => {
    if (!videos.value[index].liked) {
        videos.value[index].likes += 1;
    } else {
        videos.value[index].likes -= 1;
    }
    videos.value[index].liked = !videos.value[index].liked;

    // Heart burst effect
    const burst = document.createElement('div');
    burst.className = 'absolute text-white text-5xl animate-ping-fast pointer-events-none';
    burst.style.left = `${event.clientX}px`;
    burst.style.top = `${event.clientY}px`;
    burst.innerHTML = `<i class="fas fa-heart text-red-500"></i>`;
    event.currentTarget.parentElement.appendChild(burst);
    setTimeout(() => burst.remove(), 600);
};

const handleScroll = () => {
    const scrollPos = reelsWrapper.value.scrollTop;
    const index = Math.round(scrollPos / window.innerHeight);
    videoRefs.value.forEach((v, i) => {
        if (i === index) v.play();
        else v.pause();
    });
};

const toggleLike = (index) => {
    videos.value[index].liked = !videos.value[index].liked;
    videos.value[index].likes += videos.value[index].liked ? 1 : -1;
};

const togglePlay = (index) => {
    const video = videoRefs.value[index];
    if (video.paused) {
        video.play();
        videos.value[index].paused = false;
    } else {
        video.pause();
        videos.value[index].paused = true;
    }
};

const handleDoubleClick = (index, event) => {
    toggleLike(index);
    burstIndex.value = index;

    const containerRect = reelsWrapper.value.getBoundingClientRect();
    burstX.value = event.clientX - containerRect.left;
    burstY.value = event.clientY - containerRect.top;

    setTimeout(() => (burstIndex.value = null), 800);
};

const openComments = (index) => {
    showComments.value = true;
};

const scrollUp = () => {
    reelsWrapper.value.scrollBy({ top: -window.innerHeight, behavior: 'smooth' });
};

const scrollDown = () => {
    reelsWrapper.value.scrollBy({ top: window.innerHeight, behavior: 'smooth' });
};

onMounted(() => {
    videoRefs.value = Array.from(reelsWrapper.value.querySelectorAll('video'));
    videoRefs.value[0].play();
});

</script>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.heart-burst-enter-active {
    animation: burst 0.5s ease-out;
}

@keyframes ping-fast {
    0% {
        transform: scale(1);
        opacity: 1;
    }

    80% {
        transform: scale(2);
        opacity: 0;
    }

    100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-ping-fast {
    animation: ping-fast 0.6s ease-out;
}


@keyframes burst {
    0% {
        transform: scale(1);
        opacity: 0.8;
    }

    50% {
        transform: scale(1.4);
        opacity: 1;
    }

    100% {
        transform: scale(1);
        opacity: 0;
    }
}
</style>


<template>
    <LayoutSocial :highlight_type="'home'">

        <!-- <div class="fixed top-0 bottom-0 left-0 right-0 w-screen h-screen ">
      <div class="relative w-full h-full">

      </div>
    </div> -->

        <div class="w-full">
            <div class="w-full min-h-screen mt-[85px]">
                <div class="grid grid-cols-12 gap-6">
                    <div class="hidden sm:inline-block sm:col-span-3">

                    </div>



                    <div
                        class="col-span-12 sm:col-span-6 border-0 sm:border-r-[1px] border-secondary-300 dark:border-slate-500 min-h-screen">
                        <div class="relative w-full h-screen overflow-hidden bg-black">
                            <!-- Comment Modal -->
                            <div v-if="showComments"
                                class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50">
                                <div class="bg-white w-80 max-h-[80vh] overflow-y-auto rounded-lg shadow-lg p-4">
                                    <h2 class="text-lg font-bold mb-4">Comments</h2>
                                    <ul class="space-y-2">
                                        <li v-for="(comment, i) in comments" :key="i"
                                            class="text-sm text-gray-700 border-b pb-1">{{ comment }}</li>
                                    </ul>
                                    <button @click="showComments = false"
                                        class="mt-4 text-sm text-blue-500 hover:underline">Close</button>
                                </div>
                            </div>

                            <!-- Reels Wrapper -->
                            <div ref="reelsWrapper"
                                class="h-full w-full snap-y snap-mandatory overflow-y-scroll scrollbar-hide"
                                @scroll.passive="handleScroll">
                                <div v-for="(video, index) in reels" :key="video.id"
                                    class="w-full min-h-[95vh] flex items-center justify-center snap-start relative">
                                    <video ref="videoRefs" class="w-full h-full object-cover" :src="video.media[0].path"
                                        @click="handleTap(index, $event)" playsinline muted loop></video>
                                    <font-awesome-icon v-if="video.paused" icon="fa-solid fa-play"
                                        class="absolute text-white text-5xl opacity-80 pointer-events-none"
                                        style="top: 50%; left: 50%; transform: translate(-50%, -50%)" />

                                    <!-- Heart animation -->
                                    <transition name="heart-burst">
                                        <font-awesome-icon v-if="burstIndex === index" icon="fa-solid fa-heart"
                                            class="absolute text-red-600 text-7xl animate-ping pointer-events-none"
                                            :style="{ top: `${burstY}px`, left: `${burstX}px`, transform: 'translate(-50%, -50%)' }" />
                                    </transition>

                                    <!-- Overlay UI -->
                                    <div class="absolute bottom-10 left-4 text-white space-y-2">

                                        <Link :href="route('profile.show', video.user.slug)" class="font-bold text-lg">
                                        {{
                                            video.user.user_name }}</Link>
                                        <span class="text-secondary-600 text-xs font-bold inline-block ml-1">• {{
                                            video.relative_time }}</span>
                                        <br>
                                        <p class="text-sm w-[85%]">

                                            <span v-if="video.caption == video.caption_short" class="inline-block"
                                                v-html="video.html_caption"></span>

                                            <span v-else-if="(video.caption != video.caption_short) && video.show_more"
                                                class="inline-block"><span v-html="video.html_caption"></span> <span
                                                    @click="video.show_more = !video.show_more"
                                                    class="text-primary-100 inline-block ml-1 cursor-pointer hover:underline">less</span></span>

                                            <span v-else-if="(video.caption != video.caption_short) && !video.show_more"
                                                class="inline-block"><span v-html="video.html_caption_short"></span>
                                                <span @click="video.show_more = !video.show_more"
                                                    class="text-primary-100 inline-block ml-1 cursor-pointer hover:underline">read
                                                    more</span>
                                            </span>
                                        </p>


                                    </div>

                                    <div
                                        class="absolute bottom-10 right-4 text-white flex flex-col items-center space-y-4">
                                        <button @click="toggleLike(index)"
                                            class="focus:outline-none flex flex-col items-center">
                                            <font-awesome-icon :icon="['fas', 'heart']"
                                                :class="video.liked ? 'text-red-500 text-2xl' : 'text-white text-2xl'" />
                                            <span class="text-xs mt-1">{{
                                                mainStore.addCommas(video.likes_num) }}</span>
                                        </button>

                                        <button class="flex flex-col items-center" @click="openComments(index)">
                                            <font-awesome-icon icon="fa-solid fa-comment-dots" class="text-2xl" />
                                            <span class="text-xs mt-1">{{ video.comments_num }}</span>
                                        </button>

                                        <!-- Bookmark -->
                                        <button @click="mainStore.addPostToFavorites(video)"
                                            class="focus:outline-none flex flex-col items-center">
                                            <font-awesome-icon :icon="['fas', 'bookmark']"
                                                :class="video.is_favorite ? 'text-yellow-400 text-2xl' : 'text-white text-2xl'" />
                                            <span class="text-xs mt-1">Save</span>
                                        </button>

                                        <!-- Share -->
                                        <button @click="shareVideo(video)"
                                            class="focus:outline-none flex flex-col items-center">
                                            <font-awesome-icon icon="fa-solid fa-share-nodes" class="text-2xl" />
                                            <span class="text-xs mt-1">Share</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Scroll Buttons (Desktop) -->
                            <div class="hidden md:flex absolute top-1/2 left-4 transform -translate-y-1/2 z-10">
                                <button @click="scrollUp"
                                    class="bg-white/20 text-white p-2 rounded-full hover:bg-white/40">
                                    <font-awesome-icon icon="fa-solid fa-chevron-up" />
                                </button>
                            </div>
                            <div class="hidden md:flex absolute top-1/2 right-4 transform -translate-y-1/2 z-10">
                                <button @click="scrollDown"
                                    class="bg-white/20 text-white p-2 rounded-full hover:bg-white/40">
                                    <font-awesome-icon icon="fa-solid fa-chevron-down" />
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="hidden sm:inline-block sm:col-span-3">

                    </div>
                </div>

            </div>
        </div>


    </LayoutSocial>
</template>

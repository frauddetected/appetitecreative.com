<template>
    <div :class="className ? className : 'w-6/12'">
    <transition name="fade" mode="out-in">
    <div v-if="loaded" class="bg-white w-full">

        <h2 class="flex w-full border-b p-4">                       
            Quiz by  
            <div class="filters-dropdown">
                <span>Answers</span>
            </div>
        </h2>

        <div class="flex justify-center mt-8 mb-4">
            <div class="flex w-1/4 justify-center">
                <div class="w-2/3 text-center">
                    <h2 class="uppercase text-xs">Unique Users <br> Answers</h2>
                    <h1 class="text-3xl ml-auto font-bold">
                        {{ stats.users.uniques }}
                    </h1>
                </div>
            </div>
            
            <div class="flex w-1/4 justify-center">
                <div class="w-2/3 text-center">
                    <h2 class="uppercase text-xs">Avg Repeated Questions <br> Per User</h2>
                    <h1 class="text-3xl ml-auto font-bold">
                        {{ nFormatter(stats.users.repeat, 2) }}
                    </h1>
                </div>
            </div>
        </div>

        <ul class="grid grid-cols-2 gap-6 p-6 overflow-y-auto" style="height: 575px;">
            <li class="p-4 border bg-white border-gray-300" v-for="(q, index) in questions">
                <h2 class="text-base h-20">{{ index }}. {{ q.title }}</h2>
                <div 
                    v-tippy="{ content: `<div class='p-2'>Answered <strong class='ml-0'>(${Math.round(answer.total_answers * 100 / q.total_answers_count)}%)</strong> of the time</div>`, followCursor: true }" 
                    class="flex relative justify-between items-center border-b-2 border-white" v-for="answer in q.answers"
                    >
                    <p class="py-3 px-1 font-bold w-full flex">
                        {{ answer.title }}
                        <span v-if="answer.is_correct == true" class="ml-auto text-xs text-green-400 uppercase">correct</span>
                    </p>
                    <div class="flex">
                        <!--<span class="rounded-sm bg-ms-blue-20 text-gray-400 text-xs py-1 px-2 w-8">{{ answer.total_answers }}x</span>-->
                        <span class="ml-2 rounded-sm bg-ms-gray-120  text-white text-xs py-1 px-2 w-12">{{ Math.round(answer.total_answers * 100 / q.total_answers_count) }}%</span>
                    </div>
                    <div class="duration-300 left-0 bottom-0 h-full absolute bg-ms-blue-20/10" :style="{ width: `${answer.total_answers * 100 / q.total_answers_count}%` }"></div>
                </div>
            </li>
        </ul>

        <p class="p-4 text-center">Scroll for more</p>

    </div>
    <div v-else class="bg-white w-full flex justify-center items-center">
        <div class="w-1/2">
            <Loader type="table" />
        </div>
    </div>
    </transition>
    </div>
</template>

<script>
import Loader from '@/Components/ContentLoader.vue'

export default{
    components: { Loader },
    props: ['fullUnixRange','brand','country','className'],
    data(){
        return{
            loaded: false,
            questions: [],
            stats: []
        }
    },
    computed: {
    },
    methods: { 
        nFormatter(num, digits) {
            const lookup = [
                { value: 1, symbol: "" },
                { value: 1e3, symbol: "k" },
                { value: 1e6, symbol: "M" },
                { value: 1e9, symbol: "G" },
                { value: 1e12, symbol: "T" },
                { value: 1e15, symbol: "P" },
                { value: 1e18, symbol: "E" }
            ];
            const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
            var item = lookup.slice().reverse().find(function(item) {
                return num >= item.value;
            });
            return item ? (num / item.value).toFixed(digits).replace(rx, "$1") + item.symbol : "0";
        },  
    },
    mounted(){
        axios.post('/', { grid: 'quiz', period: this.fullUnixRange, filter: { brand: this.brand.id, country: this.country } }).then(r => {
            this.questions = r.data.questionsRes
            this.stats.users = r.data.users
            this.loaded = true
        })
    }
}
</script>
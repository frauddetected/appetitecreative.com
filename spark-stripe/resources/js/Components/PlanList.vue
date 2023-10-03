<template>
    <div class="space-y-6">
        <div class="bg-white sm:rounded-lg shadow-sm overflow-hidden" v-for="plan in plans">
            <plan :plan="plan" :seat-name="seatName" />

            <div class="px-6 py-4 bg-gray-100 bg-opacity-50 border-t border-gray-100 text-right">
                
                <a class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 bg-gray-800" v-if="plan.name != 'Bronze'" :href="'/contact?plan_type=' + encodeBase64(plan.name)">{{__('Contact Us')}}</a>
                
                <spark-button @click.native="$emit('plan-selected', plan)" v-else-if="! currentPlan || (currentPlan && currentPlan.id != plan.id)">
                    {{__('Subscribe')}}
                </spark-button>

                <div class="flex justify-end items-center" v-if="currentPlan && currentPlan.id == plan.id">
                    <div class="ml-1 text-sm text-gray-400">{{__('Currently Subscribed')}}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FormatsValues from './../Mixins/FormatsValues';
    import Plan from './../Components/Plan';
    import SparkButton from './../Components/Button';

    export default {
        mixins: [FormatsValues],

        components: {
            Plan,
            SparkButton,
        },
        methods: {
            encodeBase64(value) {
                return btoa(value); // Encode the value as Base64
            },
        },
        props: ['plans', 'interval', 'currentPlan', 'seatName'],
    }
</script>
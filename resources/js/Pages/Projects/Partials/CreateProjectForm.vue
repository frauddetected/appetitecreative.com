<template>
    <jet-form-section @submitted="createTeam">
        <template #title>
            Project Details
        </template>

        <template #description>
            Create a new project.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Project Owner" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">

                    <div class="ml-4 leading-tight">
                        <div>{{ $page.props.user.name }}</div>
                        <div class="text-ms-gray-100 text-sm">{{ $page.props.user.email }}</div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Project Name" />
                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            
        </template>

        <template #actions>
            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Create
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetButton from '@/Jetstream/Button.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'

    export default {
        components: {
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                })
            }
        },

        mounted(){

        },

        methods: {
            createTeam() {
                this.form.post(route('projects.store'), {
                    errorBag: 'createProject',
                    preserveScroll: true
                });
            },
        },
    }
</script>

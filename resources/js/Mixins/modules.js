export default{
    created(){

        this.$modules = {
            enable: (name) => {
                this.$inertia.post('/modules', { name: name, enable: true })
            },
            disable: (name) => {
                this.$inertia.post('/modules', { name: name, enable: false })
            }
        }

    }
}
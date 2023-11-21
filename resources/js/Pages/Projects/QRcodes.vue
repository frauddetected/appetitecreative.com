<template>
    <app-layout title="Project Settings">

        <template #header>
            <h2 class="text-lg text-ms-gray-160 leading-tight">
                Project <span class="text-ms-gray-50">|</span> <span class="text-gradient">QR Codes</span>
            </h2>
            <nav class="flex" v-if="$page.props.user.role.level <= 1 || $page.props.user.role.name == 'editor'">
                <button 
                    @click="handleAddNewCodeClick()" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> New
                </button>
                <button 
                    @click="handleAddNewBulkClick()" class="hover:bg-ms-gray-20 text-ms-gray-160 p-3 flex items-center">
                    <i class="ms-Icon ms-Icon--Add mr-2"></i> Bulk Generate
                </button>
            </nav>
        </template>
        <div>
            <div class="mx-auto py-10 px-4 md:px-6 xl:px-20 flex flex-wrap items-start">
                <div class="w-full md:w-2/3 lg:w-10/12 order-2 md:order-none pr-12">
                <div class="flex flex-wrap">
                    <div class="w-full md:w-4/12 lg:w-2/12 pr-8">
                        <h2 class="text-xl">QR Codes</h2>
                        
                        <ul class="mt-5">
                            <li>{{ codestats.total }} Total QRs</li>
                            <li v-if="codestats.unique" class="mt-4">{{ codestats.total - codestats.unique }} standard QRs</li>
                            <li v-if="codestats.unique">{{ codestats.unique }} unique QRs</li>
                            <li v-if="codestats.unique" class="mt-4">
                                {{ codestats.burned }} burned QRs
                                ({{  Math.round((codestats.burned / codestats.unique) * 100) }}%)
                            </li>
                        </ul>

                        <nav v-if="codestats.unique" class="flex gap-x-4 mt-4 pt-4 border-t text-xs">
                            <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/qrcodes/${project_id}/view?type=array`">API</a>
                            <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/qrcodes/${project_id}/view?type=csv`">CSV</a>
                            <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/qrcodes/${project_id}/view?type=txt`">Text</a>
                        </nav>
                        
                    </div>
                    <div class="w-full md:w-8/12 lg:w-10/12 overflow-auto">
                        <table class="table">
                            <tr class="header">
                                <th>Title</th>
                                <th class="text-center">Keyword</th>
                                <th class="text-center">Scans</th>
                                <th v-if="codestats.unique" class="text-center">Total / Unique</th>
                                <th v-if="codestats.unique" class="text-center">Burned</th>
                                <th class="w-8"></th>
                                <th class="w-8"></th>
                            </tr>
                            <tr v-for="code in codes.data">
                                <td>{{ code.title }}</td>
                                <td class="text-center">
                                    {{ code.is_unique ? '-' : code.keyword }}
                                </td>
                                <td class="text-center">{{ code.is_unique ? '-' : code.scans }}</td>
                                <td v-if="codestats.unique" class="text-center">{{ code.is_unique ? code.total.toLocaleString('en-US') : '-' }}</td>
                                <td v-if="codestats.unique" class="text-center">{{ code.is_unique ? code.burned : '-' }}</td>
                                <td v-if="!code.is_unique">
                                    <i @click="previewQRModal(code)" class="ms-Icon ms-Icon--QRCode cursor-pointer hover:text-ms-cyan-110"></i>
                                </td>
                                <td v-else class="text-xs">
                                    <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/qrcodes/${project_id}/view?type=array?filter=${code.title.replace(' ', '+')}`">API</a>
                                </td>
                                <td v-if="!code.is_unique">
                                    <VDropdown :autoHide="false" placement="right-start" v-if="$page.props.user.role.level <= 1">
                                        <span class=""><i class="ms-Icon ms-Icon--Dataflows cursor-pointer hover:text-ms-cyan-110"></i></span>
                                        <template #popper>
                                            <div class="w-[600px] pt-8">

                                                <div v-if="code.details.length" class="flex w-full mb-2" v-for="detail in code.details">
                                                    
                                                    <VueMultiselect
                                                        v-model="detail.param"
                                                        :options="project.qr_params"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        placeholder="Select One"
                                                        label="title"
                                                        track-by="id"
                                                        :taggable="true"
                                                        @tag="addTag"
                                                        class="w-full"
                                                    />
                                                    
                                                    <input type="text" class="w-full input mx-2" v-model="detail.value">

                                                    <div class="relative flex items-center justify-center">
                                                        <label class="custom-file-upload mr-2">
                                                            <input type="file" ref="file" @change="handleFileUpload($event, detail)" class="hidden" />
                                                            <i class="ms-Icon ms-Icon--CloudUpload text-2xl"></i>
                                                        </label>
                                                        <ColorPicker :color="detail.value" className="mr-l" v-model="detail.value" />
                                                    </div>

                                                </div>
                                                <div v-else>
                                                    <p>No params.</p>
                                                </div>

                                                <div class="flex justify-end mt-8">
                                                    <button class="absolute top-4 font-bold" v-close-popper>X</button>
                                                    <button class="border border-black px-8 py-1 hover:bg-gray-200 rounded-sm mr-2" v-if="!code.details" @click="code.details = [{param: '', value: ''}]">Add</button>
                                                    <button class="border border-black px-8 py-1 hover:bg-gray-200 rounded-sm mr-2" v-else @click="code.details.push({param: '', value: ''})">Add</button>
                                                    <button class="border border-black px-8 py-1 hover:bg-green-200 rounded-sm mr-auto" @click="saveDetails(code)">Save</button>
                                                </div>

                                            </div>
                                        </template>
                                    </VDropdown>
                                </td>
                                <td v-else class="text-xs">
                                    <a target="_blank" class="text-ms-magenta-10" :href="`https://query.appetite.link/api/qrcodes/${project_id}/view?type=csv?filter=${code.title.replace(' ', '+')}`">CSV</a>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                </div>

                <SubsetOptions />

            </div>
        </div>
    </app-layout>
            
            <!-- Add QR Code -->
            <jet-dialog-modal :show="previewQR" @close="previewQR=null">
                <template #title>
                    Preview QR Code
                </template>
                <template #content>
                    <div><input type="hidden" name="previewQrKeyWord" id="previewQrKeyWord" :value="`${previewQR}`" ></div>
                    <div class="flex flex-col items-center justify-center">
                        
                        <vue-qrcode class="w-56" :value="(previewQR.qrLink !== null) ? `${previewQR.qrLink}` : `${defaultQRUrl}${previewQR}`" tag="img" :options="{ width: 1024, margin: 0, color: { dark: darkColor, light: lightColor } }"></vue-qrcode>
                        <vue-qrcode @ready="onReady" class="hidden" :value="(previewQR.qrLink !== null) ? `${previewQR.qrLink}` : `${defaultQRUrl}${previewQR}`" tag="svg" :options="{ width: 1024, margin: 0, color: { dark: darkColor, light: lightColor } }"></vue-qrcode>
                        
                        <a
                            v-if="!isEditing && !previewQR.qrLink"
                            :href="`${defaultQRUrl}${previewQR.keyword}`"
                            class="mt-2 hover:bg-ms-gray-20 border px-4 py-2 border-black text-xs"
                            target="_blank"
                        >
                            {{ `${defaultQRUrl}${previewQR.keyword}` }}
                        </a>

                        <a
                            v-if="!isEditing && previewQR.qrLink"
                            :href="(previewQR.qrLink !== null) ? `${previewQR.qrLink}` : `${defaultQRUrl}${previewQR.keyword}`"
                            class="mt-2 hover:bg-ms-gray-20 border px-4 py-2 border-black text-xs"
                            target="_blank"
                        >
                            {{ previewQR.qrLink }}
                        </a>

                        <input
                            v-if="isEditing"
                            type="text"
                            name="qr_url"
                            class="mt-2 hover:bg-ms-gray-20 border px-4 py-2 border-black text-xs user-url"
                            :value="isEditing ? (updatedQrCode ? updatedQrCode : `${defaultQRUrl}${previewQR.keyword}`) : (previewQR.qrLink ? previewQR.qrLink : `${defaultQRUrl}${previewQR.keyword}`)"
                            id="editableUserUrl"
                            :readonly="!isEditing"
                            @input="updateUserUrl"
                        />
                        <span class="text-red-500">{{ qrCodeError }}</span>

                        <span v-if="!isEditing" class="edit-url-click cursor-pointer" @click="toggleEditing">Edit</span>
                        <button v-if="isEditing" class="edit-url-click" @click="saveChanges">Save</button>

                    </div>
                </template>
                <template #footer>
                    
                    <ColorPicker color="#000000" className="mr-2" v-model="darkColor" />
                    <ColorPicker color="#ffffff" className="mr-auto" v-model="lightColor" />
                    
                    <a id="download" :download="previewQR" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Download</a>
                    <button class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30" @click="close">Cancel</button>

                </template>
            </jet-dialog-modal>

            <!-- Add QR Code -->
            <jet-dialog-modal maxWidth="4xl" :show="addNewCode" @close="addNewCode=0">
                <template #title>
                    Add QR Code
                </template>

                <template #content>

                    <div class="flex">
                        <div class="flex flex-col w-full mr-2">
                            <label for="">Title</label>
                            <input v-model="form.title" type="text" class="input">
                            <span class="text-red-500">{{ titleError }}</span>
                        </div>

                       <div class="flex flex-col w-full" v-if="codes.length">
                            <label for="">Parent</label>
                            <VueMultiselect
                                v-model="form.parent_id"
                                :options="codes"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="title"
                                track-by="id"
                            />
                        </div>
                        <div v-if="$page.props.user.admin==1 || $page.props.user.role.name == 'editor'" class="ml-2 flex flex-col w-full">
                            <label for="">Keyword</label>
                            <input type="text" v-model="form.keyword" class="input">
                            <p class="text-xs text-right mt-1 text-ms-gray-90">Leave empty if no need</p>
                        </div>
                    </div>

                    <div class="flex mt-4">
                        <div class="flex flex-col w-full mr-2">
                            <label for="">Source</label>
                            <VueMultiselect
                                v-model="form.source"
                                :options="project.sources"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="title"
                                track-by="id"
                            />
                            <span class="text-red-500">{{ sourceError }}</span>
                        </div>
                        <div class="flex flex-col w-full mr-2" v-if="countries">
                            <label for="">Country</label>
                            <VueMultiselect
                                v-model="form.country"
                                :options="countries"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="name"
                                track-by="code"
                            />
                        </div>

                        <div class="flex flex-col w-full" v-if="languages">
                            <label for="">Language</label>
                            <VueMultiselect
                                v-model="form.language"
                                :options="languages"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="name"
                                track-by="code"
                            />
                        </div>
                    </div>
                    
                </template>

                <template #footer>
                    <div v-if="project.sources.length">
                        <button @click="save" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                        <button @click="close" class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                    </div>
                    <div v-else>
                        Add Sources Before Save
                    </div>
                </template>
            </jet-dialog-modal>

            <!-- Generate Bulk QR Codes -->
            <jet-dialog-modal maxWidth="4xl" :show="addNewBulk" @close="addNewBulk=0">
                <template #title>
                    Generate Bulk QR Codes
                </template>

                <template #content>

                    <div class="flex gap-x-2">
                        <div class="flex flex-col w-full">
                            <label for="">Title</label>
                            <input v-model="formBulk.title" type="text" class="input">
                            <span class="text-red-500">{{ titleBulkError }}</span>
                        </div>

                        <div v-if="$page.props.user.admin==1 || $page.props.user.role.name == 'editor'" class="flex flex-col w-full">
                            <label for="">Quantity</label>
                            <input type="text" v-model="formBulk.quantity" class="input">
                        </div>

                        <div class="flex flex-col w-full">
                            <label for="">Single Usage (Burn)</label>
                            <select v-model="formBulk.unique" class="input">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex mt-4">
                        <div class="flex flex-col w-full mr-2">
                            <label for="">Source</label>
                            <VueMultiselect
                                v-model="formBulk.source"
                                :options="project.sources"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="title"
                                track-by="id"
                            />
                            <span class="text-red-500">{{ sourceBulkError }}</span>
                        </div>
                        <div class="flex flex-col w-full mr-2" v-if="countries">
                            <label for="">Country</label>
                            <VueMultiselect
                                v-model="formBulk.country"
                                :options="countries"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="name"
                                track-by="code"
                            />
                        </div>

                        <div class="flex flex-col w-full" v-if="languages">
                            <label for="">Language</label>
                            <VueMultiselect
                                v-model="formBulk.language"
                                :options="languages"
                                :multiple="false"
                                :close-on-select="true"
                                placeholder="Select One"
                                label="name"
                                track-by="code"
                            />
                        </div>
                    </div>
                    
                </template>

                <template #footer>
                    <div v-if="project.sources.length">
                        <button v-if="!savingBulk" @click="saveBulk" class="text-white py-2 px-4 mr-2 font-semibold hover:bg-ms-cyan-120 bg-ms-cyan-110">Save</button>
                        <button v-else class="text-white py-2 px-4 mr-2 font-semibold cursor-wait bg-ms-magenta-110">Generating...</button>
                        <button  @click="close" class="py-2 px-4 font-semibold border border-ms-gray-160 text-ms-gray-160 hover:bg-ms-gray-30">Cancel</button>
                    </div>
                    <div v-else>
                        Add Sources Before Save
                    </div>
                </template>
                
            </jet-dialog-modal>

</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubsetOptions from '@/Pages/Projects/Partials/SubsetOptions.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import VueMultiselect from 'vue-multiselect'
    import VueQrcode from '@chenfengyuan/vue-qrcode'
    import ColorPicker from '@/Components/ColorPicker.vue'
    import $ from 'jquery';
    export default {
        
        props: [
            'codes',
            'project',
            'codestats',
            'project_id',
            'qrCodePermission',
        ],

        data(){
            return{
                isEditing: false,
                defaultQRUrl: `https://go.appetite.link/`,
                darkColor: '',
                lightColor: '',
                addNewCode: false,
                addNewBulk: false,
                addNewParam: false,
                previewQR: null,
                qrLink: null,
                savingBulk: false,
                valid: true,
                titleError: '',
                sourceError: '',
                titleBulkError: '',
                sourceBulkError: '',
                qrCodeError: '',
                updatedQrCode: null,
                form: this.$inertia.form({
                    title: '',
                    parent_id: '',
                    source: '',
                    keyword: '',
                    language: '',
                    country: '',
                }),
                formBulk: this.$inertia.form({
                    title: '',
                    unique: 1,
                    quantity: 10_000,
                    source: '',
                    language: '',
                    country: '',
                }),
                languages: [],
                countries: [],
            }
        },

        created(){
            this.languages = this.project.i18n && this.project.i18n.languages
            this.countries = this.project.i18n && this.project.i18n.countries
            // console.log('Codes Data:', this.codes.data);
        },

        mounted(){

            if(this.project_id == 37){
                this.defaultQRUrl = 'https://go.happydaywinterdrinks.com/';
            }

            if(this.project.qr){
                this.project.qr.forEach(item => {
                    
                    let details = item.details;
                    item.details = [];
                    if(details){
                        Object.keys(details).forEach(k => {
                            let obj = this.project.qr_params.filter(item => item.title == k)[0];
                            item.details.push({ param: {
                                id: obj.id,
                                created_at: obj.created_at,
                                project_id: obj.project_id,
                                title: obj.title,
                                updated_at: obj.updated_at,
                            } , value: details[k] })
                        })
                    }

                })
            }
        },

        methods: {
            previewQRModal(code) {
                this.previewQR = { keyword: code.keyword, qrLink: code.qr_link };
                this.isEditing = false;
                this.qrCodeError = '';
            },
            toggleEditing() {
                this.isEditing = !this.isEditing;
            },
            updateUserUrl(event) {
                this.userUrl = event.target.value;
            },
            saveChanges() {
                function isValidURL(url) {
                    // Regular expression for a valid URL
                    var urlRegex = /^(ftp|http|https):\/\/[^ "]+$/;

                    // Test the given URL against the regular expression
                    return urlRegex.test(url);
                }

                const existingKey = `${this.previewQR.keyword}`;
                const newUrl = $('#editableUserUrl').val();

                if(isValidURL(newUrl)){
                    this.$inertia.post(route('projects.qr.updateQr'), {
                        _method: 'put', // Specify the HTTP method for the update request
                        qr_code_id: existingKey,
                        new_url: newUrl,
                    }, {
                        preserveState: true,
                        onSuccess: () => {
                            this.toggleEditing();
                            this.previewQR = false
                            this.codes = this.project.qr 
                        }
                    });
                }
                else{
                    this.qrCodeError = 'Please enter valid url.';
                    this.isEditing = true;
                    this.updatedQrCode = newUrl;
                }
            },
            handleAddNewCodeClick(){
                if(!this.qrCodePermission){
                    this.$inertia.post(route('projects.qr.limit'))
                }
                else{
                    this.addNewCode = true;
                    this.titleError = '';
                    this.sourceError = '';
                }
            },
            handleAddNewBulkClick(){
                this.addNewBulk = true
                this.sourceBulkError = ''
                this.titleBulkError = ''
            },
            handleFileUpload(event, model){

                let data = new FormData();
                let file = event.target.files[0];

                data.append('name', 'my-file')
                data.append('file', file)

                let config = { header: {
                    'Content-Type' : 'multipart/form-data'
                }}

                axios.post(route('projects.qr.details.upload'), data, config).then(r => {
                    model.value = r.data.location
                })

            },
            onReady(e){
                
                //get svg element.
                var svg = e;

                //get svg source.
                var serializer = new XMLSerializer();
                var source = serializer.serializeToString(svg);

                //add name spaces.
                if(!source.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)){
                    source = source.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
                }
                if(!source.match(/^<svg[^>]+"http\:\/\/www\.w3\.org\/1999\/xlink"/)){
                    source = source.replace(/^<svg/, '<svg xmlns:xlink="http://www.w3.org/1999/xlink"');
                }

                //add xml declaration
                source = '<?xml version="1.0" standalone="no"?>\r\n' + source;

                //convert svg source to URI data scheme.
                var url = "data:image/svg+xml;charset=utf-8,"+encodeURIComponent(source);

                //set url value to a element's href attribute.
                document.getElementById("download").href = url;
                //you can download svg file by right click menu.
                
            },
            save(){
                this.valid = true;
                if(this.form.title == ''){
                    this.valid = false;
                    this.titleError = 'Please enter title.'
                }
                if(this.form.source == ''){
                    this.valid = false;
                    this.sourceError = 'Please select source.'
                }
                if(this.valid){
                    this.form.post(route('projects.qr.store'), {
                        preserveState: true,
                        onSuccess: () => {
                            this.addNewCode = false
                            this.form.reset()
                            this.codes = this.project.qr 
                        }
                    })
                }
            },
            close(){
                this.addNewCode = false
                this.addNewBulk = false
                this.previewQR = null
            },
            saveBulk(){
                this.valid = true;
                if(this.formBulk.title == ''){
                    this.valid = false;
                    this.titleBulkError = 'Please enter title.'
                }
                if(this.formBulk.source == ''){
                    this.valid = false;
                    this.sourceBulkError = 'Please select source.'
                }
                if(this.valid){
                    this.savingBulk = true
                    this.formBulk.post(route('projects.qr.bulkStore'), {
                        preserveState: true,
                        onSuccess: () => {
                            this.addNewBulk = false
                            this.formBulk.reset()
                            this.codes = this.project.qr
                            this.savingBulk = false
                        }
                    })
                }
            },
            addTag (newTag){                
                this.$inertia.post(route('projects.qr.details.add'), { value: newTag })
            },
            saveDetails(code){
                
                let obj = {}
                code.details.forEach(item => {
                    if(item.param) 
                        obj[item.param.title] = item.value;
                });

                this.$inertia.post(route('projects.qr.details.save'), {
                    code: code.id,
                    params: obj
                })

            },
            testcolor(c){
                console.log()
            }
        },

        components: {
            AppLayout,
            SubsetOptions,
            JetDialogModal,
            VueMultiselect,
            VueQrcode,
            ColorPicker
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
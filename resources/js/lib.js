import axios from 'axios';
import UAParser from 'ua-parser-js';

window.$link = class AppetiteLink
{
    static get instance() 
    {
        return axios.create({
            baseURL: 'https://query.appetite.link/api/',
            //baseURL: 'http://query.appetitelink:8000/api/',
            headers: {'Authorization': 'Bearer ' + this.token},
        });
    }

    static get API_DEFAULT_PARAMS(){
        return {
            project_id: this.project_id,
            source_id: this.source_id,
            source_value: this.source_value,
            source_campaign: this.source_campaign,
            sessid: this.sessid,
            user: this.user,
            language: this.language,
            user_agent: {
                browser: this.$.userAgent().browser,
                os: this.$.userAgent().os,
                device: this.$.userAgent().device
            }
        }
    }

    static thisUser = localStorage.getItem('linkUser') ? JSON.parse(localStorage.getItem('linkUser')) : {}
    static set user(value){
        Object.assign(this.thisUser, value);
        //this.thisUser = value       
    }
    static get user(){
        localStorage.setItem('linkUser', JSON.stringify(this.thisUser))
        return this.thisUser
    }

    static thisSelection = localStorage.getItem('linkSelection') ? JSON.parse(localStorage.getItem('linkSelection')) : {}
    static set selection(value){
        Object.assign(this.thisSelection, value);
        //this.thisSelection = value
    }
    static get selection(){
        localStorage.setItem('linkSelection', JSON.stringify(this.thisSelection))
        return this.thisSelection
    }

    static thisDetails = localStorage.getItem('linkDetails') ? JSON.parse(localStorage.getItem('linkDetails')) : {}
    static set selection(value){
        Object.assign(this.thisDetails, value);
        //this.thisDetails = value       
    }
    static get selection(){
        localStorage.setItem('linkDetails', JSON.stringify(this.thisDetails))
        return this.thisDetails
    }

    static set project_id(value){
        localStorage.setItem('linkProjectId', value);
    }

    static get project_id(){
        return localStorage.getItem('linkProjectId');
    }

    static set language(value){
        localStorage.setItem('linkLanguage', value);
    }

    static get language(){
        return localStorage.getItem('linkLanguage');
    }

    static set source_id(value){
        localStorage.setItem('linkSourceId', value);
    }

    static get source_id(){
        return localStorage.getItem('linkSourceId');
    }

    static set source_value(value){
        localStorage.setItem('linkSourceValue', value);
    }

    static get source_value(){
        return localStorage.getItem('linkSourceValue');
    }

    static set source_campaign(value){
        localStorage.setItem('linkSourceCampaign', value);
    }

    static get source_campaign(){
        return localStorage.getItem('linkSourceCampaign');
    }

    static set qr(value){
        localStorage.setItem('linkQr', JSON.stringify(value));
    }

    static get qr(){
        return JSON.parse(localStorage.getItem('linkQr'));
    }

    static set token(value){
        localStorage.setItem('linkBearerToken', value);
    }

    static get token(){
        return localStorage.getItem('linkBearerToken');
    }
    
    static get sessid(){
        if(localStorage.getItem('linkSessionId')){
            return localStorage.getItem('linkSessionId');
        } else {
            let session_id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            localStorage.setItem('linkSessionId', session_id);
            return session_id;
        }
    }

    /* * */

    static get(url, params = {}) {
        return this.instance.post(url, {
            params: {
                ...params,
                ...this.API_DEFAULT_PARAMS
            }
        });
    }

    static put(url, params = {}) {
        return this.instance.put(url, {
            ...params,
            ...this.API_DEFAULT_PARAMS
        });
    }

    static post(url, params = {}) {
        return this.instance.post(url, {
            ...params,
            ...this.API_DEFAULT_PARAMS
        });
    }

    /* * */

    static get $(){
        return{
            async playAudio(url){
                const context = new AudioContext();
                const source = context.createBufferSource();
                const audioBuffer = await fetch(url)
                    .then(res => res.arrayBuffer())
                    .then(ArrayBuffer => context.decodeAudioData(ArrayBuffer));

                source.buffer = audioBuffer;
                source.connect(context.destination);
                source.start();
            },
            getInput(parameterName)
            {
                if(this.getParam(parameterName)){
                    return this.getParam(parameterName);
                }
                if(AppetiteLink.hasOwnProperty(parameterName)){
                    return AppetiteLink[parameterName];
                }
                return null;
            },
            getParam(parameterName) 
            {
                var result = null,
                tmp = [];
                var items = location.search.substr(1).split("&");
                for (var index = 0; index < items.length; index++) {
                    tmp = items[index].split("=");
                    if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
                }
                return result;
            },
            randomIntBetween(min, max){
                return Math.floor(Math.random() * (max - min)) + min;
            },
            randomStr(length){
                var result           = '';
                var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;
                for ( var i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                return result;
            },
            randomEmail()
            {
                return this.randomStr(12) + '@appetitemail.com';
            },
            removeURLParameter(url = window.location.href, parameter = 'ucode') {
                var urlparts = url.split('?');   
                if (urlparts.length >= 2) {
                    var prefix = encodeURIComponent(parameter) + '=';
                    var pars = urlparts[1].split(/[&;]/g);
                    for (var i = pars.length; i-- > 0;) {    
                        if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                            pars.splice(i, 1);
                        }
                    }
                    return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
                }
                return url;
            },
            browserLanguage(validLanguages = []) 
            {
                let defaultLanguage = 'en';

                let userLang = navigator.language || navigator.userLanguage; 
                userLang = userLang.substring(0,2);

                // force a language setting
                if(this.getParam('lang')) {
                    return this.getParam('lang');
                }

                // array passed?
                if(validLanguages.length) {
                    if(validLanguages.includes(userLang)){
                        return userLang;
                    } else {
                        return defaultLanguage;
                    }
                }
                
                return userLang;
            },
            userAgent()
            {
                var parser = new UAParser();
                return parser.getResult();
            },
            async geoIp()
            {
                return new Promise(resolve => {

                    if(!localStorage.getItem('linkApiGeoIp2')){
                        let geo = AppetiteLink.post('/geo/ip');
                        geo.then(r => {
                            localStorage.setItem('linkApiGeoIp2', JSON.stringify(r.data));
                            resolve(r.data);
                        });
                    } else {
                        let data = JSON.parse(localStorage.getItem('linkApiGeoIp2'));
                        resolve(data);
                    }

                })

            },
            geoBrowser()
            {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        return position;
                    });
                } else { 
                    return "Geolocation is not supported by this browser.";
                }
            }
        }
    }

    /* * */

    static fakeName()
    {
        if(this.user && this.user.name){
            return this.user.name;
        }

	    var name1 = ["John","Joanna","David","Cathy","Andrew","Robert","James","Daniel","Beth","Maria","Dan","Charlotte","Nikolas","Olivia","Emma","Oliver","Noah"];
	    var name2 = ["Smith","Jones","Williams","Taylor","Davies","Brown","Wilson","Evans","Stone","Wood","Perry","Bridge","Greenwood","Hill","Brooks"];
        
        var name = name1[this.$.randomIntBetween(0, name1.length)] + ' ' + name2[this.$.randomIntBetween(0, name2.length)];
	    
        return name;
    }

    static fakeEmail()
    {
        if(this.user && this.user.email){
            return this.user.email;
        }

        return this.$.randomEmail();
    }

    /* * */

    static log(name, values = {}, params = {}) {
        return this.post('log/action', {
            name: name,
            values: values,
            ...params
        })
    }

    static get type()
    {
        if(this.$.getParam('ucode') || localStorage.getItem('linkQrCode_Ucode'))
        {    
            return 'qrcode'
        }

        if(this.$.getParam('utm_source'))
        {
            return 'utm_source'
        }

        return 'direct'
    }

    static async init(token = '', project_id = '')
    {
        if(token) this.token = token;
        if(project_id) this.project_id = project_id;

        if(this.type == 'qrcode')
        {    
            await this.qrCode();

            const state = { 'value': true }
            const title = document.title
            const url = this.$.removeURLParameter();
            history.pushState(state, title, url)

            this.source_id = this.qr.source_id
            this.source_value = this.qr.keyword

            return {
                type: 'qrcode',
                data: this.qr
            }
        }

        const social_media = ['youtube','facebook','twitter','linkedin','pinterest','tiktok','snap','vk','instagram'];
        let sm = social_media.filter((str) => document.referrer.includes(str))

        if(this.type == 'utm_source' || sm.length)
        {
            this.source_id = 2
            this.source_value = this.$.getParam('utm_source') ? this.$.getParam('utm_source') : (sm.length ? sm[0] : '');
            this.source_campaign = this.$.getParam('utm_campaign') ? this.$.getParam('utm_campaign') : null;

            if(!localStorage.getItem('source_track')){
                this.post('source/track');
                localStorage.setItem('source_track', true);
            }

            return {
                type: this.type
            }
        }

        this.source_id = 4

        if(!localStorage.getItem('source_track')){
            this.post('source/track');
            localStorage.setItem('source_track', true);
        }

        return {
            type: 'direct'
        }
    }

    static qrCode(code = '') 
    {
        return new Promise(resolve => {

            if(!code){
                code = this.$.getParam('ucode') ?? localStorage.getItem('linkQrCode_Ucode');
            }

            if(localStorage.getItem('linkQrCode_Ucode') && localStorage.getItem('linkQrCode_Ucode') == code){
                
                this.qr = JSON.parse(localStorage.getItem('linkQrCode'))
                resolve();

            } else {

                this.post('qr/details', { ucode: code }).then(r => {
                    localStorage.setItem('linkQrCode_Ucode', code);
                    localStorage.setItem('linkQrCode', JSON.stringify(r.data));
                    this.qr = r.data
                    resolve();
                })
            }

        })
    }

    static get selfie()
    {
        const self = this;
        return{
            submit(data, params = {}){
                return self.post('selfie/submit', { 
                    image: data,
                    ...params
                });
            }
        }
    }

    static get crm()
    {
        const self = this;
        return{
            contact(platform, params = {}){
                return self.post('crm/contact', {
                    crm: {
                        platform: platform,
                        ...params
                    }
                });
            }
        }
    }

    static get auth()
    {
        const self = this;
        return{
            
            me(){
                if(localStorage.getItem('authProfile')){
                    return JSON.parse(localStorage.getItem('authProfile'));
                }
                return null
            },

            resetPassword(email, mailconfig = {}){
                return self.post('auth/resetPassword', { email: email, ...mailconfig });
            },

            logout(redirect = false){
                localStorage.removeItem('authProfile');
                if(redirect){
                    window.location.href = redirect;
                }
            },

            loginOrNew(params = {}){

                self.user.email = params.email
                
                const login = self.post('auth/loginOrNew', {
                    ...params,
                    details: {...self.details,...params.details}
                })
                
                login.then(r => {
                    if(r.data.success){
                        localStorage.setItem('authProfile', JSON.stringify(r.data.profile));
                    }
                })

                return login;

            },

            register(params = {}){

                self.user.email = params.email
                
                return self.post('auth/register', {
                    ...params,
                    details: {...self.details,...params.details}
                })

            },

            update(params = {}){
                
                const update = self.post('auth/update', {
                    ...self.user,
                    details: {...params}
                })

                update.then(r => {
                    if(r.data.success){
                        localStorage.setItem('authProfile', JSON.stringify(r.data.profile));
                    }
                })
                
                return update

            },
            login(params = {}){

                self.user.email = params.email
                
                const login = self.post('auth/login', {
                    ...params,
                    details: {...self.details,...params.details}
                })
                
                login.then(r => {
                    if(r.data.success){
                        localStorage.setItem('authProfile', JSON.stringify(r.data.profile));
                    }
                })

                return login;

            },
            logout(){
                if(localStorage.getItem('authProfile')){
                    localStorage.setItem('authProfile', null);
                }
            },
        }
    }

    static get quiz()
    {
        const self = this;
        return{
            log(params = {}){
                return self.post('quiz/log', {
                    ...self.user,
                    ...params,
                    details: {...self.details,...params.details}
                })
            },
            next(params = {}){
                return self.post('quiz/next', {
                    ...self.user,
                    ...params,
                    details: {...self.details,...params.details}
                })
            },
            random(params = {}){
                return self.post('quiz/random', {
                    ...self.user,
                    ...params,
                    details: {...self.details,...params.details}
                })
            },
            all(params = {}){
                return self.post('quiz/all', {
                    ...self.user,
                    ...params,
                    details: {...self.details,...params.details}
                })
            },
            answered(arr = []){
                if(arr.length){
                    let unique = arr.filter((v, i, a) => a.indexOf(v) === i);
                    localStorage.setItem('linkQuizAnswered', JSON.stringify(unique));
                    return arr
                }
                let existent = localStorage.getItem('linkQuizAnswered') ? JSON.parse(localStorage.getItem('linkQuizAnswered')) : [];
                return existent
            }
        }
    }

    static get message()
    {
        const self = this;
        return{
            listen(param){
                window.addEventListener('message', event => {
                    if(event.data.link){
                        param(event.data.link);
                    }
                }, { passive: true });
            },
            send(params){
                if(window.frameElement){
                    window.parent.postMessage({
                        link: params
                    });
                } else {
                    window.postMessage({
                        link: params
                    });
                }
            }
        }
    }

    static get leaderboard()
    {
        const self = this;
        const randomNumber = Math.floor(100000000 + Math.random() * 900000000);

        return{
            get(params = {}){
                return self.post('leaderboard/get', {
                    ...self.user,
                    ...params,
                    details: {...self.details,...params.details}
                })
            },
            submit(params = {}){
                return self.post('leaderboard/submit', {
                    ts: randomNumber * (Date.now() / 1000),
                    number: randomNumber,
                    ...self.user,
                    ...params,
                    details: {...self.details,...params.details}
                })
            }
        }  
    }
}
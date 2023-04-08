<template>
    <div class="speech_recongition_block">
        <span :class="['action_btn',{active:srf_start}]" @click="action_submit">
            <i v-if="srf_start" class="fa fa-microphone-alt"></i>
            <i v-else class="fa fa-microphone"></i>
        </span>
    </div>
</template>
<script>
export default {
    name: 'SpeechRecognitionBlock',
    props: ['config'],
    data(){
        return {
            recognition: {},
            sel_lang: this.config.lang?this.config.lang:'en-US',
            recognition_continous: false,
            getTheTotalSpeechStr: '',
            confidenceLvl: this.config.confidence_level?this.config.confidence_level:'',
            result: '',
            action: this.config.action,
            srf_start: false
        }
    },
    watch: {
        result: function(val){
            if(val){
                this.$parent.srch_keyword = val
                if(this.action) this.action();
            }
        }
    },
    created(){
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if(!SpeechRecognition) return false

        this.recognition = new SpeechRecognition();

        // console.log(this.recognition);

        this.recognition.lang = this.sel_lang;
        this.recognition.continuous = this.recognition_continous;
        this.recognition.interimResults = true;
        this.recognition.maxAlternatives = 1;

        let elm = this

        this.recognition.onstart = function() {            
            console.log('Voice Is Activated, You Can Speak');
        };

        this.recognition.onspeechend = function() {
            elm.action_submit();
            console.log('Voice Is Deactivated, You Can\'t Speak now');                        
        }
        
        this.recognition.onresult = function(event) {
            console.log(event)
            elm.$parent.srch_keyword = ''
            for (let i = event.resultIndex; i < event.results.length; i++) {

                const {confidence, transcript} = event.results[i][0];
                // console.log(confidence, transcript);
                
                if(elm.confidence_level && confidence > elm.confidenceLvl){
                    // console.log(`${transcript}`)
                    elm.result += transcript;
                }
                else if(event.results[i].isFinal) elm.result += transcript;
                // else elm.result += transcript;
                
                elm.$parent.srch_keyword += transcript
            }
            
            if(elm.result=='') console.log('Not recognized. Try again')
            else console.log(`Get Result: ${elm.result}`)            
        };
    },
    methods: {        
        async action_submit(){            
            // this.recognition.addEventListener("onstart", this.onstart);
            if(this.srf_start){                
                this.srf_start = false;
                this.$parent.listening = false;                
                this.recognition.stop();                
                // console.log('Voice Is Dectivated, You Can\'t Speak');
            }else{
                this.result = ''
                this.srf_start = true;
                this.$parent.listening = true;
                this.recognition.start();
                // console.log('Voice Is Activated, You Can Speak');
            }
        }
    }
}
</script>
<style scoped>
    .speech_recongition_block{
        display: inline-block;
    }
    .action_btn{
        display: inline-block;        
    }
    .action_btn.active{
        background-color: #CD0000;
        color: #fff;
        animation: pulse 1.25s infinite cubic-bezier(0.90, 0, 0, 0.96);
    }
    @keyframes pulse {
        to {
            box-shadow: 0 0 0 30px rgba(243, 105, 105, 0.07);
        }
    }
</style>
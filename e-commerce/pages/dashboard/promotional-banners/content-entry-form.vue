<template>
    <div class="content_entry_form_block">
        <RainbowLoader v-if="req_submit" />
        <div class="row">
            <div class="col-md-8">
                <div v-if="form_loader" class="row">
                    <div class="col-md-12">
                        <div class="form_block">
                            <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                        </div>
                    </div>
                </div>
                <div v-else class="form_block">
                    <div class="mb-4">
                        <label>Banner Title</label>
                        <div><input type="text" placeholder="Enter banner title" class="form-control" v-model="formData.banner_title" ref="banner_title" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Promotional Link</label>
                        <div><input type="text" placeholder="Enter promotional link" class="form-control" v-model="formData.promotional_link" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Choose Desktop Banner</label>
                        <DesktopBannerInputBlock />
                    </div>
                    <div class="mb-4">
                        <label>Choose Mobile Banner</label>
                        <MobileBannerInputBlock />
                    </div>                    
                </div>
            </div>
            <div class="col-md-4">
                <div v-if="form_loader" class="row">
                    <div class="col-md-12">
                        <div class="info_block">
                            <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                        </div>
                    </div>
                </div>
                <div v-else class="info_block">                    
                    <div class="item_block mb-4">
                        <label>Display Type</label>
                        <select v-model="formData.display_type" class="form-control">
                            <option v-for="(type,index) in $store.state.banner_display_types" :key="index" :value="index">{{ type }}</option>
                        </select>
                    </div>
                    <div class="item_block mb-4">
                        <label>Schedule Type</label>
                        <select v-model="formData.schedule_type" class="form-control">
                            <option v-for="(type,index) in $store.state.banner_schedule_types" :key="index" :value="index">{{ type }}</option>
                        </select>
                    </div>
                    <template v-if="formData.schedule_type=='1'">
                        <div class="item_block mb-4">
                            <label>Choose start date time</label>
                            <datetime type="datetime" input-class="datetime_input" v-model="formData.start_time" zone="Asia/Dhaka" />
                        </div>
                        <div class="item_block mb-4">
                            <label>Choose end date time</label>
                            <datetime type="datetime" input-class="datetime_input" v-model="formData.end_time" zone="Asia/Dhaka"></datetime>
                        </div>
                    </template>
                    <div class="item_block">
                        <label>Status</label>
                        <SwithcBtn :status="formData.status" :index="'status'" />
                        <input type="hidden" v-model="formData.status" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import DesktopBannerInputBlock from './components/desktop_banner_input_block'
import MobileBannerInputBlock from './components/mobile_banner_input_block'
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {
        DesktopBannerInputBlock,
        MobileBannerInputBlock,
        SwithcBtn
    },    
    data(){
        return {
            category_list: [],
            form_loader: false,
            formData: {
                banner_title: '',                
                promotional_link: '',
                display_type: 1,
                schedule_type: 0,
                start_time: '',
                end_time: '',
                desktop_banner_image: null,
                exist_desktop_banner_image: null,
                mobile_banner_image: null,
                exist_mobile_banner_image: null,
                status: true
            },
            req_submit: false,
            form_action: 'save'
        }
    },
    computed: {
        form_submit_status () {
            return this.$store.state.form_submit_status        
        }
    },
    watch: {
        edit_content_id (val) {
            this.load_req_data(val);
        },
        form_submit_status (status) {
            if(status) this.formSubmit();        
        }
    },
    mounted(){
        if(this.edit_content_id) this.load_req_data(this.edit_content_id);
        if(this.$store.state.form_submit_status) this.formSubmit();

        // load categories
        // this.load_categories();
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },
        slug_config(){            
            this.formData.slug = this.$strSlug(this.formData.type_title)
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/promotional-banners/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data.data;

                this.formData = {
                    banner_title: getData.banner_title,                
                    promotional_link: getData.promotional_link,
                    display_type: getData.display_type,
                    schedule_type: getData.schedule_type,
                    start_time: getData.state,
                    end_time: getData.end_time,
                    desktop_banner_image: getData.desktop_banner_image,
                    exist_desktop_banner_image: getData.exist_desktop_banner_image,
                    mobile_banner_image: getData.mobile_banner_image,
                    exist_mobile_banner_image: getData.exist_mobile_banner_image,
                    status: getData.status
                }

                this.form_loader = false;
                this.form_action = 'update';
            }).catch(e => {
                console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.form_loader = false;
            });
        },
        async formSubmit(){
            this.$parent.form_submit_state(false)

            if(this.formData.banner_title.trim()==''){
                this.$toast.error('Please enter banner title', {icon: "Warning"});
                this.$refs.type_title.focus();
                return false;
            } else if(this.formData.desktop_banner_image==''){
                this.$toast.error('Please choose banner for desktop', {icon: "Warning"});
                return false;
            } else if(this.formData.mobile_banner_image==''){
                this.$toast.error('Please choose banner for mobile', {icon: "Warning"});
                return false;
            } else if(this.formData.schedule_type==1 && this.formData.start_time==''){
                this.$toast.error('Please choose start date time', {icon: "Warning"});
                return false;
            } else if(this.formData.schedule_type==1 && this.formData.end_time==''){
                this.$toast.error('Please choose end date time', {icon: "Warning"});
                return false;
            }

            if(confirm('Are you sure to submit it?')){
                // setup submitted data
                let submit_data = {
                    id: this.$parent.user_id,
                    access_token: this.$parent.user_access_token,
                    data: this.formData,
                    action: this.form_action,
                    edit_id: this.edit_content_id
                }

                // call for submit
                this.req_submit = true;
                
                await this.$parent.promotionalBannerSubmit(submit_data);
                
                this.req_submit = false;
                this.$parent.load_data();                
                
                await this.$swal("Good job!", "Data has been "+ (this.form_action == 'save'?'inserted':'updated') +" successfully.", "success");

                if(this.form_action == 'save'){
                    this.form_reset();
                    this.$parent.add_new_entity(false)
                }
            }            
        },
        form_reset(){
            this.formData = {
                banner_title: '',                
                promotional_link: '',
                display_type: 1,
                schedule_type: 0,
                start_time: '',
                end_time: '',
                desktop_banner_image: '',
                exist_desktop_banner_image: '',
                mobile_banner_image: '',
                exist_mobile_banner_image: '',
                status: true
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    .form_block,.info_block{
        display: block;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 20px;
    }
    .info_block .parent-dropdown{
        margin: 0; padding: 0;
        :deep(option){
            padding: 10px
        }
        :deep(.datetime_input){
            display: block;
            width: 100%;
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
    }
</style>
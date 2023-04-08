<template>
    <div class="content_entry_form_block">
        <RainbowLoader v-if="req_submit" />
        <div v-if="form_loader" class="row">
            <div class="col-md-12">
                <div class="form_block">
                    <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                    <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                    <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                </div>
            </div>
        </div>
        <template v-else>
            <!-- <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name</label>
                    <DataInfoForm :column="'full_name'" :placeholder="'Enter full name'" :type="'text'" />
                </div>
                <div class="col-md-6 mb-3">
                    <label>Contact Number</label>
                    <DataInfoForm :column="'contact_no'" :placeholder="'Enter contact number'" :type="'text'" />
                </div>
            </div> -->
            <div class="row">                
                <div class="col-md-7 mb-3">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Region</label>
                            <DataInfoForm :column="'region_id'" :type="'dropdown'" :data="regions" :vf="'id'" :tf="'title'" :onchange="'load_cities'"  />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>City</label>
                            <DataInfoForm :column="'city_id'" :type="'dropdown'" :data="cities" :vf="'id'" :tf="'title'" :onchange="'load_areas'" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Area</label>
                            <DataInfoForm :column="'area_id'" :type="'dropdown'" :data="areas" :vf="'id'" :tf="'title'" />
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label>Address</label>
                            <DataInfoForm :column="'address'" :type="'textarea'" :placeholder="'Enter the house no, road etc'" />
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Status</label>
                            <SwithcBtn :status="formData.status" :index="'status'" />
                            <input type="hidden" v-model="formData.status" />
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <!-- <label>Pick Current Location</label> -->
                    <div class="lat_lng_block">
                        <div class="row">
                            <!-- <div class="col-md-1">
                                <label>Pick</label>
                                <span class="pick_location_btn" title="Pick current location"><i class="fa fa-street-view"></i></span>
                            </div> -->
                            <div class="col-6 col-md-5 mb-3">
                                <label>Latitude</label>
                                <DataInfoForm :column="'latitude'" :isize="'full'" :placeholder="'0.000'" :type="'text'" />
                            </div>
                            <div class="col-6 col-md-5 mb-3">
                                <label>Longitude</label>
                                <DataInfoForm :column="'longitude'" :isize="'full'" :placeholder="'0.000'" :type="'text'" />
                            </div>                            
                        </div>

                        <LeafLetMap :height="'30vh'" :lat="formData.latitude" :lng="formData.longitude" :zoom="17" :markerPosChange="true" />
                    </div>
                </div>
                <!-- <div class="col-md-6 mb-3">
                    <label>Select a label for effective delivery</label>
                    <DataInfoForm :column="'label_id'" :type="'radio'" :data="$store.state.shipping_addr_labels" />
                </div> -->                
            </div>
        </template>
    </div>
</template>
<script>
import LeafLetMap from '@/components/map/LeafLet'
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
import DataInfoForm from '../components/DataInfoForm'
export default {
    name: 'ContentEntryFormBlock',
    props: {
        edit_content_id: Number
    },    
    components: {
        LeafLetMap,
        SwithcBtn,
        DataInfoForm
    },
    data(){
        return {
            form_loader: false,
            edit_content_load: false,
            regions: [],
            cities: [],
            areas: [],
            req_submit: false,
            formData: {
                full_name: '',
                contact_no: '',
                region_id: '',
                city_id: '',
                area_id: '',
                latitude: '',
                longitude: '',
                address: '',
                label_id: '',
                status: 1
            },
            edit_status: {
                full_name: true,
                contact_no: true,
                region_id: true,
                city_id: true,
                area_id: true,
                latitude: true,
                longitude: true,
                address: true,
                label_id: true
            }
        }
    },
    watch: {
        edit_content_id (val) {
            if(val) this.load_req_data(val);
        }
    },
    computed: {
        form_submit_allow: function(){
            for(var index in this.edit_status){
                if(this.edit_status[index]) return true
            }
        }
    },
    async mounted(){
        
        // load region list
        await this.load_regions();

        if(this.edit_content_id) this.load_req_data(this.edit_content_id);

        this.formData.latitude = this.$store.state.cur_lat
        this.formData.longitude = this.$store.state.cur_lng
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },
        newLatLngSetup: function(latlng){
            // console.log(latlng)
            this.formData.latitude = latlng.lat
            this.formData.longitude = latlng.lng
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/manage-address/edit/' + id, this.$parent.header_config).then( async (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                this.formData = {
                    full_name: getData.full_name,
                    contact_no: getData.contact_no,
                    region_id: getData.region_id,
                    city_id: getData.city_id,
                    area_id: getData.area_id,
                    latitude: getData.latitude?getData.latitude:this.$store.state.cur_lat,
                    longitude: getData.longitude?getData.longitude:this.$store.state.cur_lng,
                    address: getData.address,
                    label_id: getData.label_id,                    
                    status: getData.status
                }

                this.edit_content_load = true

                await this.load_cities();
                await this.load_areas();

                this.edit_content_load = false

                this.form_loader = false;
                this.form_action = 'update';
            }).catch(e => {
                console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.form_loader = false;
            });
        },
        async load_areas(){
            if(this.formData.city_id=='') return false

            let url = '/api/areas?city_id=' + this.formData.city_id;
            
            if(!this.edit_content_load) this.formData.area_id = '';

            this.areas = [];
            this.$axios.get(url, this.$parent.header_config).then( (response) => {
                console.log(response)
                this.areas = response.data.data
            }).catch(e => {
                this.$toast.error('Area load failed!!!', {icon: "error_outline"})
            });
        },
        async load_cities(){
            if(this.formData.region_id=='') return false

            let url = '/api/cities?region_id=' + this.formData.region_id;
            
            if(!this.edit_content_load) {
                this.formData.city_id = '';
                this.formData.area_id = '';
            }

            this.cities = [];
            this.$axios.get(url, this.$parent.header_config).then( (response) => {
                console.log(response)
                this.cities = response.data.data
            }).catch(e => {
                this.$toast.error('City load failed!!!', {icon: "error_outline"})
            });
        },
        async load_regions(){            
            let url = '/api/regions';
            
            this.regions = [];
            this.$axios.get(url, this.$parent.header_config).then( (response) => {
                console.log(response)
                this.regions = response.data.data
            }).catch(e => {
                this.$toast.error('Region load failed!!!', {icon: "error_outline"})
            });
        },
        async submit_form(){
            // let getFullName = this.formData.full_name
            // let getContactNo = this.formData.contact_no
            let regionId = this.formData.region_id
            let cityId = this.formData.city_id
            let areaId = this.formData.area_id
            let latitude = this.formData.latitude
            let longitude = this.formData.longitude
            let address = this.formData.address
            // let labelId = this.formData.label_id

            // if(getFullName.trim()==''){
            //     this.$toast.error('Please enter full name', {icon: "Warning"});
            //     return false;
            // }else 
            // if(getContactNo.trim()==''){
            //     this.$toast.error('Please enter contact number', {icon: "Warning"});
            //     return false;
            // }else 
            // if(getContactNo.trim()!=='' && !this.$validateContactNumber(getContactNo.trim())){
            //     this.$toast.error('Please valid contact number', {icon: "Warning"});
            //     return false;
            // }else 
            if(regionId==''){
                this.$toast.error('Please choose region', {icon: "Warning"});
                return false;
            }else if(cityId==''){
                this.$toast.error('Please choose city', {icon: "Warning"});
                return false;
            }else if(areaId==''){
                this.$toast.error('Please choose area', {icon: "Warning"});
                return false;
            }else if(address==''){
                this.$toast.error('Please enter address', {icon: "Warning"});
                return false;
            }
            // else if(labelId==''){
            //     this.$toast.error('Please choose delivery label', {icon: "Warning"});
            //     return false;
            // }

            if(confirm('Are you sure to submit it?')){
                this.req_submit = true;
                
                this.$axios.post('/api/manage-address/' + (this.edit_content_id?'update/' + this.edit_content_id:'store'), this.formData, this.$parent.header_config).then( async (response) => {
                    console.log('Get Data', response.data)

                    await this.$swal("Good job!", "Data has been saved successfully.", "success")

                    this.$parent.load_data()
                    
                    this.$parent.entry_form_open = false
                    this.$parent.sel_default_setup_index = ''
                    this.$router.push(this.$parent.cur_path)
                    
                    this.reset_form()
                    
                    this.req_submit = false
                }).catch(e => {
                    // console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.req_submit = false;
                });
            }
        },
        reset_form: function(){
            this.formData = {
                full_name: '',
                contact_no: '',
                region_id: '',
                city_id: '',
                area_id: '',
                latitude: this.$store.state.cur_lat,
                longitude: this.$store.state.cur_lng,
                address: '',
                label_id: '',
                status: 1
            }
            this.edit_status = {
                full_name: true,
                contact_no: true,
                region_id: true,
                city_id: true,
                area_id: true,
                latitude: true,
                longitude: true,
                address: true,
                label_id: true
            }
        }
    }
}
</script>
<style scoped>
    label{
        font-size: 12px;
    }
</style>
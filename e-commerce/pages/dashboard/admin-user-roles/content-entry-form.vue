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
                        <div class="hide">{{ access_action }}</div>
                        <label>Access</label>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <!-- <th rowspan="2" class="right_align">#</th> -->
                                        <th rowspan="2" class="left_align">Features</th>
                                        <th rowspan="2">Create</th>
                                        <th>View</th>
                                        <th colspan="2">Edit</th>
                                        <th colspan="2">Delete</th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <sub><small>Others</small></sub>
                                        </th>
                                        <th>
                                            <sub><small>Own</small></sub>
                                        </th>
                                        <th>
                                            <sub><small>Others</small></sub>
                                        </th>
                                        <th>
                                            <sub><small>own</small></sub>
                                        </th>
                                        <th>
                                            <sub><small>Others</small></sub>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="(list,caption) in system_featured_list">
                                        <tr :key="caption">
                                            <td colspan="7">
                                                <label class="fth_label">{{ caption }}</label>
                                            </td>
                                        </tr>
                                        <template v-for="(item,index) in list">
                                            <tr :key="caption + '-' + index">
                                                <!-- <td class="right_align">{{ index }}</td> -->
                                                <td class="left_align">{{ item.name }}</td>
                                                <td class="options" @click="sel_access_feature(index,'create')">
                                                    <i v-if="formData.role_accesses[index] && formData.role_accesses[index].create" class="fa fa-check-circle active"></i>
                                                    <i v-else class="far fa-check-circle"></i>
                                                </td>
                                                <td class="options" @click="sel_access_feature(index,'view_others')">
                                                    <i v-if="formData.role_accesses[index] && formData.role_accesses[index].view_others" class="fa fa-check-circle active"></i>
                                                    <i v-else class="far fa-check-circle"></i>
                                                </td>
                                                <td class="options" @click="sel_access_feature(index,'edit')">
                                                    <i v-if="formData.role_accesses[index] && formData.role_accesses[index].edit" class="fa fa-check-circle active"></i>
                                                    <i v-else class="far fa-check-circle"></i>
                                                </td>
                                                <td class="options" @click="sel_access_feature(index,'edit_others')">
                                                    <i v-if="formData.role_accesses[index] && formData.role_accesses[index].edit_others" class="fa fa-check-circle active"></i>
                                                    <i v-else class="far fa-check-circle"></i>
                                                </td>
                                                <td class="options" @click="sel_access_feature(index,'delete')">
                                                    <i v-if="formData.role_accesses[index] && formData.role_accesses[index].delete" class="fa fa-check-circle active"></i>
                                                    <i v-else class="far fa-check-circle"></i>
                                                </td>
                                                <td class="options" @click="sel_access_feature(index,'delete_others')">
                                                    <i v-if="formData.role_accesses[index] && formData.role_accesses[index].delete_others" class="fa fa-check-circle active"></i>
                                                    <i v-else class="far fa-check-circle"></i>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
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
                    <div class="mb-4">
                        <label>Role Title</label>
                        <div><input type="text" placeholder="Enter role title" class="form-control" v-model="formData.role_title" ref="role_title" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Weight (<small>Rang: [1-100]</small>)</label>
                        <div><input type="number" min="1" max="100" placeholder="i.e 1" class="form-control" v-model="formData.weight" ref="role_weight" /></div>
                    </div>
                    <div>
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
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {
        SwithcBtn
    },    
    data(){
        return {
            form_loader: false,
            formData: {
                role_title: '',
                weight: 1,
                role_accesses: {},
                status: true
            },
            req_submit: false,
            form_action: 'save',
            access_action: false
        }
    },
    computed: {
        form_submit_status () {
            return this.$store.state.form_submit_status        
        },
        system_featured_list () {
            return this.$store.state.dashboard_featured_list
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
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },
        sel_access_feature(id,index){
            if(this.formData.role_accesses[id]==undefined) this.formData.role_accesses[id] = {};
            if(this.formData.role_accesses[id][index]==undefined) this.formData.role_accesses[id][index] = 1;
            else delete this.formData.role_accesses[id][index];

            if(Object.keys(this.formData.role_accesses[id]).length==0) delete this.formData.role_accesses[id];

            if(this.access_action) this.access_action = false;
            else this.access_action = true;
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/user-role-infos/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                let getRoleAccess = {};

                getData.role_accesses.forEach(v => {
                    // getRoleAccess[v.feature_id] = {
                    //     'create': v.create?1:undefined,
                    //     'view_others': v.view_others?1:undefined,
                    //     'edit': v.edit?1:undefined,
                    //     'edit_others': v.edit_others?1:undefined,
                    //     'delete': v.delete?1:undefined,
                    //     'delete_others': v.delete_others?1:undefined
                    // }
                    getRoleAccess[v.feature_id] = {}
                    if(v.create) getRoleAccess[v.feature_id].create = 1
                    if(v.view_others) getRoleAccess[v.feature_id].view_others = 1
                    if(v.edit) getRoleAccess[v.feature_id].edit = 1
                    if(v.edit_others) getRoleAccess[v.feature_id].edit_others = 1
                    if(v.delete) getRoleAccess[v.feature_id].delete = 1
                    if(v.delete_others) getRoleAccess[v.feature_id].delete_others = 1
                });

                this.formData = {
                    role_title: getData.role_title,
                    weight: getData.weight,
                    role_accesses: getRoleAccess,
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

            if(this.formData.role_title.trim()==''){
                this.$toast.error('Please enter role title', {icon: "Warning"});
                this.$refs.role_title.focus();
                return false;
            } else if(this.formData.weight==''){
                this.$toast.error('Please enter role weight', {icon: "Warning"});
                this.$refs.role_weight.focus();
                return false;
            } else if(this.formData.weight<0 || this.formData.weight>100){
                this.$toast.error('Please enter valid role weight', {icon: "Warning"});
                this.$refs.role_weight.focus();
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
                
                await this.$parent.userRoleSubmit(submit_data);
                
                this.req_submit = false;
                this.$parent.load_data();                
                
                await this.$swal("Good job!", "Data has been saved successfully.", "success");

                if(this.form_action == 'save'){
                    this.form_reset();
                    this.$parent.add_new_entity(false)
                }
            }            
        },
        form_reset(){
            this.formData = {
                role_title: '',
                weight: 1,
                role_accesses: {},
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
    .fth_label{
        display: block;
        margin: 0;        
        text-align: center;
        color: #666;
        /* font-weight: bold; */
        font-size: 16px;
    }
    .table thead th{
        background-color: #666;
        color: #ffffff; text-shadow: 0 0 5px #000
    }
    .table thead td{
        border: 1px solid #ddd;
    }
    .table tbody td.options{
        cursor: pointer;
    }
    .table tbody td :deep(i){
        font-size: 18px;
        color: #cccccc;
        cursor: pointer;
        &.active{
            color: #363
        }
    }
</style>
<template>
    <div class="shipping_billing_info_setup_block">
        <div class="sb_info_row shipping_addr_info pt-3 pb-3">
            <div class="icon"><i class="fa fa-shipping-fast"></i></div>
            <div class="info" v-html="data.shipping_address"></div>
            <div class="action" @click="my_address_list_open=true;req_default_setup_index=1"><i class="fa fa-edit"></i></div>
        </div>
        <div class="sb_info_row billing_addr_info pb-3">
            <div class="icon"><i class="fa fa-book"></i></div>
            <div v-if="$parent.addr_same" class="info">Bill to the same address</div>
            <div v-else class="info" v-html="data.billing_address"></div>
            <div class="action" @click="my_address_list_open=true;req_default_setup_index=2"><i class="fa fa-edit"></i></div>
        </div>
        <div :class="['sb_info_row','contact_info',{edit_mode:contact_no_edit},'pb-3']">
            <div class="icon"><i class="fa fa-phone-alt"></i></div>
            <div class="info">
                <template v-if="contact_no_edit">
                    <input type="text" v-model="new_contact_no" placeholder="Enter contact number" ref="new_contact_no" />
                </template>
                <template v-else>{{ data.contact_no }}</template>
            </div>
            <div class="action">
                <template v-if="contact_no_edit">
                    <i class="fa fa-times mr-1 red_color" @click="contact_no_edit=false"></i>
                    <i class="fa fa-save green_color" @click="change_contact_no"></i>
                </template>
                <template v-else>
                    <i class="fa fa-edit" @click="contact_no_edit=true"></i>
                </template>
            </div>
        </div>
        <div :class="['sb_info_row','email_info',{edit_mode:email_edit},'pb-3']">
            <div class="icon"><i class="fa fa-envelope"></i></div>
            <div class="info">
                <template v-if="email_edit">
                    <input type="text" v-model="new_email" placeholder="Enter E-mail" ref="new_email" />
                </template>
                <template v-else>{{ data.email }}</template>    
            </div>
            <div class="action">
                <template v-if="email_edit">
                    <i class="fa fa-times mr-1 red_color" @click="email_edit=false"></i>
                    <i class="fa fa-save green_color" @click="change_email"></i>
                </template>
                <template v-else>
                    <i class="fa fa-edit" @click="email_edit=true"></i>
                </template>
            </div>
        </div>

        <template v-if="my_address_list_open">    
            <MyAddressList ref="my_address_list" />
        </template>
    </div>
</template>
<script>
import MyAddressList from './MyAddressList'
export default {
    name: 'ShippingBillingInfoSetupBlock',
    props: ['data'],
    components: {
        MyAddressList
    },
    data(){
        return {
            my_address_list_open: false,
            contact_no_edit: false,
            email_edit: false,
            new_contact_no: '',
            new_email: '',
            req_default_setup_index: ''
        }
    },
    methods: {
        change_address: function(data){
            if(this.req_default_setup_index==1) this.$parent.shipping_billing_info.shipping_address = this.$parent.get_full_address(data);
            else if(this.req_default_setup_index==2) this.$parent.shipping_billing_info.billing_address = this.$parent.get_full_address(data);

            if(this.$parent.shipping_billing_info.shipping_address===this.$parent.shipping_billing_info.billing_address) this.$parent.addr_same = true
            else this.$parent.addr_same = false

            this.my_address_list_open = false
        },
        change_contact_no: function(){
            let getContactNo = this.new_contact_no;
            if(getContactNo.trim()==''){
                this.$toast.error('Please enter contact number', {icon: "Warning"});
                this.$refs.new_contact_no.focus();
                return false;
            }else if(getContactNo.trim()!=='' && !this.$validateContactNumber(getContactNo.trim())){
                this.$toast.error('Please valid contact number', {icon: "Warning"});
                return false;
            }

            this.contact_no_edit = false;
            this.$parent.shipping_billing_info.contact_no = this.new_contact_no
        },
        change_email: function(){
            let getEmail = this.new_email;
            if(getEmail.trim()==''){
                this.$toast.error('Please enter email', {icon: "Warning"});
                this.$refs.new_email.focus();
                return false;
            }else if(getEmail.trim()!=='' && !this.$validateEmail(getEmail.trim())){
                this.$toast.error('Please valid email', {icon: "Warning"});
                return false;
            }

            this.email_edit = false;
            this.$parent.shipping_billing_info.email = this.new_email
        },
    }
}
</script>
<style lang="scss" scoped>
    .sb_info_row{
        display: flex;
        padding: 0 15px;
    }
    .sb_info_row.edit_mode > div{
        align-self: center;
    } 
    .sb_info_row > div.icon{
        position: relative;
        top: 2px; width: 20px;
        font-size: 12px;
        color: #5e97b3;
        margin-right: 8px;
    }
    .sb_info_row > div.info{
        width: 100%;        
    }
    .sb_info_row > div.info :deep(input){
        width: 100%;
        padding: 3px 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .sb_info_row > div.info :deep(.contact_person){
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
    .sb_info_row > div.info :deep(.address_dtl){
        display: flex;
        margin-top: 5px;
        font-size: 12px;
        .address_lbl{
            display: inline-block;
            white-space: nowrap;
            padding: 1px 10px;
            height: 17px;
            margin-right: 10px;
            background-color: #666;
            color: #fff;
            border-radius: 25px;
            font-size: 10px;        
            & > em{
                margin-right: 2px;
            }
            &.home{
                background-color: #006699;
            }
            &.office{
                background-color: #CD0000;
            }
        }
    }
    .sb_info_row > div.action{
        margin-left: auto;
        padding-left: 10px;
        font-size: 12px;
        cursor: pointer;
        color: #666;
        white-space: nowrap;
    }
        
    .red_color{
        color: #a72222
    }
    .green_color{
        color: #007000
    }
</style>
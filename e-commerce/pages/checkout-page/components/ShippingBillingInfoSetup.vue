<template>
    <div class="shipping_billing_info_setup_block">
        <label>Shipping address <em v-if="data.shipping_address" class="active"><i class="fa fa-check-circle"></i> provided</em><em v-else class="required"><i class="fa fa-check-circle"></i> required</em></label>
        <div class="sb_info_row shipping_addr_info">
            <div class="icon"><i class="fa fa-shipping-fast"></i></div>
            <template v-if="data.shipping_address">
                <div class="info" v-html="data.shipping_address"></div>
            </template>
            <template v-else>
                <div class="info"><em>[Not Provided]</em></div>
            </template>
            <div class="action" @click="my_address_list_open=true;req_default_setup_index=1"><i class="fa fa-edit"></i></div>
        </div>
        <label>Billing address <em v-if="data.billing_address" class="active"><i class="fa fa-check-circle"></i> provided</em><em v-else class="required"><i class="fa fa-check-circle"></i> required</em></label>
        <div class="sb_info_row billing_addr_info">
            <div class="icon"><i class="fa fa-book"></i></div>
            <template v-if="data.billing_address">
                <div v-if="$parent.addr_same" class="info">Bill to the same address</div>
                <div v-else class="info" v-html="data.billing_address"></div>
            </template>
            <template v-else>
                <div class="info"><em>[Not Provided]</em></div>
            </template>
            <div class="action" @click="my_address_list_open=true;req_default_setup_index=2"><i class="fa fa-edit"></i></div>
        </div>
        <label>Phone <em v-if="data.contact_no" class="active"><i class="fa fa-check-circle"></i> provided</em><em v-else class="required"><i class="fa fa-check-circle"></i> required</em></label>
        <div :class="['sb_info_row','contact_info',{edit_mode:contact_no_edit}]">
            <div class="icon"><i class="fa fa-phone-alt"></i></div>
            <div class="info">
                <template v-if="contact_no_edit">
                    <input type="text" v-model="new_contact_no" placeholder="Enter contact number" ref="new_contact_no" />
                </template>
                <template v-else>
                    <template v-if="data.contact_no">
                        {{ data.contact_no }}
                    </template>
                    <template v-else>
                        <div class="info"><em>[Not Provided]</em></div>
                    </template>
                </template>
            </div>
            <div class="action">
              <template v-if="contact_no_edit">
                <div class="red_color mr-1" @click="contact_no_edit=false"><i class="fa fa-times"></i> cancel</div>
                <div class="green_color" @click="change_contact_no"><i class="fa fa-check"></i> save</div>
              </template>
              <template v-else>
                <i class="fa fa-edit" @click="contact_no_edit=true"></i>
              </template>
            </div>
        </div>
        <label>E-mail <em class="optional"><i class="fa fa-check-circle"></i> optional</em></label>
        <div :class="['sb_info_row','email_info',{edit_mode:email_edit},'pb-3']">
            <div class="icon"><i class="fa fa-envelope"></i></div>
            <div class="info">
                <template v-if="email_edit">
                    <input type="text" v-model="new_email" placeholder="Enter E-mail" ref="new_email" />
                </template>
                <template v-else>
                  <template v-if="data.email">{{ data.email }}</template>
                  <template v-else>
                    <div class="info"><em>[Not Provided]</em></div>
                  </template>
                </template>
            </div>
            <div class="action">
                <template v-if="email_edit">
                    <div class="red_color mr-1" @click="email_edit=false"><i class="fa fa-times"></i> cancel</div>
                    <div class="green_color" @click="change_email"><i class="fa fa-check"></i> save</div>
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
            console.log(data)
            if(this.req_default_setup_index==1) this.$parent.shipping_billing_info.shipping_address = this.$parent.get_full_address(data, true);
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
.shipping_billing_info_setup_block{
    display: block;
    & > label{
        display: flex;
        padding: 10px 15px 5px;
        font-size: 13px;
        font-weight: 600;
        margin: 0;
        & > em{
            margin-left: 5px; color: #999;
            &.required{
                color: #CD0000
            }

            &.active{
                color: #007000
            }

            & > i{
                font-size: 10px;
            }
        }
    }

    & > .sb_info_row{
        display: flex;
        padding: 0 15px;
        & > div{            
            &.icon{
                position: relative;
                top: 2px; width: 20px;
                font-size: 12px;
                color: #5e97b3;
                margin-right: 8px;
            }
            &.info{
                width: 100%;
                & > :deep(input){
                    width: 100%;
                    padding: 3px 10px;
                    font-size: 14px;
                    border: 1px solid #ddd;
                    border-radius: 3px;
                }
                & > :deep(.contact_person){
                    font-size: 14px;
                    font-weight: bold;
                    color: #333;
                }
                & :deep(.address_dtl){
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
            }
        }
        &.edit_mode > div{
            align-self: center;
        }
        & > div.action{
            margin-left: auto;
            padding-left: 10px;
            font-size: 12px;
            cursor: pointer;
            color: #666;
            white-space: nowrap;
        }
    }
}

.red_color{
    color: #a72222
}
.green_color{
    color: #007000
}
</style>

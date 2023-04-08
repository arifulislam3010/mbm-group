
<template>
    <div :class="['invoice_detail_drawer_view_block',{mobile_device:$device.isMobile}]">
        <div class="black_mask_overlay" @click="$parent.close_order_invoice_block"></div>        
        <div class="content clearfix">
            <div class="print_btn" @click="printContent()">
                <i class="fa fa-print"></i>
            </div>
            <div id="main_content_block">
                <div class="page">
                    <div class="head_section_block">
                        <div class="bill_from_block">
                            <label>Bill From</label>
                            <div>{{ app_name }}</div>
                            <div v-html="$store.state.site_basic_config_data.printer_line_txt"></div>
                        </div>
                        <div class="company_logo">
                            <Logo />
                        </div>
                    </div>
                    <div class="ship_bill_section_block">
                        <div class="ship_to_block">
                            <label>Ship To</label>
                            <div v-html="sel_data.shipping_address"></div>                        
                            <div v-html="sel_data.contact_no"></div>                        
                            <div v-html="sel_data.email"></div>
                        </div>
                        <div class="bill_to_block ml-3 mr-3">
                            <label>Bill To</label>
                            <div v-html="sel_data.billing_address"></div>
                        </div>
                        <div class="order_info">
                            <div class="order_id">
                                <b>Invoice #</b><span>{{ sel_data.order_id }}</span>
                            </div>
                            <div class="ordered_at">
                                <b>Invoice Date</b>
                                <span>{{ sel_data.created_at }}</span>
                            </div>
                        </div>
                    </div>                
                
                    <div class="product_info mt-4">                    
                        <template v-if="Object.keys(sel_data).length>0">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th class="price_block">Unit Cost</th>
                                        <th class="price_block">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in sel_data.order_items_info" :key="index">
                                        <td>
                                            <div class="item_view_block">
                                                <div>
                                                    <div class="img">
                                                        <nuxt-img v-if="item.product_photo_info && item.product_photo_info[0].product_photo_data"
                                                            format="webp"
                                                            :src="item.product_photo_info[0].product_photo_data.content"
                                                        />
                                                        <img v-else class="empty-img" src="~/assets/images/empty-product.png" />
                                                    </div>
                                                    <div class="img mt-2" v-if="item.upload_product_image">
                                                        <nuxt-img :src="item.upload_product_image" />
                                                    </div>
                                                </div>
                                                <div class="info">                    
                                                    <div class="title text_overflow max_one_line_allow">
                                                        {{ item.product_title }}
                                                    </div>
                                                    <div class="unit_price_type text_overflow max_one_line_allow">
                                                        {{ item.product_price_type }}
                                                    </div>
                                                    <hr />
                                                    <div class="product_type_list">
                                                        <div v-for="(ptiv,ptii) in item.product_type_infos" :key="ptii">
                                                            <template v-if="ptii=='color'">
                                                                <span>{{ ptiv.title }}:</span>
                                                                <span>
                                                                    <b :style="{'background-color': ptiv.values.color_code}"></b>
                                                                    <em>{{ ptiv.values.color_title }}</em>
                                                                </span>
                                                            </template>
                                                            <template v-else>
                                                                <span>{{ ptiv.title }}:</span>
                                                                <span><em>{{ ptiv.values }}</em></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td align="center">{{ item.qty }}</td>
                                        <td class="price_block"><PriceViewBlock :data="item.price" /></td>
                                        <td class="price_block"><PriceViewBlock :data="(item.price * item.qty).toFixed(2)" /></td>
                                    </tr>
                                    <!-- <tr>
                                        <td colspan="3" align="right">Subtotal ({{ sel_data.order_items_info.length }} items)</td>
                                        <td class="price_block"><PriceViewBlock :data="sel_data.total_amount.toFixed(2)" /></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="right">VAT ({{ sel_data.vat_amount }}%)</td>
                                        <td class="price_block"><PriceViewBlock :data="((sel_data.total_amount * sel_data.vat_amount)/100).toFixed(2)" /></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="right">Discount</td>
                                        <td class="price_block"><PriceViewBlock :data="sel_data.discount.toFixed(2)" /></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="right">Delivery fee</td>
                                        <td class="price_block">
                                            <template v-if="sel_data.delivery_fee>0"><PriceViewBlock :data="sel_data.delivery_fee.toFixed(2)" /></template>
                                            <template v-else>Free</template>
                                        </td>
                                    </tr> -->
                                </tbody>
                                <!-- <tfoot>
                                    <tr>
                                        <th colspan="3" class="price_block" align="right">Total</th>
                                        <th class="price_block"><PriceViewBlock :data="sel_data.total_payable.toFixed(2)" /></th>
                                    </tr>
                                </tfoot>                             -->
                            </table>
                            <!-- <OrderDetailsReceiptInfo :data="sel_data.order_items_info" />
                            <hr style="border: 1px dashed #ddd" />
                            <OrderSummaryInfo :data="sel_data" /> -->
                        </template>
                    </div>

                    <div class="order_summary_others_block">
                        <div>
                            <div class="payment_method_block">
                                <label>Payment Method</label>
                                <div v-if="sel_data.choose_payment_type==1">Cash On Delivery</div>
                                <div v-if="sel_data.choose_payment_type==2">Digital Payment</div>
                            </div>
                        </div>
                        <div class="summary_block">
                            <OrderSummaryInfo :data="sel_data" />
                        </div>
                    </div>
                    
                    <hr />

                    <div class="footer_section">
                        <div class="powered_by">
                            <!-- <span>Powered by <a href="https://orangebd.com" target="_blank"><img src="/_ipx/images/powered-by.png" title="Powered by image" alt="Powered by image" /></a></span> -->
                        </div>
                        <div class="developer_info">
                            <span>Developed by <a href="https://orangebd.com" target="_blank"><img src="/_ipx/images/developer.logo.png" title="Developer logo" alt="Developer logo" /></a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</template>
<script>
// import OrderDetailsReceiptInfo from './components/OrderDetailsReceiptInfo'
import OrderSummaryInfo from './components/OrderSummaryInfo'
import PriceViewBlock from '@/components/content_display/price/ViewBlock'
import Logo from '@/components/Logo'
export default {
    name: 'InvoiceDrawerViewBlock',    
    components: {
        Logo,
        PriceViewBlock,
        // OrderDetailsReceiptInfo,
        OrderSummaryInfo
    },
    computed: {
        app_name: function(){
            return process.env.APP_NAME
        },
        sel_data: function(){
            return this.$store.state.order_info.sel_data
        }
    },
    mounted(){
        document.body.classList.add('popup_open')
        // this.getDataList = this.data    
    },
    methods: {
        printContent: function(){
            // var printContents = document.getElementById('main_content_block').innerHTML;
            // var originalContents = document.body.innerHTML;

            // document.body.innerHTML = printContents;

            // window.print();

            // document.body.innerHTML = originalContents;

            // var divContents = document.getElementById("main_content_block").innerHTML;
            // var a = window.open('', '', 'height=500, width=500');
            // a.document.write('<html>');
            // a.document.write('<body > <h1>Div contents are <br>');
            // a.document.write(divContents);
            // a.document.write('</body></html>');
            // a.document.close();
            // a.print();

            const options = {
                name: '_blank',
                specs: [
                    'fullscreen=yes',
                    'titlebar=yes',
                    'scrollbars=yes'
                ],
                styles: [
                    process.env.BASE_URL + '/css/bootstrap.v4.2.1.min.css',
                    // process.env.BASE_URL + '/css/fontawesome.v5.15.2.all.css',
                    process.env.BASE_URL + '/css/invoice.print.css'
                ],
                timeout: 1000, // default timeout before the print window appears
                autoClose: false, // if false, the window will not close after printing
                windowTitle: window.document.title,
            }

            this.$htmlToPaper('main_content_block', options);
        }
    }
}
</script>
<style lang="scss" scoped>
    .invoice_detail_drawer_view_block{
        position: relative;
        z-index: 1001;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        background-image: linear-gradient(to top, #000000cc, #66666680);
    }
    .content{
        position: fixed;
        color: #333;
        background-color: #fff;
        width: 850px;
        padding: 50px;
        height: 100%;
        overflow-y: auto;
        right: 0;
        top: 0;
        box-shadow: 0 0 15px #333;
        transition: all 0.4s;

        .print_btn{
            position: absolute;
            display: inline-block;
            text-align: center;
            width: 46px; height: 46px; line-height: 46px;
            right: 15px; top: 15px;
            border-radius: 50%;
            color: #fff;
            background-color: $sys_brand_color;
            cursor: pointer;
            transition: all 0.4s;

            &:hover{
                background-color: #CD0000;
            }
        }
    }
    .mobile_device .content{
        width: 300px;
    }

    .head_section_block {
        display: flex;
        padding-bottom: 15px;
        border-bottom: 1px solid #ddd;

        & > div{
            width: 50%;
            
            label {
                font-size: 12px; font-weight: bold;
            }

            &:last-child{
                margin-left: auto;
                img{
                    max-height: 60px;
                    object-fit: contain
                }
            }
        }
    }
    .ship_bill_section_block{
        display: flex;        
        padding-top: 15px;        
        font-size: 12px;        

        & > div{
            width: 100%;
            
            label {
                font-size: 12px; font-weight: bold;
            }
        }

        & > div.order_info{
            
            & > div{
                display: flex;
                width: 100%;

                & > span{
                    margin-left: auto;
                }
            }
        }
    }

    .product_info{
        display: block;
        font-size: 12px;

        thead{
            background-color: #ddd;
        }
        tfoot{
            background-color: #eee;
        }

        th,td{
            padding: 7px 15px
        }
        
        .price_block{
            text-align: right
        }

        .item_view_block{
            display: flex;                

            .img{
                width: 60px;
                height: 40px;
                margin-right: 10px;
                border: 1px solid #ddd;
                border-radius: 3px;
                background-color: #eee;
            
                & > img{
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    &.empty-img{
                        padding: 5px
                    }
                }
            }

            .info{
                margin-right: 10px;
                & > .title{
                    font-size: 13px;
                }
                & > .unit_price_type{
                    font-size: 11px;
                }

                & > hr{
                    margin: 5px 0;
                }

                & > .product_type_list > div{
                    display: flex;
                    gap: 5px;
                    font-size: 11px;
                    color: #444;

                    & > span{
                        display: flex;
                        align-items: center;
                        gap: 4px;

                        & > b{
                            display: inline-block;
                            width: 14px;
                            height: 14px;
                            border-radius: 50%;
                            border: 1px solid #ccc;
                        }
                    }

                    & > span:first-child{
                        color: #666;
                        white-space: nowrap;
                    }
                }
            }
        }
    }
    .order_summary_others_block{
        display: flex;
        margin-top: 25px;

        & > div{
            width: 50%;

            .payment_method_block {
                display: block;
                border: 1px solid #ddd;
                margin-right: 25px;
                font-size: 12px;

                & > label{
                    padding: 5px 15px;
                    background-color: #eee;
                    width: 100%; margin: 0;
                    border-bottom: 1px solid #ddd;
                    display: block; font-weight: bold;
                }

                & > div{
                    display: block;
                    padding: 5px 15px;
                }
            }

            &.summary_block{
                padding: 0 15px
            }
        }
    }
    .footer_section{
        display: flex;

        & > div{            
            font-size: 12px;

            &.developer_info{
                margin-left: auto;
            }

            img{
                max-height: 16px; object-fit: contain;
            }
        }
    }
</style>
<style scoped>
    .bill_from_block > div :deep(p){
        margin-bottom: 0; font-size: 12px;
    }
    @media print {
        a{
            text-decoration: none;
        }
    }
</style>
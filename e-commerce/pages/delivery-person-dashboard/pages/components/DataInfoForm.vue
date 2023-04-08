<template>
    <div class="data_info_form_block">
        <div class="form_block edit_content" v-if="$parent.edit_status[column]">
            <span v-if="type=='dropdown'" :class="isize?isize:''">
                <template v-if="onchange">
                    <select v-model="$parent.formData[column]" @change="$parent[onchange]">
                        <option value="">Choose One</option>
                        <option v-for="(item,index) in data" :key="index" :value="item[vf]">{{ item[tf] }}</option>
                    </select>
                </template>
                <template v-else>
                    <select v-model="$parent.formData[column]">
                        <option value="">Choose One</option>
                        <option v-for="(item,index) in data" :key="index" :value="vf?item[vf]:index">{{ tf?item[tf]:item }}</option>
                    </select>
                </template>
            </span>

            <span v-else-if="type=='radio'" :class="['radio_item_list','square',isize?isize:'']">
                <template v-for="(item,index) in data">
                    <span :key="index" :value="index" @click="$parent.formData[column]=index" :class="{active:$parent.formData[column]==index}"><span v-html="item.icon" class="mr-2"></span> {{ item.title }}</span>
                </template>                
            </span>

            <span v-else-if="type=='date'" :class="isize?isize:''">
                <datetime type="date" input-class="date_input" v-model="$parent.formData[column]" zone="Asia/Dhaka" />
            </span>

            <span v-else-if="type=='textarea'" :class="['textarea',isize?isize:'']">
                <textarea-autosize
                    :placeholder="placeholder"                    
                    v-model="$parent.formData[column]"
                    :min-height="30"
                    :max-height="450"
                    @keyup.enter="$parent.submit_form"
                />
            </span>

            <span v-else :class="isize?isize:''"><input :type="type" :placeholder="placeholder" v-model="$parent.formData[column]" @keyup.enter="$parent.submit_form" /></span>
            <span class="ml-2 remove" @click="$parent.edit_status[column]=false"><i class="fa fa-times"></i></span>
            <!-- <span class="ml-2 save"><i class="fa fa-save"></i></span> -->
        </div>
        <div class="form_block view_content" v-else>                    
            <span v-if="$parent.formData[column]">
                <template v-if="type=='dropdown'">
                    <template v-for="item in data">
                        <template v-if="item[vf]==$parent.formData[column]">{{ item[tf] }}</template>
                    </template>
                </template>
                <template v-else-if="type=='radio'">{{ data[$parent.formData[column]][tf] }}</template>
                <template v-else-if="type=='date'">{{ $parent.formData[column] | luxon('dd-MM-yyyy') }}</template>
                <template v-else>{{ $parent.formData[column] }}</template>    
            </span>
            <span v-else><em>[Not provided]</em></span>
            <span class="ml-2 edit" @click="$parent.edit_status[column]=true"><i class="fa fa-pen"></i></span>
        </div>
    </div>
</template>
<script>
export default {
    name: 'DataInfoFormBlock',
    props: ['column','type','placeholder','data','isize','vf','tf','onchange'],
    mounted(){}
}
</script>
<style lang="scss" scoped>
    .form_block{
        display: flex;
        min-height: 26px;
        width: 100%;
        :deep(select),
        :deep(input),
        :deep(textarea),
        :deep(.date_input){
            border: 1px solid #ddd;
            font-size: 13px;
            border-radius: 3px;
            padding: 2px 5px;
            width: 100%;
        }
        :deep(textarea){
            padding: 5px 10px;
        }
        & > span{
            align-self: center;
            &.full{
                width: 100%;
            }
            &.textarea{
                align-self: baseline;
                width: 100%;
            }
            &.radio_item_list{
                align-self: baseline;    
                &.square > span{
                    display: inline-block;
                    padding: 8px 15px; margin: 0 10px;
                    border: 1px solid #ddd; border-radius: 5px;
                    cursor: pointer;
                    &.active{
                        border-color: #b64545; color: #b64545; background-color: #f7f7f7; font-weight: bold;
                    }
                    &:first-child{
                        margin-left: 0;
                    }
                }
            }
            & > i{
                color: #999; font-size: 12px;
                cursor: pointer;
            }
            &.remove > i{
                color: #b64545;
            }
            &.save > i{
                color: #538053;
            }
            &.edit > i{
                color: #999;
            }
        }
    }
</style>
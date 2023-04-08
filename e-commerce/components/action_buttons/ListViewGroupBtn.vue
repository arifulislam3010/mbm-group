<template>
    <div class="list_view_group_btn">
        <!-- <span v-if="$store.state.user_role_access[access_id] && (
            (
                $store.state.auth.user.id==user_id &&
                $store.state.user_role_access[access_id].edit
            ) || 
            $store.state.user_role_access[access_id].edit_all
        )" class="action_btn" @click="$parent.edit_item(req_id)"><i class="fa fa-edit"></i></span>
        <span v-if="$store.state.user_role_access[access_id] && (
            $store.state.user_role_access[access_id].delete || $store.state.user_role_access[access_id].delete_all
        )" class="action_btn del_btn" @click="$parent.del_item(req_id)"><i class="fa fa-trash"></i></span> -->
        <span class="action_btn" @click="$parent.edit_item(req_id)"><i class="fa fa-edit"></i></span>
        <!-- <span class="action_btn del_btn" @click="$parent.del_item(req_id)"><i class="fa fa-trash"></i></span> -->
    </div>
</template>
<script>
export default {
    name: 'ListViewGroupBtn',
    props: ['user_id','access_id','req_id']
}
</script>
<style scoped>
    .action_btn{
        display: inline-block;
        font-size: 14px;
        margin: 0 3px;
        cursor: pointer;
        color: #006699;
    }
    .action_btn.del_btn{
        color: #ad6d6d
    }
</style>
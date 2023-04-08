<template>
    <no-ssr>
        <vue-tags-input
            v-model="tag"
            :tags="tags"
            :autocomplete-items="autocompleteItems"
            :addOnlyFromAutocomplete="only_use_autocomplete"
            @tags-changed="update"
        />
    </no-ssr>
</template>
<script>
export default {
    name: 'ProductTypeNameManagementBlock',
    props: ['cur_index','product_type_id','product_open_entry'],
    data() {
        return {
            only_use_autocomplete: this.product_open_entry?false:true,
            tag: '',
            tags: this.$parent.$parent.$parent.ProductTypeNames[this.cur_index],
            autocompleteItems: [],
            debounce: null,
        };
    },
    watch: {
        'tag': 'initItems',
        product_open_entry: function(status){
            if(status) this.only_use_autocomplete = false
            else this.only_use_autocomplete = true
        }
    },
    methods: {
        update(newTags) {
            this.autocompleteItems = [];
            this.tags = newTags;
            let getTagsObj = [];
            let getTags = [];
            let getProductTypeNamesObj = [];
            console.log('Get Product Type Names', this.tags)
            this.tags.forEach( async (v,i) => {
                getTagsObj.push(v.text);
                getTags[i] = {};
                getTags[i]['tiClasses'] = [];
                getTags[i]['text'] = v.text;
                getTags[i]['id'] = v.id;
                getTags[i]['tiClasses'][0] = 'ti-valid';

                /**
                 * Product Type Names List Object
                 */
                getProductTypeNamesObj[i] = {
                  id: v.id,
                  type_name: v.text
                };
            });
            this.$parent.$parent.$parent.ProductTypeNames[this.cur_index] = getTags;
            this.$parent.$parent.$parent.product_type_arr[this.cur_index].type_name = getTagsObj;

            // console.log('Get product type names', getProductTypeNamesObj)
            if(!this.$parent.$parent.$parent.$parent.product_type_names_info[this.product_type_id]) this.$parent.$parent.$parent.$parent.product_type_names_info[this.product_type_id] = {}
            this.$parent.$parent.$parent.$parent.product_type_names_info[this.product_type_id] = getProductTypeNamesObj;
        },
        initItems() {
            if (this.tag.length < 2) return;
            const url = `/api/product-type-info-name/search?term=${this.tag}&product_type_id=${this.product_type_id}&limit=6`

            clearTimeout(this.debounce);
            this.debounce = setTimeout( async () => {
                // this.$axios.get(url, this.$parent.$parent.header_config).then(response => {
                // this.autocompleteItems = response.data.map(a => {
                //     return { text: a.tag_title };
                // });
                // }).catch(() => console.warn('Oh. Something went wrong'));

                let getReponseData = await this.$http.$get(url, this.$parent.$parent.$parent.$parent.$parent.header_config);
                // console.log('Response data', getReponseData);
                this.autocompleteItems = getReponseData.map(a => {
                    return { id: a.id, text: a.type_name };
                });
            }, 600);
        }
    }
}
</script>
<style lang="scss" scoped>
    .vue-tags-input{
        max-width: 100%;
        border-radius: 5px;
        :deep(.ti-input){
            border-radius: 3px
        }
    }
</style>

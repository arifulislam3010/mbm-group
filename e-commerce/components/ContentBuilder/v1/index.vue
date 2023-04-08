<template>
    <div id="gjs"></div>
</template>
<script>
import grapesjs from 'grapesjs'
import plugin1 from 'grapesjs-preset-webpage';
import plugin2 from 'grapesjs-blocks-basic';
import plugin3 from 'grapesjs-tui-image-editor';
import 'grapesjs/dist/css/grapes.min.css'
// import 'grapesjs/dist/grapes.min.js'
// import 'grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.css'
// import 'grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.js'
// import 'grapesjs-preset-webpage/dist/index.d.js'
// import 'grapesjs-preset-webpage/dist/index.js'
export default {
    name: 'ContentBuilderBlock',
    props: {
        content: String,
        css: String,
        assets: Boolean
    },
    data(){
        return {
            editor: '',
            asset_list: [],
            asset_limit: 10,
            user_access_token: this.$store.state.auth_info.user_data.token
        }
    },
    computed: {
        headers (){
            let obj = {
                'Access-Control-Allow-Credentials': true,
                'Authorization': 'Bearer ' + this.user_access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': this.user_access_token
            }

            return obj;
        },
        header_config (){
            let obj = {
                headers: this.headers
            };
            return obj;
        }
    },
    mounted(){        
        this.editor = grapesjs.init({
            container : '#gjs',
            // height: '80%',
            // components: this.content,
            style: '.txt-red{color: red}',
            storageManager: false,
            plugins: [plugin1,plugin2,plugin3],
            pluginsOpts: {
                [plugin1]: {
                    // blocksBasicOpts: {
                    //     blocks: ['column1', 'column2', 'column3', 'column3-7', 'text', 'link', 'image', 'video'],
                    //     flexGrid: 1,
                    // },
                    // blocks: ['link-block', 'quote', 'text-basic'],
                    
                },
                [plugin2]: {},
                [plugin3]: {}
            },
            layerManager: {
                // If the `root` is not specified or the component element is not found,
                // the main wrapper component will be used.
                root: '#my-custom-root',
                sortable: true,
                hidable: false,
            },
            assetManager: {                
                // custom: true,
                assets: [],
                // upload: process.env.API_ENDPOINT + '/api/media-galleries/upload',
                // uploadName: 'editor_files',
                // headers: this.headers,
                // credentials: 'include',
            },
            canvas: {
                styles: [
                    `/fonts/aller-fonts/Aller_Rg.woff`,
                    `/fonts/aller-fonts/Aller_It.woff`,
                    `/fonts/aller-fonts/Aller_Bd.woff`,
                    `/fonts/aller-fonts/Aller_BdIt.woff`,
                    `/fonts/aller-fonts/AllerDisplay.woff`,
                    `/fonts/aller-fonts/Aller_LtIt.woff`                    
                ]
            }
        });

        this.editor.setComponents(this.content);
        this.editor.setStyle(this.css);
        this.editor.on('change', this.getData);
        this.editor.on('load', () => {
            const customFontAdd = this.editor.StyleManager.getProperty('typography', 'font-family');
            customFontAdd.set('options', [
                {value: "Aller", name: 'Aller'},
                {value: "Aller Italic", name: 'Aller Italic'},
                {value: "Aller Bold", name: 'Aller Bold'},
                {value: "Aller Bold Italic", name: 'Aller Bold Italic'},
                {value: "Aller Display", name: 'Aller Display'},
                {value: "Aller Light Italic", name: 'Aller Light Italic'},
                {value: "Helvetica Neue,Helvetica,Arial,sans-serif", name: 'Helvetica'},            
                {value: "'Oswald', sans-serif", name: 'Oswald'},
                {value: "sans-serif", name: 'sans-serif'},
                {value: "Times New Roman", name: 'Times New Roman'},
                {value: "Arial Black", name: 'Arial Black'},
                {value: "'Montserrat', sans-serif", name: 'Montserrat'},
                {value: "Verdana, Geneva, sans-serif", name: 'Verdana'},
                {value: "Courier New Courier, monospace", name: 'Courier New Courier'},
                {value: "'Lato', sans-serif", name: 'Lato'},
                {value: "'Open Sans', sans-serif", name: 'Open Sans'}
            ]);
        })        

        // The upload is started
        this.editor.on('asset:upload:start', () => {
            // startAnimation();
        });

        // The upload is ended (completed or not)
        this.editor.on('asset:upload:end', () => {
            // endAnimation();
        });

        // Error handling
        this.editor.on('asset:upload:error', (err) => {
            // notifyError(err);
        });

        // Do something on response
        this.editor.on('asset:upload:response', (response) => {
            // this.loadAssets()
            console.log(response)
        });

        if(this.assets) this.loadAssets()
    },
    methods: {
        getData: function(){
            let getHtml = this.editor.getHtml()
            let getCss = this.editor.getCss()
            this.$parent.getData(getHtml,getCss)
        },
        loadAssets: function(){
            let url = '/api/media-galleries'+ (this.asset_limit>0?'?limit=' + this.asset_limit:'');
            
            this.asset_list = [];
            this.$axios.get(url, this.header_config).then( (response) => {
                console.log(response)
                if(response.data.data.length>0){
                    const assetManager = this.editor.AssetManager;
                    response.data.data.forEach((v,i) => {
                        let obj = {
                            type: 'image',
                            src: v.content,
                            height: 350,
                            width: 250,
                            name: v.content_title
                        }

                        // if(i==0) this.asset_list.push(v.content)
                        // else this.asset_list.push(obj)
                        assetManager.add(obj)
                    })

                    // this.editor.on('asset:add', props => {
                    //     props.assets = this.asset_list
                    //     console.log(props.assets)
                    // })
                }                
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})                
            });
        }
    }
}
</script>
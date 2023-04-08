<template>
    <div class="leaflet_map_block">
        <div id="map-wrap" :style="{height: height}">            
            <no-ssr>
                <template v-if="markerPosChange">
                    <span class="pick_cur_location" @click="pick_cur_location" title="Pick exact location"><i class="fa fa-location-arrow"></i></span>
                    <l-map :zoom="zoom" :center="[lat,lng]">
                        <l-tile-layer url="http://{s}.tile.osm.org/{z}/{x}/{y}.png"></l-tile-layer>
                        <l-marker :lat-lng="[lat,lng]" :icon="icon" :draggable="true" @dragend="dragStartHandler">
                            <l-tooltip>
                                <div>I am here</div>
                            </l-tooltip>
                            <div class="photo">
                                <img src="/icon.png" />
                            </div>
                        </l-marker>
                    </l-map>
                </template>
                <template v-else>
                    <l-map :zoom="zoom" :center="[lat,lng]">
                        <l-tile-layer url="http://{s}.tile.osm.org/{z}/{x}/{y}.png"></l-tile-layer>
                        <l-marker :lat-lng="[lat,lng]" :icon="icon">
                            <l-tooltip>
                                <div>I am here</div>
                            </l-tooltip>
                        </l-marker>
                    </l-map>
                </template>
            </no-ssr>
        </div>
    </div>
</template>
<script>
export default {
    name: 'LeafLetMapBlock',
    props: ['zoom','lat','lng','height','markerPosChange'],
    data(){
        return{
            iconSize: 36
        }
    },
    computed: {
        dynamicSize () {
            return [this.iconSize, this.iconSize * 1.15];
        },
        dynamicAnchor () {
            return [this.iconSize / 2, this.iconSize * 1.15];
        },
        icon (){
            // return L.icon({
            //     iconUrl: '/marker.png',
            //     iconSize: [38, 50],
            //     iconAnchor: [20, 30]
            // });
            return L.divIcon({
                className: 'map_div_icon',
                // iconUrl: '/marker.png',
                iconSize: [38, 50],
                iconAnchor: [20, 30],
                html: '<div class="default_marker_icon"><img src="/marker.png" /></div><div class="select_icon"><img src="/icon.png" /></div>'
            })
        }
    },
    methods: {
        dragStartHandler: function (e) {
            var latlng = e.target._latlng
            this.$parent.newLatLngSetup(latlng)
            // console.log(latlng)
        },
        pick_cur_location: function(){
            let elm = this
            navigator.geolocation.getCurrentPosition(function(position) {
                // Get the coordinates of the current position.
                console.log("Current Position", position);          

                // console.log("Latitude : " + position.coords.latitude);
                // console.log("Longitude : " + position.coords.longitude);
                let latlng = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                }
                elm.$parent.newLatLngSetup(latlng)
            });
        }
    }
}
</script>
<style lang="scss" scoped>
    #map-wrap{
        position: relative;
        .pick_cur_location{
            position: absolute;
            z-index: 1000;
            top: 5px;
            right: 5px;
            /* padding: 5px; */
            margin: 5px;
            background-color: #35a8ca;
            color: #fff;
            width: 20px;
            height: 20px;
            line-height: 22px;
            border-radius: 25px;
            box-shadow: 0 0 5px #000;
            text-align: center;
            font-size: 8px;
            cursor: pointer
        }
        :deep(.map_div_icon){
            .default_marker_icon{
                display: inline-block;
                position: relative;
                width: 38px; height: 50px;
                img{
                    width: 100%; height: 100%; object-fit: contain;
                }
            }

            .select_icon{
                position: absolute;
                left: 0;
                top: 0;
                width: 38px;
                height: 38px;
                padding: 5px;
                border-radius: 50%;
                overflow: hidden;
                img{
                    width: 100%; height: 100%; object-fit: cover;
                }
            }
        }
    }    
</style>
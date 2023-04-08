<template>
    <div class="rating_n_reviews_block clearfix">        
        <div class="left-side">
            <div class="avg_points mt-2 mb-1">
                <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="25" :r2_h="0" />
            </div>
            <div class="rating_view pre_loader">
                <FormBlockLoader :cols="1" :height="50" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="45" :r2_h="0" />
            </div>
            <div class="total_rating mt-3">
                <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="15" :r2_h="0" />
            </div>
        </div>
        <div class="right-side">
            <div class="rating_row" v-for="(n,i) in 5" :key="i">
                <div class="rating_view pre_loader">
                    <FormBlockLoader :cols="1" :height="80" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="75" :r2_h="0" />
                </div>
                <div class="rating_progress">
                    <span></span>
                </div>
                <div class="rating_count pre_loader">
                    <FormBlockLoader :cols="1" :height="80" :r1="true" :r2="false" :r1_w="30" :r2_w="0" :r1_h="75" :r2_h="0" />
                </div>
            </div>
        </div>        
    </div>
</template>
<script>
export default {
    name: 'RatingNReviewsBlock'
}
</script>
<style scoped>
    .left-side{
        float: left;
        width: 300px;
    }
    .left-side .rating_view{
        display: inline-block;
    }
    .left-side .rating_view.pre_loader{
        width: 100%;
    }
    .avg_points > span{
        display: inline-block;
        font-size: 26px; color: #ccc   
    }
    .avg_points > .obtain_points{
        display: inline-block;
        font-size: 36px; color: #333
    }    
    .right-side{
        float: left;
        width: calc(100% - 315px);
        margin-left: 15px;
    }
    .rating_row{
        display: flex;
        height: 20px; line-height: 20px;
        width: 100%;
        margin-bottom: 10px;
    }
    .rating_row > .rating_view{
        width: 100px
    }
    .rating_progress{
        position: relative;
        width: 150px; background-color: #eee; margin: 3px 15px;
    }
    .rating_progress > span{
        display: inline-block;
        height: 100%; background-color: #f05a24;
    }
    .rating_count{
        font-size: 12px;
    }
    .rating_count.pre_loader{
        width: 80px
    }
    @media screen and (max-width: 426px) {
        .left-side{
            float: none;
            width: 100%;
            text-align: center;
        }
        .right-side{
            float: none;
            margin-left: 0;
            margin-top: 15px;
            width: 100%;
        }
    }
</style>
<template>
    <div class="rating_n_reviews_block clearfix">        
        <div class="left-side">
            <div class="avg_points mt-2 mb-1">
                <span class="obtain_points">3.5</span>
                <span class="divider">/</span>
                <span class="outof_points">5</span>
            </div>
            <div class="rating_view">
                <star-rating
                    :rating="3.5"
                    :read-only="true"
                    :animate="false"
                    :active-color="['#eeeeee','#f05a24']"
                    :active-border-color="['#ffffff','#f05a24']" :border-width="2"
                    :rounded-corners="false"
                    :show-rating="false"
                    :increment="0.1"
                    :star-size="24">
                </star-rating>
            </div>
            <div class="total_rating mt-3">1 Rating</div>
        </div>
        <div class="right-side">
            <div class="rating_row" v-for="(n,i) in 5" :key="i">
                <div class="rating_view">
                    <star-rating
                        :rating="3.5"
                        :read-only="true"
                        :animate="false"
                        :active-color="['#eeeeee','#f05a24']"
                        :active-border-color="['#ffffff','#f05a24']" :border-width="2"
                        :rounded-corners="false"
                        :show-rating="false"
                        :increment="0.1"
                        :star-size="10">
                    </star-rating>
                </div>
                <div class="rating_progress">
                    <span :style="{'width':'75%'}"></span>
                </div>
                <div class="rating_count">3.5</div>
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
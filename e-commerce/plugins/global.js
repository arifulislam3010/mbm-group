import Vue from 'vue';
import Editor from '@tinymce/tinymce-vue'
import FormBlockLoader from '~/components/loader/FormBlockLoader';
import RainbowLoader from '~/components/loader/RainbowLoader';
import TableContentLoader from '~/components/loader/TableContentLoader'
import SpinkitLoader from '~/components/loader/SpinkitLoader'
import SpinkitBounceLoader from '~/components/loader/SpinkitBounceLoader'
import SpinkitDoubleBounceLoader from '~/components/loader/SpinkitDoubleBounceLoader'
import LogoPercentageBarLoaderV1 from '~/components/loader/LogoPercentageBarLoader.v1'
import LogoPercentageBarLoaderV2 from '~/components/loader/LogoPercentageBarLoader.v2'
import Gravatar from '~/components/gravatar'
import Pagination from '~/components/Pagination';

Vue.component('TinyMce', Editor);
Vue.component('FormBlockLoader', FormBlockLoader);
Vue.component('RainbowLoader', RainbowLoader);
Vue.component('TableContentLoader', TableContentLoader);
Vue.component('SpinkitLoader', SpinkitLoader);
Vue.component('SpinkitBounceLoader', SpinkitBounceLoader);
Vue.component('SpinkitDoubleBounceLoader', SpinkitDoubleBounceLoader);
Vue.component('LogoPercentageBarLoaderV1', LogoPercentageBarLoaderV1);
Vue.component('LogoPercentageBarLoaderV2', LogoPercentageBarLoaderV2);
Vue.component('Gravatar', Gravatar);
Vue.component('Pagination', Pagination);
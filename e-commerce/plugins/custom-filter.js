// import axios from 'axios'
// import http from 'http'

// TinyMce setup
function tinyMce(){
  let data = {
    token: '0owqnt3cqdly029w7huz6jb33gtd2kr4mmrhjl2uh9pa5frc',
    init: {
      plugins: [
      'advlist lists link image paste help wordcount'
      ],
      toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | help'
    }
  };

  return data;
}

// Vue.prototype.$enToBnNumber = (str) => {
function enToBnNumber(str) {
  let reqStr = str + "";
  const bnArr = ['০','১','২','৩','৪','৫','৬','৭','৮','৯']
  let getStr = ''
  for(var i=0; i < reqStr.length; i++) {
      if(bnArr[reqStr[i]]!==undefined) getStr = getStr + bnArr[reqStr[i]];
      else getStr = getStr + reqStr[i];
  }
  return getStr
};

function strCapitalize(s) {
  if (typeof s !== 'string') return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}

function validateEmail(email) {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}

function validateContactNumber(number){
  const re = /^[\+]?[0-9]{1,3}?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,9}$/g;
  return re.test(number);
}

// slug setup
function strSlug(s) {
  if (typeof s !== 'string') return ''
  return s.trim().toLowerCase().replace(/\&/g,'and').replace(/[\s\/(\[\])%?]/g,'-');
}

// Vue.prototype.$timeAgo = (time) => {
function timeAgo(str) {
  const between = Date.now() / 1000 - Number(time)
  if (between < 3600) {
    return pluralize(~~(between / 60), ' minute')
  } else if (between < 86400) {
    return pluralize(~~(between / 3600), ' hour')
  } else {
    return pluralize(~~(between / 86400), ' day')
  }
}

function imageUrl(file_path){
  return process.env.BASE_URL + '/public/storage/images/' + file_path
}

function dtlProductUrl(item){
  return '/' + this.$store.state.product_dtl_path + '/' + item.slug
}

export default ({ app }, inject) => {
  // console.log('APP', app)
  // inject('axios', axios);
  // inject('http', http);
  inject('tinyMce', tinyMce);
  inject('validateEmail', validateEmail);
  inject('validateContactNumber', validateContactNumber);
  inject('enToBnNumber', enToBnNumber);
  inject('strCapitalize', strCapitalize);
  inject('strSlug', strSlug);
  inject('timeAgo', timeAgo);
  inject('imageUrl', imageUrl);
  inject('dtlProductUrl', dtlProductUrl);
}

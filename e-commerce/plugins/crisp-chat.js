export default () => {
    window.$crisp = [];
    window.CRISP_WEBSITE_ID = "5f185555-f12d-48e6-8bcf-05841673e45a";
  
    (function() {
      var d = document;
      var s = d.createElement("script");
  
      s.src = "https://client.crisp.chat/l.js";
      s.async = 1;
      d.getElementsByTagName("head")[0].appendChild(s);
    })();
};
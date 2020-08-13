require.config({
　paths: {
　　"zepto.min": "/public/public/js/zepto.min",
    "iscroll":"/public/public/js/iscroll-lite",
    "demoUtils":"/public/public/js/demoUtils",
    "TouchSlide":"/public/public/js/TouchSlide.1.1",
    "layer":"/public/public/js/layer",
    "selectbook": "/public/Elearn/js/selectbook",
    "question": "/public/Elearn/js/question",
　},
  shim: {
　　　　　　
            "iscroll": {
    　　　　　　　　exports:"/public/public/js/iscroll"
    　　　　},
    		"enElearn": {
    　　　　　　　　exports:"/public/Elearn/js/enElearn"
    　　　　}
　　　　}
});
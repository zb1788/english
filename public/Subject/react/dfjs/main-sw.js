if ('serviceWorker' in navigator) {
    console.log("支持workservice");

    // register service worker
    navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(function(reg) {
        console.log("注册成功");
        if(reg.installing) {
          console.log('Service worker installing');
        } else if(reg.waiting) {
          console.log('Service worker installed');
        } else if(reg.active) {
          console.log('Service worker active');
        }
    }).catch(function(error) {
        // registration failed
        console.log('Registration failed with ' + error);
    });
  
}
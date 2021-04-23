"use strict";

var _this = void 0;

/* eslint-env browser */

/* eslint eqeqeq: "off" */
(function () {
  var replace = function replace(target, url) {
    var waitToHide = 300;
    var animate = setTimeout(function () {
      target.classList.add('__fernet_skeleton');
    }, waitToHide);
    target.querySelectorAll('a').forEach(function (el) {
      el.style.pointerEvents = 'none';
      el.setAttribute('aria-disabled', true);
    });

    var preventClicking = function preventClicking(event) {
      event.preventDefault();
      event.stopPropagation();
    };

    target.addEventListener('click', preventClicking);
    var options = {
      method: 'PUT',
      body: 'fernet_replace'
    };
    fetch(url, options).then(function (response) {
      return response.text();
    }).then(function (html) {
      target.innerHTML = html;
      clearInterval(animate);
      target.removeEventListener('click', preventClicking);
      target.classList.remove('__fernet_skeleton');
    })["catch"](function (err) {
      console.error(err);
      window.location.href = url;
    });
  };

  var load = function load() {
    var routerElement = document.getElementById('__fr');
    document.addEventListener('click', function (event) {
      for (var target = event.target; target && target != _this; target = target.parentNode) {
        if (target.matches && target.matches('a')) {
          if (target.href.match(/__fe/)) {
            replace(target.closest('.__fw'), target.href);
            event.preventDefault();
          }

          if (routerElement && target.classList.contains('__fl')) {
            document.querySelector('.active.__fl').classList.remove('active');
            target.classList.add('active');
            replace(routerElement, target.href);
            history.pushState({
              link: target.innerHTML
            }, target.innerHTML, target.href);
            target.blur();
            event.preventDefault();
          }
        }
      }
    });
  };

  if (window.fetch) {
    try {
      load();
    } catch (err) {
      console.error && console.error(err);
    }
  }
})();
"use strict";

var _this = void 0;

/* eslint-env browser */

/* eslint eqeqeq: "off" */
(function () {
  var replace = function replace(target, url) {
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
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
    options.headers = {
      'X-Fernet-Js': 1
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
    });
  };

  var load = function load() {
    var routerElement = document.getElementById('__fr');
    document.addEventListener('click', function (event) {
      for (var target = event.target; target && target != _this; target = target.parentNode) {
        if (target.matches && target.matches('a')) {
          if (target.href.match(/__fe/)) {
            event.preventDefault();
            replace(target.closest('.__fw'), target.href);
          }

          if (routerElement && target.classList.contains('__fl')) {
            var _document$querySelect;

            event.preventDefault();
            var activeClass = target.dataset.activeClass;
            (_document$querySelect = document.querySelector('.__fl.' + activeClass)) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.classList.remove(activeClass);
            target.classList.add(activeClass);
            replace(routerElement, target.href);
            history.pushState({
              link: target.innerHTML
            }, target.innerHTML, target.href);
            target.blur();
          }
        }
      }
    });
    document.addEventListener('submit', function (event) {
      for (var target = event.target; target && target !== _this; target = target.parentNode) {
        if (target.matches && target.matches('form')) {
          event.preventDefault();
          var options = {
            method: target.method,
            body: new FormData(target)
          };
          replace(target.closest('.__fw'), target.action, options);
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
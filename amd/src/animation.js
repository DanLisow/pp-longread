/**
 * AMD-модуль для анимации плавного появления частей лонгрида.
 *
 * @module     mod_longread/animation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function () {
  /**
   * Инициализирует обработчик для плавного появления частей.
   *
   * @return {void}
   */
  function init() {
    const observerOptions = {
      root: null,
      rootMargin: "0px 0px -50px 0px",
      threshold: 0.1,
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    document.addEventListener("longread:partadded", function (event) {
      if (event.detail && event.detail.el) {
        observer.observe(event.detail.el);
      }
    });
  }

  return { init: init };
});
